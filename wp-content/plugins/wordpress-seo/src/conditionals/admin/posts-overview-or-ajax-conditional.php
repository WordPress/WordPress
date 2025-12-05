<?php

namespace Yoast\WP\SEO\Conditionals\Admin;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when on a post overview page or during an ajax request.
 */
class Posts_Overview_Or_Ajax_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		global $pagenow;
		return $pagenow === 'edit.php' || \wp_doing_ajax();
	}
}
