<?php

namespace Automattic\WooCommerce\Admin;

use Automattic\WooCommerce\Admin\DeprecatedClassFacade;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

/**
 * Loader Class.
 *
 * @deprecated since 6.3.0, use WooCommerce\Internal\Admin\Loader.
 */
class Loader extends DeprecatedClassFacade {

	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Internal\Admin\Loader';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '6.3.0';

	/**
	 * Returns if a specific wc-admin feature is enabled.
	 *
	 * @param  string $feature Feature slug.
	 * @return bool Returns true if the feature is enabled.
	 *
	 * @deprecated since 5.0.0, use Features::is_enabled( $feature )
	 */
	public static function is_feature_enabled( $feature ) {
		wc_deprecated_function( 'is_feature_enabled', '5.0', '\Automattic\WooCommerce\Internal\Admin\Features\Features::is_enabled()' );
		return Features::is_enabled( $feature );
	}

	/**
	 * Returns true if we are on a JS powered admin page or
	 * a "classic" (non JS app) powered admin page (an embedded page).
	 *
	 * @deprecated 6.3.0
	 */
	public static function is_admin_or_embed_page() {
		wc_deprecated_function( 'is_admin_or_embed_page', '6.3', '\Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page()' );
		return PageController::is_admin_or_embed_page();
	}

	/**
	 * Returns true if we are on a JS powered admin page.
	 *
	 * @deprecated 6.3.0
	 */
	public static function is_admin_page() {
		wc_deprecated_function( 'is_admin_page', '6.3', '\Automattic\WooCommerce\Admin\PageController::is_admin_page()' );
		return PageController::is_admin_page();
	}

	/**
	 * Returns true if we are on a "classic" (non JS app) powered admin page.
	 *
	 * @deprecated 6.3.0
	 */
	public static function is_embed_page() {
		wc_deprecated_function( 'is_embed_page', '6.3', '\Automattic\WooCommerce\Admin\PageController::is_embed_page()' );
		return PageController::is_embed_page();
	}

	/**
	 * Determines if a minified JS file should be served.
	 *
	 * @param  boolean $script_debug Only serve unminified files if script debug is on.
	 * @return boolean If js asset should use minified version.
	 *
	 * @deprecated since 6.3.0, use WCAdminAssets::should_use_minified_js_file( $script_debug )
	 */
	public static function should_use_minified_js_file( $script_debug ) {
		// Bail if WC isn't initialized (This can be called from WCAdmin's entrypoint).
		if ( ! defined( 'WC_ABSPATH' ) ) {
			return;
		}
		return WCAdminAssets::should_use_minified_js_file( $script_debug );
	}
}
