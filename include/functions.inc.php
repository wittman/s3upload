<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');
function s3upload_log($text,  $file=__FILE__, $func=__FUNCTION__, $line=__LINE__){
	#s3upload_log("pathArr: ".print_r($pathArr), __FILE__, __FUNCTION__, __LINE__);
	$filename = PHPWG_ROOT_PATH.'__log.txt';
	$expected_path = '/Applications/MAMP/htdocs/piwigo/plugins/s3upload/';
	if(strpos($file, $expected_path) !== false){
		$file = str_replace($expected_path, '', $file);
	}
	file_put_contents($filename, " [$file]"." $func()"." ($line)"." $text".PHP_EOL.PHP_EOL, FILE_APPEND);
}
function s3upload_guess_mime_type($ext){
	switch( strtolower($ext) ){
		case "jpe": case "jpeg":
		case "jpg": $ctype="image/jpeg"; break;
		case "png": $ctype="image/png"; break;
		case "gif": $ctype="image/gif"; break;
		case "tiff":
		case "tif": $ctype="image/tiff"; break;
		case "txt": $ctype="text/plain"; break;
		case "html":
		case "htm": $ctype="text/html"; break;
		case "xml": $ctype="text/xml"; break;
		case "pdf": $ctype="application/pdf"; break;
		case "zip": $ctype="application/zip"; break;
		case "ogg": $ctype="application/ogg"; break;
		default: $ctype="image/jpeg";
	}
	return $ctype;
}
function s3upload_init_config(){
	global $conf;
	$s3upload_default_config = serialize(array(
		'active' => true,
		'aws_endpoint_region' => '',
		'aws_bucket' => '',
		'aws_bucket_path' => '/',
		'aws_access_key' => '',
		'aws_secret_key' => '',
		'aws_add_file_name_timestamp' => 'none',
		'run_cron_on_admin_only' => false,
		'upload_original_not_resampled' => false,
		'prevent_duplicates_by_md5' => true,
		'show_public_hifi_link' => true,
		'show_public_hifi_link_admin_only' => false,
		'tag' => '',
      ));

	conf_update_param('s3upload', $s3upload_default_config);
	$conf['s3upload'] = $s3upload_default_config;
}
function s3upload_add_quotes($str) {
    return sprintf("'%s'", $str);
}
function s3upload_rawurlencode_preserve_slash($s){
	return str_replace('%2F', '/', rawurlencode($s));
}
function s3upload_aws_url($fileName='', $img_id, $format){
	global $conf, $template;
	
	if($fileName != ''){
		$fileName = s3upload_timestamp_filename($fileName, $img_id, $format);
	}
	$aws_url = '//' . $conf['s3upload']['aws_bucket'] . '.s3.amazonaws.com/' . s3upload_rawurlencode_preserve_slash($conf['s3upload']['aws_bucket_path']  . $fileName);
	if(isset($conf['s3upload']['aws_endpoint_region']) && $conf['s3upload']['aws_endpoint_region']){
		$aws_url = '//' . $conf['s3upload']['aws_bucket'] . '.s3-' . $conf['s3upload']['aws_endpoint_region'] . '.amazonaws.com/' . s3upload_rawurlencode_preserve_slash($conf['s3upload']['aws_bucket_path'] . $fileName);
	}
	
	return $aws_url;
}
function s3upload_get_img_path($id){
	$query = "
SELECT path
FROM ".IMAGES_TABLE."
WHERE id='$id' LIMIT 1;
";
	list($path) = pwg_db_fetch_row(pwg_query($query));
	return $path;
}
function s3upload_get_category_hierarchy($img_id){
	/*$query = "
SELECT ic.category_id AS category_id
FROM ".IMAGES_TABLE." i
LEFT JOIN ".IMAGE_CATEGORY_TABLE." ic ON i.id = ic.image_id
WHERE i.id=$img_id
";*/

	$query = "
SELECT category_id
FROM ".IMAGE_CATEGORY_TABLE."
WHERE image_id=$img_id
";
	list($cat_id) = pwg_db_fetch_row(pwg_query($query));
	
	$cat_info = get_cat_info($cat_id);
	$cat_hierarchy = '';
	foreach($cat_info['upper_names'] as $un){
		$cat_hierarchy .= $un['name'] . '/';
	}
	
	/*
	* if(is_array($cat_info['upper_names']) && count > 1){

	}else{
		$cat_hierarchy = $cat_info['upper_names'][0]['name'];
	}
	
	$query = "
SELECT uppercats
FROM ".CATEGORIES_TABLE."
WHERE id=$cat_id
;";
	
	list($uppercats) = pwg_db_fetch_row(pwg_query($query));
	
	$uppercatsArr = explode(',', $uppercats);
	
	$cat_hierarchy = implode('/', $uppercatsArr);
	*/
	return $cat_hierarchy;
}
function s3upload_timestamp_filename($fileName, $img_id, $format){
	// Example: "./upload/2013/03/31/20130331013511-5d467604.jpg"
	$path = s3upload_get_img_path($img_id);
	
	//Check if galleries (FTP) path
	$pathArr = explode('/', $path);
	if($pathArr[1] == 'galleries'){
		array_pop($pathArr);
		array_shift($pathArr); //remove .
		array_shift($pathArr); //remove galleries
		$path = implode('/', $pathArr);
		return $path.'/'.$fileName;
	}

	switch($format){
		case 'none':
			return $fileName;
			break;
		case 'path':
			preg_match('#^\.\/\w+/\d+\/\d+\/\d+/#', $path.$fileName, $matches);
			return substr($matches[0], 2).$fileName; //remove ./ at start
			break;
		case 'path_and_file':
			preg_match('#^\.\/\w+/\d+\/\d+\/\d+/\d+-#', $path.$fileName, $matches);
			return substr($matches[0], 2).$fileName; //remove ./ at start
			break;
		case 'path_and_file_unique':
			preg_match('#^\.\/\w+/\d+\/\d+\/\d+/\d+-[a-z0-9]+#i', $path.$fileName, $matches);
			return substr($matches[0], 2).'-'.$fileName; //remove ./ at start
			break;
		case 'file_unique':
			preg_match('#(^\.\/\w+/\d+\/\d+\/\d+/)(\d+-[a-z0-9]+)#i', $path.$fileName, $matches);
			return $matches[2].'-'.$fileName;
			break;
		case 'cat_hierarchy':
			$cat_hierarchy = s3upload_get_category_hierarchy($img_id);
			return $cat_hierarchy.$fileName;
			break;
		default:
			//file
			preg_match('#(^\.\/\w+/\d+\/\d+\/\d+/)(\d+-)#', $path.$fileName, $matches);
			return $matches[2].$fileName;
	}
}
function s3upload_get_sqlWhereMd5(){
	$prevent_dups = true;
	if(isset($conf['s3upload']['prevent_duplicates_by_md5']) && $conf['s3upload']['prevent_duplicates_by_md5'] === false){
		$prevent_dups = false;
	}
	$sqlWhereMd5 = '';
	if($prevent_dups){
		//Gather md5 for dup prevention
		$query = "
SELECT id, img_id, img_md5sum
FROM ".S3UPLOAD_TABLE."
WHERE status='uploaded'
";
		$md5_hashes = array_from_query($query, 'img_md5sum');
		$md5_hashes = array_unique($md5_hashes);
		if( count($md5_hashes) > 0 ){
			$sqlWhereMd5 = ' AND IFNULL(i.md5sum, \'-1\') NOT IN ('.implode(',', array_map('s3upload_add_quotes', $md5_hashes)).')'; //'-1' if md5sum is NULL; don't count as dup if no hash exists (ie. synced from FTP dir/files)
		}
	}
	return $sqlWhereMd5;
}
function s3upload_remove_first_and_last_char_match($text, $match_char){
	$text = preg_replace('#'.$match_char.'$#', '', $text);
	$text = preg_replace('#^'.$match_char.'#', '', $text);
	return $text;
}
function s3upload_normalize_aws_bucket_path($path){
	$path = s3upload_remove_first_and_last_char_match($path, '/');
	if($path == '') return '';
	return $path . '/';
}
function s3upload_html_escape($html){
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}
function s3upload_insert_monitoring_enabled_marker(){
	//Find latest photo in image table, add placeholder entry in s3upload table
	$query = "
SELECT id, file, path, md5sum
FROM ".IMAGES_TABLE."
ORDER BY id DESC LIMIT 1
";
	$result = pwg_query($query);
	if($result){
		list($id, $file, $path, $md5sum) = pwg_db_fetch_row($result);
		$query = "
INSERT INTO ".S3UPLOAD_TABLE."
(date_updated, img_id, img_file, img_path, img_md5sum, status)
VALUES (NOW(), '$id', '$file', '$path', '$md5sum', 'monitoring_enabled')
;";
		pwg_query($query);
	}
}
function s3upload_apply_tag($img_id){
	global $conf;
	$tag_id = tag_id_from_tag_name($conf['s3upload']['tag']);
	$tag_ids = array($tag_id);
	set_tags($tag_ids, $img_id);	
}
function third_party_provider($root_url){
	if(strpos($root_url, '//piwigo.com') !== false){
		return true;
	}else{
		return false;
	}
}
?>