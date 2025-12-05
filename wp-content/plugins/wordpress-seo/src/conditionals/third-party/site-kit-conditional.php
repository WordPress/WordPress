<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when the SiteKit plugin is active.
 */
class Site_Kit_Conditional implements Conditional {

	/**
	 * Checks whether the SiteKit plugin is active.
	 *
	 * @return bool Whether the SiteKit plugin is active.
	 */
	public function is_met() {
		return \defined( 'GOOGLESITEKIT_VERSION' );
	}
}
