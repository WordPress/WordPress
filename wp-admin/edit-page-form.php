
<div class="wrap">
<?php

$allowed_users = explode(" ", trim(get_settings('fileupload_allowedusers')));

$form_action = 'post';
$form_extra = '';
?>

<form name="post" action="post.php" method="post" id="post">

<?php
if (isset($mode) && 'bookmarklet' == $mode) {
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

<div id="poststuff">
    <fieldset id="titlediv">
      <legend><a href="http://wordpress.org/docs/reference/post/#title" title="<?php _e('Help on titles') ?>"><?php _e('Title') ?></a></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

<br />
<fieldset id="postdiv">
    <legend><a href="http://wordpress.org/docs/reference/post/#post" title="<?php _e('Help with page field') ?>"><?php _e('Page') ?></a></legend>
		<div id="quicktags">
<?php
if ('bookmarklet' != $mode) {
	echo '<a href="http://wordpress.org/docs/reference/post/#quicktags" title="' . __('Help with quicktags') . '">' . __('Quicktags') . '</a>: ';
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


<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<p class="submit">
  <input name="savepage" type="submit" id="savepage" tabindex="6" style="font-weight: bold;" value="<?php _e('Save') ?>" /> 
  <input name="referredby" type="hidden" id="referredby" value="<?php if (isset($_SERVER['HTTP_REFERER'])) echo urlencode($_SERVER['HTTP_REFERER']); ?>" />
</p>
<?php do_action('edit_page_form', ''); ?>
</div>
</form>

</div>
