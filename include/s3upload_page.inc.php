<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');

global $page, $template, $conf, $user, $tokens, $pwg_loaded_plugins;


# DO SOME STUFF HERE... or not !


$template->assign(array(
  'INTRO_CONTENT' => load_language('intro.html', S3UPLOAD_PATH, array('return'=>true)),
  'S3UPLOAD_PATH' => S3UPLOAD_PATH,
  'S3UPLOAD_ABS_PATH' => realpath(S3UPLOAD_PATH).'/',
  ));

$template->set_filename('index', realpath(S3UPLOAD_PATH . 'template/s3upload_page.tpl'));

?>