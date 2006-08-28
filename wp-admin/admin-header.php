<?php
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');
if ( $editing ) {
	wp_enqueue_script( array("dbx-admin-key?pagenow=$pagenow",'admin-custom-fields') );
	if ( current_user_can('manage_categories') )
		wp_enqueue_script( 'ajaxcat' );
	if ( user_can_richedit() )
		wp_enqueue_script( 'wp_tiny_mce' );
}

get_admin_page_title();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<?php if ( ($parent_file != 'link-manager.php') && ($parent_file != 'options-general.php') ) : ?>
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
<body>
<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/'; ?>"><?php _e('View site &raquo;') ?></a>)</span></h1>
</div>
<div id="user_info"><p><?php printf(__('Howdy, <strong>%s</strong>.'), $user_identity) ?> [<a href="<?php echo get_settings('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Sign Out'); ?></a>, <a href="profile.php"><?php _e('My Profile'); ?></a>] </p></div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
