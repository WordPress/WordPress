<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class BrowsersConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		$browsers = new DataTable();
		$data     = [];
		foreach ( $wp_statistics_data as $visit ) {
			$browser_name = empty( $visit['browser']['name'] ) ? '' : $visit['browser']['name'];
			if ( ! array_key_exists( $browser_name, $data ) ) {
				$data[ $browser_name ] = 0;
			}
			$data[ $browser_name ]++;
		}
		foreach ( $data as $browser => $hits ) {
			$browsers->addRowFromSimpleArray(
				[
					'label'     => $browser,
					'nb_visits' => $hits,
				]
			);
		}
		return $browsers;
	}
}
