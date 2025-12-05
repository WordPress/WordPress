<?php
namespace Elementor\Core\Responsive;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Modules\DevTools\Deprecation;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor responsive.
 *
 * Elementor responsive handler class is responsible for setting up Elementor
 * responsive breakpoints.
 *
 * @since 1.0.0
 * @deprecated 3.2.0
 */
class Responsive {

	/**
	 * The Elementor breakpoint prefix.
	 *
	 * @deprecated 3.2.0
	 */
	const BREAKPOINT_OPTION_PREFIX = 'viewport_';

	/**
	 * Default breakpoints.
	 *
	 * Holds the default responsive breakpoints.
	 *
	 * @since 1.0.0
	 * @deprecated 3.2.0
	 * @access private
	 * @static
	 *
	 * @var array Default breakpoints.
	 */
	private static $default_breakpoints = [
		'xs' => 0,
		'sm' => 480,
		'md' => 768,
		'lg' => 1025,
		'xl' => 1440,
		'xxl' => 1600,
	];

	/**
	 * Editable breakpoint keys.
	 *
	 * Holds the editable breakpoint keys.
	 *
	 * @since 1.0.0
	 * @deprecated 3.2.0
	 * @access private
	 * @static
	 *
	 * @var array Editable breakpoint keys.
	 */
	private static $editable_breakpoints_keys = [
		'md',
		'lg',
	];

	/**
	 * Get default breakpoints.
	 *
	 * Retrieve the default responsive breakpoints.
	 *
	 * @since 1.0.0
	 * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::get_default_config()` instead.
	 * @access public
	 * @static
	 *
	 * @return array Default breakpoints.
	 */
	public static function get_default_breakpoints() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.2.0', 'Elementor\Core\Breakpoints\Manager::get_default_config()' );

		return self::$default_breakpoints;
	}

	/**
	 * Get editable breakpoints.
	 *
	 * Retrieve the editable breakpoints.
	 *
	 * @since 1.0.0
	 * @deprecated 3.2.0
	 * @access public
	 * @static
	 *
	 * @return array Editable breakpoints.
	 */
	public static function get_editable_breakpoints() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.2.0' );

		return array_intersect_key( self::get_breakpoints(), array_flip( self::$editable_breakpoints_keys ) );
	}

	/**
	 * Get breakpoints.
	 *
	 * Retrieve the responsive breakpoints.
	 *
	 * @since 1.0.0
	 * @deprecated 3.2.0
	 * @access public
	 * @static
	 *
	 * @return array Responsive breakpoints.
	 */
	public static function get_breakpoints() {
		return array_reduce(
			array_keys( self::$default_breakpoints ), function( $new_array, $breakpoint_key ) {
				if ( ! in_array( $breakpoint_key, self::$editable_breakpoints_keys, true ) ) {
					$new_array[ $breakpoint_key ] = self::$default_breakpoints[ $breakpoint_key ];
				} else {
					$saved_option = Plugin::$instance->kits_manager->get_current_settings( self::BREAKPOINT_OPTION_PREFIX . $breakpoint_key );

					$new_array[ $breakpoint_key ] = $saved_option ? (int) $saved_option : self::$default_breakpoints[ $breakpoint_key ];
				}

				return $new_array;
			}, []
		);
	}

	/**
	 * @since 2.1.0
	 * @deprecated 3.2.0 Use `Plugin::$instance->breakpoints->has_custom_breakpoints()` instead.
	 * @access public
	 * @static
	 */
	public static function has_custom_breakpoints() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.2.0', 'Plugin::$instance->breakpoints->has_custom_breakpoints()' );

		return (bool) array_diff( self::$default_breakpoints, self::get_breakpoints() );
	}

	/**
	 * @since 2.1.0
	 * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::get_stylesheet_templates_path()` instead.
	 * @access public
	 * @static
	 */
	public static function get_stylesheet_templates_path() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.2.0', 'Elementor\Core\Breakpoints\Manager::get_stylesheet_templates_path()' );

		return Breakpoints_Manager::get_stylesheet_templates_path();
	}

	/**
	 * @since 2.1.0
	 * @deprecated 3.2.0 Use `Elementor\Core\Breakpoints\Manager::compile_stylesheet_templates()` instead.
	 * @access public
	 * @static
	 */
	public static function compile_stylesheet_templates() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.2.0', 'Elementor\Core\Breakpoints\Manager::compile_stylesheet_templates()' );

		Breakpoints_Manager::compile_stylesheet_templates();
	}
}
