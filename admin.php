<?php
/**
 * This is the main administration page, if you have only one admin page you can put 
 * directly its code here or using the tabsheet system like bellow
 */

defined('S3UPLOAD_PATH') or die('Hacking attempt!');
 
global $template, $page, $conf;

// get current tab
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'home';

// plugin tabsheet is not present on photo page
if ($page['tab'] != 'photo'){
	// tabsheet
	include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
	$tabsheet = new tabsheet();
	$tabsheet->set_id('s3upload');

	$tabsheet->add('home', l10n('Dashboard'), S3UPLOAD_ADMIN . '-home');
	$tabsheet->add('config', l10n('Configuration'), S3UPLOAD_ADMIN . '-config');
	$tabsheet->select($page['tab']);
	$tabsheet->assign();
}

// include page
include(S3UPLOAD_PATH . 'admin/' . $page['tab'] . '.php');

// template vars
$template->assign(array(
	'S3UPLOAD_PATH'=> get_root_url() . S3UPLOAD_PATH, // used for images, scripts, ... access
	'S3UPLOAD_ABS_PATH'=> realpath(S3UPLOAD_PATH),	// used for template inclusion (Smarty needs a real path)
	'S3UPLOAD_ADMIN' => S3UPLOAD_ADMIN,
));
  
// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 's3upload_content');

?>