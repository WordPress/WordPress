<?php 
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />

<script type="text/javascript">
//<![CDATA[

function customToggleLink() {
	// TODO: Only show link if there's a hidden row
	document.write('<small>(<a href="javascript:;" id="customtoggle" onclick="toggleHidden()"><?php _e('Show hidden'); ?></a>)</small>');
	// TODO: Rotate link to say "show" or "hide"
	// TODO: Use DOM
}

function toggleHidden() {
	var allElements = document.getElementsByTagName('tr');
	for (i = 0; i < allElements.length; i++) {
		if ( allElements[i].className.indexOf('hidden') != -1 ) {
			 allElements[i].className = allElements[i].className.replace('hidden', '');
		}
	}
}

<?php if ( isset($xfn) ) : ?>

function GetElementsWithClassName(elementName, className) {
	var allElements = document.getElementsByTagName(elementName);
	var elemColl = new Array();
	for (i = 0; i < allElements.length; i++) {
		if (allElements[i].className == className) {
			elemColl[elemColl.length] = allElements[i];
		}
	}
	return elemColl;
}

function meChecked() {
  var undefined;
  var eMe = document.getElementById('me');
  if (eMe == undefined) return false;
  else return eMe.checked;
}

function upit() {
	var isMe = meChecked(); //document.getElementById('me').checked;
	var inputColl = GetElementsWithClassName('input', 'valinp');
	var results = document.getElementById('rel');
	var linkText, linkUrl, inputs = '';
	for (i = 0; i < inputColl.length; i++) {
		 inputColl[i].disabled = isMe;
		 inputColl[i].parentNode.className = isMe ? 'disabled' : '';
		 if (!isMe && inputColl[i].checked && inputColl[i].value != '') {
			inputs += inputColl[i].value + ' ';
				}
		 }
	inputs = inputs.substr(0,inputs.length - 1);
	if (isMe) inputs='me';
	results.value = inputs;
	}

function blurry() {
	if (!document.getElementById) return;

	var aInputs = document.getElementsByTagName('input');

	for (var i = 0; i < aInputs.length; i++) {		
		 aInputs[i].onclick = aInputs[i].onkeyup = upit;
	}
}

window.onload = blurry;
<?php endif; ?>

<?php if ( isset( $editing ) ) : ?>
var elem;
var eID = 'content';
var cID = 'twWPAutoSave';

function createCookie(name,value,days){
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name + "=" + escape(value ) + expires + "; path=/";
}

function eraseCookie(name){
	createCookie(name,"",-1);
}

function readCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

function KeyPressEvent(event){
	if (document.all) {
		event = window.event;
	}
	if (event.which){
		key = event.which;
	} else {
		key = event.keyCode;
	}
	createCookie(cID,elem.value, 7)
}

function WhenLoaded(){
var postdiv;
	if (postdiv = document.getElementById('poststuff')){
		var data = postdiv.innerHTML
		var index = data.indexOf('<p class="submit">') + 18;
		var after = data.substring(index);
		var before = data.substring(0, index);
		postdiv.innerHTML = before + '<input type="submit" id="AutoRestore" value="Restore" onclick="restoreData(); return false;" />' + after;

		edCanvas = document.getElementById('content');  //re-enable quicktags with correct element (original content element was overwritten).
		
		elem = document.getElementById('content');
		elem.onkeyup = KeyPressEvent;
	}
}

function restoreData(){
	elem.value = readCookie(cID);
}
<?php endif; ?>
//]]>
</script>

<?php do_action('admin_head', ''); ?>
</head>
<body>

<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/'; ?>"><?php _e('View site') ?> &raquo;</a>)</span></h1>
</div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
