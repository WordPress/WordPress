<?php
/**
 * Defines which Core components to load.
 *
 * It can be different on a per page bases to keep it as performant and lightweight as possible
 * by only loading what is needed.
 *
 * @package Core
 */

if ( ! function_exists( 'et_core_load_component' ) ) :
/**
 * Load Core components.
 *
 * This function loads Core components. Components are only loaded once, even if they are called many times.
 * Admin components/functions are automatically wrapped in an is_admin() check.
 *
 * @since 1.0.0
 *
 * @param string|array $components Name of the Core component(s) to include as and indexed array.
 *
 * @return bool Always return true.
 */
function et_core_load_component( $components ) {
	static $loaded = array();

	// Load in front end and backend.
	$common = array();

	// Only load admin components if is_admin() is true.
	$admin = is_admin() ? array(
		'portability' => ET_CORE_PATH . 'admin/includes/portability.php',
		'cache'       => array(
			ET_CORE_PATH . 'admin/includes/cache.php',
			ET_CORE_PATH . 'admin/includes/class-cache.php'
		),
	) : array();

	// Set dependencies.
	$dependencies = array(
		'portability' => 'cache',
	);

	foreach ( (array) $components as $component ) {
		// Stop here if the component is already loaded or doesn't exists.
		if ( in_array( $component, $loaded ) || ( ! isset( $common[$component] ) && ! isset( $admin[$component] ) ) ) {
			continue;
		}

		// Cache loaded component before calling dependencies.
		$loaded[] = $component;

		// Load dependencies.
		if ( array_key_exists( $component, $dependencies ) ) {
			et_core_load_component( $dependencies[$component] );
		}

		$_components = array();

		if ( isset( $common[$component] ) ) {
			$_components = (array) $common[$component];
		}

		if ( isset( $admin[$component] ) ) {
			$_components = array_merge( (array) $_components, (array) $admin[$component] );
		}

		foreach ( $_components as $component_path ) {
			require_once( $component_path );
		}

		/**
		 * Fires when an Core component is loaded.
		 *
		 * The dynamic portion of the hook name, $component, refers to the name of the Core component loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'et_core_loaded_component_' . $component );
	}
}
endif;