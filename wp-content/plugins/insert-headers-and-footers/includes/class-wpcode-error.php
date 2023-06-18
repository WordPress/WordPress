<?php
/**
 * This class handles PHP errors, keeping tabs of errors thrown
 * and the messages displayed back to the user.
 *
 * @package wpcode
 */

/**
 * WPCode_Error class.
 */
class WPCode_Error {

	/**
	 * An array of errors already caught.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * The error object caught when running the code.
	 *
	 * @param ParseError|Exception|Error|array $error The caught error.
	 *
	 * @return void
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
		$this->write_error_to_log( $error );
	}

	/**
	 * Check if an error has been recorded.
	 *
	 * @return bool
	 */
	public function has_error() {
		return ! empty( $this->errors );
	}

	/**
	 * Empty the errors record, useful if you want to
	 * make sure the last error was thrown by your code.
	 *
	 * @return void
	 */
	public function clear_errors() {
		$this->errors = array();
	}

	/**
	 * Store the error in the logs.
	 *
	 * @param array|Exception $error The error object.
	 *
	 * @return void
	 */
	private function write_error_to_log( $error ) {
		$handle = 'error';
		if ( is_array( $error ) && isset( $error['snippet'] ) ) {
			$handle = 'snippet-' . $error['snippet'];
		}

		wpcode()->logger->handle( time(), $this->get_error_message( $error ), $handle );
	}

	/**
	 * Get the last error message.
	 *
	 * @return string
	 */
	public function get_last_error_message() {
		if ( empty( $this->errors ) ) {
			return '';
		}
		$last_error = end( $this->errors );

		return $this->get_error_message( $last_error );
	}

	/**
	 * Get the error message from the error object, either an array or an Exception object.
	 *
	 * @param array|Exception $error The error object.
	 *
	 * @return string
	 */
	public function get_error_message( $error ) {
		if ( is_array( $error ) && isset( $error['message'] ) ) {
			return $error['message'];
		}

		if ( ! is_array( $error ) && method_exists( $error, 'getMessage' ) ) {
			return $error->getMessage();
		}

		return '';
	}
}
