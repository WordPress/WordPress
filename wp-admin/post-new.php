<?php
/**
 * New Post Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

/**
 * @global string  $post_type
 * @global object  $post_type_object
 * @global WP_Post $post
 */
global $post_type, $post_type_object, $post;

if ( ! isset( $_GET['post_type'] ) ) {
	$post_type = 'post';
} elseif ( in_array( $_GET['post_type'], get_post_types( array( 'show_ui' => true ) ) ) ) {
	$post_type = $_GET['post_type'];
} else {
	wp_die( __( 'Invalid post type.' ) );
}
$post_type_object = get_post_type_object( $post_type );

if ( 'post' == $post_type ) {
	$parent_file  = 'edit.php';
	$submenu_file = 'post-new.php';
} elseif ( 'attachment' == $post_type ) {
	if ( wp_redirect( admin_url( 'media-new.php' ) ) ) {
		exit;
	}
} else {
	$submenu_file = "post-new.php?post_type=$post_type";
	if ( isset( $post_type_object ) && $post_type_object->show_in_menu && $post_type_object->show_in_menu !== true ) {
		$parent_file = $post_type_object->show_in_menu;
		// What if there isn't a post-new.php item for this post type?
		if ( ! isset( $_registered_pages[ get_plugin_page_hookname( "post-new.php?post_type=$post_type", $post_type_object->show_in_menu ) ] ) ) {
			if ( isset( $_registered_pages[ get_plugin_page_hookname( "edit.php?post_type=$post_type", $post_type_object->show_in_menu ) ] ) ) {
				// Fall back to edit.php for that post type, if it exists
				$submenu_file = "edit.php?post_type=$post_type";
			} else {
				// Otherwise, give up and highlight the parent
				$submenu_file = $parent_file;
			}
		}
	} else {
		$parent_file = "edit.php?post_type=$post_type";
	}
}

$title = $post_type_object->labels->add_new_item;

$editing = true;

if ( ! current_user_can( $post_type_object->cap->edit_posts ) || ! current_user_can( $post_type_object->cap->create_posts ) ) {
	wp_die(
		'<h1>' . __( 'Cheatin&#8217; uh?' ) . '</h1>' .
		'<p>' . __( 'Sorry, you are not allowed to create posts as this user.' ) . '</p>',
		403
	);
}

// Schedule auto-draft cleanup
if ( ! wp_next_scheduled( 'wp_scheduled_auto_draft_delete' ) ) {
	wp_schedule_event( time(), 'daily', 'wp_scheduled_auto_draft_delete' );
}

$post    = get_default_post_to_edit( $post_type, true );
$post_ID = $post->ID;

/** This filter is documented in wp-admin/post.php */
if ( apply_filters( 'replace_editor', false, $post ) !== true ) {
	wp_enqueue_script( 'autosave' );
	include( ABSPATH . 'wp-admin/edit-form-advanced.php' );
}

include( ABSPATH . 'wp-admin/admin-footer.php' );
