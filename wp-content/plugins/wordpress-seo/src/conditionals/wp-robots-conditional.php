<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Class that checks if wp_robots exists.
 */
class WP_Robots_Conditional implements Conditional {

	/**
	 * Checks if the wp_robots function exists.
	 *
	 * @return bool True when the wp_robots function exists.
	 */
	public function is_met() {
		return \function_exists( 'wp_robots' );
	}
}
