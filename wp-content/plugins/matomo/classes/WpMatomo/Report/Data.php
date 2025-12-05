<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Report;

use Piwik\API\Request;
use WpMatomo\Bootstrap;
use WpMatomo\Site;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Data {

	/**
	 * @param array      $report_metadata
	 * @param string     $period
	 * @param string     $date
	 * @param string     $sort_by_column
	 * @param string|int $filter_limit
	 *
	 * @return array  An array containing reportData, metrics, columns, ...
	 */
	public function fetch_report( $report_metadata, $period, $date, $sort_by_column, $filter_limit ) {
		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		Bootstrap::do_bootstrap();

		if ( empty( $idsite ) ) {
			return [];
		}

		$params = [
			'apiModule'          => $report_metadata['module'],
			'apiAction'          => $report_metadata['action'],
			'filter_limit'       => $filter_limit,
			'filter_sort_column' => $sort_by_column,
			'period'             => $period,
			'date'               => $date,
			'idSite'             => $idsite,
		];
		if ( ! empty( $report_metadata['parameters'] ) ) {
			$params = array_merge( $params, $report_metadata['parameters'] );
		}

		$report = Request::processRequest( 'API.getProcessedReport', $params );

		return $report;
	}
}
