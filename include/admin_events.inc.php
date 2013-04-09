<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');

/**
 * admin plugins menu link
 */
function s3upload_admin_plugin_menu_links($menu){
  array_push($menu, array(
    'NAME' => l10n('S3Upload'),
    'URL' => S3UPLOAD_ADMIN,
  ));
  return $menu;
}

/**
 * add a tab on photo properties page
 */
function s3upload_tabsheet_before_select($sheets, $id){
	if ($id == 'photo'){
		$sheets['s3upload'] = array(
			'caption' => l10n('S3upload'),
			'url' => S3UPLOAD_ADMIN.'-photo&amp;image_id='.$_GET['image_id'],
		);
	}
  
	return $sheets;
}

/**
 * add an action to the Batch Manager
 */
function s3upload_loc_end_element_set_global()
{
	global $template;
  
	$template->append('element_set_global_plugins_actions', array(
		'ID' => 's3upload', 
		'NAME' => l10n('S3upload'), 
		'CONTENT' => '<label><input type="checkbox" name="check_s3upload"> '.l10n('Confirm').'</label>', // this is optional
	));
}

/**
 * perform added action
 */
function s3upload_element_set_global_action($action, $collection){
	if($action == 's3upload'){
		global $conf, $page;    
		if(empty($_POST['check_s3upload'])){
			array_push($page['warnings'], l10n('\'Confirm\' was not checked. Nothing processed.'));
			return;
	    }else{
			$sqlWhereMd5 = s3upload_get_sqlWhereMd5();

			$prevent_dups = true;
			if($conf['s3upload']['prevent_duplicates_by_md5'] === false){
				$prevent_dups = false;
			}

		//Fill s3upload table with new photos for pending upload
			if($prevent_dups){
				$query = "
SELECT i.id AS id, i.file AS file, i.path AS path, i.md5sum AS md5sum
FROM ".IMAGES_TABLE."
WHERE i.id IN (" . implode(',', $collection) . ") ".$sqlWhereMd5."
";
			}else{
				$query = "
SELECT id, file, path, md5sum
FROM ".IMAGES_TABLE."
WHERE id IN (" . implode(',', $collection) . ") 
";
			}

			$result = pwg_query($query);
			$inserts = array();
			$dbfields = array('date_updated','img_id','img_file','img_path','img_md5sum','s3_url','status');
			$record_count = 0;
			while( $result && $row = pwg_db_fetch_assoc($result) ){
				$record_count++;
				$file_sqlsafe = pwg_db_real_escape_string($row['file']);
				$insert = array(
					'date_updated'	=> date('Y-m-d H:i:s'),
					'img_id'		=> (int)$row['id'],
					'img_file'	  	=> $file_sqlsafe,
					'img_path'		=> $row['path'],
					'img_md5sum' 	=> $row['md5sum'],
					's3_url'	  	=> $s3_url = s3upload_aws_url($row['file'], $row['id'], $conf['s3upload']['aws_add_file_name_timestamp']),
					'status'	  	=> 'pending'
				);

				array_push($inserts, $insert);
			}
			mass_inserts(S3UPLOAD_TABLE, $dbfields, $inserts);
			$countMsg = '';
			if($record_count == 0) {
				 $countMsg = '<br><br>' . sprintf(l10n('No selected records were queued for upload because option %s<em>Prevent duplicate uploads (by md5 hash)</em>%s is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>'), '<a href="admin.php?page=plugin-s3upload-config">', '</a>', '<a href="admin.php?page=page=plugin-s3upload">', '</a>');
			}else if ($record_count != count($collection)){
				$countMsg = '<br><br>' . sprintf(l10n('Not all selected records were queued for upload because option <em>Prevent duplicate uploads (by md5 hash)</em> is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>'), '<a href="admin.php?page=page=plugin-s3upload">', '</a>');
			}
			array_push($page['infos'], '(' . $record_count . ') ' . l10n('Queued for S3 Upload.') . $countMsg);
		}
	}
}
?>