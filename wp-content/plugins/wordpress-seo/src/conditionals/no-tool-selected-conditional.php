<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when current page is not a specific tool's page.
 */
class No_Tool_Selected_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We just check whether a URL parameter does not exist.
		return ! isset( $_GET['tool'] );
	}
}
