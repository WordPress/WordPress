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

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
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

addLoadEvent(blurry);
<?php endif; ?>
//]]>
</script>
<script type="text/javascript" src="fat.js"></script>
<?php if ( get_option('rich_editing') ) :?>
<script type="text/javascript" src="tinymce/tiny_mce_src.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "title",
	width : "100%",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,image,emotions,separator,undo,redo,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	entity_encoding : "raw",
	extended_valid_elements : "a[id|href|title|onclick],img[class|src|alt|title|width|height|align]",
	plugins : "emotions"
	<?php do_action('mce_options'); ?>
});
</script>
<?php endif; ?>
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

<div id="user_info"><p><?php printf(__('Howdy, <strong>%s</strong>.'), $user_identity) ?> [<a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Sign Out'); ?></a>, <a href="profile.php"><?php _e('My Account'); ?></a>] </p></div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
