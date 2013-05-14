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
 * Retrieve post format metadata for a post
 *
 * @since 3.6.0
 *
 * @param int $post_id (optional) The post ID.
 * @return array The array of post format metadata.
 */
function get_post_format_meta( $post_id = 0 ) {
	$meta = get_post_meta( $post_id );
	$keys = array( 'quote', 'quote_source_name', 'quote_source_url', 'link_url', 'gallery', 'audio_embed', 'video_embed', 'url', 'image' );

	if ( empty( $meta ) )
		return array_fill_keys( $keys, '' );

	$upgrade = array(
		'_wp_format_quote_source' => 'quote_source_name',
		'_wp_format_audio' => 'audio_embed',
		'_wp_format_video' => 'video_embed'
	);

	$format = get_post_format( $post_id );
	if ( ! empty( $format ) ) {
		switch ( $format ) {
		case 'link':
			$upgrade['_wp_format_url'] = 'link_url';
			break;
		case 'quote':
			$upgrade['_wp_format_url'] = 'quote_source_url';
			break;
		}
	}

	$upgrade_keys = array_keys( $upgrade );
	foreach ( $meta as $key => $values ) {
		if ( ! in_array( $key, $upgrade_keys ) )
			continue;
		update_post_meta( $post_id, '_format_' . $upgrade[$key], reset( $values ) );
		delete_post_meta( $post_id, $key );
	}

	$values = array();

	foreach ( $keys as $key ) {
		$value = get_post_meta( $post_id, '_format_' . $key, true );
		$values[$key] = empty( $value ) ? '' : $value;
	}

	return apply_filters( 'post_format_meta', $values );
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
 * Return the class for a post format content wrapper
 *
 * @since 3.6.0
 *
 * @param string $format The post format slug, such as status, quote, image, gallery, etc.
 * @return string Filtered post format content class.
 */
function get_post_format_content_class( $format ) {
	return apply_filters( 'post_format_content_class', 'post-format-content', $format );
}

/**
 * Output the class for a post format content wrapper
 *
 * @since 3.6.0
 *
 * @param string $format The post format slug, such as status, quote, image, gallery, etc.
 */
function post_format_content_class( $format ) {
	echo get_post_format_content_class( $format );
}

/**
 * Provide fallback behavior for Posts that have associated post format
 *
 * @since 3.6.0
 *
 * @uses get_post_format_meta()
 *
 * @param string $content The post content.
 * @param int $post_id (optional) The post ID.
 * @return string Formatted output based on associated post format.
 */
function post_formats_compat( $content, $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) )
		return $content;

	$format = get_post_format( $post );
	if ( empty( $format ) || in_array( $format, array( 'status', 'aside', 'chat', 'gallery' ) ) )
		return $content;

	if ( current_theme_supports( 'structured-post-formats', $format ) )
		return $content;

	$defaults = array(
		'position' => 'after',
		'tag' => 'div',
		'class' => get_post_format_content_class( $format ),
		'link_class' => '',
	);

	$args = apply_filters( 'post_format_compat', array() );
	$compat = wp_parse_args( $args, $defaults );

	$show_content = true;
	$format_output = '';
	$meta = get_post_format_meta( $post->ID );

	switch ( $format ) {
		case 'link':
			if ( ! empty( $meta['link_url'] ) ) {
				$esc_url = preg_quote( $meta['link_url'], '#' );
				// Make sure the same URL isn't in the post (modified/extended versions allowed)
				if ( ! preg_match( '#' . $esc_url . '[^/&\?]?#', $content ) ) {
					$url = $meta['link_url'];
				} else {
					$url = get_content_url( $content, true );
				}
			} else {
				$content_before = $content;
				$url = get_content_url( $content, true );
				if ( $content_before == $content )
					$url = '';
			}

			if ( ! empty( $url ) ) {
				$format_output .= sprintf(
					'<a %shref="%s">%s</a>',
					empty( $compat['link_class'] ) ? '' : sprintf( 'class="%s" ', esc_attr( $compat['link_class'] ) ),
					esc_url( $url ),
					empty( $post->post_title ) ? esc_url( $meta['link_url'] ) : apply_filters( 'the_title', $post->post_title, $post->ID )
				);
 			}
			break;

		case 'image':
			if ( ! empty( $meta['image'] ) ) {

				if ( has_shortcode( $meta['image'], 'caption' ) ) {
					// wrap <img> in <a>
					if ( ! empty( $meta['url'] ) && false === strpos( $meta['image'], '<a ' ) ) {
						$meta['image'] = preg_replace(
							'#(<img[^>]+>)#',
							sprintf( '<a href="%s">$1</a>', esc_url( $meta['url'] ) ),
							$meta['image']
						);
					}
					$format_output .= do_shortcode( $meta['image'] );
				} else {

					if ( is_numeric( $meta['image'] ) ) {
						$image = wp_get_attachment_image( absint( $meta['image'] ), 'full' );
					} elseif ( ! preg_match( '#<[^>]+>#', $meta['image'] ) ) {
						// not HTML, assume URL
						$image = sprintf(
							'<img %ssrc="%s" alt="" />',
							empty( $compat['image_class'] ) ? '' : sprintf( 'class="%s" ', esc_attr( $compat['image_class'] ) ),
							esc_url( $meta['image'] )
						);
					} else {
						// assume HTML
						$image = $meta['image'];
					}

					if ( ! empty( $meta['url'] ) && false === strpos( $image, '<a ' ) ) {
						$format_output .= sprintf(
							'<a href="%s">%s</a>',
							esc_url( $meta['url'] ),
							$image
						);
					} else {
						$format_output .= $image;
					}
				}
			}
			break;

		case 'quote':
			$quote = get_the_post_format_quote( $post );

			// Replace the existing quote in-place.
			if ( ! empty( $quote ) )
				get_content_quote( $content, true, $quote );
			break;

		case 'video':
		case 'audio':
			if ( ! has_shortcode( $post->post_content, $format ) && ! empty( $meta[$format . '_embed'] ) ) {
				$value = $meta[$format . '_embed'];
				// the metadata is an attachment ID
				if ( is_numeric( $value ) ) {
					$url = wp_get_attachment_url( $value );
					$format_output .= sprintf( '[%s src="%s"]', $format, $url );
				// the metadata is a shortcode or an embed code
				} elseif ( preg_match( '/' . get_shortcode_regex() . '/s', $value ) || preg_match( '#<[^>]+>#', $value ) ) {
					$format_output .= $value;
				} elseif ( ! stristr( $content, $value ) ) {
					// attempt to embed the URL
					$format_output .= sprintf( '[embed]%s[/embed]', $value );
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

/**
 * Add chat detection support to the `get_content_chat()` chat parser.
 *
 * @since 3.6.0
 *
 * @global array $_wp_chat_parsers
 *
 * @param string $name Unique identifier for chat format. Example: IRC
 * @param string $newline_regex RegEx to match the start of a new line, typically when a new "username:" appears
 *	The parser will handle up to 3 matched expressions
 *	$matches[0] = the string before the user's message starts
 *	$matches[1] = the time of the message, if present
 *	$matches[2] = the author/username
 *	OR
 *	$matches[0] = the string before the user's message starts
 *	$matches[1] = the author/username
 * @param string $delimiter_regex RegEx to determine where to split the username syntax from the chat message
 */
function add_chat_detection_format( $name, $newline_regex, $delimiter_regex ) {
	global $_wp_chat_parsers;

	if ( empty( $_wp_chat_parsers ) )
		$_wp_chat_parsers = array();

	$_wp_chat_parsers = array( $name => array( $newline_regex, $delimiter_regex ) ) + $_wp_chat_parsers;
}
add_chat_detection_format( 'IM', '#^([^:]+):#', '#[:]#' );
add_chat_detection_format( 'Skype', '#(\[.+?\])\s([^:]+):#', '#[:]#' );

/**
 * Deliberately interpret passed content as a chat transcript that is optionally
 * followed by commentary
 *
 * If the content does not contain username syntax, assume that it does not contain
 * chat logs and return
 *
 * Example:
 *
 * One stanza of chat:
 * Scott: Hey, let's chat!
 * Helen: No.
 *
 * $stanzas = array(
 *     array(
 *         array(
 *             'time' => '',
 *             'author' => 'Scott',
 *             'messsage' => "Hey, let's chat!"
 *         ),
 *         array(
 *             'time' => '',
 *             'author' => 'Helen',
 *             'message' => 'No.'
 *         )
 *     )
 * )
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain chat data, passed by reference.
 * @param boolean $remove Whether to remove the found data from the passed content.
 * @return array A chat log as structured data
 */
function get_content_chat( &$content, $remove = false ) {
	global $_wp_chat_parsers;

	$trimmed = strip_tags( trim( $content ) );
	if ( empty( $trimmed ) )
		return array();

	$matched_parser = false;
	foreach ( $_wp_chat_parsers as $parser ) {
		@list( $newline_regex, $delimiter_regex ) = $parser;
		if ( preg_match( $newline_regex, $trimmed ) ) {
			$matched_parser = $parser;
			break;
		}
	}

	if ( false === $matched_parser )
		return array();

	$last_index = 0;
	$stanzas = $data = $stanza = array();
	$author = $time = '';
	$lines = explode( "\n", make_clickable( $trimmed ) );
	$found = false;
	$found_index = 0;

	foreach ( $lines as $index => $line ) {
		if ( ! $found )
			$found_index = $index;

		$line = trim( $line );

		if ( empty( $line ) && $found ) {
			if ( ! empty( $author ) ) {
				$stanza[] = array(
					'time'    => $time,
					'author'  => $author,
					'message' => join( ' ', $data )
				);
			}

			$stanzas[] = $stanza;

			$stanza = $data = array();
			$author = $time = '';
			if ( ! empty( $lines[$index + 1] ) && ! preg_match( $delimiter_regex, $lines[$index + 1] ) )
				break;
			else
				continue;
		}

		$matched = preg_match( $newline_regex, $line, $matches );
		if ( ! $matched )
			continue;

		$found = true;
		$last_index = $index;
		$author_match = empty( $matches[2] ) ? $matches[1] : $matches[2];
		// assume username syntax if no whitespace is present
		$no_ws = $matched && ! preg_match( '#[\r\n\t ]#', $author_match );
		// allow script-like stanzas
		$has_ws = $matched && preg_match( '#[\r\n\t ]#', $author_match ) && empty( $lines[$index + 1] ) && empty( $lines[$index - 1] );
		if ( $matched && ( ! empty( $matches[2] ) || ( $no_ws || $has_ws ) ) ) {
			if ( ! empty( $author ) ) {
				$stanza[] = array(
					'time'    => $time,
					'author'  => $author,
					'message' => join( ' ', $data )
				);
				$data = array();
			}

			$time = empty( $matches[2] ) ? '' : $matches[1];
			$author = $author_match;
			$data[] = trim( str_replace( $matches[0], '', $line ) );
		} elseif ( preg_match( '#\S#', $line ) ) {
			$data[] = $line;
		}
	}

	if ( ! empty( $author ) ) {
		$stanza[] = array(
			'time'    => $time,
			'author'  => $author,
			'message' => trim( join( ' ', $data ) )
		);
	}

	if ( ! empty( $stanza ) )
		$stanzas[] = $stanza;

	if ( $remove ) {
		if ( 0 === $found_index ) {
			$removed = array_slice( $lines, $last_index );
		} else {
			$before = array_slice( $lines, 0, $found_index );
			$after = array_slice( $lines, $last_index + 1 );
			$removed = array_filter( array_merge( $before, $after ) );
		}
		$content = trim( join( "\n", $removed ) );
	}

	return $stanzas;
}

/**
 * Retrieve structured chat data from the current or passed post
 *
 * @since 3.6.0
 *
 * @param int $post_id (optional) The post ID.
 * @return array The chat content.
 */
function get_the_post_format_chat( $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) )
		return array();

	$data = get_content_chat( get_paged_content( $post->post_content ) );
	if ( empty( $data ) )
		return array();

	return $data;
}

/**
 * Output HTML for a given chat's structured data. Themes can use this as a
 * template tag in place of the_content() for Chat post format templates.
 *
 * @since 3.6.0
 *
 * @uses get_the_post_format_chat()
 *
 * @print HTML
 */
function the_post_format_chat() {
	$output  = '<dl class="chat">';
	$stanzas = get_the_post_format_chat();

	foreach ( $stanzas as $stanza ) {
		foreach ( $stanza as $row ) {
			$time = '';
			if ( ! empty( $row['time'] ) )
				$time = sprintf( '<time class="chat-timestamp">%s</time>', esc_html( $row['time'] ) );

			$output .= sprintf(
				'<dt class="chat-author chat-author-%1$s vcard">%2$s <cite class="fn">%3$s</cite>: </dt>
					<dd class="chat-text">%4$s</dd>
				',
				esc_attr( sanitize_title_with_dashes( $row['author'] ) ), // Slug.
				$time,
				esc_html( $row['author'] ),
				$row['message']
			);
		}
	}

	$output .= '</dl><!-- .chat -->';

	echo $output;
}

/**
 * Get the first <blockquote> from the $content string passed by reference.
 *
 * If $content does not have a blockquote, assume the whole string
 * is the quote.
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain chat data, passed by reference.
 * @param bool $remove (optional) Whether to remove the quote from the content.
 * @param string $replace (optional) Content to replace the quote content with.
 * @return string The quote content.
 */
function get_content_quote( &$content, $remove = false, $replace = '' ) {
	if ( empty( $content ) )
		return '';

	if ( ! preg_match( '/<blockquote[^>]*>(.+?)<\/blockquote>/is', $content, $matches ) ) {
		$quote = $content;
		if ( $remove || ! empty( $replace ) )
			$content = $replace;
		return $quote;
	}

	if ( $remove || ! empty( $replace ) )
		$content = preg_replace( '/<blockquote[^>]*>(.+?)<\/blockquote>/is', addcslashes( $replace, '\\$' ), $content, 1 );

	return $matches[1];
}

/**
 * Get a quote from the post content and set split_content for future use.
 *
 * @since 3.6.0
 *
 * @uses get_content_quote()
 * @uses apply_filters() Calls 'quote_source_format' filter to allow changing the typographical mark added to the quote source (em-dash prefix, by default)
 *
 * @param object $post (optional) A reference to the post object, falls back to get_post().
 * @return string The quote html.
 */
function get_the_post_format_quote( &$post = null ) {
	if ( empty( $post ) )
		$post = get_post();

	if ( empty( $post ) )
		return '';

	$content = $post->post_content;
	$quote = get_content_quote( $content, true );
	$post->split_content = $content;

	if ( ! empty( $quote ) )
		$quote = sprintf( '<blockquote>%s</blockquote>', wpautop( $quote ) );

	$meta = get_post_format_meta( $post->ID );

	if ( ! empty( $meta['quote_source_name'] ) ) {
		$source = ( empty( $meta['quote_source_url'] ) ) ? $meta['quote_source_name'] : sprintf( '<a href="%s">%s</a>', esc_url( $meta['quote_source_url'] ), $meta['quote_source_name'] );
		$source = sprintf( apply_filters( 'quote_source_format', __( '&#8212;&#160;%s' ) ), $source );
		$quote .= sprintf( '<figcaption class="quote-caption">%s</figcaption>', $source );
	}

	if ( ! empty( $quote ) )
		$quote = sprintf( '<figure class="quote">%s</figure>', $quote );

	return $quote;
}

/**
 * Outputs the post format quote.
 *
 * @since 3.6.0
 */
function the_post_format_quote() {
	echo get_the_post_format_quote();
}

/**
 * Extract a URL from passed content, if possible
 * Checks for a URL on the first line of the content or the first encountered href attribute.
 *
 * @since 3.6.0
 *
 * @param string $content A string which might contain a URL, passed by reference.
 * @param boolean $remove Whether to remove the found URL from the passed content.
 * @return string The found URL.
 */
function get_content_url( &$content, $remove = false ) {
	if ( empty( $content ) )
		return '';

	// the content is a URL
	$trimmed = trim( $content );
	if ( 0 === stripos( $trimmed, 'http' ) && ! preg_match( '#\s#', $trimmed ) ) {
		if ( $remove )
			$content = '';

		return $trimmed;
	// the content is HTML so we grab the first href
	} elseif ( preg_match( '/<a\s[^>]*?href=([\'"])(.+?)\1/is', $content, $matches ) ) {
		return esc_url_raw( $matches[2] );
	}

	$lines = explode( "\n", $trimmed );
	$line = trim( array_shift( $lines ) );

	// the content is a URL followed by content
	if ( 0 === stripos( $line, 'http' ) ) {
		if ( $remove )
			$content = trim( join( "\n", $lines ) );

		return esc_url_raw( $line );
	}

	return '';
}

/**
 * Attempt to retrieve a URL from a post's content
 *
 * @since 3.6.0
 *
 * @param int $post_id (optional) The post ID.
 * @return string A URL, if found.
 */
function get_the_post_format_url( $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) )
		return '';

	$format = get_post_format( $post->ID );
	if ( in_array( $format, array( 'image', 'link', 'quote' ) ) ) {
		$meta = get_post_format_meta( $post->ID );
		$meta_link = '';

		switch ( $format ) {
		case 'link':
			if ( ! empty( $meta['link_url'] ) )
				$meta_link = $meta['link_url'];
			break;
		case 'image':
			if ( ! empty( $meta['url'] ) )
				$meta_link = $meta['url'];
			break;
		case 'quote':
			if ( ! empty( $meta['quote_source_url'] ) )
				$meta_link = $meta['quote_source_url'];
			break;
		}

		if ( ! empty( $meta_link ) )
			return apply_filters( 'get_the_post_format_url', esc_url_raw( $meta_link ), $post );
	}

	if ( ! empty( $post->post_content ) )
		return apply_filters( 'get_the_post_format_url', get_content_url( $post->post_content ), $post );
}

/**
 * Attempt to output a URL from a post's content
 *
 * @since 3.6.0
 *.
 */
function the_post_format_url() {
	echo esc_url( get_the_post_format_url() );
}

/**
 * Retrieve the post content, minus the extracted post format content
 *
 * @since 3.6.0
 *
 * @internal there is a lot of code that could be abstracted from get_the_content()
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $strip_teaser Optional. Strip teaser content before the more text. Default is false.
 * @return string The content minus the extracted post format content.
 */
function get_the_remaining_content( $more_link_text = null, $strip_teaser = false ) {
	global $more, $page, $format_pages, $multipage, $preview;

	$post = get_post();

	if ( null === $more_link_text )
		$more_link_text = __( '(more&hellip;)' );

	$output = '';
	$has_teaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required() )
		return get_the_password_form();

	if ( $page > count( $format_pages ) ) // if the requested page doesn't exist
		$page = count( $format_pages ); // give them the highest numbered page that DOES exist

	$content = $format_pages[$page-1];
	if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
		$content = explode( $matches[0], $content, 2 );
		if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
			$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );

		$has_teaser = true;
	} else {
		$content = array( $content );
	}

	if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) )
		$strip_teaser = true;

	$teaser = $content[0];

	if ( $more && $strip_teaser && $has_teaser )
		$teaser = '';

	$output .= $teaser;

	if ( count( $content ) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty( $more_link_text ) )
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );

			$output = force_balance_tags( $output );
		}
	}

	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output = preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );

	return $output;
}

/**
 * Display the post content minus the parsed post format data.
 *
 * @since 3.6.0
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $strip_teaser Optional. Strip teaser content before the more text. Default is false.
 */
function the_remaining_content( $more_link_text = null, $strip_teaser = false ) {
	$extra = get_the_remaining_content( $more_link_text, $strip_teaser );

	remove_filter( 'the_content', 'post_formats_compat', 7 );
	$content = apply_filters( 'the_content', $extra );
	add_filter( 'the_content', 'post_formats_compat', 7 );

	echo str_replace( ']]>', ']]&gt;', $content );
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
