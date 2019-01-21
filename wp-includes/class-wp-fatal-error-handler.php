<?php
/**
 * Error Protection API: WP_Fatal_Error_Handler class
 *
 * @package WordPress
 * @since 5.1.0
 */

/**
 * Core class used as the default shutdown handler for fatal errors.
 *
 * A drop-in 'fatal-error-handler.php' can be used to override the instance of this class and use a custom
 * implementation for the fatal error handler that WordPress registers. The custom class should extend this class and
 * can override its methods individually as necessary. The file must return the instance of the class that should be
 * registered.
 *
 * @since 5.1.0
 */
class WP_Fatal_Error_Handler {

	/**
	 * Runs the shutdown handler.
	 *
	 * This method is registered via `register_shutdown_function()`.
	 *
	 * @since 5.1.0
	 */
	public function handle() {
		// Bail if WordPress executed successfully.
		if ( defined( 'WP_EXECUTION_SUCCEEDED' ) && WP_EXECUTION_SUCCEEDED ) {
			return;
		}

		try {
			// Bail if no error found.
			$error = $this->detect_error();
			if ( ! $error ) {
				return;
			}

			// If the error was stored and thus the extension paused,
			// redirect the request to catch multiple errors in one go.
			if ( $this->store_error( $error ) ) {
				$this->redirect_protected();
			}

			// Display the PHP error template.
			$this->display_error_template();
		} catch ( Exception $e ) {
			// Catch exceptions and remain silent.
		}
	}

	/**
	 * Detects the error causing the crash if it should be handled.
	 *
	 * @since 5.1.0
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
		if ( ! wp_should_handle_error( $error ) ) {
			return null;
		}

		return $error;
	}

	/**
	 * Stores the given error so that the extension causing it is paused.
	 *
	 * @since 5.1.0
	 *
	 * @param array $error Error that was triggered.
	 * @return bool True if the error was stored successfully, false otherwise.
	 */
	protected function store_error( $error ) {
		// Do not pause extensions if they only crash on a non-protected endpoint.
		if ( ! is_protected_endpoint() ) {
			return false;
		}

		return wp_record_extension_error( $error );
	}

	/**
	 * Redirects the current request to allow recovering multiple errors in one go.
	 *
	 * The redirection will only happen when on a protected endpoint.
	 *
	 * It must be ensured that this method is only called when an error actually occurred and will not occur on the
	 * next request again. Otherwise it will create a redirect loop.
	 *
	 * @since 5.1.0
	 */
	protected function redirect_protected() {
		// Do not redirect requests on non-protected endpoints.
		if ( ! is_protected_endpoint() ) {
			return;
		}

		// Pluggable is usually loaded after plugins, so we manually include it here for redirection functionality.
		if ( ! function_exists( 'wp_redirect' ) ) {
			include ABSPATH . WPINC . '/pluggable.php';
		}

		$scheme = is_ssl() ? 'https://' : 'http://';

		$url = "{$scheme}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		wp_redirect( $url );
		exit;
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
	 * @since 5.1.0
	 */
	protected function display_error_template() {
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			// Load custom PHP error template, if present.
			$php_error_pluggable = WP_CONTENT_DIR . '/php-error.php';
			if ( is_readable( $php_error_pluggable ) ) {
				require_once $php_error_pluggable;
				return;
			}
		}

		// Otherwise, display the default error template.
		$this->display_default_error_template();
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
	 * @since 5.1.0
	 */
	protected function display_default_error_template() {
		if ( ! function_exists( '__' ) ) {
			wp_load_translations_early();
		}

		if ( ! function_exists( 'wp_die' ) ) {
			require_once ABSPATH . WPINC . '/functions.php';
		}

		$message = __( 'The site is experiencing technical difficulties.' );

		$args = array(
			'response' => 500,
			'exit'     => false,
		);
		if ( function_exists( 'admin_url' ) ) {
			$args['link_url']  = admin_url();
			$args['link_text'] = __( 'Log into the admin backend to fix this.' );
		}

		/**
		 * Filters the message that the default PHP error template displays.
		 *
		 * @since 5.1.0
		 *
		 * @param string $message HTML error message to display.
		 */
		$message = apply_filters( 'wp_php_error_message', $message );

		/**
		 * Filters the arguments passed to {@see wp_die()} for the default PHP error template.
		 *
		 * @since 5.1.0
		 *
		 * @param array $args Associative array of arguments passed to `wp_die()`. By default these contain a
		 *                    'response' key, and optionally 'link_url' and 'link_text' keys.
		 */
		$args = apply_filters( 'wp_php_error_args', $args );

		wp_die( $message, '', $args );
	}
}
