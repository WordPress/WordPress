<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when the current request uses the GET method.
 */
class Get_Request_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'GET' ) {
			return true;
		}

		return false;
	}
}
