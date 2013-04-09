<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(S3UPLOAD_PATH . 'include/functions.inc.php');
include_once(S3UPLOAD_PATH . 'include/s3_upload.inc.php');

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');


function s3upload_init_upload_new_photos(){
	global $conf;

	if(!$conf['s3upload']['aws_bucket'] || !$conf['s3upload']['aws_access_key'] || !$conf['s3upload']['aws_secret_key']){
		return;
	}

	$query = "
SELECT id, img_id, img_md5sum, img_file, img_path, status
FROM ".S3UPLOAD_TABLE."
WHERE status IN ('pending', 'pending_ftp')
";

	list($max_image_table_id) = pwg_db_fetch_row(pwg_query($query));

	$result = pwg_query($query);
	$resizeLog = '';
	$aws_upload_response = '';
	while( $result && ($row = pwg_db_fetch_assoc($result)) ){
		$imgFileName = $row['img_file'];
		$status = $row['status'];
		$imgPath = $row['img_path'];
		$fileTempName = S3UPLOAD_ABS_ROOT_PATH.substr($row['img_path'], 1) . '.s3'; //Add .s3 extra extension
		if(!file_exists($fileTempName)){
			$fileTempName = S3UPLOAD_ABS_ROOT_PATH.substr($row['img_path'], 1); //Without .s3, in case file not patched with copy command
		}
		$img_id = $row['img_id'];
		$id = $row['id'];
		$mime = s3upload_guess_mime_type( get_extension($imgFileName) );

		$s3_url = s3upload_aws_url($imgFileName, $img_id, $conf['s3upload']['aws_add_file_name_timestamp']);
		$imgFileNameTimestamp = s3upload_timestamp_filename($imgFileName, $img_id, $conf['s3upload']['aws_add_file_name_timestamp']);
		
		$s3_response = s3upload_do_upload($imgFileNameTimestamp, $fileTempName, $mime);

		if($s3_response['result']  == 'Successful'){
			if($s3_response['status_code'] != 'already_exists'){
				single_update(
					S3UPLOAD_TABLE,
					array(
						'date_updated' => date('Y-m-d H:i:s'),
						'status' => 'uploaded',
						's3_url' => $s3_url
					),
					array('id' => $id)
				);
			}else{
				//Already exists, so remove pending record(s)
				pwg_query("DELETE FROM ". S3UPLOAD_TABLE ." WHERE status LIKE 'pending' AND img_id=$img_id ;");
			}
			pwg_query("DELETE FROM ". S3UPLOAD_TABLE ." WHERE status LIKE 'failed%' AND img_id=$img_id ;");
			$aws_upload_response = 'Successful';
			if(substr($fileTempName, -3) == '.s3'){
				@unlink($fileTempName); //Remove if .s3 entension file - it is just a copy of the original file before any resizing
			}
		}else{
			$aws_upload_response = 'Failed';
			single_update(
				S3UPLOAD_TABLE,
				array('status' => 'failed_code_' . strval($s3_response['status_code'])),
				array('id' => $id)
			);
		}
		
	}//end while
}
?>