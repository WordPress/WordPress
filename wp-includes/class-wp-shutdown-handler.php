<?php
/**
 * Error Protection API: WP_Shutdown_Handler class
 *
 * @package WordPress
 * @since 5.1.0
 */

/**
 * Core class used as the default shutdown handler.
 *
 * A drop-in 'shutdown-handler.php' can be used to override the instance of this class and use a custom implementation
 * for the shutdown handler that WordPress registers. The custom class should extend this class and can override its
 * methods individually as necessary. The file must return the instance of the class that should be registered.
 *
 * @since 5.1.0
 */
class WP_Shutdown_Handler {

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
			// Bail if no error found or if it could not be stored.
			if ( ! $this->detect_error() ) {
				return;
			}

			// Redirect the request to catch multiple errors in one go.
			$this->redirect_protected();

			// Display the PHP error template.
			$this->display_error_template();
		} catch ( Exception $e ) {
			// Catch exceptions and remain silent.
		}
	}

	/**
	 * Detects the error causing the crash and stores it if one was found.
	 *
	 * @since 5.1.0
	 *
	 * @return bool True if an error was found and stored, false otherwise.
	 */
	protected function detect_error() {
		$error = error_get_last();

		// No error, just skip the error handling code.
		if ( null === $error ) {
			return false;
		}

		// Bail if this error should not be handled.
		if ( ! wp_should_handle_error( $error ) ) {
			return false;
		}

		// Try to store the error so that the respective extension is paused.
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
	 * print the HTML markup indicating that a PHP error occurred. Alternatively, {@see wp_die()} can be used. Note
	 * that this drop-in may potentially be executed very early in the WordPress bootstrap process, so any core
	 * functions used that are not part of `wp-includes/load.php` should be checked for before being called.
	 *
	 * The default template also displays a link to the admin in order to fix the problem, however doing so is not
	 * mandatory.
	 *
	 * @since 5.1.0
	 */
	protected function display_error_template() {
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			// Load custom PHP error template, if present.
			$php_error_pluggable = WP_CONTENT_DIR . '/php-error.php';
			if ( is_readable( $php_error_pluggable ) ) {
				require_once $php_error_pluggable;
				die();
			}
		}

		// Otherwise, fail with a `wp_die()` message.
		$message = $this->get_error_message_markup();

		// `wp_die()` wraps the message in paragraph tags, so let's just try working around that.
		if ( substr( $message, 0, 3 ) === '<p>' && substr( $message, -4 ) === '</p>' ) {
			$message = substr( $message, 3, -4 );
		}

		wp_die( $message, '', 500 );
	}

	/**
	 * Returns the error message markup to display in the default error template.
	 *
	 * @since 5.1.0
	 *
	 * @return string Error message HTML output.
	 */
	protected function get_error_message_markup() {
		if ( ! function_exists( '__' ) ) {
			function __( $text ) {
				return $text;
			}
		}

		$message = sprintf(
			'<p>%s</p>',
			__( 'The site is experiencing technical difficulties.' )
		);

		if ( function_exists( 'admin_url' ) ) {
			$message .= sprintf(
				'<hr><p><em>%s <a href="%s">%s</a></em></p>',
				__( 'Are you the site owner?' ),
				admin_url(),
				__( 'Log into the admin backend to fix this.' )
			);
		}

		if ( function_exists( 'apply_filters' ) ) {
			/**
			 * Filters the message that the default PHP error page displays.
			 *
			 * @since 5.1.0
			 *
			 * @param string $message HTML error message to display.
			 */
			$message = apply_filters( 'wp_technical_issues_display', $message );
		}

		return $message;
	}
}
