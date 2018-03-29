<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Load plugin compatibility file if supported plugins are activated
 * @since 0.7 (builder version)
 */
class ET_Builder_Plugin_Compat_Loader {
	/**
	 * Unique instance of class
	 */
	public static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Gets the instance of the class
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook methods to WordPress action and filter
	 * @return void
	 */
	private function init_hooks() {
		// Load plugin.php for frontend usage
		if ( ! function_exists( 'is_plugin_active' ) || ! function_exists( 'get_plugins' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Loop plugin list and load active plugin compatibility file
		foreach ( array_keys( get_plugins() ) as $plugin ) {
			// Load plugin compat file if plugin is active
			if ( is_plugin_active( $plugin ) ) {
				$plugin_compat_name = dirname( $plugin );
				$plugin_compat_url  = apply_filters(
					"et_builder_plugin_compat_path_{$plugin_compat_name}",
					ET_BUILDER_DIR . "plugin-compat/{$plugin_compat_name}.php",
					$plugin_compat_name
				);

				// Load plugin compat file (if compat file found)
				if ( file_exists( $plugin_compat_url ) ) {
					require_once $plugin_compat_url;
				}
			}
		}
	}
}

ET_Builder_Plugin_Compat_Loader::init();
