<?php // phpcs:ignore Yoast.Files.FileName.InvalidClassFileName -- Reason: this explicitly concerns the Yoast tools page.

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when current page is the tools page.
 */
class User_Profile_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		global $pagenow;

		if ( $pagenow !== 'profile.php' ) {
			return false;
		}

		return true;
	}
}
