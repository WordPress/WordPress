<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
use WpMatomo\WpStatistics\Importers\Actions\RecordImporter;

/**
 * aggregate data on the number fields
 *
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class NumberConverter {
	/**
	 * @param []     $wp_statistics_data
	 * @param string $key the key to aggregate data
	 *
	 * @return DataTable
	 */
	public static function aggregate_by_key( $wp_statistics_data, $key ) {
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

				$nb = isset( $row['number'] ) ? $row['number'] : $row['views'];

				$data[ $row[ $key ] ]['nb_visits']        += intval( $nb );
				$data[ $row[ $key ] ]['nb_uniq_visitors'] += intval( $nb );
			}
		}

		$datatable = new DataTable();
		foreach ( array_values( $data ) as $row ) {
			$datatable->addRowFromSimpleArray( $row );
		}
		return $datatable;
	}
}
