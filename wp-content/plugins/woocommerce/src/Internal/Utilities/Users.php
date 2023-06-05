<?php

namespace Automattic\WooCommerce\Internal\Utilities;

/**
 * Helper functions for working with users.
 */
class Users {
	/**
	 * Indicates if the user qualifies as site administrator.
	 *
	 * In the context of multisite networks, this means that they must have the `manage_sites`
	 * capability. In all other cases, they must have the `manage_options` capability.
	 *
	 * @param int $user_id Optional, used to specify a specific user (otherwise we look at the current user).
	 *
	 * @return bool
	 */
	public static function is_site_administrator( int $user_id = 0 ): bool {
		$user = 0 === $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id );

		if ( false === $user ) {
			return false;
		}

		return is_multisite() ? $user->has_cap( 'manage_sites' ) : $user->has_cap( 'manage_options' );
	}
}
