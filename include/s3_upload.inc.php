<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

require_once 's3.inc.php';

function s3upload_do_upload($imgFileName, $filePath, $mime){
	global $conf;

	$host = 's3.amazonaws.com';
	if(isset($conf['s3upload']['aws_endpoint_region']) && $conf['s3upload']['aws_endpoint_region']){
		$host =  's3-' . $conf['s3upload']['aws_endpoint_region'] . '.' . 'amazonaws.com';
	}
	$aws_access_key = '';
	if(isset($conf['s3upload']['aws_access_key']) && $conf['s3upload']['aws_access_key']){
		$aws_access_key = $conf['s3upload']['aws_access_key'];
	}
	$aws_secret_key = '';
	if(isset($conf['s3upload']['aws_secret_key']) && $conf['s3upload']['aws_secret_key']){
		$aws_secret_key = $conf['s3upload']['aws_secret_key'];
	}
	
	$s3 = new S3($aws_access_key, $aws_secret_key, $host);

	$bucket = '';
	if(isset($conf['s3upload']['aws_bucket']) && $conf['s3upload']['aws_bucket']){
		$bucket = $conf['s3upload']['aws_bucket'];
	}
	
	$path = '';
	if(isset($conf['s3upload']['aws_bucket_path']) && $conf['s3upload']['aws_bucket_path']){
		$path = $conf['s3upload']['aws_bucket_path'];
	}
	

	$headers['Content-Type'] = $mime;
	
	$dest_path = s3upload_rawurlencode_preserve_slash($path . $imgFileName);
	
	if(is_array($s3->getObjectInfo($bucket, $dest_path))){
		//Exists already
		return array('result' => 'Successful', 'status_code' => 'already_exists');
	}else{
		//Doesn't exist in S3 yet, so attempt upload
		if( $s3->uploadFile($bucket, $dest_path, $filePath, $web_accessible = true, $headers) ){
			return array('result' => 'Successful', 'status_code' => $s3->getResponseCode());
		}else{  
			return array('result' => 'Failed', 'status_code' => $s3->getResponseCode());
		}
	}
}
?>