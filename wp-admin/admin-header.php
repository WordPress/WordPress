<?php
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');
if ( $editing ) {
	if ( user_can_richedit() )
		wp_enqueue_script( 'wp_tiny_mce' );
}

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
?>
<!--[if gte IE 6]>
<?php wp_admin_css( 'css/ie' );
?>
<![endif]-->
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func) {if (typeof jQuery != "undefined") jQuery(document).ready(func); else if (typeof wpOnload!='function'){wpOnload=func;} else {var oldonload=wpOnload; wpOnload=function(){oldonload();func();}}};
//]]>
</script>
<?php if ( ($parent_file != 'link-manager.php') && ($parent_file != 'options-general.php') && $ie6_no_scrollbar ) : ?>
<style type="text/css">* html { overflow-x: hidden; }</style>
<?php endif;
if ( isset($page_hook) )
	do_action('admin_print_scripts-' . $page_hook);
else if ( isset($plugin_page) )
	do_action('admin_print_scripts-' . $plugin_page);
do_action('admin_print_scripts');

if ( isset($page_hook) )
	do_action('admin_head-' . $page_hook);
else if ( isset($plugin_page) )
	do_action('admin_head-' . $plugin_page);
do_action('admin_head');
?>
</head>
<body class="wp-admin <?php echo apply_filters( 'admin_body_class', '' ); ?>">
<div id="wpwrap">
<div id="wpcontent">
<div id="wphead">
<h1><?php bloginfo('name'); ?><span id="viewsite"><a href="<?php echo trailingslashit( get_option('home') ); ?>"><?php _e('Visit Site') ?></a></span></h1>
</div>
<div id="user_info"><p><?php printf(__('Howdy, <a href="%1$s">%2$s</a>!'), 'profile.php', $user_identity) ?> | <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a> | <?php _e('<a href="http://codex.wordpress.org/">Help</a>') ?> | <?php _e('<a href="http://wordpress.org/support/">Forums</a>') ?></p></div>

<?php
require(ABSPATH . 'wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . 'wp-admin/options-head.php');
}
?>
<div id="wpbody">
