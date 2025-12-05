<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Robots_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Indexing_Controls.
 */
class Indexing_Controls implements Integration_Interface {

	/**
	 * The robots helper.
	 *
	 * @var Robots_Helper
	 */
	protected $robots;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * The constructor.
	 *
	 * @codeCoverageIgnore Sets the dependencies.
	 *
	 * @param Robots_Helper $robots The robots helper.
	 */
	public function __construct( Robots_Helper $robots ) {
		$this->robots = $robots;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function register_hooks() {
		// The option `blog_public` is set in Settings > Reading > Search Engine Visibility.
		if ( (string) \get_option( 'blog_public' ) === '0' ) {
			\add_filter( 'wpseo_robots_array', [ $this->robots, 'set_robots_no_index' ] );
		}

		\add_action( 'template_redirect', [ $this, 'noindex_robots' ] );
		\add_filter( 'loginout', [ $this, 'nofollow_link' ] );
		\add_filter( 'register', [ $this, 'nofollow_link' ] );

		// Remove actions that we will handle through our wpseo_head call, and probably change the output of.
		\remove_action( 'wp_head', 'rel_canonical' );
		\remove_action( 'wp_head', 'index_rel_link' );
		\remove_action( 'wp_head', 'start_post_rel_link' );
		\remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		\remove_action( 'wp_head', 'noindex', 1 );
	}

	/**
	 * Sends a Robots HTTP header preventing URL from being indexed in the search results while allowing search engines
	 * to follow the links in the object at the URL.
	 *
	 * @return bool Boolean indicating whether the noindex header was sent.
	 */
	public function noindex_robots() {
		if ( ! \is_robots() ) {
			return false;
		}

		return $this->set_robots_header();
	}

	/**
	 * Adds rel="nofollow" to a link, only used for login / registration links.
	 *
	 * @param string $input The link element as a string.
	 *
	 * @return string
	 */
	public function nofollow_link( $input ) {
		return \str_replace( '<a ', '<a rel="nofollow" ', $input );
	}

	/**
	 * Sets the x-robots-tag to noindex follow.
	 *
	 * @codeCoverageIgnore Too difficult to test.
	 *
	 * @return bool
	 */
	protected function set_robots_header() {
		if ( \headers_sent() === false ) {
			\header( 'X-Robots-Tag: noindex, follow', true );

			return true;
		}

		return false;
	}
}
