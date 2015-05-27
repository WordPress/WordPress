<?php
/**
 * WordPress Post Thumbnail Template Functions.
 *
 * Support for post thumbnails.
 * Theme's functions.php must call add_theme_support( 'post-thumbnails' ) to use these.
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
 * @param int|null $post_id Optional. Post ID.
 * @return mixed
 */
function get_post_thumbnail_id( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	return get_post_meta( $post_id, '_thumbnail_id', true );
}

/**
 * Display the post thumbnail.
 *
 * When a theme adds 'post-thumbnail' support, a special 'post-thumbnail' image size
 * is registered, which differs from the 'thumbnail' image size managed via the
 * Settings > Media screen.
 *
 * When using the_post_thumbnail() or related functions, the 'post-thumbnail' image
 * size is used by default, though a different size can be specified instead as needed.
 *
 * @since 2.9.0
 *
 * @see get_the_post_thumbnail()
 *
 * @param string|array $size Optional. Registered image size to use, or flat array of height
 *                           and width values. Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 */
function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	echo get_the_post_thumbnail( null, $size, $attr );
}

/**
 * Update cache for thumbnails in the current loop
 *
 * @since 3.2.0
 *
 * @global WP_Query $wp_query
 *
 * @param WP_Query $wp_query Optional. A WP_Query instance. Defaults to the $wp_query global.
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
 * Retrieve the post thumbnail.
 *
 * When a theme adds 'post-thumbnail' support, a special 'post-thumbnail' image size
 * is registered, which differs from the 'thumbnail' image size managed via the
 * Settings > Media screen.
 *
 * When using the_post_thumbnail() or related functions, the 'post-thumbnail' image
 * size is used by default, though a different size can be specified instead as needed.
 *
 * @since 2.9.0
 *
 * @param int $post_id       Post ID. Default is the ID of the `$post` global.
 * @param string|array $size Optional. Registered image size to use, or flat array of height
 *                           and width values. Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 * @return string
 */
function get_the_post_thumbnail( $post_id = null, $size = 'post-thumbnail', $attr = '' ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	$post_thumbnail_id = get_post_thumbnail_id( $post_id );

	/**
	 * Filter the post thumbnail size.
	 *
	 * @since 2.9.0
	 *
	 * @param string $size The post thumbnail size.
	 */
	$size = apply_filters( 'post_thumbnail_size', $size );

	if ( $post_thumbnail_id ) {

		/**
		 * Fires before fetching the post thumbnail HTML.
		 *
		 * Provides "just in time" filtering of all filters in wp_get_attachment_image().
		 *
		 * @since 2.9.0
		 *
		 * @param string $post_id           The post ID.
		 * @param string $post_thumbnail_id The post thumbnail ID.
		 * @param string $size              The post thumbnail size.
		 */
		do_action( 'begin_fetch_post_thumbnail_html', $post_id, $post_thumbnail_id, $size );
		if ( in_the_loop() )
			update_post_thumbnail_cache();
		$html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

		/**
		 * Fires after fetching the post thumbnail HTML.
		 *
		 * @since 2.9.0
		 *
		 * @param string $post_id           The post ID.
		 * @param string $post_thumbnail_id The post thumbnail ID.
		 * @param string $size              The post thumbnail size.
		 */
		do_action( 'end_fetch_post_thumbnail_html', $post_id, $post_thumbnail_id, $size );

	} else {
		$html = '';
	}
	/**
	 * Filter the post thumbnail HTML.
	 *
	 * @since 2.9.0
	 *
	 * @param string $html              The post thumbnail HTML.
	 * @param string $post_id           The post ID.
	 * @param string $post_thumbnail_id The post thumbnail ID.
	 * @param string $size              The post thumbnail size.
	 * @param string $attr              Query string of attributes.
	 */
	return apply_filters( 'post_thumbnail_html', $html, $post_id, $post_thumbnail_id, $size, $attr );
}
