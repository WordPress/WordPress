<?php
/**
 * WordPress Post Thumbnail Template Functions.
 *
 * Support for post thumbnails
 * Themes function.php must call add_theme_support( 'post-thumbnails' ) to use these.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Check if post has an image attached.
 *
 * @since 2.9.0
 *
 * @param int $post_id Optional. Post ID.
 * @return bool Whether post has an image attached.
 */
function has_post_thumbnail( $post_id = null ) {
	return (bool) get_post_thumbnail_id( $post_id );
}

/**
 * Retrieve Post Thumbnail ID.
 *
 * @since 2.9.0
 *
 * @param int $post_id Optional. Post ID.
 * @return int
 */
function get_post_thumbnail_id( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	return get_post_meta( $post_id, '_thumbnail_id', true );
}

/**
 * Display Post Thumbnail.
 *
 * @since 2.9.0
 *
 * @param string|array $size Optional. Image size. Defaults to 'post-thumbnail', which theme sets using set_post_thumbnail_size( $width, $height, $crop_flag );.
 * @param string|array $attr Optional. Query string or array of attributes.
 */
function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	echo get_the_post_thumbnail( null, $size, $attr );
}

/**
 * Update cache for thumbnails in the current loop
 *
 * @since 3.2
 *
 * @param object $wp_query Optional. A WP_Query instance. Defaults to the $wp_query global.
 */
function update_post_thumbnail_cache( $wp_query = null ) {
	if ( ! $wp_query )
		$wp_query = $GLOBALS['wp_query'];

	if ( $wp_query->thumbnails_cached )
		return;

	$thumb_ids = array();
	foreach ( $wp_query->posts as $post ) {
		if ( $id = get_post_thumbnail_id( $post->ID ) )
			$thumb_ids[] = $id;
	}

	if ( ! empty ( $thumb_ids ) ) {
		_prime_post_caches( $thumb_ids, false, true );
	}

	$wp_query->thumbnails_cached = true;
}

/**
 * Retrieve Post Thumbnail.
 *
 * @since 2.9.0
 *
 * @param int $post_id Optional. Post ID.
 * @param string $size Optional. Image size. Defaults to 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes.
 */
function get_the_post_thumbnail( $post_id = null, $size = 'post-thumbnail', $attr = '' ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );
	$size = apply_filters( 'post_thumbnail_size', $size );
	if ( $post_thumbnail_id ) {
		do_action( 'begin_fetch_post_thumbnail_html', $post_id, $post_thumbnail_id, $size ); // for "Just In Time" filtering of all of wp_get_attachment_image()'s filters
		if ( in_the_loop() )
			update_post_thumbnail_cache();
		$html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );
		do_action( 'end_fetch_post_thumbnail_html', $post_id, $post_thumbnail_id, $size );
	} else {
		$html = '';
	}
	return apply_filters( 'post_thumbnail_html', $html, $post_id, $post_thumbnail_id, $size, $attr );
}
