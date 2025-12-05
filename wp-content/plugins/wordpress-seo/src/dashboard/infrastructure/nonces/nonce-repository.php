<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Nonces;

/**
 * Repository for WP nonces.
 */
class Nonce_Repository {

	/**
	 * Creates the nonce for a WP REST request.
	 *
	 * @return string
	 */
	public function get_rest_nonce(): string {
		return \wp_create_nonce( 'wp_rest' );
	}
}
