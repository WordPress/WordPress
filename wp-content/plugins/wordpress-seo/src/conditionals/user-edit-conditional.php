<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when current page is the Edit User page or the User's profile page.
 */
class User_Edit_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		global $pagenow;

		if ( $pagenow !== 'profile.php' && $pagenow !== 'user-edit.php' ) {
			return false;
		}

		return true;
	}
}
