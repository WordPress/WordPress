<?php
/**
 * Error Protection API: Functions
 *
 * @package WordPress
 * @since 5.1.0
 */

/**
 * Gets the instance for storing paused plugins.
 *
 * @since 5.1.0
 *
 * @return WP_Paused_Extensions_Storage Paused plugins storage.
 */
function wp_paused_plugins() {
	static $wp_paused_plugins_storage = null;

	if ( null === $wp_paused_plugins_storage ) {
		$wp_paused_plugins_storage = new WP_Paused_Extensions_Storage( 'paused_plugins', 'paused_plugin_' );
	}

	return $wp_paused_plugins_storage;
}

/**
 * Gets the instance for storing paused themes.
 *
 * @since 5.1.0
 *
 * @return WP_Paused_Extensions_Storage Paused themes storage.
 */
function wp_paused_themes() {
	static $wp_paused_themes_storage = null;

	if ( null === $wp_paused_themes_storage ) {
		$wp_paused_themes_storage = new WP_Paused_Extensions_Storage( 'paused_themes', 'paused_theme_' );
	}

	return $wp_paused_themes_storage;
}

/**
 * Records the extension error as a database option.
 *
 * @since 5.1.0
 *
 * @global array $wp_theme_directories
 *
 * @param array $error Error that was triggered.
 * @return bool Whether the error was correctly recorded.
 */
function wp_record_extension_error( $error ) {
	global $wp_theme_directories;

	if ( ! isset( $error['file'] ) ) {
		return false;
	}

	if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
		return false;
	}

	$error_file    = wp_normalize_path( $error['file'] );
	$wp_plugin_dir = wp_normalize_path( WP_PLUGIN_DIR );

	if ( 0 === strpos( $error_file, $wp_plugin_dir ) ) {
		$callback = 'wp_paused_plugins';
		$path     = str_replace( $wp_plugin_dir . '/', '', $error_file );
	} else {
		foreach ( $wp_theme_directories as $theme_directory ) {
			$theme_directory = wp_normalize_path( $theme_directory );
			if ( 0 === strpos( $error_file, $theme_directory ) ) {
				$callback = 'wp_paused_themes';
				$path     = str_replace( $theme_directory . '/', '', $error_file );
			}
		}
	}

	if ( empty( $callback ) || empty( $path ) ) {
		return false;
	}

	$parts     = explode( '/', $path );
	$extension = array_shift( $parts );

	return call_user_func( $callback )->record( $extension, $error );
}

/**
 * Forgets a previously recorded extension error again.
 *
 * @since 5.1.0
 *
 * @param string $type         Type of the extension.
 * @param string $extension    Relative path of the extension.
 * @param bool   $network_wide Optional. Whether to resume the plugin for the entire
 *                             network. Default false.
 * @return bool Whether the extension error was successfully forgotten.
 */
function wp_forget_extension_error( $type, $extension, $network_wide = false ) {
	switch ( $type ) {
		case 'plugins':
			$callback          = 'wp_paused_plugins';
			list( $extension ) = explode( '/', $extension );
			break;
		case 'themes':
			$callback          = 'wp_paused_themes';
			list( $extension ) = explode( '/', $extension );
			break;
	}

	if ( empty( $callback ) || empty( $extension ) ) {
		return false;
	}

	// Handle manually since the regular APIs do not expose this functionality.
	if ( $network_wide && is_site_meta_supported() ) {
		$site_meta_query_clause = call_user_func( $callback )->get_site_meta_query_clause( $extension );
		return delete_metadata( 'blog', 0, $site_meta_query_clause['key'], '', true );
	}

	return call_user_func( $callback )->forget( $extension );
}

/**
 * Determines whether we are dealing with an error that WordPress should handle
 * in order to protect the admin backend against WSODs.
 *
 * @param array $error Error information retrieved from error_get_last().
 *
 * @return bool Whether WordPress should handle this error.
 */
function wp_should_handle_error( $error ) {
	if ( ! isset( $error['type'] ) ) {
		return false;
	}

	$error_types_to_handle = array(
		E_ERROR,
		E_PARSE,
		E_USER_ERROR,
		E_COMPILE_ERROR,
		E_RECOVERABLE_ERROR,
	);

	return in_array( $error['type'], $error_types_to_handle, true );
}

/**
 * Registers the shutdown handler for fatal errors.
 *
 * The handler will only be registered if {@see wp_is_fatal_error_handler_enabled()} returns true.
 *
 * @since 5.1.0
 */
function wp_register_fatal_error_handler() {
	if ( ! wp_is_fatal_error_handler_enabled() ) {
		return;
	}

	$handler = null;
	if ( defined( 'WP_CONTENT_DIR' ) && is_readable( WP_CONTENT_DIR . '/fatal-error-handler.php' ) ) {
		$handler = include WP_CONTENT_DIR . '/fatal-error-handler.php';
	}

	if ( ! is_object( $handler ) || ! is_callable( array( $handler, 'handle' ) ) ) {
		$handler = new WP_Fatal_Error_Handler();
	}

	register_shutdown_function( array( $handler, 'handle' ) );
}

/**
 * Checks whether the fatal error handler is enabled.
 *
 * A constant `WP_DISABLE_FATAL_ERROR_HANDLER` can be set in `wp-config.php` to disable it, or alternatively the
 * {@see 'wp_fatal_error_handler_enabled'} filter can be used to modify the return value.
 *
 * @since 5.1.0
 *
 * @return bool True if the fatal error handler is enabled, false otherwise.
 */
function wp_is_fatal_error_handler_enabled() {
	$enabled = ! defined( 'WP_DISABLE_FATAL_ERROR_HANDLER' ) || ! WP_DISABLE_FATAL_ERROR_HANDLER;

	/**
	 * Filters whether the fatal error handler is enabled.
	 *
	 * @since 5.1.0
	 *
	 * @param bool $enabled True if the fatal error handler is enabled, false otherwise.
	 */
	return apply_filters( 'wp_fatal_error_handler_enabled', $enabled );
}
