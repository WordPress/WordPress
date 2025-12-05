<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * Class Feed_Improvements
 */
class Feed_Improvements implements Integration_Interface {

	/**
	 * Holds the options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Holds the meta helper surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta;

	/**
	 * Canonical_Header constructor.
	 *
	 * @codeCoverageIgnore It only sets depedencies.
	 *
	 * @param Options_Helper $options The options helper.
	 * @param Meta_Surface   $meta    The meta surface.
	 */
	public function __construct( Options_Helper $options, Meta_Surface $meta ) {
		$this->options = $options;
		$this->meta    = $meta;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Registers hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'get_bloginfo_rss', [ $this, 'filter_bloginfo_rss' ], 10, 2 );
		\add_filter( 'document_title_separator', [ $this, 'filter_document_title_separator' ] );

		\add_action( 'do_feed_rss', [ $this, 'handle_rss_feed' ], 9 );
		\add_action( 'do_feed_rss2', [ $this, 'send_canonical_header' ], 9 );
		\add_action( 'do_feed_rss2', [ $this, 'add_robots_headers' ], 9 );
	}

	/**
	 * Filter `bloginfo_rss` output to give the URL for what's being shown instead of just always the homepage.
	 *
	 * @param string $show The output so far.
	 * @param string $what What is being shown.
	 *
	 * @return string
	 */
	public function filter_bloginfo_rss( $show, $what ) {
		if ( $what === 'url' ) {
			return $this->get_url_for_queried_object( $show );
		}

		return $show;
	}

	/**
	 * Makes sure send canonical header always runs, because this RSS hook does not support the for_comments parameter
	 *
	 * @return void
	 */
	public function handle_rss_feed() {
		$this->send_canonical_header( false );
	}

	/**
	 * Adds a canonical link header to the main canonical URL for the requested feed object. If it is not a comment
	 * feed.
	 *
	 * @param bool $for_comments If the RRS feed is meant for a comment feed.
	 *
	 * @return void
	 */
	public function send_canonical_header( $for_comments ) {

		if ( $for_comments || \headers_sent() ) {
			return;
		}

		$queried_object = \get_queried_object();
		// Don't call get_class with null. This gives a warning.
		$class = ( $queried_object !== null ) ? \get_class( $queried_object ) : null;

		$url = $this->get_url_for_queried_object( $this->meta->for_home_page()->canonical );
		if ( ( ! empty( $url ) && $url !== $this->meta->for_home_page()->canonical ) || $class === null ) {
			\header( \sprintf( 'Link: <%s>; rel="canonical"', $url ), false );
		}
	}

	/**
	 * Adds noindex, follow tag for comment feeds.
	 *
	 * @param bool $for_comments If the RSS feed is meant for a comment feed.
	 *
	 * @return void
	 */
	public function add_robots_headers( $for_comments ) {
		if ( $for_comments && ! \headers_sent() ) {
			\header( 'X-Robots-Tag: noindex, follow', true );
		}
	}

	/**
	 * Makes sure the title separator set in Yoast SEO is used for all feeds.
	 *
	 * @param string $separator The separator from WordPress.
	 *
	 * @return string The separator from Yoast SEO's settings.
	 */
	public function filter_document_title_separator( $separator ) {
		return \html_entity_decode( $this->options->get_title_separator() );
	}

	/**
	 * Determines the main URL for the queried object.
	 *
	 * @param string $url The URL determined so far.
	 *
	 * @return string The canonical URL for the queried object.
	 */
	protected function get_url_for_queried_object( $url = '' ) {
		$queried_object = \get_queried_object();
		// Don't call get_class with null. This gives a warning.
		$class = ( $queried_object !== null ) ? \get_class( $queried_object ) : null;
		$meta  = false;

		switch ( $class ) {
			// Post type archive feeds.
			case 'WP_Post_Type':
				$meta = $this->meta->for_post_type_archive( $queried_object->name );
				break;
			// Post comment feeds.
			case 'WP_Post':
				$meta = $this->meta->for_post( $queried_object->ID );
				break;
			// Term feeds.
			case 'WP_Term':
				$meta = $this->meta->for_term( $queried_object->term_id );
				break;
			// Author feeds.
			case 'WP_User':
				$meta = $this->meta->for_author( $queried_object->ID );
				break;
			// This would be NULL on the home page and on date archive feeds.
			case null:
				$meta = $this->meta->for_home_page();
				break;
			default:
				break;
		}

		if ( $meta ) {
			return $meta->canonical;
		}

		return $url;
	}
}
