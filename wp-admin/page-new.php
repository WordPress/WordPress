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
	
	$post = get_default_post_to_edit();
	$post->post_status = 'static';

	include('edit-page-form.php');
}
?>

<?php include('admin-footer.php'); ?> 