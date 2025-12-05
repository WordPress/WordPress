<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * Represents helper methods for WooCommerce.
 */
class Woocommerce_Helper {

	/**
	 * Checks if WooCommerce is active.
	 *
	 * @return bool Is WooCommerce active.
	 */
	public function is_active() {
		return \class_exists( 'WooCommerce' );
	}

	/**
	 * Returns the id of the set WooCommerce shop page.
	 *
	 * @return int The ID of the set page.
	 */
	public function get_shop_page_id() {
		if ( ! \function_exists( 'wc_get_page_id' ) ) {
			return -1;
		}

		return \wc_get_page_id( 'shop' );
	}

	/**
	 * Checks if the current page is a WooCommerce shop page.
	 *
	 * @return bool True when the page is a shop page.
	 */
	public function is_shop_page() {
		if ( ! \function_exists( 'is_shop' ) ) {
			return false;
		}

		if ( ! \is_shop() ) {
			return false;
		}

		if ( \is_search() ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the current page is a WooCommerce shop page.
	 *
	 * @return bool True when the page is a shop page.
	 */
	public function current_post_is_terms_and_conditions_page() {
		if ( ! \function_exists( 'wc_terms_and_conditions_page_id' ) ) {
			return false;
		}

		global $post;

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		return \intval( $post->ID ) === \intval( \wc_terms_and_conditions_page_id() );
	}
}
