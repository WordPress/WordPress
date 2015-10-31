<?php
/**
 * oEmbed API: Top-level oEmbed functionality
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
 * Registers the oEmbed REST API route.
 *
 * @since 4.4.0
 */
function wp_oembed_register_route() {
	$controller = new WP_oEmbed_Controller();
	$controller->register_routes();
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

		if ( class_exists( 'SimpleXMLElement' ) ) {
			$output .= '<link rel="alternate" type="text/xml+oembed" href="' . esc_url( get_oembed_endpoint_url( get_permalink(), 'xml' ) ) . '" />' . "\n";
		}
	}

	/**
	 * Filter the oEmbed discovery links HTML.
	 *
	 * @since 4.4.0
	 *
	 * @param string $output HTML of the discovery links.
	 */
	echo apply_filters( 'oembed_discovery_links', $output );
}

/**
 * Adds the necessary JavaScript to communicate with the embedded iframes.
 *
 * @since 4.4.0
 */
function wp_oembed_add_host_js() {
	wp_enqueue_script( 'wp-embed' );
}

/**
 * Retrieves the URL to embed a specific post in an iframe.
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
 * Retrieves the oEmbed endpoint URL for a given permalink.
 *
 * Pass an empty string as the first argument to get the endpoint base URL.
 *
 * @since 4.4.0
 *
 * @param string $permalink Optional. The permalink used for the `url` query arg. Default empty.
 * @param string $format    Optional. The requested response format. Default 'json'.
 * @return string The oEmbed endpoint URL.
 */
function get_oembed_endpoint_url( $permalink = '', $format = 'json' ) {
	$url = rest_url( 'oembed/1.0/embed' );

	if ( 'json' === $format ) {
		$format = false;
	}

	if ( '' !== $permalink ) {
		$url = add_query_arg( array(
			'url'    => urlencode( $permalink ),
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
 * Retrieves the embed code for a specific post.
 *
 * @since 4.4.0
 *
 * @param int         $width  The width for the response.
 * @param int         $height The height for the response.
 * @param int|WP_Post $post   Optional. Post ID or object. Default is global `$post`.
 * @return string|false Embed code on success, false if post doesn't exist.
 */
function get_post_embed_html( $width, $height, $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	$embed_url = get_post_embed_url( $post );

	$output = '<blockquote><a href="' . get_permalink( $post ) . '">' . get_the_title( $post ) . "</a></blockquote>\n";

	$output .= "<script type='text/javascript'>\n";
	$output .= "<!--//--><![CDATA[//><!--\n";
	if ( SCRIPT_DEBUG ) {
		$output .= file_get_contents( ABSPATH . WPINC . '/js/wp-embed.js' );
	} else {
		/*
		 * If you're looking at a src version of this file, you'll see an "include"
		 * statement below. This is used by the `grunt build` process to directly
		 * include a minified version of wp-embed.js, instead of using the
		 * file_get_contents() method from above.
		 *
		 * If you're looking at a build version of this file, you'll see a string of
		 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
		 * and edit wp-embed.js directly.
		 */
		$output .=<<<JS
		!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f=-1!==navigator.appVersion.indexOf("MSIE 10"),g=!!navigator.userAgent.match(/Trident.*rv\:11\./);if(f||g)for(a=b.querySelectorAll(".wp-embedded-content[security]"),d=0;d<a.length;d++)c=a[d].cloneNode(!0),c.removeAttribute("security"),a[d].parentNode.replaceChild(c,a[d])}}var d=b.querySelector&&a.addEventListener,e=!1;a.wp=a.wp||{},a.wp.receiveEmbedMessage||(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d.secret||d.message||d.value){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)f=j[e],f.style.display="","height"===d.message&&(g=parseInt(d.value,10),g>1e3?g=1e3:200>~~g&&(g=200),f.height=g),"link"===d.message&&(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host&&b.activeElement===f&&(a.top.location.href=d.value))}},d&&(a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)))}(window,document);
JS;
	}
	$output .= "\n//--><!]]>";
	$output .= "\n</script>";

	$output .= sprintf(
		'<iframe sandbox="allow-scripts" security="restricted" src="%1$s" width="%2$d" height="%3$d" title="%4$s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>',
		esc_url( $embed_url ),
		absint( $width ),
		absint( $height ),
		esc_attr__( 'Embedded WordPress Post' )
	);

	/**
	 * Filter the embed HTML output for a given post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $output The default HTML.
	 * @param WP_Post $post   Current post object.
	 * @param int     $width  Width of the response.
	 * @param int     $height Height of the response.
	 */
	return apply_filters( 'embed_html', $output, $post, $width, $height );
}

/**
 * Retrieves the oEmbed response data for a given post.
 *
 * @since 4.4.0
 *
 * @param WP_Post|int $post  Post object or ID.
 * @param int         $width The requested width.
 * @return array|false Response data on success, false if post doesn't exist.
 */
function get_oembed_response_data( $post, $width ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	}

	if ( 'publish' !== get_post_status( $post ) ) {
		return false;
	}

	/**
	 * Filter the allowed minimum and maximum widths for the oEmbed response.
	 *
	 * @since 4.4.0
	 *
	 * @param array $min_max_width {
	 *     Minimum and maximum widths for the oEmbed response.
	 *
	 *     @type int $min Minimum width. Default 200.
	 *     @type int $max Maximum width. Default 600.
	 * }
	 */
	$min_max_width = apply_filters( 'oembed_min_max_width', array(
		'min' => 200,
		'max' => 600
	) );

	$width  = min( max( $min_max_width['min'], $width ), $min_max_width['max'] );
	$height = max( ceil( $width / 16 * 9 ), 200 );

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
	$data['html']   = get_post_embed_html( $width, $height, $post );

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
 * @param string $format The oEmbed response format. Accepts 'json' or 'xml'.
 * @return string The format, either 'xml' or 'json'. Default 'json'.
 */
function wp_oembed_ensure_format( $format ) {
	if ( ! in_array( $format, array( 'json', 'xml' ), true ) ) {
		return 'json';
	}

	return $format;
}

/**
 * Hooks into the REST API output to print XML instead of JSON.
 *
 * This is only done for the oEmbed API endpoint,
 * which supports both formats.
 *
 * @access private
 * @since 4.4.0
 *
 * @param bool                      $served  Whether the request has already been served.
 * @param WP_HTTP_ResponseInterface $result  Result to send to the client. Usually a WP_REST_Response.
 * @param WP_REST_Request           $request Request used to generate the response.
 * @param WP_REST_Server            $server  Server instance.
 * @return true
 */
function _oembed_rest_pre_serve_request( $served, $result, $request, $server ) {
	$params = $request->get_params();

	if ( '/oembed/1.0/embed' !== $request->get_route() || 'GET' !== $request->get_method() ) {
		return $served;
	}

	if ( ! isset( $params['format'] ) || 'xml' !== $params['format'] ) {
		return $served;
	}

	// Embed links inside the request.
	$data = $server->response_to_data( $result, false );

	if ( 404 === $result->get_status() ) {
		$data = $data[0];
	}

	if ( ! class_exists( 'SimpleXMLElement' ) ) {
		status_header( 501 );
		die( get_status_header_desc( 501 ) );
	}

	$result = _oembed_create_xml( $data );

	// Bail if there's no XML.
	if ( ! $result ) {
		status_header( 501 );
		return get_status_header_desc( 501 );
	}

	if ( ! headers_sent() ) {
		$server->send_header( 'Content-Type', 'text/xml; charset=' . get_option( 'blog_charset' ) );
	}

	echo $result;

	return true;
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
 * Filters the given oEmbed HTML.
 *
 * If the `$url` isn't on the trusted providers list,
 * we need to filter the HTML heavily for security.
 *
 * Only filters 'rich' and 'html' response types.
 *
 * @since 4.4.0
 *
 * @param string $result The oEmbed HTML result.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered and sanitized oEmbed result.
 */
function wp_filter_oembed_result( $result, $data, $url ) {
	if ( false === $result || ! in_array( $data->type, array( 'rich', 'video' ) ) ) {
		return $result;
	}

	require_once( ABSPATH . WPINC . '/class-oembed.php' );
	$wp_oembed = _wp_oembed_get_object();

	// Don't modify the HTML for trusted providers.
	if ( false !== $wp_oembed->get_provider( $url, array( 'discover' => false ) ) ) {
		return $result;
	}

	$allowed_html = array(
		'a'          => array(
			        'href' => true,
		),
		'blockquote' => array(),
		'iframe'     => array(
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

	$html = wp_kses( $result, $allowed_html );

	preg_match( '|(<blockquote>.*?</blockquote>)?.*(<iframe.*?></iframe>)|ms', $html, $content );
	// We require at least the iframe to exist.
	if ( empty( $content[2] ) ) {
		return false;
	}
	$html = $content[1] . $content[2];

	if ( ! empty( $content[1] ) ) {
		// We have a blockquote to fall back on. Hide the iframe by default.
		$html = str_replace( '<iframe', '<iframe style="display:none;"', $html );
	}

	$html = str_replace( '<iframe', '<iframe sandbox="allow-scripts" security="restricted"', $html );

	preg_match( '/ src=[\'"]([^\'"]*)[\'"]/', $html, $results );

	if ( ! empty( $results ) ) {
		$secret = wp_generate_password( 10, false );

		$url = esc_url( "{$results[1]}#?secret=$secret" );

		$html = str_replace( $results[0], " src=\"$url\" data-secret=\"$secret\"", $html );
		$html = str_replace( '<blockquote', "<blockquote data-secret=\"$secret\"", $html );
	}

	return $html;
}

/**
 * Filters the string in the 'more' link displayed after a trimmed excerpt.
 *
 * Replaces '[...]' (appended to automatically generated excerpts) with an
 * ellipsis and a "Continue reading" link in the embed template.
 *
 * @since 4.4.0
 *
 * @param string $more_string Default 'more' string.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function wp_embed_excerpt_more( $more_string ) {
	if ( ! is_embed() ) {
		return $more_string;
	}

	$link = sprintf( '<a href="%1$s" class="wp-embed-more" target="_top">%2$s</a>',
		esc_url( get_permalink() ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading %s' ), '<span class="screen-reader-text">' . get_the_title() . '</span>' )
	);
	return ' &hellip; ' . $link;
}

/**
 * Displays the post excerpt for the embed template.
 *
 * Intended to be used in 'The Loop'.
 *
 * @since 4.4.0
 */
function the_excerpt_embed() {
	$output = get_the_excerpt();

	/**
	 * Filter the post excerpt for the embed template.
	 *
	 * @since 4.4.0
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
function wp_embed_excerpt_attachment( $content ) {
	if ( is_attachment() ) {
		return prepend_attachment( '' );
	}

	return $content;
}

/**
 * Enqueue embed iframe default CSS and JS & fire do_action('enqueue_embed_scripts')
 *
 * Enqueue PNG fallback CSS for embed iframe for legacy versions of IE.
 *
 * Allows plugins to queue scripts for the embed iframe end using wp_enqueue_script().
 * Runs first in oembed_head().
 *
 * @since 4.4.0
 */
function enqueue_embed_scripts() {
	wp_enqueue_style( 'open-sans' );
	wp_enqueue_style( 'wp-embed-template-ie' );

	/**
	 * Fires when scripts and styles are enqueued for the embed iframe.
	 *
	 * @since 4.4.0
	 */
	do_action( 'enqueue_embed_scripts' );
}

/**
 * Prints the CSS in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_embed_styles() {
	?>
	<style type="text/css">
	<?php
		if ( SCRIPT_DEBUG ) {
			readfile( ABSPATH . WPINC . "/css/wp-embed-template.css" );
		} else {
			/*
			 * If you're looking at a src version of this file, you'll see an "include"
			 * statement below. This is used by the `grunt build` process to directly
			 * include a minified version of wp-oembed-embed.css, instead of using the
			 * readfile() method from above.
			 *
			 * If you're looking at a build version of this file, you'll see a string of
			 * minified CSS. If you need to debug it, please turn on SCRIPT_DEBUG
			 * and edit wp-embed-template.css directly.
			 */
			?>
			body,html{padding:0;margin:0}body{font-family:sans-serif}.screen-reader-text{clip:rect(1px,1px,1px,1px);height:1px;overflow:hidden;position:absolute!important;width:1px}.dashicons{display:inline-block;width:20px;height:20px;background-color:transparent;background-repeat:no-repeat;-webkit-background-size:20px 20px;background-size:20px;background-position:center;-webkit-transition:background .1s ease-in;transition:background .1s ease-in;position:relative;top:5px}.dashicons-no{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M15.55%2013.7l-2.19%202.06-3.42-3.65-3.64%203.43-2.06-2.18%203.64-3.43-3.42-3.64%202.18-2.06%203.43%203.64%203.64-3.42%202.05%202.18-3.64%203.43z%27%20fill%3D%27%23fff%27%2F%3E%3C%2Fsvg%3E")}.dashicons-admin-comments{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M5%202h9q.82%200%201.41.59T16%204v7q0%20.82-.59%201.41T14%2013h-2l-5%205v-5H5q-.82%200-1.41-.59T3%2011V4q0-.82.59-1.41T5%202z%27%20fill%3D%27%2382878c%27%2F%3E%3C%2Fsvg%3E")}.wp-embed-comments a:hover .dashicons-admin-comments{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M5%202h9q.82%200%201.41.59T16%204v7q0%20.82-.59%201.41T14%2013h-2l-5%205v-5H5q-.82%200-1.41-.59T3%2011V4q0-.82.59-1.41T5%202z%27%20fill%3D%27%230073aa%27%2F%3E%3C%2Fsvg%3E")}.dashicons-share{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.5%2012q1.24%200%202.12.88T17.5%2015t-.88%202.12-2.12.88-2.12-.88T11.5%2015q0-.34.09-.69l-4.38-2.3Q6.32%2013%205%2013q-1.24%200-2.12-.88T2%2010t.88-2.12T5%207q1.3%200%202.21.99l4.38-2.3q-.09-.35-.09-.69%200-1.24.88-2.12T14.5%202t2.12.88T17.5%205t-.88%202.12T14.5%208q-1.3%200-2.21-.99l-4.38%202.3Q8%209.66%208%2010t-.09.69l4.38%202.3q.89-.99%202.21-.99z%27%20fill%3D%27%2382878c%27%2F%3E%3C%2Fsvg%3E");display:none}.js .dashicons-share{display:inline-block}.wp-embed-share-dialog-open:hover .dashicons-share{background-image:url("data:image/svg+xml;charset=utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%2020%2020%27%3E%3Cpath%20d%3D%27M14.5%2012q1.24%200%202.12.88T17.5%2015t-.88%202.12-2.12.88-2.12-.88T11.5%2015q0-.34.09-.69l-4.38-2.3Q6.32%2013%205%2013q-1.24%200-2.12-.88T2%2010t.88-2.12T5%207q1.3%200%202.21.99l4.38-2.3q-.09-.35-.09-.69%200-1.24.88-2.12T14.5%202t2.12.88T17.5%205t-.88%202.12T14.5%208q-1.3%200-2.21-.99l-4.38%202.3Q8%209.66%208%2010t-.09.69l4.38%202.3q.89-.99%202.21-.99z%27%20fill%3D%27%230073aa%27%2F%3E%3C%2Fsvg%3E")}.wp-embed{padding:25px;font:400 14px/1.5 'Open Sans',sans-serif;color:#82878c;background:#fff;border:1px solid #e5e5e5;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05);overflow:auto;zoom:1}.wp-embed a{color:#82878c;text-decoration:none}.wp-embed a:hover{text-decoration:underline}.wp-embed-featured-image{margin-bottom:20px}.wp-embed-featured-image img{width:100%;height:auto;border:none}.wp-embed-featured-image.square{float:left;max-width:10pc;margin-right:20px}.wp-embed p{margin:0}p.wp-embed-heading{margin:0 0 15px;font-weight:700;font-size:22px;line-height:1.3}.wp-embed-heading a{color:#32373c}.wp-embed .wp-embed-more{color:#b4b9be}.wp-embed-footer{display:table;width:100%;margin-top:30px}.wp-embed-site-icon{position:absolute;top:50%;left:0;-webkit-transform:translateY(-50%);-ms-transform:translateY(-50%);transform:translateY(-50%);height:25px;width:25px;border:0}.wp-embed-site-title{font-weight:700;line-height:25px}.wp-embed-site-title a{position:relative;display:inline-block;padding-left:35px}.wp-embed-meta,.wp-embed-site-title{display:table-cell}.wp-embed-meta{text-align:right;white-space:nowrap;vertical-align:middle}.wp-embed-comments,.wp-embed-share{display:inline}.wp-embed-comments a,.wp-embed-share-tab-button{display:inline-block}.wp-embed-meta a:hover{text-decoration:none;color:#0073aa}.wp-embed-comments a{line-height:25px}.wp-embed-comments+.wp-embed-share{margin-left:10px}.wp-embed-share-dialog{position:absolute;top:0;left:0;right:0;bottom:0;background-color:#222;background-color:rgba(10,10,10,.9);color:#fff;opacity:1;-webkit-transition:opacity .25s ease-in-out;transition:opacity .25s ease-in-out}.wp-embed-share-dialog.hidden{opacity:0;visibility:hidden}.wp-embed-share-dialog-close,.wp-embed-share-dialog-open{margin:-8px 0 0;padding:0;background:0 0;border:none;cursor:pointer;outline:0}.wp-embed-share-dialog-close .dashicons,.wp-embed-share-dialog-open .dashicons{padding:4px}.wp-embed-share-dialog-open .dashicons{top:8px}.wp-embed-share-dialog-close:focus .dashicons,.wp-embed-share-dialog-open:focus .dashicons{-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);-webkit-border-radius:100%;border-radius:100%}.wp-embed-share-dialog-close{position:absolute;top:20px;right:20px;font-size:22px}.wp-embed-share-dialog-close:hover{text-decoration:none}.wp-embed-share-dialog-close .dashicons{height:24px;width:24px;-webkit-background-size:24px 24px;background-size:24px}.wp-embed-share-dialog-content{height:100%;-webkit-transform-style:preserve-3d;transform-style:preserve-3d;overflow:hidden}.wp-embed-share-dialog-text{margin-top:25px;padding:20px}.wp-embed-share-tabs{margin:0 0 20px;padding:0;list-style:none}.wp-embed-share-tab-button button{margin:0;padding:0;border:none;background:0 0;font-size:1pc;line-height:1.3;color:#aaa;cursor:pointer;-webkit-transition:color .1s ease-in;transition:color .1s ease-in}.wp-embed-share-tab-button [aria-selected=true],.wp-embed-share-tab-button button:hover{color:#fff}.wp-embed-share-tab-button+.wp-embed-share-tab-button{margin:0 0 0 10px;padding:0 0 0 11px;border-left:1px solid #aaa}.wp-embed-share-tab[aria-hidden=true]{display:none}p.wp-embed-share-description{margin:0;font-size:14px;line-height:1;font-style:italic;color:#aaa}.wp-embed-share-input{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;width:100%;border:none;height:28px;margin:0 0 10px;padding:0 5px;font:400 14px/1.5 'Open Sans',sans-serif;resize:none;cursor:text}textarea.wp-embed-share-input{height:72px}html[dir=rtl] .wp-embed-featured-image.square{float:right;margin-right:0;margin-left:20px}html[dir=rtl] .wp-embed-site-title a{padding-left:0;padding-right:35px}html[dir=rtl] .wp-embed-site-icon{margin-right:0;margin-left:10px;left:auto;right:0}html[dir=rtl] .wp-embed-meta{text-align:left}html[dir=rtl] .wp-embed-share{margin-left:0;margin-right:10px}html[dir=rtl] .wp-embed-share-dialog-close{right:auto;left:20px}html[dir=rtl] .wp-embed-share-tab-button+.wp-embed-share-tab-button{margin:0 10px 0 0;padding:0 11px 0 0;border-left:none;border-right:1px solid #aaa}
			<?php
		}
	?>
	</style>
	<?php
}

/**
 * Prints the JavaScript in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_embed_scripts() {
	?>
	<script type="text/javascript">
	<?php
		if ( SCRIPT_DEBUG ) {
			readfile( ABSPATH . WPINC . "/js/wp-embed-template.js" );
		} else {
			/*
			 * If you're looking at a src version of this file, you'll see an "include"
			 * statement below. This is used by the `grunt build` process to directly
			 * include a minified version of wp-embed-template.js, instead of using the
			 * readfile() method from above.
			 *
			 * If you're looking at a build version of this file, you'll see a string of
			 * minified JavaScript. If you need to debug it, please turn on SCRIPT_DEBUG
			 * and edit wp-embed-template.js directly.
			 */
			?>
			!function(a,b){"use strict";function c(b,c){a.parent.postMessage({message:b,value:c,secret:g},"*")}function d(){function d(){k.className=k.className.replace("hidden",""),n[0].select()}function e(){k.className+=" hidden",b.querySelector(".wp-embed-share-dialog-open").focus()}function f(a){var c=b.querySelector('.wp-embed-share-tab-button [aria-selected="true"]');c.setAttribute("aria-selected","false"),b.querySelector("#"+c.getAttribute("aria-controls")).setAttribute("aria-hidden","true"),a.target.setAttribute("aria-selected","true"),b.querySelector("#"+a.target.getAttribute("aria-controls")).setAttribute("aria-hidden","false")}function g(a){var c,d,e=a.target,f=e.parentElement.previousElementSibling,g=e.parentElement.nextElementSibling;if(37===a.keyCode)c=f;else{if(39!==a.keyCode)return!1;c=g}"rtl"===b.documentElement.getAttribute("dir")&&(c=c===f?g:f),c&&(d=c.firstElementChild,e.setAttribute("tabindex","-1"),e.setAttribute("aria-selected",!1),b.querySelector("#"+e.getAttribute("aria-controls")).setAttribute("aria-hidden","true"),d.setAttribute("tabindex","0"),d.setAttribute("aria-selected","true"),d.focus(),b.querySelector("#"+d.getAttribute("aria-controls")).setAttribute("aria-hidden","false"))}function h(a){var b,d=a.target;b=d.hasAttribute("href")?d.getAttribute("href"):d.parentElement.getAttribute("href"),c("link",b),a.preventDefault()}if(!i){i=!0;var j,k=b.querySelector(".wp-embed-share-dialog"),l=b.querySelector(".wp-embed-share-dialog-open"),m=b.querySelector(".wp-embed-share-dialog-close"),n=b.querySelectorAll(".wp-embed-share-input"),o=b.querySelectorAll(".wp-embed-share-tab-button button"),p=b.getElementsByTagName("a");if(n)for(j=0;j<n.length;j++)n[j].addEventListener("click",function(a){a.target.select()});if(l&&l.addEventListener("click",function(a){d(),a.preventDefault()}),m&&m.addEventListener("click",function(a){e(),a.preventDefault()}),o)for(j=0;j<o.length;j++)o[j].addEventListener("click",f),o[j].addEventListener("keydown",g);if(b.addEventListener("keydown",function(a){27===a.keyCode&&-1===k.className.indexOf("hidden")&&e()},!1),a.self!==a.top)for(c("height",Math.ceil(b.body.getBoundingClientRect().height)),j=0;j<p.length;j++)p[j].addEventListener("click",h)}}function e(){a.self!==a.top&&(clearTimeout(f),f=setTimeout(function(){c("height",Math.ceil(b.body.getBoundingClientRect().height))},100))}var f,g=a.location.hash.replace(/.*secret=([\d\w]{10}).*/,"$1"),h=b.querySelector&&a.addEventListener,i=!1;h&&(b.documentElement.className=b.documentElement.className.replace(/\bno-js\b/,"")+" js",b.addEventListener("DOMContentLoaded",d,!1),a.addEventListener("load",d,!1),a.addEventListener("resize",e,!1))}(window,document);
			<?php
		}
	?>
	</script>
	<?php
}

/**
 * Prepare the oembed HTML to be displayed in an RSS feed.
 *
 * @since 4.4.0
 * @access private
 *
 * @param string $content The content to filter.
 * @return string The filtered content.
 */
function _oembed_filter_feed_content( $content ) {
	return str_replace( '<iframe sandbox="allow-scripts" security="restricted" style="display:none;"', '<iframe sandbox="allow-scripts" security="restricted"', $content );
}
