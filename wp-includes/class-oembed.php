<?php
/**
 * API for fetching the HTML to embed remote content based on a provided URL.
 * Used internally by the {@link WP_Embed} class, but is designed to be generic.
 *
 * @link http://codex.wordpress.org/oEmbed oEmbed Codex Article
 * @link http://oembed.com/ oEmbed Homepage
 *
 * @package WordPress
 * @subpackage oEmbed
 */

/**
 * oEmbed class.
 *
 * @package WordPress
 * @subpackage oEmbed
 * @since 2.9.0
 */
class WP_oEmbed {
	var $providers = array();

	/**
	 * Constructor
	 *
	 * @uses apply_filters() Filters a list of pre-defined oEmbed providers.
	 */
	function __construct() {
		// List out some popular sites that support oEmbed.
		// The WP_Embed class disables discovery for non-unfiltered_html users, so only providers in this array will be used for them.
		// Add to this list using the wp_oembed_add_provider() function (see it's PHPDoc for details).
		$this->providers = apply_filters( 'oembed_providers', array(
			'#http://(www\.)?youtube.com/watch.*#i'         => array( 'http://www.youtube.com/oembed',            true  ),
			'http://youtu.be/*'                             => array( 'http://www.youtube.com/oembed',            false ),
			'http://blip.tv/file/*'                         => array( 'http://blip.tv/oembed/',                   false ),
			'#http://(www\.)?vimeo\.com/.*#i'               => array( 'http://www.vimeo.com/api/oembed.{format}', true  ),
			'#http://(www\.)?dailymotion\.com/.*#i'         => array( 'http://www.dailymotion.com/api/oembed',    true  ),
			'#http://(www\.)?flickr\.com/.*#i'              => array( 'http://www.flickr.com/services/oembed/',   true  ),
			'#http://(.+)?smugmug\.com/.*#i'                => array( 'http://api.smugmug.com/services/oembed/',  true  ),
			'#http://(www\.)?hulu\.com/watch/.*#i'          => array( 'http://www.hulu.com/api/oembed.{format}',  true  ),
			'#http://(www\.)?viddler\.com/.*#i'             => array( 'http://lab.viddler.com/services/oembed/',  true  ),
			'http://qik.com/*'                              => array( 'http://qik.com/api/oembed.{format}',       false ),
			'http://revision3.com/*'                        => array( 'http://revision3.com/api/oembed/',         false ),
			'http://i*.photobucket.com/albums/*'            => array( 'http://photobucket.com/oembed',            false ),
			'http://gi*.photobucket.com/groups/*'           => array( 'http://photobucket.com/oembed',            false ),
			'#http://(www\.)?scribd\.com/.*#i'              => array( 'http://www.scribd.com/services/oembed',    true  ),
			'http://wordpress.tv/*'                         => array( 'http://wordpress.tv/oembed/',              false ),
			'#http://(answers|surveys)\.polldaddy.com/.*#i' => array( 'http://polldaddy.com/oembed/',             true  ),
			'#http://(www\.)?funnyordie\.com/videos/.*#i'   => array( 'http://www.funnyordie.com/oembed',         true  ),
		) );

		// Fix any embeds that contain new lines in the middle of the HTML which breaks wpautop().
		add_filter( 'oembed_dataparse', array(&$this, '_strip_newlines'), 10, 3 );
	}

	/**
	 * The do-it-all function that takes a URL and attempts to return the HTML.
	 *
	 * @see WP_oEmbed::discover()
	 * @see WP_oEmbed::fetch()
	 * @see WP_oEmbed::data2html()
	 *
	 * @param string $url The URL to the content that should be attempted to be embedded.
	 * @param array $args Optional arguments. Usually passed from a shortcode.
	 * @return bool|string False on failure, otherwise the UNSANITIZED (and potentially unsafe) HTML that should be used to embed.
	 */
	function get_html( $url, $args = '' ) {
		$provider = false;

		if ( !isset($args['discover']) )
			$args['discover'] = true;

		foreach ( $this->providers as $matchmask => $data ) {
			list( $providerurl, $regex ) = $data;

			// Turn the asterisk-type provider URLs into regex
			if ( !$regex )
				$matchmask = '#' . str_replace( '___wildcard___', '(.+)', preg_quote( str_replace( '*', '___wildcard___', $matchmask ), '#' ) ) . '#i';

			if ( preg_match( $matchmask, $url ) ) {
				$provider = str_replace( '{format}', 'json', $providerurl ); // JSON is easier to deal with than XML
				break;
			}
		}

		if ( !$provider && $args['discover'] )
			$provider = $this->discover( $url );

		if ( !$provider || false === $data = $this->fetch( $provider, $url, $args ) )
			return false;

		return apply_filters( 'oembed_result', $this->data2html( $data, $url ), $url, $args );
	}

	/**
	 * Attempts to find oEmbed provider discovery <link> tags at the given URL.
	 *
	 * @param string $url The URL that should be inspected for discovery <link> tags.
	 * @return bool|string False on failure, otherwise the oEmbed provider URL.
	 */
	function discover( $url ) {
		$providers = array();

		// Fetch URL content
		if ( $html = wp_remote_retrieve_body( wp_remote_get( $url ) ) ) {

			// <link> types that contain oEmbed provider URLs
			$linktypes = apply_filters( 'oembed_linktypes', array(
				'application/json+oembed' => 'json',
				'text/xml+oembed' => 'xml',
				'application/xml+oembed' => 'xml', // Incorrect, but used by at least Vimeo
			) );

			// Strip <body>
			$html = substr( $html, 0, stripos( $html, '</head>' ) );

			// Do a quick check
			$tagfound = false;
			foreach ( $linktypes as $linktype => $format ) {
				if ( stripos($html, $linktype) ) {
					$tagfound = true;
					break;
				}
			}

			if ( $tagfound && preg_match_all( '/<link([^<>]+)>/i', $html, $links ) ) {
				foreach ( $links[1] as $link ) {
					$atts = shortcode_parse_atts( $link );

					if ( !empty($atts['type']) && !empty($linktypes[$atts['type']]) && !empty($atts['href']) ) {
						$providers[$linktypes[$atts['type']]] = $atts['href'];

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
	 * @param string $provider The URL to the oEmbed provider.
	 * @param string $url The URL to the content that is desired to be embedded.
	 * @param array $args Optional arguments. Usually passed from a shortcode.
	 * @return bool|object False on failure, otherwise the result in the form of an object.
	 */
	function fetch( $provider, $url, $args = '' ) {
		$args = wp_parse_args( $args, wp_embed_defaults() );

		$provider = add_query_arg( 'maxwidth', $args['width'], $provider );
		$provider = add_query_arg( 'maxheight', $args['height'], $provider );
		$provider = add_query_arg( 'url', urlencode($url), $provider );

		foreach( array( 'json', 'xml' ) as $format ) {
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
	 * @param string $provider_url_with_args URL to the provider with full arguments list (url, maxheight, etc.)
	 * @param string $format Format to use
	 * @return bool|object False on failure, otherwise the result in the form of an object.
	 */
	function _fetch_with_format( $provider_url_with_args, $format ) {
		$provider_url_with_args = add_query_arg( 'format', $format, $provider_url_with_args );
		$response = wp_remote_get( $provider_url_with_args );
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
	 */
	function _parse_json( $response_body ) {
		return ( ( $data = json_decode( trim( $response_body ) ) ) && is_object( $data ) ) ? $data : false;
	}

	/**
	 * Parses an XML response body.
	 *
	 * @since 3.0.0
	 * @access private
	 */
	function _parse_xml( $response_body ) {
		if ( function_exists('simplexml_load_string') ) {
			$errors = libxml_use_internal_errors( 'true' );
			$data = simplexml_load_string( $response_body );
			libxml_use_internal_errors( $errors );
			if ( is_object( $data ) )
				return $data;
		}
		return false;
	}

	/**
	 * Converts a data object from {@link WP_oEmbed::fetch()} and returns the HTML.
	 *
	 * @param object $data A data object result from an oEmbed provider.
	 * @param string $url The URL to the content that is desired to be embedded.
	 * @return bool|string False on error, otherwise the HTML needed to embed.
	 */
	function data2html( $data, $url ) {
		if ( !is_object($data) || empty($data->type) )
			return false;

		switch ( $data->type ) {
			case 'photo':
				if ( empty($data->url) || empty($data->width) || empty($data->height) )
					return false;

				$title = ( !empty($data->title) ) ? $data->title : '';
				$return = '<a href="' . esc_url( $url ) . '"><img src="' . esc_url( $data->url ) . '" alt="' . esc_attr($title) . '" width="' . esc_attr($data->width) . '" height="' . esc_attr($data->height) . '" /></a>';
				break;

			case 'video':
			case 'rich':
				$return = ( !empty($data->html) ) ? $data->html : false;
				break;

			case 'link':
				$return = ( !empty($data->title) ) ? '<a href="' . esc_url($url) . '">' . esc_html($data->title) . '</a>' : false;
				break;

			default;
				$return = false;
		}

		// You can use this filter to add support for custom data types or to filter the result
		return apply_filters( 'oembed_dataparse', $return, $data, $url );
	}

	/**
	 * Strip any new lines from the HTML.
	 *
	 * @access private
	 * @param string $html Existing HTML.
	 * @param object $data Data object from WP_oEmbed::data2html()
	 * @param string $url The original URL passed to oEmbed.
	 * @return string Possibly modified $html
	 */
	function _strip_newlines( $html, $data, $url ) {
		if ( false !== strpos( $html, "\n" ) )
			$html = str_replace( array( "\r\n", "\n" ), '', $html );

		return $html;
	}
}

/**
 * Returns the initialized {@link WP_oEmbed} object
 *
 * @since 2.9.0
 * @access private
 *
 * @see WP_oEmbed
 * @uses WP_oEmbed
 *
 * @return WP_oEmbed object.
 */
function &_wp_oembed_get_object() {
	static $wp_oembed;

	if ( is_null($wp_oembed) )
		$wp_oembed = new WP_oEmbed();

	return $wp_oembed;
}

?>