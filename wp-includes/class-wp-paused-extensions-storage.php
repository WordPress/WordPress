<?php
/**
 * Error Protection API: WP_Paused_Extensions_Storage class
 *
 * @package WordPress
 * @since 5.1.0
 */

/**
 * Core class used for storing paused extensions.
 *
 * @since 5.1.0
 */
class WP_Paused_Extensions_Storage {

	/**
	 * Option name for storing paused extensions.
	 *
	 * @since 5.1.0
	 * @var string
	 */
	protected $option_name;

	/**
	 * Prefix for paused extensions stored as site metadata.
	 *
	 * @since 5.1.0
	 * @var string
	 */
	protected $meta_prefix;

	/**
	 * Constructor.
	 *
	 * @since 5.1.0
	 *
	 * @param string $option_name Option name for storing paused extensions.
	 * @param string $meta_prefix Prefix for paused extensions stored as site metadata.
	 */
	public function __construct( $option_name, $meta_prefix ) {
		$this->option_name = $option_name;
		$this->meta_prefix = $meta_prefix;
	}

	/**
	 * Records an extension error.
	 *
	 * Only one error is stored per extension, with subsequent errors for the same extension overriding the
	 * previously stored error.
	 *
	 * @since 5.1.0
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @param array  $error     {
	 *     Error that was triggered.
	 *
	 *     @type string $type    The error type.
	 *     @type string $file    The name of the file in which the error occurred.
	 *     @type string $line    The line number in which the error occurred.
	 *     @type string $message The error message.
	 * }
	 * @return bool True on success, false on failure.
	 */
	public function record( $extension, $error ) {
		if ( ! $this->is_api_loaded() ) {
			return false;
		}

		if ( is_multisite() && is_site_meta_supported() ) {
			// Do not update if the error is already stored.
			if ( get_site_meta( get_current_blog_id(), $this->meta_prefix . $extension, true ) === $error ) {
				return true;
			}

			return (bool) update_site_meta( get_current_blog_id(), $this->meta_prefix . $extension, $error );
		}

		$paused_extensions = $this->get_all();

		// Do not update if the error is already stored.
		if ( isset( $paused_extensions[ $extension ] ) && $paused_extensions[ $extension ] === $error ) {
			return true;
		}

		$paused_extensions[ $extension ] = $error;

		return update_option( $this->option_name, $paused_extensions );
	}

	/**
	 * Forgets a previously recorded extension error.
	 *
	 * @since 5.1.0
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @return bool True on success, false on failure.
	 */
	public function forget( $extension ) {
		if ( ! $this->is_api_loaded() ) {
			return false;
		}

		if ( is_multisite() && is_site_meta_supported() ) {
			// Do not delete if no error is stored.
			if ( get_site_meta( get_current_blog_id(), $this->meta_prefix . $extension ) === array() ) {
				return true;
			}

			return (bool) delete_site_meta( get_current_blog_id(), $this->meta_prefix . $extension );
		}

		$paused_extensions = $this->get_all();

		// Do not delete if no error is stored.
		if ( ! isset( $paused_extensions[ $extension ] ) ) {
			return true;
		}

		// Clean up the entire option if we're removing the only error.
		if ( count( $paused_extensions ) === 1 ) {
			return delete_option( $this->option_name );
		}

		unset( $paused_extensions[ $extension ] );

		return update_option( $this->option_name, $paused_extensions );
	}

	/**
	 * Gets the error for an extension, if paused.
	 *
	 * @since 5.1.0
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @return array|null Error that is stored, or null if the extension is not paused.
	 */
	public function get( $extension ) {
		if ( ! $this->is_api_loaded() ) {
			return null;
		}

		if ( is_multisite() && is_site_meta_supported() ) {
			$error = get_site_meta( get_current_blog_id(), $this->meta_prefix . $extension, true );
			if ( ! $error ) {
				return null;
			}

			return $error;
		}

		$paused_extensions = $this->get_all();

		if ( ! isset( $paused_extensions[ $extension ] ) ) {
			return null;
		}

		return $paused_extensions[ $extension ];
	}

	/**
	 * Gets the paused extensions with their errors.
	 *
	 * @since 5.1.0
	 *
	 * @return array Associative array of $extension => $error pairs.
	 */
	public function get_all() {
		if ( ! $this->is_api_loaded() ) {
			return array();
		}

		if ( is_multisite() && is_site_meta_supported() ) {
			$site_metadata = get_site_meta( get_current_blog_id() );

			$paused_extensions = array();
			foreach ( $site_metadata as $meta_key => $meta_values ) {
				if ( 0 !== strpos( $meta_key, $this->meta_prefix ) ) {
					continue;
				}

				$error = maybe_unserialize( array_shift( $meta_values ) );

				$paused_extensions[ substr( $meta_key, strlen( $this->meta_prefix ) ) ] = $error;
			}

			return $paused_extensions;
		}

		return (array) get_option( $this->option_name, array() );
	}

	/**
	 * Gets the site meta query clause for querying sites with paused extensions.
	 *
	 * @since 5.1.0
	 *
	 * @param string $extension Plugin or theme directory name.
	 * @return array A single clause to add to a meta query.
	 */
	public function get_site_meta_query_clause( $extension ) {
		return array(
			'key'         => $this->meta_prefix . $extension,
			'compare_key' => '=',
		);
	}

	/**
	 * Checks whether the underlying API to store paused extensions is loaded.
	 *
	 * @since 5.1.0
	 *
	 * @return bool True if the API is loaded, false otherwise.
	 */
	protected function is_api_loaded() {
		if ( is_multisite() ) {
			return function_exists( 'is_site_meta_supported' ) && function_exists( 'get_site_meta' );
		}

		return function_exists( 'get_option' );
	}
}
