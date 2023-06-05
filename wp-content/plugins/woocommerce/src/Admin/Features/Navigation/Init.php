<?php
/**
 * Navigation Experience
 *
 * @package Woocommerce Admin
 */

namespace Automattic\WooCommerce\Admin\Features\Navigation;

use Automattic\WooCommerce\Internal\Admin\Survey;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\CoreMenu;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

/**
 * Contains logic for the Navigation
 */
class Init {
	/**
	 * Option name used to toggle this feature.
	 */
	const TOGGLE_OPTION_NAME = 'woocommerce_navigation_enabled';

	/**
	 * Determines if the feature has been toggled on or off.
	 *
	 * @var boolean
	 */
	protected static $is_updated = false;

	/**
	 * Hook into WooCommerce.
	 */
	public function __construct() {
		add_action( 'update_option_' . self::TOGGLE_OPTION_NAME, array( $this, 'reload_page_on_toggle' ), 10, 2 );
		add_action( 'woocommerce_settings_saved', array( $this, 'maybe_reload_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_opt_out_scripts' ) );

		if ( Features::is_enabled( 'navigation' ) ) {
			Menu::instance()->init();
			CoreMenu::instance()->init();
			Screen::instance()->init();
		}
	}

	/**
	 * Add the feature toggle to the features settings.
	 *
	 * @deprecated 7.0 The WooCommerce Admin features are now handled by the WooCommerce features engine (see the FeaturesController class).
	 *
	 * @param array $features Feature sections.
	 * @return array
	 */
	public static function add_feature_toggle( $features ) {
		return $features;
	}

	/**
	 * Determine if sufficient versions are present to support Navigation feature
	 */
	public function is_nav_compatible() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		$gutenberg_minimum_version = '9.0.0'; // https://github.com/WordPress/gutenberg/releases/tag/v9.0.0.
		$wp_minimum_version        = '5.6';
		$has_gutenberg             = is_plugin_active( 'gutenberg/gutenberg.php' );
		$gutenberg_version         = $has_gutenberg ? get_plugin_data( WP_PLUGIN_DIR . '/gutenberg/gutenberg.php' )['Version'] : false;

		if ( $gutenberg_version && version_compare( $gutenberg_version, $gutenberg_minimum_version, '>=' ) ) {
			return true;
		}

		// Get unmodified $wp_version.
		include ABSPATH . WPINC . '/version.php';

		// Strip '-src' from the version string. Messes up version_compare().
		$wp_version = str_replace( '-src', '', $wp_version );

		if ( version_compare( $wp_version, $wp_minimum_version, '>=' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Reloads the page when the option is toggled to make sure all nav features are loaded.
	 *
	 * @param string $old_value Old value.
	 * @param string $value     New value.
	 */
	public static function reload_page_on_toggle( $old_value, $value ) {
		if ( $old_value === $value ) {
			return;
		}

		if ( 'yes' !== $value ) {
			update_option( 'woocommerce_navigation_show_opt_out', 'yes' );
		}

		self::$is_updated = true;
	}

	/**
	 * Reload the page if the setting has been updated.
	 */
	public static function maybe_reload_page() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || ! self::$is_updated ) {
			return;
		}

		wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		exit();
	}

	/**
	 * Enqueue the opt out scripts.
	 */
	public function maybe_enqueue_opt_out_scripts() {
		if ( get_option( 'woocommerce_navigation_show_opt_out', 'no' ) !== 'yes' ) {
			return;
		}

		$rtl = is_rtl() ? '.rtl' : '';
		wp_enqueue_style(
			'wc-admin-navigation-opt-out',
			WCAdminAssets::get_url( "navigation-opt-out/style{$rtl}", 'css' ),
			array( 'wp-components' ),
			WCAdminAssets::get_file_version( 'css' )
		);

		WCAdminAssets::register_script( 'wp-admin-scripts', 'navigation-opt-out', true );
		wp_localize_script(
			'wc-admin-navigation-opt-out',
			'surveyData',
			array(
				'url' => Survey::get_url( '/new-navigation-opt-out' ),
			)
		);
		delete_option( 'woocommerce_navigation_show_opt_out' );
	}
}
