<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when in the admin.
 */
class W3_Total_Cache_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		if ( ! \defined( 'W3TC_DIR' ) ) {
			return false;
		}

		return \function_exists( 'w3tc_objectcache_flush' );
	}
}
