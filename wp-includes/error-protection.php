<?php
/**
 * Error Protection API: Functions
 *
 * @package WordPress
 * @since 5.2.0
 */

/**
 * Get the instance for storing paused plugins.
 *
 * @return WP_Paused_Extensions_Storage
 */
function wp_paused_plugins() {
	static $storage = null;

	if ( null === $storage ) {
		$storage = new WP_Paused_Extensions_Storage( 'plugin' );
	}

	return $storage;
}

/**
 * Get the instance for storing paused extensions.
 *
 * @return WP_Paused_Extensions_Storage
 */
function wp_paused_themes() {
	static $storage = null;

	if ( null === $storage ) {
		$storage = new WP_Paused_Extensions_Storage( 'theme' );
	}

	return $storage;
}

/**
 * Get a human readable description of an extension's error.
 *
 * @since 5.2.0
 *
 * @param array $error Error details {@see error_get_last()}
 *
 * @return string Formatted error description.
 */
function wp_get_extension_error_description( $error ) {
	$constants   = get_defined_constants( true );
	$constants   = isset( $constants['Core'] ) ? $constants['Core'] : $constants['internal'];
	$core_errors = array();

	foreach ( $constants as $constant => $value ) {
		if ( 0 === strpos( $constant, 'E_' ) ) {
			$core_errors[ $value ] = $constant;
		}
	}

	if ( isset( $core_errors[ $error['type'] ] ) ) {
		$error['type'] = $core_errors[ $error['type'] ];
	}

	/* translators: 1: Error type, 2: Error line number, 3: Error file name, 4: Error message. */
	$error_message = __( 'An error of type %1$s was caused in line %2$s of the file %3$s. Error message: %4$s' );

	return sprintf(
		$error_message,
		"<code>{$error['type']}</code>",
		"<code>{$error['line']}</code>",
		"<code>{$error['file']}</code>",
		"<code>{$error['message']}</code>"
	);
}

/**
 * Registers the shutdown handler for fatal errors.
 *
 * The handler will only be registered if {@see wp_is_fatal_error_handler_enabled()} returns true.
 *
 * @since 5.2.0
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
 * @since 5.2.0
 *
 * @return bool True if the fatal error handler is enabled, false otherwise.
 */
function wp_is_fatal_error_handler_enabled() {
	$enabled = ! defined( 'WP_DISABLE_FATAL_ERROR_HANDLER' ) || ! WP_DISABLE_FATAL_ERROR_HANDLER;

	/**
	 * Filters whether the fatal error handler is enabled.
	 *
	 * @since 5.2.0
	 *
	 * @param bool $enabled True if the fatal error handler is enabled, false otherwise.
	 */
	return apply_filters( 'wp_fatal_error_handler_enabled', $enabled );
}

/**
 * Access the WordPress Recovery Mode instance.
 *
 * @since 5.2.0
 *
 * @return WP_Recovery_Mode
 */
function wp_recovery_mode() {
	static $wp_recovery_mode;

	if ( ! $wp_recovery_mode ) {
		$wp_recovery_mode = new WP_Recovery_Mode();
	}

	return $wp_recovery_mode;
}
