<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when we aren't in a multisite setup.
 */
class Non_Multisite_Conditional implements Conditional {

	/**
	 * Returns `true` when we aren't in a multisite setup.
	 *
	 * @return bool `true` when we aren't in a multisite setup.
	 */
	public function is_met() {
		return ! \is_multisite();
	}
}
