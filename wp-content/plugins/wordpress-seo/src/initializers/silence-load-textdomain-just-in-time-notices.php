<?php

namespace Yoast\WP\SEO\Initializers;

use Yoast\WP\SEO\Conditionals\WP_Tests_Conditional;

/**
 * Silences load_textdomain_just_in_time issues when running WP tests.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Silence_Load_Textdomain_Just_In_Time_Notices implements Initializer_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string> The array of conditionals.
	 */
	public static function get_conditionals() {
		return [ WP_Tests_Conditional::class ];
	}

	/**
	 * Hooks our required hooks.
	 *
	 * @return void
	 */
	public function initialize() {
		\add_filter( 'doing_it_wrong_trigger_error', [ $this, 'silence_textdomain_notices' ], 10, 2 );
	}

	/**
	 * Silences textdomain notices.
	 *
	 * @param bool   $trigger       Whether to trigger the error. Default true.
	 * @param string $function_name The function name that triggered the error.
	 *
	 * @return bool
	 */
	public function silence_textdomain_notices( $trigger, $function_name ) {
		if ( $function_name === '_load_textdomain_just_in_time' ) {
			// Silence the notice.
			return false;
		}

		return $trigger;
	}
}
