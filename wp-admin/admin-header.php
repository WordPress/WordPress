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
$title = esc_html( strip_tags( $title ) );
wp_user_settings();
wp_menu_unfold();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php echo $title; ?> &lsaquo; <?php bloginfo('name') ?>  &#8212; WordPress</title>
<?php

wp_admin_css( 'css/global' );
wp_admin_css();
wp_admin_css( 'css/colors' );
wp_admin_css( 'css/ie' );
if ( is_multisite() )
	wp_admin_css( 'css/ms' );
wp_enqueue_script('utils');

$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {'url':'<?php echo SITECOOKIEPATH; ?>','uid':'<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>','time':'<?php echo time() ?>'};
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>', pagenow = '<?php echo substr($pagenow, 0, -4); ?>', typenow = '<?php echo $typenow; ?>', adminpage = '<?php echo $admin_body_class; ?>',  thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>', decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>';
//]]>
</script>
<?php

if ( in_array( $pagenow, array('post.php', 'post-new.php') ) ) {
	add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
	wp_enqueue_script('quicktags');
}

do_action('admin_enqueue_scripts', $hook_suffix);
do_action("admin_print_styles-$hook_suffix");
do_action('admin_print_styles');
do_action("admin_print_scripts-$hook_suffix");
do_action('admin_print_scripts');
do_action("admin_head-$hook_suffix");
do_action('admin_head');

if ( get_user_setting('mfold') == 'f' )
	$admin_body_class .= ' folded';

if ( $is_iphone ) { ?>
<style type="text/css">.row-actions{visibility:visible;}</style>
<?php } ?>
</head>
<body class="wp-admin no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">
<script type="text/javascript">
//<![CDATA[
(function(){
var c = document.body.className;
c = c.replace(/no-js/, 'js');
document.body.className = c;
})();
//]]>
</script>

<div id="wpwrap">
<div id="wpcontent">
<div id="wphead">
<?php
$blog_name = get_bloginfo('name', 'display');
if ( '' == $blog_name ) {
	$blog_name = '&nbsp;';
} else {
	$blog_name_excerpt = wp_html_excerpt($blog_name, 40);
	if ( $blog_name != $blog_name_excerpt )
		$blog_name_excerpt = trim($blog_name_excerpt) . '&hellip;';
	$blog_name = $blog_name_excerpt;
	unset($blog_name_excerpt);
}
$title_class = '';
if ( function_exists('mb_strlen') ) {
	if ( mb_strlen($blog_name, 'UTF-8') > 30 )
		$title_class = 'class="long-title"';
} else {
	if ( strlen($blog_name) > 30 )
		$title_class = 'class="long-title"';
}
?>

<img id="header-logo" src="<?php echo esc_url( includes_url( 'images/blank.gif' ) ); ?>" alt="" width="32" height="32" /> <h1 id="site-heading" <?php echo $title_class ?>><a href="<?php echo trailingslashit( get_bloginfo('url') ); ?>" title="<?php esc_attr_e('Visit Site') ?>"><span id="site-title"><?php echo $blog_name ?></span> <em id="site-visit-button"><?php _e('Visit Site') ?></em></a><?php if ( ! get_option('blog_public') ) { ?> <a id="privacy-on-link" href="options-privacy.php" title="<?php esc_attr_e('Your blog is hidden from search engines') ?>"><?php _e('Privacy On') ?></a><?php } ?></h1>

<?php do_action('in_admin_header'); ?>

<div id="wphead-info">
<div id="user_info">
<p><?php
$links = array();
$links[5] = sprintf(__('Howdy, <a href="%1$s" title="Edit your profile">%2$s</a>'), 'profile.php', $user_identity);
if ( ! $is_opera )
	$links[10] = '<span class="turbo-nag hidden"> | <a href="tools.php">' . __('Turbo') . '</a></span>';
$links[15] = '| <a href="' . wp_logout_url() . '" title="' . __('Log Out') . '">' . __('Log Out') . '</a>';

$links = apply_filters('admin_user_info_links', $links, $current_user);
ksort($links);

echo implode(' ', $links);
?></p>
</div>

<?php favorite_actions($current_screen); ?>
</div>
</div>

<div id="wpbody">
<?php
unset($title_class, $blog_name);

require(ABSPATH . 'wp-admin/menu-header.php');

$current_screen->parent_file = $parent_file;
$current_screen->parent_base = preg_replace('/\?.*$/', '', $parent_file);
$current_screen->parent_base = str_replace('.php', '', $current_screen->parent_base);
?>

<div id="wpbody-content">
<?php
screen_meta($current_screen);

do_action('admin_notices');

if ( $parent_file == 'options-general.php' )
	require(ABSPATH . 'wp-admin/options-head.php');
