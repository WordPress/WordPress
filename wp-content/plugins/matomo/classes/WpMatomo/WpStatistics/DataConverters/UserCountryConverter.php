<?php

namespace WpMatomo\WpStatistics\DataConverters;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class UserCountryConverter extends VisitorsConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		return self::aggregate_by_key( $wp_statistics_data, 'matomo_country' );
	}
}
