<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional interface, used to prevent integrations from loading.
 */
interface Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met();
}
