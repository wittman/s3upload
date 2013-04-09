<?php

/*
 * as much as possible prefer using comprehensive keys (such as "Check here") instead of
 * formatted keys (eg "s3upload_check_button"), it would make your code more readable and help
 * the translation team as well
 */

$lang['S3upload'] = 'S3upload';
$lang['S3Upload'] = 'S3Upload';
$lang['S3Upload Plugin'] = 'S3Upload Plugin';
$lang['What S3upload can do for me?'] = 'What S3upload can do for me?';
$lang['Common configuration'] = 'Common configuration';
$lang['Last Queued Image ID'] = 'Last Queued Image ID';
$lang['Example'] = 'Example';
$lang['Prevent duplicate uploads (by md5 hash)'] = 'Prevent duplicate uploads (by md5 hash)';
$lang['may be optional, leave blank if region is US Standard'] = 'may be optional, leave blank if region is US Standard';
$lang['AWS Endpoint Location Constraint'] = 'AWS Endpoint Location Constraint';
$lang['(may be optional, leave blank if US Standard)'] = '(may be optional, leave blank if US Standard)';
$lang['See AWS Docs'] = 'See AWS Docs';
$lang['AWS Bucket Path'] = 'AWS Bucket Path';
$lang['AWS Bucket'] = 'AWS Bucket';
$lang['For root of bucket leave blank. Any other path should end, but not start, with:'] = 'For root of bucket leave blank. Any other path should end, but not start, with:';
$lang['10 Most Recent Successful Uploads to S3'] = '10 Most Recent Successful Uploads to S3';
$lang['10 Most Recent Pending Uploads to S3'] = '10 Most Recent Pending Uploads to S3';
$lang['AWS Access Key'] = 'AWS Access Key';
$lang['AWS Secret Key'] = 'AWS Secret Key';
$lang['No Pending Uploads'] = 'No Pending Uploads';
$lang['None Uploaded Yet'] = 'None Uploaded Yet';
$lang['photos pending S3 upload'] = 'photos pending S3 upload';

$lang['View Photo'] = 'View Photo';
$lang['S3 Image'] = 'S3 Image';

$lang['S3Upload Plugin failed to patch admin/include/functions_upload.inc.php file (probably a file access permission issue).'] = 'S3Upload Plugin failed to patch admin/include/functions_upload.inc.php file (probably a file access permission issue).';
$lang['So, the "Piwigo Reduced Size + S3 Full Size Original Photo" option is *unavailable*.'] = 'So, the "Piwigo Reduced Size + S3 Full Size Original Photo" option is *unavailable*.';

$lang['Show S3 Download Link'] = 'Show S3 Download Link';
$lang['Option to show original quality image S3 download link in gallery photo info display'] = 'Option to show original quality image S3 download link in gallery photo info display';

$lang['Pseudo-Cron" <em>Only</em> During Admin Login'] = 'Pseudo-Cron" <em>Only</em> During Admin Session';
$lang['By default, the S3Upload Plugin pseudo-cron (a Javascript AJAX call that is placed in each page load) runs no matter how the site is accessed. Uncheck this options to only run while authenticated as Admin. The \'cron\' processes the potentially long execution time procedures (queueing and uploading photos to your S3 account). It\'s safe to run on any page load, but the option to only run during Admin session may be helpful to avoid affecting page hit statistics by guests/other non-admin logins.<br><br>If turned on, one disadvantage is uploads won\'t get processed until you log in as Admin, thus creating more of a delay than you may want (photos uploaded via the web services API will not trigger the processing, though will be handled as soon as the cron runs on web page activity per this option).<br><br>To remove the disadvantage, enable the <em>Auto-reload Dashboard Every 60 seconds</em> option above and leave a browser window with the S3Upload Dashboard open to let processing continue.'] = 'By default, the S3Upload Plugin pseudo-cron (a Javascript AJAX call that is placed in each page load) runs no matter how the site is accessed. Uncheck this options to only run while authenticated as Admin. The \'cron\' processes the potentially long execution time procedures (queueing and uploading photos to your S3 account). It\'s safe to run on any page load, but the option to only run during Admin session may be helpful to avoid affecting page hit statistics by guests/other non-admin logins.<br><br>If turned on, one disadvantage is uploads won\'t get processed until you log in as Admin, thus creating more of a delay than you may want (photos uploaded via the web services API will not trigger the processing, though will be handled as soon as the cron runs on web page activity per this option).<br><br>To remove the disadvantage, enable the <em>Auto-reload Dashboard Every 60 seconds</em> option above and leave a browser window with the S3Upload Dashboard open to let processing continue.';

$lang['Duplicate prevention is only applicable to photos uploaded via Piwigo web form or an app using the web services API (e.g. the Lightroom plugin), but <em>not</em> FTP + Synchronization [directory/files structure]'] = 'Duplicate prevention is only applicable to photos uploaded via Piwigo web form or an app using the web services API (e.g. the Lightroom plugin), but <em>not</em> FTP + Synchronization [directory/files structure]';

$lang['S3 bucket name appears to be invalid. <small>(SEE <a target="_blank" href="http://docs.aws.amazon.com/AmazonS3/latest/dev/BucketRestrictions.html#bucketnamingrules">AWS naming rules</a>)</small>'] = 'S3 bucket name appears to be invalid. <small>(SEE <a target="_blank" href="http://docs.aws.amazon.com/AmazonS3/latest/dev/BucketRestrictions.html#bucketnamingrules">AWS naming rules</a>)</small>';


$lang['Add Timestamp/Unique to Path/File for S3 Image Location'] = 'Add Timestamp/Unique to Path/File for S3 Image Location';

$lang['Option to add prepend timestamp/random sequence to path/file name to improve uniqueness or simply to break up location of file storage in S3 bucket into smaller groupings.<br>Only applies to web form and API uploaded photos. FTP + Synchronization photos will get the directory hierarchy in which they exist prepended for S3.'] = 'Option to add prepend timestamp/random sequence to path/file name to improve uniqueness or simply to break up location of file storage in S3 bucket into smaller groupings.<br>Only applies to web form and API uploaded photos. FTP + Synchronization photos will get the directory hierarchy in which they exist prepended for S3.';
$lang['EXAMPLES'] = 'EXAMPLES';
$lang["(Don't Add)"] = "(Don't Add)";
$lang['Add Path'] = 'Add Path';
$lang['Add File'] = 'Add File';
$lang['Add Path'] = 'Add Path';
$lang['Add Path + File'] = 'Add Path + File';
$lang['Add File Unique'] = 'Add File Unique';
$lang['Add Path + File Unique'] = 'Add Path + File Unique';

$lang['Required'] = 'Required';

$lang['When checked, plugin monitors when new photos are added. When un-checked, added photos will not be queued for <em>S3 upload. Show S3 Download Link</em> option below will continue to operate, though(unless it is un-checked).'] = 'When checked, plugin monitors when new photos are added. When un-checked, added photos will not be queued for <em>S3 upload. Show S3 Download Link</em> option below will continue to operate, though(unless it is un-checked).';

$lang['Queued for S3 Upload.'] = 'Queued for S3 Upload.';
$lang['\'Confirm\' was not checked. Nothing processed.'] = '\'Confirm\' was not checked. Nothing processed.';

$lang['No selected records were queued for upload because option %s<em>Prevent duplicate uploads (by md5 hash)</em>%s is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>'] = 'No selected records were queued for upload because option %s<em>Prevent duplicate uploads (by md5 hash)</em>%s is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>';

$lang['Not all selected records were queued for upload because option <em>Prevent duplicate uploads (by md5 hash)</em> is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>'] = 'Not all selected records were queued for upload because option <em>Prevent duplicate uploads (by md5 hash)</em> is checked and matching hash(es) were found.<br><br> See %sRecent Activity%s for progress.<br><br>';


$lang['Auto-reload Dashboard Every 60 seconds'] = 'Auto-reload Dashboard Every 60 seconds';
$lang['Option to auto-reload the Dashboard tab every 1 minute for cases where a significant number of large size photos are queued for S3 upload and you want them to continue being processed and your gallery is not otherwise active. Any normal page hits will continue processing with or without this option enabled.'] = 'Option to auto-reload the Dashboard tab every 1 minute for cases where a significant number of large size photos are queued for S3 upload and you want them to continue being processed and your gallery is not otherwise active. Any normal page hits will continue processing with or without this option enabled.';

$lang['Auto-reload Dashboard is enabled.'] = 'Auto-reload Dashboard is enabled.';
$lang['This page will reload in less than 60 seconds. Disable in Configuration tab.'] = 'This page will reload in less than 60 seconds. Disable in Configuration tab.';


$lang['<em>Only</em> Show S3 Download Link to Authenticated Admin'] = '<em>Only</em> Show S3 Download Link to Authenticated Admin';
$lang['Option to only show original quality image S3 download link in gallery photo info display if the user is logged in as an admin. For this option to be effective, <em>Show S3 Down Link</em> setting above must also be enabled'] = 'Option to only show original quality image S3 download link in gallery photo info display if the user is logged in as an admin. For this option to be effective, <em>Show S3 Down Link</em> setting above must also be enabled';


$lang['S3Upload Plugin failed to patch \'admin/include/functions_upload.inc.php\' file (probably a file READ access permission issue).'] = 'S3Upload Plugin failed to patch \'admin/include/functions_upload.inc.php\' file (probably a file READ access permission issue).';
$lang['So, S3 uploads will be the Piwigo-reduced size (if applicable), not the true original.'] = 'So, S3 uploads will be the Piwigo-reduced size (if applicable), not the true original.';

$lang['Apply Tag to Each Queued/Uploaded Photo'] = 'Apply Tag to Each Queued/Uploaded Photo';
$lang['Enter a <a href="admin.php?page=tags">tag</a> name which, for every queued/uploaded image, will be applied to the corresponding Piwigo photo in gallery. Leave blank to not automatically add a tag.'] = 'Enter a <a href="admin.php?page=tags">tag</a> name which, for every queued/uploaded image, will be applied to the corresponding Piwigo photo in gallery. Leave blank to not automatically add a tag.';
$lang['Your version of Piwigo is not known to be compatible with this version of S3Upload'] = 'Your version of Piwigo is not known to be compatible with this version of S3Upload';
$lang['Version'] = 'Version';

$lang['%smax size chosen%s'] = '%smax size chosen%s';

$lang['Enter a %stag%s name which, for every queued/uploaded image, will be applied to the corresponding Piwigo photo in gallery. Leave blank to not automatically add a tag.'] = 'Enter a %stag%s name which, for every queued/uploaded image, will be applied to the corresponding Piwigo photo in gallery. Leave blank to not automatically add a tag.';

$lang['Third party (multi-tenant) service provider (like piwigo.com), so <small>Upload Original to S3 (not smaller, resized version)</small> option not available for technical reasons (a shared core file needs to be patched for feature to work).'] = 'Third party (multi-tenant) service provider (like piwigo.com), so <small>Upload Original to S3 (not smaller, resized version)</small> option not available for technical reasons (a shared core file needs to be patched for feature to work).';

#$lang[''] = '';
?>