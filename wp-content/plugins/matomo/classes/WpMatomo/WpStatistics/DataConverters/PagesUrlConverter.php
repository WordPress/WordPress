<?php

namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
use Piwik\Metrics;
use Piwik\Plugins\Actions\ArchivingHelper;
use Piwik\Tracker\Action;
use Piwik\Tracker\PageUrl;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class PagesUrlConverter extends NumberConverter implements DataConverterInterface {

	public static function convert( array $wp_statistics_data ) {
		$first_page = reset( $wp_statistics_data );
		$key        = isset( $first_page['uri'] ) ? 'uri' : 'str_url';

		$rows                   = self::aggregate_by_key( $wp_statistics_data, $key );
		$main_url_without_slash = site_url();
		$main_url_without_slash = rtrim( $main_url_without_slash, '/' );
		$data_tables            = [
			Action::TYPE_PAGE_URL => new DataTable(),
		];
		ArchivingHelper::reloadConfig();
		foreach ( $rows as $row ) {
			$whole_url = $main_url_without_slash . '/' . ltrim( $row->getColumn( 'label' ), '/' );
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$row_label = str_replace( array_keys( PageUrl::$urlPrefixMap ), '', $whole_url );
			$row->setColumn( Metrics::INDEX_PAGE_NB_HITS, $row['nb_visits'] );
			$row->setColumn( Metrics::INDEX_NB_VISITS, $row['nb_visits'] );
			$row->setColumn( Metrics::INDEX_NB_UNIQ_VISITORS, $row['nb_visits'] );
			$row->deleteColumn( 'nb_visits' );
			$row->deleteColumn( 'nb_uniq_visitors' );

			$action_row = ArchivingHelper::getActionRow( $row_label, Action::TYPE_PAGE_URL, '', $data_tables );

			$row->deleteColumn( 'label' );

			$action_row->sumRow( $row, $copy_metadata = false );

			if ( $action_row->getColumn( 'label' ) !== DataTable::LABEL_SUMMARY_ROW ) {
				$action_row->setMetadata( 'url', $whole_url );
			}
		}
		// to aggregate the subtable data
		ArchivingHelper::deleteInvalidSummedColumnsFromDataTable( $data_tables[ Action::TYPE_PAGE_URL ] );
		return $data_tables[ Action::TYPE_PAGE_URL ];
	}
}
