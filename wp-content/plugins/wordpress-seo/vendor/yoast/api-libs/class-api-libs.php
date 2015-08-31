<?php

/**
 * Include this class to use the Yoast_Api_Libs, you can include this as a submodule in your project
 * and you just have to autoload this class
 *
 *
 * NAMING CONVENTIONS
 * - Register 'oauth' by using $this->register_api_library()
 * - Create folder 'oauth'
 * - Create file 'class-api-oauth.php'
 * - Class name should be 'Yoast_Api_Oauth'
 */
class Yoast_Api_Libs {

	/**
	 * Current version number of the API-libs
	 */
	const version = '2.0';

	/**
	 * Check if minimal required version is met.
	 *
	 * @param string $minimal_required_version
	 *
	 * @throws Exception
	 */
	public function __construct( $minimal_required_version )  {
		$this->load_google();

		if ( ! version_compare( self::version, $minimal_required_version, '>=' )) {
			throw new Exception( 'required_version' );
		}
	}

	/**
	 * Loading the google api library which will set the autoloader
	 */
	private function load_google() {
		if ( ! class_exists('Yoast_Api_Google', false) ) {
			// Require the file
			require_once dirname( __FILE__ ) . '/' . 'class-api-google.php';

			// Initialize the Google API Class to set the autoloader
			new Yoast_Api_Google();
		}
	}

}
