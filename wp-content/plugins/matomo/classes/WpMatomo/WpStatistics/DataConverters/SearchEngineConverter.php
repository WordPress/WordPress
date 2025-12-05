<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
use WpMatomo\WpStatistics\Importers\Actions\RecordImporter;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class SearchEngineConverter extends VisitorsConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		$key  = 'engine';
		$data = [];
		if ( count( $wp_statistics_data ) ) {
			foreach ( $wp_statistics_data as $row ) {
				if ( ! array_key_exists( $row[ $key ], $data ) ) {
					$data[ $row[ $key ] ] = [
						'label'            => RecordImporter::get_label( $row, $key ),
						'nb_visits'        => 0,
						'nb_uniq_visitors' => 0,
					];
				}
				$data[ $row[ $key ] ]['nb_visits']        += $row['nb'];
				$data[ $row[ $key ] ]['nb_uniq_visitors'] += $row['nb'];
			}
		}
		$datatable = new DataTable();
		foreach ( array_values( $data ) as $row ) {
			$datatable->addRowFromSimpleArray( $row );
		}

		return $datatable;
	}
}
