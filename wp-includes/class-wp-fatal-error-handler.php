<?php
/**
 * Error Protection API: WP_Fatal_Error_Handler class
 *
 * @package WordPress
 * @since   5.2.0
 */

/**
 * Core class used as the default shutdown handler for fatal errors.
 *
 * A drop-in 'fatal-error-handler.php' can be used to override the instance of this class and use a custom
 * implementation for the fatal error handler that WordPress registers. The custom class should extend this class and
 * can override its methods individually as necessary. The file must return the instance of the class that should be
 * registered.
 *
 * @since 5.2.0
 */
class WP_Fatal_Error_Handler {

	/**
	 * Runs the shutdown handler.
	 *
	 * This method is registered via `register_shutdown_function()`.
	 *
	 * @since 5.2.0
	 */
	public function handle() {
		if ( defined( 'WP_SANDBOX_SCRAPING' ) && WP_SANDBOX_SCRAPING ) {
			return;
		}

		try {
			// Bail if no error found.
			$error = $this->detect_error();
			if ( ! $error ) {
				return;
			}

			if ( ! isset( $GLOBALS['wp_locale'] ) ) {
				load_default_textdomain();
			}

			if ( ! is_multisite() && wp_recovery_mode()->is_initialized() ) {
				wp_recovery_mode()->handle_error( $error );
			}

			// Display the PHP error template if headers not sent.
			if ( is_admin() || ! headers_sent() ) {
				$this->display_error_template( $error );
			}
		} catch ( Exception $e ) {
			// Catch exceptions and remain silent.
		}
	}

	/**
	 * Detects the error causing the crash if it should be handled.
	 *
	 * @since 5.2.0
	 *
	 * @return array|null Error that was triggered, or null if no error received or if the error should not be handled.
	 */
	protected function detect_error() {
		$error = error_get_last();

		// No error, just skip the error handling code.
		if ( null === $error ) {
			return null;
		}

		// Bail if this error should not be handled.
		if ( ! $this->should_handle_error( $error ) ) {
			return null;
		}

		return $error;
	}

	/**
	 * Determines whether we are dealing with an error that WordPress should handle
	 * in order to protect the admin backend against WSODs.
	 *
	 * @since 5.2.0
	 *
	 * @param array $error Error information retrieved from error_get_last().
	 * @return bool Whether WordPress should handle this error.
	 */
	protected function should_handle_error( $error ) {
		$error_types_to_handle = array(
			E_ERROR,
			E_PARSE,
			E_USER_ERROR,
			E_COMPILE_ERROR,
			E_RECOVERABLE_ERROR,
		);

		if ( isset( $error['type'] ) && in_array( $error['type'], $error_types_to_handle, true ) ) {
			return true;
		}

		/**
		 * Filters whether a given thrown error should be handled by the fatal error handler.
		 *
		 * This filter is only fired if the error is not already configured to be handled by WordPress core. As such,
		 * it exclusively allows adding further rules for which errors should be handled, but not removing existing
		 * ones.
		 *
		 * @since 5.2.0
		 *
		 * @param bool  $should_handle_error Whether the error should be handled by the fatal error handler.
		 * @param array $error               Error information retrieved from error_get_last().
		 */
		return (bool) apply_filters( 'wp_should_handle_php_error', false, $error );
	}

	/**
	 * Displays the PHP error template and sends the HTTP status code, typically 500.
	 *
	 * A drop-in 'php-error.php' can be used as a custom template. This drop-in should control the HTTP status code and
	 * print the HTML markup indicating that a PHP error occurred. Note that this drop-in may potentially be executed
	 * very early in the WordPress bootstrap process, so any core functions used that are not part of
	 * `wp-includes/load.php` should be checked for before being called.
	 *
	 * If no such drop-in is available, this will call {@see WP_Fatal_Error_Handler::display_default_error_template()}.
	 *
	 * @since 5.2.0
	 *
	 * @param array $error Error information retrieved from `error_get_last()`.
	 */
	protected function display_error_template( $error ) {
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			// Load custom PHP error template, if present.
			$php_error_pluggable = WP_CONTENT_DIR . '/php-error.php';
			if ( is_readable( $php_error_pluggable ) ) {
				require_once $php_error_pluggable;

				return;
			}
		}

		// Otherwise, display the default error template.
		$this->display_default_error_template( $error );
	}

	/**
	 * Displays the default PHP error template.
	 *
	 * This method is called conditionally if no 'php-error.php' drop-in is available.
	 *
	 * It calls {@see wp_die()} with a message indicating that the site is experiencing technical difficulties and a
	 * login link to the admin backend. The {@see 'wp_php_error_message'} and {@see 'wp_php_error_args'} filters can
	 * be used to modify these parameters.
	 *
	 * @since 5.2.0
	 *
	 * @param array $error Error information retrieved from `error_get_last()`.
	 */
	protected function display_default_error_template( $error ) {
		if ( ! function_exists( '__' ) ) {
			wp_load_translations_early();
		}

		if ( ! function_exists( 'wp_die' ) ) {
			require_once ABSPATH . WPINC . '/functions.php';
		}

		if ( is_protected_endpoint() ) {
			$message = __( 'The site is experiencing technical difficulties. Please check your site admin email inbox for instructions.' );
		} else {
			$message = __( 'The site is experiencing technical difficulties.' );
		}

		$args = array(
			'response' => 500,
			'exit'     => false,
		);

		/**
		 * Filters the message that the default PHP error template displays.
		 *
		 * @since 5.2.0
		 *
		 * @param string $message HTML error message to display.
		 * @param array  $error   Error information retrieved from `error_get_last()`.
		 */
		$message = apply_filters( 'wp_php_error_message', $message, $error );

		/**
		 * Filters the arguments passed to {@see wp_die()} for the default PHP error template.
		 *
		 * @since 5.2.0
		 *
		 * @param array $args Associative array of arguments passed to `wp_die()`. By default these contain a
		 *                    'response' key, and optionally 'link_url' and 'link_text' keys.
		 * @param array $error Error information retrieved from `error_get_last()`.
		 */
		$args = apply_filters( 'wp_php_error_args', $args, $error );

		$wp_error = new WP_Error(
			'internal_server_error',
			$message,
			array(
				'error' => $error,
			)
		);

		wp_die( $wp_error, '', $args );
	}
}
