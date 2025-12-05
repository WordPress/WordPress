<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use Piwik\Plugin\Manager;
use WpMatomo\Access;
use WpMatomo\Bootstrap;
use WpMatomo\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class AdminSettings {
	const TAB_TRACKING    = 'tracking';
	const TAB_ACCESS      = 'access';
	const TAB_EXCLUSIONS  = 'exlusions';
	const TAB_PRIVACY     = 'privacy';
	const TAB_GEOLOCATION = 'geolocation';
	const TAB_ADVANCED    = 'advanced';

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public static function make_url( $tab ) {
		global $_parent_pages;
		$menu_slug = Menu::SLUG_SETTINGS;

		if ( is_multisite() && is_network_admin() ) {
			if ( isset( $_parent_pages[ $menu_slug ] ) ) {
				$parent_slug = $_parent_pages[ $menu_slug ];
				if ( $parent_slug && ! isset( $_parent_pages[ $parent_slug ] ) ) {
					$url = network_admin_url( add_query_arg( 'page', $menu_slug, $parent_slug ) );
				} else {
					$url = network_admin_url( 'admin.php?page=' . $menu_slug );
				}
			} else {
				$url = '';
			}
		} else {
			$url = menu_page_url( $menu_slug, false );
		}

		return add_query_arg( [ 'tab' => $tab ], $url );
	}

	public function show() {
		$access          = new Access( $this->settings );
		$access_settings = new AccessSettings( $access, $this->settings );
		$tracking        = new TrackingSettings( $this->settings );
		$exclusions      = new ExclusionSettings( $this->settings );
		$geolocation     = new GeolocationSettings( $this->settings );
		$privacy         = new PrivacySettings( $this->settings );
		$advanced        = new AdvancedSettings( $this->settings );
		$setting_tabs    = [
			self::TAB_TRACKING    => $tracking,
			self::TAB_ACCESS      => $access_settings,
			self::TAB_PRIVACY     => $privacy,
			self::TAB_EXCLUSIONS  => $exclusions,
			self::TAB_GEOLOCATION => $geolocation,
			self::TAB_ADVANCED    => $advanced,
		];

		$active_tab = self::TAB_TRACKING;

		if ( $this->settings->is_network_enabled() && ! is_network_admin() ) {
			$active_tab   = self::TAB_EXCLUSIONS;
			$setting_tabs = [
				self::TAB_EXCLUSIONS => $exclusions,
				self::TAB_PRIVACY    => $privacy,
			];
		}

		$plugin_settings_tabs = $this->get_plugin_settings_tabs();
		$plugin_settings_tabs = array_map(
			function ( $info ) {
				return new PluginMeasurableSettings( $info['plugin_name'], $info['plugin_display_name'] );
			},
			$plugin_settings_tabs
		);
		$setting_tabs         = array_merge( $setting_tabs, $plugin_settings_tabs );

		$setting_tabs = apply_filters( 'matomo_setting_tabs', $setting_tabs, $this->settings );

		if ( ! empty( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
			if ( isset( $setting_tabs[ $tab ] ) ) {
				$active_tab = $tab;
			}
		}

		$content_tab     = $setting_tabs[ $active_tab ];
		$matomo_settings = $this->settings;

		include dirname( __FILE__ ) . '/views/settings.php';
	}

	private function get_plugin_settings_tabs() {
		$active_wordpress_plugins = (array) get_option( 'active_plugins', [] );

		$cache_key = 'plugin-settings-tabs-' . md5( implode( ',', $active_wordpress_plugins ) );

		if ( $this->settings->is_network_enabled() ) {
			$network_plugins = (array) get_site_option( 'active_sitewide_plugins', [] );
			$cache_key       = $cache_key . '-' . md5( implode( ',', $network_plugins ) );
		}

		$tabs = get_transient( $cache_key );
		if ( false === $tabs || ! is_array( $tabs ) || empty( $active_wordpress_plugins ) ) {
			$all_wordpress_plugins = $this->get_wordpress_plugins();

			Bootstrap::do_bootstrap();
			$all_matomo_plugins = Manager::getInstance()->getActivatedPlugins();

			$marketplace_plugins = array_intersect( array_keys( $all_wordpress_plugins ), $all_matomo_plugins );

			$tabs = [];
			foreach ( $marketplace_plugins as $plugin_name ) {
				$settings_class = 'Piwik\\Plugins\\' . $plugin_name . '\\MeasurableSettings';
				if ( ! class_exists( $settings_class ) ) {
					continue;
				}

				$plugin_display_name = $all_wordpress_plugins[ $plugin_name ]['Name'];
				$plugin_display_name = preg_replace( '/\s+\(Matomo Plugin\)\s*/', '', $plugin_display_name );

				$tabs[ "plugin-{$plugin_name}" ] = [
					'plugin_name'         => $plugin_name,
					'plugin_display_name' => $plugin_display_name,
				];
			}

			set_transient( $cache_key, $tabs, 60 * 60 * 24 * 7 );
		}

		return $tabs;
	}

	private function get_wordpress_plugins() {
		$all_wordpress_plugins = array_merge( get_plugins(), get_mu_plugins() );
		$all_wordpress_plugins = array_combine(
			array_map(
				function ( $path ) {
					return basename( dirname( $path ) );
				},
				array_keys( $all_wordpress_plugins )
			),
			$all_wordpress_plugins
		);
		return $all_wordpress_plugins;
	}
}
