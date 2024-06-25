<?php
/**
 * Add Link Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_links' ) ) {
	wp_die( __( 'Sorry, you are not allowed to add links to this site.' ) );
}

// Used in the HTML title tag.
$title       = __( 'Add New Link' );
$parent_file = 'link-manager.php';

$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';
$cat_id  = ! empty( $_REQUEST['cat_id'] ) ? absint( $_REQUEST['cat_id'] ) : 0;
$link_id = ! empty( $_REQUEST['link_id'] ) ? absint( $_REQUEST['link_id'] ) : 0;

wp_enqueue_script( 'link' );
wp_enqueue_script( 'xfn' );

if ( wp_is_mobile() ) {
	wp_enqueue_script( 'jquery-touch-punch' );
}

$link = get_default_link_to_edit();
require ABSPATH . 'wp-admin/edit-link-form.php';

require_once ABSPATH . 'wp-admin/admin-footer.php';
