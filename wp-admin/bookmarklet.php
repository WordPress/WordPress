<?php
$mode = 'bookmarklet';
require_once('admin.php');

if ( ! current_user_can('edit_posts') )
	wp_die(__('Cheatin&#8217; uh?'));

if ('b' == $a):
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
<!--
window.close()
-->
</script>
</head>
<body></body>
</html>
<?php
exit;
endif;

$post = get_default_post_to_edit();

$popuptitle = wp_specialchars(stripslashes($popuptitle));
$text       = wp_specialchars(stripslashes(urldecode($text)));

$popuptitle = funky_javascript_fix($popuptitle);
$text       = funky_javascript_fix($text);

$post_title = wp_specialchars($_REQUEST['post_title']);
if (!empty($post_title))
	$post->post_title =  stripslashes($post_title);
else
	$post->post_title = $popuptitle;


$content  = wp_specialchars($_REQUEST['content']);
$popupurl = clean_url($_REQUEST['popupurl']);
if ( !empty($content) ) {
	$post->post_content = wp_specialchars( stripslashes($_REQUEST['content']) );
} else {
	$post->post_content = '<a href="'.$popupurl.'">'.$popuptitle.'</a>'."\n$text";
}

/* /big funky fixes */

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; Bookmarklet &#8212; WordPress</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<?php
wp_admin_css( 'css/global' );
wp_admin_css();
?>

<style type="text/css">
<!--

#wpbookmarklet textarea,input,select {
	border-width: 1px;
	border-color: #cccccc;
	border-style: solid;
	padding: 2px;
	margin: 1px;
}

#wpbookmarklet .checkbox {
	background-color: #ffffff;
	border-width: 0px;
	padding: 0px;
	margin: 0px;
}

#wpbookmarklet textarea {
	font-family: Verdana, Geneva, Arial, Helvetica;
	font-size: 0.9em;
}

#wpbookmarklet .wrap {
	border: 0px;
}

#wpbookmarklet #postdiv {
	margin-bottom: 0.5em;
}

#wpbookmarklet #titlediv {
	margin-bottom: 1em;
}

-->
</style>
</head>
<body id="wpbookmarklet">
<div id="wphead">
<h1><?php bloginfo('name') ?></h1>
</div>

<?php require('edit-form.php'); ?>

<?php do_action('admin_footer', ''); ?>

</body>
</html>
