<?php

namespace WpMatomo\WpStatistics\Importers\Actions;

use Piwik\Common;
use Piwik\Config as PiwikConfig;
use Piwik\Date;
use Psr\Log\LoggerInterface;
use WP_STATISTICS\GeoIP;
use WpMatomo\WpStatistics\DataConverters\UserCityConverter;
use WpMatomo\WpStatistics\DataConverters\UserCountryConverter;
use WpMatomo\WpStatistics\DataConverters\UserRegionConverter;
use WpMatomo\WpStatistics\Geoip2;
use Piwik\Plugins\UserCountry\Archiver;

/**
 * reprocess geo localisation data as we can use a different (more complete) database
 *
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class UserCountryImporter extends RecordImporter implements ActionsInterface {

	const PLUGIN_NAME = 'UserCountry';

	const CITY_PATTERN = '/[a-z ]+,([a-z ]+)/i';

	protected $visitors = null;

	private $geoip;

	public function __construct( LoggerInterface $logger ) {
		parent::__construct( $logger );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_data_table_level_zero = @PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_standard'];
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_sub_data_table = @PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_standard'];
	}
	public function import_records( Date $date ) {
		$this->geoip    = Geoip2::getInstance();
		$this->visitors = $this->get_visitors( $date );
		if ( method_exists( GeoIP::class, 'active' ) && ! GeoIP::active( 'city' ) ) {
			// fix if geoip city if is not enabled
			$nb_visitors = count( $this->visitors );
			for ( $i = 0; $i < $nb_visitors; $i++ ) {
				$this->visitors[ $i ]['city'] = '';
			}
		}

		$this->import_countries();
		$this->import_regions();
		$this->import_cities();
	}

	/**
	 * Extract the region from the wpstatistics label
	 *
	 * @param array $visitor the wpstatistics visitor data
	 *
	 * @return string
	 */
	private function getRegion( array $visitor ) {
		$matches = [];
		$region  = '';
		if ( ! empty( $visitor['city'] ) && preg_match( self::CITY_PATTERN, $visitor['city'], $matches ) ) {
			$region = trim( $matches[1] );
		}
		return $region;
	}

	private function import_regions() {
		foreach ( $this->visitors as $id => $visitor ) {
			$this->visitors[ $id ]['matomo_region'] = $this->geoip->get_matomo_region_code( $this->get_ip( $visitor ), $this->getRegion( $visitor ) );
		}
		$regions = UserRegionConverter::convert( $this->visitors );
		$this->logger->debug( 'Import {nb_regions} regions...', [ 'nb_regions' => $regions->getRowsCount() ] );
		$this->insert_record( Archiver::REGION_RECORD_NAME, $regions, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table );
		Common::destroy( $regions );
	}

	private function import_cities() {
		// apply the country name to normalize with the matomo data
		foreach ( $this->visitors as $id => $visitor ) {
			$this->visitors[ $id ]['matomo_city'] = $this->geoip->get_matomo_city_code( $this->get_ip( $visitor ), $this->getRegion( $visitor ) );
		}
		$cities = UserCityConverter::convert( $this->visitors );
		$this->logger->debug( 'Import {nb_cities} cities...', [ 'nb_cities' => $cities->getRowsCount() ] );
		$this->insert_record( Archiver::CITY_RECORD_NAME, $cities, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table );
		Common::destroy( $cities );
	}

	private function import_countries() {
		foreach ( $this->visitors as $id => $visitor ) {
			$this->visitors[ $id ]['matomo_country'] = $this->geoip->get_matomo_country_code( $this->get_ip( $visitor ) );
		}
		$countries = UserCountryConverter::convert( $this->visitors );
		$this->logger->debug( 'Import {nb_countries} countries...', [ 'nb_countries' => $countries->getRowsCount() ] );
		$this->insert_record( Archiver::COUNTRY_RECORD_NAME, $countries, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table );
		$this->insert_numeric_records( [ Archiver::DISTINCT_COUNTRIES_METRIC => $countries->getRowsCount() ] );
		Common::destroy( $countries );
	}

	private function get_ip( $visitor ) {
		if ( isset( $visitor['IP'] ) ) {
			return $visitor['IP'];
		}

		return $visitor['ip']['value'];
	}
}
