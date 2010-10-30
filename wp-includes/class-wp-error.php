<?php
/**
 * WordPress Error API.
 *
 * Contains the WP_Error class and the is_wp_error() function.
 *
 * @package WordPress
 */

/**
 * WordPress Error class.
 *
 * Container for checking for WordPress errors and error messages. Return
 * WP_Error and use {@link is_wp_error()} to check if this class is returned.
 * Many core WordPress functions pass this class in the event of an error and
 * if not handled properly will result in code errors.
 *
 * @package WordPress
 * @since 2.1.0
 */
class WP_Error {
	/**
	 * Stores the list of errors.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $errors = array();

	/**
	 * Stores the list of data for error codes.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $error_data = array();

	/**
	 * PHP4 Constructor - Sets up error message.
	 *
	 * If code parameter is empty then nothing will be done. It is possible to
	 * add multiple messages to the same code, but with other methods in the
	 * class.
	 *
	 * All parameters are optional, but if the code parameter is set, then the
	 * data parameter is optional.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Error code
	 * @param string $message Error message
	 * @param mixed $data Optional. Error data.
	 * @return WP_Error
	 */
	function WP_Error($code = '', $message = '', $data = '') {
		if ( empty($code) )
			return;

		$this->errors[$code][] = $message;

		if ( ! empty($data) )
			$this->error_data[$code] = $data;
	}

	/**
	 * Retrieve all error codes.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array List of error codes, if avaiable.
	 */
	function get_error_codes() {
		if ( empty($this->errors) )
			return array();

		return array_keys($this->errors);
	}

	/**
	 * Retrieve first error code available.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return string|int Empty string, if no error codes.
	 */
	function get_error_code() {
		$codes = $this->get_error_codes();

		if ( empty($codes) )
			return '';

		return $codes[0];
	}

	/**
	 * Retrieve all error messages or error messages matching code.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Retrieve messages matching code, if exists.
	 * @return array Error strings on success, or empty array on failure (if using codee parameter).
	 */
	function get_error_messages($code = '') {
		// Return all messages if no code specified.
		if ( empty($code) ) {
			$all_messages = array();
			foreach ( (array) $this->errors as $code => $messages )
				$all_messages = array_merge($all_messages, $messages);

			return $all_messages;
		}

		if ( isset($this->errors[$code]) )
			return $this->errors[$code];
		else
			return array();
	}

	/**
	 * Get single error message.
	 *
	 * This will get the first message available for the code. If no code is
	 * given then the first code available will be used.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Error code to retrieve message.
	 * @return string
	 */
	function get_error_message($code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();
		$messages = $this->get_error_messages($code);
		if ( empty($messages) )
			return '';
		return $messages[0];
	}

	/**
	 * Retrieve error data for error code.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Error code.
	 * @return mixed Null, if no errors.
	 */
	function get_error_data($code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();

		if ( isset($this->error_data[$code]) )
			return $this->error_data[$code];
		return null;
	}

	/**
	 * Append more error messages to list of error messages.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string|int $code Error code.
	 * @param string $message Error message.
	 * @param mixed $data Optional. Error data.
	 */
	function add($code, $message, $data = '') {
		$this->errors[$code][] = $message;
		if ( ! empty($data) )
			$this->error_data[$code] = $data;
	}

	/**
	 * Add data for error code.
	 *
	 * The error code can only contain one error data.
	 *
	 * @since 2.1.0
	 *
	 * @param mixed $data Error data.
	 * @param string|int $code Error code.
	 */
	function add_data($data, $code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();

		$this->error_data[$code] = $data;
	}
}

/**
 * Check whether variable is a WordPress Error.
 *
 * Looks at the object and if a WP_Error class. Does not check to see if the
 * parent is also WP_Error, so can't inherit WP_Error and still use this
 * function.
 *
 * @since 2.1.0
 *
 * @param mixed $thing Check if unknown variable is WordPress Error object.
 * @return bool True, if WP_Error. False, if not WP_Error.
 */
function is_wp_error($thing) {
	if ( is_object($thing) && is_a($thing, 'WP_Error') )
		return true;
	return false;
}

?>