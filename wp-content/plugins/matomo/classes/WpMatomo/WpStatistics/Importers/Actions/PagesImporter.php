<?php

namespace WpMatomo\WpStatistics\Importers\Actions;

use Piwik\Common;
use Piwik\Config as PiwikConfig;
use Piwik\Metrics;
use Piwik\Plugins\Actions\Archiver;
use Psr\Log\LoggerInterface;
use WP_STATISTICS\MetaBox\pages;
use Piwik\Date;
use WP_Statistics\Service\Admin\Metabox\MetaboxDataProvider;
use WpMatomo\WpStatistics\Config;
use WpMatomo\WpStatistics\DataConverters\PagesUrlConverter;
use WpMatomo\WpStatistics\DataConverters\PagesTitleConverter;
use WpMatomo\WpStatistics\DataConverters\SearchQueryConverter;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class PagesImporter extends RecordImporter implements ActionsInterface {

	const PLUGIN_NAME = 'Actions';

	public function __construct( LoggerInterface $logger ) {
		parent::__construct( $logger );
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_data_table_level_zero = @PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_actions'];
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$this->maximum_rows_in_sub_data_table = @PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_subtable_actions'];
	}

	public function import_records( Date $date ) {
		$limit = 100;
		$pages = [];
		$page  = 0;
		do {
			$page ++;
			if ( class_exists( '\WP_STATISTICS\MetaBox\pages' ) ) {
				$pages_found = pages::get(
					[
						'from'     => $date->toString( Config::WP_STATISTICS_DATE_FORMAT ),
						'to'       => $date->toString( Config::WP_STATISTICS_DATE_FORMAT ),
						'per_page' => $limit,
						'paged'    => $page,
					]
				);

				$has_no_data_prop     = ( array_key_exists( 'no_data', $pages_found ) && ( 1 === $pages_found['no_data'] ) );
				$has_empty_pages_prop = ( array_key_exists( 'pages', $pages_found ) && empty( $pages_found['pages'] ) );
				$no_data              = $has_no_data_prop || $has_empty_pages_prop;
			} else {
				$pages_found = $this->get_metabox_data_provider()->getTopPages(
					[
						'date'     => [
							'from' => $date->toString( Config::WP_STATISTICS_DATE_FORMAT ),
							'to'   => $date->toString( Config::WP_STATISTICS_DATE_FORMAT ),
						],
						'per_page' => $limit,
						'page'     => $page,
						'fields'   => [ 'id', 'uri', 'type', 'SUM(count) as views', 'page_id' ],
					]
				);

				$pages_found = array_map(
					function ( $row_std_class ) {
						return (array) $row_std_class;
					},
					$pages_found
				);

				$no_data = count( $pages_found ) < 1;
			}

			if ( ! $no_data ) {
				$pages = array_merge( $pages, array_key_exists( 'pages', $pages_found ) ? $pages_found['pages'] : $pages_found );
			}
		} while ( true !== $no_data );
		$search_keywords = SearchQueryConverter::convert( $pages );
		$this->logger->debug( 'Import {nb_keywords} search keywords...', [ 'nb_keywords' => $search_keywords->getRowsCount() ] );
		$this->insert_record( Archiver::SITE_SEARCH_RECORD_NAME, $search_keywords, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table );
		Common::destroy( $search_keywords );

		foreach ( $pages as $id => $page ) {
			$key = isset( $page['uri'] ) ? 'uri' : 'str_url';
			$uri = $page[ $key ];

			$pos = strpos( $uri, '?' );
			if ( false !== $pos ) {
				$pages[ $id ][ $key ] = substr( $uri, 0, $pos );
			}
		}
		$pages_url = PagesUrlConverter::convert( $pages );
		$this->logger->debug( 'Import {nb_pages} global pages...', [ 'nb_pages' => $pages_url->getRowsCount() ] );
		$this->insert_record( Archiver::PAGE_URLS_RECORD_NAME, $pages_url, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table, Metrics::INDEX_NB_VISITS );
		Common::destroy( $pages_url );

		$pages_title = PagesTitleConverter::convert( $pages );
		$this->logger->debug( 'Import {nb_pages} page titles...', [ 'nb_pages' => $pages_title->getRowsCount() ] );
		$this->insert_record( Archiver::PAGE_TITLES_RECORD_NAME, $pages_title, $this->maximum_rows_in_data_table_level_zero, $this->maximum_rows_in_sub_data_table, Metrics::INDEX_NB_VISITS );
		Common::destroy( $pages_title );
	}

	private function get_metabox_data_provider() {
		return new MetaboxDataProvider();
	}
}
