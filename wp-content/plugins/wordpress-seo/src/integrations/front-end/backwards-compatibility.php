<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Adds actions that were previously called and are now deprecated.
 */
class Backwards_Compatibility implements Integration_Interface {

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Backwards_Compatibility constructor
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->options->get( 'opengraph' ) === true ) {
			\add_action( 'wpseo_head', [ $this, 'call_wpseo_opengraph' ], 30 );
		}
		if ( $this->options->get( 'twitter' ) === true && \apply_filters( 'wpseo_output_twitter_card', true ) !== false ) {
			\add_action( 'wpseo_head', [ $this, 'call_wpseo_twitter' ], 40 );
		}
	}

	/**
	 * Calls the old wpseo_opengraph action.
	 *
	 * @return void
	 */
	public function call_wpseo_opengraph() {
		\do_action_deprecated( 'wpseo_opengraph', [], '14.0', 'wpseo_frontend_presenters' );
	}

	/**
	 * Calls the old wpseo_twitter action.
	 *
	 * @return void
	 */
	public function call_wpseo_twitter() {
		\do_action_deprecated( 'wpseo_twitter', [], '14.0', 'wpseo_frontend_presenters' );
	}
}
