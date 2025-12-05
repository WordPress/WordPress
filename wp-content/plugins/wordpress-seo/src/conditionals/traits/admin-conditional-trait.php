<?php

namespace Yoast\WP\SEO\Conditionals\Traits;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;

/**
 * Trait for all integration that rely on the Admin-conditional
 */
trait Admin_Conditional_Trait {

	/**
	 * Returns an empty array, meaning no conditionals are required to load whatever uses this trait.
	 *
	 * @return array The conditionals that must be met to load this.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}
}
