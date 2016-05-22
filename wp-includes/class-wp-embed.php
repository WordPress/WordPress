<?php
/**
 * API for easily embedding rich media such as videos and images into content.
 *
 * @package WordPress
 * @subpackage Embed
 * @since 2.9.0
 */
class WP_Embed {
	public $handlers = array();
	public $post_ID;
	public $usecache = true;
	public $linkifunknown = true;
	public $last_attr = array();
	public $last_url = '';

	/**
	 * When a URL cannot be embedded, return false instead of returning a link
	 * or the URL. Bypasses the 'embed_maybe_make_link' filter.
	 */
	public $return_false_on_fail = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hack to get the [embed] shortcode to run before wpautop()
		add_filter( 'the_content', array( $this, 'run_shortcode' ), 8 );

		// Shortcode placeholder for strip_shortcodes()
		add_shortcode( 'embed', '__return_false' );

		// Attempts to embed all URLs in a post
		add_filter( 'the_content', array( $this, 'autoembed' ), 8 );

		// After a post is saved, cache oEmbed items via AJAX
		add_action( 'edit_form_advanced', array( $this, 'maybe_run_ajax_cache' ) );
		add_action( 'edit_page_form', array( $this, 'maybe_run_ajax_cache' ) );
	}

	/**
	 * Process the [embed] shortcode.
	 *
	 * Since the [embed] shortcode needs to be run earlier than other shortcodes,
	 * this function removes all existing shortcodes, registers the [embed] shortcode,
	 * calls do_shortcode(), and then re-registers the old shortcodes.
	 *
	 * @global array $shortcode_tags
	 *
	 * @param string $content Content to parse
	 * @return string Content with shortcode parsed
	 */
	public function run_shortcode( $content ) {
		global $shortcode_tags;

		// Back up current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		remove_all_shortcodes();

		add_shortcode( 'embed', array( $this, 'shortcode' ) );

		// Do the shortcode (only the [embed] one is registered)
		$content = do_shortcode( $content, true );

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}

	/**
	 * If a post/page was saved, then output JavaScript to make
	 * an AJAX request that will call WP_Embed::cache_oembed().
	 */
	public function maybe_run_ajax_cache() {
		$post = get_post();

		if ( ! $post || empty( $_GET['message'] ) )
			return;

?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$.get("<?php echo admin_url( 'admin-ajax.php?action=oembed-cache&post=' . $post->ID, 'relative' ); ?>");
	});
</script>
<?php
	}

	/**
	 * Registers an embed handler.
	 *
	 * Do not use this function directly, use wp_embed_register_handler() instead.
	 *
	 * This function should probably also only be used for sites that do not support oEmbed.
	 *
	 * @param string $id An internal ID/name for the handler. Needs to be unique.
	 * @param string $regex The regex that will be used to see if this handler should be used for a URL.
	 * @param callable $callback The callback function that will be called if the regex is matched.
	 * @param int $priority Optional. Used to specify the order in which the registered handlers will be tested (default: 10). Lower numbers correspond with earlier testing, and handlers with the same priority are tested in the order in which they were added to the action.
	 */
	public function register_handler( $id, $regex, $callback, $priority = 10 ) {
		$this->handlers[$priority][$id] = array(
			'regex'    => $regex,
			'callback' => $callback,
		);
	}

	/**
	 * Unregisters a previously-registered embed handler.
	 *
	 * Do not use this function directly, use wp_embed_unregister_handler() instead.
	 *
	 * @param string $id The handler ID that should be removed.
	 * @param int $priority Optional. The priority of the handler to be removed (default: 10).
	 */
	public function unregister_handler( $id, $priority = 10 ) {
		unset( $this->handlers[ $priority ][ $id ] );
	}

	/**
	 * The do_shortcode() callback function.
	 *
	 * Attempts to convert a URL into embed HTML. Starts by checking the URL against the regex of
	 * the registered embed handlers. If none of the regex matches and it's enabled, then the URL
	 * will be given to the WP_oEmbed class.
	 *
	 * @param array $attr {
	 *     Shortcode attributes. Optional.
	 *
	 *     @type int $width  Width of the embed in pixels.
	 *     @type int $height Height of the embed in pixels.
	 * }
	 * @param string $url The URL attempting to be embedded.
	 * @return string|false The embed HTML on success, otherwise the original URL.
	 *                      `->maybe_make_link()` can return false on failure.
	 */
	public function shortcode( $attr, $url = '' ) {
		$post = get_post();

		if ( empty( $url ) && ! empty( $attr['src'] ) ) {
			$url = $attr['src'];
		}

		$this->last_url = $url;

		if ( empty( $url ) ) {
			$this->last_attr = $attr;
			return '';
		}

		$rawattr = $attr;
		$attr = wp_parse_args( $attr, wp_embed_defaults( $url ) );

		$this->last_attr = $attr;

		// kses converts & into &amp; and we need to undo this
		// See https://core.trac.wordpress.org/ticket/11311
		$url = str_replace( '&amp;', '&', $url );

		// Look for known internal handlers
		ksort( $this->handlers );
		foreach ( $this->handlers as $priority => $handlers ) {
			foreach ( $handlers as $id => $handler ) {
				if ( preg_match( $handler['regex'], $url, $matches ) && is_callable( $handler['callback'] ) ) {
					if ( false !== $return = call_user_func( $handler['callback'], $matches, $attr, $url, $rawattr ) )
						/**
						 * Filters the returned embed handler.
						 *
						 * @since 2.9.0
						 *
						 * @see WP_Embed::shortcode()
						 *
						 * @param mixed  $return The shortcode callback function to call.
						 * @param string $url    The attempted embed URL.
						 * @param array  $attr   An array of shortcode attributes.
						 */
						return apply_filters( 'embed_handler_html', $return, $url, $attr );
				}
			}
		}

		$post_ID = ( ! empty( $post->ID ) ) ? $post->ID : null;
		if ( ! empty( $this->post_ID ) ) // Potentially set by WP_Embed::cache_oembed()
			$post_ID = $this->post_ID;

		// Unknown URL format. Let oEmbed have a go.
		if ( $post_ID ) {

			// Check for a cached result (stored in the post meta)
			$key_suffix = md5( $url . serialize( $attr ) );
			$cachekey = '_oembed_' . $key_suffix;
			$cachekey_time = '_oembed_time_' . $key_suffix;

			/**
			 * Filters the oEmbed TTL value (time to live).
			 *
			 * @since 4.0.0
			 *
			 * @param int    $time    Time to live (in seconds).
			 * @param string $url     The attempted embed URL.
			 * @param array  $attr    An array of shortcode attributes.
			 * @param int    $post_ID Post ID.
			 */
			$ttl = apply_filters( 'oembed_ttl', DAY_IN_SECONDS, $url, $attr, $post_ID );

			$cache = get_post_meta( $post_ID, $cachekey, true );
			$cache_time = get_post_meta( $post_ID, $cachekey_time, true );

			if ( ! $cache_time ) {
				$cache_time = 0;
			}

			$cached_recently = ( time() - $cache_time ) < $ttl;

			if ( $this->usecache || $cached_recently ) {
				// Failures are cached. Serve one if we're using the cache.
				if ( '{{unknown}}' === $cache )
					return $this->maybe_make_link( $url );

				if ( ! empty( $cache ) ) {
					/**
					 * Filters the cached oEmbed HTML.
					 *
					 * @since 2.9.0
					 *
					 * @see WP_Embed::shortcode()
					 *
					 * @param mixed  $cache   The cached HTML result, stored in post meta.
					 * @param string $url     The attempted embed URL.
					 * @param array  $attr    An array of shortcode attributes.
					 * @param int    $post_ID Post ID.
					 */
					return apply_filters( 'embed_oembed_html', $cache, $url, $attr, $post_ID );
				}
			}

			/**
			 * Filters whether to inspect the given URL for discoverable link tags.
			 *
			 * @since 2.9.0
			 * @since 4.4.0 The default value changed to true.
			 *
			 * @see WP_oEmbed::discover()
			 *
			 * @param bool $enable Whether to enable `<link>` tag discovery. Default true.
			 */
			$attr['discover'] = ( apply_filters( 'embed_oembed_discover', true ) );

			// Use oEmbed to get the HTML
			$html = wp_oembed_get( $url, $attr );

			// Maybe cache the result
			if ( $html ) {
				update_post_meta( $post_ID, $cachekey, $html );
				update_post_meta( $post_ID, $cachekey_time, time() );
			} elseif ( ! $cache ) {
				update_post_meta( $post_ID, $cachekey, '{{unknown}}' );
			}

			// If there was a result, return it
			if ( $html ) {
				/** This filter is documented in wp-includes/class-wp-embed.php */
				return apply_filters( 'embed_oembed_html', $html, $url, $attr, $post_ID );
			}
		}

		// Still unknown
		return $this->maybe_make_link( $url );
	}

	/**
	 * Delete all oEmbed caches. Unused by core as of 4.0.0.
	 *
	 * @param int $post_ID Post ID to delete the caches for.
	 */
	public function delete_oembed_caches( $post_ID ) {
		$post_metas = get_post_custom_keys( $post_ID );
		if ( empty($post_metas) )
			return;

		foreach ( $post_metas as $post_meta_key ) {
			if ( '_oembed_' == substr( $post_meta_key, 0, 8 ) )
				delete_post_meta( $post_ID, $post_meta_key );
		}
	}

	/**
	 * Triggers a caching of all oEmbed results.
	 *
	 * @param int $post_ID Post ID to do the caching for.
	 */
	public function cache_oembed( $post_ID ) {
		$post = get_post( $post_ID );

		$post_types = get_post_types( array( 'show_ui' => true ) );
		/**
		 * Filters the array of post types to cache oEmbed results for.
		 *
		 * @since 2.9.0
		 *
		 * @param array $post_types Array of post types to cache oEmbed results for. Defaults to post types with `show_ui` set to true.
		 */
		if ( empty( $post->ID ) || ! in_array( $post->post_type, apply_filters( 'embed_cache_oembed_types', $post_types ) ) ){
			return;
		}

		// Trigger a caching
		if ( ! empty( $post->post_content ) ) {
			$this->post_ID = $post->ID;
			$this->usecache = false;

			$content = $this->run_shortcode( $post->post_content );
			$this->autoembed( $content );

			$this->usecache = true;
		}
	}

	/**
	 * Passes any unlinked URLs that are on their own line to WP_Embed::shortcode() for potential embedding.
	 *
	 * @see WP_Embed::autoembed_callback()
	 *
	 * @param string $content The content to be searched.
	 * @return string Potentially modified $content.
	 */
	public function autoembed( $content ) {
		// Replace line breaks from all HTML elements with placeholders.
		$content = wp_replace_in_html_tags( $content, array( "\n" => '<!-- wp-line-break -->' ) );

		// Find URLs that are on their own line.
		$content = preg_replace_callback( '|^(\s*)(https?://[^\s"]+)(\s*)$|im', array( $this, 'autoembed_callback' ), $content );

		// Put the line breaks back.
		return str_replace( '<!-- wp-line-break -->', "\n", $content );
	}

	/**
	 * Callback function for WP_Embed::autoembed().
	 *
	 * @param array $match A regex match array.
	 * @return string The embed HTML on success, otherwise the original URL.
	 */
	public function autoembed_callback( $match ) {
		$oldval = $this->linkifunknown;
		$this->linkifunknown = false;
		$return = $this->shortcode( array(), $match[2] );
		$this->linkifunknown = $oldval;

		return $match[1] . $return . $match[3];
	}

	/**
	 * Conditionally makes a hyperlink based on an internal class variable.
	 *
	 * @param string $url URL to potentially be linked.
	 * @return false|string Linked URL or the original URL. False if 'return_false_on_fail' is true.
	 */
	public function maybe_make_link( $url ) {
		if ( $this->return_false_on_fail ) {
			return false;
		}

		$output = ( $this->linkifunknown ) ? '<a href="' . esc_url($url) . '">' . esc_html($url) . '</a>' : $url;

		/**
		 * Filters the returned, maybe-linked embed URL.
		 *
		 * @since 2.9.0
		 *
		 * @param string $output The linked or original URL.
		 * @param string $url    The original URL.
		 */
		return apply_filters( 'embed_maybe_make_link', $output, $url );
	}
}
$GLOBALS['wp_embed'] = new WP_Embed();
