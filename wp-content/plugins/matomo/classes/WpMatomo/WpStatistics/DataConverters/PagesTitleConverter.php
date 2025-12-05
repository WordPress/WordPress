<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
use Piwik\Metrics;
use Piwik\Plugins\Actions\ArchivingHelper;
use Piwik\Tracker\Action;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class PagesTitleConverter extends NumberConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		// the title property is not set in newer versions of wp-statistics
		foreach ( $wp_statistics_data as $key => $row ) {
			if ( ! isset( $row['title'] ) ) {
				$wp_statistics_data[ $key ]['title'] = get_the_title( $row['page_id'] );
			}
		}

		$rows = self::aggregate_by_key( $wp_statistics_data, 'title' );

		$data_tables = [
			Action::TYPE_PAGE_TITLE => new DataTable(),
		];
		ArchivingHelper::reloadConfig();
		foreach ( $rows as $row ) {
			$title = $row->getColumn( 'label' );

			$row->setColumn( Metrics::INDEX_PAGE_NB_HITS, $row['nb_visits'] );
			$row->setColumn( Metrics::INDEX_NB_VISITS, $row['nb_visits'] );
			$row->setColumn( Metrics::INDEX_NB_UNIQ_VISITORS, $row['nb_visits'] );
			$row->deleteColumn( 'nb_visits' );
			$row->deleteColumn( 'nb_uniq_visitors' );

			$action_row = ArchivingHelper::getActionRow( $title, Action::TYPE_PAGE_TITLE, null, $data_tables );

			$row->deleteColumn( 'label' );

			$action_row->sumRow( $row, $copy_metadata = false );
			$action_row->setMetadata( 'page_title_path', $title );
		}

		// to aggregate the subtable data
		ArchivingHelper::deleteInvalidSummedColumnsFromDataTable( $data_tables[ Action::TYPE_PAGE_TITLE ] );
		return $data_tables[ Action::TYPE_PAGE_TITLE ];
	}
}
