<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;

/**
 * aggregate data on the number fields
 *
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class SearchQueryConverter extends FilterConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		$first_page = reset( $wp_statistics_data );
		$key        = isset( $first_page['uri'] ) ? 'uri' : 'str_url';

		$data = self::filter( $wp_statistics_data, '?s=', $key );
		foreach ( $data as $id => $url ) {
			$matches = [];
			if ( preg_match( '/\?s=(.+)$/', $url[ $key ], $matches ) ) {
				$data[ $id ]['keyword'] = $matches[1];
			}
		}

		return self::aggregate_by_key( $data, 'keyword' );
	}
}
