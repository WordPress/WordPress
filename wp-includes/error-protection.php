<?php
/**
 * Error Protection API: Functions
 *
 * @package WordPress
 * @since   5.2.0
 */

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
