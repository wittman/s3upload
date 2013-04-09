{combine_css path=$S3UPLOAD_PATH|@cat:"admin/template/style.css"}

<div class="titrePage">
	<h2>S3upload</h2>
</div>
{if $S3UPLOAD_COMPATIBLE}
<form method="post" action="" class="properties">
{if $s3upload.autoreload}
	<fieldset>
		<legend style="color:red">{'Auto-reload Dashboard is enabled.'|@translate} ({'This page will reload in less than 60 seconds. Disable in Configuration tab.'|@translate})</span></legend>
	</fieldset>
{/if}
<fieldset>
	<legend>{'S3Upload Plugin'|@translate}</legend>
	{$INTRO_CONTENT}
</fieldset>

<fieldset>
	<legend>{'Recent Activity'|@translate}</legend>

	<h3>{'10 Most Recent <span style="color:#00B359">Successful</span> Uploads to S3'|@translate}</h3>
	{if $html_recent_uploads}
	  {$html_recent_uploads}
	{else}
	    <div style="text-align:center">{'None Uploaded Yet'|@translate}</div>
	{/if}

	<h3>{'10 Most Recent <span style="color:#CC6600">Pending</span> Uploads to S3'|@translate}</h3>
	{if $html_recent_pending}
	  {$html_recent_pending}
	{else}
	  <div style="text-align:center">{'No Pending Uploads'|@translate}</div>
	{/if}

</fieldset>

<fieldset id="s3upload_home_more_info">
	<legend>{'More Info'|@translate}</legend>
  
	{$MORE_INFO_CONTENT}

</fieldset>
  
</form>
{if $s3upload.autoreload}{literal}<script type="text/javascript">setTimeout(function(){window.location.reload()}, 60000);</script>{/literal}{/if}
{else}
	<fieldset>
		<legend>
		{$INTRO_CONTENT}
	</legend>
	</fieldset>

	<div style="color:red;font-weight:bold;font-size:16pt">{'Your version of Piwigo is not known to be compatible with this version of S3Upload'|@translate}</div>
	
{/if}