<?php
/* <Bookmarklet> */

$mode = "bookmarklet";

$standalone = 1;
require("./b2header.php");

if ($user_level == 0)
die ("Cheatin' uh ?");

if ($a=="b") {

?><html>
<head>
<script language="javascript">
<!--
window.close()
-->
</script>
</head>
<body></body>
</html><?php

} else {

?><html>
<head>
<title>b2 > bookmarklet</title>
<link rel="stylesheet" href="<?php echo $b2inc; ?>/b2.css" type="text/css">
<?php
if ($use_spellchecker) {
?><script type="text/javascript" language="javascript">
<!--

function DoSpell(formname, subject, body) {
	document.SPELLDATA.formname.value=formname
	document.SPELLDATA.subjectname.value=subject
	document.SPELLDATA.messagebodyname.value=body
	document.SPELLDATA.companyID.value="custom\\http://cafelog.com"
	document.SPELLDATA.language.value=1033
	document.SPELLDATA.opener.value="sproxy.pl"
	document.SPELLDATA.formaction.value="http://www.spellchecker.com/spell/startspelling.asp "
	window.open("b2spell.php","Spell",
	"toolbar=no,directories=no,location=yes,urlbar=yes,resizable=yes,width=620,height=600,top=100,left=100")
}

function preview(form) {
	var preview_date = "<?php echo date("Y-m-d H:i:s"); ?>";
	var preview_userid = "<?php echo $user_ID ?>";
	var preview_title = form.post_title.value;
	var preview_category = form.post_category.value;
	var preview_content = form.content.value;
	var preview_autobr = form.post_autobr.value;
	preview_date = escape(preview_date);
	preview_userid = escape(preview_userid);
	preview_title = escape(preview_title);
	preview_category = escape(preview_category);
	preview_content = escape(preview_content);
	preview_autobr = escape(preview_autobr);
	window.open ("<?php echo "$siteurl/$blogfilename" ?>?preview=1&preview_date="+preview_date +"&preview_userid="+preview_userid +"&preview_title="+preview_title +"&preview_category="+preview_category +"&preview_content="+preview_content +"&preview_autobr="+preview_autobr ,"Preview", "location=0,menubar=1,resizable=1,scrollbars=yes,status=1,toolbar=0");
}

function launchupload() {
	window.open ("b2upload.php", "b2upload", "width=380,height=360,location=0,menubar=0,resizable=1,scrollbars=yes,status=1,toolbar=0");
}

//-->
</script>
<?php
}
?>
<style type="text/css">
<!--
body {
	background-image: url('<?php
if ($is_gecko || $is_IE) {
?>b2-img/bgbookmarklet1.gif<?php
} else {
?>b2-img/bgbookmarklet3.gif<?php
}
?>');
	background-repeat: no-repeat;
}
<?php
if (!$is_NS4) {
?>
textarea,input,select {
	background-color: transparent;
<?php if ($is_gecko || $is_macIE) { ?>
	background-image: url('b2-img/bgbookmarklet.png');
<?php } elseif ($is_winIE) { ?>
	background-color: #cccccc;
	filter: alpha(opacity:80);
<?php } ?>
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
<?php if (!$is_gecko) { ?>
.checkbox {
	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}
<?php } ?>
<?php
}
?>
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

/* /big funky fixes */


?>
<table width="100%" cellpadding="8" cellspacing="0" width="415">
<tr>
<td width="40">&nbsp;</td>
<td align="left" width="415">
<table cellspacing="0" cellpadding="0">
<td height="50" width="250" align="left" valign="bottom"><b>Title</b><br />
<input type="text" name="post_title" size="20" tabindex="1" style="width: 215px;" value="<?php echo stripslashes($popuptitle) ?>" /></td>
<td width="165" align="left" valign="bottom"><b>Category</b><br /><?php dropdown_categories(); ?></td>
</table>
</td>
</tr>
<tr height="40">
<td width="40">&nbsp;</td>
<td width="415" align="left" height="40">
<table width="415" cellpadding="0" cellspacing="0">
<td align="left" valign="bottom"><b>Post</b></td>
<td align="right" valign="bottom"><?php if ($use_quicktags) include($b2inc."/b2quicktags.php"); ?></td>
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

<textarea rows="<?php echo $rows ?>" cols="48" style="width:415px;" name="content" tabindex="2" class="postform"><?php echo "<a href=\"$popupurl\">$popuptitle</a>\n$text" ?></textarea><br />

<table cellpadding="0" cellspacing="0">
<td align="left" width="90">
<input type="checkbox" name="post_autobr" value="1" <?php if ($autobr) echo " checked" ?> tabindex="4" class="checkbox" id="autobr" /><label for="autobr"> Auto-BR</label>
</td>
<?php if ($pingback) { ?>
<td align="left">
<input type="checkbox" class="checkbox" name="post_pingback" value="1" checked="checked" tabindex="7" id="pingback" /><label for="pingback"> PingBack</label>
</td>
<?php } ?>
</table>

<?php if ($use_preview) { ?>
<input type="button" value="preview" onclick="preview(this.form);" class="search" tabindex="8" />
<?php } ?>

<input type="submit" name="submit" value="Blog this !" class="search" tabindex="3" /> 

<?php if ($use_spellchecker) { ?>
<!--<input type = "button" value = "Spell Check" onclick="var f=document.forms[0]; doSpell( 'en', f.post_content, '<?php echo $spellchecker_url ?>/sproxy.cgi', true);" class="search" tabindex="5" />-->
<input type="button" value="Spellcheck" onclick="DoSpell
('post','content','');" class="search" />
<?php } ?>

<?php if ( ($use_fileupload) && ($user_level >= $fileupload_minlevel) && ((ereg(" ".$user_login." ", $fileupload_allowedusers)) || (trim($fileupload_allowedusers)=="")) ) { ?>
<input type="button" value="upload a file" onclick="launchupload();" class="search" />
<?php } ?>

<script language="JavaScript">
<!--
window.focus();
//				document.blog.post_content.focus();
//-->
</script>
</td>
</tr>
<?php if ($trackback) { ?>
<tr>
<td width="40">&nbsp;</td>
<td width="415" align="left" height="40">
<b>TrackBack</b> an URL:<br />
<input type="text" name="trackback_url" style="width: 415px" />
</td>
</tr>
<?php } ?>
</table>
</div>

</form>

<!-- this is for the spellchecker -->
<form name="SPELLDATA"><div>
<input name="formname" type="hidden" value="">
<input name="messagebodyname" type="hidden" value="">
<input name="subjectname" type="hidden" value="">
<input name="companyID" type="hidden" value="">
<input name="language" type="hidden" value="">
<input name="opener" type="hidden" value="">
<input name="formaction" type="hidden" value="">
</div></form>

</body>
</html><?php
}
?>