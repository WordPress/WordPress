<?php
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');
if ( $editing ) {
	if ( user_can_richedit() )
		wp_enqueue_script( 'wp_tiny_mce' );
}
wp_enqueue_script( 'wp-gears' );

$min_width_pages = array( 'post.php', 'post-new.php', 'page.php', 'page-new.php', 'widgets.php', 'comment.php', 'link.php' );
$the_current_page = preg_replace('|^.*/wp-admin/|i', '', $_SERVER['PHP_SELF']);
$ie6_no_scrollbar = true;

function add_minwidth($c) {
	return $c . 'minwidth ';
}

if ( in_array( $the_current_page, $min_width_pages ) ) {
		$ie6_no_scrollbar = false;
		add_filter( 'admin_body_class', 'add_minwidth' );
}

get_admin_page_title();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo wp_specialchars( strip_tags( $title ) ); ?> &#8212; WordPress</title>
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
<div id="wpcontent">
<div id="wphead">
<h1><?php if ( '' == get_bloginfo('name', 'display') ) echo '&nbsp;'; else echo get_bloginfo('name', 'display'); ?><span id="viewsite"><a href="<?php echo trailingslashit( get_option('home') ); ?>"><?php _e('Visit Site') ?></a></span></h1>
</div>

<?php
if ( ! $is_opera ) { 
?>
	<div id="gears-info-box" class="info-box" style="display:none;">
	<img src="images/gear.png" title="Gear" alt="" class="gears-img" />
	<div id="gears-msg1">
	<h3 class="info-box-title"><?php _e('Speed up WordPress'); ?></h3>
	<p><?php _e('WordPress now has support for Gears, which adds new features to your web browser.'); ?><br />
	<a href="http://gears.google.com/" target="_blank" style="font-weight:normal;"><?php _e('More information...'); ?></a></p>
	<p><?php _e('After you install and enable Gears most of WordPress&#8217; images, scripts, and CSS files will be stored locally on your computer. This speeds up page load time.'); ?></p>
	<p><strong><?php _e('Don&#8217;t install on a public or shared computer.'); ?></strong></p>	<div class="submit"><button onclick="window.location = 'http://gears.google.com/?action=install&amp;return=<?php echo urlencode( admin_url() ); ?>';" class="button"><?php _e('Install Now'); ?></button>
	<button class="button" style="margin-left:10px;" onclick="document.getElementById('gears-info-box').style.display='none';"><?php _e('Cancel'); ?></button></div>
	</div>

	<div id="gears-msg2" style="display:none;">
	<h3 class="info-box-title"><?php _e('Gears Status'); ?></h3>
	<p><?php _e('Gears is installed on this computer but is not enabled for use with WordPress.'); ?></p> 
	<p><?php 
	
	if ( $is_safari )
		_e('To enable it, make sure this web site is not on the denied list in Gears Settings under the Safari menu, then click the button below.');
	else
		_e('To enable it, make sure this web site is not on the denied list in Gears Settings under your browser Tools menu, then click the button below.'); 
	
	?></p>
	<p><strong><?php _e('However if this is a public or shared computer, Gears should not be enabled.'); ?></strong></p>
	<div class="submit"><button class="button" onclick="wpGears.getPermission();"><?php _e('Enable Gears'); ?></button>
	<button class="button" style="margin-left:10px;" onclick="document.getElementById('gears-info-box').style.display='none';"><?php _e('Cancel'); ?></button></div>
	</div>

	<div id="gears-msg3" style="display:none;">
	<h3 class="info-box-title"><?php _e('Gears Status'); ?></h3>
	<p><?php
	
	if ( $is_safari )
		_e('Gears is installed and enabled on this computer. You can disable it from the Safari menu.');
	else
		_e('Gears is installed and enabled on this computer. You can disable it from your browser Tools menu.'); 
	
	?></p>
	<p><?php _e('If there are any errors, try disabling Gears, then reload the page and enable it again.'); ?></p>
	<p><?php _e('Local storage status:'); ?> <span id="gears-wait"><span style="color:#f00;"><?php _e('Please wait! Updating files:'); ?></span> <span id="gears-upd-number"></span></span></p>
	<div class="submit"><button class="button" onclick="document.getElementById('gears-info-box').style.display='none';"><?php _e('Close'); ?></button></div>
	</div>
	</div>
<?php } ?>

<div id="user_info"><p><?php printf(__('Howdy, <a href="%1$s">%2$s</a>!'), 'profile.php', $user_identity) ?> | <a href="<?php echo site_url('wp-login.php?action=logout', 'login') ?>" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a> | <?php _e('<a href="http://codex.wordpress.org/">Help</a>') ?> | <?php _e('<a href="http://wordpress.org/support/">Forums</a>'); if ( ! $is_opera ) { ?> | <span id="gears-menu"><a href="#" onclick="wpGears.message(1);return false;"><?php _e('Turbo') ?></a></span><?php } ?></p></div>

<?php
require(ABSPATH . 'wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . 'wp-admin/options-head.php');
}
?>
<div id="wpbody">
