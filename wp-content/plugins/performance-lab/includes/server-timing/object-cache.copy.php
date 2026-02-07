<?php
/**
 * Plugin Name: Performance Lab Server Timing Object Cache Drop-In
 * Plugin URI: https://github.com/WordPress/performance
 * Description: Performance Lab drop-in to register Server-Timing metrics early. This is not a real object cache drop-in and will not override other actual object cache drop-ins.
 * Version: 3
 * Author: WordPress Performance Team
 * Author URI: https://make.wordpress.org/performance/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Object cache drop-in from Performance Lab plugin.
 *
 * This drop-in is used, admittedly as a hack, to be able to measure server
 * timings in WordPress as early as possible. Once a plugin is loaded, it is
 * too late to capture several critical events.
 *
 * This file respects any real object cache implementation the site may already
 * be using, and it is implemented in a way that there is no risk for breakage.
 *
 * If you do not want the Performance Lab plugin to place this file and thus be
 * limited to server timings only from after plugins are loaded, you can remove
 * this file and set the following constant (e.g. in wp-config.php):
 *
 *     define( 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN', true );
 *
 * @package performance-lab
 * @since 1.8.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

// Set constant to be able to later check for whether this file was loaded.
if ( ! defined( 'PERFLAB_OBJECT_CACHE_DROPIN_VERSION' ) ) {
	define( 'PERFLAB_OBJECT_CACHE_DROPIN_VERSION', 3 );
}

if ( ! function_exists( 'perflab_load_server_timing_api_from_dropin' ) ) {
	/**
	 * Loads the Performance Lab Server-Timing API if available.
	 *
	 * This function will short-circuit if at least one of the constants
	 * 'PERFLAB_DISABLE_SERVER_TIMING' or
	 * 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' is set as true.
	 *
	 * @since 1.8.0
	 */
	function perflab_load_server_timing_api_from_dropin(): void {
		if ( defined( 'PERFLAB_DISABLE_SERVER_TIMING' ) && PERFLAB_DISABLE_SERVER_TIMING ) {
			return;
		}

		if ( defined( 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' ) && PERFLAB_DISABLE_OBJECT_CACHE_DROPIN ) {
			return;
		}

		$plugins_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
		$plugin_dir  = $plugins_dir . '/performance-lab/';
		if ( ! file_exists( $plugin_dir . 'includes/server-timing/load.php' ) ) {
			$plugin_dir = $plugins_dir . '/performance/';
			if ( ! file_exists( $plugin_dir . 'includes/server-timing/load.php' ) ) {
				return;
			}
		}

		require_once $plugin_dir . 'includes/server-timing/class-perflab-server-timing-metric.php';
		require_once $plugin_dir . 'includes/server-timing/class-perflab-server-timing.php';
		require_once $plugin_dir . 'includes/server-timing/load.php';
		require_once $plugin_dir . 'includes/server-timing/defaults.php';
	}
}
perflab_load_server_timing_api_from_dropin();

/**
 * Load the original object cache drop-in if present.
 * This is only here for backward compatibility, as new Performance Lab
 * versions no longer use the approach of backing up the original
 * object-cache.php file and loading both.
 * It is critical however to maintain this line here to not break existing
 * sites where this approach has been working as expected.
 */
if ( file_exists( WP_CONTENT_DIR . '/object-cache-plst-orig.php' ) ) {
	require_once WP_CONTENT_DIR . '/object-cache-plst-orig.php';
}
