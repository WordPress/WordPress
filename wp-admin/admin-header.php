<?php require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../wp-images/wp-favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />

<?php if (isset($xfn)) : ?>
<script type="text/javascript">
//<![CDATA[

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

function blurry() {
	if (!document.getElementById) return;
	
	var aInputs = document.getElementsByTagName('input');
	
	for (var i = 0; i < aInputs.length; i++) {      
		aInputs[i].onclick = function() {
			var inputColl = GetElementsWithClassName('input','valinp');
			var rel = document.getElementById('rel');
			var inputs = '';
			for (i = 0; i < inputColl.length; i++) {
				if (inputColl[i].checked) {
				if (inputColl[i].value != '') inputs += inputColl[i].value + ' ';
				}
			}
			inputs = inputs.substr(0,inputs.length - 1);
			if (rel != null) {
				rel.value = inputs;
			}
		}
		
		aInputs[i].onkeyup = function() {
			var inputColl = GetElementsWithClassName('input','valinp');
			var rel = document.getElementById('rel');
			var inputs = '';
			for (i = 0; i < inputColl.length; i++) {
				if (inputColl[i].checked) {
					inputs += inputColl[i].value + ' ';
				}
			}
			inputs = inputs.substr(0,inputs.length - 1);
			if (rel != null) {
				rel.value = inputs;
			}
		}
		
	}
}

window.onload = blurry;
//]]>
</script>
<?php endif; ?>

<?php do_action('admin_head', ''); ?>
</head>
<body>

<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/' . get_settings('blogfilename'); ?>"><?php _e('View site') ?> &raquo;</a>)</span></h1>
</div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>