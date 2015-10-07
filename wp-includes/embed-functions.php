<?php
/**
 * WordPress API for embedding content.
 *
 * @package WordPress
 * @subpackage oEmbed
 * @since 4.4.0
 */

/**
 * Registers an embed handler.
 *
 * Should probably only be used for sites that do not support oEmbed.
 *
 * @since 2.9.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param string   $id       An internal ID/name for the handler. Needs to be unique.
 * @param string   $regex    The regex that will be used to see if this handler should be used for a URL.
 * @param callable $callback The callback function that will be called if the regex is matched.
 * @param int      $priority Optional. Used to specify the order in which the registered handlers will
 *                           be tested. Default 10.
 */
function wp_embed_register_handler( $id, $regex, $callback, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->register_handler( $id, $regex, $callback, $priority );
}

/**
 * Unregisters a previously-registered embed handler.
 *
 * @since 2.9.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param string $id       The handler ID that should be removed.
 * @param int    $priority Optional. The priority of the handler to be removed. Default 10.
 */
function wp_embed_unregister_handler( $id, $priority = 10 ) {
	global $wp_embed;
	$wp_embed->unregister_handler( $id, $priority );
}

/**
 * Create default array of embed parameters.
 *
 * The width defaults to the content width as specified by the theme. If the
 * theme does not specify a content width, then 500px is used.
 *
 * The default height is 1.5 times the width, or 1000px, whichever is smaller.
 *
 * The 'embed_defaults' filter can be used to adjust either of these values.
 *
 * @since 2.9.0
 *
 * @global int $content_width
 *
 * @param string $url Optional. The URL that should be embedded. Default empty.
 *
 * @return array Default embed parameters.
 */
function wp_embed_defaults( $url = '' ) {
	if ( ! empty( $GLOBALS['content_width'] ) )
		$width = (int) $GLOBALS['content_width'];

	if ( empty( $width ) )
		$width = 500;

	$height = min( ceil( $width * 1.5 ), 1000 );

	/**
	 * Filter the default array of embed dimensions.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $width  Width of the embed in pixels.
	 * @param int    $height Height of the embed in pixels.
	 * @param string $url    The URL that should be embedded.
	 */
	return apply_filters( 'embed_defaults', compact( 'width', 'height' ), $url );
}

/**
 * Attempts to fetch the embed HTML for a provided URL using oEmbed.
 *
 * @since 2.9.0
 *
 * @see WP_oEmbed
 *
 * @param string $url  The URL that should be embedded.
 * @param array  $args Optional. Additional arguments and parameters for retrieving embed HTML.
 *                     Default empty.
 * @return false|string False on failure or the embed HTML on success.
 */
function wp_oembed_get( $url, $args = '' ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$oembed = _wp_oembed_get_object();
	return $oembed->get_html( $url, $args );
}

/**
 * Adds a URL format and oEmbed provider URL pair.
 *
 * @since 2.9.0
 *
 * @see WP_oEmbed
 *
 * @param string  $format   The format of URL that this provider can handle. You can use asterisks
 *                          as wildcards.
 * @param string  $provider The URL to the oEmbed provider.
 * @param boolean $regex    Optional. Whether the `$format` parameter is in a RegEx format. Default false.
 */
function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );

	if ( did_action( 'plugins_loaded' ) ) {
		$oembed = _wp_oembed_get_object();
		$oembed->providers[$format] = array( $provider, $regex );
	} else {
		WP_oEmbed::_add_provider_early( $format, $provider, $regex );
	}
}

/**
 * Removes an oEmbed provider.
 *
 * @since 3.5.0
 *
 * @see WP_oEmbed
 *
 * @param string $format The URL format for the oEmbed provider to remove.
 * @return bool Was the provider removed successfully?
 */
function wp_oembed_remove_provider( $format ) {
	require_once( ABSPATH . WPINC . '/class-oembed.php' );

	if ( did_action( 'plugins_loaded' ) ) {
		$oembed = _wp_oembed_get_object();

		if ( isset( $oembed->providers[ $format ] ) ) {
			unset( $oembed->providers[ $format ] );
			return true;
		}
	} else {
		WP_oEmbed::_remove_provider_early( $format );
	}

	return false;
}

/**
 * Determines if default embed handlers should be loaded.
 *
 * Checks to make sure that the embeds library hasn't already been loaded. If
 * it hasn't, then it will load the embeds library.
 *
 * @since 2.9.0
 *
 * @see wp_embed_register_handler()
 */
function wp_maybe_load_embeds() {
	/**
	 * Filter whether to load the default embed handlers.
	 *
	 * Returning a falsey value will prevent loading the default embed handlers.
	 *
	 * @since 2.9.0
	 *
	 * @param bool $maybe_load_embeds Whether to load the embeds library. Default true.
	 */
	if ( ! apply_filters( 'load_default_embeds', true ) ) {
		return;
	}

	wp_embed_register_handler( 'youtube_embed_url', '#https?://(www.)?youtube\.com/(?:v|embed)/([^/]+)#i', 'wp_embed_handler_youtube' );

	wp_embed_register_handler( 'googlevideo', '#http://video\.google\.([A-Za-z.]{2,5})/videoplay\?docid=([\d-]+)(.*?)#i', 'wp_embed_handler_googlevideo' );

	/**
	 * Filter the audio embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Audio embed handler callback function.
	 */
	wp_embed_register_handler( 'audio', '#^https?://.+?\.(' . join( '|', wp_get_audio_extensions() ) . ')$#i', apply_filters( 'wp_audio_embed_handler', 'wp_embed_handler_audio' ), 9999 );

	/**
	 * Filter the video embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Video embed handler callback function.
	 */
	wp_embed_register_handler( 'video', '#^https?://.+?\.(' . join( '|', wp_get_video_extensions() ) . ')$#i', apply_filters( 'wp_video_embed_handler', 'wp_embed_handler_video' ), 9999 );
}

/**
 * The Google Video embed handler callback.
 *
 * Google Video does not support oEmbed.
 *
 * @see WP_Embed::register_handler()
 * @see WP_Embed::shortcode()
 *
 * @param array  $matches The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array  $attr    Embed attributes.
 * @param string $url     The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_googlevideo( $matches, $attr, $url, $rawattr ) {
	// If the user supplied a fixed width AND height, use it
	if ( !empty($rawattr['width']) && !empty($rawattr['height']) ) {
		$width  = (int) $rawattr['width'];
		$height = (int) $rawattr['height'];
	} else {
		list( $width, $height ) = wp_expand_dimensions( 425, 344, $attr['width'], $attr['height'] );
	}

	/**
	 * Filter the Google Video embed output.
	 *
	 * @since 2.9.0
	 *
	 * @param string $html    Google Video HTML embed markup.
	 * @param array  $matches The RegEx matches from the provided regex.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'embed_googlevideo', '<embed type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docid=' . esc_attr($matches[2]) . '&amp;hl=en&amp;fs=true" style="width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px" allowFullScreen="true" allowScriptAccess="always" />', $matches, $attr, $url, $rawattr );
}

/**
 * YouTube iframe embed handler callback.
 *
 * Catches YouTube iframe embed URLs that are not parsable by oEmbed but can be translated into a URL that is.
 *
 * @since 4.0.0
 *
 * @global WP_Embed $wp_embed
 *
 * @param array  $matches The RegEx matches from the provided regex when calling
 *                        wp_embed_register_handler().
 * @param array  $attr    Embed attributes.
 * @param string $url     The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_youtube( $matches, $attr, $url, $rawattr ) {
	global $wp_embed;
	$embed = $wp_embed->autoembed( "https://youtube.com/watch?v={$matches[2]}" );

	/**
	 * Filter the YoutTube embed output.
	 *
	 * @since 4.0.0
	 *
	 * @see wp_embed_handler_youtube()
	 *
	 * @param string $embed   YouTube embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_youtube', $embed, $attr, $url, $rawattr );
}

/**
 * Audio embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array  $matches The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array  $attr Embed attributes.
 * @param string $url The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_audio( $matches, $attr, $url, $rawattr ) {
	$audio = sprintf( '[audio src="%s" /]', esc_url( $url ) );

	/**
	 * Filter the audio embed output.
	 *
	 * @since 3.6.0
	 *
	 * @param string $audio   Audio embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_audio', $audio, $attr, $url, $rawattr );
}

/**
 * Video embed handler callback.
 *
 * @since 3.6.0
 *
 * @param array  $matches The RegEx matches from the provided regex when calling wp_embed_register_handler().
 * @param array  $attr    Embed attributes.
 * @param string $url     The original URL that was matched by the regex.
 * @param array  $rawattr The original unmodified attributes.
 * @return string The embed HTML.
 */
function wp_embed_handler_video( $matches, $attr, $url, $rawattr ) {
	$dimensions = '';
	if ( ! empty( $rawattr['width'] ) && ! empty( $rawattr['height'] ) ) {
		$dimensions .= sprintf( 'width="%d" ', (int) $rawattr['width'] );
		$dimensions .= sprintf( 'height="%d" ', (int) $rawattr['height'] );
	}
	$video = sprintf( '[video %s src="%s" /]', $dimensions, esc_url( $url ) );

	/**
	 * Filter the video embed output.
	 *
	 * @since 3.6.0
	 *
	 * @param string $video   Video embed output.
	 * @param array  $attr    An array of embed attributes.
	 * @param string $url     The original URL that was matched by the regex.
	 * @param array  $rawattr The original unmodified attributes.
	 */
	return apply_filters( 'wp_embed_handler_video', $video, $attr, $url, $rawattr );
}

/**
 * Parse an oEmbed API query.
 *
 * @since 4.4.0
 */
function wp_oembed_parse_query( $wp_query ) {
	$controller = new WP_oEmbed_Controller;
	$controller->parse_query( $wp_query );
}

/**
 * Adds oEmbed discovery links in the website <head>.
 *
 * @since 4.4.0
 */
function wp_oembed_add_discovery_links() {
	$output = '';

	if ( is_singular() ) {
		$output .= '<link rel="alternate" type="application/json+oembed" href="' . esc_url( get_oembed_endpoint_url( get_permalink() ) ) . '" />' . "\n";
		$output .= '<link rel="alternate" type="text/xml+oembed" href="' . esc_url( get_oembed_endpoint_url( get_permalink(), 'xml' ) ) . '" />' . "\n";
	}

	/**
	 * Filter the oEmbed discovery links.
	 *
	 * @since 4.4.0
	 *
	 * @param string $output HTML of the discovery links.
	 */
	echo apply_filters( 'oembed_discovery_links', $output );
}

/**
 * Add the necessary JavaScript to communicate with the embedded iframes.
 *
 * @since 4.4.0
 */
function wp_oembed_add_host_js() {
	wp_enqueue_script( 'wp-oembed' );
}


/**
 * Get the URL to embed a specific post in an iframe.
 *
 * @since 4.4.0
 *
 * @param int|WP_Post $post Optional. Post ID or object. Defaults to the current post.
 * @return string|false The post embed URL on success, false if the post doesn't exist.
 */
function get_post_embed_url( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	if ( get_option( 'permalink_structure' ) ) {
		$embed_url = trailingslashit( get_permalink( $post ) ) . user_trailingslashit( 'embed' );
	} else {
		$embed_url = add_query_arg( array( 'embed' => 'true' ), get_permalink( $post ) );
	}

	/**
	 * Filter the URL to embed a specific post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $embed_url The post embed URL.
	 * @param WP_Post $post      The corresponding post object.
	 */
	return esc_url_raw( apply_filters( 'post_embed_url', $embed_url, $post ) );
}

/**
 * Get the oEmbed endpoint URL for a given permalink.
 *
 * Pass an empty string as the first argument
 * to get the endpoint base URL.
 *
 * @since 4.4.0
 *
 * @param string $permalink Optional. The permalink used for the `url` query arg. Default empty.
 * @param string $format    Optional. The requested response format. Default 'json'.
 * @return string The oEmbed endpoint URL.
 */
function get_oembed_endpoint_url( $permalink = '', $format = 'json' ) {
	$url = add_query_arg( array( 'oembed' => 'true' ), home_url( '/' ) );

	if ( 'json' === $format ) {
		$format = false;
	}

	if ( '' !== $permalink ) {
		$url = add_query_arg( array(
			'url'    => $permalink,
			'format' => $format,
		), $url );
	}

	/**
	 * Filter the oEmbed endpoint URL.
	 *
	 * @since 4.4.0
	 *
	 * @param string $url       The URL to the oEmbed endpoint.
	 * @param string $permalink The permalink used for the `url` query arg.
	 * @param string $format    The requested response format.
	 */
	return apply_filters( 'oembed_endpoint_url', $url, $permalink, $format );
}

/**
 * Get the embed code for a specific post.
 *
 * @since 4.4.0
 *
 * @param int|WP_Post $post   Optional. Post ID or object. Default is global `$post`.
 * @param int         $width  The width for the response.
 * @param int         $height The height for the response.
 * @return string|false Embed code on success, false if post doesn't exist.
 */
function get_post_embed_html( $post = null, $width, $height ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$embed_url = get_post_embed_url( $post );

	$output = "<script type='text/javascript'>\n";
	if ( SCRIPT_DEBUG ) {
		$output .= file_get_contents( ABSPATH . WPINC . '/js/wp-oembed.js' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `grunt build` process to directly
		 * include a minified version of wp-oembed.js, instead of using the
		 * file_get_contents() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-oembed.js directly.
		 */
		$output .=<<<JS
		include "js/wp-oembed.min.js"
JS;
	}
	$output .= "\n</script>";

	$output .= sprintf(
		'<iframe sandbox="allow-scripts" security="restricted" src="%1$s" width="%2$d" height="%3$d" title="%4$s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>',
		esc_url( $embed_url ),
		absint( $width ),
		absint( $height ),
		esc_attr__( 'Embedded WordPress Post', 'oembed-api' )
	);

	/**
	 * Filters the oEmbed HTML output.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $output The default HTML.
	 * @param WP_Post $post   Current post object.
	 * @param int     $width  Width of the response.
	 * @param int     $height Height of the response.
	 */
	return apply_filters( 'oembed_html', $output, $post, $width, $height );
}

/**
 * Get the oEmbed response data for a given post.
 *
 * @since 4.4.0
 *
 * @param WP_Post|int $post  Optional. Post object or ID. Default is global `$post`.
 * @param int         $width The requested width.
 * @return array|false Response data on success, false if post doesn't exist.
 */
function get_oembed_response_data( $post = null, $width ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	if ( 'publish' !== get_post_status( $post ) ) {
		return false;
	}

	/**
	 * Filter the allowed minimum width for the oEmbed response.
	 *
	 * @param int $width The minimum width. Defaults to 200.
	 */
	$minwidth = apply_filters( 'oembed_minwidth', 200 );

	/**
	 * Filter the allowed maximum width for the oEmbed response.
	 *
	 * @param int $width The maximum width. Defaults to 600.
	 */
	$maxwidth = apply_filters( 'oembed_maxwidth', 600 );

	if ( $width < $minwidth ) {
		$width = $minwidth;
	} else if ( $width > $maxwidth ) {
		$width = $maxwidth;
	}

	$height = ceil( $width / 16 * 9 );

	if ( 200 > $height ) {
		$height = 200;
	}

	$data = array(
		'version'       => '1.0',
		'provider_name' => get_bloginfo( 'name' ),
		'provider_url'  => get_home_url(),
		'author_name'   => get_bloginfo( 'name' ),
		'author_url'    => get_home_url(),
		'title'         => $post->post_title,
		'type'          => 'link',
	);

	$author = get_userdata( $post->post_author );

	if ( $author ) {
		$data['author_name'] = $author->display_name;
		$data['author_url']  = get_author_posts_url( $author->ID );
	}

	/**
	 * Filter the oEmbed response data.
	 *
	 * @since 4.4.0
	 *
	 * @param array   $data   The response data.
	 * @param WP_Post $post   The post object.
	 * @param int     $width  The requested width.
	 * @param int     $height The calculated height.
	 */
	return apply_filters( 'oembed_response_data', $data, $post, $width, $height );
}

/**
 * Filters the oEmbed response data to return an iframe embed code.
 *
 * @since 4.4.0
 *
 * @param array   $data   The response data.
 * @param WP_Post $post   The post object.
 * @param int     $width  The requested width.
 * @param int     $height The calculated height.
 * @return array The modified response data.
 */
function get_oembed_response_data_rich( $data, $post, $width, $height ) {
	$data['width']  = absint( $width );
	$data['height'] = absint( $height );
	$data['type']   = 'rich';
	$data['html']   = get_post_embed_html( $post, $width, $height );

	// Add post thumbnail to response if available.
	$thumbnail_id = false;

	if ( has_post_thumbnail( $post->ID ) ) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
	}

	if ( 'attachment' === get_post_type( $post ) ) {
		if ( wp_attachment_is_image( $post ) ) {
			$thumbnail_id = $post->ID;
		} else if ( wp_attachment_is( 'video', $post ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post );
			$data['type'] = 'video';
		}
	}

	if ( $thumbnail_id ) {
		list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $thumbnail_id, array( $width, 99999 ) );
		$data['thumbnail_url']    = $thumbnail_url;
		$data['thumbnail_width']  = $thumbnail_width;
		$data['thumbnail_height'] = $thumbnail_height;
	}

	return $data;
}

/**
 * Ensures that the specified format is either 'json' or 'xml'.
 *
 * @since 4.4.0
 *
 * @param string $format The oEmbed response format. Accepts 'json', 'xml'.
 * @return string The format, either 'xml' or 'json'. Default 'json'.
 */
function wp_oembed_ensure_format( $format ) {
	if ( ! in_array( $format, array( 'json', 'xml' ), true ) ) {
		return 'json';
	}

	return $format;
}

/**
 * Creates an XML string from a given array.
 *
 * @since 4.4.0
 * @access private
 *
 * @param array            $data The original oEmbed response data.
 * @param SimpleXMLElement $node Optional. XML node to append the result to recursively.
 * @return string|false XML string on success, false on error.
 */
function _oembed_create_xml( $data, $node = null ) {
	if ( ! is_array( $data ) || empty( $data ) ) {
		return false;
	}

	if ( null === $node ) {
		$node = new SimpleXMLElement( '<oembed></oembed>' );
	}

	foreach ( $data as $key => $value ) {
		if ( is_numeric( $key ) ) {
			$key = 'oembed';
		}

		if ( is_array( $value ) ) {
			$item = $node->addChild( $key );
			_oembed_create_xml( $value, $item );
		} else {
			$node->addChild( $key, esc_html( $value ) );
		}
	}

	return $node->asXML();
}

/**
 * Filters the returned oEmbed HTML.
 *
 * If the $url isn't on the trusted providers list,
 * we need to filter the HTML heavily for security.
 *
 * Only filters 'rich' and 'html' response types.
 *
 * @since 4.4.0
 *
 * @param string $return The returned oEmbed HTML.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered and sanitized oEmbed result.
 */
function wp_filter_oembed_result( $return, $data, $url ) {
	if ( false === $return || ! in_array( $data->type, array( 'rich', 'video' ) ) ) {
		return $return;
	}

	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$wp_oembed = _wp_oembed_get_object();

	// Don't modify the HTML for trusted providers.
	if ( false !== $wp_oembed->get_provider( $url, array( 'discover' => false ) ) ) {
		return $return;
	}

	$allowed_html = array(
		'iframe' => array(
			'src'          => true,
			'width'        => true,
			'height'       => true,
			'frameborder'  => true,
			'marginwidth'  => true,
			'marginheight' => true,
			'scrolling'    => true,
			'title'        => true,
			'class'        => true,
		),
	);

	$html = wp_kses( $return, $allowed_html );
	preg_match( '|^.*(<iframe.*?></iframe>).*$|m', $html, $iframes );

	if ( empty( $iframes ) ) {
		return false;
	}

	$html = str_replace( '<iframe', '<iframe sandbox="allow-scripts" security="restricted"', $iframes[1] );

	preg_match( '/ src=[\'"]([^\'"]*)[\'"]/', $html, $results );

	if ( ! empty( $results ) ) {
		$secret = wp_generate_password( 10, false );

		$url = esc_url( "{$results[1]}#?secret=$secret" );

		$html = str_replace( $results[0], " src=\"$url\" data-secret=\"$secret\"", $html );
	}

	return $html;
}

/**
 * Filters the string in the "more" link displayed after a trimmed excerpt.
 *
 * @since 4.4.0
 *
 * @param string $more_string The string shown within the more link.
 * @return string The modified excerpt.
 */
function wp_oembed_excerpt_more( $more_string ) {
	if ( ! is_embed() ) {
		return $more_string;
	}

	return sprintf(
		_x( '&hellip; %s', 'read more link', 'oembed-api' ),
		sprintf(
			'<a class="wp-embed-more" href="%s" target="_top">%s</a>',
			get_the_permalink(),
			__( 'Read more', 'oembed-api' )
		)
	);
}

/**
 * Display the post excerpt for the embed template.
 *
 * @since 4.4.0
 */
function the_excerpt_embed() {
	$output = get_the_excerpt();

	/**
	 * Filter the post excerpt for the embed template.
	 *
	 * @param string $output The current post excerpt.
	 */
	echo apply_filters( 'the_excerpt_embed', $output );
}

/**
 * Filters the post excerpt for the embed template.
 *
 * Shows players for video and audio attachments.
 *
 * @since 4.4.0
 *
 * @param string $content The current post excerpt.
 * @return string The modified post excerpt.
 */
function wp_oembed_excerpt_attachment( $content ) {
	if ( is_attachment() ) {
		return prepend_attachment( '' );
	}

	return $content;
}

/**
 * Print the CSS in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_oembed_embed_styles() {
	?>
	<style type="text/css">
	<?php
		if ( WP_DEBUG ) {
			readfile( ABSPATH . WPINC . "/css/wp-oembed-embed.css" );
		} else {
			/*
			 * If you're looking at a src version of this file, you'll see an "include"
			 * statement below. This is used by the `grunt build` process to directly
			 * include a minified version of wp-oembed-embed.css, instead of using the
			 * readfile() method from above.
			 *
			 * If you're looking at a build version of this file, you'll see a string of
			 * minified CSS. If you need to debug it, please turn on WP_DEBUG
			 * and edit wp-oembed-embed.css directly.
			 */
			?>
			include "css/wp-oembed-embed.min.css"
			<?php
		}
	?>
	</style>
	<?php
}

/**
 * Print the CSS in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_oembed_embed_scripts() {
	?>
	<script type="text/javascript">
	<?php
		if ( SCRIPT_DEBUG ) {
			readfile( ABSPATH . WPINC . "/js/wp-oembed-embed.js" );
		} else {
			/*
			 * If you're looking at a src version of this file, you'll see an "include"
			 * statement below. This is used by the `grunt build` process to directly
			 * include a minified version of wp-oembed-embed.js, instead of using the
			 * readfile() method from above.
			 *
			 * If you're looking at a build version of this file, you'll see a string of
			 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
			 * and edit wp-oembed-embed.js directly.
			 */
			?>
			include "js/wp-oembed-embed.min.js"
			<?php
		}
	?>
	</script>
	<?php
}