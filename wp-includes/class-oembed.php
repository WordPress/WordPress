<?php
/**
 * API for fetching the HTML to embed remote content based on a provided URL
 *
 * Used internally by the WP_Embed class, but is designed to be generic.
 *
 * @link https://codex.wordpress.org/oEmbed oEmbed Codex Article
 * @link http://oembed.com/ oEmbed Homepage
 *
 * @package WordPress
 * @subpackage oEmbed
 */

/**
 * Core class used to implement oEmbed functionality.
 *
 * @since 2.9.0
 */
class WP_oEmbed {

	/**
	 * A list of oEmbed providers.
	 *
	 * @since 2.9.0
	 * @access public
	 * @var array
	 */
	public $providers = array();

	/**
	 * A list of an early oEmbed providers.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 * @var array
	 */
	public static $early_providers = array();

	/**
	 * A list of private/protected methods, used for backwards compatibility.
	 *
	 * @since 4.2.0
	 * @access private
	 * @var array
	 */
	private $compat_methods = array( '_fetch_with_format', '_parse_json', '_parse_xml', '_parse_body' );

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 * @access public
	 */
	public function __construct() {
		$host = urlencode( home_url() );
		$providers = array(
			'#http://((m|www)\.)?youtube\.com/watch.*#i'          => array( 'http://www.youtube.com/oembed',                             true  ),
			'#https://((m|www)\.)?youtube\.com/watch.*#i'         => array( 'http://www.youtube.com/oembed?scheme=https',                true  ),
			'#http://((m|www)\.)?youtube\.com/playlist.*#i'       => array( 'http://www.youtube.com/oembed',                             true  ),
			'#https://((m|www)\.)?youtube\.com/playlist.*#i'      => array( 'http://www.youtube.com/oembed?scheme=https',                true  ),
			'#http://youtu\.be/.*#i'                              => array( 'http://www.youtube.com/oembed',                             true  ),
			'#https://youtu\.be/.*#i'                             => array( 'http://www.youtube.com/oembed?scheme=https',                true  ),
			'#https?://(.+\.)?vimeo\.com/.*#i'                    => array( 'http://vimeo.com/api/oembed.{format}',                      true  ),
			'#https?://(www\.)?dailymotion\.com/.*#i'             => array( 'https://www.dailymotion.com/services/oembed',               true  ),
			'#https?://dai.ly/.*#i'                               => array( 'https://www.dailymotion.com/services/oembed',               true  ),
			'#https?://(www\.)?flickr\.com/.*#i'                  => array( 'https://www.flickr.com/services/oembed/',                   true  ),
			'#https?://flic\.kr/.*#i'                             => array( 'https://www.flickr.com/services/oembed/',                   true  ),
			'#https?://(.+\.)?smugmug\.com/.*#i'                  => array( 'http://api.smugmug.com/services/oembed/',                   true  ),
			'#https?://(www\.)?hulu\.com/watch/.*#i'              => array( 'http://www.hulu.com/api/oembed.{format}',                   true  ),
			'http://i*.photobucket.com/albums/*'                  => array( 'http://api.photobucket.com/oembed',                         false ),
			'http://gi*.photobucket.com/groups/*'                 => array( 'http://api.photobucket.com/oembed',                         false ),
			'#https?://(www\.)?scribd\.com/doc/.*#i'              => array( 'http://www.scribd.com/services/oembed',                     true  ),
			'#https?://wordpress.tv/.*#i'                         => array( 'http://wordpress.tv/oembed/',                               true  ),
			'#https?://(.+\.)?polldaddy\.com/.*#i'                => array( 'https://polldaddy.com/oembed/',                             true  ),
			'#https?://poll\.fm/.*#i'                             => array( 'https://polldaddy.com/oembed/',                             true  ),
			'#https?://(www\.)?funnyordie\.com/videos/.*#i'       => array( 'http://www.funnyordie.com/oembed',                          true  ),
			'#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i' => array( 'https://publish.twitter.com/oembed',                        true  ),
			'#https?://(www\.)?twitter\.com/.+?/timelines/.*#i'   => array( 'https://publish.twitter.com/oembed',                        true  ),
			'#https?://(www\.)?twitter\.com/i/moments/.*#i'       => array( 'https://publish.twitter.com/oembed',                        true  ),
			'#https?://vine.co/v/.*#i'                            => array( 'https://vine.co/oembed.{format}',                           true  ),
			'#https?://(www\.)?soundcloud\.com/.*#i'              => array( 'http://soundcloud.com/oembed',                              true  ),
			'#https?://(.+?\.)?slideshare\.net/.*#i'              => array( 'https://www.slideshare.net/api/oembed/2',                   true  ),
			'#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'      => array( 'https://api.instagram.com/oembed',                          true  ),
			'#https?://(open|play)\.spotify\.com/.*#i'            => array( 'https://embed.spotify.com/oembed/',                         true  ),
			'#https?://(.+\.)?imgur\.com/.*#i'                    => array( 'http://api.imgur.com/oembed',                               true  ),
			'#https?://(www\.)?meetu(\.ps|p\.com)/.*#i'           => array( 'http://api.meetup.com/oembed',                              true  ),
			'#https?://(www\.)?issuu\.com/.+/docs/.+#i'           => array( 'http://issuu.com/oembed_wp',                                true  ),
			'#https?://(www\.)?collegehumor\.com/video/.*#i'      => array( 'http://www.collegehumor.com/oembed.{format}',               true  ),
			'#https?://(www\.)?mixcloud\.com/.*#i'                => array( 'http://www.mixcloud.com/oembed',                            true  ),
			'#https?://(www\.|embed\.)?ted\.com/talks/.*#i'       => array( 'http://www.ted.com/talks/oembed.{format}',                  true  ),
			'#https?://(www\.)?(animoto|video214)\.com/play/.*#i' => array( 'https://animoto.com/oembeds/create',                        true  ),
			'#https?://(.+)\.tumblr\.com/post/.*#i'               => array( 'https://www.tumblr.com/oembed/1.0',                         true  ),
			'#https?://(www\.)?kickstarter\.com/projects/.*#i'    => array( 'https://www.kickstarter.com/services/oembed',               true  ),
			'#https?://kck\.st/.*#i'                              => array( 'https://www.kickstarter.com/services/oembed',               true  ),
			'#https?://cloudup\.com/.*#i'                         => array( 'https://cloudup.com/oembed',                                true  ),
			'#https?://(www\.)?reverbnation\.com/.*#i'            => array( 'https://www.reverbnation.com/oembed',                       true  ),
			'#https?://videopress.com/v/.*#'                      => array( 'https://public-api.wordpress.com/oembed/1.0/?for=' . $host, true  ),
			'#https?://(www\.)?reddit\.com/r/[^/]+/comments/.*#i' => array( 'https://www.reddit.com/oembed',                             true  ),
			'#https?://(www\.)?speakerdeck\.com/.*#i'             => array( 'https://speakerdeck.com/oembed.{format}',                   true  ),
		);

		if ( ! empty( self::$early_providers['add'] ) ) {
			foreach ( self::$early_providers['add'] as $format => $data ) {
				$providers[ $format ] = $data;
			}
		}

		if ( ! empty( self::$early_providers['remove'] ) ) {
			foreach ( self::$early_providers['remove'] as $format ) {
				unset( $providers[ $format ] );
			}
		}

		self::$early_providers = array();

		/**
		 * Filter the list of whitelisted oEmbed providers.
		 *
		 * Since WordPress 4.4, oEmbed discovery is enabled for all users and allows embedding of sanitized
		 * iframes. The providers in this list are whitelisted, meaning they are trusted and allowed to
		 * embed any content, such as iframes, videos, JavaScript, and arbitrary HTML.
		 *
		 * Supported providers:
		 *
		 * |   Provider   |        Flavor         | Supports HTTPS |   Since   |
		 * | ------------ | --------------------- | :------------: | --------- |
		 * | Dailymotion  | dailymotion.com       |      Yes       | 2.9.0     |
		 * | Flickr       | flickr.com            |      Yes       | 2.9.0     |
		 * | Hulu         | hulu.com              |      Yes       | 2.9.0     |
		 * | Photobucket  | photobucket.com       |      No        | 2.9.0     |
		 * | Scribd       | scribd.com            |      Yes       | 2.9.0     |
		 * | Vimeo        | vimeo.com             |      Yes       | 2.9.0     |
		 * | WordPress.tv | wordpress.tv          |      Yes       | 2.9.0     |
		 * | YouTube      | youtube.com/watch     |      Yes       | 2.9.0     |
		 * | Funny or Die | funnyordie.com        |      Yes       | 3.0.0     |
		 * | Polldaddy    | polldaddy.com         |      Yes       | 3.0.0     |
		 * | SmugMug      | smugmug.com           |      Yes       | 3.0.0     |
		 * | YouTube      | youtu.be              |      Yes       | 3.0.0     |
		 * | Twitter      | twitter.com           |      Yes       | 3.4.0     |
		 * | Instagram    | instagram.com         |      Yes       | 3.5.0     |
		 * | Instagram    | instagr.am            |      Yes       | 3.5.0     |
		 * | Slideshare   | slideshare.net        |      Yes       | 3.5.0     |
		 * | SoundCloud   | soundcloud.com        |      Yes       | 3.5.0     |
		 * | Dailymotion  | dai.ly                |      Yes       | 3.6.0     |
		 * | Flickr       | flic.kr               |      Yes       | 3.6.0     |
		 * | Spotify      | spotify.com           |      Yes       | 3.6.0     |
		 * | Imgur        | imgur.com             |      Yes       | 3.9.0     |
		 * | Meetup.com   | meetup.com            |      Yes       | 3.9.0     |
		 * | Meetup.com   | meetu.ps              |      Yes       | 3.9.0     |
		 * | Animoto      | animoto.com           |      Yes       | 4.0.0     |
		 * | Animoto      | video214.com          |      Yes       | 4.0.0     |
		 * | CollegeHumor | collegehumor.com      |      Yes       | 4.0.0     |
		 * | Issuu        | issuu.com             |      Yes       | 4.0.0     |
		 * | Mixcloud     | mixcloud.com          |      Yes       | 4.0.0     |
		 * | Polldaddy    | poll.fm               |      Yes       | 4.0.0     |
		 * | TED          | ted.com               |      Yes       | 4.0.0     |
		 * | YouTube      | youtube.com/playlist  |      Yes       | 4.0.0     |
		 * | Vine         | vine.co               |      Yes       | 4.1.0     |
		 * | Tumblr       | tumblr.com            |      Yes       | 4.2.0     |
		 * | Kickstarter  | kickstarter.com       |      Yes       | 4.2.0     |
		 * | Kickstarter  | kck.st                |      Yes       | 4.2.0     |
		 * | Cloudup      | cloudup.com           |      Yes       | 4.4.0     |
		 * | ReverbNation | reverbnation.com      |      Yes       | 4.4.0     |
		 * | VideoPress   | videopress.com        |      Yes       | 4.4.0     |
		 * | Reddit       | reddit.com            |      Yes       | 4.4.0     |
		 * | Speaker Deck | speakerdeck.com       |      Yes       | 4.4.0     |
		 * | Twitter      | twitter.com/timelines |      Yes       | 4.5.0     |
		 * | Twitter      | twitter.com/moments   |      Yes       | 4.5.0     |
		 *
		 * No longer supported providers:
		 *
		 * |   Provider   |        Flavor        | Supports HTTPS |   Since   |  Removed  |
		 * | ------------ | -------------------- | :------------: | --------- | --------- |
		 * | Qik          | qik.com              |      Yes       | 2.9.0     | 3.9.0     |
		 * | Viddler      | viddler.com          |      Yes       | 2.9.0     | 4.0.0     |
		 * | Revision3    | revision3.com        |      No        | 2.9.0     | 4.2.0     |
		 * | Blip         | blip.tv              |      No        | 2.9.0     | 4.4.0     |
		 * | Rdio         | rdio.com             |      Yes       | 3.6.0     | 4.4.1     |
		 * | Rdio         | rd.io                |      Yes       | 3.6.0     | 4.4.1     |
		 *
		 * @see wp_oembed_add_provider()
		 *
		 * @since 2.9.0
		 *
		 * @param array $providers An array of popular oEmbed providers.
		 */
		$this->providers = apply_filters( 'oembed_providers', $providers );

		// Fix any embeds that contain new lines in the middle of the HTML which breaks wpautop().
		add_filter( 'oembed_dataparse', array($this, '_strip_newlines'), 10, 3 );
	}

	/**
	 * Exposes private/protected methods for backwards compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param callable $name      Method to call.
	 * @param array    $arguments Arguments to pass when calling.
	 * @return mixed|bool Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, $this->compat_methods ) ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		}
		return false;
	}

	/**
	 * Takes a URL and returns the corresponding oEmbed provider's URL, if there is one.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @see WP_oEmbed::discover()
	 *
	 * @param string        $url  The URL to the content.
	 * @param string|array  $args Optional provider arguments.
	 * @return false|string False on failure, otherwise the oEmbed provider URL.
	 */
	public function get_provider( $url, $args = '' ) {

		$provider = false;

		if ( !isset($args['discover']) )
			$args['discover'] = true;

		foreach ( $this->providers as $matchmask => $data ) {
			list( $providerurl, $regex ) = $data;

			// Turn the asterisk-type provider URLs into regex
			if ( !$regex ) {
				$matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $matchmask ), '#' ) ) . '#i';
				$matchmask = preg_replace( '|^#http\\\://|', '#https?\://', $matchmask );
			}

			if ( preg_match( $matchmask, $url ) ) {
				$provider = str_replace( '{format}', 'json', $providerurl ); // JSON is easier to deal with than XML
				break;
			}
		}

		if ( !$provider && $args['discover'] )
			$provider = $this->discover( $url );

		return $provider;
	}

	/**
	 * Adds an oEmbed provider.
	 *
	 * The provider is removed just-in-time when wp_oembed_add_provider() is called before
	 * the {@see 'plugins_loaded'} hook.
	 *
	 * The just-in-time addition is for the benefit of the {@see 'oembed_providers'} filter.
	 *
	 * @static
	 * @since 4.0.0
	 * @access public
	 *
	 * @see wp_oembed_add_provider()
	 *
	 * @param string $format   Format of URL that this provider can handle. You can use
	 *                         asterisks as wildcards.
	 * @param string $provider The URL to the oEmbed provider..
	 * @param bool   $regex    Optional. Whether the $format parameter is in a regex format.
	 *                         Default false.
	 */
	public static function _add_provider_early( $format, $provider, $regex = false ) {
		if ( empty( self::$early_providers['add'] ) ) {
			self::$early_providers['add'] = array();
		}

		self::$early_providers['add'][ $format ] = array( $provider, $regex );
	}

	/**
	 * Removes an oEmbed provider.
	 *
	 * The provider is removed just-in-time when wp_oembed_remove_provider() is called before
	 * the {@see 'plugins_loaded'} hook.
	 *
	 * The just-in-time removal is for the benefit of the {@see 'oembed_providers'} filter.
	 *
	 * @since 4.0.0
	 * @access public
	 * @static
	 *
	 * @see wp_oembed_remove_provider()
	 *
	 * @param string $format The format of URL that this provider can handle. You can use
	 *                       asterisks as wildcards.
	 */
	public static function _remove_provider_early( $format ) {
		if ( empty( self::$early_providers['remove'] ) ) {
			self::$early_providers['remove'] = array();
		}

		self::$early_providers['remove'][] = $format;
	}

	/**
	 * The do-it-all function that takes a URL and attempts to return the HTML.
	 *
	 * @see WP_oEmbed::fetch()
	 * @see WP_oEmbed::data2html()
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param string       $url  The URL to the content that should be attempted to be embedded.
	 * @param array|string $args Optional. Arguments, usually passed from a shortcode. Default empty.
	 * @return false|string False on failure, otherwise the UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
	 */
	public function get_html( $url, $args = '' ) {
		$provider = $this->get_provider( $url, $args );

		if ( !$provider || false === $data = $this->fetch( $provider, $url, $args ) )
			return false;

		/**
		 * Filter the HTML returned by the oEmbed provider.
		 *
		 * @since 2.9.0
		 *
		 * @param string $data The returned oEmbed HTML.
		 * @param string $url  URL of the content to be embedded.
		 * @param array  $args Optional arguments, usually passed from a shortcode.
		 */
		return apply_filters( 'oembed_result', $this->data2html( $data, $url ), $url, $args );
	}

	/**
	 * Attempts to discover link tags at the given URL for an oEmbed provider.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param string $url The URL that should be inspected for discovery `<link>` tags.
	 * @return false|string False on failure, otherwise the oEmbed provider URL.
	 */
	public function discover( $url ) {
		$providers = array();
		$args = array(
			'limit_response_size' => 153600, // 150 KB
		);

		/**
		 * Filter oEmbed remote get arguments.
		 *
		 * @since 4.0.0
		 *
		 * @see WP_Http::request()
		 *
		 * @param array  $args oEmbed remote get arguments.
		 * @param string $url  URL to be inspected.
		 */
		$args = apply_filters( 'oembed_remote_get_args', $args, $url );

		// Fetch URL content
		$request = wp_safe_remote_get( $url, $args );
		if ( $html = wp_remote_retrieve_body( $request ) ) {

			/**
			 * Filter the link types that contain oEmbed provider URLs.
			 *
			 * @since 2.9.0
			 *
			 * @param array $format Array of oEmbed link types. Accepts 'application/json+oembed',
			 *                      'text/xml+oembed', and 'application/xml+oembed' (incorrect,
			 *                      used by at least Vimeo).
			 */
			$linktypes = apply_filters( 'oembed_linktypes', array(
				'application/json+oembed' => 'json',
				'text/xml+oembed' => 'xml',
				'application/xml+oembed' => 'xml',
			) );

			// Strip <body>
			if ( $html_head_end = stripos( $html, '</head>' ) ) {
				$html = substr( $html, 0, $html_head_end );
			}

			// Do a quick check
			$tagfound = false;
			foreach ( $linktypes as $linktype => $format ) {
				if ( stripos($html, $linktype) ) {
					$tagfound = true;
					break;
				}
			}

			if ( $tagfound && preg_match_all( '#<link([^<>]+)/?>#iU', $html, $links ) ) {
				foreach ( $links[1] as $link ) {
					$atts = shortcode_parse_atts( $link );

					if ( !empty($atts['type']) && !empty($linktypes[$atts['type']]) && !empty($atts['href']) ) {
						$providers[$linktypes[$atts['type']]] = htmlspecialchars_decode( $atts['href'] );

						// Stop here if it's JSON (that's all we need)
						if ( 'json' == $linktypes[$atts['type']] )
							break;
					}
				}
			}
		}

		// JSON is preferred to XML
		if ( !empty($providers['json']) )
			return $providers['json'];
		elseif ( !empty($providers['xml']) )
			return $providers['xml'];
		else
			return false;
	}

	/**
	 * Connects to a oEmbed provider and returns the result.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param string       $provider The URL to the oEmbed provider.
	 * @param string       $url      The URL to the content that is desired to be embedded.
	 * @param array|string $args     Optional. Arguments, usually passed from a shortcode. Default empty.
	 * @return false|object False on failure, otherwise the result in the form of an object.
	 */
	public function fetch( $provider, $url, $args = '' ) {
		$args = wp_parse_args( $args, wp_embed_defaults( $url ) );

		$provider = add_query_arg( 'maxwidth', (int) $args['width'], $provider );
		$provider = add_query_arg( 'maxheight', (int) $args['height'], $provider );
		$provider = add_query_arg( 'url', urlencode($url), $provider );

		/**
		 * Filter the oEmbed URL to be fetched.
		 *
		 * @since 2.9.0
		 *
		 * @param string $provider URL of the oEmbed provider.
		 * @param string $url      URL of the content to be embedded.
		 * @param array  $args     Optional arguments, usually passed from a shortcode.
		 */
		$provider = apply_filters( 'oembed_fetch_url', $provider, $url, $args );

		foreach ( array( 'json', 'xml' ) as $format ) {
			$result = $this->_fetch_with_format( $provider, $format );
			if ( is_wp_error( $result ) && 'not-implemented' == $result->get_error_code() )
				continue;
			return ( $result && ! is_wp_error( $result ) ) ? $result : false;
		}
		return false;
	}

	/**
	 * Fetches result from an oEmbed provider for a specific format and complete provider URL
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $provider_url_with_args URL to the provider with full arguments list (url, maxheight, etc.)
	 * @param string $format Format to use
	 * @return false|object|WP_Error False on failure, otherwise the result in the form of an object.
	 */
	private function _fetch_with_format( $provider_url_with_args, $format ) {
		$provider_url_with_args = add_query_arg( 'format', $format, $provider_url_with_args );

		/** This filter is documented in wp-includes/class-oembed.php */
		$args = apply_filters( 'oembed_remote_get_args', array(), $provider_url_with_args );

		$response = wp_safe_remote_get( $provider_url_with_args, $args );
		if ( 501 == wp_remote_retrieve_response_code( $response ) )
			return new WP_Error( 'not-implemented' );
		if ( ! $body = wp_remote_retrieve_body( $response ) )
			return false;
		$parse_method = "_parse_$format";
		return $this->$parse_method( $body );
	}

	/**
	 * Parses a json response body.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $response_body
	 * @return object|false
	 */
	private function _parse_json( $response_body ) {
		$data = json_decode( trim( $response_body ) );
		return ( $data && is_object( $data ) ) ? $data : false;
	}

	/**
	 * Parses an XML response body.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param string $response_body
	 * @return object|false
	 */
	private function _parse_xml( $response_body ) {
		if ( ! function_exists( 'libxml_disable_entity_loader' ) )
			return false;

		$loader = libxml_disable_entity_loader( true );
		$errors = libxml_use_internal_errors( true );

		$return = $this->_parse_xml_body( $response_body );

		libxml_use_internal_errors( $errors );
		libxml_disable_entity_loader( $loader );

		return $return;
	}

	/**
	 * Serves as a helper function for parsing an XML response body.
	 *
	 * @since 3.6.0
	 * @access private
	 *
	 * @param string $response_body
	 * @return object|false
	 */
	private function _parse_xml_body( $response_body ) {
		if ( ! function_exists( 'simplexml_import_dom' ) || ! class_exists( 'DOMDocument', false ) )
			return false;

		$dom = new DOMDocument;
		$success = $dom->loadXML( $response_body );
		if ( ! $success )
			return false;

		if ( isset( $dom->doctype ) )
			return false;

		foreach ( $dom->childNodes as $child ) {
			if ( XML_DOCUMENT_TYPE_NODE === $child->nodeType )
				return false;
		}

		$xml = simplexml_import_dom( $dom );
		if ( ! $xml )
			return false;

		$return = new stdClass;
		foreach ( $xml as $key => $value ) {
			$return->$key = (string) $value;
		}

		return $return;
	}

	/**
	 * Converts a data object from WP_oEmbed::fetch() and returns the HTML.
	 *
	 * @since 2.9.0
	 * @access public
	 *
	 * @param object $data A data object result from an oEmbed provider.
	 * @param string $url The URL to the content that is desired to be embedded.
	 * @return false|string False on error, otherwise the HTML needed to embed.
	 */
	public function data2html( $data, $url ) {
		if ( ! is_object( $data ) || empty( $data->type ) )
			return false;

		$return = false;

		switch ( $data->type ) {
			case 'photo':
				if ( empty( $data->url ) || empty( $data->width ) || empty( $data->height ) )
					break;
				if ( ! is_string( $data->url ) || ! is_numeric( $data->width ) || ! is_numeric( $data->height ) )
					break;

				$title = ! empty( $data->title ) && is_string( $data->title ) ? $data->title : '';
				$return = '<a href="' . esc_url( $url ) . '"><img src="' . esc_url( $data->url ) . '" alt="' . esc_attr($title) . '" width="' . esc_attr($data->width) . '" height="' . esc_attr($data->height) . '" /></a>';
				break;

			case 'video':
			case 'rich':
				if ( ! empty( $data->html ) && is_string( $data->html ) )
					$return = $data->html;
				break;

			case 'link':
				if ( ! empty( $data->title ) && is_string( $data->title ) )
					$return = '<a href="' . esc_url( $url ) . '">' . esc_html( $data->title ) . '</a>';
				break;

			default:
				$return = false;
		}

		/**
		 * Filter the returned oEmbed HTML.
		 *
		 * Use this filter to add support for custom data types, or to filter the result.
		 *
		 * @since 2.9.0
		 *
		 * @param string $return The returned oEmbed HTML.
		 * @param object $data   A data object result from an oEmbed provider.
		 * @param string $url    The URL of the content to be embedded.
		 */
		return apply_filters( 'oembed_dataparse', $return, $data, $url );
	}

	/**
	 * Strips any new lines from the HTML.
	 *
	 * @since 2.9.0 as strip_scribd_newlines()
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $html Existing HTML.
	 * @param object $data Data object from WP_oEmbed::data2html()
	 * @param string $url The original URL passed to oEmbed.
	 * @return string Possibly modified $html
	 */
	public function _strip_newlines( $html, $data, $url ) {
		if ( false === strpos( $html, "\n" ) ) {
			return $html;
		}

		$count = 1;
		$found = array();
		$token = '__PRE__';
		$search = array( "\t", "\n", "\r", ' ' );
		$replace = array( '__TAB__', '__NL__', '__CR__', '__SPACE__' );
		$tokenized = str_replace( $search, $replace, $html );

		preg_match_all( '#(<pre[^>]*>.+?</pre>)#i', $tokenized, $matches, PREG_SET_ORDER );
		foreach ( $matches as $i => $match ) {
			$tag_html = str_replace( $replace, $search, $match[0] );
			$tag_token = $token . $i;

			$found[ $tag_token ] = $tag_html;
			$html = str_replace( $tag_html, $tag_token, $html, $count );
		}

		$replaced = str_replace( $replace, $search, $html );
		$stripped = str_replace( array( "\r\n", "\n" ), '', $replaced );
		$pre = array_values( $found );
		$tokens = array_keys( $found );

		return str_replace( $tokens, $pre, $stripped );
	}
}

/**
 * Returns the initialized WP_oEmbed object.
 *
 * @since 2.9.0
 * @access private
 *
 * @staticvar WP_oEmbed $wp_oembed
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
