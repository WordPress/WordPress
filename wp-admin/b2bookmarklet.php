<?php
/* <Bookmarklet> */

// accepts 'post_title' and 'content' as vars passed in. Add-on from Alex King

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

$mode = 'bookmarklet';

$standalone = 1;
require_once('b2header.php');

if ($user_level == 0)
	die ("Cheatin' uh?");

if ('b' == $a) {

?><html>
<head>
<script language="javascript" type="text/javascript">
<!--
window.close()
-->
</script>
</head>
<body></body>
</html><?php

} else {

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress > Bookmarklet</title>
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<script type="text/javascript" language="javascript">
<!--
function launchupload() {
	window.open ("b2upload.php", "b2upload", "width=380,height=360,location=0,menubar=0,resizable=1,scrollbars=yes,status=1,toolbar=0");
}

//-->
</script>
<style type="text/css">
<!--
body {
}

textarea,input,select {
	background-color: transparent;
	background-color: #cccccc;
	filter: alpha(opacity:80);
	-moz-opacity: .8;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}

.checkbox {
	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}

textarea {
	font-family: Verdana, Geneva, Arial, Helvetica;
	font-size: 0.9em;
}
-->
</style>
</head>
<body>
<form name="post" action="b2edit.php" method="POST">
<input type="hidden" name="action" value="post" />
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="mode" value="bookmarklet" />

<div align="right">
<?php

$popuptitle = stripslashes($popuptitle);
$text = stripslashes($text);


/* big funky fixes for browsers' javascript bugs */

if (($is_macIE) && (!isset($IEMac_bookmarklet_fix))) {
	$popuptitle = preg_replace($b2_macIE_correction["in"],$b2_macIE_correction["out"],$popuptitle);
	$text = preg_replace($b2_macIE_correction["in"],$b2_macIE_correction["out"],$text);
}

if (($is_winIE) && (!isset($IEWin_bookmarklet_fix))) {
	$popuptitle =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $popuptitle);
	$text =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $text);
}

if (($is_gecko) && (!isset($Gecko_bookmarklet_fix))) {
	$popuptitle = preg_replace($b2_gecko_correction["in"],$b2_gecko_correction["out"],$popuptitle);
	$text = preg_replace($b2_gecko_correction["in"],$b2_gecko_correction["out"],$text);
}

$post_title = $_REQUEST['post_title'];
if (!empty($post_title)) {
    $post_title =  stripslashes($post_title);
} else {
    $post_title = $popuptitle;
}

$content = $_REQUEST['content'];
if (!empty($content)) {
    $content =  stripslashes($content);
} else {
    $content = '<a href="'.$popupurl.'">'.$popuptitle.'</a>'."\n$text";
}

/* /big funky fixes */


?>
<table width="100%" cellpadding="8" cellspacing="0" width="415">
  <tr>
    <td width="40">&nbsp;</td>
    <td align="left" width="415">
      <table cellspacing="0" cellpadding="0">
        <td height="50" width="250" align="left" valign="bottom"><label>Title<br />
          <input type="text" name="post_title" size="20" tabindex="1" style="width: 215px;" value="<?php echo $post_title; ?>" /></label></td>
        <td width="165" align="left" valign="bottom"><b>Category</b><br /><?php dropdown_categories(); ?></td>
      </table>
    </td>
  </tr>

  <tr height="40">
    <td width="40">&nbsp;</td>
    <td align="left" width="415">
      <table cellspacing="0" cellpadding="0">
        <td height="50" width="150" align="left" valign="bottom">
          <label for="post_status">Post Status:</label>&nbsp;<br />
          <select name="post_status" id="post_status">
            <option value="publish"<?php selected($post_status, 'publish'); ?>>Publish</option>
      			<option value="draft"<?php selected($post_status, 'draft'); ?>>Draft</option>
      			<option value="private"<?php selected($post_status, 'private'); ?>>Private</option>
        	</select>
        </td>
        <td height="50" width="250" align="left" valign="bottom">
          <label for="comment_status">Comments:</label>&nbsp;<br />
          <select name="comment_status" id="comment_status">
            <option value="open"<?php selected($comment_status, 'open'); ?>>Open</option>
      			<option value="closed"<?php selected($comment_status, 'closed'); ?>>Closed</option>
        	</select>
        </td>
      </table>
    </td>
  </tr>

  <tr height="40">
    <td width="40">&nbsp;</td>
    <td align="left" width="415">
      <table cellspacing="0" cellpadding="0">
        <td height="50" width="100" align="left" valign="bottom">
          <label for="ping_status">Pings:</label><br />
          <select name="ping_status" id="ping_status">
            <option value="open"<?php selected($ping_status, 'open'); ?>>Open</option>
      			<option value="closed"<?php selected($ping_status, 'open'); ?>>Closed</option>
        	</select>
        </td>
        <td height="50" width="250" align="left" valign="bottom">
          <label for="post_password">Post Password:</label><br />
          <input name="post_password" type="text" id="post_password" value="<?php echo $post_password ?>" />
        </td>
      </table>
    </td>
	</tr>

  <tr height="40">
    <td width="40">&nbsp;</td>
    <td width="415" align="left" height="40">
      <table width="415" cellpadding="0" cellspacing="0">
        <td align="left" valign="bottom"><b>Post</b></td>
        <td align="right" valign="bottom"><?php if ($use_quicktags) include("wp-quicktags.php"); ?></td>
      </table>
<?php
if ((preg_match("/Nav/",$HTTP_USER_AGENT)) || (preg_match("/Mozilla\/4\.7/",$HTTP_USER_AGENT))) {
	$rows="6";
} else {
	$rows="8";
} ?>
<?php
// stuff to fix textism.com's WEIRD characters conflict with javascript on IE5Mac
preg_match("/\%u[1-9A-F][1-9A-F][1-9A-F][1-9A-F]/is", $text, $stufftofix);
// ... and so on. currently coding the fix
?>
      <textarea rows="<?php echo $rows ?>" cols="48" style="width:415px;" name="content" tabindex="2" class="postform"><?php echo $content ?></textarea><br />
      <table cellpadding="0" cellspacing="0">
        <td align="left" width="90"></td>
<?php if ($pingback) { ?>
        <td align="left">
          <input type="checkbox" class="checkbox" name="post_pingback" value="1" checked="checked" tabindex="7" id="pingback" /><label for="pingback"> PingBack</label>
        </td>
<?php } ?>
      </table>

<?php if (0 /*$use_preview*/) { ?>
      <input type="button" value="preview" onClick="preview(this.form);" class="search" tabindex="8" />
<?php } ?>

      <input type="submit" name="submit" value="Blog this !" class="search" tabindex="3" />


<?php if ( ($use_fileupload) && ($user_level >= $fileupload_minlevel) && ((ereg(" ".$user_login." ", $fileupload_allowedusers)) || (trim($fileupload_allowedusers)=="")) ) { ?>
      <input type="button" value="upload a file" onClick="launchupload();" class="search" />
<?php } ?>

      <script language="JavaScript" type="text/javascript">
      <!--
          window.focus();
          //document.blog.post_content.focus();
      //-->
      </script>
    </td>
  </tr>
<?php if ($trackback) { ?>
  <tr>
    <td width="40">&nbsp;</td>
    <td width="415" align="left" height="40">
      <label for="trackback"><strong>TrackBack</strong> an <acronym title="Uniform Resource Locator">URL</acronym>:</label> (Separate multiple URLs with commas.)<br />
      <input type="text" name="trackback" style="width: 415px" />
    </td>
  </tr>
<?php } ?>
</table>

</div>

</form>

</div>

</body>
</html><?php
}
?>
