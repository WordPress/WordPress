<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use WP_Query;
use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Crawl_Cleanup_Searches.
 */
class Crawl_Cleanup_Searches implements Integration_Interface {

	/**
	 * Patterns to match against to find spam.
	 *
	 * @var array
	 */
	private $patterns = [
		'/[：（）【】［］]+/u',
		'/(TALK|QQ)\:/iu',
	];

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The redirect helper.
	 *
	 * @var Redirect_Helper
	 */
	private $redirect_helper;

	/**
	 * Crawl_Cleanup_Searches integration constructor.
	 *
	 * @param Options_Helper  $options_helper  The option helper.
	 * @param Redirect_Helper $redirect_helper The redirect helper.
	 */
	public function __construct( Options_Helper $options_helper, Redirect_Helper $redirect_helper ) {
		$this->options_helper  = $options_helper;
		$this->redirect_helper = $redirect_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->options_helper->get( 'search_cleanup' ) ) {
			\add_filter( 'pre_get_posts', [ $this, 'validate_search' ] );
		}
		if ( $this->options_helper->get( 'redirect_search_pretty_urls' ) && ! empty( \get_option( 'permalink_structure' ) ) ) {
			\add_action( 'template_redirect', [ $this, 'maybe_redirect_searches' ], 2 );
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
	 * Check if we want to allow this search to happen.
	 *
	 * @param WP_Query $query The main query.
	 *
	 * @return WP_Query
	 */
	public function validate_search( WP_Query $query ) {
		if ( ! $query->is_search() ) {
			return $query;
		}
		// First check against emoji and patterns we might not want.
		$this->check_unwanted_patterns( $query );

		// Then limit characters if still needed.
		$this->limit_characters();

		return $query;
	}

	/**
	 * Redirect pretty search URLs to the "raw" equivalent
	 *
	 * @return void
	 */
	public function maybe_redirect_searches() {
		if ( ! \is_search() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( isset( $_SERVER['REQUEST_URI'] ) && \stripos( $_SERVER['REQUEST_URI'], '/search/' ) === 0 ) {
			$args = [];

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$parsed = \wp_parse_url( $_SERVER['REQUEST_URI'] );

			if ( ! empty( $parsed['query'] ) ) {
				\wp_parse_str( $parsed['query'], $args );
			}

			$args['s'] = \get_search_query();

			$proper_url = \home_url( '/' );

			if ( \intval( \get_query_var( 'paged' ) ) > 1 ) {
				$proper_url .= \sprintf( 'page/%s/', \get_query_var( 'paged' ) );
				unset( $args['paged'] );
			}

			$proper_url = \add_query_arg( \array_map( 'rawurlencode_deep', $args ), $proper_url );

			if ( ! empty( $parsed['fragment'] ) ) {
				$proper_url .= '#' . \rawurlencode( $parsed['fragment'] );
			}

			$this->redirect_away( 'We redirect pretty URLs to the raw format.', $proper_url );
		}
	}

	/**
	 * Check query against unwanted search patterns.
	 *
	 * @param WP_Query $query The main WordPress query.
	 *
	 * @return void
	 */
	private function check_unwanted_patterns( WP_Query $query ) {
		$s = \rawurldecode( $query->query_vars['s'] );
		if ( $this->options_helper->get( 'search_cleanup_emoji' ) && $this->has_emoji( $s ) ) {
			$this->redirect_away( 'We don\'t allow searches with emojis and other special characters.' );
		}

		if ( ! $this->options_helper->get( 'search_cleanup_patterns' ) ) {
			return;
		}
		foreach ( $this->patterns as $pattern ) {
			$outcome = \preg_match( $pattern, $s, $matches );
			if ( $outcome && $matches !== [] ) {
				$this->redirect_away( 'Your search matched a common spam pattern.' );
			}
		}
	}

	/**
	 * Redirect to the homepage for invalid searches.
	 *
	 * @param string $reason The reason for redirecting away.
	 * @param string $to_url The URL to redirect to.
	 *
	 * @return void
	 */
	private function redirect_away( $reason, $to_url = '' ) {
		if ( empty( $to_url ) ) {
			$to_url = \get_home_url();
		}

		$this->redirect_helper->do_safe_redirect( $to_url, 301, 'Yoast Search Filtering: ' . $reason );
	}

	/**
	 * Limits the number of characters in the search query.
	 *
	 * @return void
	 */
	private function limit_characters() {
		// We retrieve the search term unescaped because we want to count the characters properly. We make sure to escape it afterwards, if we do something with it.
		$unescaped_s = \get_search_query( false );

		// We then unslash the search term, again because we want to count the characters properly. We make sure to slash it afterwards, if we do something with it.
		$raw_s = \wp_unslash( $unescaped_s );
		if ( \mb_strlen( $raw_s, 'UTF-8' ) > $this->options_helper->get( 'search_character_limit' ) ) {
			$new_s = \mb_substr( $raw_s, 0, $this->options_helper->get( 'search_character_limit' ), 'UTF-8' );
			\set_query_var( 's', \wp_slash( \esc_attr( $new_s ) ) );
		}
	}

	/**
	 * Determines if a text string contains an emoji or not.
	 *
	 * @param string $text The text string to detect emoji in.
	 *
	 * @return bool
	 */
	private function has_emoji( $text ) {
		$emojis_regex = '/([^-\p{L}\x00-\x7F]+)/u';
		\preg_match( $emojis_regex, $text, $matches );

		return ! empty( $matches );
	}
}
