
<div class="wrap">
<?php

$allowed_users = explode(" ", trim($fileupload_allowedusers));

$submitbutton_text = 'Blog this!';
$toprow_title = 'New Post';
$form_action = 'post';
$form_extra = '';
if ($use_pingback) {
	$form_pingback = '<input type="checkbox" class="checkbox" name="post_pingback" value="1" ';
	if ($post_pingback) $form_pingback .= 'checked="checked" ';
	$form_pingback .= 'tabindex="7" id="pingback" /> <label for="pingback"><strong>PingBack</strong> the <acronym title="Uniform Resource Locators">URL</acronym>s in this post</label> <a href="http://wordpress.org/docs/reference/post/#pingback" title="Help on Pingbacks">?</a><br />';
} else {
	$form_pingback = '';
}
if ($use_trackback) {
	$form_trackback = '<p><label for="trackback"><a href="http://wordpress.org/docs/reference/post/#trackback" title="Help on trackbacks"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym></a>:</label> (Separate multiple <acronym title="Uniform Resource Locator">URL</acronym>s with spaces.)<br />
	<input type="text" name="trackback_url" style="width: 360px" id="trackback" tabindex="7" /></p>';
	if ('' != $pinged) {
		$form_trackback .= '<p>Already pinged:</p><ul>';
		$already_pinged = explode("\n", trim($pinged));
		foreach ($already_pinged as $pinged_url) {
			$form_trackback .= "\n\t<li>$pinged_url</li>";
		}
		$form_trackback .= '</ul>';
	}
} else {
	$form_trackback = '';
}
$colspan = 3;
$saveasdraft = '';


?>

<form name="post" action="post.php" method="post" id="post">

<?php
if ('bookmarklet' == $mode) {
    echo '<input type="hidden" name="mode" value="bookmarklet" />';
}
?>
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.post.title.focus();
}
window.onload = focusit;
//-->
</script>
<style media="screen" type="text/css">
#titlediv, #postpassworddiv {
	height: 3.5em;
}
</style>
<div id="poststuff">
    <fieldset id="titlediv">
      <legend><a href="http://wordpress.org/docs/reference/post/#title" title="Help on titles">Title</a></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

    <fieldset id="categorydiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#category" title="Help on categories">Categories</a></legend> 
	  <div><?php dropdown_categories($default_post_cat); ?></div>
    </fieldset>

<br />
<fieldset id="postdiv">
<legend><a href="http://wordpress.org/docs/reference/post/#post" title="Help with post field">Post</a></legend>
		<div id="quicktags">
<?php
if (get_settings('use_quicktags') && 'bookmarklet' != $mode) {
	echo '<a href="http://wordpress.org/docs/reference/post/#quicktags" title="Help with quicktags">Quicktags</a>: ';
	include('quicktags.php');
}
?>
</div>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content"><?php echo $content ?></textarea></div>
</fieldset>

<?php
if (get_settings('use_quicktags')) {
?>
<script type="text/javascript" language="JavaScript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>
<?php } ?>

<?php echo $form_pingback ?>
<?php echo $form_prevstatus ?>
<?php echo $form_trackback; ?>
<p><input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="Save as Draft" /> 
  <input name="saveasprivate" type="submit" id="saveasprivate" tabindex="10" value="Save as Private" /> 
  <input name="publish" type="submit" id="publish" tabindex="6" style="font-weight: bold;" value="Publish" /> 
  <?php if ('bookmarklet' != $mode) {
      echo '<input name="advanced" type="submit" id="advancededit" tabindex="7" value="Advanced Editing &raquo;" />';
  } ?>
  <input name="referredby" type="hidden" id="referredby" value="<?php echo $HTTP_SERVER_VARS['HTTP_REFERER']; ?>" />
</p>

</div>
</form>

</div>
