<?php

require_once('../wp-config.php');
require_once(ABSPATH.'/wp-admin/auth.php');
require(ABSPATH.'/wp-admin/admin-functions.php');

function gethelp_link($this_file, $helptag) {
    $url = 'http://wordpress.org/docs/reference/links/#'.$helptag;
    $s = ' <a href="'.$url.'" title="' . __('Click here for help') .'">?</a>';
    return $s;
}

if (!isset($blogID))    $blog_ID=1;
if (!isset($debug))        $debug=0;
timer_start();

$dogs = $wpdb->get_results("SELECT * FROM $tablecategories WHERE 1=1");
foreach ($dogs as $catt) {
    $cache_categories[$catt->cat_ID] = $catt;
}

get_currentuserinfo();

$posts_per_page = get_settings('posts_per_page');
$what_to_show = get_settings('what_to_show');
$archive_mode = get_settings('archive_mode');
$date_format = stripslashes(get_settings('date_format'));
$time_format = stripslashes(get_settings('time_format'));

// let's deactivate quicktags on IE Mac and Lynx, because they don't work there.
if (($is_macIE) || ($is_lynx))
    $use_quicktags = 0;

$wpvarstoreset = array('profile','standalone','redirect','redirect_url','a','popuptitle','popupurl','text', 'trackback', 'pingback');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($_POST["$wpvar"])) {
            if (empty($_GET["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $_GET["$wpvar"];
            }
        } else {
            $$wpvar = $_POST["$wpvar"];
        }
    }
}

if ($standalone == 0) :

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WordPress &rsaquo; <?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?></title>
<link rel="stylesheet" href="wp-admin.css" type="text/css" />
<link rel="shortcut icon" href="../wp-images/wp-favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
<?php
if ($redirect==1) {
?>
<script language="javascript" type="text/javascript">
<!--
function redirect() {
  window.location = "<?php echo $redirect_url; ?>";
}
setTimeout("redirect();", 600);
//-->
</script>
<?php
} // redirect
?>

<?php if (isset($xfn)) : ?>
<script language="javascript" type="text/javascript">
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
<h1><a href="http://wordpress.org" rel="external" title="<?php _e('Visit WordPress.org') ?>"><?php _e('WordPress') ?></a></h1>
</div>

<?php
require('./menu.php');
endif;
?>