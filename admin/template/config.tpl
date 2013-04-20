{combine_css path=$S3UPLOAD_PATH|@cat:"admin/template/style.css"}
{footer_script}{literal}
jQuery(document).ready(function(){
	/*jQuery('input[name="option2"]').change(function() {
		$('.option1').toggle();
	});
	jQuery("#configASummary a.showInfo").click(function() {
		jQuery("#configASummary").hide();
		jQuery("#configA").show();
		return false;
	});*/
});
{/literal}{/footer_script}

<div class="titrePage">
	<h2>S3upload</h2>
</div>

{if $S3UPLOAD_COMPATIBLE}
<form id="s3upload_config_form" method="post" action="" class="properties">
<fieldset>
  <legend>{'Common configuration'|@translate}</legend>
  
  <ul>
	
	<li class="option1">
	<label>
		<span class="property">{'Active Monitoring'|@translate}<br>
		<small>{'When checked, plugin monitors when new photos are added. When un-checked, added photos will not automatically be queued and uploaded to S3 upload. The <em>S3Upload</em> command in the Batch Manager is functional no matter how monitoring is set. The <em>Show S3 Download Link</em> option below also functions independent of monitoring.'|@translate}</small><br><br>
		<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_active" value="1" {if $s3upload.active}checked="checked"{/if}>
	</label>
    </li>
	
	<li class="option1">
	<label>
		<span class="property">{'Show S3 Download Link'|@translate}<br>
		<small>({'Option to show original quality image S3 download link in gallery photo info display'|@translate}</small>)
		</span>
		<input type="checkbox" name="s3upload_show_public_hifi_link" value="1" {if $s3upload.show_public_hifi_link}checked="checked"{/if}>
	</label>
    </li>
	<div class="hr"></div>
	
	<li class="option1">
	<label>
		<span class="property">{'<em>Only</em> Show S3 Download Link to Authenticated Admin'|@translate}<br>
		<small>({'Option to only show original quality image S3 download link in gallery photo info display if the user is logged in as an admin. For this option to be effective, <em>Show S3 Down Link</em> setting above must also be enabled'|@translate}.</small>)
		<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_show_public_hifi_link_admin_only" value="1" {if $s3upload.show_public_hifi_link_admin_only}checked="checked"{/if}>
	</label>
    </li>
	
    <li class="option1">
	<label>
		<span class="property">{'AWS Bucket'|@translate}<small class="required"> ({'Required'|@translate})</small>
		<div class="hr"></div>
		</span>
		<input type="text" name="s3upload_aws_bucket" value="{$s3upload.aws_bucket}" size="36">
	</label>
    </li>
	

    <li class="option2">
	<label>
		<span class="property">{'AWS Bucket Path'|@translate}<br>
			<small>({'For root of bucket leave blank. Any other path should end, but not start, with:'|@translate} <span style="font-weight:bold;font-family:Courier New">/ <span>)</small>
		<div class="hr"></div>
		</span>
		<input type="text" name="s3upload_aws_bucket_path" value="{$s3upload.aws_bucket_path}" size="36">
	</label>
    </li>
	
	
	<li class="option1">
	<label>
		<span class="property">{'AWS Location Constraint'|@translate}
			<br><small>({'Example'|@translate}: <span style="font-weight:bold;font-family:Courier New">us-west-2</span> - {'may be optional, leave blank if region is US Standard'|@translate})</small>
		
		<small>(<a target="_blank" href="http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region">{'See AWS Docs'|@translate}</a>)</small>
		<div class="hr"></div>
		</span>
		<input type="text" name="s3upload_aws_endpoint_region" value="{$s3upload.aws_endpoint_region}" size="24">
	</label>
    </li>
	
	
	<li class="option1">
	<label>
		<span class="property">{'AWS Access Key'|@translate}<small class="required"> ({'Required'|@translate})</small>
		<div class="hr"></div>	
		</span>
		<input type="text" name="s3upload_aws_access_key" value="{$s3upload.aws_access_key}" size="40">
	</label>
    </li>
	
	
	<li class="option1">
	<label>
		<span class="property">{'AWS Secret Key'|@translate}<small class="required"> ({'Required'|@translate})</small></span>
		<input type="text" name="s3upload_aws_secret_key" value="{$s3upload.aws_secret_key}" size="50">
	</label>
    </li>
	<div class="hr"></div>
	
	<li class="option1">
	<label>
		<span class="property">{'Upload Original to S3 (not smaller, resized version)'|@translate}<br>
		<small>{'Option to upload the original size image, not the smaller version resized by Piwigo (if configured to do so, and the image exceeds the '|@translate}{$max_size_link}.<br><br>
			<strong>IMPORTANT!</strong> For this feature to work, a core Piwigo file (admin/include/functions_upload.inc.php) will be patched with a small code addition by the plugin. If you do not wish to allow patching, do not enable this option. If and when you deactivate this S3Upload plugin, the file will be restored to its original state. 
			</small>
		<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_upload_original_not_resampled" value="1" {if $s3upload.upload_original_not_resampled}checked="checked"{/if}>
	</label>
    </li>
	
	<li class="option1">
	<label>
		<span class="property">{'Add Timestamp/Unique to Path/File for S3 Image Location'|@translate}<br>
		<small>{'Option to add prepend timestamp/unique sequence to path/file name to improve uniqueness or simply to break up location of file storage in S3 bucket into smaller groupings.<br>Only applies to web form and API uploaded photos. FTP + Synchronization photos will get the directory hierarchy in which they exist prepended for S3.'|@translate}<br>
			<br>
			<strong><em style="border-bottom: solid gray 1px">EXAMPLES</em></strong>
			<br><br>
			<strong>{"(Don't Add)"|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><strong>IMG_101.jpeg</strong><br><br>
			
			<strong>{'Add Path'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>upload/2013/03/31/<strong>IMG_101.jpeg</strong></em><br><br>
			
			<strong>{'Add File'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>20130331013511-<strong>IMG_101.jpeg</strong></em><br><br>
			
			<strong>{'Add Path + File'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>upload/2013/03/31/20130331013511-<strong>IMG_101.jpeg</strong></em><br><br>
			
			<strong>{'Add File Unique'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>20130331013511-5d467604-<strong>IMG_101.jpeg</strong></em><br><br>
			
			<strong>{'Add Path + File Unique'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>upload/2013/03/31/20130331013511-5d467604-<strong>IMG_101.jpeg</strong></em><br><br>
			
			<strong>{'Add Category Hierarchy'|@translate}</strong><br>
			<span class="s3upload_bucket_path_component">{$s3upload.aws_bucket}/{$s3upload.aws_bucket_path}</span><em>upload/Animals/Mammals/<strong>IMG_101.jpeg</strong></em><br><br>
			
		</small>
		<div class="hr"></div>
		</span>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="none" {if $s3upload.aws_add_file_name_timestamp=='none'}checked="checked"{/if} /> <span>{"(Don't Add)"|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="path" {if $s3upload.aws_add_file_name_timestamp=='path'}checked="checked"{/if} /> <span>{'Add Path'|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="file" {if $s3upload.aws_add_file_name_timestamp=='file'}checked="checked"{/if} /> <span>{'Add File'|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="path_and_file" {if $s3upload.aws_add_file_name_timestamp=='path_and_file'}checked="checked"{/if} /> <span>{'Add Path + File'|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="file_unique" {if $s3upload.aws_add_file_name_timestamp=='file_unique'}checked="checked"{/if} /> <span>{'Add File Unique'|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="path_and_file_unique" {if $s3upload.aws_add_file_name_timestamp=='path_and_file_unique'}checked="checked"{/if} /> <span>{'Add Path + File Unique'|@translate} &nbsp;&nbsp; </span>
		<br>
		<input type="radio" name="s3upload_aws_add_file_name_timestamp" value="cat_hierarchy" {if $s3upload.aws_add_file_name_timestamp=='cat_hierarchy'}checked="checked"{/if} /> <span>{'Add Category Hierarchy'|@translate} &nbsp;&nbsp; </span>
	</label>
    </li>

	<li class="option1">
	<label>
		<span class="property">{'Apply Tag to Each Queued/Uploaded Photo'|@translate}<small> ({$enter_a_tag})</small>
		<div class="hr"></div>	
		</span>
		<input type="text" name="s3upload_tag" value="{$s3upload.tag}" size="25">
	</label>
    </li>

	
    <li class="option1">
	<label>
		<span class="property">{'Prevent duplicate uploads (by md5 hash)'|@translate}<br>
		<small>({'Duplicate prevention is only applicable to photos uploaded via Piwigo web form or an app using the web services API (e.g. the Lightroom plugin), but <em>not</em> FTP + Synchronization [directory/files structure]'|@translate}).</small>
		<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_prevent_duplicates_by_md5" value="1" {if $s3upload.prevent_duplicates_by_md5}checked="checked"{/if}>
	</label>
    </li>
	
	
	<li class="option1">
	<label>
		<span class="property">{'Auto-reload Dashboard Every 60 seconds'|@translate}<br>
			<small style="font-weight:normal">({'Option to auto-reload the Dashboard tab every 1 minute for cases where a significant number of large size photos are queued for S3 upload and you want them to continue being processed by the \'pseudo-cron\' and your gallery is not otherwise active. Any normal page hits will continue processing with or without this option enabled.'|@translate}</small>
				<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_autoreload" value="1" {if $s3upload.autoreload}checked="checked"{/if}>
	</label>
    </li>
	
	<li class="option1">
	<label>
		<span class="property">{'Run "Pseudo-Cron" <em>Only</em> During Admin Session'|@translate}<br>
			<small style="font-weight:normal">({'By default, the S3Upload Plugin pseudo-cron (a Javascript AJAX call that is placed in each page load) runs no matter how the site is accessed. Uncheck this options to only run while authenticated as Admin. The \'cron\' processes the potentially long execution time procedures (queueing and uploading photos to your S3 account). It\'s safe to run on any page load, but the option to only run during Admin session may be helpful to avoid affecting page hit statistics by guests/other non-admin logins.<br><br>If turned on, one disadvantage is uploads won\'t get processed until you log in as Admin, thus creating more of a delay than you may want (photos uploaded via the web services API will not trigger the processing, though will be handled as soon as the cron runs on web page activity per this option).<br><br>To remove the disadvantage, enable the <em>Auto-reload Dashboard Every 60 seconds</em> option above and leave a browser window with the S3Upload Dashboard open to let processing continue.'|@translate}</small>
				<div class="hr"></div>
		</span>
		<input type="checkbox" name="s3upload_run_cron_on_admin_only" value="1" {if $s3upload.run_cron_on_admin_only}checked="checked"{/if}>
	</label>
    </li>
	
  </ul>
</fieldset>

<p style="text-align:left;"><input type="submit" name="save_config" value="{'Save Settings'|@translate}"></p>

</form>
{else}
<fieldset>
  <legend>{'Common configuration'|@translate}</legend>

</fieldset>

<div style="color:red;font-weight:bold;font-size:16pt">{'Your version of Piwigo  not known to be compatible with this version of S3Upload'|@translate}</div>

{/if}