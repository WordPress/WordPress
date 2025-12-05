<?php

namespace Yoast\WP\SEO\Wrappers;

use WP_Query;

/**
 * Wrapper for WP_Query.
 */
class WP_Query_Wrapper {

	/**
	 * Returns the global WP_Query object.
	 *
	 * @return WP_Query The WP_Query object.
	 */
	public function get_query() {
		return $GLOBALS['wp_query'];
	}

	/**
	 * Returns the global main WP_Query object.
	 *
	 * @return WP_Query The WP_Query object.
	 */
	public function get_main_query() {
		return $GLOBALS['wp_the_query'];
	}

	/**
	 * Sets the global WP_Query object.
	 *
	 * @param WP_Query $wp_query The WP Query.
	 *
	 * @return void
	 */
	public function set_query( WP_Query $wp_query ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- This is a deliberate action.
		$GLOBALS['wp_query'] = $wp_query;
	}

	/**
	 * Resets the global WP_Query object.
	 *
	 * @return void
	 */
	public function reset_query() {
		\wp_reset_query();
	}
}
