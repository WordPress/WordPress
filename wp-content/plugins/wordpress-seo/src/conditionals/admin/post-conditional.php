<?php

namespace Yoast\WP\SEO\Conditionals\Admin;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when on a post edit or new post page.
 */
class Post_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		global $pagenow;

		// Current page is the creation of a new post (type, i.e. post, page, custom post or attachment).
		if ( $pagenow === 'post-new.php' ) {
			return true;
		}

		// Current page is the edit page of an existing post (type, i.e. post, page, custom post or attachment).
		if ( $pagenow === 'post.php' ) {
			return true;
		}

		return false;
	}
}
