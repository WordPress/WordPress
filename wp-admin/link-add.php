<?php
/**
 * Add Link Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Add New Link');
$parent_file = 'link-manager.php';

wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image',
	'description', 'visible', 'target', 'category', 'link_id',
	'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
	'notes', 'linkcheck[]'));

wp_enqueue_script('link');
wp_enqueue_script('xfn');

$link_added = ( isset($_GET['added']) && '' != $_POST['link_name'] ) ?
	'<div id="message" class="updated fade"><p>' . __('Link added.') . '</p></div>' : '';

require('admin-header.php');
?>

<?php if ( isset( $_GET['added'] ) && '' != $_POST['link_name']) : ?>
<div id="message" class="updated fade"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>

<?php
$link = get_default_link_to_edit();
include('edit-link-form.php');

require('admin-footer.php');
?>