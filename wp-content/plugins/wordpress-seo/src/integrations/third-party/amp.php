<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Integrations\Front_End_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * AMP integration.
 */
class AMP implements Integration_Interface {

	/**
	 * The front end integration.
	 *
	 * @var Front_End_Integration
	 */
	protected $front_end;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class ];
	}

	/**
	 * Constructs the AMP integration
	 *
	 * @param Front_End_Integration $front_end The front end integration.
	 */
	public function __construct( Front_End_Integration $front_end ) {
		$this->front_end = $front_end;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'amp_post_template_head', [ $this, 'remove_amp_meta_output' ], 0 );
		\add_action( 'amp_post_template_head', [ $this->front_end, 'call_wpseo_head' ], 9 );
	}

	/**
	 * Removes amp meta output.
	 *
	 * @return void
	 */
	public function remove_amp_meta_output() {
		\remove_action( 'amp_post_template_head', 'amp_post_template_add_title' );
		\remove_action( 'amp_post_template_head', 'amp_post_template_add_canonical' );
		\remove_action( 'amp_post_template_head', 'amp_print_schemaorg_metadata' );
	}
}
