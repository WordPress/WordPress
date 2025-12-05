<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for the home URL.
 */
class Home_Url_Helper {

	/**
	 * The home url.
	 *
	 * @var string
	 */
	protected static $home_url;

	/**
	 * The parsed home url.
	 *
	 * @var array
	 */
	protected static $parsed_home_url;

	/**
	 * Retrieves the home url.
	 *
	 * @return string The home url.
	 */
	public function get() {
		if ( static::$home_url === null ) {
			static::$home_url = \home_url();
		}

		return static::$home_url;
	}

	/**
	 * Retrieves the home url that has been parsed.
	 *
	 * @return array The parsed url.
	 */
	public function get_parsed() {
		if ( static::$parsed_home_url === null ) {
			static::$parsed_home_url = \wp_parse_url( $this->get() );
		}

		return static::$parsed_home_url;
	}
}
