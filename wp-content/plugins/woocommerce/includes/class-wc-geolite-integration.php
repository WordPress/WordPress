<?php
/**
 * Wrapper for MaxMind GeoLite2 Reader
 *
 * This class provide an interface to handle geolocation and error handling.
 *
 * Requires PHP 5.4+.
 *
 * @package WooCommerce\Classes
 * @since   3.4.0
 * @deprecated 3.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Geolite integration class.
 *
 * @deprecated 3.9.0
 */
class WC_Geolite_Integration {

	/**
	 * MaxMind GeoLite2 database path.
	 *
	 * @var string
	 */
	private $database = '';

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger
	 */
	private $log = null;

	/**
	 * Constructor.
	 *
	 * @param string $database MaxMind GeoLite2 database path.
	 */
	public function __construct( $database ) {
		$this->database = $database;
	}

	/**
	 * Get country 2-letters ISO by IP address.
	 * Returns empty string when not able to find any ISO code.
	 *
	 * @param string $ip_address User IP address.
	 * @return string
	 * @deprecated 3.9.0
	 */
	public function get_country_iso( $ip_address ) {
		wc_deprecated_function( 'get_country_iso', '3.9.0' );

		$iso_code = '';

		try {
			$reader = new MaxMind\Db\Reader( $this->database ); // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
			$data   = $reader->get( $ip_address );

			if ( isset( $data['country']['iso_code'] ) ) {
				$iso_code = $data['country']['iso_code'];
			}

			$reader->close();
		} catch ( Exception $e ) {
			$this->log( $e->getMessage(), 'warning' );
		}

		return sanitize_text_field( strtoupper( $iso_code ) );
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level   Log level.
	 *                        Available options: 'emergency', 'alert',
	 *                        'critical', 'error', 'warning', 'notice',
	 *                        'info' and 'debug'.
	 *                        Defaults to 'info'.
	 */
	private function log( $message, $level = 'info' ) {
		if ( is_null( $this->log ) ) {
			$this->log = wc_get_logger();
		}

		$this->log->log( $level, $message, array( 'source' => 'geoip' ) );
	}
}
