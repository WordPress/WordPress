<?php
/**
 * WordPress Post Image Template Functions.
 *
 * Support for post thumbnails
 * Themes function.php must call add_theme_support( 'post-thumbnails' ) to use these.
 *
 * @package WordPress
 * @subpackage Template
 */

function has_post_image( $post_id = NULL ) {
	global $id;
	$post_id = ( NULL === $post_id ) ? $id : $post_id;
	return !! get_post_image_id( $post_id );
}

function get_post_image_id( $post_id = NULL ) {
	global $id;
	$post_id = ( NULL === $post_id ) ? $id : $post_id;
	return get_post_meta( $post_id, '_thumbnail_id', true );
}

function the_post_image( $size = 'thumbnail', $attr = '' ) {
	echo get_the_post_image( NULL, $size, $attr );
}

function get_the_post_image( $post_id = NULL, $size = 'thumbnail', $attr = '' ) {
	global $id;
	$post_id = ( NULL === $post_id ) ? $id : $post_id;
	$post_image_id = get_post_image_id( $post_id );
	$size = apply_filters( 'post_image_size', $size );
	if ( $post_image_id ) {
		do_action( 'begin_fetch_post_image_html', $post_id, $post_image_id, $size ); // for "Just In Time" filtering of all of wp_get_attachment_image()'s filters
		$html = wp_get_attachment_image( $post_image_id, $size, false, $attr );
		do_action( 'end_fetch_post_image_html', $post_id, $post_image_id, $size );
	} else {
		$html = '';
	}
	return apply_filters( 'post_image_html', $html, $post_id, $post_image_id, $size, $attr );
}

?>