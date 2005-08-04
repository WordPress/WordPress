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

<?php if ( get_option('rich_editing') ) :?>
<script type="text/javascript" src="tinymce/tiny_mce_gzip.php"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "title",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,image,emotions,separator,undo,redo,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	entity_encoding : "numeric",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|width|height|align],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	plugins : "emotions"
});
</script>
<?php endif; ?>

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


//]]>
</script>

<?php if ( isset( $editing ) ) : ?>
<script type="text/javascript" src="dbx.js"></script>
<script type="text/javascript" src="dbx-key.js"></script>
<?php endif; ?>

<?php do_action('admin_head'); ?>
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
