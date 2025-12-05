<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Trait for integrations that do not have any conditionals.
 */
trait No_Conditionals {

	/**
	 * Returns an empty array, meaning no conditionals are required to load whatever uses this trait.
	 *
	 * @return array The conditionals that must be met to load this.
	 */
	public static function get_conditionals() {
		return [];
	}
}
