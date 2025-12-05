<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is met when the Elementor plugin is installed and activated.
 */
class Elementor_Activated_Conditional implements Conditional {

	/**
	 * Checks if the Elementor plugins is installed and activated.
	 *
	 * @return bool `true` when the Elementor plugin is installed and activated.
	 */
	public function is_met() {
		return \defined( 'ELEMENTOR__FILE__' );
	}
}
