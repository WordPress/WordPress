<?php

require_once("b2config.php");
require_once($b2inc."/b2template.functions.php");
require_once($b2inc."/b2verifauth.php");
require_once($b2inc."/b2vars.php");
require_once($b2inc."/b2functions.php");
require_once($b2inc."/xmlrpc.inc");
require_once($b2inc."/xmlrpcs.inc");

if (!isset($use_cache))	$use_cache=1;
if (!isset($blogID))	$blog_ID=1;
if (!isset($debug))		$debug=0;
timer_start();

get_currentuserinfo();

$request = " SELECT * FROM $tablesettings ";
$result = mysql_query($request);
$querycount++;
while($row = mysql_fetch_object($result)) {
	$posts_per_page=$row->posts_per_page;
	$what_to_show=$row->what_to_show;
	$archive_mode=$row->archive_mode;
	$time_difference=$row->time_difference;
	$autobr=$row->AutoBR;
	$date_format=stripslashes($row->date_format);
	$time_format=stripslashes($row->time_format);
}

// let's deactivate quicktags on IE Mac and Lynx, because they don't work there.
if (($is_macIE) || ($is_lynx))
	$use_quicktags=0;

$b2varstoreset = array('profile','standalone','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}

if ($standalone == 0) {

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>b2 > <?php echo $title; ?></title>
<link rel="stylesheet" href="<?php echo $b2inc; ?>/b2.css" type="text/css">
<style type="text/css">
<!--
<?php
if (!$is_NS4) {
?>
td.menutop {
	padding-top: 2px;
	padding-bottom: 2px;
	border-color: #999999;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 0px;
	border-right-width: 0px;
	border-style: dashed;
}
textarea,input,select {
	background-color: #f0f0f0;
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}
.checkbox {
<?php
if ((preg_match("/MSIE/",$HTTP_USER_AGENT)) && (!preg_match("/Mac/",$HTTP_USER_AGENT))) {
?>	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}
<?php
}
}
?>
-->
</style>
<?php
if ($use_spellchecker) {
?><script type="text/javascript" language="javascript">
<!--
function DoSpell(formname, subject, body)
{
document.SPELLDATA.formname.value=formname
document.SPELLDATA.subjectname.value=subject
document.SPELLDATA.messagebodyname.value=body
document.SPELLDATA.companyID.value="custom\\http://cafelog.com"
document.SPELLDATA.language.value=1033
document.SPELLDATA.opener.value="<?php echo $pathserver ?>/sproxy.pl"
document.SPELLDATA.formaction.value="http://www.spellchecker.com/spell/startspelling.asp "
window.open("<?php echo $pathserver ?>/b2spell.php","Spell",
"toolbar=no,directories=no,location=yes,resizable=yes,width=620,height=400,top=100,left=100")
}
//-->
</script><?php
}
if ($redirect==1) {
?>
<script language="javascript">
<!--
function redirect() {
  window.location = "<?php echo $redirect_url; ?>";
}
setTimeout("redirect();", 600);
//-->
</script>
<?php
}
?>
<script language="javascript">
<!-- hiding from old terrible browsers

	function profile(userID) {
		window.open ("b2profile.php?action=viewprofile&user="+userID, "Profile", "width=500, height=450, location=0, menubar=0, resizable=0, scrollbars=1, status=1, titlebar=0, toolbar=0, screenX=60, left=60, screenY=60, top=60");
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

//  End -->
</script>
</head>
<body bgcolor="#ffffff" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" cellpadding="0" cellspacing="0" align="center">
<?php
if ($profile==0) {
?>
<tr height="60">
<td valign="top">
<?php include($b2inc."/b2menutop.php") ?>
</td>
</tr><tr>
<?php
}
?>
<td valign="top">
<img src="b2-img/blank.gif" border="0" width="35" height="24" />
<div class="panelbody">
<?php

}
?>