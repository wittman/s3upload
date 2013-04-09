<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

function s3upload_patcher_patch(){
	$result = 'Successful';
	//Patch functions_upload.inc.php
	$path_to_functions_upload = PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php';
	$file_contents = @file_get_contents($path_to_functions_upload);
	if($file_contents === false){
		$_SESSION['page_errors'][] = l10n('S3Upload Plugin failed to patch \'admin/include/functions_upload.inc.php\' file (probably a file READ access permission issue).');
		$result = 'Failed';
	}else{
		if(md5($file_contents) == 'a288c1bbae4f25cb03b9e6db61e8494b'){ //File contents hash as of Piwigo 2.5.0
			$file_contents = str_replace('move_uploaded_file($source_filepath, $file_path);','move_uploaded_file($source_filepath, $file_path); copy($file_path, $file_path.\'.s3\'); //patch by s3Upload Plugin', $file_contents);
			@file_put_contents($path_to_functions_upload, $file_contents);
			$file_contents = @file_get_contents($path_to_functions_upload);
			if( ($file_contents === false) || (md5($file_contents) != '38dc62b3623ece4adda679e04a943f75') ){ //File contents after patching
				$result = 'Failed';
				$_SESSION['page_errors'][] = l10n('S3Upload Plugin failed to patch \'admin/include/functions_upload.inc.php\' file (probably a file WRITE access permission issue).') . ' ' . l10n('So, S3 uploads will be the Piwigo-reduced size (if applicable), not the true original.');
			}
		}else{
			$result = 'Failed';
			$_SESSION['page_errors'][] = l10n('S3Upload Plugin failed to patch \'admin/include/functions_upload.inc.php\' because file did not contains was not the expected 2.5.0 contents. An update to the S3Upload plugin to match the current Piwigo release is pending.') . ' ' . l10n('So, S3 uploads will be the Piwigo-reduced size (if applicable), not the true original.');
		}
	}
	return $result;
}
?>