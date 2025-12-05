<?php
namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class VisitsTimeConverter extends VisitorsConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		$datatable = new DataTable();
		if ( isset( $wp_statistics_data[0]['date'] ) ) { // older wp-statistics version
			$label = empty( $wp_statistics_data[0]['date'] ) ? '' : $wp_statistics_data[0]['date'];
			$datatable->addRowFromSimpleArray(
				[
					'label'     => $label,
					'nb_visits' => count( $wp_statistics_data ),
				]
			);
		} else {
			$data = [];
			foreach ( $wp_statistics_data as $wp_stat_row ) {
				$hour = gmdate( 'H', $wp_stat_row['last_view'] );
				if ( empty( $hour ) ) {
					continue;
				}

				if ( ! isset( $data[ $hour ] ) ) {
					$data[ $hour ] = [
						'label'            => $hour,
						'nb_visits'        => 0,
						'nb_uniq_visitors' => 0,
					];
				}

				$data[ $hour ]['nb_visits'] ++;
				$data[ $hour ]['nb_uniq_visitors'] ++;
			}
			$datatable->addRowsFromSimpleArray( array_values( $data ) );
		}
		return $datatable;
	}
}
