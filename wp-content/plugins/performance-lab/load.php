<?php
/**
 * Plugin Name: Performance Lab
 * Plugin URI: https://github.com/WordPress/performance
 * Description: Performance plugin from the WordPress Performance Team, which is a collection of standalone performance features.
 * Requires at least: 6.6
 * Requires PHP: 7.2
 * Version: 4.0.0
 * Author: WordPress Performance Team
 * Author URI: https://make.wordpress.org/performance/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: performance-lab
 *
 * @package performance-lab
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

define( 'PERFLAB_VERSION', '4.0.0' );
define( 'PERFLAB_MAIN_FILE', __FILE__ );
define( 'PERFLAB_PLUGIN_DIR_PATH', plugin_dir_path( PERFLAB_MAIN_FILE ) );
define( 'PERFLAB_SCREEN', 'performance-lab' );

// If the constant isn't defined yet, it means the Performance Lab object cache file is not loaded.
if ( ! defined( 'PERFLAB_OBJECT_CACHE_DROPIN_VERSION' ) ) {
	define( 'PERFLAB_OBJECT_CACHE_DROPIN_VERSION', false );
}
define( 'PERFLAB_OBJECT_CACHE_DROPIN_LATEST_VERSION', 3 );

// Load server-timing API.
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/server-timing/class-perflab-server-timing-metric.php';
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/server-timing/class-perflab-server-timing.php';
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/server-timing/load.php';
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/server-timing/defaults.php';

// Load site health checks.
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/site-health/load.php';

/**
 * Gets the content attribute for the generator tag for the Performance Lab plugin.
 *
 * This attribute is then used in {@see perflab_render_generator()}.
 *
 * @since 1.1.0
 * @since 2.9.0 The generator tag now includes the active standalone plugin slugs.
 * @since 3.0.0 The generator tag no longer includes module slugs.
 */
function perflab_get_generator_content(): string {
	$active_plugins = array();
	foreach ( perflab_get_standalone_plugin_version_constants() as $plugin_slug => $constant_name ) {
		if ( defined( $constant_name ) && ! str_starts_with( constant( $constant_name ), 'Performance Lab ' ) ) {
			$active_plugins[] = $plugin_slug;
		}
	}

	return sprintf(
		// Use the plugin slug as it is immutable.
		'performance-lab %1$s; plugins: %2$s',
		PERFLAB_VERSION,
		implode( ', ', $active_plugins )
	);
}

/**
 * Displays the HTML generator tag for the Performance Lab plugin.
 *
 * See {@see 'wp_head'}.
 *
 * @since 1.1.0
 */
function perflab_render_generator(): void {
	$content = perflab_get_generator_content();

	echo '<meta name="generator" content="' . esc_attr( $content ) . '">' . "\n";
}
add_action( 'wp_head', 'perflab_render_generator' );

/**
 * Gets the standalone plugins and their data.
 *
 * @since 3.0.0
 *
 * @return array<string, array{'constant': string, 'experimental'?: bool}> Associative array of $plugin_slug => $plugin_data pairs.
 */
function perflab_get_standalone_plugin_data(): array {
	/*
	 * Alphabetically sorted list of plugin slugs and their data.
	 * Supported keys per plugin are:
	 * - 'constant' (string, required)
	 * - 'experimental' (boolean, optional)
	 */
	return array(
		'auto-sizes'              => array(
			'constant'     => 'IMAGE_AUTO_SIZES_VERSION',
			'experimental' => false,
		),
		'dominant-color-images'   => array(
			'constant' => 'DOMINANT_COLOR_IMAGES_VERSION',
		),
		'embed-optimizer'         => array(
			'constant'     => 'EMBED_OPTIMIZER_VERSION',
			'experimental' => false,
		),
		'image-prioritizer'       => array(
			'constant'     => 'IMAGE_PRIORITIZER_VERSION',
			'experimental' => false,
		),
		'performant-translations' => array(
			'constant' => 'PERFORMANT_TRANSLATIONS_VERSION',
		),
		'nocache-bfcache'         => array(
			'constant' => 'WestonRuter\NocacheBFCache\VERSION',
		),
		'speculation-rules'       => array(
			'constant' => 'SPECULATION_RULES_VERSION',
		),
		'view-transitions'        => array(
			'constant'     => 'VIEW_TRANSITIONS_VERSION',
			'experimental' => true,
		),
		'web-worker-offloading'   => array(
			'constant'     => 'WEB_WORKER_OFFLOADING_VERSION',
			'experimental' => true,
		),
		'webp-uploads'            => array(
			'constant' => 'WEBP_UPLOADS_VERSION',
		),
	);
}

/**
 * Gets the standalone plugin constants used for each available standalone plugin.
 *
 * @since 2.9.0
 * @since 3.0.0 The $source parameter was removed.
 *
 * @return array<string, string> Map of plugin slug and the version constant used.
 */
function perflab_get_standalone_plugin_version_constants(): array {
	return wp_list_pluck( perflab_get_standalone_plugin_data(), 'constant' );
}

/**
 * Places the Performance Lab's object cache drop-in in the drop-ins folder.
 *
 * This only runs in WP Admin to not have any potential performance impact on
 * the frontend.
 *
 * This function will short-circuit if at least one of the constants
 * 'PERFLAB_DISABLE_SERVER_TIMING' or
 * 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' is set as true or if the
 * 'PERFLAB_PLACE_OBJECT_CACHE_DROPIN' constant is not set to a truthy value.
 *
 * @since 1.8.0
 * @since 2.1.0 No longer attempts to use two of the drop-ins together.
 * @since 4.0.0 No longer places the drop-in on new sites by default, unless the `PERFLAB_PLACE_OBJECT_CACHE_DROPIN` constant is set to true.
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 */
function perflab_maybe_set_object_cache_dropin(): void {
	global $wp_filesystem;

	// Bail if Server-Timing is disabled entirely.
	if ( defined( 'PERFLAB_DISABLE_SERVER_TIMING' ) && PERFLAB_DISABLE_SERVER_TIMING ) {
		return;
	}

	// Bail if the drop-in is not enabled.
	if ( ! defined( 'PERFLAB_PLACE_OBJECT_CACHE_DROPIN' ) || ! PERFLAB_PLACE_OBJECT_CACHE_DROPIN ) {
		return;
	}

	// Bail if disabled via constant.
	// This constant is maintained only for backward compatibility and should not be relied upon in new implementations.
	// Use the 'PERFLAB_PLACE_OBJECT_CACHE_DROPIN' constant instead to control drop-in placement.
	if ( defined( 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' ) && PERFLAB_DISABLE_OBJECT_CACHE_DROPIN ) {
		return;
	}

	/**
	 * Filters whether the Perflab server timing drop-in should be set.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $disabled Whether to disable the server timing drop-in. Default false.
	 */
	if ( apply_filters( 'perflab_disable_object_cache_dropin', false ) ) {
		return;
	}

	/**
	 * Filters the value of the `object-cache.php` drop-in constant.
	 *
	 * This filter should not be used outside of tests.
	 *
	 * @since 3.0.0
	 * @internal
	 *
	 * @param int|bool $current_dropin_version The drop-in version as defined by the
	 *                                         `PERFLAB_OBJECT_CACHE_DROPIN_VERSION` constant.
	 */
	$current_dropin_version = apply_filters( 'perflab_object_cache_dropin_version', PERFLAB_OBJECT_CACHE_DROPIN_VERSION );

	// Bail if already placed in the latest version or newer.
	if ( null !== $current_dropin_version && $current_dropin_version >= PERFLAB_OBJECT_CACHE_DROPIN_LATEST_VERSION ) {
		return;
	}

	// Bail if already attempted before timeout has been completed.
	// This is present in case placing the file fails for some reason, to avoid
	// excessively retrying to place it on every request.
	$timeout = get_transient( 'perflab_set_object_cache_dropin' );
	if ( false !== $timeout ) {
		return;
	}

	if ( $wp_filesystem instanceof WP_Filesystem_Base || true === WP_Filesystem() ) {
		$dropin_path = WP_CONTENT_DIR . '/object-cache.php';

		/*
		 * If there is an actual object-cache.php file, it is most likely from
		 * a third party, or it may be an older version of the Performance Lab
		 * object-cache.php. If it's from a third party, do not replace it.
		 *
		 * Previous versions of the Performance Lab plugin were renaming the
		 * original object-cache.php file and then loading both. However, due
		 * to other plugins eagerly checking file headers, this caused too many
		 * problems across sites, so it was decided to remove this layer.
		 * Only placing the drop-in file if no other one exists yet is the
		 * safest solution.
		 */
		if ( $wp_filesystem->exists( $dropin_path ) ) {
			// If this constant evaluates to `false`, the existing file is for sure from a third party.
			if ( false === $current_dropin_version ) {
				// Set timeout of 1 day before retrying again (only in case the file already exists).
				set_transient( 'perflab_set_object_cache_dropin', true, DAY_IN_SECONDS );
				return;
			}

			// Otherwise, verify that it's actually the Performance Lab drop-in.
			$test_content = "<?php\n/**\n * Plugin Name: Performance Lab Server Timing Object Cache Drop-In\n";
			if ( ! str_starts_with( $wp_filesystem->get_contents( $dropin_path ), $test_content ) ) {
				// Set timeout of 1 day before retrying again (only in case the file already exists).
				set_transient( 'perflab_set_object_cache_dropin', true, DAY_IN_SECONDS );
				return;
			}

			/*
			 * If this logic is reached, the existing file is an older version
			 * of the Performance Lab drop-in, so it can be safely deleted, and
			 * then be replaced below.
			 */
			$wp_filesystem->delete( $dropin_path );
		}

		$wp_filesystem->copy( PERFLAB_PLUGIN_DIR_PATH . 'includes/server-timing/object-cache.copy.php', $dropin_path );
	}

	// Set timeout of 1 hour before retrying again (only relevant in case the above failed).
	set_transient( 'perflab_set_object_cache_dropin', true, HOUR_IN_SECONDS );
}
add_action( 'admin_init', 'perflab_maybe_set_object_cache_dropin' );

/**
 * Removes the Performance Lab's object cache drop-in from the drop-ins folder.
 *
 * This function should be run on plugin deactivation. For backward compatibility with
 * an earlier implementation of `perflab_maybe_set_object_cache_dropin()`, this function
 * checks whether there is an object-cache-plst-orig.php file, and if so restores it.
 *
 * This function will short-circuit if the constant
 * 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' is set as true.
 *
 * @since 1.8.0
 *
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 */
function perflab_maybe_remove_object_cache_dropin(): void {
	global $wp_filesystem;

	// Bail if disabled via constant.
	if ( defined( 'PERFLAB_DISABLE_OBJECT_CACHE_DROPIN' ) && PERFLAB_DISABLE_OBJECT_CACHE_DROPIN ) {
		return;
	}

	// Bail if custom drop-in not present anyway.
	if ( ! PERFLAB_OBJECT_CACHE_DROPIN_VERSION ) {
		return;
	}

	if ( $wp_filesystem instanceof WP_Filesystem_Base || true === WP_Filesystem() ) {
		$dropin_path        = WP_CONTENT_DIR . '/object-cache.php';
		$dropin_backup_path = WP_CONTENT_DIR . '/object-cache-plst-orig.php';

		/**
		 * If there is an object-cache-plst-orig.php file, restore it and
		 * override the Performance Lab file. This is only relevant for
		 * backward-compatibility with previous Performance Lab versions
		 * which were backing up the file and then loading both.
		 * Otherwise, just delete the Performance Lab file.
		 */
		if ( $wp_filesystem->exists( $dropin_backup_path ) ) {
			$wp_filesystem->move( $dropin_backup_path, $dropin_path, true );
		} else {
			$wp_filesystem->delete( $dropin_path );
		}
	}

	// Delete transient for drop-in check in case the plugin is reactivated shortly after.
	delete_transient( 'perflab_set_object_cache_dropin' );
}
register_deactivation_hook( __FILE__, 'perflab_maybe_remove_object_cache_dropin' );

/**
 * Redirects legacy module page to the performance feature page.
 *
 * @since 3.0.0
 *
 * @global $plugin_page
 */
function perflab_no_access_redirect_module_to_performance_feature_page(): void {
	global $plugin_page;

	if ( 'perflab-modules' !== $plugin_page ) {
		return;
	}

	if (
		current_user_can( 'manage_options' ) &&
		wp_safe_redirect( add_query_arg( 'page', PERFLAB_SCREEN ) )
	) {
		exit;
	}
}
add_action( 'admin_page_access_denied', 'perflab_no_access_redirect_module_to_performance_feature_page' );

/**
 * Cleanup function to delete legacy 'perflab_modules_settings' option if present.
 *
 * @since 3.0.0
 */
function perflab_cleanup_option(): void {
	if ( current_user_can( 'manage_options' ) ) {
		delete_option( 'perflab_modules_settings' );
	}
}
add_action( 'admin_init', 'perflab_cleanup_option' );

// Only load admin integration when in admin.
if ( is_admin() ) {
	require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/load.php';
	require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/server-timing.php';
	require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/plugins.php';
}

// Load REST API.
require_once PERFLAB_PLUGIN_DIR_PATH . 'includes/admin/rest-api.php';
