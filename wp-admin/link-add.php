<?php
/**
 * Add Link Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can('manage_links') )
	wp_die(__('You do not have sufficient permissions to add links to this site.'));

$title = __('Add New Link');
$parent_file = 'link-manager.php';

wp_reset_vars(array('action', 'cat_id', 'linkurl', 'name', 'image',
	'description', 'visible', 'target', 'category', 'link_id',
	'submit', 'order_by', 'links_show_cat_id', 'rating', 'rel',
	'notes', 'linkcheck[]'));

wp_enqueue_script('link');
wp_enqueue_script('xfn');

$link = get_default_link_to_edit();
include('./edit-link-form.php');

require('./admin-footer.php');
