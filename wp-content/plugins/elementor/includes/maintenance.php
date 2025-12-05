<?php
namespace Elementor;

use Elementor\Core\Kits\Manager;
use Elementor\Core\Upgrade\Manager as Upgrade_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor maintenance.
 *
 * Elementor maintenance handler class is responsible for setting up Elementor
 * activation and uninstallation hooks.
 *
 * @since 1.0.0
 */
class Maintenance {

	/**
	 * Activate Elementor.
	 *
	 * Set Elementor activation hook.
	 *
	 * Fired by `register_activation_hook` when the plugin is activated.
	 *
	 * @param bool $network_wide
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function activation( $network_wide ) {
		wp_clear_scheduled_hook( 'elementor/tracker/send_event' );

		wp_schedule_event( time(), 'daily', 'elementor/tracker/send_event' );
		flush_rewrite_rules();

		if ( is_multisite() && $network_wide ) {
			static::create_default_kit(
				get_sites( [
					'fields' => 'ids',
				] )
			);

			return;
		}

		static::create_default_kit();
		static::insert_defaults_options();

		set_transient( 'elementor_activation_redirect', true, MINUTE_IN_SECONDS );
	}

	public static function insert_defaults_options() {
		$history = Upgrade_Manager::get_installs_history();
		if ( empty( $history ) ) {
			$default_options = [
				'elementor_font_display' => 'swap',
			];
			foreach ( $default_options as $option_name => $option_value ) {
				if ( \Elementor\Utils::is_empty( get_option( $option_name ) ) ) {
					add_option( $option_name, $option_value );
				}
			}
		}
	}

	public static function deactivation() {
		Api::get_deactivation_data();
	}

	/**
	 * Uninstall Elementor.
	 *
	 * Set Elementor uninstallation hook.
	 *
	 * Fired by `register_uninstall_hook` when the plugin is uninstalled.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function uninstall() {
		wp_clear_scheduled_hook( 'elementor/tracker/send_event' );

		Api::get_uninstalled_data();
	}

	/**
	 * Init.
	 *
	 * Initialize Elementor Maintenance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		register_activation_hook( ELEMENTOR_PLUGIN_BASE, [ __CLASS__, 'activation' ] );
		register_deactivation_hook( ELEMENTOR_PLUGIN_BASE, [ __CLASS__, 'deactivation' ] );
		register_uninstall_hook( ELEMENTOR_PLUGIN_BASE, [ __CLASS__, 'uninstall' ] );

		add_action( 'wpmu_new_blog', function ( $site_id ) {
			if ( ! is_plugin_active_for_network( ELEMENTOR_PLUGIN_BASE ) ) {
				return;
			}

			static::create_default_kit( [ $site_id ] );
		} );
	}

	/**
	 * @param array $site_ids
	 */
	private static function create_default_kit( array $site_ids = [] ) {
		if ( ! empty( $site_ids ) ) {
			foreach ( $site_ids as $site_id ) {
				switch_to_blog( $site_id );

				Manager::create_default_kit();

				restore_current_blog();
			}

			return;
		}

		Manager::create_default_kit();
	}
}
