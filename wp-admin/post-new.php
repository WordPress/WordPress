<?php
/**
 * New Post Administration Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once('./admin.php');

if ( !isset($_GET['post_type']) )
	$post_type = 'post';
elseif ( in_array( $_GET['post_type'], get_post_types( array('show_ui' => true ) ) ) )
	$post_type = $_GET['post_type'];
else
	wp_die( __('Invalid post type') );

$post_type_object = get_post_type_object( $post_type );

if ( 'post' == $post_type ) {
	$parent_file = 'edit.php';
	$submenu_file = 'post-new.php';
} else {
	$submenu_file = "post-new.php?post_type=$post_type";
	if ( isset( $post_type_object ) && $post_type_object->show_in_menu && $post_type_object->show_in_menu !== true ) {
		$parent_file = $post_type_object->show_in_menu;
		if ( ! isset( $_registered_pages[ get_plugin_page_hookname( "post-new.php?post_type=$post_type", $post_type_object->show_in_menu ) ] ) )
			$submenu_file = $parent_file;
	} else {
		$parent_file = "edit.php?post_type=$post_type";
	}
}

$title = $post_type_object->labels->add_new_item;

$editing = true;

if ( ! current_user_can( $post_type_object->cap->edit_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

wp_enqueue_script('autosave');

// Show post form.
$post = get_default_post_to_edit( $post_type, true );
$post_ID = $post->ID;
include('edit-form-advanced.php');
include('./admin-footer.php');
