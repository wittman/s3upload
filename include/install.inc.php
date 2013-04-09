<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

/**
 * The installation function is called by main.inc.php and maintain.inc.php
 * in order to install and/or update the plugin.
 *
 */

function s3upload_install(){
	global $conf, $prefixeTable;

	if(!defined('S3UPLOAD_TABLE')) define('S3UPLOAD_TABLE',   $prefixeTable . 's3upload'); 
	if(!defined('S3UPLOAD_PATH')) define('S3UPLOAD_PATH' ,   PHPWG_PLUGINS_PATH . S3UPLOAD_ID . '/');
	
	include_once(S3UPLOAD_PATH . 'include/functions.inc.php');
	
	// add config parameter
	if(empty($conf['s3upload'])){
		s3upload_init_config();
	}else{
		// if you need to test the "old" configuration you must check if not yet unserialized
		$old_conf = is_string($conf['s3upload']) ? unserialize($conf['s3upload']) : $conf['s3upload'];
	}

  // add a new table
	pwg_query('
CREATE TABLE IF NOT EXISTS `'. $prefixeTable .'s3upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_updated` datetime DEFAULT NULL,
  `img_id` mediumint(8) DEFAULT NULL,
  `img_file` varchar(255) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `s3_url` varchar(255) DEFAULT NULL,
  `img_md5sum` char(32) DEFAULT NULL,
  `status` varchar(24) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

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
VALUES (NOW(), '$id', '$file', '$path', '$md5sum', 'install')
;";
		pwg_query($query);
	}
}
?>