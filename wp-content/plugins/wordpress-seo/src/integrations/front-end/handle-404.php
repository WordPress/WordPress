<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Wrappers\WP_Query_Wrapper;

/**
 * Handles intercepting requests.
 */
class Handle_404 implements Integration_Interface {

	/**
	 * The WP Query wrapper.
	 *
	 * @var WP_Query_Wrapper
	 */
	private $query_wrapper;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'pre_handle_404', [ $this, 'handle_404' ] );
	}

	/**
	 * Handle_404 constructor.
	 *
	 * @codeCoverageIgnore Handles dependencies.
	 *
	 * @param WP_Query_Wrapper $query_wrapper The query wrapper.
	 */
	public function __construct( WP_Query_Wrapper $query_wrapper ) {
		$this->query_wrapper = $query_wrapper;
	}

	/**
	 * Handles the 404 status code.
	 *
	 * @param bool $handled Whether we've handled the request.
	 *
	 * @return bool True if it's 404.
	 */
	public function handle_404( $handled ) {
		if ( ! $this->is_feed_404() ) {
			return $handled;
		}

		$this->set_404();
		$this->set_headers();

		\add_filter( 'old_slug_redirect_url', '__return_false' );
		\add_filter( 'redirect_canonical', '__return_false' );

		return true;
	}

	/**
	 * If there are no posts in a feed, make it 404 instead of sending an empty RSS feed.
	 *
	 * @return bool True if it's 404.
	 */
	protected function is_feed_404() {
		if ( ! \is_feed() ) {
			return false;
		}

		$wp_query = $this->query_wrapper->get_query();

		// Don't 404 if the query contains post(s) or an object.
		if ( $wp_query->posts || $wp_query->get_queried_object() ) {
			return false;
		}

		// Don't 404 if it isn't archive or singular.
		if ( ! $wp_query->is_archive() && ! $wp_query->is_singular() ) {
			return false;
		}

		return true;
	}

	/**
	 * Sets the 404 status code.
	 *
	 * @return void
	 */
	protected function set_404() {
		$wp_query          = $this->query_wrapper->get_query();
		$wp_query->is_feed = false;
		$wp_query->set_404();
		$this->query_wrapper->set_query( $wp_query );
	}

	/**
	 * Sets the headers for http.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	protected function set_headers() {
		// Overwrite Content-Type header.
		if ( ! \headers_sent() ) {
			\header( 'Content-Type: ' . \get_option( 'html_type' ) . '; charset=' . \get_option( 'blog_charset' ) );
		}

		\status_header( 404 );
		\nocache_headers();
	}
}
