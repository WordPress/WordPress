<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when the Polylang plugin is active.
 */
class Polylang_Conditional implements Conditional {

	/**
	 * Checks whether the Polylang plugin is installed and active.
	 *
	 * @return bool Whether Polylang is installed and active.
	 */
	public function is_met() {
		return \defined( 'POLYLANG_FILE' );
	}
}
