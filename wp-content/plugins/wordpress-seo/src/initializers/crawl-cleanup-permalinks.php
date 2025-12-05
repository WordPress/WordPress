<?php

namespace Yoast\WP\SEO\Initializers;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Crawl_Cleanup_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;

/**
 * Class Crawl_Cleanup_Permalinks.
 */
class Crawl_Cleanup_Permalinks implements Initializer_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	private $url_helper;

	/**
	 * The Redirect_Helper.
	 *
	 * @var Redirect_Helper
	 */
	private $redirect_helper;

	/**
	 * The Crawl_Cleanup_Helper.
	 *
	 * @var Crawl_Cleanup_Helper
	 */
	private $crawl_cleanup_helper;

	/**
	 * Crawl Cleanup Basic integration constructor.
	 *
	 * @param Options_Helper       $options_helper       The option helper.
	 * @param Url_Helper           $url_helper           The URL helper.
	 * @param Redirect_Helper      $redirect_helper      The Redirect Helper.
	 * @param Crawl_Cleanup_Helper $crawl_cleanup_helper The Crawl_Cleanup_Helper.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Url_Helper $url_helper,
		Redirect_Helper $redirect_helper,
		Crawl_Cleanup_Helper $crawl_cleanup_helper
	) {
		$this->options_helper       = $options_helper;
		$this->url_helper           = $url_helper;
		$this->redirect_helper      = $redirect_helper;
		$this->crawl_cleanup_helper = $crawl_cleanup_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function initialize() {
		// We need to hook after 10 because otherwise our options helper isn't available yet.
		\add_action( 'plugins_loaded', [ $this, 'register_hooks' ], 15 );
	}

	/**
	 * Hooks our required hooks.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->options_helper->get( 'clean_campaign_tracking_urls' ) && ! empty( \get_option( 'permalink_structure' ) ) ) {
			\add_action( 'template_redirect', [ $this, 'utm_redirect' ], 0 );
		}
		if ( $this->options_helper->get( 'clean_permalinks' ) && ! empty( \get_option( 'permalink_structure' ) ) ) {
			\add_action( 'template_redirect', [ $this, 'clean_permalinks' ], 1 );
		}
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array The array of conditionals.
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Redirect utm variables away.
	 *
	 * @return void
	 */
	public function utm_redirect() {
		// Prevents WP CLI from throwing an error.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || \strpos( $_SERVER['REQUEST_URI'], '?' ) === false ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! \stripos( $_SERVER['REQUEST_URI'], 'utm_' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$parsed = \wp_parse_url( $_SERVER['REQUEST_URI'] );

		$query      = \explode( '&', $parsed['query'] );
		$utms       = [];
		$other_args = [];

		foreach ( $query as $query_arg ) {
			if ( \stripos( $query_arg, 'utm_' ) === 0 ) {
				$utms[] = $query_arg;
				continue;
			}
			$other_args[] = $query_arg;
		}

		if ( empty( $utms ) ) {
			return;
		}

		$other_args_str = '';
		if ( \count( $other_args ) > 0 ) {
			$other_args_str = '?' . \implode( '&', $other_args );
		}

		$new_path = $parsed['path'] . $other_args_str . '#' . \implode( '&', $utms );

		$message = \sprintf(
			/* translators: %1$s: Yoast SEO */
			\__( '%1$s: redirect utm variables to #', 'wordpress-seo' ),
			'Yoast SEO'
		);

		$this->redirect_helper->do_safe_redirect( \trailingslashit( $this->url_helper->recreate_current_url( false ) ) . \ltrim( $new_path, '/' ), 301, $message );
	}

	/**
	 * Removes unneeded query variables from the URL.
	 *
	 * @return void
	 */
	public function clean_permalinks() {

		if ( $this->crawl_cleanup_helper->should_avoid_redirect() ) {
			return;
		}

		$current_url    = $this->url_helper->recreate_current_url();
		$allowed_params = $this->crawl_cleanup_helper->allowed_params( $current_url );

		// If we had only allowed params, let's just bail out, no further processing needed.
		if ( empty( $allowed_params['query'] ) ) {
			return;
		}

		$url_type = $this->crawl_cleanup_helper->get_url_type();

		switch ( $url_type ) {
			case 'singular_url':
				$proper_url = $this->crawl_cleanup_helper->singular_url();
				break;
			case 'front_page_url':
				$proper_url = $this->crawl_cleanup_helper->front_page_url();
				break;
			case 'page_for_posts_url':
				$proper_url = $this->crawl_cleanup_helper->page_for_posts_url();
				break;
			case 'taxonomy_url':
				$proper_url = $this->crawl_cleanup_helper->taxonomy_url();
				break;
			case 'search_url':
				$proper_url = $this->crawl_cleanup_helper->search_url();
				break;
			case 'page_not_found_url':
				$proper_url = $this->crawl_cleanup_helper->page_not_found_url( $current_url );
				break;
			default:
				$proper_url = '';
		}

		if ( $this->crawl_cleanup_helper->is_query_var_page( $proper_url ) ) {
			$proper_url = $this->crawl_cleanup_helper->query_var_page_url( $proper_url );
		}

		$proper_url = \add_query_arg( $allowed_params['allowed_query'], $proper_url );

		if ( empty( $proper_url ) || $current_url === $proper_url ) {
			return;
		}

		$this->crawl_cleanup_helper->do_clean_redirect( $proper_url );
	}
}
