<?php
/**
 * WordPress Administration Template Header
 *
 * @package WordPress
 * @subpackage Administration
 */

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');

get_admin_page_title();
$title = wp_specialchars( strip_tags( $title ) );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<?php

wp_admin_css( 'css/global' );
wp_admin_css();
wp_admin_css( 'css/colors' );
wp_admin_css( 'css/ie' );

?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func) {if (typeof jQuery != "undefined") jQuery(document).ready(func); else if (typeof wpOnload!='function'){wpOnload=func;} else {var oldonload=wpOnload; wpOnload=function(){oldonload();func();}}};
//]]>
</script>
<?php

switch ( $pagenow ) {
	case 'post.php':
		add_action( 'admin_head-post.php', 'wp_tiny_mce' );
		break;
	case 'post-new.php':
		add_action( 'admin_head-post-new.php', 'wp_tiny_mce' );
		break;
	case 'page.php':
		add_action( 'admin_head-page.php', 'wp_tiny_mce' );
		break;
	case 'page-new.php':
		add_action( 'admin_head-page-new.php', 'wp_tiny_mce' );
		break;
}

$hook_suffixes = array();

if ( isset($page_hook) )
	$hook_suffixes[] = "-$page_hook";
else if ( isset($plugin_page) )
	$hook_suffixes[] = "-$plugin_page";
else if ( isset($pagenow) )
	$hook_suffixes[] = "-$pagenow";

$hook_suffixes[] = '';

foreach ( $hook_suffixes as $hook_suffix )
	do_action("admin_print_styles$hook_suffix"); // do_action( 'admin_print_styles-XXX' ); do_action( 'admin_print_styles' );
foreach ( $hook_suffixes as $hook_suffix )
	do_action("admin_print_scripts$hook_suffix"); // do_action( 'admin_print_scripts-XXX' ); do_action( 'admin_print_scripts' );
foreach ( $hook_suffixes as $hook_suffix )
	do_action("admin_head$hook_suffix"); // do_action( 'admin_head-XXX' ); do_action( 'admin_head' );
unset($hook_suffixes, $hook_suffix);

?>
</head>
<body class="wp-admin <?php echo apply_filters( 'admin_body_class', '' ); ?>">
<div id="wpwrap">
<div id="wpcontent">
<div id="wphead">
<?php
$blog_name = get_bloginfo('name', 'display');
if ( '' == $blog_name )
	$blog_name = '&nbsp;';
$title_class = '';
if ( function_exists('mb_strlen') && mb_strlen($blog_name, 'UTF-8') > 30 )
	$title_class = 'class="long-title"';
?>

<img id="logo50" src="images/wp-logo.gif" alt="" /> <h1 <?php echo $title_class ?>><a href="<?php echo trailingslashit( get_bloginfo('url') ); ?>" title="<?php _e('Visit site') ?>"><?php echo $blog_name ?></a></h1>

<div id="wphead-info">
<div id="user_info">
<p><?php printf(__('Howdy, <a href="%1$s" title="Edit your profile">%2$s</a>'), 'profile.php', $user_identity) ?> |
<a href="<?php echo wp_logout_url() ?>" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a></p>
</div>

<?php favorite_actions(); ?>
</div>
</div>

<div id="wpbody">
<?php require(ABSPATH . 'wp-admin/menu-header.php'); ?>

<div id="wpbody-content">
<?php
do_action('admin_notices');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . 'wp-admin/options-head.php');
}
