<?php
/**
 * New page administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');
$title = __('Add New Page');
$parent_file = 'edit-pages.php';
$editing = true;
wp_enqueue_script('autosave');
wp_enqueue_script('post');
if ( user_can_richedit() )
	wp_enqueue_script('editor');
add_thickbox();
wp_enqueue_script('media-upload');
wp_enqueue_script('word-count');
wp_enqueue_script('set-post-thumbnail-handler');

if ( current_user_can('edit_pages') ) {
	$action = 'post';
	$post = get_default_page_to_edit();

	include('edit-page-form.php');
}

include('admin-footer.php');

?>
