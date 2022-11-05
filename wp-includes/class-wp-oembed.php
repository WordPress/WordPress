<?php
/**
 * API for fetching the HTML to embed remote content based on a provided URL
 *
 * Used internally by the WP_Embed class, but is designed to be generic.
 *
 * @link https://wordpress.org/support/article/embeds/
 * @link http://oembed.com/
 *
 * @package WordPress
 * @subpackage oEmbed
 */

/**
 * Core class used to implement oEmbed functionality.
 *
 * @since 2.9.0
 */
#[AllowDynamicProperties]
class WP_oEmbed {

	/**
	 * A list of oEmbed providers.
	 *
	 * @since 2.9.0
	 * @var array
	 */
	public $providers = array();

	/**
	 * A list of an early oEmbed providers.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	public static $early_providers = array();

	/**
	 * A list of private/protected methods, used for backward compatibility.
	 *
	 * @since 4.2.0
	 * @var array
	 */
	private $compat_methods = array( '_fetch_with_format', '_parse_json', '_parse_xml', '_parse_xml_body' );

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		$host      = urlencode( home_url() );
		$providers = array(
			'#https?://((m|www)\.)?youtube\.com/watch.*#i' => array( 'https://www.youtube.com/oembed', true ),
			'#https?://((m|www)\.)?youtube\.com/playlist.*#i' => array( 'https://www.youtube.com/oembed', true ),
			'#https?://((m|www)\.)?youtube\.com/shorts/*#i' => array( 'https://www.youtube.com/oembed', true ),
			'#https?://youtu\.be/.*#i'                     => array( 'https://www.youtube.com/oembed', true ),
			'#https?://(.+\.)?vimeo\.com/.*#i'             => array( 'https://vimeo.com/api/oembed.{format}', true ),
			'#https?://(www\.)?dailymotion\.com/.*#i'      => array( 'https://www.dailymotion.com/services/oembed', true ),
			'#https?://dai\.ly/.*#i'                       => array( 'https://www.dailymotion.com/services/oembed', true ),
			'#https?://(www\.)?flickr\.com/.*#i'           => array( 'https://www.flickr.com/services/oembed/', true ),
			'#https?://flic\.kr/.*#i'                      => array( 'https://www.flickr.com/services/oembed/', true ),
			'#https?://(.+\.)?smugmug\.com/.*#i'           => array( 'https://api.smugmug.com/services/oembed/', true ),
			'#https?://(www\.)?scribd\.com/(doc|document)/.*#i' => array( 'https://www.scribd.com/services/oembed', true ),
			'#https?://wordpress\.tv/.*#i'                 => array( 'https://wordpress.tv/oembed/', true ),
			'#https?://(.+\.)?polldaddy\.com/.*#i'         => array( 'https://api.crowdsignal.com/oembed', true ),
			'#https?://poll\.fm/.*#i'                      => array( 'https://api.crowdsignal.com/oembed', true ),
			'#https?://(.+\.)?survey\.fm/.*#i'             => array( 'https://api.crowdsignal.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/\w{1,15}/status(es)?/.*#i' => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/\w{1,15}$#i'   => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/\w{1,15}/likes$#i' => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/\w{1,15}/lists/.*#i' => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/\w{1,15}/timelines/.*#i' => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?twitter\.com/i/moments/.*#i' => array( 'https://publish.twitter.com/oembed', true ),
			'#https?://(www\.)?soundcloud\.com/.*#i'       => array( 'https://soundcloud.com/oembed', true ),
			'#https?://(.+?\.)?slideshare\.net/.*#i'       => array( 'https://www.slideshare.net/api/oembed/2', true ),
			'#https?://(open|play)\.spotify\.com/.*#i'     => array( 'https://embed.spotify.com/oembed/', true ),
			'#https?://(.+\.)?imgur\.com/.*#i'             => array( 'https://api.imgur.com/oembed', true ),
			'#https?://(www\.)?issuu\.com/.+/docs/.+#i'    => array( 'https://issuu.com/oembed_wp', true ),
			'#https?://(www\.)?mixcloud\.com/.*#i'         => array( 'https://www.mixcloud.com/oembed', true ),
			'#https?://(www\.|embed\.)?ted\.com/talks/.*#i' => array( 'https://www.ted.com/services/v1/oembed.{format}', true ),
			'#https?://(www\.)?(animoto|video214)\.com/play/.*#i' => array( 'https://animoto.com/oembeds/create', true ),
			'#https?://(.+)\.tumblr\.com/.*#i'             => array( 'https://www.tumblr.com/oembed/1.0', true ),
			'#https?://(www\.)?kickstarter\.com/projects/.*#i' => array( 'https://www.kickstarter.com/services/oembed', true ),
			'#https?://kck\.st/.*#i'                       => array( 'https://www.kickstarter.com/services/oembed', true ),
			'#https?://cloudup\.com/.*#i'                  => array( 'https://cloudup.com/oembed', true ),
			'#https?://(www\.)?reverbnation\.com/.*#i'     => array( 'https://www.reverbnation.com/oembed', true ),
			'#https?://videopress\.com/v/.*#'              => array( 'https://public-api.wordpress.com/oembed/?for=' . $host, true ),
			'#https?://(www\.)?reddit\.com/r/[^/]+/comments/.*#i' => array( 'https://www.reddit.com/oembed', true ),
			'#https?://(www\.)?speakerdeck\.com/.*#i'      => array( 'https://speakerdeck.com/oembed.{format}', true ),
			'#https?://(www\.)?screencast\.com/.*#i'       => array( 'https://api.screencast.com/external/oembed', true ),
			'#https?://([a-z0-9-]+\.)?amazon\.(com|com\.mx|com\.br|ca)/.*#i' => array( 'https://read.amazon.com/kp/api/oembed', true ),
			'#https?://([a-z0-9-]+\.)?amazon\.(co\.uk|de|fr|it|es|in|nl|ru)/.*#i' => array( 'https://read.amazon.co.uk/kp/api/oembed', true ),
			'#https?://([a-z0-9-]+\.)?amazon\.(co\.jp|com\.au)/.*#i' => array( 'https://read.amazon.com.au/kp/api/oembed', true ),
			'#https?://([a-z0-9-]+\.)?amazon\.cn/.*#i'     => array( 'https://read.amazon.cn/kp/api/oembed', true ),
			'#https?://(www\.)?a\.co/.*#i'                 => array( 'https://read.amazon.com/kp/api/oembed', true ),
			'#https?://(www\.)?amzn\.to/.*#i'              => array( 'https://read.amazon.com/kp/api/oembed', true ),
			'#https?://(www\.)?amzn\.eu/.*#i'              => array( 'https://read.amazon.co.uk/kp/api/oembed', true ),
			'#https?://(www\.)?amzn\.in/.*#i'              => array( 'https://read.amazon.in/kp/api/oembed', true ),
			'#https?://(www\.)?amzn\.asia/.*#i'            => array( 'https://read.amazon.com.au/kp/api/oembed', true ),
			'#https?://(www\.)?z\.cn/.*#i'                 => array( 'https://read.amazon.cn/kp/api/oembed', true ),
			'#https?://www\.someecards\.com/.+-cards/.+#i' => array( 'https://www.someecards.com/v2/oembed/', true ),
			'#https?://www\.someecards\.com/usercards/viewcard/.+#i' => array( 'https://www.someecards.com/v2/oembed/', true ),
			'#https?://some\.ly\/.+#i'                     => array( 'https://www.someecards.com/v2/oembed/', true ),
			'#https?://(www\.)?tiktok\.com/.*/video/.*#i'  => array( 'https://www.tiktok.com/oembed', true ),
			'#https?://([a-z]{2}|www)\.pinterest\.com(\.(au|mx))?/.*#i' => array( 'https://www.pinterest.com/oembed.json', true ),
			'#https?://(www\.)?wolframcloud\.com/obj/.+#i' => array( 'https://www.wolframcloud.com/oembed', true ),
			'#https?://pca\.st/.+#i'                       => array( 'https://pca.st/oembed.json', true ),
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
		 * Filters the list of sanctioned oEmbed providers.
		 *
		 * Since WordPress 4.4, oEmbed discovery is enabled for all users and allows embedding of sanitized
		 * iframes. The providers in this list are sanctioned, meaning they are trusted and allowed to
		 * embed any content, such as iframes, videos, JavaScript, and arbitrary HTML.
		 *
		 * Supported providers:
		 *
		 * |   Provider   |                     Flavor                |  Since  |
		 * | ------------ | ----------------------------------------- | ------- |
		 * | Dailymotion  | dailymotion.com                           | 2.9.0   |
		 * | Flickr       | flickr.com                                | 2.9.0   |
		 * | Scribd       | scribd.com                                | 2.9.0   |
		 * | Vimeo        | vimeo.com                                 | 2.9.0   |
		 * | WordPress.tv | wordpress.tv                              | 2.9.0   |
		 * | YouTube      | youtube.com/watch                         | 2.9.0   |
		 * | Crowdsignal  | polldaddy.com                             | 3.0.0   |
		 * | SmugMug      | smugmug.com                               | 3.0.0   |
		 * | YouTube      | youtu.be                                  | 3.0.0   |
		 * | Twitter      | twitter.com                               | 3.4.0   |
		 * | Slideshare   | slideshare.net                            | 3.5.0   |
		 * | SoundCloud   | soundcloud.com                            | 3.5.0   |
		 * | Dailymotion  | dai.ly                                    | 3.6.0   |
		 * | Flickr       | flic.kr                                   | 3.6.0   |
		 * | Spotify      | spotify.com                               | 3.6.0   |
		 * | Imgur        | imgur.com                                 | 3.9.0   |
		 * | Animoto      | animoto.com                               | 4.0.0   |
		 * | Animoto      | video214.com                              | 4.0.0   |
		 * | Issuu        | issuu.com                                 | 4.0.0   |
		 * | Mixcloud     | mixcloud.com                              | 4.0.0   |
		 * | Crowdsignal  | poll.fm                                   | 4.0.0   |
		 * | TED          | ted.com                                   | 4.0.0   |
		 * | YouTube      | youtube.com/playlist                      | 4.0.0   |
		 * | Tumblr       | tumblr.com                                | 4.2.0   |
		 * | Kickstarter  | kickstarter.com                           | 4.2.0   |
		 * | Kickstarter  | kck.st                                    | 4.2.0   |
		 * | Cloudup      | cloudup.com                               | 4.3.0   |
		 * | ReverbNation | reverbnation.com                          | 4.4.0   |
		 * | VideoPress   | videopress.com                            | 4.4.0   |
		 * | Reddit       | reddit.com                                | 4.4.0   |
		 * | Speaker Deck | speakerdeck.com                           | 4.4.0   |
		 * | Twitter      | twitter.com/timelines                     | 4.5.0   |
		 * | Twitter      | twitter.com/moments                       | 4.5.0   |
		 * | Twitter      | twitter.com/user                          | 4.7.0   |
		 * | Twitter      | twitter.com/likes                         | 4.7.0   |
		 * | Twitter      | twitter.com/lists                         | 4.7.0   |
		 * | Screencast   | screencast.com                            | 4.8.0   |
		 * | Amazon       | amazon.com (com.mx, com.br, ca)           | 4.9.0   |
		 * | Amazon       | amazon.de (fr, it, es, in, nl, ru, co.uk) | 4.9.0   |
		 * | Amazon       | amazon.co.jp (com.au)                     | 4.9.0   |
		 * | Amazon       | amazon.cn                                 | 4.9.0   |
		 * | Amazon       | a.co                                      | 4.9.0   |
		 * | Amazon       | amzn.to (eu, in, asia)                    | 4.9.0   |
		 * | Amazon       | z.cn                                      | 4.9.0   |
		 * | Someecards   | someecards.com                            | 4.9.0   |
		 * | Someecards   | some.ly                                   | 4.9.0   |
		 * | Crowdsignal  | survey.fm                                 | 5.1.0   |
		 * | TikTok       | tiktok.com                                | 5.4.0   |
		 * | Pinterest    | pinterest.com                             | 5.9.0   |
		 * | WolframCloud | wolframcloud.com                          | 5.9.0   |
		 * | Pocket Casts | pocketcasts.com                           | 6.1.0   |
		 *
		 * No longer supported providers:
		 *
		 * |   Provider   |        Flavor        |   Since   |  Removed  |
		 * | ------------ | -------------------- | --------- | --------- |
		 * | Qik          | qik.com              | 2.9.0     | 3.9.0     |
		 * | Viddler      | viddler.com          | 2.9.0     | 4.0.0     |
		 * | Revision3    | revision3.com        | 2.9.0     | 4.2.0     |
		 * | Blip         | blip.tv              | 2.9.0     | 4.4.0     |
		 * | Rdio         | rdio.com             | 3.6.0     | 4.4.1     |
		 * | Rdio         | rd.io                | 3.6.0     | 4.4.1     |
		 * | Vine         | vine.co              | 4.1.0     | 4.9.0     |
		 * | Photobucket  | photobucket.com      | 2.9.0     | 5.1.0     |
		 * | Funny or Die | funnyordie.com       | 3.0.0     | 5.1.0     |
		 * | CollegeHumor | collegehumor.com     | 4.0.0     | 5.3.1     |
		 * | Hulu         | hulu.com             | 2.9.0     | 5.5.0     |
		 * | Instagram    | instagram.com        | 3.5.0     | 5.5.2     |
		 * | Instagram    | instagr.am           | 3.5.0     | 5.5.2     |
		 * | Instagram TV | instagram.com        | 5.1.0     | 5.5.2     |
		 * | Instagram TV | instagr.am           | 5.1.0     | 5.5.2     |
		 * | Facebook     | facebook.com         | 4.7.0     | 5.5.2     |
		 * | Meetup.com   | meetup.com           | 3.9.0     | 6.0.1     |
		 * | Meetup.com   | meetu.ps             | 3.9.0     | 6.0.1     |
		 *
		 * @see wp_oembed_add_provider()
		 *
		 * @since 2.9.0
		 *
		 * @param array[] $providers An array of arrays containing data about popular oEmbed providers.
		 */
		$this->providers = apply_filters( 'oembed_providers', $providers );

		// Fix any embeds that contain new lines in the middle of the HTML which breaks wpautop().
		add_filter( 'oembed_dataparse', array( $this, '_strip_newlines' ), 10, 3 );
	}

	/**
	 * Exposes private/protected methods for backward compatibility.
	 *
	 * @since 4.0.0
	 *
	 * @param string $name      Method to call.
	 * @param array  $arguments Arguments to pass when calling.
	 * @return mixed|false Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, $this->compat_methods, true ) ) {
			return $this->$name( ...$arguments );
		}
		return false;
	}

	/**
	 * Takes a URL and returns the corresponding oEmbed provider's URL, if there is one.
	 *
	 * @since 4.0.0
	 *
	 * @see WP_oEmbed::discover()
	 *
	 * @param string       $url  The URL to the content.
	 * @param string|array $args {
	 *     Optional. Additional provider arguments. Default empty.
	 *
	 *     @type bool $discover Optional. Determines whether to attempt to discover link tags
	 *                          at the given URL for an oEmbed provider when the provider URL
	 *                          is not found in the built-in providers list. Default true.
	 * }
	 * @return string|false The oEmbed provider URL on success, false on failure.
	 */
	public function get_provider( $url, $args = '' ) {
		$args = wp_parse_args( $args );

		$provider = false;

		if ( ! isset( $args['discover'] ) ) {
			$args['discover'] = true;
		}

		foreach ( $this->providers as $matchmask => $data ) {
			list( $providerurl, $regex ) = $data;

			// Turn the asterisk-type provider URLs into regex.
			if ( ! $regex ) {
				$matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $matchmask ), '#' ) ) . '#i';
				$matchmask = preg_replace( '|^#http\\\://|', '#https?\://', $matchmask );
			}

			if ( preg_match( $matchmask, $url ) ) {
				$provider = str_replace( '{format}', 'json', $providerurl ); // JSON is easier to deal with than XML.
				break;
			}
		}

		if ( ! $provider && $args['discover'] ) {
			$provider = $this->discover( $url );
		}

		return $provider;
	}

	/**
	 * Adds an oEmbed provider.
	 *
	 * The provider is added just-in-time when wp_oembed_add_provider() is called before
	 * the {@see 'plugins_loaded'} hook.
	 *
	 * The just-in-time addition is for the benefit of the {@see 'oembed_providers'} filter.
	 *
	 * @since 4.0.0
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
	 * Takes a URL and attempts to return the oEmbed data.
	 *
	 * @see WP_oEmbed::fetch()
	 *
	 * @since 4.8.0
	 *
	 * @param string       $url  The URL to the content that should be attempted to be embedded.
	 * @param string|array $args Optional. Additional arguments for retrieving embed HTML.
	 *                           See wp_oembed_get() for accepted arguments. Default empty.
	 * @return object|false The result in the form of an object on success, false on failure.
	 */
	public function get_data( $url, $args = '' ) {
		$args = wp_parse_args( $args );

		$provider = $this->get_provider( $url, $args );

		if ( ! $provider ) {
			return false;
		}

		$data = $this->fetch( $provider, $url, $args );

		if ( false === $data ) {
			return false;
		}

		return $data;
	}

	/**
	 * The do-it-all function that takes a URL and attempts to return the HTML.
	 *
	 * @see WP_oEmbed::fetch()
	 * @see WP_oEmbed::data2html()
	 *
	 * @since 2.9.0
	 *
	 * @param string       $url  The URL to the content that should be attempted to be embedded.
	 * @param string|array $args Optional. Additional arguments for retrieving embed HTML.
	 *                           See wp_oembed_get() for accepted arguments. Default empty.
	 * @return string|false The UNSANITIZED (and potentially unsafe) HTML that should be used to embed
	 *                      on success, false on failure.
	 */
	public function get_html( $url, $args = '' ) {
		/**
		 * Filters the oEmbed result before any HTTP requests are made.
		 *
		 * This allows one to short-circuit the default logic, perhaps by
		 * replacing it with a routine that is more optimal for your setup.
		 *
		 * Returning a non-null value from the filter will effectively short-circuit retrieval
		 * and return the passed value instead.
		 *
		 * @since 4.5.3
		 *
		 * @param null|string  $result The UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
		 *                             Default null to continue retrieving the result.
		 * @param string       $url    The URL to the content that should be attempted to be embedded.
		 * @param string|array $args   Optional. Additional arguments for retrieving embed HTML.
		 *                             See wp_oembed_get() for accepted arguments. Default empty.
		 */
		$pre = apply_filters( 'pre_oembed_result', null, $url, $args );

		if ( null !== $pre ) {
			return $pre;
		}

		$data = $this->get_data( $url, $args );

		if ( false === $data ) {
			return false;
		}

		/**
		 * Filters the HTML returned by the oEmbed provider.
		 *
		 * @since 2.9.0
		 *
		 * @param string|false $data The returned oEmbed HTML (false if unsafe).
		 * @param string       $url  URL of the content to be embedded.
		 * @param string|array $args Optional. Additional arguments for retrieving embed HTML.
		 *                           See wp_oembed_get() for accepted arguments. Default empty.
		 */
		return apply_filters( 'oembed_result', $this->data2html( $data, $url ), $url, $args );
	}

	/**
	 * Attempts to discover link tags at the given URL for an oEmbed provider.
	 *
	 * @since 2.9.0
	 *
	 * @param string $url The URL that should be inspected for discovery `<link>` tags.
	 * @return string|false The oEmbed provider URL on success, false on failure.
	 */
	public function discover( $url ) {
		$providers = array();
		$args      = array(
			'limit_response_size' => 153600, // 150 KB
		);

		/**
		 * Filters oEmbed remote get arguments.
		 *
		 * @since 4.0.0
		 *
		 * @see WP_Http::request()
		 *
		 * @param array  $args oEmbed remote get arguments.
		 * @param string $url  URL to be inspected.
		 */
		$args = apply_filters( 'oembed_remote_get_args', $args, $url );

		// Fetch URL content.
		$request = wp_safe_remote_get( $url, $args );
		$html    = wp_remote_retrieve_body( $request );
		if ( $html ) {

			/**
			 * Filters the link types that contain oEmbed provider URLs.
			 *
			 * @since 2.9.0
			 *
			 * @param string[] $format Array of oEmbed link types. Accepts 'application/json+oembed',
			 *                         'text/xml+oembed', and 'application/xml+oembed' (incorrect,
			 *                         used by at least Vimeo).
			 */
			$linktypes = apply_filters(
				'oembed_linktypes',
				array(
					'application/json+oembed' => 'json',
					'text/xml+oembed'         => 'xml',
					'application/xml+oembed'  => 'xml',
				)
			);

			// Strip <body>.
			$html_head_end = stripos( $html, '</head>' );
			if ( $html_head_end ) {
				$html = substr( $html, 0, $html_head_end );
			}

			// Do a quick check.
			$tagfound = false;
			foreach ( $linktypes as $linktype => $format ) {
				if ( stripos( $html, $linktype ) ) {
					$tagfound = true;
					break;
				}
			}

			if ( $tagfound && preg_match_all( '#<link([^<>]+)/?>#iU', $html, $links ) ) {
				foreach ( $links[1] as $link ) {
					$atts = shortcode_parse_atts( $link );

					if ( ! empty( $atts['type'] ) && ! empty( $linktypes[ $atts['type'] ] ) && ! empty( $atts['href'] ) ) {
						$providers[ $linktypes[ $atts['type'] ] ] = htmlspecialchars_decode( $atts['href'] );

						// Stop here if it's JSON (that's all we need).
						if ( 'json' === $linktypes[ $atts['type'] ] ) {
							break;
						}
					}
				}
			}
		}

		// JSON is preferred to XML.
		if ( ! empty( $providers['json'] ) ) {
			return $providers['json'];
		} elseif ( ! empty( $providers['xml'] ) ) {
			return $providers['xml'];
		} else {
			return false;
		}
	}

	/**
	 * Connects to a oEmbed provider and returns the result.
	 *
	 * @since 2.9.0
	 *
	 * @param string       $provider The URL to the oEmbed provider.
	 * @param string       $url      The URL to the content that is desired to be embedded.
	 * @param string|array $args     Optional. Additional arguments for retrieving embed HTML.
	 *                               See wp_oembed_get() for accepted arguments. Default empty.
	 * @return object|false The result in the form of an object on success, false on failure.
	 */
	public function fetch( $provider, $url, $args = '' ) {
		$args = wp_parse_args( $args, wp_embed_defaults( $url ) );

		$provider = add_query_arg( 'maxwidth', (int) $args['width'], $provider );
		$provider = add_query_arg( 'maxheight', (int) $args['height'], $provider );
		$provider = add_query_arg( 'url', urlencode( $url ), $provider );
		$provider = add_query_arg( 'dnt', 1, $provider );

		/**
		 * Filters the oEmbed URL to be fetched.
		 *
		 * @since 2.9.0
		 * @since 4.9.0 The `dnt` (Do Not Track) query parameter was added to all oEmbed provider URLs.
		 *
		 * @param string $provider URL of the oEmbed provider.
		 * @param string $url      URL of the content to be embedded.
		 * @param array  $args     Optional. Additional arguments for retrieving embed HTML.
		 *                         See wp_oembed_get() for accepted arguments. Default empty.
		 */
		$provider = apply_filters( 'oembed_fetch_url', $provider, $url, $args );

		foreach ( array( 'json', 'xml' ) as $format ) {
			$result = $this->_fetch_with_format( $provider, $format );
			if ( is_wp_error( $result ) && 'not-implemented' === $result->get_error_code() ) {
				continue;
			}
			return ( $result && ! is_wp_error( $result ) ) ? $result : false;
		}
		return false;
	}

	/**
	 * Fetches result from an oEmbed provider for a specific format and complete provider URL
	 *
	 * @since 3.0.0
	 *
	 * @param string $provider_url_with_args URL to the provider with full arguments list (url, maxheight, etc.)
	 * @param string $format                 Format to use.
	 * @return object|false|WP_Error The result in the form of an object on success, false on failure.
	 */
	private function _fetch_with_format( $provider_url_with_args, $format ) {
		$provider_url_with_args = add_query_arg( 'format', $format, $provider_url_with_args );

		/** This filter is documented in wp-includes/class-wp-oembed.php */
		$args = apply_filters( 'oembed_remote_get_args', array(), $provider_url_with_args );

		$response = wp_safe_remote_get( $provider_url_with_args, $args );
		if ( 501 == wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'not-implemented' );
		}
		$body = wp_remote_retrieve_body( $response );
		if ( ! $body ) {
			return false;
		}
		$parse_method = "_parse_$format";
		return $this->$parse_method( $body );
	}

	/**
	 * Parses a json response body.
	 *
	 * @since 3.0.0
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
	 *
	 * @param string $response_body
	 * @return object|false
	 */
	private function _parse_xml( $response_body ) {
		if ( ! function_exists( 'libxml_disable_entity_loader' ) ) {
			return false;
		}

		if ( PHP_VERSION_ID < 80000 ) {
			// This function has been deprecated in PHP 8.0 because in libxml 2.9.0, external entity loading
			// is disabled by default, so this function is no longer needed to protect against XXE attacks.
			// phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions.libxml_disable_entity_loaderDeprecated
			$loader = libxml_disable_entity_loader( true );
		}

		$errors = libxml_use_internal_errors( true );

		$return = $this->_parse_xml_body( $response_body );

		libxml_use_internal_errors( $errors );

		if ( PHP_VERSION_ID < 80000 && isset( $loader ) ) {
			// phpcs:ignore PHPCompatibility.FunctionUse.RemovedFunctions.libxml_disable_entity_loaderDeprecated
			libxml_disable_entity_loader( $loader );
		}

		return $return;
	}

	/**
	 * Serves as a helper function for parsing an XML response body.
	 *
	 * @since 3.6.0
	 *
	 * @param string $response_body
	 * @return stdClass|false
	 */
	private function _parse_xml_body( $response_body ) {
		if ( ! function_exists( 'simplexml_import_dom' ) || ! class_exists( 'DOMDocument', false ) ) {
			return false;
		}

		$dom     = new DOMDocument;
		$success = $dom->loadXML( $response_body );
		if ( ! $success ) {
			return false;
		}

		if ( isset( $dom->doctype ) ) {
			return false;
		}

		foreach ( $dom->childNodes as $child ) {
			if ( XML_DOCUMENT_TYPE_NODE === $child->nodeType ) {
				return false;
			}
		}

		$xml = simplexml_import_dom( $dom );
		if ( ! $xml ) {
			return false;
		}

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
	 *
	 * @param object $data A data object result from an oEmbed provider.
	 * @param string $url  The URL to the content that is desired to be embedded.
	 * @return string|false The HTML needed to embed on success, false on failure.
	 */
	public function data2html( $data, $url ) {
		if ( ! is_object( $data ) || empty( $data->type ) ) {
			return false;
		}

		$return = false;

		switch ( $data->type ) {
			case 'photo':
				if ( empty( $data->url ) || empty( $data->width ) || empty( $data->height ) ) {
					break;
				}
				if ( ! is_string( $data->url ) || ! is_numeric( $data->width ) || ! is_numeric( $data->height ) ) {
					break;
				}

				$title  = ! empty( $data->title ) && is_string( $data->title ) ? $data->title : '';
				$return = '<a href="' . esc_url( $url ) . '"><img src="' . esc_url( $data->url ) . '" alt="' . esc_attr( $title ) . '" width="' . esc_attr( $data->width ) . '" height="' . esc_attr( $data->height ) . '" /></a>';
				break;

			case 'video':
			case 'rich':
				if ( ! empty( $data->html ) && is_string( $data->html ) ) {
					$return = $data->html;
				}
				break;

			case 'link':
				if ( ! empty( $data->title ) && is_string( $data->title ) ) {
					$return = '<a href="' . esc_url( $url ) . '">' . esc_html( $data->title ) . '</a>';
				}
				break;

			default:
				$return = false;
		}

		/**
		 * Filters the returned oEmbed HTML.
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

		$count     = 1;
		$found     = array();
		$token     = '__PRE__';
		$search    = array( "\t", "\n", "\r", ' ' );
		$replace   = array( '__TAB__', '__NL__', '__CR__', '__SPACE__' );
		$tokenized = str_replace( $search, $replace, $html );

		preg_match_all( '#(<pre[^>]*>.+?</pre>)#i', $tokenized, $matches, PREG_SET_ORDER );
		foreach ( $matches as $i => $match ) {
			$tag_html  = str_replace( $replace, $search, $match[0] );
			$tag_token = $token . $i;

			$found[ $tag_token ] = $tag_html;
			$html                = str_replace( $tag_html, $tag_token, $html, $count );
		}

		$replaced = str_replace( $replace, $search, $html );
		$stripped = str_replace( array( "\r\n", "\n" ), '', $replaced );
		$pre      = array_values( $found );
		$tokens   = array_keys( $found );

		return str_replace( $tokens, $pre, $stripped );
	}
}
