<?php
/**
 * WordPress Administration Template Header
 *
 * @package WordPress
 * @subpackage Administration
 */

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if ( ! defined( 'WP_ADMIN' ) )
	require_once( './admin.php' );

// In case admin-header.php is included in a function.
global $title, $hook_suffix, $current_screen, $wp_locale, $pagenow, $wp_version, $is_iphone,
	$user_identity, $current_site, $update_title, $total_update_count, $parent_file;

get_admin_page_title();
$title = esc_html( strip_tags( $title ) );

if ( is_network_admin() )
	$admin_title = __( 'Network Admin' );
elseif ( is_user_admin() )
	$admin_title = __( 'Global Dashboard' );
else
	$admin_title = get_bloginfo( 'name' );

if ( $admin_title == $title )
	$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), $title );
else
	$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress' ), $title, $admin_title );

$admin_title = apply_filters( 'admin_title', $admin_title, $title );

wp_user_settings();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php echo $admin_title; ?></title>
<?php

wp_enqueue_style( 'colors' );
wp_enqueue_style( 'ie' );
wp_enqueue_script('utils');

$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {
		'url': '<?php echo SITECOOKIEPATH; ?>',
		'uid': '<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>',
		'time':'<?php echo time() ?>'
	},
	ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>',
	pagenow = '<?php echo $current_screen->id; ?>',
	typenow = '<?php if ( isset($current_screen->post_type) ) echo $current_screen->post_type; ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>;
</script>
<?php

do_action('admin_enqueue_scripts', $hook_suffix);
do_action("admin_print_styles-$hook_suffix");
do_action('admin_print_styles');
do_action("admin_print_scripts-$hook_suffix");
do_action('admin_print_scripts');
do_action("admin_head-$hook_suffix");
do_action('admin_head');

if ( get_user_setting('mfold') == 'f' )
	$admin_body_class .= ' folded';
else
	$admin_body_class .= ' expanded';

if ( is_admin_bar_showing() )
	$admin_body_class .= ' admin-bar';

if ( is_rtl() )
	$admin_body_class .= ' rtl';

$admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
$admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
$admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );

if ( $is_iphone ) { ?>
<style type="text/css">.row-actions{visibility:visible;}</style>
<?php } ?>
</head>
<body class="wp-admin no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">
<script type="text/javascript">
document.body.className = document.body.className.replace(/no-js/, 'js');
if ( document.body.clientWidth > 1300 )
	document.body.className += ' wide-window';
</script>

<div id="wpwrap">
<?php require(ABSPATH . 'wp-admin/menu-header.php'); ?>
<div id="wpcontent">

<?php
do_action('in_admin_header');
?>

<div id="wpbody">
<?php
unset($title_class, $blog_name, $total_update_count, $update_title);

$current_screen->parent_file = $parent_file;
$current_screen->parent_base = preg_replace('/\?.*$/', '', $parent_file);
$current_screen->parent_base = str_replace('.php', '', $current_screen->parent_base);
?>

<div id="wpbody-content">
<?php
screen_meta($current_screen);

if ( is_network_admin() )
	do_action('network_admin_notices');
elseif ( is_user_admin() )
	do_action('user_admin_notices');
else
	do_action('admin_notices');

do_action('all_admin_notices');

if ( $parent_file == 'options-general.php' )
	require(ABSPATH . 'wp-admin/options-head.php');
