<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Abstract class for creating conditionals based on feature flags.
 */
class Premium_Inactive_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return ! \YoastSEO()->helpers->product->is_premium();
	}
}
