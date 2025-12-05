<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Crawl_Cleanup_Basic.
 */
class Crawl_Cleanup_Basic implements Integration_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Crawl Cleanup Basic integration constructor.
	 *
	 * @param Options_Helper $options_helper The option helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Remove HTTP headers we don't want.
		\add_action( 'wp', [ $this, 'clean_headers' ], 0 );

		if ( $this->is_true( 'remove_shortlinks' ) ) {
			// Remove shortlinks.
			\remove_action( 'wp_head', 'wp_shortlink_wp_head' );
			\remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
		}

		if ( $this->is_true( 'remove_rest_api_links' ) ) {
			// Remove REST API links.
			\remove_action( 'wp_head', 'rest_output_link_wp_head' );
			\remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		}

		if ( $this->is_true( 'remove_rsd_wlw_links' ) ) {
			// Remove RSD and WLW Manifest links.
			\remove_action( 'wp_head', 'rsd_link' );
			\remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
			\remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( $this->is_true( 'remove_oembed_links' ) ) {
			// Remove JSON+XML oEmbed links.
			\remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		}

		if ( $this->is_true( 'remove_generator' ) ) {
			\remove_action( 'wp_head', 'wp_generator' );
		}

		if ( $this->is_true( 'remove_emoji_scripts' ) ) {
			// Remove emoji scripts and additional stuff they cause.
			\remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			\remove_action( 'wp_print_styles', 'print_emoji_styles' );
			\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			\remove_action( 'admin_print_styles', 'print_emoji_styles' );
			\add_filter( 'wp_resource_hints', [ $this, 'resource_hints_plain_cleanup' ], 1 );
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
	 * Removes X-Pingback and X-Powered-By headers as they're unneeded.
	 *
	 * @return void
	 */
	public function clean_headers() {
		if ( \headers_sent() ) {
			return;
		}

		if ( $this->is_true( 'remove_powered_by_header' ) ) {
			\header_remove( 'X-Powered-By' );
		}
		if ( $this->is_true( 'remove_pingback_header' ) ) {
			\header_remove( 'X-Pingback' );
		}
	}

	/**
	 * Remove the core s.w.org hint as it's only used for emoji stuff we don't use.
	 *
	 * @param array $hints The hints we're adding to.
	 *
	 * @return array
	 */
	public function resource_hints_plain_cleanup( $hints ) {
		foreach ( $hints as $key => $hint ) {
			if ( \is_array( $hint ) && isset( $hint['href'] ) ) {
				if ( \strpos( $hint['href'], '//s.w.org' ) !== false ) {
					unset( $hints[ $key ] );
				}
			}
			elseif ( \strpos( $hint, '//s.w.org' ) !== false ) {
					unset( $hints[ $key ] );
			}
		}

		return $hints;
	}

	/**
	 * Checks if the value of an option is set to true.
	 *
	 * @param string $option_name The option name.
	 *
	 * @return bool
	 */
	private function is_true( $option_name ) {
		return $this->options_helper->get( $option_name ) === true;
	}
}
