<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for redirects.
 */
class Redirect_Helper {

	/**
	 * Wraps wp_redirect to allow testing for redirects.
	 *
	 * @codeCoverageIgnore It only wraps a WordPress function.
	 *
	 * @param string $location The path to redirect to.
	 * @param int    $status   The status code to use.
	 * @param string $reason   The reason for the redirect.
	 *
	 * @return void
	 */
	public function do_unsafe_redirect( $location, $status = 302, $reason = 'Yoast SEO' ) {
		// phpcs:ignore WordPress.Security.SafeRedirect -- intentional, function has been renamed to make unsafe more clear.
		\wp_redirect( $location, $status, $reason );
		exit;
	}

	/**
	 * Wraps wp_safe_redirect to allow testing for safe redirects.
	 *
	 * @codeCoverageIgnore It only wraps a WordPress function.
	 *
	 * @param string $location The path to redirect to.
	 * @param int    $status   The status code to use.
	 * @param string $reason   The reason for the redirect.
	 *
	 * @return void
	 */
	public function do_safe_redirect( $location, $status = 302, $reason = 'Yoast SEO' ) {
		\wp_safe_redirect( $location, $status, $reason );
		exit;
	}

	/**
	 * Sets a header.
	 * This is a tiny helper function to enable better testing.
	 *
	 * @codeCoverageIgnore It only wraps a WordPress function.
	 *
	 * @param string $header The header to set.
	 *
	 * @return void
	 */
	public function set_header( $header ) {
		\header( $header );
	}

	/**
	 * Removes a header.
	 * This is a tiny helper function to enable better testing.
	 *
	 * @codeCoverageIgnore It only wraps a WordPress function.
	 *
	 * @param string $header The header to remove.
	 *
	 * @return void
	 */
	public function remove_header( $header ) {
		\header_remove( $header );
	}
}
