<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


defined('S3UPLOAD_ID') or define('S3UPLOAD_ID', basename(dirname(__FILE__)));
include_once(PHPWG_PLUGINS_PATH . S3UPLOAD_ID . '/include/install.inc.php');

/**
 * plugin installation
 *
 * perform here all needed step for the plugin installation
 * such as create default config, add database tables, 
 * add fields to existing tables, create local folders...
 */
function plugin_install(){
	s3upload_install();
	define('s3upload_installed', true);
}

/**
 * plugin activation
 *
 * this function is triggered adter installation, by manual activation
 * or after a plugin update
 * for this last case you must manage updates tasks of your plugin in this function
 */
function plugin_activate(){
	global $conf;
	if( !defined('s3upload_installed') ){ //a plugin is activated just after its installation
		s3upload_install();
	}
}

/**
 * plugin unactivation
 *
 * triggered before uninstallation or by manual unactivation
 */
function plugin_deactivate(){
	global $conf;
	if($conf['s3upload']['upload_original_not_resampled']){
		//Disabled, and needs unpatching, so undo patch
		include_once(S3UPLOAD_PATH . 'include/patcher_undo_patch.inc.php');
		s3upload_patcher_undo_patch();
		$conf['s3upload']['upload_original_not_resampled'] = false;
		conf_update_param('s3upload', serialize($conf['s3upload']));
	}
}

/**
 * plugin uninstallation
 *
 * perform here all cleaning tasks when the plugin is removed
 * you should revert all changes made by plugin_install()
 */
function plugin_uninstall(){
	global $prefixeTable;

	// delete configuration
	pwg_query('DELETE FROM `'. CONFIG_TABLE .'` WHERE param = "s3upload" LIMIT 1;');

	// delete table
	pwg_query('DROP TABLE `'. $prefixeTable .'s3upload`;');
}
?>