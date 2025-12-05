<?php

namespace Yoast\WP\SEO\Conditionals;

use WPSEO_Utils;

/**
 * Conditional that is only met when in development mode.
 */
class Development_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return WPSEO_Utils::is_development_mode();
	}
}
