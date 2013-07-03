<?php
/**
 * Post format functions.
 *
 * @package WordPress
 * @subpackage Post
 */

/**
 * Retrieve the format slug for a post
 *
 * @since 3.1.0
 *
 * @param int|object $post Post ID or post object. Optional, default is the current post from the loop.
 * @return mixed The format if successful. False otherwise.
 */
function get_post_format( $post = null ) {
	if ( ! $post = get_post( $post ) )
		return false;

	if ( ! post_type_supports( $post->post_type, 'post-formats' ) )
		return false;

	$_format = get_the_terms( $post->ID, 'post_format' );

	if ( empty( $_format ) )
		return false;

	$format = array_shift( $_format );

	return str_replace('post-format-', '', $format->slug );
}

/**
 * Check if a post has a particular format
 *
 * @since 3.1.0
 *
 * @uses has_term()
 *
 * @param string $format The format to check for.
 * @param object|int $post The post to check. If not supplied, defaults to the current post if used in the loop.
 * @return bool True if the post has the format, false otherwise.
 */
function has_post_format( $format, $post = null ) {
	return has_term('post-format-' . sanitize_key($format), 'post_format', $post);
}

/**
 * Assign a format to a post
 *
 * @since 3.1.0
 *
 * @param int|object $post The post for which to assign a format.
 * @param string $format A format to assign. Use an empty string or array to remove all formats from the post.
 * @return mixed WP_Error on error. Array of affected term IDs on success.
 */
function set_post_format( $post, $format ) {
	$post = get_post( $post );

	if ( empty( $post ) )
		return new WP_Error( 'invalid_post', __( 'Invalid post' ) );

	if ( ! empty( $format ) ) {
		$format = sanitize_key( $format );
		if ( 'standard' === $format || ! in_array( $format, get_post_format_slugs() ) )
			$format = '';
		else
			$format = 'post-format-' . $format;
	}

	return wp_set_post_terms( $post->ID, $format, 'post_format' );
}

/**
 * Returns an array of post format slugs to their translated and pretty display versions
 *
 * @since 3.1.0
 *
 * @return array The array of translated post format names.
 */
function get_post_format_strings() {
	$strings = array(
		'standard' => _x( 'Standard', 'Post format' ), // Special case. any value that evals to false will be considered standard
		'aside'    => _x( 'Aside',    'Post format' ),
		'chat'     => _x( 'Chat',     'Post format' ),
		'gallery'  => _x( 'Gallery',  'Post format' ),
		'link'     => _x( 'Link',     'Post format' ),
		'image'    => _x( 'Image',    'Post format' ),
		'quote'    => _x( 'Quote',    'Post format' ),
		'status'   => _x( 'Status',   'Post format' ),
		'video'    => _x( 'Video',    'Post format' ),
		'audio'    => _x( 'Audio',    'Post format' ),
	);
	return $strings;
}

/**
 * Retrieves an array of post format slugs.
 *
 * @since 3.1.0
 *
 * @uses get_post_format_strings()
 *
 * @return array The array of post format slugs.
 */
function get_post_format_slugs() {
	$slugs = array_keys( get_post_format_strings() );
	return array_combine( $slugs, $slugs );
}

/**
 * Returns a pretty, translated version of a post format slug
 *
 * @since 3.1.0
 *
 * @uses get_post_format_strings()
 *
 * @param string $slug A post format slug.
 * @return string The translated post format name.
 */
function get_post_format_string( $slug ) {
	$strings = get_post_format_strings();
	if ( !$slug )
		return $strings['standard'];
	else
		return ( isset( $strings[$slug] ) ) ? $strings[$slug] : '';
}

/**
 * Returns a link to a post format index.
 *
 * @since 3.1.0
 *
 * @param string $format The post format slug.
 * @return string The post format term link.
 */
function get_post_format_link( $format ) {
	$term = get_term_by('slug', 'post-format-' . $format, 'post_format' );
	if ( ! $term || is_wp_error( $term ) )
		return false;
	return get_term_link( $term );
}

/**
 * Filters the request to allow for the format prefix.
 *
 * @access private
 * @since 3.1.0
 */
function _post_format_request( $qvs ) {
	if ( ! isset( $qvs['post_format'] ) )
		return $qvs;
	$slugs = get_post_format_slugs();
	if ( isset( $slugs[ $qvs['post_format'] ] ) )
		$qvs['post_format'] = 'post-format-' . $slugs[ $qvs['post_format'] ];
	$tax = get_taxonomy( 'post_format' );
	if ( ! is_admin() )
		$qvs['post_type'] = $tax->object_type;
	return $qvs;
}
add_filter( 'request', '_post_format_request' );

/**
 * Filters the post format term link to remove the format prefix.
 *
 * @access private
 * @since 3.1.0
 */
function _post_format_link( $link, $term, $taxonomy ) {
	global $wp_rewrite;
	if ( 'post_format' != $taxonomy )
		return $link;
	if ( $wp_rewrite->get_extra_permastruct( $taxonomy ) ) {
		return str_replace( "/{$term->slug}", '/' . str_replace( 'post-format-', '', $term->slug ), $link );
	} else {
		$link = remove_query_arg( 'post_format', $link );
		return add_query_arg( 'post_format', str_replace( 'post-format-', '', $term->slug ), $link );
	}
}
add_filter( 'term_link', '_post_format_link', 10, 3 );

/**
 * Remove the post format prefix from the name property of the term object created by get_term().
 *
 * @access private
 * @since 3.1.0
 */
function _post_format_get_term( $term ) {
	if ( isset( $term->slug ) ) {
		$term->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
	}
	return $term;
}
add_filter( 'get_post_format', '_post_format_get_term' );

/**
 * Remove the post format prefix from the name property of the term objects created by get_terms().
 *
 * @access private
 * @since 3.1.0
 */
function _post_format_get_terms( $terms, $taxonomies, $args ) {
	if ( in_array( 'post_format', (array) $taxonomies ) ) {
		if ( isset( $args['fields'] ) && 'names' == $args['fields'] ) {
			foreach( $terms as $order => $name ) {
				$terms[$order] = get_post_format_string( str_replace( 'post-format-', '', $name ) );
			}
		} else {
			foreach ( (array) $terms as $order => $term ) {
				if ( isset( $term->taxonomy ) && 'post_format' == $term->taxonomy ) {
					$terms[$order]->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
				}
			}
		}
	}
	return $terms;
}
add_filter( 'get_terms', '_post_format_get_terms', 10, 3 );

/**
 * Remove the post format prefix from the name property of the term objects created by wp_get_object_terms().
 *
 * @access private
 * @since 3.1.0
 */
function _post_format_wp_get_object_terms( $terms ) {
	foreach ( (array) $terms as $order => $term ) {
		if ( isset( $term->taxonomy ) && 'post_format' == $term->taxonomy ) {
			$terms[$order]->name = get_post_format_string( str_replace( 'post-format-', '', $term->slug ) );
		}
	}
	return $terms;
}
add_filter( 'wp_get_object_terms', '_post_format_wp_get_object_terms' );

/**
 * Extract a URL from passed content, if possible
 * Checks for a URL on the first line of the content or the first encountered href attribute.
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain a URL.
 * @return string The found URL.
 */
function get_content_url( $content ) {
	if ( empty( $content ) )
		return '';

	// the content is a URL
	$trimmed = trim( $content );
	if ( 0 === stripos( $trimmed, 'http' ) && ! preg_match( '#\s#', $trimmed ) ) {
		return $trimmed;

	// the content is HTML so we grab the first href
	} elseif ( preg_match( '/<a\s[^>]*?href=([\'"])(.+?)\1/is', $content, $matches ) ) {
		return esc_url_raw( $matches[2] );
	}

	$lines = explode( "\n", $trimmed );
	$line = trim( array_shift( $lines ) );

	// the content is a URL followed by content
	if ( 0 === stripos( $line, 'http' ) )
		return esc_url_raw( $line );

	return '';
}

/**
 * Don't display post titles for asides and status posts on the front end.
 *
 * @since 3.6.0
 * @access private
 */
function _post_formats_title( $title, $post_id = 0 ) {
	if ( ! $post_id || is_admin() || is_feed() || ! in_array( get_post_format( $post_id ), array( 'aside', 'status' ) ) )
		return $title;

	// Return an empty string only if the title is auto-generated.
	$post = get_post( $post_id );
	if ( $title == _post_formats_generate_title( $post->post_content, get_post_format( $post_id ) ) )
		$title = '';

	return $title;
}

/**
 * Generate a title from the post content or format.
 *
 * @since 3.6.0
 * @access private
 */
function _post_formats_generate_title( $content, $post_format = '' ) {
	$title = wp_trim_words( strip_shortcodes( $content ), 8, '' );

	if ( empty( $title ) )
		$title = get_post_format_string( $post_format );

	return $title;
}

/**
 * Fixes empty titles for aside and status formats.
 *
 * Passes a generated post title to the 'wp_insert_post_data' filter.
 *
 * @since 3.6.0
 * @access private
 *
 * @uses _post_formats_generate_title()
 */
function _post_formats_fix_empty_title( $data, $postarr ) {
	if ( 'auto-draft' == $data['post_status'] || ! post_type_supports( $data['post_type'], 'post-formats' ) )
		return $data;

	$post_id = ( isset( $postarr['ID'] ) ) ? absint( $postarr['ID'] ) : 0;
	$post_format = '';

	if ( $post_id )
		$post_format = get_post_format( $post_id );

	if ( isset( $postarr['post_format'] ) )
		$post_format = ( in_array( $postarr['post_format'], get_post_format_slugs() ) ) ? $postarr['post_format'] : '';

	if ( ! in_array( $post_format, array( 'aside', 'status' ) ) )
		return $data;

	if ( $data['post_title'] == _post_formats_generate_title( $data['post_content'], $post_format ) )
		return $data;

	// If updating an existing post, check whether the title was auto-generated.
	if ( $post_id && $post = get_post( $post_id ) )
		if ( $post->post_title == $data['post_title'] && $post->post_title == _post_formats_generate_title( $post->post_content, get_post_format( $post->ID ) ) )
			$data['post_title'] = '';

	if ( empty( $data['post_title'] ) )
		$data['post_title'] = _post_formats_generate_title( $data['post_content'], $post_format );

	return $data;
}
