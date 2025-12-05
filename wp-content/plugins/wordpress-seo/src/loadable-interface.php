<?php

namespace Yoast\WP\SEO;

/**
 * An interface for registering integrations with WordPress
 */
interface Loadable_Interface {

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals();
}
