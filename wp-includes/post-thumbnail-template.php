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
 * Determines whether a post has an image attached.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.9.0
 * @since 4.4.0 `$post` can be a post ID or WP_Post object.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 * @return bool Whether the post has an image attached.
 */
function has_post_thumbnail( $post = null ) {
	$thumbnail_id  = get_post_thumbnail_id( $post );
	$has_thumbnail = (bool) $thumbnail_id;

	/**
	 * Filters whether a post has a post thumbnail.
	 *
	 * @since 5.1.0
	 *
	 * @param bool             $has_thumbnail true if the post has a post thumbnail, otherwise false.
	 * @param int|WP_Post|null $post          Post ID or WP_Post object. Default is global `$post`.
	 * @param int|string       $thumbnail_id  Post thumbnail ID or empty string.
	 */
	return (bool) apply_filters( 'has_post_thumbnail', $has_thumbnail, $post, $thumbnail_id );
}

/**
 * Retrieve post thumbnail ID.
 *
 * @since 2.9.0
 * @since 4.4.0 `$post` can be a post ID or WP_Post object.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 * @return string|int Post thumbnail ID or empty string.
 */
function get_post_thumbnail_id( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	return get_post_meta( $post->ID, '_thumbnail_id', true );
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
 * @param string|array $size Optional. Image size to use. Accepts any valid image size, or
 *                           an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 */
function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	echo get_the_post_thumbnail( null, $size, $attr );
}

/**
 * Update cache for thumbnails in the current loop.
 *
 * @since 3.2.0
 *
 * @global WP_Query $wp_query WordPress Query object.
 *
 * @param WP_Query $wp_query Optional. A WP_Query instance. Defaults to the $wp_query global.
 */
function update_post_thumbnail_cache( $wp_query = null ) {
	if ( ! $wp_query ) {
		$wp_query = $GLOBALS['wp_query'];
	}

	if ( $wp_query->thumbnails_cached ) {
		return;
	}

	$thumb_ids = array();
	foreach ( $wp_query->posts as $post ) {
		$id = get_post_thumbnail_id( $post->ID );
		if ( $id ) {
			$thumb_ids[] = $id;
		}
	}

	if ( ! empty( $thumb_ids ) ) {
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
 * @since 4.4.0 `$post` can be a post ID or WP_Post object.
 *
 * @param int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global `$post`.
 * @param string|array $size Optional. Image size to use. Accepts any valid image size, or
 *                           an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 * @param string|array $attr Optional. Query string or array of attributes. Default empty.
 * @return string The post thumbnail image tag.
 */
function get_the_post_thumbnail( $post = null, $size = 'post-thumbnail', $attr = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$post_thumbnail_id = get_post_thumbnail_id( $post );

	/**
	 * Filters the post thumbnail size.
	 *
	 * @since 2.9.0
	 * @since 4.9.0 Added the `$post_id` parameter.
	 *
	 * @param string|array $size    The post thumbnail size. Image size or array of width and height
	 *                              values (in that order). Default 'post-thumbnail'.
	 * @param int          $post_id The post ID.
	 */
	$size = apply_filters( 'post_thumbnail_size', $size, $post->ID );

	if ( $post_thumbnail_id ) {

		/**
		 * Fires before fetching the post thumbnail HTML.
		 *
		 * Provides "just in time" filtering of all filters in wp_get_attachment_image().
		 *
		 * @since 2.9.0
		 *
		 * @param int          $post_id           The post ID.
		 * @param string       $post_thumbnail_id The post thumbnail ID.
		 * @param string|array $size              The post thumbnail size. Image size or array of width
		 *                                        and height values (in that order). Default 'post-thumbnail'.
		 */
		do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );
		if ( in_the_loop() ) {
			update_post_thumbnail_cache();
		}
		$html = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );

		/**
		 * Fires after fetching the post thumbnail HTML.
		 *
		 * @since 2.9.0
		 *
		 * @param int          $post_id           The post ID.
		 * @param string       $post_thumbnail_id The post thumbnail ID.
		 * @param string|array $size              The post thumbnail size. Image size or array of width
		 *                                        and height values (in that order). Default 'post-thumbnail'.
		 */
		do_action( 'end_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );

	} else {
		$html = '';
	}
	/**
	 * Filters the post thumbnail HTML.
	 *
	 * @since 2.9.0
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param string       $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size. Image size or array of width and height
	 *                                        values (in that order). Default 'post-thumbnail'.
	 * @param string       $attr              Query string of attributes.
	 */
	return apply_filters( 'post_thumbnail_html', $html, $post->ID, $post_thumbnail_id, $size, $attr );
}

/**
 * Return the post thumbnail URL.
 *
 * @since 4.4.0
 *
 * @param int|WP_Post  $post Optional. Post ID or WP_Post object.  Default is global `$post`.
 * @param string|array $size Optional. Registered image size to retrieve the source for or a flat
 *                           array of height and width dimensions. Default 'post-thumbnail'.
 * @return string|false Post thumbnail URL or false if no URL is available.
 */
function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post );
	if ( ! $post_thumbnail_id ) {
		return false;
	}
	return wp_get_attachment_image_url( $post_thumbnail_id, $size );
}

/**
 * Display the post thumbnail URL.
 *
 * @since 4.4.0
 *
 * @param string|array $size Optional. Image size to use. Accepts any valid image size,
 *                           or an array of width and height values in pixels (in that order).
 *                           Default 'post-thumbnail'.
 */
function the_post_thumbnail_url( $size = 'post-thumbnail' ) {
	$url = get_the_post_thumbnail_url( null, $size );
	if ( $url ) {
		echo esc_url( $url );
	}
}

/**
 * Returns the post thumbnail caption.
 *
 * @since 4.6.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 * @return string Post thumbnail caption.
 */
function get_the_post_thumbnail_caption( $post = null ) {
	$post_thumbnail_id = get_post_thumbnail_id( $post );
	if ( ! $post_thumbnail_id ) {
		return '';
	}

	$caption = wp_get_attachment_caption( $post_thumbnail_id );

	if ( ! $caption ) {
		$caption = '';
	}

	return $caption;
}

/**
 * Displays the post thumbnail caption.
 *
 * @since 4.6.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global `$post`.
 */
function the_post_thumbnail_caption( $post = null ) {
	/**
	 * Filters the displayed post thumbnail caption.
	 *
	 * @since 4.6.0
	 *
	 * @param string $caption Caption for the given attachment.
	 */
	echo apply_filters( 'the_post_thumbnail_caption', get_the_post_thumbnail_caption( $post ) );
}
