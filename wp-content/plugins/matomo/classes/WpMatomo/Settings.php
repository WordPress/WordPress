<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 * Code Based on
 * @author Andr&eacute; Br&auml;kling
 * https://github.com/braekling/matomo
 *
 */

namespace WpMatomo;

use Piwik\CliMulti\Process;
use WpMatomo\Admin\CookieConsent;
use WpMatomo\Admin\TrackingSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Settings {
	const OPTION_PREFIX                        = 'matomo-';
	const GLOBAL_OPTION_PREFIX                 = 'matomo_global-';
	const OPTION                               = 'matomo-option';
	const OPTION_GLOBAL                        = 'matomo-global-option';
	const OPTION_KEY_CAPS_ACCESS               = 'caps_access';
	const OPTION_KEY_STEALTH                   = 'caps_tracking';
	const OPTION_LAST_TRACKING_SETTINGS_CHANGE = 'last_tracking_settings_update';
	const OPTION_LAST_TRACKING_CODE_UPDATE     = 'last_tracking_code_update';
	const SHOW_GET_STARTED_PAGE                = 'show_get_started_page';
	const DELETE_ALL_DATA_ON_UNINSTALL         = 'delete_all_data_uninstall';
	const SITE_CURRENCY                        = 'site_currency';
	const NETWORK_CONFIG_OPTIONS               = 'config_options';
	const DISABLE_ASYNC_ARCHIVING_OPTION_NAME  = 'matomo_disable_async_archiving';
	const USE_SESSION_VISITOR_ID_OPTION_NAME   = 'use_session_visitor_id';
	const SERVER_SIDE_TRACKING_DELAY_SECS      = 'server_side_tracking_delay_secs';

	// NOTE: this is not a setting value, but is stored with setting values to avoid
	// adding an extra get_option call to every WordPress backoffice request.
	const INSTANCE_COMPONENTS_INSTALLED = 'instance-components-installed';

	public static $is_doing_action_tracking_related = false;
	/**
	 * @internal tests only
	 * @var bool
	 */
	public $force_disable_addhandler = false;

	/**
	 * Tests only
	 *
	 * @ignore
	 * @var bool
	 */
	private $assume_is_network_enabled_in_tests = false;

	/**
	 * Register default configuration set
	 *
	 * (public for tests)
	 *
	 * @var array
	 */
	public $default_global_settings = [
		// Plugin settings
		'last_settings_update'                     => 0,
		self::OPTION_LAST_TRACKING_SETTINGS_CHANGE => 0,
		self::OPTION_KEY_STEALTH                   => [],
		self::OPTION_KEY_CAPS_ACCESS               => [],
		self::NETWORK_CONFIG_OPTIONS               => [],
		self::DELETE_ALL_DATA_ON_UNINSTALL         => true,
		self::SITE_CURRENCY                        => 'USD',
		// User settings: Stats configuration
		// User settings: Tracking configuration
		'track_mode'                               => 'disabled',
		'track_js_endpoint'                        => 'default',
		'track_api_endpoint'                       => 'default',
		'track_codeposition'                       => 'footer',
		'track_noscript'                           => false,
		'track_content'                            => 'disabled',
		'track_ecommerce'                          => true,
		'track_search'                             => false,
		'track_404'                                => false,
		'tagmanger_container_ids'                  => [],
		'add_post_annotations'                     => [],
		'add_customvars_box'                       => false,
		'js_manually'                              => '',
		'noscript_manually'                        => '',
		'add_download_extensions'                  => '',
		'set_download_extensions'                  => '',
		'set_link_classes'                         => '',
		'set_download_classes'                     => '',
		'core_version'                             => '',
		'version_history'                          => [],
		'mail_history'                             => [],
		'disable_cookies'                          => false,
		'cookie_consent'                           => CookieConsent::REQUIRE_NONE,
		'force_post'                               => false,
		'limit_cookies'                            => false,
		'limit_cookies_visitor'                    => 34186669, // Matomo default 13 months
		'limit_cookies_session'                    => 1800, // Matomo default 30 minutes
		'limit_cookies_referral'                   => 15778463, // Matomo default 6 months
		'track_admin'                              => false,
		'track_across'                             => false,
		'track_across_alias'                       => false,
		'track_crossdomain_linking'                => false,
		'track_feed'                               => false,
		'track_feed_addcampaign'                   => false,
		'track_feed_campaign'                      => 'feed',
		'track_heartbeat'                          => 0,
		'track_user_id'                            => 'disabled',
		'track_datacfasync'                        => false,
		'track_jserrors'                           => false,
		'force_protocol'                           => 'disabled',
		'maxmind_license_key'                      => '',
		self::SHOW_GET_STARTED_PAGE                => 1,
		self::DISABLE_ASYNC_ARCHIVING_OPTION_NAME  => false,
	];

	/**
	 * Settings stored per blog
	 *
	 * @var array
	 */
	private $default_blog_settings = [
		'noscript_code'                          => '',
		'tracking_code'                          => '',
		self::OPTION_LAST_TRACKING_CODE_UPDATE   => 0,
		self::USE_SESSION_VISITOR_ID_OPTION_NAME => false,
		self::SERVER_SIDE_TRACKING_DELAY_SECS    => 180,
	];

	private $global_settings = [];
	private $blog_settings   = [];

	private $settings_changed = [];

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * Constructor class to prepare settings manager
	 *
	 */
	public function __construct() {
		$this->logger = new Logger();

		$this->init_settings();
	}

	public function init_settings() {
		$this->settings_changed = [];
		$this->global_settings  = [];
		$this->blog_settings    = [];

		if ( $this->is_network_enabled() ) {
			$global_settings = get_site_option( self::OPTION_GLOBAL, [] );
		} else {
			$global_settings = get_option( self::OPTION_GLOBAL, [] );
		}

		if ( ! empty( $global_settings ) && is_array( $global_settings ) ) {
			$this->global_settings = $global_settings;
		}

		$settings = get_option( self::OPTION, [] );

		if ( ! empty( $settings ) && is_array( $settings ) ) {
			$this->blog_settings = $settings;
		}
	}

	public function get_customised_global_settings() {
		$custom_settings = [];

		foreach ( $this->global_settings as $key => $val ) {
			if ( isset( $this->default_global_settings[ $key ] )
			     // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				 && $this->default_global_settings[ $key ] != $val ) {
				$custom_settings[ $key ] = $val;
			}
		}

		return $custom_settings;
	}

	public function uninstall() {
		Uninstaller::uninstall_options( self::OPTION_PREFIX );
		Uninstaller::uninstall_options( self::GLOBAL_OPTION_PREFIX );
		Uninstaller::uninstall_site_meta( self::GLOBAL_OPTION_PREFIX );
		$this->init_settings();
	}

	public function is_multisite() {
		return function_exists( 'is_multisite' ) && is_multisite();
	}

	/**
	 * @api
	 */
	public function is_network_enabled() {
		if ( $this->assume_is_network_enabled_in_tests ) {
			return true;
		}

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active_for_network( 'matomo/matomo.php' );
	}

	/**
	 * Save all settings as WordPress options
	 */
	public function save() {
		if ( empty( $this->settings_changed ) ) {
			$this->logger->log( 'No settings changed yet' );

			return;
		}

		$this->logger->log( 'Save settings' );

		if ( $this->is_network_enabled() ) {
			update_site_option( self::OPTION_GLOBAL, $this->global_settings );
		} else {
			update_option( self::OPTION_GLOBAL, $this->global_settings );
		}

		update_option( self::OPTION, $this->blog_settings );

		$keys_changed           = array_values( array_unique( $this->settings_changed ) );
		$this->settings_changed = [];

		foreach ( $keys_changed as $key_changed ) {
			do_action( 'matomo_setting_change_' . $key_changed );
		}
	}

	/**
	 * Get a global option's value
	 *
	 * @param string $key
	 *            option key
	 *
	 * @return string|array option value
	 * @api
	 */
	public function get_global_option( $key ) {
		if ( isset( $this->global_settings[ $key ] ) ) {
			return $this->global_settings[ $key ];
		}

		if ( isset( $this->default_global_settings[ $key ] ) ) {
			return $this->default_global_settings[ $key ];
		}
	}

	/**
	 * Get an option's value related to a specific blog
	 *
	 * @param string $key option key
	 *
	 * @return string|array
	 * @api
	 */
	public function get_option( $key ) {
		if ( isset( $this->blog_settings[ $key ] ) ) {
			return $this->blog_settings[ $key ];
		}

		if ( isset( $this->default_blog_settings[ $key ] ) ) {
			return $this->default_blog_settings[ $key ];
		}
	}

	private function convert_type( $value, $type ) {
		if ( 'array' === $type && empty( $value ) ) {
			$value = []; // prevent eg converting '' to array('')
		} else {
			settype( $value, $type );
		}

		return $value;
	}

	/**
	 * Set a global option's value
	 *
	 * @param string       $key option key
	 * @param string|array $value new option value
	 */
	public function set_global_option( $key, $value ) {
		if ( isset( $this->default_global_settings[ $key ] ) ) {
			$type  = gettype( $this->default_global_settings[ $key ] );
			$value = $this->convert_type( $value, $type );
		}

		if ( ! isset( $this->global_settings[ $key ] )
			 || $this->global_settings[ $key ] !== $value ) {
			$this->settings_changed[] = $key;
			$this->logger->log( 'Changed global option ' . $key . ': ' . ( is_array( $value ) ? wp_json_encode( $value ) : $value ) );

			$this->global_settings[ $key ] = $value;
		}
	}

	/**
	 * Set an option's value related to a specific blog
	 *
	 * @param string $key option key
	 * @param string $value new option value
	 */
	public function set_option( $key, $value ) {
		if ( isset( $this->default_blog_settings[ $key ] ) ) {
			$type  = gettype( $this->default_blog_settings[ $key ] );
			$value = $this->convert_type( $value, $type );
		}

		if ( ! isset( $this->blog_settings[ $key ] )
			 || $this->blog_settings[ $key ] !== $value ) {
			$this->settings_changed[] = $key;
			$this->logger->log( 'Changed option ' . $key . ': ' . $value );
			$this->blog_settings[ $key ] = $value;
		}
	}

	/**
	 * @param array $values
	 *
	 * @api
	 */
	public function apply_tracking_related_changes( $values ) {
		$this->set_global_option( self::OPTION_LAST_TRACKING_SETTINGS_CHANGE, time() );

		$this->apply_changes( $values );

		if ( ! self::$is_doing_action_tracking_related ) {
			// prevent recurison if any plugin was listening to this event and calling this method again
			self::$is_doing_action_tracking_related = true;
			do_action( 'matomo_tracking_settings_changed', $this, $values );
			self::$is_doing_action_tracking_related = false;
		}
	}

	public function should_disable_addhandler() {
		if ( $this->force_disable_addhandler ) {
			return true;
		}

		return defined( 'MATOMO_DISABLE_ADDHANDLER' ) && MATOMO_DISABLE_ADDHANDLER;
	}

	/**
	 * Apply new configuration
	 *
	 * @param array $settings
	 *            new configuration set
	 *
	 * @api
	 */
	public function apply_changes( $settings ) {
		$this->logger->log( 'Apply changed settings:' );
		foreach ( $this->default_global_settings as $key => $val ) {
			if ( isset( $settings[ $key ] ) ) {
				$this->set_global_option( $key, $settings[ $key ] );
			}
		}
		foreach ( $this->default_blog_settings as $key => $val ) {
			if ( isset( $settings[ $key ] ) ) {
				$this->set_option( $key, $settings[ $key ] );
			}
		}
		$this->set_global_option( 'last_settings_update', time() );

		if ( $this->should_save_tracking_code_across_sites() ) {
			// special case for when the same tracking code needs to be used across all instances
			$this->set_global_option( 'js_manually', $this->get_option( 'tracking_code' ) );
			$this->set_global_option( 'noscript_manually', $this->get_option( 'noscript_code' ) );
		}

		$this->save();
	}

	private function should_save_tracking_code_across_sites() {
		return $this->is_network_enabled()
			   && $this->get_global_option( 'track_mode' ) === TrackingSettings::TRACK_MODE_MANUALLY;
	}

	public function get_js_tracking_code() {
		if ( $this->should_save_tracking_code_across_sites() ) {
			return $this->get_global_option( 'js_manually' );
		}

		return $this->get_option( 'tracking_code' );
	}

	public function get_noscript_tracking_code() {
		if ( $this->should_save_tracking_code_across_sites() ) {
			return $this->get_global_option( 'noscript_manually' );
		}

		return $this->get_option( 'noscript_code' );
	}

	public function is_cross_domain_linking_enabled() {
		return $this->get_global_option( 'track_crossdomain_linking' );
	}

	public function should_delete_all_data_on_uninstall() {
		if ( defined( 'MATOMO_REMOVE_ALL_DATA' ) ) {
			return (bool) MATOMO_REMOVE_ALL_DATA;
		}

		return (bool) $this->get_global_option( self::DELETE_ALL_DATA_ON_UNINSTALL );
	}

	/**
	 * Check if feed tracking is enabled
	 *
	 * @return boolean Is feed tracking enabled?
	 */
	public function is_track_feed() {
		return $this->get_global_option( 'track_feed' );
	}

	/**
	 * Check if admin tracking is enabled
	 *
	 * @return boolean Is admin tracking enabled?
	 */
	public function is_admin_tracking_enabled() {
		return $this->get_global_option( 'track_admin' ) && is_admin();
	}

	public function is_current_tracking_code() {
		$last_tracking_code_update     = $this->get_option( self::OPTION_LAST_TRACKING_CODE_UPDATE );
		$last_tracking_settings_update = $this->get_global_option( self::OPTION_LAST_TRACKING_SETTINGS_CHANGE );

		return $last_tracking_code_update && $last_tracking_code_update > $last_tracking_settings_update;
	}

	/**
	 * Check if feed permalinks get a campaign parameter
	 *
	 * @return boolean Add campaign parameter to feed permalinks?
	 */
	public function is_add_feed_campaign() {
		return $this->get_global_option( 'track_feed_addcampaign' );
	}

	public function is_tracking_enabled() {
		return $this->get_global_option( 'track_mode' ) !== 'disabled';
	}

	/**
	 * Check if noscript code insertion is enabled
	 *
	 * @return boolean Insert noscript code?
	 */
	public function is_add_no_script_code() {
		return $this->get_global_option( 'track_noscript' );
	}

	public function get_tracking_code_position() {
		return $this->get_global_option( 'track_codeposition' );
	}

	public function track_404_enabled() {
		return $this->get_global_option( 'track_404' );
	}

	public function track_user_id_enabled() {
		return $this->get_global_option( 'track_user_id' ) !== 'disabled';
	}

	public function track_search_enabled() {
		return ( is_search() && $this->get_global_option( 'track_search' ) );
	}

	public function set_assume_is_network_enabled_in_tests( $network_enabled = true ) {
		$this->assume_is_network_enabled_in_tests = $network_enabled;
	}

	public function is_async_archiving_supported() {
		return Process::isSupported() && ( ! defined( 'MATOMO_SUPPORT_ASYNC_ARCHIVING' ) || MATOMO_SUPPORT_ASYNC_ARCHIVING );
	}

	public function is_async_archiving_disabled_by_option() {
		return (bool) $this->get_global_option( self::DISABLE_ASYNC_ARCHIVING_OPTION_NAME );
	}

	public function get_matomo_major_version() {
		$core_version = $this->get_global_option( 'core_version' );
		$core_version = isset( $core_version ) ? $core_version : '';

		$parts = explode( '.', $core_version );
		if ( empty( $parts ) ) {
			return 0;
		}

		return (int) $parts[0];
	}
}
