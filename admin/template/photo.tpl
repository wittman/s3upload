{combine_css path=$S3UPLOAD_PATH|@cat:"admin/template/style.css"}

<h2>{$TITLE} &#8250; {'View Photo'|@translate} {$TABSHEET_TITLE}</h2>


<form>
  <fieldset>
    <legend>{'S3 Image'|@translate}</legend>
    <p>
	<img src="{$S3_SRC}" alt="{'S3 Image'|@translate}" class="">
    </p>
	<p>
		S3 URL: <a href="{$S3_SRC}">{$S3_SRC_DISPLAY}</a>
	</p>

  </fieldset>
</form>