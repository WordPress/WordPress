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
 * @param int|object $post A post
 *
 * @return mixed The format if successful. False if no format is set. WP_Error if errors.
 */
function get_post_format( $post = null ) {
	$post = get_post($post);

	if ( ! post_type_supports( $post->post_type, 'post-formats' ) )
		return false;

	$_format = get_the_terms( $post->ID, 'post_format' );

	if ( empty( $_format ) )
		return false;

	$format = array_shift( $_format );

	return ( str_replace('post-format-', '', $format->slug ) );
}

/**
 * Check if a post has a particular format
 *
 * @since 3.1.0
 * @uses has_term()
 *
 * @param string $format The format to check for
 * @param object|id $post The post to check. If not supplied, defaults to the current post if used in the loop.
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
 * @param int|object $post The post for which to assign a format
 * @param string $format  A format to assign. Use an empty string or array to remove all formats from the post.
 * @return mixed WP_Error on error. Array of affected term IDs on success.
 */
function set_post_format( $post, $format ) {
	$post = get_post($post);

	if ( empty($post) )
		return new WP_Error('invalid_post', __('Invalid post'));

	if ( !empty($format) ) {
		$format = sanitize_key($format);
		if ( 'standard' == $format || !in_array( $format, array_keys( get_post_format_slugs() ) ) )
			$format = '';
		else
			$format = 'post-format-' . $format;
	}

	return wp_set_post_terms($post->ID, $format, 'post_format');
}

/**
 * Retrieve post format metadata for a post
 *
 * @since 3.6.0
 *
 * @param int $post_id
 * @return null
 */
function get_post_format_meta( $post_id = 0 ) {
	$values = array(
		'quote'        => '',
		'quote_source' => '',
		'image'        => '',
		'url'          => '',
		'gallery'      => '',
		'media'        => '',
	);

	foreach ( $values as $key => $value )
		$values[$key] = get_post_meta( $post_id, '_wp_format_' . $key, true );

	return $values;
}

/**
 * Returns an array of post format slugs to their translated and pretty display versions
 *
 * @since 3.1.0
 *
 * @return array The array of translations
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
 * @param string $slug A post format slug
 * @return string The translated post format name
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
 * @param string $format Post format
 * @return string Link
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
 * Return the class for a post format content wrapper
 *
 * @since 3.6.0
 *
 * @param string $format
 */
function get_post_format_content_class( $format ) {
	return apply_filters( 'post_format_content_class', 'post-format-content', $format );
}

/**
 * Ouput the class for a post format content wrapper
 *
 * @since 3.6.0
 *
 * @param string $format
 */
function post_format_content_class( $format ) {
	echo get_post_format_content_class( $format );
}

/**
 * Provide fallback behavior for Posts that have associated post format
 *
 * @since 3.6.0
 *
 * @param string $content
 */
function post_formats_compat( $content, $id = 0 ) {
	$post = empty( $id ) ? get_post() : get_post( $id );
	if ( empty( $post ) )
		return $content;

	$format = get_post_format( $post );
	if ( empty( $format ) || in_array( $format, array( 'status', 'aside', 'chat' ) ) )
		return $content;

	if ( current_theme_supports( 'structured-post-formats', $format ) )
		return $content;

	$defaults = array(
		'position' => 'after',
		'tag' => 'div',
		'class' => get_post_format_content_class( $format ),
		'link_class' => '',
		'image_class' => '',
	);

	$args = apply_filters( 'post_format_compat', array() );
	$compat = wp_parse_args( $args, $defaults );

	$show_content = true;
	$format_output = '';
	$meta = get_post_format_meta( $post->ID );

	switch ( $format ) {
		case 'link':
			$compat['tag'] = '';

			if ( ! empty( $meta['url'] ) ) {
				$esc_url = preg_quote( $meta['url'], '#' );
				// Make sure the same URL isn't in the post (modified/extended versions allowed)
				if ( ! preg_match( '#' . $esc_url . '[^/&\?]#', $content ) ) {
					$format_output .= sprintf(
						'<a %shref="%s">%s</a>',
						empty( $compat['link_class'] ) ? '' : sprintf( 'class="%s" ', esc_attr( $compat['link_class'] ) ),
						esc_url( $meta['url'] ),
						empty( $post->post_title ) ? esc_url( $meta['url'] ) : apply_filters( 'the_title', $post->post_title )
					);
				}
			}
			break;

		case 'quote':
			if ( ! empty( $meta['quote'] ) && ! stristr( $content, $meta['quote'] ) ) {
				$format_output .= sprintf( '<blockquote>%s</blockquote>', $meta['quote'] );
				if ( ! empty( $meta['quote_source'] ) ) {
					$format_output .= sprintf(
						'<cite>%s</cite>',
						! empty( $meta['url'] ) ?
							sprintf( '<a href="%s">%s</a>', esc_url( $meta['url'] ), $meta['quote_source'] ) :
							$meta['quote_source']
					);
				}
			}
			break;

		case 'image':
			if ( ! empty( $meta['image'] ) ) {
				$image = is_numeric( $meta['image'] ) ? wp_get_attachment_url( $meta['image'] ) : $meta['image'];

				if ( ! empty( $image ) && ! stristr( $content, $image ) ) {
					$image_html = sprintf(
						'<img %ssrc="%s" alt="" />',
						empty( $compat['image_class'] ) ? '' : sprintf( 'class="%s" ', esc_attr( $compat['image_class'] ) ),
						$image
					);
					if ( empty( $meta['url'] ) ) {
						$format_output .= $image_html;
					} else {
						$format_output .= sprintf(
							'<a href="%s">%s</a>',
							esc_url( $meta['url'] ),
							$image_html
						);
					}
				}
			}
			break;

		case 'gallery':
			preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches );
			if ( ! empty( $matches ) && isset( $matches[2] ) ) {
				foreach ( (array) $matches[2] as $match ) {
					if ( 'gallery' === $match )
						break 2; // foreach + case
				}
			}

			if ( ! empty( $meta['gallery'] ) ) {
				$format_output .= $meta['gallery'];
			}
			break;

		case 'video':
		case 'audio':
			$shortcode_regex = '/' . get_shortcode_regex() . '/s';
			$matches = preg_match( $shortcode_regex, $content );
			if ( ! $matches || $format !== $matches[2] ) {
				if ( ! empty( $meta['media'] ) ) {
					// the metadata is a shortcode or an embed code
					if ( preg_match( $shortcode_regex, $meta['media'] ) || preg_match( '#<[^>]+>#', $meta['media'] ) ) {
						$format_output .= $meta['media'];
					} elseif ( ! stristr( $content, $meta['media'] ) ) {
						// attempt to embed the URL
						$format_output .= sprintf( '[embed]%s[/embed]', $meta['media'] );
					}
				}
			}
			break;
		default:
			return $content;
			break;
	}

	if ( empty( $format_output ) )
		return $content;

	$output = '';

	if ( ! empty( $content ) && $show_content && 'before' !== $compat['position'] )
		$output .= $content . "\n\n";

	if ( ! empty( $compat['tag'] ) )
		$output .= sprintf( '<%s class="%s">', tag_escape( $compat['tag'] ), esc_attr( $compat['class'] ) );

	$output .= "\n\n" . $format_output;

	if ( ! empty( $compat['tag'] ) )
		$output .= sprintf( '</%s>', tag_escape( $compat['tag'] ) );

	if ( ! empty( $content ) && $show_content && 'before' === $compat['position'] )
		$output .= "\n\n" . $content;

	return $output;
}
