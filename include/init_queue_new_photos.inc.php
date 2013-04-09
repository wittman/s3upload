<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

include_once(S3UPLOAD_PATH . 'include/functions.inc.php');

function s3upload_init_queue_new_photos(){
	global $tokens, $page, $conf;

	if(!$conf['s3upload']['active'] || !$conf['s3upload']['aws_bucket']) return;

	$proceed = true;
	
	$query = '
SELECT MAX(id)
FROM '.IMAGES_TABLE
;
	list($max_image_table_id) = pwg_db_fetch_row(pwg_query($query));
	
	if(!$max_image_table_id){
		//No photos exist yet
		return;
	}
	
	$query = "
SELECT MAX(img_id)
FROM ".S3UPLOAD_TABLE."
WHERE status IN ('install', 'uploaded', 'monitoring_enabled')
";

	list($max_img_id_in_s3table) = pwg_db_fetch_row(pwg_query($query));
	
	$max_img_id_in_s3table = intval($max_img_id_in_s3table);
	$max_image_table_id = intval($max_image_table_id);
	
	if($max_image_table_id > $max_img_id_in_s3table){
		$proceed = true;
	}else{
		$proceed = false;
	}
	
	
	if($proceed){
		$sqlWhereMd5 = s3upload_get_sqlWhereMd5();
		
		$prevent_dups = true;
		if($conf['s3upload']['prevent_duplicates_by_md5'] === false){
			$prevent_dups = false;
		}
		
		//Fill s3upload table with new photos for pending upload
		if($prevent_dups){
			$query = '
SELECT i.id AS id, i.file AS file, i.path AS path, i.md5sum as md5sum
FROM '.IMAGES_TABLE.' i
LEFT OUTER JOIN '.S3UPLOAD_TABLE.' s ON i.id=s.img_id WHERE s.img_id IS NULL
AND i.id > '.$max_img_id_in_s3table.$sqlWhereMd5.'
';
		}else{
			$query = '
SELECT i.id AS id, i.file AS file, i.path AS path, i.md5sum as md5sum
FROM '.IMAGES_TABLE.' i
LEFT OUTER JOIN '.S3UPLOAD_TABLE.' s ON i.id=s.img_id WHERE s.img_id IS NULL
AND i.id > '.$max_img_id_in_s3table.'
';
		}
		$row_count = 0;
		$result = pwg_query($query);
		while( $result && $row = pwg_db_fetch_assoc($result) ){
			$row_count++;
			$v_md5sum = $row['md5sum'];
			$status = 'pending';
			if($v_md5sum == NULL){
				$status = 'pending_ftp';
			}
			$v_id = $row['id'];
			$file_sqlsafe = pwg_db_real_escape_string($row['file']);
			$v_file = $file_sqlsafe;
			$v_path = $row['path'];
			$timestampPath = $conf['s3upload']['aws_add_file_name_timestamp'] ? substr($v_path, 2) : '';
			$s3_url = s3upload_aws_url($v_file, $v_id, $conf['s3upload']['aws_add_file_name_timestamp']);
			$query = "
INSERT INTO ".S3UPLOAD_TABLE."
(date_updated, img_id, img_file, img_path, img_md5sum, s3_url, status)
VALUES (NOW(), '$v_id', '$v_file', '$v_path', '$v_md5sum', '$s3_url', '$status')
;";
			pwg_query($query);
			if($conf['s3upload']['tag']){
				s3upload_apply_tag($v_id);
			}
		}//end while
		if(defined('IN_ADMIN')){
			if($row_count) $_SESSION['page_infos'][] = "($row_count) " . l10n('photos pending S3 upload');
		}
    }
}
?>