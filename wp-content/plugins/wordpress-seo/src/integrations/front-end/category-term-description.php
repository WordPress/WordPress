<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Adds support for shortcodes to category and term descriptions.
 */
class Category_Term_Description implements Integration_Interface {

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
		\add_filter( 'category_description', [ $this, 'add_shortcode_support' ] );
		\add_filter( 'term_description', [ $this, 'add_shortcode_support' ] );
	}

	/**
	 * Adds shortcode support to category and term descriptions.
	 *
	 * This methods wrap in output buffering to prevent shortcodes that echo stuff
	 * instead of return from breaking things.
	 *
	 * @param string $description String to add shortcodes in.
	 *
	 * @return string Content with shortcodes filtered out.
	 */
	public function add_shortcode_support( $description ) {
		\ob_start();
		$description = \do_shortcode( $description );
		\ob_end_clean();

		return $description;
	}
}
