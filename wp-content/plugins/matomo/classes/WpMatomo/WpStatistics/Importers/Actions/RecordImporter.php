<?php

namespace WpMatomo\WpStatistics\Importers\Actions;

use Piwik\Config;
use Piwik\DataTable;
use WP_STATISTICS\DB;
use WP_Statistics\Decorators\VisitorDecorator;
use WP_STATISTICS\MetaBox\top_visitors;
use WP_Statistics\Models\VisitorsModel;
use WpMatomo\WpStatistics\RecordInserter;
use Psr\Log\LoggerInterface;
use Piwik\Date;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class RecordImporter {

	const IS_IMPORTED_FROM_WPSTATISTICS_METADATA_NAME = 'is_imported_from_wpstatistics';
	protected $logger                                 = null;

	protected $maximum_rows_in_data_table_level_zero;

	protected $maximum_rows_in_sub_data_table;

	protected $record_inserter;

	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
		// Reading pre 2.0 config file settings
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_data_table_level_zero = @Config::getInstance()->General['datatable_archiving_maximum_rows_actions'];
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_sub_data_table = @Config::getInstance()->General['datatable_archiving_maximum_rows_subtable_actions'];
		if ( empty( $this->maximum_rows_in_data_table_level_zero ) ) {
			$this->maximum_rows_in_data_table_level_zero = Config::getInstance()->General['datatable_archiving_maximum_rows_referrers'];
			$this->maximum_rows_in_sub_data_table        = Config::getInstance()->General['datatable_archiving_maximum_rows_subtable_referrers'];
		}
	}

	public function supports_site() {
		return true;
	}

	public function set_record_inserter( RecordInserter $record_inserter ) {
		$this->record_inserter = $record_inserter;
	}

	protected function insert_record(
		$record_name, DataTable $record, $maximum_rows_in_data_table = null,
		$maximum_rows_in_sub_data_table = null, $column_to_sort_by_before_truncation = null
	) {
		$this->record_inserter->insert_record( $record_name, $record, $maximum_rows_in_data_table, $maximum_rows_in_sub_data_table, $column_to_sort_by_before_truncation );
	}

	protected function insert_blob_record( $name, $values ) {
		$this->record_inserter->insert_blob_record( $name, $values );
	}

	protected function insert_numeric_records( array $values ) {
		$this->record_inserter->insert_numeric_records( $values );
	}

	protected function get_visitors( Date $date ) {
		if ( class_exists( '\WP_Statistics\Models\VisitorsModel' ) ) {
			return $this->get_visitors_from_model( $date );
		} else {
			return $this->get_visitors_from_metabox( $date );
		}
	}

	/**
	 * Returns the prefixed table name for a wpstatistics plugin.
	 *
	 * @param string $unprefixed_name
	 * @return array|mixed|string|null
	 */
	protected function get_table_name( $unprefixed_name ) {
		if ( method_exists( DB::class, 'getTableName' ) ) {
			return DB::getTableName( $unprefixed_name );
		}

		return DB::table( $unprefixed_name );
	}

	private function get_visitors_from_model( Date $date ) {
		$page           = 1;
		$limit          = 1000;
		$visitors_found = [];

		$visitors_model = new VisitorsModel();
		do {
			try {
				// code copied from top_visitors::get (copy required since newer versions
				// do not support pagination in top_visitors::get)
				$visitors = $visitors_model->getVisitorsData(
					[
						'date'      => [
							'from' => $date->toString(),
							'to'   => $date->toString(),
						],
						'page'      => $page,
						'per_page'  => $limit,
						'order_by'  => 'hits',
						'order'     => 'DESC',
						'user_info' => true,
						'page_info' => true,
					]
				);
			} catch ( \Exception $e ) {
				$visitors = [];
			}

			$page++;
			$no_data = count( $visitors ) < 1; // copied from wpstatistics
			if ( $no_data ) {
				$visitors = [];
			} else {
				$visitors = $this->convert_visitors_to_array( $visitors );
			}
			$visitors_found = array_merge( $visitors_found, $visitors );
		} while ( true !== $no_data );
		return $visitors_found;
	}

	/**
	 * Converts VisitorDecorator objects to an array that Converter classes expect.
	 *
	 * @param VisitorDecorator[] $visitors
	 */
	protected function convert_visitors_to_array( $visitors ) {
		$property = new \ReflectionProperty( VisitorDecorator::class, 'visitor' );
		$property->setAccessible( true );

		$result = [];
		foreach ( $visitors as $visitor ) {
			$raw_visitor_props   = $property->getValue( $visitor );
			$last_view_timestamp = strtotime(
				isset( $raw_visitor_props->last_view ) ? $raw_visitor_props->last_view : $raw_visitor_props->last_counter
			);

			$result[] = [
				'ID'        => $visitor->getId(),
				'IP'        => $visitor->getIP(),
				'last_view' => $last_view_timestamp,
				'last_page' => $visitor->getLastPage(),
				'hits'      => $visitor->getHits(),
				'referrer'  => [
					'name' => $visitor->getReferral()->getRawReferrer(),
					'link' => $visitor->getReferral()->getReferrer(),
				],
				'location'  => [
					'country' => $visitor->getLocation()->getCountryName(),
					'flag'    => $visitor->getLocation()->getCountryFlag(),
				],
				'browser'   => [
					'name'    => $visitor->getBrowser()->getName(),
					'version' => $visitor->getBrowser()->getVersion(),
				],
				'os'        => [
					'name' => $visitor->getOs()->getName(),
				],
			];
		}

		return $result;
	}

	private function get_visitors_from_metabox( Date $date ) {
		$page           = 1;
		$limit          = 1000;
		$visitors_found = [];

		do {
			$visitors = top_visitors::get(
				[
					'day'      => $date->toString(),
					'per_page' => $limit,
					'paged'    => $page,
				]
			);
			$page ++;
			$no_data = ( ( array_key_exists( 'no_data', $visitors ) ) && ( 1 === $visitors['no_data'] ) );
			if ( $no_data ) {
				$visitors = [];
			}
			$visitors_found = array_merge( $visitors_found, $visitors );
		} while ( true !== $no_data );

		return $visitors_found;
	}

	public static function get_label( $row, $key ) {
		return empty( $row[ $key ] ) ? '' : $row[ $key ];
	}
}
