<?php
require_once('admin.php');
$title = __('New Page');
$parent_file = 'post.php';
require_once('admin-header.php');

get_currentuserinfo();
?>

<?php if ( isset($_GET['saved']) ) : ?>
<div class="updated"><p><strong><?php _e('Page saved.') ?> <a href="edit-pages.php"><?php _e('Manage pages'); ?> &raquo;</a></strong></p></div>
<?php endif; ?>

<?php
if ($user_level > 0) {
	$action = 'post';
	get_currentuserinfo();
	//set defaults
	$post_status = 'static';
	$comment_status = get_settings('default_comment_status');
	$ping_status = get_settings('default_ping_status');
	$post_pingback = get_settings('default_pingback_flag');
	$post_parent = 0;
	$page_template = 'default';

	include('edit-page-form.php');
}
?>

<?php include('admin-footer.php'); ?> 