<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when we're on Yoast's WP tests.
 */
class WP_Tests_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return \defined( 'YOAST_WP_TESTS' ) && \YOAST_WP_TESTS;
	}
}
