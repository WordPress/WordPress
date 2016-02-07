<?php
/**
 * Edit Term Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.5.0
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( empty( $_REQUEST['term_id'] ) ) {
	$sendback = admin_url( 'edit-tags.php' );
	if ( ! empty( $taxnow ) ) {
		$sendback = add_query_arg( array( 'taxonomy' => $taxnow ), $sendback );
	}
	wp_redirect( esc_url( $sendback ) );
	exit;
}

$term_id = absint( $_REQUEST['term_id'] );
$tag    = get_term( $term_id, $taxnow, OBJECT, 'edit' );

if ( ! $tag instanceof WP_Term ) {
	wp_die( __( 'You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?' ) );
}

$tax      = get_taxonomy( $tag->taxonomy );
$taxonomy = $tax->name;
$title    = $tax->labels->edit_item;

if ( ! in_array( $taxonomy, get_taxonomies( array( 'show_ui' => true ) ) ) ||
     ! current_user_can( $tax->cap->manage_terms )
) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'You are not allowed to manage this item.' ) . '</p>',
		403
	);
}

$post_type = get_current_screen()->post_type;

// Default to the first object_type associated with the taxonomy if no post type was passed.
if ( empty( $post_type ) ) {
	$post_type = reset( $tax->object_type );
}

if ( 'post' != $post_type ) {
	$parent_file  = ( 'attachment' == $post_type ) ? 'upload.php' : "edit.php?post_type=$post_type";
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type";
} elseif ( 'link_category' == $taxonomy ) {
	$parent_file  = 'link-manager.php';
	$submenu_file = 'edit-tags.php?taxonomy=link_category';
} else {
	$parent_file  = 'edit.php';
	$submenu_file = "edit-tags.php?taxonomy=$taxonomy";
}

get_current_screen()->set_screen_reader_content( array(
	'heading_pagination' => $tax->labels->items_list_navigation,
	'heading_list'       => $tax->labels->items_list,
) );

require_once( ABSPATH . 'wp-admin/admin-header.php' );
include( ABSPATH . 'wp-admin/edit-tag-form.php' );
include( ABSPATH . 'wp-admin/admin-footer.php' );
