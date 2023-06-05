<?php
/**
 * FeaturesController class file
 */

namespace Automattic\WooCommerce\Internal\Features;

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Internal\Admin\Analytics;
use Automattic\WooCommerce\Admin\Features\Navigation\Init;
use Automattic\WooCommerce\Admin\Features\NewProductManagementExperience;
use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\PluginUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Class to define the WooCommerce features that can be enabled and disabled by admin users,
 * provides also a mechanism for WooCommerce plugins to declare that they are compatible
 * (or incompatible) with a given feature.
 */
class FeaturesController {

	use AccessiblePrivateMethods;

	public const FEATURE_ENABLED_CHANGED_ACTION = 'woocommerce_feature_enabled_changed';

	/**
	 * The existing feature definitions.
	 *
	 * @var array[]
	 */
	private $features;

	/**
	 * The registered compatibility info for WooCommerce plugins, with plugin names as keys.
	 *
	 * @var array
	 */
	private $compatibility_info_by_plugin;

	/**
	 * Ids of the legacy features (they existed before the features engine was implemented).
	 *
	 * @var array
	 */
	private $legacy_feature_ids;

	/**
	 * The registered compatibility info for WooCommerce plugins, with feature ids as keys.
	 *
	 * @var array
	 */
	private $compatibility_info_by_feature;

	/**
	 * The LegacyProxy instance to use.
	 *
	 * @var LegacyProxy
	 */
	private $proxy;

	/**
	 * The PluginUtil instance to use.
	 *
	 * @var PluginUtil
	 */
	private $plugin_util;

	/**
	 * Flag indicating that features will be enableable from the settings page
	 * even when they are incompatible with active plugins.
	 *
	 * @var bool
	 */
	private $force_allow_enabling_features = false;

	/**
	 * Flag indicating that plugins will be activable from the plugins page
	 * even when they are incompatible with enabled features.
	 *
	 * @var bool
	 */
	private $force_allow_enabling_plugins = false;

	/**
	 * Creates a new instance of the class.
	 */
	public function __construct() {
		$features = array(
			'analytics'              => array(
				'name'               => __( 'Analytics', 'woocommerce' ),
				'description'        => __( 'Enables WooCommerce Analytics', 'woocommerce' ),
				'is_experimental'    => false,
				'enabled_by_default' => true,
				'disable_ui'         => false,
			),
			'new_navigation'         => array(
				'name'            => __( 'Navigation', 'woocommerce' ),
				'description'     => __( 'Adds the new WooCommerce navigation experience to the dashboard', 'woocommerce' ),
				'is_experimental' => false,
				'disable_ui'      => false,
			),
			'new_product_management' => array(
				'name'            => __( 'New product editor', 'woocommerce' ),
				'description'     => __( 'Try the new product editor (Beta)', 'woocommerce' ),
				'is_experimental' => true,
				'disable_ui'      => false,
			),
			'custom_order_tables'    => array(
				'name'            => __( 'High-Performance order storage (COT)', 'woocommerce' ),
				'description'     => __( 'Enable the high performance order storage feature.', 'woocommerce' ),
				'is_experimental' => true,
				'disable_ui'      => false,
			),
			'cart_checkout_blocks'   => array(
				'name'            => __( 'Cart & Checkout Blocks', 'woocommerce' ),
				'description'     => __( 'Optimize for faster checkout', 'woocommerce' ),
				'is_experimental' => false,
				'disable_ui'      => true,
			),
		);

		$this->legacy_feature_ids = array( 'analytics', 'new_navigation', 'new_product_management' );

		$this->init_features( $features );

		self::add_filter( 'updated_option', array( $this, 'process_updated_option' ), 999, 3 );
		self::add_filter( 'added_option', array( $this, 'process_added_option' ), 999, 3 );
		self::add_filter( 'woocommerce_get_sections_advanced', array( $this, 'add_features_section' ), 10, 1 );
		self::add_filter( 'woocommerce_get_settings_advanced', array( $this, 'add_feature_settings' ), 10, 2 );
		self::add_filter( 'deactivated_plugin', array( $this, 'handle_plugin_deactivation' ), 10, 1 );
		self::add_filter( 'all_plugins', array( $this, 'filter_plugins_list' ), 10, 1 );
		self::add_action( 'admin_notices', array( $this, 'display_notices_in_plugins_page' ), 10, 0 );
		self::add_action( 'after_plugin_row', array( $this, 'handle_plugin_list_rows' ), 10, 2 );
		self::add_action( 'current_screen', array( $this, 'enqueue_script_to_fix_plugin_list_html' ), 10, 1 );
		self::add_filter( 'views_plugins', array( $this, 'handle_plugins_page_views_list' ), 10, 1 );
	}

	/**
	 * Initialize the class according to the existing features.
	 *
	 * @param array $features Information about the existing features.
	 */
	private function init_features( array $features ) {
		$this->compatibility_info_by_plugin  = array();
		$this->compatibility_info_by_feature = array();

		$this->features = $features;

		foreach ( array_keys( $this->features ) as $feature_id ) {
			$this->compatibility_info_by_feature[ $feature_id ] = array(
				'compatible'   => array(),
				'incompatible' => array(),
			);
		}
	}

	/**
	 * Initialize the class instance.
	 *
	 * @internal
	 *
	 * @param LegacyProxy $proxy The instance of LegacyProxy to use.
	 * @param PluginUtil  $plugin_util The instance of PluginUtil to use.
	 */
	final public function init( LegacyProxy $proxy, PluginUtil $plugin_util ) {
		$this->proxy       = $proxy;
		$this->plugin_util = $plugin_util;
	}

	/**
	 * Get all the existing WooCommerce features.
	 *
	 * Returns an associative array where keys are unique feature ids
	 * and values are arrays with these keys:
	 *
	 * - name (string)
	 * - description (string)
	 * - is_experimental (bool)
	 * - is_enabled (bool) (only if $include_enabled_info is passed as true)
	 *
	 * @param bool $include_experimental Include also experimental/work in progress features in the list.
	 * @param bool $include_enabled_info True to include the 'is_enabled' field in the returned features info.
	 * @returns array An array of information about existing features.
	 */
	public function get_features( bool $include_experimental = false, bool $include_enabled_info = false ): array {
		$features = $this->features;

		if ( ! $include_experimental ) {
			$features = array_filter(
				$features,
				function( $feature ) {
					return ! $feature['is_experimental'];
				}
			);
		}

		if ( $include_enabled_info ) {
			foreach ( array_keys( $features ) as $feature_id ) {
				$is_enabled                            = $this->feature_is_enabled( $feature_id );
				$features[ $feature_id ]['is_enabled'] = $is_enabled;
			}
		}

		return $features;
	}

	/**
	 * Check if a given feature is currently enabled.
	 *
	 * @param  string $feature_id Unique feature id.
	 * @return bool True if the feature is enabled, false if not or if the feature doesn't exist.
	 */
	public function feature_is_enabled( string $feature_id ): bool {
		if ( ! $this->feature_exists( $feature_id ) ) {
			return false;
		}

		$default_value = $this->feature_is_enabled_by_default( $feature_id ) ? 'yes' : 'no';
		return 'yes' === get_option( $this->feature_enable_option_name( $feature_id ), $default_value );
	}

	/**
	 * Check if a given feature is enabled by default.
	 *
	 * @param string $feature_id Unique feature id.
	 * @return boolean TRUE if the feature is enabled by default, FALSE otherwise.
	 */
	private function feature_is_enabled_by_default( string $feature_id ): bool {
		return ! empty( $this->features[ $feature_id ]['enabled_by_default'] );
	}

	/**
	 * Change the enabled/disabled status of a feature.
	 *
	 * @param string $feature_id Unique feature id.
	 * @param bool   $enable True to enable the feature, false to disable it.
	 * @return bool True on success, false if feature doesn't exist or the new value is the same as the old value.
	 */
	public function change_feature_enable( string $feature_id, bool $enable ): bool {
		if ( ! $this->feature_exists( $feature_id ) ) {
			return false;
		}

		return update_option( $this->feature_enable_option_name( $feature_id ), $enable ? 'yes' : 'no' );
	}

	/**
	 * Declare (in)compatibility with a given feature for a given plugin.
	 *
	 * This method MUST be executed from inside a handler for the 'before_woocommerce_init' hook.
	 *
	 * The plugin name is expected to be in the form 'directory/file.php' and be one of the keys
	 * of the array returned by 'get_plugins', but this won't be checked. Plugins are expected to use
	 * FeaturesUtil::declare_compatibility instead, passing the full plugin file path instead of the plugin name.
	 *
	 * @param string $feature_id Unique feature id.
	 * @param string $plugin_name Plugin name, in the form 'directory/file.php'.
	 * @param bool   $positive_compatibility True if the plugin declares being compatible with the feature, false if it declares being incompatible.
	 * @return bool True on success, false on error (feature doesn't exist or not inside the required hook).
	 * @throws \Exception A plugin attempted to declare itself as compatible and incompatible with a given feature at the same time.
	 */
	public function declare_compatibility( string $feature_id, string $plugin_name, bool $positive_compatibility = true ): bool {
		if ( ! $this->proxy->call_function( 'doing_action', 'before_woocommerce_init' ) ) {
			$class_and_method = ( new \ReflectionClass( $this ) )->getShortName() . '::' . __FUNCTION__;
			/* translators: 1: class::method 2: before_woocommerce_init */
			$this->proxy->call_function( 'wc_doing_it_wrong', $class_and_method, sprintf( __( '%1$s should be called inside the %2$s action.', 'woocommerce' ), $class_and_method, 'before_woocommerce_init' ), '7.0' );
			return false;
		}

		if ( ! $this->feature_exists( $feature_id ) ) {
			return false;
		}

		$plugin_name = str_replace( '\\', '/', $plugin_name );

		// Register compatibility by plugin.

		ArrayUtil::ensure_key_is_array( $this->compatibility_info_by_plugin, $plugin_name );

		$key          = $positive_compatibility ? 'compatible' : 'incompatible';
		$opposite_key = $positive_compatibility ? 'incompatible' : 'compatible';
		ArrayUtil::ensure_key_is_array( $this->compatibility_info_by_plugin[ $plugin_name ], $key );
		ArrayUtil::ensure_key_is_array( $this->compatibility_info_by_plugin[ $plugin_name ], $opposite_key );

		if ( in_array( $feature_id, $this->compatibility_info_by_plugin[ $plugin_name ][ $opposite_key ], true ) ) {
			throw new \Exception( "Plugin $plugin_name is trying to declare itself as $key with the '$feature_id' feature, but it already declared itself as $opposite_key" );
		}

		if ( ! in_array( $feature_id, $this->compatibility_info_by_plugin[ $plugin_name ][ $key ], true ) ) {
			$this->compatibility_info_by_plugin[ $plugin_name ][ $key ][] = $feature_id;
		}

		// Register compatibility by feature.

		$key = $positive_compatibility ? 'compatible' : 'incompatible';

		if ( ! in_array( $plugin_name, $this->compatibility_info_by_feature[ $feature_id ][ $key ], true ) ) {
			$this->compatibility_info_by_feature[ $feature_id ][ $key ][] = $plugin_name;
		}

		return true;
	}

	/**
	 * Check whether a feature exists with a given id.
	 *
	 * @param string $feature_id The feature id to check.
	 * @return bool True if the feature exists.
	 */
	private function feature_exists( string $feature_id ): bool {
		return isset( $this->features[ $feature_id ] );
	}

	/**
	 * Get the ids of the features that a certain plugin has declared compatibility for.
	 *
	 * This method can't be called before the 'woocommerce_init' hook is fired.
	 *
	 * @param string $plugin_name Plugin name, in the form 'directory/file.php'.
	 * @param bool   $enabled_features_only True to return only names of enabled plugins.
	 * @return array An array having a 'compatible' and an 'incompatible' key, each holding an array of feature ids.
	 */
	public function get_compatible_features_for_plugin( string $plugin_name, bool $enabled_features_only = false ) : array {
		$this->verify_did_woocommerce_init( __FUNCTION__ );

		$features = $this->features;
		if ( $enabled_features_only ) {
			$features = array_filter(
				$features,
				array( $this, 'feature_is_enabled' ),
				ARRAY_FILTER_USE_KEY
			);
		}

		if ( ! isset( $this->compatibility_info_by_plugin[ $plugin_name ] ) ) {
			return array(
				'compatible'   => array(),
				'incompatible' => array(),
				'uncertain'    => array_keys( $features ),
			);
		}

		$info                 = $this->compatibility_info_by_plugin[ $plugin_name ];
		$info['compatible']   = array_values( array_intersect( array_keys( $features ), $info['compatible'] ) );
		$info['incompatible'] = array_values( array_intersect( array_keys( $features ), $info['incompatible'] ) );
		$info['uncertain']    = array_values( array_diff( array_keys( $features ), $info['compatible'], $info['incompatible'] ) );

		return $info;
	}

	/**
	 * Get the names of the plugins that have been declared compatible or incompatible with a given feature.
	 *
	 * @param string $feature_id Feature id.
	 * @param bool   $active_only True to return only active plugins.
	 * @return array An array having a 'compatible' and an 'incompatible' key, each holding an array of plugin names.
	 */
	public function get_compatible_plugins_for_feature( string $feature_id, bool $active_only = false ) : array {
		$this->verify_did_woocommerce_init( __FUNCTION__ );

		$woo_aware_plugins = $this->plugin_util->get_woocommerce_aware_plugins( $active_only );
		if ( ! $this->feature_exists( $feature_id ) ) {
			return array(
				'compatible'   => array(),
				'incompatible' => array(),
				'uncertain'    => $woo_aware_plugins,
			);
		}

		$info              = $this->compatibility_info_by_feature[ $feature_id ];
		$info['uncertain'] = array_values( array_diff( $woo_aware_plugins, $info['compatible'], $info['incompatible'] ) );

		return $info;
	}

	/**
	 * Check if the 'woocommerce_init' has run or is running, do a 'wc_doing_it_wrong' if not.
	 *
	 * @param string|null $function Name of the invoking method, if not null, 'wc_doing_it_wrong' will be invoked if 'woocommerce_init' has not run and is not running.
	 * @return bool True if 'woocommerce_init' has run or is running, false otherwise.
	 */
	private function verify_did_woocommerce_init( string $function = null ): bool {
		if ( ! $this->proxy->call_function( 'did_action', 'woocommerce_init' ) &&
			! $this->proxy->call_function( 'doing_action', 'woocommerce_init' ) ) {
			if ( ! is_null( $function ) ) {
				$class_and_method = ( new \ReflectionClass( $this ) )->getShortName() . '::' . $function;
				/* translators: 1: class::method 2: plugins_loaded */
				$this->proxy->call_function( 'wc_doing_it_wrong', $class_and_method, sprintf( __( '%1$s should not be called before the %2$s action.', 'woocommerce' ), $class_and_method, 'woocommerce_init' ), '7.0' );
			}
			return false;
		}

		return true;
	}

	/**
	 * Get the name of the option that enables/disables a given feature.
	 * Note that it doesn't check if the feature actually exists.
	 *
	 * @param string $feature_id The id of the feature.
	 * @return string The option that enables or disables the feature.
	 */
	public function feature_enable_option_name( string $feature_id ): string {
		if ( 'analytics' === $feature_id ) {
			return Analytics::TOGGLE_OPTION_NAME;
		} elseif ( 'new_navigation' === $feature_id ) {
			return Init::TOGGLE_OPTION_NAME;
		} elseif ( 'new_product_management' === $feature_id ) {
			return NewProductManagementExperience::TOGGLE_OPTION_NAME;
		}

		return "woocommerce_feature_{$feature_id}_enabled";
	}

	/**
	 * Checks whether a feature id corresponds to a legacy feature
	 * (a feature that existed prior to the implementation of the features engine).
	 *
	 * @param string $feature_id The feature id to check.
	 * @return bool True if the id corresponds to a legacy feature.
	 */
	public function is_legacy_feature( string $feature_id ): bool {
		return in_array( $feature_id, $this->legacy_feature_ids, true );
	}

	/**
	 * Sets a flag indicating that it's allowed to enable features for which incompatible plugins are active
	 * from the WooCommerce feature settings page.
	 */
	public function allow_enabling_features_with_incompatible_plugins(): void {
		$this->force_allow_enabling_features = true;
	}

	/**
	 * Sets a flag indicating that it's allowed to activate plugins for which incompatible features are enabled
	 * from the WordPress plugins page.
	 */
	public function allow_activating_plugins_with_incompatible_features(): void {
		$this->force_allow_enabling_plugins = true;
	}

	/**
	 * Handler for the 'added_option' hook.
	 *
	 * It fires FEATURE_ENABLED_CHANGED_ACTION when a feature is enabled or disabled.
	 *
	 * @param string $option The option that has been created.
	 * @param mixed  $value The value of the option.
	 */
	private function process_added_option( string $option, $value ) {
		$this->process_updated_option( $option, false, $value );
	}

	/**
	 * Handler for the 'updated_option' hook.
	 *
	 * It fires FEATURE_ENABLED_CHANGED_ACTION when a feature is enabled or disabled.
	 *
	 * @param string $option The option that has been modified.
	 * @param mixed  $old_value The old value of the option.
	 * @param mixed  $value The new value of the option.
	 */
	private function process_updated_option( string $option, $old_value, $value ) {
		$matches = array();
		$success = preg_match( '/^woocommerce_feature_([a-zA-Z0-9_]+)_enabled$/', $option, $matches );

		if ( ! $success && Analytics::TOGGLE_OPTION_NAME !== $option && Init::TOGGLE_OPTION_NAME !== $option && NewProductManagementExperience::TOGGLE_OPTION_NAME !== $option ) {
			return;
		}

		if ( $value === $old_value ) {
			return;
		}

		if ( Analytics::TOGGLE_OPTION_NAME === $option ) {
			$feature_id = 'analytics';
		} elseif ( Init::TOGGLE_OPTION_NAME === $option ) {
			$feature_id = 'new_navigation';
		} elseif ( NewProductManagementExperience::TOGGLE_OPTION_NAME === $option ) {
			$feature_id = 'new_product_management';
		} else {
			$feature_id = $matches[1];
		}

		/**
		 * Action triggered when a feature is enabled or disabled (the value of the corresponding setting option is changed).
		 *
		 * @param string $feature_id The id of the feature.
		 * @param bool $enabled True if the feature has been enabled, false if it has been disabled.
		 *
		 * @since 7.0.0
		 */
		do_action( self::FEATURE_ENABLED_CHANGED_ACTION, $feature_id, 'yes' === $value );
	}

	/**
	 * Handler for the 'woocommerce_get_sections_advanced' hook,
	 * it adds the "Features" section to the advanced settings page.
	 *
	 * @param array $sections The original sections array.
	 * @return array The updated sections array.
	 */
	private function add_features_section( $sections ) {
		if ( ! isset( $sections['features'] ) ) {
			$sections['features'] = __( 'Features', 'woocommerce' );
		}
		return $sections;
	}

	/**
	 * Handler for the 'woocommerce_get_settings_advanced' hook,
	 * it adds the settings UI for all the existing features.
	 *
	 * Note that the settings added via the 'woocommerce_settings_features' hook will be
	 * displayed in the non-experimental features section.
	 *
	 * @param array  $settings The existing settings for the corresponding settings section.
	 * @param string $current_section The section to get the settings for.
	 * @return array The updated settings array.
	 */
	private function add_feature_settings( $settings, $current_section ): array {
		if ( 'features' !== $current_section ) {
			return $settings;
		}

		// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		/**
		 * Filter allowing WooCommerce Admin to be disabled.
		 *
		 * @param bool $disabled False.
		 */
		$admin_features_disabled = apply_filters( 'woocommerce_admin_disabled', false );
		// phpcs:enable WooCommerce.Commenting.CommentHooks.MissingSinceComment

		$feature_settings =
			array(
				array(
					'title' => __( 'Features', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'Start using new features that are being progressively rolled out to improve the store management experience.', 'woocommerce' ),
					'id'    => 'features_options',
				),
			);

		$features = $this->get_features( true );

		$feature_ids              = array_keys( $features );
		$experimental_feature_ids = array_filter(
			$feature_ids,
			function( $feature_id ) use ( $features ) {
				return $features[ $feature_id ]['is_experimental'];
			}
		);
		$mature_feature_ids       = array_diff( $feature_ids, $experimental_feature_ids );
		$feature_ids              = array_merge( $mature_feature_ids, array( 'mature_features_end' ), $experimental_feature_ids );

		foreach ( $feature_ids as $id ) {
			if ( 'mature_features_end' === $id ) {
				// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
				/**
				 * Filter allowing to add additional settings to the WooCommerce Advanced - Features settings page.
				 *
				 * @param bool $disabled False.
				 */
				$additional_features = apply_filters( 'woocommerce_settings_features', $features );
				// phpcs:enable WooCommerce.Commenting.CommentHooks.MissingSinceComment

				$feature_settings = array_merge( $feature_settings, $additional_features );

				if ( ! empty( $experimental_feature_ids ) ) {
					$feature_settings[] = array(
						'type' => 'sectionend',
						'id'   => 'features_options',
					);

					$feature_settings[] = array(
						'title' => __( 'Experimental features', 'woocommerce' ),
						'type'  => 'title',
						'desc'  => __( 'These features are either experimental or incomplete, enable them at your own risk!', 'woocommerce' ),
						'id'    => 'experimental_features_options',
					);
				}
				continue;
			}

			if ( isset( $features[ $id ]['disable_ui'] ) && $features[ $id ]['disable_ui'] ) {
				continue;
			}

			$feature_settings[] = $this->get_setting_for_feature( $id, $features[ $id ], $admin_features_disabled );
		}

		$feature_settings[] = array(
			'type' => 'sectionend',
			'id'   => empty( $experimental_feature_ids ) ? 'features_options' : 'experimental_features_options',
		);

		return $feature_settings;
	}

	/**
	 * Get the parameters to display the setting enable/disable UI for a given feature.
	 *
	 * @param string $feature_id The feature id.
	 * @param array  $feature The feature parameters, as returned by get_features.
	 * @param bool   $admin_features_disabled True if admin features have been disabled via 'woocommerce_admin_disabled' filter.
	 * @return array The parameters to add to the settings array.
	 */
	private function get_setting_for_feature( string $feature_id, array $feature, bool $admin_features_disabled ): array {
		$description = $feature['description'];
		$disabled    = false;
		$desc_tip    = '';
		$tooltip     = isset( $feature['tooltip'] ) ? $feature['tooltip'] : '';

		if ( ( 'analytics' === $feature_id || 'new_navigation' === $feature_id ) && $admin_features_disabled ) {
			$disabled = true;
			$desc_tip = __( 'WooCommerce Admin has been disabled', 'woocommerce' );
		} elseif ( 'new_navigation' === $feature_id ) {
			$disabled = ! $this->feature_is_enabled( $feature_id );

			if ( $disabled ) {
				$update_text = sprintf(
				// translators: 1: line break tag.
					__( '%1$s The development of this feature is currently on hold.', 'woocommerce' ),
					'<br/>'
				);
			} else {
				$update_text = sprintf(
				// translators: 1: line break tag.
					__(
						'%1$s This navigation will soon become unavailable while we make necessary improvements.
			             If you turn it off now, you will not be able to turn it back on.',
						'woocommerce'
					),
					'<br/>'
				);
			}

			$needs_update = version_compare( get_bloginfo( 'version' ), '5.6', '<' );
			if ( $needs_update && current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
				$update_text = sprintf(
				// translators: 1: line break tag, 2: open link to WordPress update link, 3: close link tag.
					__( '%1$s %2$sUpdate WordPress to enable the new navigation%3$s', 'woocommerce' ),
					'<br/>',
					'<a href="' . self_admin_url( 'update-core.php' ) . '" target="_blank">',
					'</a>'
				);
				$disabled = true;
			}

			if ( ! empty( $update_text ) ) {
				$description .= $update_text;
			}
		}

		if ( 'new_product_management' === $feature_id ) {
			$disabled = true;
			$desc_tip = __( '⚠ This feature will be available soon. Stay tuned!', 'woocommerce' );
		}

		if ( ! $this->is_legacy_feature( $feature_id ) && ! $disabled && $this->verify_did_woocommerce_init() ) {
			$plugin_info_for_feature = $this->get_compatible_plugins_for_feature( $feature_id, true );
			$incompatibles           = array_merge( $plugin_info_for_feature['incompatible'], $plugin_info_for_feature['uncertain'] );
			$incompatibles           = array_filter( $incompatibles, 'is_plugin_active' );
			$incompatible_count      = count( $incompatibles );
			if ( $incompatible_count > 0 ) {
				if ( 1 === $incompatible_count ) {
					/* translators: %s = printable plugin name */
					$desc_tip = sprintf( __( "⚠ This feature shouldn't be enabled, the %s plugin is active and isn't compatible with it.", 'woocommerce' ), $this->plugin_util->get_plugin_name( $incompatibles[0] ) );
				} elseif ( 2 === $incompatible_count ) {
					/* translators: %1\$s, %2\$s = printable plugin names */
					$desc_tip = sprintf(
						__( "⚠ This feature shouldn't be enabled: the %1\$s and %2\$s plugins are active and aren't compatible with it.", 'woocommerce' ),
						$this->plugin_util->get_plugin_name( $incompatibles[0] ),
						$this->plugin_util->get_plugin_name( $incompatibles[1] )
					);
				} else {
					/* translators: %1\$s, %2\$s = printable plugin names, %3\$d = plugins count */
					$desc_tip = sprintf(
						_n(
							"⚠ This feature shouldn't be enabled: %1\$s, %2\$s and %3\$d more active plugin isn't compatible with it",
							"⚠ This feature shouldn't be enabled: the %1\$s and %2\$s plugins are active and aren't compatible with it. There are %3\$d other incompatible plugins.",
							$incompatible_count - 2,
							'woocommerce'
						),
						$this->plugin_util->get_plugin_name( $incompatibles[0] ),
						$this->plugin_util->get_plugin_name( $incompatibles[1] ),
						$incompatible_count - 2
					);
				}

				$incompatible_plugins_url = add_query_arg(
					array(
						'plugin_status' => 'incompatible_with_feature',
						'feature_id'    => $feature_id,
					),
					admin_url( 'plugins.php' )
				);
				/* translators: %s = URL of the plugins page */
				$extra_desc_tip = sprintf( __( " <a href='%s'>Manage incompatible plugins</a>", 'woocommerce' ), $incompatible_plugins_url );

				$desc_tip .= $extra_desc_tip;

				$disabled = ! $this->feature_is_enabled( $feature_id );
			}
		}

		/**
		 * Filter to customize the description tip that appears under the description of each feature in the features settings page.
		 *
		 * @since 7.1.0
		 *
		 * @param string $desc_tip The original description tip.
		 * @param string $feature_id The id of the feature for which the description tip is being customized.
		 * @param bool $disabled True if the UI currently prevents changing the enable/disable status of the feature.
		 * @return string The new description tip to use.
		 */
		$desc_tip = apply_filters( 'woocommerce_feature_description_tip', $desc_tip, $feature_id, $disabled );

		return array(
			'title'    => $feature['name'],
			'desc'     => $description,
			'type'     => 'checkbox',
			'id'       => $this->feature_enable_option_name( $feature_id ),
			'disabled' => $disabled && ! $this->force_allow_enabling_features,
			'desc_tip' => $desc_tip,
			'tooltip'  => $tooltip,
			'default'  => $this->feature_is_enabled_by_default( $feature_id ) ? 'yes' : 'no',
		);
	}

	/**
	 * Handle the plugin deactivation hook.
	 *
	 * @param string $plugin_name Name of the plugin that has been deactivated.
	 */
	private function handle_plugin_deactivation( $plugin_name ): void {
		unset( $this->compatibility_info_by_plugin[ $plugin_name ] );

		foreach ( array_keys( $this->compatibility_info_by_feature ) as $feature ) {
			$compatibles = $this->compatibility_info_by_feature[ $feature ]['compatible'];
			$this->compatibility_info_by_feature[ $feature ]['compatible'] = array_diff( $compatibles, array( $plugin_name ) );

			$incompatibles = $this->compatibility_info_by_feature[ $feature ]['incompatible'];
			$this->compatibility_info_by_feature[ $feature ]['incompatible'] = array_diff( $incompatibles, array( $plugin_name ) );

		}
	}

	/**
	 * Handler for the all_plugins filter.
	 *
	 * Returns the list of plugins incompatible with a given plugin
	 * if we are in the plugins page and the query string of the current request
	 * looks like '?plugin_status=incompatible_with_feature&feature_id=<feature id>'.
	 *
	 * @param array $list The original list of plugins.
	 */
	private function filter_plugins_list( $list ): array {
		if ( ! $this->verify_did_woocommerce_init() ) {
			return $list;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput
		if ( ! function_exists( 'get_current_screen' ) || get_current_screen() && 'plugins' !== get_current_screen()->id || 'incompatible_with_feature' !== ArrayUtil::get_value_or_default( $_GET, 'plugin_status' ) ) {
			return $list;
		}

		$feature_id = $_GET['feature_id'] ?? 'all';
		if ( 'all' !== $feature_id && ! $this->feature_exists( $feature_id ) ) {
			return $list;
		}

		$incompatibles = array();

		// phpcs:enable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput
		foreach ( array_keys( $list ) as $plugin_name ) {
			if ( ! $this->plugin_util->is_woocommerce_aware_plugin( $plugin_name ) || ! $this->proxy->call_function( 'is_plugin_active', $plugin_name ) ) {
				continue;
			}

			$compatibility     = $this->get_compatible_features_for_plugin( $plugin_name );
			$incompatible_with = array_diff(
				array_merge( $compatibility['incompatible'], $compatibility['uncertain'] ),
				$this->legacy_feature_ids
			);

			if ( ( 'all' === $feature_id && ! empty( $incompatible_with ) ) || in_array( $feature_id, $incompatible_with, true ) ) {
				$incompatibles[] = $plugin_name;
			}
		}

		return array_intersect_key( $list, array_flip( $incompatibles ) );
	}

	/**
	 * Handler for the admin_notices action.
	 */
	private function display_notices_in_plugins_page(): void {
		if ( ! $this->verify_did_woocommerce_init() ) {
			return;
		}

		$feature_filter_description_shown = $this->maybe_display_current_feature_filter_description();
		if ( ! $feature_filter_description_shown ) {
			$this->maybe_display_feature_incompatibility_warning();
		}
	}

	/**
	 * Shows a warning when there are any incompatibility between active plugins and enabled features.
	 * The warning is shown in on any admin screen except the plugins screen itself, since
	 * there's already a "You are viewing
	 */
	private function maybe_display_feature_incompatibility_warning(): void {
		$incompatible_plugins = false;

		foreach ( $this->plugin_util->get_woocommerce_aware_plugins( true ) as $plugin ) {
			$compatibility     = $this->get_compatible_features_for_plugin( $plugin, true );
			$incompatible_with = array_diff(
				array_merge( $compatibility['incompatible'], $compatibility['uncertain'] ),
				$this->legacy_feature_ids
			);

			if ( $incompatible_with ) {
				$incompatible_plugins = true;
				break;
			}
		}

		if ( ! $incompatible_plugins ) {
			return;
		}

		$message = str_replace(
			'<a>',
			'<a href="' . esc_url( add_query_arg( array( 'plugin_status' => 'incompatible_with_feature' ), admin_url( 'plugins.php' ) ) ) . '">',
			__( 'WooCommerce has detected that some of your active plugins are incompatible with currently enabled WooCommerce features. Please <a>review the details</a>.', 'woocommerce' )
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div class="notice notice-error">
		<p><?php echo $message; ?></p>
		</div>
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Shows a "You are viewing the plugins that are incompatible with the X feature"
	 * if we are in the plugins page and the query string of the current request
	 * looks like '?plugin_status=incompatible_with_feature&feature_id=<feature id>'.
	 */
	private function maybe_display_current_feature_filter_description(): bool {
		if ( 'plugins' !== get_current_screen()->id ) {
			return false;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput
		$plugin_status = $_GET['plugin_status'] ?? '';
		$feature_id    = $_GET['feature_id'] ?? '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput

		if ( 'incompatible_with_feature' !== $plugin_status ) {
			return false;
		}

		$feature_id = ( '' === $feature_id ) ? 'all' : $feature_id;

		if ( 'all' !== $feature_id && ! $this->feature_exists( $feature_id ) ) {
			return false;
		}

		// phpcs:enable WordPress.Security.NonceVerification
		$plugins_page_url  = admin_url( 'plugins.php' );
		$features_page_url = $this->get_features_page_url();

		$message =
			'all' === $feature_id
			? __( 'You are viewing active plugins that are incompatible with currently enabled WooCommerce features.', 'woocommerce' )
			: sprintf(
				/* translators: %s is a feature name. */
				__( "You are viewing the active plugins that are incompatible with the '%s' feature.", 'woocommerce' ),
				$this->features[ $feature_id ]['name']
			);

		$message .= '<br />';
		$message .= sprintf(
			__( "<a href='%1\$s'>View all plugins</a> - <a href='%2\$s'>Manage WooCommerce features</a>", 'woocommerce' ),
			$plugins_page_url,
			$features_page_url
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div class="notice notice-info">
			<p><?php echo $message; ?></p>
		</div>
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		return true;
	}

	/**
	 * Handler for the 'after_plugin_row' action.
	 * Displays a "This plugin is incompatible with X features" notice if necessary.
	 *
	 * @param string $plugin_file The id of the plugin for which a row has been rendered in the plugins page.
	 * @param array  $plugin_data Plugin data, as returned by 'get_plugins'.
	 */
	private function handle_plugin_list_rows( $plugin_file, $plugin_data ) {
		global $wp_list_table;

		if ( 'incompatible_with_feature' !== ArrayUtil::get_value_or_default( $_GET, 'plugin_status' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( is_null( $wp_list_table ) || ! $this->plugin_util->is_woocommerce_aware_plugin( $plugin_data ) ) {
			return;
		}

		if ( ! $this->proxy->call_function( 'is_plugin_active', $plugin_file ) ) {
			return;
		}

		$feature_compatibility_info = $this->get_compatible_features_for_plugin( $plugin_file, true );
		$incompatible_features      = array_merge( $feature_compatibility_info['incompatible'], $feature_compatibility_info['uncertain'] );
		$incompatible_features      = array_values(
			array_filter(
				$incompatible_features,
				function( $feature_id ) {
					return ! $this->is_legacy_feature( $feature_id );
				}
			)
		);

		$incompatible_features_count = count( $incompatible_features );
		if ( $incompatible_features_count > 0 ) {
			$columns_count      = $wp_list_table->get_column_count();
			$is_active          = true; // For now we are showing active plugins in the "Incompatible with..." view.
			$is_active_class    = $is_active ? 'active' : 'inactive';
			$is_active_td_style = $is_active ? " style='border-left: 4px solid #72aee6;'" : '';

			if ( 1 === $incompatible_features_count ) {
				$message = sprintf(
					/* translators: %s = printable plugin name */
					__( "⚠ This plugin is incompatible with the enabled WooCommerce feature '%s', it shouldn't be activated.", 'woocommerce' ),
					$this->features[ $incompatible_features[0] ]['name']
				);
			} elseif ( 2 === $incompatible_features_count ) {
				/* translators: %1\$s, %2\$s = printable plugin names */
				$message = sprintf(
					__( "⚠ This plugin is incompatible with the enabled WooCommerce features '%1\$s' and '%2\$s', it shouldn't be activated.", 'woocommerce' ),
					$this->features[ $incompatible_features[0] ]['name'],
					$this->features[ $incompatible_features[1] ]['name']
				);
			} else {
				/* translators: %1\$s, %2\$s = printable plugin names, %3\$d = plugins count */
				$message = sprintf(
					__( "⚠ This plugin is incompatible with the enabled WooCommerce features '%1\$s', '%2\$s' and %3\$d more, it shouldn't be activated.", 'woocommerce' ),
					$this->features[ $incompatible_features[0] ]['name'],
					$this->features[ $incompatible_features[1] ]['name'],
					$incompatible_features_count - 2
				);
			}
			$features_page_url       = $this->get_features_page_url();
			$manage_features_message = __( 'Manage WooCommerce features', 'woocommerce' );

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<tr class='plugin-update-tr update <?php echo $is_active_class; ?>' data-plugin='<?php echo $plugin_file; ?>' data-plugin-row-type='feature-incomp-warn'>
				<td colspan='<?php echo $columns_count; ?>' class='plugin-update'<?php echo $is_active_td_style; ?>>
					<div class='notice inline notice-warning notice-alt'>
						<p>
							<?php echo $message; ?>
							<a href="<?php echo $features_page_url; ?>"><?php echo $manage_features_message; ?></a>
						</p>
					</div>
				</td>
			</tr>
			<?php
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get the URL of the features settings page.
	 *
	 * @return string
	 */
	private function get_features_page_url(): string {
		return admin_url( 'admin.php?page=wc-settings&tab=advanced&section=features' );
	}

	/**
	 * Fix for the HTML of the plugins list when there are feature-plugin incompatibility warnings.
	 *
	 * WordPress renders the plugin information rows in the plugins page in <tr> elements as follows:
	 *
	 * - If the plugin needs update, the <tr> will have an "update" class. This will prevent the lower
	 *   border line to be drawn. Later an additional <tr> with an "update available" warning will be rendered,
	 *   it will have a "plugin-update-tr" class which will draw the missing lower border line.
	 * - Otherwise, the <tr> will be already drawn with the lower border line.
	 *
	 * This is a problem for our rendering of the "plugin is incompatible with X features" warning:
	 *
	 * - If the plugin info <tr> has "update", our <tr> will render nicely right after it; but then
	 *   our own "plugin-update-tr" class will draw an additional line before the "needs update" warning.
	 * - If not, the plugin info <tr> will render its lower border line right before our compatibility info <tr>.
	 *
	 * This small script fixes this by adding the "update" class to the plugin info <tr> if it doesn't have it
	 * (so no extra line before our <tr>), or removing 'plugin-update-tr' from our <tr> otherwise
	 * (and then some extra manual tweaking of margins is needed).
	 *
	 * @param string $current_screen The current screen object.
	 */
	private function enqueue_script_to_fix_plugin_list_html( $current_screen ): void {
		if ( 'plugins' !== $current_screen->id ) {
			return;
		}

		wc_enqueue_js(
			"
	    const warningRows = document.querySelectorAll('tr[data-plugin-row-type=\"feature-incomp-warn\"]');
	    for(const warningRow of warningRows) {
	    	const pluginName = warningRow.getAttribute('data-plugin');
			const pluginInfoRow = document.querySelector('tr.active[data-plugin=\"' + pluginName + '\"]:not(.plugin-update-tr), tr.inactive[data-plugin=\"' + pluginName + '\"]:not(.plugin-update-tr)');
			if(pluginInfoRow.classList.contains('update')) {
				warningRow.classList.remove('plugin-update-tr');
				warningRow.querySelector('.notice').style.margin = '5px 10px 15px 30px';
			}
			else {
				pluginInfoRow.classList.add('update');
			}
	    }
		"
		);
	}

	/**
	 * Handler for the 'views_plugins' hook that shows the links to the different views in the plugins page.
	 * If we come from a "Manage incompatible plugins" in the features page we'll show just two views:
	 * "All" (so that it's easy to go back to a known state) and "Incompatible with X".
	 * We'll skip the rest of the views since the counts are wrong anyway, as we are modifying
	 * the plugins list via the 'all_plugins' filter.
	 *
	 * @param array $views An array of view ids => view links.
	 * @return string[] The actual views array to use.
	 */
	private function handle_plugins_page_views_list( $views ): array {
		// phpcs:disable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput
		if ( 'incompatible_with_feature' !== ArrayUtil::get_value_or_default( $_GET, 'plugin_status' ) ) {
			return $views;
		}

		$feature_id = $_GET['feature_id'] ?? 'all';
		if ( 'all' !== $feature_id && ! $this->feature_exists( $feature_id ) ) {
			return $views;
		}
		// phpcs:enable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput

		$all_items = get_plugins();

		$incompatible_plugins_count = count( $this->filter_plugins_list( $all_items ) );
		$incompatible_text          =
			'all' === $feature_id
			? __( 'Incompatible with WooCommerce features', 'woocommerce' )
			/* translators: %s = name of a WooCommerce feature */
			: sprintf( __( "Incompatible with '%s'", 'woocommerce' ), $this->features[ $feature_id ]['name'] );
		$incompatible_link = "<a href='plugins.php?plugin_status=incompatible_with_feature&feature_id={$feature_id}' class='current' aria-current='page'>{$incompatible_text} <span class='count'>({$incompatible_plugins_count})</span></a>";

		$all_plugins_count = count( $all_items );
		$all_text          = __( 'All', 'woocommerce' );
		$all_link          = "<a href='plugins.php?plugin_status=all'>{$all_text} <span class='count'>({$all_plugins_count})</span></a>";

		return array(
			'all'                       => $all_link,
			'incompatible_with_feature' => $incompatible_link,
		);
	}
}
