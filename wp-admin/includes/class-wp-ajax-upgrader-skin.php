<?php
/**
 * Upgrader API: WP_Ajax_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Upgrader Skin for Ajax WordPress upgrades.
 *
 * This skin is designed to be used for Ajax updates.
 *
 * @since 4.6.0
 *
 * @see Automatic_Upgrader_Skin
 */
class WP_Ajax_Upgrader_Skin extends Automatic_Upgrader_Skin {

	/**
	 * Holds the WP_Error object.
	 *
	 * @since 4.6.0
	 * @var null|WP_Error
	 */
	protected $errors = null;

	/**
	 * Constructor.
	 *
	 * @since 4.6.0
	 *
	 * @param array $args Options for the upgrader, see WP_Upgrader_Skin::__construct().
	 */
	public function __construct( $args = array() ) {
		parent::__construct( $args );

		$this->errors = new WP_Error();
	}

	/**
	 * Retrieves the list of errors.
	 *
	 * @since 4.6.0
	 *
	 * @return WP_Error Errors during an upgrade.
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Retrieves a string for error messages.
	 *
	 * @since 4.6.0
	 *
	 * @return string Error messages during an upgrade.
	 */
	public function get_error_messages() {
		$messages = array();

		foreach ( $this->errors->get_error_codes() as $error_code ) {
			$error_data = $this->errors->get_error_data( $error_code );

			if ( $error_data && is_string( $error_data ) ) {
				$messages[] = $this->errors->get_error_message( $error_code ) . ' ' . esc_html( strip_tags( $error_data ) );
			} else {
				$messages[] = $this->errors->get_error_message( $error_code );
			}
		}

		return implode( ', ', $messages );
	}

	/**
	 * Stores a log entry for an error.
	 *
	 * @since 4.6.0
	 *
	 * @param string|WP_Error $errors Errors.
	 */
	public function error( $errors ) {
		if ( is_string( $errors ) ) {
			$string = $errors;
			if ( ! empty( $this->upgrader->strings[ $string ] ) ) {
				$string = $this->upgrader->strings[ $string ];
			}

			if ( false !== strpos( $string, '%' ) ) {
				$args = func_get_args();
				$args = array_splice( $args, 1 );
				if ( ! empty( $args ) ) {
					$string = vsprintf( $string, $args );
				}
			}

			// Count existing errors to generate an unique error code.
			$errors_count = count( $this->errors->get_error_codes() );
			$this->errors->add( 'unknown_upgrade_error_' . $errors_count + 1, $string );
		} elseif ( is_wp_error( $errors ) ) {
			foreach ( $errors->get_error_codes() as $error_code ) {
				$this->errors->add( $error_code, $errors->get_error_message( $error_code ), $errors->get_error_data( $error_code ) );
			}
		}

		$args = func_get_args();
		call_user_func_array( array( $this, 'parent::error' ), $args );
	}

	/**
	 * Stores a log entry.
	 *
	 * @since 4.6.0
	 *
	 * @param string|array|WP_Error $data Log entry data.
	 */
	public function feedback( $data ) {
		if ( is_wp_error( $data ) ) {
			foreach ( $data->get_error_codes() as $error_code ) {
				$this->errors->add( $error_code, $data->get_error_message( $error_code ), $data->get_error_data( $error_code ) );
			}
		}

		$args = func_get_args();
		call_user_func_array( array( $this, 'parent::feedback' ), $args );
	}
}
