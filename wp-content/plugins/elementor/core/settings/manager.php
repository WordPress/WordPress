<?php
namespace Elementor\Core\Settings;

use Elementor\Core\Settings\Base\CSS_Model;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor settings manager.
 *
 * Elementor settings manager handler class is responsible for registering and
 * managing Elementor settings managers.
 *
 * @since 1.6.0
 */
class Manager {

	/**
	 * Settings managers.
	 *
	 * Holds all the registered settings managers.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @var Base\Manager[]
	 */
	private static $settings_managers = [];

	/**
	 * Builtin settings managers names.
	 *
	 * Holds the names for builtin Elementor settings managers.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @var array
	 */
	private static $builtin_settings_managers_names = [ 'page', 'editorPreferences' ];

	/**
	 * Add settings manager.
	 *
	 * Register a single settings manager to the registered settings managers.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @param Base\Manager $manager Settings manager.
	 */
	public static function add_settings_manager( Base\Manager $manager ) {
		self::$settings_managers[ $manager->get_name() ] = $manager;
	}

	/**
	 * Get settings managers.
	 *
	 * Retrieve registered settings manager(s).
	 *
	 * If no parameter passed, it will retrieve all the settings managers. For
	 * any given parameter it will retrieve a single settings manager if one
	 * exist, or `null` otherwise.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @param string $manager_name Optional. Settings manager name. Default is
	 *                             null.
	 *
	 * @return Base\Manager|Base\Manager[] Single settings manager, if it exists,
	 *                                     null if it doesn't exists, or the all
	 *                                     the settings managers if no parameter
	 *                                     defined.
	 */
	public static function get_settings_managers( $manager_name = null ) {
		if ( $manager_name ) {
			// Backwards compatibility for `general` manager, since 3.0.0.
			// Register the class only if needed.
			if ( 'general' === $manager_name ) {
				// TODO: _deprecated_argument( $manager_name, '3.0.0', 'Plugin::$instance->kits_manager->get_active_kit_for_frontend();' );
				$manager_class = self::get_manager_class( $manager_name );

				self::add_settings_manager( new $manager_class() );
			}

			if ( isset( self::$settings_managers[ $manager_name ] ) ) {
				return self::$settings_managers[ $manager_name ];
			}

			return null;
		}

		return self::$settings_managers;
	}

	/**
	 * Register default settings managers.
	 *
	 * Register builtin Elementor settings managers.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 */
	private static function register_default_settings_managers() {
		foreach ( self::$builtin_settings_managers_names as $manager_name ) {
			$manager_class = self::get_manager_class( $manager_name );

			self::add_settings_manager( new $manager_class() );
		}
	}

	/**
	 * Get class path for default settings managers.
	 *
	 * @return string
	 * @since  3.0.0
	 * @access private
	 * @static
	 */
	private static function get_manager_class( $manager_name ) {
		return __NAMESPACE__ . '\\' . ucfirst( $manager_name ) . '\Manager';
	}

	/**
	 * Get settings managers config.
	 *
	 * Retrieve the settings managers configuration.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @return array The settings managers configuration.
	 */
	public static function get_settings_managers_config() {
		$config = [];

		$user_can = Plugin::instance()->role_manager->user_can( 'design' );

		foreach ( self::$settings_managers as $name => $manager ) {
			$settings_model = $manager->get_model_for_config();
			$tabs = $settings_model->get_tabs_controls();

			if ( ! $user_can ) {
				unset( $tabs['style'] );
			}

			$config[ $name ] = [
				'name' => $manager->get_name(),
				'panelPage' => $settings_model->get_panel_page_settings(),
				'controls' => $settings_model->get_controls(),
				'tabs' => $tabs,
				'settings' => $settings_model->get_settings(),
			];

			if ( $settings_model instanceof CSS_Model ) {
				$config[ $name ]['cssWrapperSelector'] = $settings_model->get_css_wrapper_selector();
			}
		}

		return $config;
	}

	/**
	 * Get settings frontend config.
	 *
	 * Retrieve the settings managers frontend configuration.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @return array The settings managers frontend configuration.
	 */
	public static function get_settings_frontend_config() {
		$config = [];

		foreach ( self::$settings_managers as $name => $manager ) {
			$settings_model = $manager->get_model_for_config();

			if ( $settings_model ) {
				$config[ $name ] = $settings_model->get_frontend_settings();
			}
		}

		return $config;
	}

	/**
	 * Run settings managers.
	 *
	 * Register builtin Elementor settings managers.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 */
	public static function run() {
		self::register_default_settings_managers();
	}
}
