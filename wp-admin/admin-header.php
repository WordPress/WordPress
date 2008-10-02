<?php
/**
 * WordPress Administration Template Header
 *
 * @package WordPress
 * @subpackage Administration
 */

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');
wp_enqueue_script( 'wp-gears' );

$min_width_pages = array( 'post.php', 'post-new.php', 'page.php', 'page-new.php', 'widgets.php', 'comment.php', 'link.php' );
$the_current_page = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$ie6_no_scrollbar = true;

/**
 * Append 'minwidth' to value.
 *
 * @param mixed $c
 * @return string
 */
function add_minwidth($c) {
	return $c . 'minwidth ';
}

if ( in_array( $the_current_page, $min_width_pages ) ) {
		$ie6_no_scrollbar = false;
		add_filter( 'admin_body_class', 'add_minwidth' );
}

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
<?php if ( ($parent_file != 'link-manager.php') && ($parent_file != 'options-general.php') && $ie6_no_scrollbar ) : ?>
<style type="text/css">* html { overflow-x: hidden; }</style>
<?php endif;

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
<div id="sidemenu-bg"><br /></div>
<div id="wpcontent">
<div id="wphead">
<?php
if ( 'index.php' == $pagenow ) {
	$breadcrumb = __('Dashboard');
} else {
	$breadcrumb = '<a href="index.php">' . __('Dashboard') . '</a> &rsaquo; ' . $title;
}
?>
<img id="logo50" src="images/logo50.png" alt="" /> <h1><?php if ( '' == get_bloginfo('name', 'display') ) echo '&nbsp;'; else echo get_bloginfo('name', 'display'); ?> <a href="<?php echo trailingslashit( get_bloginfo('url') ); ?>" title="View site" id="view-site-link"><img src="<?php echo trailingslashit( bloginfo('wpurl') ) . 'wp-admin/images/new-window-icon.gif'; ?>" alt="View site" /></a><span id="breadcrumb"><?php echo $breadcrumb ?></span></h1>
</div>

<div id="user_info"><p><?php printf(__('Howdy, <a href="%1$s">%2$s</a>!'), 'profile.php', $user_identity) ?> <a href="<?php echo wp_logout_url() ?>" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a></p></div>
<?php
require(ABSPATH . 'wp-admin/menu-header.php');
?>
<div id="wpbody">
<div id="wpbody-content">
<?php
do_action('admin_notices');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . 'wp-admin/options-head.php');
}

favorite_actions();

$settings_pages = array( 'categories.php', 'edit.php', 'edit-comments.php', 'edit-form-advanced.php', 'edit-link-categories.php', 'edit-link-form.php', 'edit-page-form.php', 'edit-tags.php', 'link-manager.php', 'upload.php', 'users.php', 'edit-pages.php', 'post-new.php' );

if ( in_array( $pagenow, $settings_pages ) ) { ?>
<div id="edit-settings">
<a href="#edit_settings" id="show-settings-link" class="hide-if-no-js show-settings"><?php _e('Page Options') ?></a>
<a href="#edit_settings" id="hide-settings-link" class="show-settings" style="display:none;"><?php _e('Hide Options') ?></a>
</div>
<?php } ?>
