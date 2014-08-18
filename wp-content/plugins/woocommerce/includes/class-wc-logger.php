<?php
/**
 * Allows log files to be written to for debugging purposes.
 *
 * @class 		WC_Logger
 * @version		1.6.4
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Logger {

	/**
	 * @var array Stores open file _handles.
	 * @access private
	 */
	private $_handles;

	/**
	 * Constructor for the logger.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_handles = array();
	}


	/**
	 * Destructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		foreach ( $this->_handles as $handle )
	       @fclose( escapeshellarg( $handle ) );
	}


	/**
	 * Open log file for writing.
	 *
	 * @access private
	 * @param mixed $handle
	 * @return bool success
	 */
	private function open( $handle ) {

		if ( isset( $this->_handles[ $handle ] ) )
			return true;

		if ( $this->_handles[ $handle ] = @fopen( WC()->plugin_path() . '/logs/' . $this->file_name( $handle ) . '.txt', 'a' ) )
			return true;

		return false;
	}


	/**
	 * Add a log entry to chosen file.
	 *
	 * @access public
	 * @param mixed $handle
	 * @param mixed $message
	 * @return void
	 */
	public function add( $handle, $message ) {
		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
			$time = date_i18n( 'm-d-Y @ H:i:s -' ); //Grab Time
			@fwrite( $this->_handles[ $handle ], $time . " " . $message . "\n" );
		}
	}


	/**
	 * Clear entries from chosen file.
	 *
	 * @access public
	 * @param mixed $handle
	 * @return void
	 */
	public function clear( $handle ) {

		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) )
			@ftruncate( $this->_handles[ $handle ], 0 );
	}


	/**
	 * file_name function.
	 *
	 * @access private
	 * @param mixed $handle
	 * @return string
	 */
	private function file_name( $handle ) {
		return $handle . '-' . sanitize_file_name( wp_hash( $handle ) );
	}

}