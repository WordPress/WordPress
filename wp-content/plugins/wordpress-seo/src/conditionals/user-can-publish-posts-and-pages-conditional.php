<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when the current user has the `wpseo_manage_options` capability.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class User_Can_Publish_Posts_And_Pages_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return \current_user_can( 'publish_posts' ) && \current_user_can( 'publish_pages' );
	}
}
