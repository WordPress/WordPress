<?php
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php');
if ( $editing ) {
	$dbx_js = true;
	$pmeta_js = true;
	$list_js = true;
	if ( current_user_can('manage_categories') ) {
		$cat_js = true;
	}
}
if ( $list_js )
	$sack_js = true;
?>
<?php get_admin_page_title(); ?>
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
<script type="text/javascript" src="../wp-includes/js/fat.js"></script>
<?php if ( $xfn_js ) { ?>
<script type="text/javascript" src="xfn.js"></script>
<?php } ?>
<?php if ( $sack_js ) { ?>
<script type="text/javascript" src="../wp-includes/js/tw-sack.js"></script>
<?php } ?>
<?php if ( $list_js ) { ?>
<script type="text/javascript" src="list-manipulation-js.php"></script>
<?php } ?>
<?php if ( $pmeta_js ) { ?>
<script type="text/javascript" src="custom-fields.js"></script>
<?php } ?>
<?php if ( 'categories.php' == $pagenow && 'edit' != $action ) { ?>
<script type="text/javascript" src="categories.js"></script>
<?php } ?>
<?php if ( $users_js ) { ?>
<script type="text/javascript" src="users.js"></script>
<?php } ?>
<?php if ( 'edit-comments.php' == $pagenow || ( 'edit.php' == $pagenow && 1 == $_GET['c'] ) ) { ?>
<script type="text/javascript" src="edit-comments.js"></script>
<?php } ?>
<?php if ( $dbx_js ) { ?>
<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
<script type="text/javascript">
//<![CDATA[
addLoadEvent( function() {
<?php switch ( $pagenow ) : case 'post.php' : case 'post-new.php' : ?>
var manager = new dbxManager('postmeta');
<?php break; case 'page.php' : case 'page-new.php' : ?>
var manager = new dbxManager('pagemeta');
<?php break; case 'link.php' : case 'link-add.php' : ?>
var manager = new dbxManager('linkmeta');
<?php break; endswitch; ?>
});
//]]>
</script>
<script type="text/javascript" src="../wp-includes/js/dbx-key.js"></script>
<?php } ?>
<?php if ( $editing && user_can_richedit() ) { tinymce_include(); } ?>
<?php if ( $cat_js ) { ?>
<script type="text/javascript" src="cat-js.php"></script>
<?php } ?>
<?php if ( ($parent_file != 'link-manager.php') && ($parent_file != 'options-general.php') ) : ?>
<style type="text/css">* html { overflow-x: hidden; }</style>
<?php endif; ?>
<?php do_action('admin_head'); ?>
</head>
<body>
<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/'; ?>"><?php _e('View site &raquo;') ?></a>)</span></h1>
</div>
<div id="user_info"><p><?php printf(__('Howdy, <strong>%s</strong>.'), $user_identity) ?> [<a href="<?php echo get_settings('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Sign Out'); ?></a>, <a href="profile.php"><?php _e('My Account'); ?></a>] </p></div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
