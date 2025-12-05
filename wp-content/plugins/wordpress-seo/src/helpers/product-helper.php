<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * A helper object for the Yoast products.
 */
class Product_Helper {

	/**
	 * Gets the product name.
	 *
	 * @return string
	 */
	public function get_product_name() {
		if ( $this->is_premium() ) {
			return 'Yoast SEO Premium';
		}

		return 'Yoast SEO';
	}

	/**
	 * Gets the product name in the head section.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_product_name() . ' plugin';
	}

	/**
	 * Checks if the installed version is Yoast SEO Premium.
	 *
	 * @return bool True when is premium.
	 */
	public function is_premium() {
		return \defined( 'WPSEO_PREMIUM_FILE' );
	}

	/**
	 * Gets the Premium version if defined, returns null otherwise.
	 *
	 * @return string|null The Premium version or null when premium version is not defined.
	 */
	public function get_premium_version() {
		if ( \defined( 'WPSEO_PREMIUM_VERSION' ) ) {
			return \WPSEO_PREMIUM_VERSION;
		}

		return null;
	}

	/**
	 * Gets the version.
	 *
	 * @return string The version.
	 */
	public function get_version() {
		return \WPSEO_VERSION;
	}
}
