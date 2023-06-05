<?php
/**
 * Handles running payment method specs
 */

namespace Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions\DefaultFreeExtensions;

/**
 * Remote Payment Methods engine.
 * This goes through the specs and gets eligible payment methods.
 */
class Init {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_updated', array( __CLASS__, 'delete_specs_transient' ) );
	}

	/**
	 * Go through the specs and run them.
	 *
	 * @param array $allowed_bundles Optional array of allowed bundles to be returned.
	 * @return array
	 */
	public static function get_extensions( $allowed_bundles = array() ) {
		$bundles = array();
		$specs   = self::get_specs();

		foreach ( $specs as $spec ) {
			$spec              = (object) $spec;
			$bundle            = (array) $spec;
			$bundle['plugins'] = array();

			if ( ! empty( $allowed_bundles ) && ! in_array( $spec->key, $allowed_bundles, true ) ) {
				continue;
			}

			foreach ( $spec->plugins as $plugin ) {
				$extension = EvaluateExtension::evaluate( (object) $plugin );

				if ( ! property_exists( $extension, 'is_visible' ) || $extension->is_visible ) {
					$bundle['plugins'][] = $extension;
				}
			}

			$bundles[] = $bundle;
		}

		return $bundles;
	}

	/**
	 * Delete the specs transient.
	 */
	public static function delete_specs_transient() {
		RemoteFreeExtensionsDataSourcePoller::get_instance()->delete_specs_transient();
	}

	/**
	 * Get specs or fetch remotely if they don't exist.
	 */
	public static function get_specs() {
		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return DefaultFreeExtensions::get_all();
		}
		$specs = RemoteFreeExtensionsDataSourcePoller::get_instance()->get_specs_from_data_sources();

		// Fetch specs if they don't yet exist.
		if ( false === $specs || ! is_array( $specs ) || 0 === count( $specs ) ) {
			return DefaultFreeExtensions::get_all();
		}

		return $specs;
	}
}
