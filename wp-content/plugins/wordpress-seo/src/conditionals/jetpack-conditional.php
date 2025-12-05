<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when Jetpack exists.
 */
class Jetpack_Conditional implements Conditional {

	/**
	 * Returns `true` when the Jetpack plugin exists on this
	 * WordPress installation.
	 *
	 * @return bool `true` when the Jetpack plugin exists on this WordPress installation.
	 */
	public function is_met() {
		return \class_exists( 'Jetpack' );
	}
}
