<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Class Premium_Active_Conditional.
 */
class Premium_Active_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return \YoastSEO()->helpers->product->is_premium();
	}
}
