<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
use WpMatomo\WpStatistics\Importers\Actions\RecordImporter;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class SubtableConverter {

	public static function aggregate_by_key( array $wp_statistics_data, $first_key, $second_key ) {
		$data = [];
		if ( count( $wp_statistics_data ) ) {
			foreach ( $wp_statistics_data as $row ) {
				if ( ! array_key_exists( $row[ $first_key ], $data ) ) {
					$data[ $row[ $first_key ] ] = [
						'label'            => RecordImporter::get_label( $row, $first_key ),
						'data'             => [],
						'nb_uniq_visitors' => 0,
						'nb_visits'        => 0,
					];
				}
				$data[ $row[ $first_key ] ]['data'][ $row[ $second_key ] ] = [
					'label'            => RecordImporter::get_label( $row, $second_key ),
					'nb_uniq_visitors' => $row['nb'],
					'nb_visits'        => $row['nb'],
				];
				$data[ $row[ $first_key ] ]['nb_visits']                  += $row['nb'];
				$data[ $row[ $first_key ] ]['nb_uniq_visitors']           += $row['nb'];
			}
		}

		$data_table = new DataTable();
		foreach ( $data as $key => $row ) {
			$data = $row['data'];
			unset( $row['data'] );
			$top_level_row = self::add_row_to_table( $data_table, new DataTable\Row( array( 0 => $row ) ), $key );
			foreach ( $data as $sub_key => $sub_row ) {
				self::add_row_to_subtable( $top_level_row, new DataTable\Row( array( 0 => $sub_row ) ), $sub_key );
			}
		}
		return $data_table;
	}

	protected static function add_row_to_subtable( DataTable\Row $top_level_row, DataTable\Row $row_to_add, $new_label ) {
		$sub_table = $top_level_row->getSubtable();
		if ( ! $sub_table ) {
			$sub_table = new DataTable();
			$top_level_row->setSubtable( $sub_table );
		}

		return self::add_row_to_table( $sub_table, $row_to_add, $new_label );
	}

	protected static function add_row_to_table( DataTable $record, DataTable\Row $row, $new_label ) {
		$found_row = $record->getRowFromLabel( $new_label );
		if ( empty( $found_row ) ) {
			$found_row = clone $row;
			$found_row->deleteMetadata();
			$found_row->setColumn( 'label', $new_label );
			$record->addRow( $found_row );
		} else {
			$found_row->sumRow( $row, $copy_metadata = false );
		}

		return $found_row;
	}
}
