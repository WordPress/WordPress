<?php

namespace Yoast\WP\SEO\Wrappers;

use WP_Rewrite;

/**
 * Wrapper for WP_Rewrite.
 */
class WP_Rewrite_Wrapper {

	/**
	 * Returns the global WP_Rewrite_Wrapper object.
	 *
	 * @return WP_Rewrite The WP_Query object.
	 */
	public function get() {
		return $GLOBALS['wp_rewrite'];
	}
}
