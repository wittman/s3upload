<?php
defined('S3UPLOAD_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Photo[S3upload] tab                                                   |
// +-----------------------------------------------------------------------+

global $conf;

/* Basic checks */
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];
$self_url = S3UPLOAD_ADMIN.'-photo&amp;image_id='.$_GET['image_id'];


/* Tabs */
// when adding a tab to an existing tabsheet you MUST reproduce the core tabsheet code
// this way it will not break compatibility with other plugins and with core functions
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('photo'); // <= don't forget tabsheet id
$tabsheet->select('s3upload');
$tabsheet->assign();


/* Initialisation */
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$picture = pwg_db_fetch_assoc(pwg_query($query));

$aws_url =  s3upload_aws_url($picture['file'], $_GET['image_id'], $conf['s3upload']['aws_add_file_name_timestamp']);

/* Template */
$template->assign(array(
  'F_ACTION' => $self_url,
  's3upload' => $conf['s3upload'],
  'TITLE' => render_element_name($picture),
  'TN_SRC' => DerivativeImage::thumb_url($picture),
  'S3_SRC' => $aws_url,
  'S3_SRC_DISPLAY' => substr($aws_url, 2),
));

$template->set_filename('s3upload_content', realpath(S3UPLOAD_PATH . 'admin/template/photo.tpl'));

?>