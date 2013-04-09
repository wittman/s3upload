<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Home tab                                                              |
// +-----------------------------------------------------------------------+

if(S3UPLOAD_COMPATIBLE){
	if(!$conf['s3upload']['aws_bucket'] || !$conf['s3upload']['aws_access_key'] || !$conf['s3upload']['aws_secret_key']){
		$_SESSION['page_errors'][] = 'S3upload Not Configured Yet (<a href="'.S3UPLOAD_ADMIN.'-config">Configuration tab</a>)';
	}

	//Uploaded
	$query = "
	SELECT img_id, date_updated, img_path, img_file, s3_url
	FROM ".S3UPLOAD_TABLE."
	WHERE status='uploaded'
	ORDER BY date_updated DESC, img_id DESC
	LIMIT 10;
	";

	$result = pwg_query($query);
	$html_recent_uploads = 
	'<tr>'
			.'<td>'
				. 'ID'
			.'</td>'
			.'<td>'
				. 'Updated'
			.'</td>'
			.'<td>'
				. 'File'
			.'</td>'
			.'<td>'
				. 'Path'
			.'</td>'
			.'<td>'
				. 'S3 URL'
			.'</td>'
		.'</tr>';

	$hasUploaded = false;
	while( $result && $row = pwg_db_fetch_assoc($result) ){
		$hasUploaded = true;
		$v_date_updated = $row['date_updated'];
		$v_img_id = $row['img_id'];
		$v_img_file = $row['img_file'];
		$v_img_path = $row['img_path'];
		$v_s3_url = $row['s3_url'];
		$html_recent_uploads .= '<tr>'
				.'<td>('
					. $v_img_id
				.')</td>'
				.'<td>'
					. $v_date_updated
				.'</td>'
				.'<td>'
					. s3upload_html_escape($v_img_file)
				.'</td>'
				.'<td>'
					. s3upload_html_escape($v_img_path)
				.'</td>'
				.'<td>'
					. '<a href="'.$v_s3_url.'" target="_blank">' . substr($v_s3_url, 2) /* remove auto-protocol double slash */ . '</a>'
				.'</td>'
			.'</tr>'
		;
	}
	$html_recent_uploads = '<table cellpadding="1" border="1">' . $html_recent_uploads . '</table>';

	//Pending
	$query = "
	SELECT img_id, date_updated, img_path, img_file, status, s3_url
	FROM ".S3UPLOAD_TABLE."
	WHERE status IN ('pending', 'pending_ftp')
		OR status LIKE 'failed%'
	ORDER BY date_updated DESC, img_id DESC
	LIMIT 10;
	";

	$result = pwg_query($query);
	$html_recent_pending = 
	'<tr>'
			.'<td>'
				. 'ID'
			.'</td>'
			.'<td>'
				. 'Updated'
			.'</td>'
			.'<td>'
				. 'File'
			.'</td>'
			.'<td>'
				. 'Path'
			.'</td>'
			.'<td>'
				. 'Status'
			.'</td>'
			.'<td>'
				. 'S3 URL'
			.'</td>'
		.'</tr>';

	$hasPending = false;

	while( $result && $row = pwg_db_fetch_assoc($result) ){
		$hasPending = true;
		$v_date_updated = $row['date_updated'];
		$v_img_id = $row['img_id'];
		$v_img_file = $row['img_file'];
		$v_img_path = $row['img_path'];
		$v_status = $row['status'];
		$v_s3_url = $row['s3_url'];
		$html_recent_pending .= '<tr>'
				.'<td>('
					. $v_img_id
				.')</td>'
				.'<td>'
					. $v_date_updated
				.'</td>'
				.'<td>'
					. s3upload_html_escape($v_img_file)
				.'</td>'
				.'<td>'
					. s3upload_html_escape($v_img_path)
				.'</td>'
				.'<td>'
					. $v_status
				.'</td>'
				.'<td>'
					. substr($v_s3_url, 2) /* remove auto-protocol double slash */
				.'</td>'
			.'</tr>'
		;
	}
	$html_recent_pending = '<table cellpadding="1" border="1">' . $html_recent_pending . '</table>';

	if(!$hasPending){
		$html_recent_pending = '';
	}

	if(!$hasUploaded){
		$html_recent_uploads = '';
	}


	// send variables to template
	$template->assign(array(
		'S3UPLOAD_COMPATIBLE' => S3UPLOAD_COMPATIBLE,
		's3upload' => $conf['s3upload'],
		'INTRO_CONTENT' => load_language('intro.html', S3UPLOAD_PATH, array('return'=>true)),
		'MORE_INFO_CONTENT' => load_language('more_info.html', S3UPLOAD_PATH, array('return'=>true)),
		'html_recent_uploads' => $html_recent_uploads,
		'html_recent_pending' => $html_recent_pending,
		's3upload' => $conf['s3upload'],
		'ROOT_URL' => get_root_url()
	  ));

}else{
	//Incompatible
	$template->assign(array(
		'S3UPLOAD_COMPATIBLE' => S3UPLOAD_COMPATIBLE,
		'INTRO_CONTENT' => load_language('intro.html', S3UPLOAD_PATH, array('return'=>true)),
		'ROOT_URL' => get_root_url()
	  ));
}
// define template file
$template->set_filename('s3upload_content', realpath(S3UPLOAD_PATH . 'admin/template/home.tpl'));
?>