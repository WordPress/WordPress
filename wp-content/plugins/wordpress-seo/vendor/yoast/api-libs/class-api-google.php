<?php

class Yoast_Api_Google {

	/**
	 * This class will be loaded when someone calls the API library with the Google analytics module
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload_api_google_files' ) );
	}

	/**
	 * Autoload the API Google class
	 *
	 * @param string $class_name - The class that should be loaded
	 */
	private function autoload_api_google_files( $class_name ) {
		$path        = dirname( __FILE__ );
		$class_name  = strtolower( $class_name );
		$oauth_files = array(
			// Main requires
			'yoast_google_client'          => 'google/Google_Client',
			'yoast_api_google_client'      => 'class-api-google-client',

			// Requires in classes
			'yoast_google_auth'            => 'google/auth/Google_Auth',
			'yoast_google_assertion'       => 'google/auth/Google_AssertionCredentials',
			'yoast_google_signer'          => 'google/auth/Google_Signer',
			'yoast_google_p12signer'       => 'google/auth/Google_P12Signer',
			'yoast_google_authnone'        => 'google/auth/Google_AuthNone',
			'yoast_google_oauth2'          => 'google/auth/Google_OAuth2',
			'yoast_google_verifier'        => 'google/auth/Google_Verifier',
			'yoast_google_loginticket'     => 'google/auth/Google_LoginTicket',
			'yoast_google_pemverifier'     => 'google/auth/Google_PemVerifier',
			'yoast_google_model'           => 'google/service/Google_Model',
			'yoast_google_service'         => 'google/service/Google_Service',
			'yoast_google_serviceresource' => 'google/service/Google_ServiceResource',
			'yoast_google_utils'           => 'google/service/Google_Utils',
			'yoast_google_batchrequest'    => 'google/service/Google_BatchRequest',
			'yoast_google_mediafileupload' => 'google/service/Google_MediaFileUpload',
			'yoast_google_uritemplate'     => 'google/external/URITemplateParser',
			'yoast_google_cache'           => 'google/cache/Google_Cache',

			// Requests
			'yoast_google_cacheparser'     => 'google/io/Google_CacheParser',
			'yoast_google_io'              => 'google/io/Google_IO',
			'yoast_google_httprequest'     => 'google/io/Google_HttpRequest',
			'yoast_google_rest'            => 'google/io/Google_REST',

			// Wordpress
			'yoast_google_wpio'            => 'google/io/Google_WPIO',
			'yoast_google_wpcache'         => 'google/cache/Google_WPCache',
		);

		if ( ! empty( $oauth_files[$class_name] ) ) {
			if ( file_exists( $path . '/' . $oauth_files[$class_name] . '.php' ) ) {
				require_once( $path . '/' . $oauth_files[$class_name] . '.php' );
			}

		}

	}

}