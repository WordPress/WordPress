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
 * Creates default array of embed parameters.
 *
 * The width defaults to the content width as specified by the theme. If the
 * theme does not specify a content width, then 500px is used.
 *
 * The default height is 1.5 times the width, or 1000px, whichever is smaller.
 *
 * The {@see 'embed_defaults'} filter can be used to adjust either of these values.
 *
 * @since 2.9.0
 *
 * @global int $content_width
 *
 * @param string $url Optional. The URL that should be embedded. Default empty.
 * @return int[] {
 *     Indexed array of the embed width and height in pixels.
 *
 *     @type int $0 The embed width.
 *     @type int $1 The embed height.
 * }
 */
function wp_embed_defaults( $url = '' ) {
	if ( ! empty( $GLOBALS['content_width'] ) ) {
		$width = (int) $GLOBALS['content_width'];
	}

	if ( empty( $width ) ) {
		$width = 500;
	}

	$height = min( ceil( $width * 1.5 ), 1000 );

	/**
	 * Filters the default array of embed dimensions.
	 *
	 * @since 2.9.0
	 *
	 * @param int[]  $size {
	 *     Indexed array of the embed width and height in pixels.
	 *
	 *     @type int $0 The embed width.
	 *     @type int $1 The embed height.
	 * }
	 * @param string $url  The URL that should be embedded.
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
 * @param array|string $args {
 *     Optional. Additional arguments for retrieving embed HTML. Default empty.
 *
 *     @type int|string $width    Optional. The `maxwidth` value passed to the provider URL.
 *     @type int|string $height   Optional. The `maxheight` value passed to the provider URL.
 *     @type bool       $discover Optional. Determines whether to attempt to discover link tags
 *                                at the given URL for an oEmbed provider when the provider URL
 *                                is not found in the built-in providers list. Default true.
 * }
 * @return string|false The embed HTML on success, false on failure.
 */
function wp_oembed_get( $url, $args = '' ) {
	$oembed = _wp_oembed_get_object();
	return $oembed->get_html( $url, $args );
}

/**
 * Returns the initialized WP_oEmbed object.
 *
 * @since 2.9.0
 * @access private
 *
 * @return WP_oEmbed object.
 */
function _wp_oembed_get_object() {
	static $wp_oembed = null;

	if ( is_null( $wp_oembed ) ) {
		$wp_oembed = new WP_oEmbed();
	}
	return $wp_oembed;
}

/**
 * Adds a URL format and oEmbed provider URL pair.
 *
 * @since 2.9.0
 *
 * @see WP_oEmbed
 *
 * @param string $format   The format of URL that this provider can handle. You can use asterisks
 *                         as wildcards.
 * @param string $provider The URL to the oEmbed provider.
 * @param bool   $regex    Optional. Whether the `$format` parameter is in a RegEx format. Default false.
 */
function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	if ( did_action( 'plugins_loaded' ) ) {
		$oembed                       = _wp_oembed_get_object();
		$oembed->providers[ $format ] = array( $provider, $regex );
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
	 * Filters whether to load the default embed handlers.
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

	/**
	 * Filters the audio embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Audio embed handler callback function.
	 */
	wp_embed_register_handler( 'audio', '#^https?://.+?\.(' . implode( '|', wp_get_audio_extensions() ) . ')$#i', apply_filters( 'wp_audio_embed_handler', 'wp_embed_handler_audio' ), 9999 );

	/**
	 * Filters the video embed handler callback.
	 *
	 * @since 3.6.0
	 *
	 * @param callable $handler Video embed handler callback function.
	 */
	wp_embed_register_handler( 'video', '#^https?://.+?\.(' . implode( '|', wp_get_video_extensions() ) . ')$#i', apply_filters( 'wp_video_embed_handler', 'wp_embed_handler_video' ), 9999 );
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
	$embed = $wp_embed->autoembed( sprintf( 'https://youtube.com/watch?v=%s', urlencode( $matches[2] ) ) );

	/**
	 * Filters the YoutTube embed output.
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
	 * Filters the audio embed output.
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
	 * Filters the video embed output.
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
 * Adds oEmbed discovery links in the head element of the website.
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
	 * Filters the oEmbed discovery links HTML.
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
 * This function is no longer used directly. For back-compat it exists exclusively as a way to indicate that the oEmbed
 * host JS _should_ be added. In `default-filters.php` there remains this code:
 *
 *     add_action( 'wp_head', 'wp_oembed_add_host_js' )
 *
 * Historically a site has been able to disable adding the oEmbed host script by doing:
 *
 *     remove_action( 'wp_head', 'wp_oembed_add_host_js' )
 *
 * In order to ensure that such code still works as expected, this function remains. There is now a `has_action()` check
 * in `wp_maybe_enqueue_oembed_host_js()` to see if `wp_oembed_add_host_js()` has not been unhooked from running at the
 * `wp_head` action.
 *
 * @since 4.4.0
 * @deprecated 5.9.0 Use {@see wp_maybe_enqueue_oembed_host_js()} instead.
 */
function wp_oembed_add_host_js() {}

/**
 * Enqueue the wp-embed script if the provided oEmbed HTML contains a post embed.
 *
 * In order to only enqueue the wp-embed script on pages that actually contain post embeds, this function checks if the
 * provided HTML contains post embed markup and if so enqueues the script so that it will get printed in the footer.
 *
 * @since 5.9.0
 *
 * @param string $html Embed markup.
 * @return string Embed markup (without modifications).
 */
function wp_maybe_enqueue_oembed_host_js( $html ) {
	if (
		has_action( 'wp_head', 'wp_oembed_add_host_js' )
		&&
		preg_match( '/<blockquote\s[^>]*?wp-embedded-content/', $html )
	) {
		wp_enqueue_script( 'wp-embed' );
	}
	return $html;
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

	$embed_url     = trailingslashit( get_permalink( $post ) ) . user_trailingslashit( 'embed' );
	$path_conflict = get_page_by_path( str_replace( home_url(), '', $embed_url ), OBJECT, get_post_types( array( 'public' => true ) ) );

	if ( ! get_option( 'permalink_structure' ) || $path_conflict ) {
		$embed_url = add_query_arg( array( 'embed' => 'true' ), get_permalink( $post ) );
	}

	/**
	 * Filters the URL to embed a specific post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $embed_url The post embed URL.
	 * @param WP_Post $post      The corresponding post object.
	 */
	return sanitize_url( apply_filters( 'post_embed_url', $embed_url, $post ) );
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

	if ( '' !== $permalink ) {
		$url = add_query_arg(
			array(
				'url'    => urlencode( $permalink ),
				'format' => ( 'json' !== $format ) ? $format : false,
			),
			$url
		);
	}

	/**
	 * Filters the oEmbed endpoint URL.
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

	$secret     = wp_generate_password( 10, false );
	$embed_url .= "#?secret={$secret}";

	$output = sprintf(
		'<blockquote class="wp-embedded-content" data-secret="%1$s"><a href="%2$s">%3$s</a></blockquote>',
		esc_attr( $secret ),
		esc_url( get_permalink( $post ) ),
		get_the_title( $post )
	);

	$output .= sprintf(
		'<iframe sandbox="allow-scripts" security="restricted" src="%1$s" width="%2$d" height="%3$d" title="%4$s" data-secret="%5$s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" class="wp-embedded-content"></iframe>',
		esc_url( $embed_url ),
		absint( $width ),
		absint( $height ),
		esc_attr(
			sprintf(
				/* translators: 1: Post title, 2: Site title. */
				__( '&#8220;%1$s&#8221; &#8212; %2$s' ),
				get_the_title( $post ),
				get_bloginfo( 'name' )
			)
		),
		esc_attr( $secret )
	);

	// Note that the script must be placed after the <blockquote> and <iframe> due to a regexp parsing issue in
	// `wp_filter_oembed_result()`. Because of the regex pattern starts with `|(<blockquote>.*?</blockquote>)?.*|`
	// wherein the <blockquote> is marked as being optional, if it is not at the beginning of the string then the group
	// will fail to match and everything will be matched by `.*` and not included in the group. This regex issue goes
	// back to WordPress 4.4, so in order to not break older installs this script must come at the end.
	$output .= wp_get_inline_script_tag(
		file_get_contents( ABSPATH . WPINC . '/js/wp-embed' . wp_scripts_get_suffix() . '.js' )
	);

	/**
	 * Filters the embed HTML output for a given post.
	 *
	 * @since 4.4.0
	 *
	 * @param string  $output The default iframe tag to display embedded content.
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
 * @param WP_Post|int $post  Post ID or post object.
 * @param int         $width The requested width.
 * @return array|false Response data on success, false if post doesn't exist
 *                     or is not publicly viewable.
 */
function get_oembed_response_data( $post, $width ) {
	$post  = get_post( $post );
	$width = absint( $width );

	if ( ! $post ) {
		return false;
	}

	if ( ! is_post_publicly_viewable( $post ) ) {
		return false;
	}

	/**
	 * Filters the allowed minimum and maximum widths for the oEmbed response.
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
	$min_max_width = apply_filters(
		'oembed_min_max_width',
		array(
			'min' => 200,
			'max' => 600,
		)
	);

	$width  = min( max( $min_max_width['min'], $width ), $min_max_width['max'] );
	$height = max( ceil( $width / 16 * 9 ), 200 );

	$data = array(
		'version'       => '1.0',
		'provider_name' => get_bloginfo( 'name' ),
		'provider_url'  => get_home_url(),
		'author_name'   => get_bloginfo( 'name' ),
		'author_url'    => get_home_url(),
		'title'         => get_the_title( $post ),
		'type'          => 'link',
	);

	$author = get_userdata( $post->post_author );

	if ( $author ) {
		$data['author_name'] = $author->display_name;
		$data['author_url']  = get_author_posts_url( $author->ID );
	}

	/**
	 * Filters the oEmbed response data.
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
 * Retrieves the oEmbed response data for a given URL.
 *
 * @since 5.0.0
 *
 * @param string $url  The URL that should be inspected for discovery `<link>` tags.
 * @param array  $args oEmbed remote get arguments.
 * @return object|false oEmbed response data if the URL does belong to the current site. False otherwise.
 */
function get_oembed_response_data_for_url( $url, $args ) {
	$switched_blog = false;

	if ( is_multisite() ) {
		$url_parts = wp_parse_args(
			wp_parse_url( $url ),
			array(
				'host' => '',
				'path' => '/',
			)
		);

		$qv = array(
			'domain'                 => $url_parts['host'],
			'path'                   => '/',
			'update_site_meta_cache' => false,
		);

		// In case of subdirectory configs, set the path.
		if ( ! is_subdomain_install() ) {
			$path = explode( '/', ltrim( $url_parts['path'], '/' ) );
			$path = reset( $path );

			if ( $path ) {
				$qv['path'] = get_network()->path . $path . '/';
			}
		}

		$sites = get_sites( $qv );
		$site  = reset( $sites );

		// Do not allow embeds for deleted/archived/spam sites.
		if ( ! empty( $site->deleted ) || ! empty( $site->spam ) || ! empty( $site->archived ) ) {
			return false;
		}

		if ( $site && get_current_blog_id() !== (int) $site->blog_id ) {
			switch_to_blog( $site->blog_id );
			$switched_blog = true;
		}
	}

	$post_id = url_to_postid( $url );

	/** This filter is documented in wp-includes/class-wp-oembed-controller.php */
	$post_id = apply_filters( 'oembed_request_post_id', $post_id, $url );

	if ( ! $post_id ) {
		if ( $switched_blog ) {
			restore_current_blog();
		}

		return false;
	}

	$width = isset( $args['width'] ) ? $args['width'] : 0;

	$data = get_oembed_response_data( $post_id, $width );

	if ( $switched_blog ) {
		restore_current_blog();
	}

	return $data ? (object) $data : false;
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
		} elseif ( wp_attachment_is( 'video', $post ) ) {
			$thumbnail_id = get_post_thumbnail_id( $post );
			$data['type'] = 'video';
		}
	}

	if ( $thumbnail_id ) {
		list( $thumbnail_url, $thumbnail_width, $thumbnail_height ) = wp_get_attachment_image_src( $thumbnail_id, array( $width, 99999 ) );
		$data['thumbnail_url']                                      = $thumbnail_url;
		$data['thumbnail_width']                                    = $thumbnail_width;
		$data['thumbnail_height']                                   = $thumbnail_height;
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
 * @param bool             $served  Whether the request has already been served.
 * @param WP_HTTP_Response $result  Result to send to the client. Usually a `WP_REST_Response`.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @param WP_REST_Server   $server  Server instance.
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
 * Filters the given oEmbed HTML to make sure iframes have a title attribute.
 *
 * @since 5.2.0
 *
 * @param string $result The oEmbed HTML result.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered oEmbed result.
 */
function wp_filter_oembed_iframe_title_attribute( $result, $data, $url ) {
	if ( false === $result || ! in_array( $data->type, array( 'rich', 'video' ), true ) ) {
		return $result;
	}

	$title = ! empty( $data->title ) ? $data->title : '';

	$pattern = '`<iframe([^>]*)>`i';
	if ( preg_match( $pattern, $result, $matches ) ) {
		$attrs = wp_kses_hair( $matches[1], wp_allowed_protocols() );

		foreach ( $attrs as $attr => $item ) {
			$lower_attr = strtolower( $attr );
			if ( $lower_attr === $attr ) {
				continue;
			}
			if ( ! isset( $attrs[ $lower_attr ] ) ) {
				$attrs[ $lower_attr ] = $item;
				unset( $attrs[ $attr ] );
			}
		}
	}

	if ( ! empty( $attrs['title']['value'] ) ) {
		$title = $attrs['title']['value'];
	}

	/**
	 * Filters the title attribute of the given oEmbed HTML iframe.
	 *
	 * @since 5.2.0
	 *
	 * @param string $title  The title attribute.
	 * @param string $result The oEmbed HTML result.
	 * @param object $data   A data object result from an oEmbed provider.
	 * @param string $url    The URL of the content to be embedded.
	 */
	$title = apply_filters( 'oembed_iframe_title_attribute', $title, $result, $data, $url );

	if ( '' === $title ) {
		return $result;
	}

	if ( isset( $attrs['title'] ) ) {
		unset( $attrs['title'] );
		$attr_string = implode( ' ', wp_list_pluck( $attrs, 'whole' ) );
		$result      = str_replace( $matches[0], '<iframe ' . trim( $attr_string ) . '>', $result );
	}
	return str_ireplace( '<iframe ', sprintf( '<iframe title="%s" ', esc_attr( $title ) ), $result );
}


/**
 * Filters the given oEmbed HTML.
 *
 * If the `$url` isn't on the trusted providers list,
 * we need to filter the HTML heavily for security.
 *
 * Only filters 'rich' and 'video' response types.
 *
 * @since 4.4.0
 *
 * @param string $result The oEmbed HTML result.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 * @return string The filtered and sanitized oEmbed result.
 */
function wp_filter_oembed_result( $result, $data, $url ) {
	if ( false === $result || ! in_array( $data->type, array( 'rich', 'video' ), true ) ) {
		return $result;
	}

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
		),
	);

	$html = wp_kses( $result, $allowed_html );

	preg_match( '|(<blockquote>.*?</blockquote>)?.*(<iframe.*?></iframe>)|ms', $html, $content );
	// We require at least the iframe to exist.
	if ( empty( $content[2] ) ) {
		return false;
	}
	$html = $content[1] . $content[2];

	preg_match( '/ src=([\'"])(.*?)\1/', $html, $results );

	if ( ! empty( $results ) ) {
		$secret = wp_generate_password( 10, false );

		$url = esc_url( "{$results[2]}#?secret=$secret" );
		$q   = $results[1];

		$html = str_replace( $results[0], ' src=' . $q . $url . $q . ' data-secret=' . $q . $secret . $q, $html );
		$html = str_replace( '<blockquote', "<blockquote data-secret=\"$secret\"", $html );
	}

	$allowed_html['blockquote']['data-secret'] = true;
	$allowed_html['iframe']['data-secret']     = true;

	$html = wp_kses( $html, $allowed_html );

	if ( ! empty( $content[1] ) ) {
		// We have a blockquote to fall back on. Hide the iframe by default.
		$html = str_replace( '<iframe', '<iframe style="position: absolute; clip: rect(1px, 1px, 1px, 1px);"', $html );
		$html = str_replace( '<blockquote', '<blockquote class="wp-embedded-content"', $html );
	}

	$html = str_ireplace( '<iframe', '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted"', $html );

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

	$link = sprintf(
		'<a href="%1$s" class="wp-embed-more" target="_top">%2$s</a>',
		esc_url( get_permalink() ),
		/* translators: %s: Post title. */
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
	 * Filters the post excerpt for the embed template.
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
 * Enqueues embed iframe default CSS and JS.
 *
 * Enqueue PNG fallback CSS for embed iframe for legacy versions of IE.
 *
 * Allows plugins to queue scripts for the embed iframe end using wp_enqueue_script().
 * Runs first in oembed_head().
 *
 * @since 4.4.0
 */
function enqueue_embed_scripts() {
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
	$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
	$suffix    = SCRIPT_DEBUG ? '' : '.min';
	?>
	<style<?php echo $type_attr; ?>>
		<?php echo file_get_contents( ABSPATH . WPINC . "/css/wp-embed-template$suffix.css" ); ?>
	</style>
	<?php
}

/**
 * Prints the JavaScript in the embed iframe header.
 *
 * @since 4.4.0
 */
function print_embed_scripts() {
	wp_print_inline_script_tag(
		file_get_contents( ABSPATH . WPINC . '/js/wp-embed-template' . wp_scripts_get_suffix() . '.js' )
	);
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
	return str_replace( '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted" style="position: absolute; clip: rect(1px, 1px, 1px, 1px);"', '<iframe class="wp-embedded-content" sandbox="allow-scripts" security="restricted"', $content );
}

/**
 * Prints the necessary markup for the embed comments button.
 *
 * @since 4.4.0
 */
function print_embed_comments_button() {
	if ( is_404() || ! ( get_comments_number() || comments_open() ) ) {
		return;
	}
	?>
	<div class="wp-embed-comments">
		<a href="<?php comments_link(); ?>" target="_top">
			<span class="dashicons dashicons-admin-comments"></span>
			<?php
			printf(
				/* translators: %s: Number of comments. */
				_n(
					'%s <span class="screen-reader-text">Comment</span>',
					'%s <span class="screen-reader-text">Comments</span>',
					get_comments_number()
				),
				number_format_i18n( get_comments_number() )
			);
			?>
		</a>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the embed sharing button.
 *
 * @since 4.4.0
 */
function print_embed_sharing_button() {
	if ( is_404() ) {
		return;
	}
	?>
	<div class="wp-embed-share">
		<button type="button" class="wp-embed-share-dialog-open" aria-label="<?php esc_attr_e( 'Open sharing dialog' ); ?>">
			<span class="dashicons dashicons-share"></span>
		</button>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the embed sharing dialog.
 *
 * @since 4.4.0
 */
function print_embed_sharing_dialog() {
	if ( is_404() ) {
		return;
	}

	$unique_suffix            = get_the_ID() . '-' . wp_rand();
	$share_tab_wordpress_id   = 'wp-embed-share-tab-wordpress-' . $unique_suffix;
	$share_tab_html_id        = 'wp-embed-share-tab-html-' . $unique_suffix;
	$description_wordpress_id = 'wp-embed-share-description-wordpress-' . $unique_suffix;
	$description_html_id      = 'wp-embed-share-description-html-' . $unique_suffix;
	?>
	<div class="wp-embed-share-dialog hidden" role="dialog" aria-label="<?php esc_attr_e( 'Sharing options' ); ?>">
		<div class="wp-embed-share-dialog-content">
			<div class="wp-embed-share-dialog-text">
				<ul class="wp-embed-share-tabs" role="tablist">
					<li class="wp-embed-share-tab-button wp-embed-share-tab-button-wordpress" role="presentation">
						<button type="button" role="tab" aria-controls="<?php echo $share_tab_wordpress_id; ?>" aria-selected="true" tabindex="0"><?php esc_html_e( 'WordPress Embed' ); ?></button>
					</li>
					<li class="wp-embed-share-tab-button wp-embed-share-tab-button-html" role="presentation">
						<button type="button" role="tab" aria-controls="<?php echo $share_tab_html_id; ?>" aria-selected="false" tabindex="-1"><?php esc_html_e( 'HTML Embed' ); ?></button>
					</li>
				</ul>
				<div id="<?php echo $share_tab_wordpress_id; ?>" class="wp-embed-share-tab" role="tabpanel" aria-hidden="false">
					<input type="text" value="<?php the_permalink(); ?>" class="wp-embed-share-input" aria-label="<?php esc_attr_e( 'URL' ); ?>" aria-describedby="<?php echo $description_wordpress_id; ?>" tabindex="0" readonly/>

					<p class="wp-embed-share-description" id="<?php echo $description_wordpress_id; ?>">
						<?php _e( 'Copy and paste this URL into your WordPress site to embed' ); ?>
					</p>
				</div>
				<div id="<?php echo $share_tab_html_id; ?>" class="wp-embed-share-tab" role="tabpanel" aria-hidden="true">
					<textarea class="wp-embed-share-input" aria-label="<?php esc_attr_e( 'HTML' ); ?>" aria-describedby="<?php echo $description_html_id; ?>" tabindex="0" readonly><?php echo esc_textarea( get_post_embed_html( 600, 400 ) ); ?></textarea>

					<p class="wp-embed-share-description" id="<?php echo $description_html_id; ?>">
						<?php _e( 'Copy and paste this code into your site to embed' ); ?>
					</p>
				</div>
			</div>

			<button type="button" class="wp-embed-share-dialog-close" aria-label="<?php esc_attr_e( 'Close sharing dialog' ); ?>">
				<span class="dashicons dashicons-no"></span>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Prints the necessary markup for the site title in an embed template.
 *
 * @since 4.5.0
 */
function the_embed_site_title() {
	$site_title = sprintf(
		'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon" /><span>%s</span></a>',
		esc_url( home_url() ),
		esc_url( get_site_icon_url( 32, includes_url( 'images/w-logo-blue.png' ) ) ),
		esc_url( get_site_icon_url( 64, includes_url( 'images/w-logo-blue.png' ) ) ),
		esc_html( get_bloginfo( 'name' ) )
	);

	$site_title = '<div class="wp-embed-site-title">' . $site_title . '</div>';

	/**
	 * Filters the site title HTML in the embed footer.
	 *
	 * @since 4.4.0
	 *
	 * @param string $site_title The site title HTML.
	 */
	echo apply_filters( 'embed_site_title_html', $site_title );
}

/**
 * Filters the oEmbed result before any HTTP requests are made.
 *
 * If the URL belongs to the current site, the result is fetched directly instead of
 * going through the oEmbed discovery process.
 *
 * @since 4.5.3
 *
 * @param null|string $result The UNSANITIZED (and potentially unsafe) HTML that should be used to embed. Default null.
 * @param string      $url    The URL that should be inspected for discovery `<link>` tags.
 * @param array       $args   oEmbed remote get arguments.
 * @return null|string The UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
 *                     Null if the URL does not belong to the current site.
 */
function wp_filter_pre_oembed_result( $result, $url, $args ) {
	$data = get_oembed_response_data_for_url( $url, $args );

	if ( $data ) {
		return _wp_oembed_get_object()->data2html( $data, $url );
	}

	return $result;
}
