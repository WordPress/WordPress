<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when the current user has the `edit_users` capability.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class User_Can_Edit_Users_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return \current_user_can( 'edit_users' );
	}
}
