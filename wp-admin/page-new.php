<?php
require_once('admin.php');
$title = __('New Page');
$parent_file = 'post-new.php';
$editing = true;
wp_enqueue_script('prototype');
wp_enqueue_script('interface');
wp_enqueue_script('autosave');
require_once('admin-header.php');
?>

<?php if ( (isset($_GET['posted']) && $_GET['posted'])  || isset($_GET['saved'])  ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Page saved.') ?></strong> <a href="edit-pages.php"><?php _e('Manage pages'); ?></a> | <a href="<?php echo get_page_link( isset($_GET['posted']) ? $_GET['posted'] : $_GET['saved'] ); ?>"><?php _e('View page &raquo;') ; ?></a></p></div>
<?php endif; ?>

<?php
if ( current_user_can('edit_pages') ) {
	$action = 'post';
	$post = get_default_post_to_edit();
	$post->post_type = 'page';

	include('edit-page-form.php');
}
?>

<?php include('admin-footer.php'); ?>
