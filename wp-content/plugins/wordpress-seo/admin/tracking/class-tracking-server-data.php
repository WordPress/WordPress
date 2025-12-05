<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

/**
 * Represents the server data.
 */
class WPSEO_Tracking_Server_Data implements WPSEO_Collection {

	/**
	 * Returns the collection data.
	 *
	 * @return array The collection data.
	 */
	public function get() {
		return [
			'server' => $this->get_server_data(),
		];
	}

	/**
	 * Returns the values with server details.
	 *
	 * @return array Array with the value.
	 */
	protected function get_server_data() {
		$server_data = [];

		// Validate if the server address is a valid IP-address.
		$ipaddress = isset( $_SERVER['SERVER_ADDR'] ) ? filter_var( wp_unslash( $_SERVER['SERVER_ADDR'] ), FILTER_VALIDATE_IP ) : '';
		if ( $ipaddress ) {
			$server_data['ip']       = $ipaddress;
			$server_data['Hostname'] = gethostbyaddr( $ipaddress );
		}

		$server_data['os']            = function_exists( 'php_uname' ) ? php_uname() : PHP_OS;
		$server_data['PhpVersion']    = PHP_VERSION;
		$server_data['CurlVersion']   = $this->get_curl_info();
		$server_data['PhpExtensions'] = $this->get_php_extensions();

		return $server_data;
	}

	/**
	 * Returns details about the curl version.
	 *
	 * @return array|null The curl info. Or null when curl isn't available..
	 */
	protected function get_curl_info() {
		if ( ! function_exists( 'curl_version' ) ) {
			return null;
		}

		$curl = curl_version();

		$ssl_support = true;
		if ( ! $curl['features'] && CURL_VERSION_SSL ) {
			$ssl_support = false;
		}

		return [
			'version'    => $curl['version'],
			'sslSupport' => $ssl_support,
		];
	}

	/**
	 * Returns a list with php extensions.
	 *
	 * @return array Returns the state of the php extensions.
	 */
	protected function get_php_extensions() {
		return [
			'imagick'   => extension_loaded( 'imagick' ),
			'filter'    => extension_loaded( 'filter' ),
			'bcmath'    => extension_loaded( 'bcmath' ),
			'pcre'      => extension_loaded( 'pcre' ),
			'xml'       => extension_loaded( 'xml' ),
			'pdo_mysql' => extension_loaded( 'pdo_mysql' ),
		];
	}
}
