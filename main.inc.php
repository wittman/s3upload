<?php 
/*
Plugin Name: S3Upload
Version: 1.12
Description: Piwigo plugin that uploads gallery photos to an AWS S3 storage account, automatically or by batch selection.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=691
Author: Micah Wittman
Author URI: http://wittman.org
*/

/**
 * This is the main file of the plugin, called by Piwigo in "include/common.inc.php" line 137.
 * At this point of the code, Piwigo is not completely initialized, so nothing should be done directly
 * except define constants and event handlers.
 */


global $prefixeTable, $conf;

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');
defined('S3UPLOAD_ID') or define('S3UPLOAD_ID', basename(dirname(__FILE__)));
if(!defined('S3UPLOAD_PATH')) define('S3UPLOAD_PATH' ,   PHPWG_PLUGINS_PATH . S3UPLOAD_ID . '/');
if(!defined('S3UPLOAD_TABLE')) define('S3UPLOAD_TABLE',   $prefixeTable . 's3upload');
define('S3UPLOAD_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . S3UPLOAD_ID);
define('S3UPLOAD_PUBLIC',  get_absolute_root_url() . make_index_url(array('section' => 's3upload')) . '/');
define('S3UPLOAD_DIR', PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 's3upload/');
define('S3UPLOAD_VERSION', '1.0');
define('S3UPLOAD_ABS_ROOT_PATH', realpath(PHPWG_ROOT_PATH));
define('S3UPLOAD_ROOT_URL', get_root_url());;

//Check for known compatible Piwigo version
if (version_compare(PHPWG_VERSION, '2.5.0', '=')) {
	//Compatible
	define('S3UPLOAD_COMPATIBLE', true);
}else{
	//Not known compatible
	define('S3UPLOAD_COMPATIBLE', false);
}

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+
// init the plugin

add_event_handler('init', 's3upload_init');

if(S3UPLOAD_COMPATIBLE && isset($_GET['s3uploadcron']) ){
	add_event_handler('init', 's3upload_init_queue_new_photos');
	add_event_handler('init', 's3upload_init_upload_new_photos');
}

include_once(S3UPLOAD_PATH . 'include/init_queue_new_photos.inc.php');
include_once(S3UPLOAD_PATH . 'include/init_upload_new_photos.inc.php');

if(defined('IN_ADMIN')){
	// admin plugins menu link
	add_event_handler('get_admin_plugin_menu_links', 's3upload_admin_plugin_menu_links');
}



if(S3UPLOAD_COMPATIBLE && defined('IN_ADMIN')){

	// new tab on photo page
	add_event_handler('tabsheet_before_select', 's3upload_tabsheet_before_select', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

	// new action in Batch Manager
	add_event_handler('loc_end_element_set_global', 's3upload_loc_end_element_set_global');
	add_event_handler('element_set_global_action', 's3upload_element_set_global_action', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);

}

if(defined('IN_ADMIN')){
	// file containing all previous handlers functions
	include_once(S3UPLOAD_PATH . 'include/admin_events.inc.php');
}

/**
 * plugin initialization
 *	 - check for upgrades
 *	 - unserialize configuration
 *	 - load language
 */
function s3upload_init(){
	global $conf, $pwg_loaded_plugins, $page;
	
	// load plugin language file
	load_language('plugin.lang', S3UPLOAD_PATH);
	
	// prepare plugin configuration
	if(isset($conf['s3upload'])){
		$conf['s3upload'] = unserialize($conf['s3upload']);
	}else{
		include_once(S3UPLOAD_PATH . 'include/install.inc.php');
		s3upload_install();
	}
	
	// apply upgrade if needed
	if (
		S3UPLOAD_VERSION == 'auto' or
		$pwg_loaded_plugins[S3UPLOAD_ID]['version'] == 'auto' or
		version_compare($pwg_loaded_plugins[S3UPLOAD_ID]['version'], S3UPLOAD_VERSION, '<')
	){
		// call install function
		include_once(S3UPLOAD_PATH . 'include/install.inc.php');
		s3upload_install();
	
		// update plugin version in database
		if( $pwg_loaded_plugins[S3UPLOAD_ID]['version'] != 'auto' and S3UPLOAD_VERSION != 'auto' ){
			$query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. S3UPLOAD_VERSION .'"
WHERE id = "'. S3UPLOAD_ID .'"';
			pwg_query($query);
		
			$pwg_loaded_plugins[S3UPLOAD_ID]['version'] = S3UPLOAD_VERSION;
		
			if (defined('IN_ADMIN')){
				$_SESSION['page_infos'][] = 'S3upload updated to version '. S3UPLOAD_VERSION;
			}
		}
	}

	if(!S3UPLOAD_COMPATIBLE){
		if(isset($_GET['page']) && strpos($_GET['page'], 'plugin') !== false){
			$_SESSION['page_errors'][] = l10n('S3Upload Plugin was installed/activated, but is not operating because you are running Piwigo version ('.PHPWG_VERSION.') , but <em>S3Upload Plugin</em> is only known to be compatible with version 2.5.0.<br><br>Deactivate S3Upload plugin to turn off this alert.<br><br>');
		}
		return;
	}

	add_event_handler('loc_begin_page_header', 'inject_js', 20);
}

function inject_js(){
	global $template, $conf, $page;

	$is_ajax_ping = isset($_GET['s3uploadcron']) ? true : false;
	$run_cron_here = true;
 	if(!$conf['s3upload']['run_cron_on_admin_only']){
		$run_cron_here = true;
	}else{
		if( (defined('IN_ADMIN') && IN_ADMIN ) && is_admin() ){
			$run_cron_here = true;
		}else{
			$run_cron_here = false;
		}
	}
	
	if($is_ajax_ping){
		$run_cron_here = false;
	}
	
	$js_cron = '';
	if($run_cron_here){
		$js_cron = '
			/* Pseudo-cron job to process uploads */
			jQuery.get("' . get_absolute_root_url() . '?s3uploadcron=1");
';
	}
	
	$show_public_hifi_link = false;
	if(isset($page['body_id']) && $page['body_id'] == 'thePicturePage'){
		if(isset($conf['s3upload']) && $conf['s3upload']['show_public_hifi_link']){
			$show_public_hifi_link = $conf['s3upload']['show_public_hifi_link'];
		}
	}
	
	$js_hifi = '';
	$js_hifi_infotable = '';
	if($show_public_hifi_link){
		if(is_admin() || !$conf['s3upload']['show_public_hifi_link_admin_only'] ){
			$current = $template->get_template_vars('current');
			$photoID = $current['id'];

			$query = "
SELECT s3_url
FROM ".S3UPLOAD_TABLE."
WHERE img_id='$photoID' AND status='uploaded' LIMIT 1;
			";
			list($aws_url) = pwg_db_fetch_row(pwg_query($query));
			if($aws_url){
				$js_hifi = '
					/* S3 image download link */
					jQuery(".imageInfoTable").append(\'<div>&nbsp;</div><div id="S3Link" class="imageInfo"> <dt>Original Quality Image</dt> <dd><a href="' . $aws_url .'">Download</a></dd> </div>\');
		';
				$js_hifi_infotable = ' || jQuery(".imageInfoTable").length == 0';
			}
		}
	}
	
	if($run_cron_here || $js_hifi){
		$js_script = '
<script>	
	/* S3Upload Plugin */
	s3uploadCheckDOM();
	function s3uploadCheckDOM(){
		if (typeof jQuery == "undefined"'.$js_hifi_infotable.')
		{
			setTimeout(s3uploadCheckDOM, 1000);
		}else{
			'.$js_hifi
			 .$js_cron.'
		}
	}
</script>
';
		$template->append('head_elements', $js_script);
	}
}