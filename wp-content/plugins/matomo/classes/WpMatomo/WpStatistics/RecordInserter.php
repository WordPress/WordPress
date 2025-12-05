<?php

namespace WpMatomo\WpStatistics;

use Piwik\DataAccess\ArchiveWriter;
use Piwik\DataTable;
use Piwik\Metrics;
use WpMatomo\WpStatistics\Importers\Actions\RecordImporter;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class RecordInserter {

	/**
	 * @var ArchiveWriter
	 */
	private $archive_writer;

	public function __construct( ArchiveWriter $writer ) {
		$this->archive_writer = $writer;
	}

	public function insert_record( $record_name, DataTable $record, $maximum_rows_in_data_table = null,
									$maximum_rows_in_sub_data_table = null, $column_to_sort_by_before_truncation = 'nb_visits' ) {
		$record->setMetadata( RecordImporter::IS_IMPORTED_FROM_WPSTATISTICS_METADATA_NAME, 1 );

		$blob = $record->getSerialized( $maximum_rows_in_data_table, $maximum_rows_in_sub_data_table, $column_to_sort_by_before_truncation );
		$this->insert_blob_record( $record_name, $blob );
	}

	public function insert_blob_record( $name, $values ) {
		$this->archive_writer->insertBlobRecord( $name, $values );
	}

	public function insert_numeric_records( array $values ) {
		foreach ( $values as $name => $value ) {
			if ( is_numeric( $name ) ) {
				$name = Metrics::getReadableColumnName( $name );
			}
			$this->archive_writer->insertRecord( $name, $value );
		}
	}
}
