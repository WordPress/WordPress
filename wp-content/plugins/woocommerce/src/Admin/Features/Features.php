<?php
/**
 * Features loader for features developed in WooCommerce Admin.
 */

namespace Automattic\WooCommerce\Admin\Features;

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Internal\Admin\Loader;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

/**
 * Features Class.
 */
class Features {
	/**
	 * Class instance.
	 *
	 * @var Loader instance
	 */
	protected static $instance = null;

	/**
	 * Optional features
	 *
	 * @var array
	 */
	protected static $optional_features = array(
		'navigation'                 => array( 'default' => 'no' ),
		'settings'                   => array( 'default' => 'no' ),
		'analytics'                  => array( 'default' => 'yes' ),
		'remote-inbox-notifications' => array( 'default' => 'yes' ),
	);

	/**
	 * Beta features
	 *
	 * @var array
	 */
	protected static $beta_features = array(
		'navigation',
		'new-product-management-experience',
		'settings',
	);

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_internal_class_aliases();
		// Load feature before WooCommerce update hooks.
		add_action( 'init', array( __CLASS__, 'load_features' ), 4 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_load_beta_features_modal' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ), 15 );
		add_filter( 'admin_body_class', array( __CLASS__, 'add_admin_body_classes' ) );
		add_filter( 'update_option_woocommerce_allow_tracking', array( __CLASS__, 'maybe_disable_features' ), 10, 2 );
	}

	/**
	 * Gets a build configured array of enabled WooCommerce Admin features/sections, but does not respect optionally disabled features.
	 *
	 * @return array Enabled Woocommerce Admin features/sections.
	 */
	public static function get_features() {
		return apply_filters( 'woocommerce_admin_features', array() );
	}

	/**
	 * Gets the optional feature options as an associative array that can be toggled on or off.
	 *
	 * @return array
	 */
	public static function get_optional_feature_options() {
		$features = [];

		foreach ( array_keys( self::$optional_features ) as $optional_feature_key ) {
			$feature_class = self::get_feature_class( $optional_feature_key );

			if ( $feature_class ) {
				$features[ $optional_feature_key ] = $feature_class::TOGGLE_OPTION_NAME;
			}
		}
		return $features;
	}

	/**
	 * Returns if a specific wc-admin feature exists in the current environment.
	 *
	 * @param  string $feature Feature slug.
	 * @return bool Returns true if the feature exists.
	 */
	public static function exists( $feature ) {
		$features = self::get_features();
		return in_array( $feature, $features, true );
	}

	/**
	 * Get the feature class as a string.
	 *
	 * @param string $feature Feature name.
	 * @return string|null
	 */
	public static function get_feature_class( $feature ) {
		$feature       = str_replace( '-', '', ucwords( strtolower( $feature ), '-' ) );
		$feature_class = 'Automattic\\WooCommerce\\Admin\\Features\\' . $feature;

		if ( class_exists( $feature_class ) ) {
			return $feature_class;
		}

		// Handle features contained in subdirectory.
		if ( class_exists( $feature_class . '\\Init' ) ) {
			return $feature_class . '\\Init';
		}

		return null;
	}

	/**
	 * Class loader for enabled WooCommerce Admin features/sections.
	 */
	public static function load_features() {
		$features = self::get_features();
		foreach ( $features as $feature ) {
			$feature_class = self::get_feature_class( $feature );

			if ( $feature_class ) {
				new $feature_class();
			}
		}
	}

	/**
	 * Gets a build configured array of enabled WooCommerce Admin respecting optionally disabled features.
	 *
	 * @return array Enabled Woocommerce Admin features/sections.
	 */
	public static function get_available_features() {
		$features                      = self::get_features();
		$optional_feature_keys         = array_keys( self::$optional_features );
		$optional_features_unavailable = [];

		/**
		 * Filter allowing WooCommerce Admin optional features to be disabled.
		 *
		 * @param bool $disabled False.
		 */
		if ( apply_filters( 'woocommerce_admin_disabled', false ) ) {
			return array_values( array_diff( $features, $optional_feature_keys ) );
		}

		foreach ( $optional_feature_keys as $optional_feature_key ) {
			$feature_class = self::get_feature_class( $optional_feature_key );

			if ( $feature_class ) {
				$default = isset( self::$optional_features[ $optional_feature_key ]['default'] ) ?
					self::$optional_features[ $optional_feature_key ]['default'] :
					'no';

				// Check if the feature is currently being enabled, if it is continue.
				/* phpcs:disable WordPress.Security.NonceVerification */
				$feature_option = $feature_class::TOGGLE_OPTION_NAME;
				if ( isset( $_POST[ $feature_option ] ) && '1' === $_POST[ $feature_option ] ) {
					continue;
				}

				if ( 'yes' !== get_option( $feature_class::TOGGLE_OPTION_NAME, $default ) ) {
					$optional_features_unavailable[] = $optional_feature_key;
				}
			}
		}

		return array_values( array_diff( $features, $optional_features_unavailable ) );
	}

	/**
	 * Check if a feature is enabled.
	 *
	 * @param string $feature Feature slug.
	 * @return bool
	 */
	public static function is_enabled( $feature ) {
		$available_features = self::get_available_features();
		return in_array( $feature, $available_features, true );
	}

	/**
	 * Enable a toggleable optional feature.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public static function enable( $feature ) {
		$features = self::get_optional_feature_options();

		if ( isset( $features[ $feature ] ) ) {
			update_option( $features[ $feature ], 'yes' );
			return true;
		}

		return false;
	}

	/**
	 * Disable a toggleable optional feature.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public static function disable( $feature ) {
		$features = self::get_optional_feature_options();

		if ( isset( $features[ $feature ] ) ) {
			update_option( $features[ $feature ], 'no' );
			return true;
		}

		return false;
	}

	/**
	 * Disable features when opting out of tracking.
	 *
	 * @param string $old_value Old value.
	 * @param string $value New value.
	 */
	public static function maybe_disable_features( $old_value, $value ) {
		if ( 'yes' === $value ) {
			return;
		}

		foreach ( self::$beta_features as $feature ) {
			self::disable( $feature );
		}
	}

	/**
	 * Adds the Features section to the advanced tab of WooCommerce Settings
	 *
	 * @deprecated 7.0 The WooCommerce Admin features are now handled by the WooCommerce features engine (see the FeaturesController class).
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public static function add_features_section( $sections ) {
		return $sections;
	}

	/**
	 * Adds the Features settings.
	 *
	 * @deprecated 7.0 The WooCommerce Admin features are now handled by the WooCommerce features engine (see the FeaturesController class).
	 *
	 * @param array  $settings Settings.
	 * @param string $current_section Current section slug.
	 * @return array
	 */
	public static function add_features_settings( $settings, $current_section ) {
		return $settings;
	}

	/**
	 * Conditionally loads the beta features tracking modal.
	 *
	 * @param string $hook Page hook.
	 */
	public static function maybe_load_beta_features_modal( $hook ) {
		if (
			'woocommerce_page_wc-settings' !== $hook ||
			! isset( $_GET['tab'] ) || 'advanced' !== $_GET['tab'] || // phpcs:ignore CSRF ok.
			! isset( $_GET['section'] ) || 'features' !== $_GET['section'] // phpcs:ignore CSRF ok.
		) {
			return;
		}
		$tracking_enabled = get_option( 'woocommerce_allow_tracking', 'no' );

		if ( empty( self::$beta_features ) ) {
			return;
		}

		if ( 'yes' === $tracking_enabled ) {
			return;
		}

		$rtl = is_rtl() ? '.rtl' : '';

		wp_enqueue_style(
			'wc-admin-beta-features-tracking-modal',
			WCAdminAssets::get_url( "beta-features-tracking-modal/style{$rtl}", 'css' ),
			array( 'wp-components' ),
			WCAdminAssets::get_file_version( 'css' )
		);

		wp_enqueue_script(
			'wc-admin-beta-features-tracking-modal',
			WCAdminAssets::get_url( 'wp-admin-scripts/beta-features-tracking-modal', 'js' ),
			array( 'wp-i18n', 'wp-element', WC_ADMIN_APP ),
			WCAdminAssets::get_file_version( 'js' ),
			true
		);
	}

	/**
	 * Loads the required scripts on the correct pages.
	 */
	public static function load_scripts() {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return;
		}

		$features         = self::get_features();
		$enabled_features = array();
		foreach ( $features as $key ) {
			$enabled_features[ $key ] = self::is_enabled( $key );
		}
		wp_add_inline_script( WC_ADMIN_APP, 'window.wcAdminFeatures = ' . wp_json_encode( $enabled_features ), 'before' );
	}


	/**
	 * Adds body classes to the main wp-admin wrapper, allowing us to better target elements in specific scenarios.
	 *
	 * @param string $admin_body_class Body class to add.
	 */
	public static function add_admin_body_classes( $admin_body_class = '' ) {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return $admin_body_class;
		}

		$classes = explode( ' ', trim( $admin_body_class ) );

		$features = self::get_features();
		foreach ( $features as $feature_key ) {
			$classes[] = sanitize_html_class( 'woocommerce-feature-enabled-' . $feature_key );
		}

		$admin_body_class = implode( ' ', array_unique( $classes ) );
		return " $admin_body_class ";
	}

	/**
	 * Alias internal features classes to make them backward compatible.
	 * We've moved our feature classes to src-internal as part of merging this
	 * repository with WooCommerce Core to form a monorepo.
	 * See https://wp.me/p90Yrv-2HY for details.
	 */
	private function register_internal_class_aliases() {
		$aliases = array(
			// new class => original class (this will be aliased).
			'Automattic\WooCommerce\Internal\Admin\WCPayPromotion\Init' => 'Automattic\WooCommerce\Admin\Features\WcPayPromotion\Init',
			'Automattic\WooCommerce\Internal\Admin\RemoteFreeExtensions\Init' => 'Automattic\WooCommerce\Admin\Features\RemoteFreeExtensions\Init',
			'Automattic\WooCommerce\Internal\Admin\ActivityPanels' => 'Automattic\WooCommerce\Admin\Features\ActivityPanels',
			'Automattic\WooCommerce\Internal\Admin\Analytics' => 'Automattic\WooCommerce\Admin\Features\Analytics',
			'Automattic\WooCommerce\Internal\Admin\Coupons' => 'Automattic\WooCommerce\Admin\Features\Coupons',
			'Automattic\WooCommerce\Internal\Admin\CouponsMovedTrait' => 'Automattic\WooCommerce\Admin\Features\CouponsMovedTrait',
			'Automattic\WooCommerce\Internal\Admin\CustomerEffortScoreTracks' => 'Automattic\WooCommerce\Admin\Features\CustomerEffortScoreTracks',
			'Automattic\WooCommerce\Internal\Admin\Homescreen' => 'Automattic\WooCommerce\Admin\Features\Homescreen',
			'Automattic\WooCommerce\Internal\Admin\Marketing' => 'Automattic\WooCommerce\Admin\Features\Marketing',
			'Automattic\WooCommerce\Internal\Admin\MobileAppBanner' => 'Automattic\WooCommerce\Admin\Features\MobileAppBanner',
			'Automattic\WooCommerce\Internal\Admin\RemoteInboxNotifications' => 'Automattic\WooCommerce\Admin\Features\RemoteInboxNotifications',
			'Automattic\WooCommerce\Internal\Admin\SettingsNavigationFeature' => 'Automattic\WooCommerce\Admin\Features\Settings',
			'Automattic\WooCommerce\Internal\Admin\ShippingLabelBanner' => 'Automattic\WooCommerce\Admin\Features\ShippingLabelBanner',
			'Automattic\WooCommerce\Internal\Admin\ShippingLabelBannerDisplayRules' => 'Automattic\WooCommerce\Admin\Features\ShippingLabelBannerDisplayRules',
			'Automattic\WooCommerce\Internal\Admin\WcPayWelcomePage' => 'Automattic\WooCommerce\Admin\Features\WcPayWelcomePage',
		);
		foreach ( $aliases as $new_class => $orig_class ) {
			class_alias( $new_class, $orig_class );
		}
	}
}
