<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when on deactivating Yoast SEO.
 */
class Deactivating_Yoast_Seo_Conditional implements Conditional {

	/**
	 * Returns whether this conditional is met.
	 *
	 * @return bool Whether the conditional is met.
	 */
	public function is_met() {
		// phpcs:ignore WordPress.Security.NonceVerification -- We can't verify nonce since this might run from any user.
		if ( isset( $_GET['action'] ) && \sanitize_text_field( \wp_unslash( $_GET['action'] ) ) === 'deactivate' && isset( $_GET['plugin'] ) && \sanitize_text_field( \wp_unslash( $_GET['plugin'] === 'wordpress-seo/wp-seo.php' ) ) ) {
			return true;
		}

		return false;
	}
}
