<?php
require_once('admin.php');
$title = __('New Page');
$parent_file = 'post-new.php';
$editing = true;
wp_enqueue_script('autosave');
wp_enqueue_script('page');
if ( user_can_richedit() )
	wp_enqueue_script('editor');
add_thickbox();
wp_enqueue_script('media-upload');
wp_enqueue_script('word-count');

require_once('admin-header.php');
?>

<?php if ( (isset($_GET['posted']) && $_GET['posted'])  || isset($_GET['saved'])  ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Page saved.') ?></strong> <a href="edit-pages.php"><?php _e('Manage pages'); ?></a> | <a href="<?php echo get_page_link( isset($_GET['posted']) ? $_GET['posted'] : $_GET['saved'] ); ?>"><?php _e('View page') ; ?></a></p></div>
<?php endif; ?>

<?php
if ( current_user_can('edit_pages') ) {
	$action = 'post';
	$post = get_default_page_to_edit();

	include('edit-page-form.php');
}
?>

<?php include('admin-footer.php'); ?>
