<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Configuration tab                                                     |
// +-----------------------------------------------------------------------+

global $conf;

if(S3UPLOAD_COMPATIBLE){
	//Compatible
	
	// save config
	if (isset($_POST['save_config']))
	{
		if( $_POST['s3upload_aws_bucket'] != '' && !preg_match("/^[a-z0-9.-]+$/", $_POST['s3upload_aws_bucket']) ){
			$_SESSION['page_errors'][] = l10n('S3 bucket name appears to be invalid. <small>(SEE <a target="_blank" href="http://docs.aws.amazon.com/AmazonS3/latest/dev/BucketRestrictions.html#bucketnamingrules">AWS naming rules</a>)</small>');
		}

		$resampled = (isset($_POST['s3upload_upload_original_not_resampled']) ? true : false);
		if(!$conf['s3upload']['upload_original_not_resampled'] && (isset($_POST['s3upload_upload_original_not_resampled']) ? true : false) == true){
			if(!third_party_provider(S3UPLOAD_ROOT_URL)){
				//Enabled, so patch
				include_once(S3UPLOAD_PATH . 'include/patcher_patch.inc.php');
				$patcher_undo_patch_result = s3upload_patcher_patch();
				if($patcher_undo_patch_result == 'Failed'){
					$resampled = $conf['s3upload']['upload_original_not_resampled']; //revert
				}
			}else{
				$resampled = $conf['s3upload']['upload_original_not_resampled']; //revert
				$_SESSION['page_errors'][] = l10n('Third party (multi-tenant) service provider (like piwigo.com), so <small>Upload Original to S3 (not smaller, resized version)</small> option not available for technical reasons (a shared core file needs to be patched for feature to work).').'<br><br>';
			}
		}
	
		if(!$conf['s3upload']['active'] && (isset($_POST['s3upload_active']) ? true : false) == true){
			//Active Monitoring Enabled/re-enabled, so insert marker into s3upload table
			s3upload_insert_monitoring_enabled_marker();
		}
	
		if($conf['s3upload']['upload_original_not_resampled'] && (isset($_POST['s3upload_upload_original_not_resampled']) ? true : false) == false){
			//Disabled, so undo patch
			include_once(S3UPLOAD_PATH . 'include/patcher_undo_patch.inc.php');
			$patcher_patch_result = s3upload_patcher_undo_patch();
			if($patcher_patch_result == 'Failed'){
				$resampled = $conf['s3upload']['upload_original_not_resampled']; //revert
			}
		}

		$conf['s3upload'] = array(
			'active' => (isset($_POST['s3upload_active']) ? true : false),
			'autoreload' => (isset($_POST['s3upload_autoreload']) ? true : false),
			'prevent_duplicates_by_md5' => (isset($_POST['s3upload_prevent_duplicates_by_md5']) ? true : false),
			'show_public_hifi_link' => (isset($_POST['s3upload_show_public_hifi_link']) ? true : false),
			'show_public_hifi_link_admin_only' => (isset($_POST['s3upload_show_public_hifi_link_admin_only']) ? true : false),
			'run_cron_on_admin_only' => (isset($_POST['s3upload_run_cron_on_admin_only']) ? true : false),
			'upload_original_not_resampled' => $resampled,
			'aws_endpoint_region' => $_POST['s3upload_aws_endpoint_region'],
			'aws_bucket' => $_POST['s3upload_aws_bucket'],
			'aws_bucket_path' => s3upload_normalize_aws_bucket_path($_POST['s3upload_aws_bucket_path']),
			'aws_access_key' => $_POST['s3upload_aws_access_key'],
			'aws_secret_key' => $_POST['s3upload_aws_secret_key'],
			'aws_add_file_name_timestamp' => $_POST['s3upload_aws_add_file_name_timestamp'],
			'tag' => $_POST['s3upload_tag'],
	    );
      
	  conf_update_param('s3upload', serialize($conf['s3upload']));
	  array_push($page['infos'], l10n('Information data registered in database'));
	}

	// send config to template
	$template->assign(array(
		'S3UPLOAD_COMPATIBLE' => S3UPLOAD_COMPATIBLE,
		'max_size_link' => l10n(sprintf('%smax size chosen%s', '<a href="'.get_root_url().'admin.php?page=configuration&amp;section=sizes">', '</a>')),
		'enter_a_tag' => l10n(sprintf('Enter a %stag%s name which, for every queued/uploaded image, will be applied to the corresponding Piwigo photo in gallery. Leave blank to not automatically add a tag.', '<a href="'.get_root_url().'admin.php?page=tags">', '</a>')),
		's3upload' => $conf['s3upload'],
	  ));

}else{
	//Incompatible
	$template->assign(array(
		'S3UPLOAD_COMPATIBLE' => S3UPLOAD_COMPATIBLE,
	  ));
}

// define template file
$template->set_filename('s3upload_content', realpath(S3UPLOAD_PATH . 'admin/template/config.tpl'));

?>