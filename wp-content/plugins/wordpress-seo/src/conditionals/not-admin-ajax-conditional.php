<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when not in a admin-ajax request.
 */
class Not_Admin_Ajax_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return ( ! \wp_doing_ajax() );
	}
}
