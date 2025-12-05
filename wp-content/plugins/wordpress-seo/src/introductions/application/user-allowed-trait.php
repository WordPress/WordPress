<?php

namespace Yoast\WP\SEO\Introductions\Application;

trait User_Allowed_Trait {

	/**
	 * Determines whether the user has the required capabilities.
	 *
	 * @param string[] $capabilities The required capabilities.
	 *
	 * @return bool Whether the user has the required capabilities.
	 */
	private function is_user_allowed( $capabilities ) {
		foreach ( $capabilities as $capability ) {
			if ( ! \current_user_can( $capability ) ) {
				return false;
			}
		}

		return true;
	}
}
