<?php

namespace WpMatomo\WpStatistics;

use Matomo\Dependencies\GeoIp2\Database\Reader;
use WpMatomo\Paths;

/**
 * GeoIP2 client for matomo.
 *
 * Matomo expects a specific format for the location data, this is what this class does
 *
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class Geoip2 {

	private static $instance = null;

	private $geoip;

	protected static $records = array();

	private function __construct() {
		$wp_statistics_db = WP_CONTENT_DIR . '/uploads/wp-statistics/GeoLite2-City.mmdb';
		if ( file_exists( $wp_statistics_db ) ) {
			$this->geoip = new Reader( $wp_statistics_db );
		} else {
			$paths     = new Paths();
			$matomo_db = $paths->get_upload_base_dir() . '/DBIP-City.mmdb';
			if ( ! file_exists( $matomo_db ) ) {
				throw new \Exception( 'No city database available' );
			}
			$this->geoip = new Reader( $matomo_db );
		}
	}

	/**
	 * @return Geoip2
	 */
	// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @param string $ip
	 *
	 * @return \GeoIp2\Model\City|mixed
	 * @throws \GeoIp2\Exception\AddressNotFoundException In case IP address is not found.
	 * @throws \MaxMind\Db\Reader\InvalidDatabaseException In case this is not a valid database.
	 */
	private function get_record( $ip ) {
		if ( ! array_key_exists( $ip, self::$records ) ) {
			self::$records[ $ip ] = $this->geoip->city( $ip );
		}
		return self::$records[ $ip ];
	}

	public function get_matomo_country_code( $ip ) {
		try {
			$record = $this->get_record( $ip );
			return strtolower( $record->country->isoCode );
		} catch ( \Exception $e ) {
			return 'us';
		}
	}

	/**
	 * @param string $ip
	 * @param string $region
	 * @return string
	 */
	public function get_matomo_region_code( $ip, $region ) {
		try {
			$record = $this->get_record( $ip );
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$region_code = $record->mostSpecificSubdivision->isoCode;
			if ( empty( $region_code ) ) {
				$regions = include dirname( MATOMO_ANALYTICS_FILE ) . '/app/plugins/GeoIp2/data/isoRegionNames.php';
				if ( array_key_exists( $record->country->isoCode, $regions ) ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$region_name         = $record->mostSpecificSubdivision->name;
					$regions_for_country = $regions[ $record->country->isoCode ];

					$region_code = null;
					foreach ( $regions_for_country as $code => $region_info ) {
						if ( $region_info['name'] === $region_name
							|| in_array( $region_name, $region_info['altNames'], true )
						) {
							$region_code = $code;
							break;
						}

						if ( $region
							&& (
								$region === $region_info['name']
								|| in_array( $region, $region_info['altNames'], true )
							)
						) {
							$region_code = $code;
							break;
						}
					}
				}
			}
			return $region_code . '|' . $this->get_matomo_country_code( $ip );
		} catch ( \Exception $e ) {
			return '|us';
		}
	}

	public function get_matomo_city_code( $ip, $region ) {
		try {
			$record = $this->get_record( $ip );
			return $record->city->name . '|' . $this->get_matomo_region_code( $ip, $region ) . '|' . $record->location->latitude . '|' . $record->location->longitude;
		} catch ( \Exception $e ) {
			return '||us';
		}
	}
}
