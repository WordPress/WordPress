<?php

namespace Yoast\WP\SEO\Introductions\Application;

trait Current_Page_Trait {

	/**
	 * Determines whether the current page is applicable.
	 *
	 * @param string[] $pages The applicable pages.
	 *
	 * @return bool Whether the current page is applicable.
	 */
	private function is_on_yoast_page( $pages ) {
		return \in_array( $this->get_page(), $pages, true );
	}

	/**
	 * Determines whether the current page is one of our installation pages.
	 *
	 * @return bool Whether the current page is one of our installation pages.
	 */
	private function is_on_installation_page() {
		return $this->is_on_yoast_page( [ 'wpseo_installation_successful_free', 'wpseo_installation_successful' ] );
	}

	/**
	 * Retrieve the page variable.
	 *
	 * Note: the result is not safe to use in anything than strict comparisons!
	 *
	 * @return string The page variable.
	 */
	private function get_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && \is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, only using it in strict comparison.
			return \wp_unslash( $_GET['page'] );
		}

		return '';
	}
}
