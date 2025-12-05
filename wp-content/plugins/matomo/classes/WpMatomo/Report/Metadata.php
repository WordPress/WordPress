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

class Metadata {

	public static $cache_all_reports      = [];
	public static $cache_all_report_pages = [];

	public function get_all_reports() {
		if ( ! empty( self::$cache_all_reports ) ) {
			return self::$cache_all_reports;
		}

		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		$report_dates      = new Dates();
		$report_date       = $report_dates->get_date_from_query();
		[ $period, $date ] = $report_dates->detect_period_and_date( $report_date );

		if ( $idsite ) {
			Bootstrap::do_bootstrap();

			$all_reports = Request::processRequest(
				'API.getReportMetadata',
				[
					'idSite'       => $idsite,
					'filter_limit' => - 1,
					'period'       => $period,
					'date'         => $date,
				]
			);
			foreach ( $all_reports as $single_report ) {
				if ( isset( $single_report['uniqueId'] ) ) {
					self::$cache_all_reports[ $single_report['uniqueId'] ] = $single_report;
				}
			}
		}

		return self::$cache_all_reports;
	}

	/**
	 * @internal
	 * tests only
	 */
	public static function clear_cache() {
		self::$cache_all_reports      = [];
		self::$cache_all_report_pages = [];
	}

	public function find_report_by_unique_id( $unique_id ) {
		if ( Renderer::CUSTOM_UNIQUE_ID_VISITS_OVER_TIME === $unique_id ) {
			return [
				'uniqueId' => Renderer::CUSTOM_UNIQUE_ID_VISITS_OVER_TIME,
				'name'     => 'Visits over time',
			];
		}
		$all_reports = self::get_all_reports();

		if ( isset( $all_reports[ $unique_id ] ) ) {
			return $all_reports[ $unique_id ];
		}
	}

	public function get_all_report_pages() {
		if ( ! empty( self::$cache_all_report_pages ) ) {
			return self::$cache_all_report_pages;
		}

		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		if ( $idsite ) {
			Bootstrap::do_bootstrap();

			self::$cache_all_report_pages = Request::processRequest(
				'API.getReportPagesMetadata',
				[
					'idSite'       => $idsite,
					'filter_limit' => - 1,
				]
			);
		}

		return self::$cache_all_report_pages;
	}

	public function find_report_page_params_by_report_metadata( $report_metadata ) {
		if ( empty( $report_metadata['module'] )
			 || empty( $report_metadata['action'] ) ) {
			return [];
		}

		$report_pages = self::get_all_report_pages();

		foreach ( $report_pages as $report_page ) {
			if ( ! empty( $report_page['widgets'] ) ) {
				foreach ( $report_page['widgets'] as $widget ) {
					if ( ! empty( $widget['module'] ) && $widget['module'] === $report_metadata['module']
						 && ! empty( $widget['action'] ) && $widget['action'] === $report_metadata['action'] ) {
						return [
							'category'    => $report_page['category']['id'],
							'subcategory' => $report_page['subcategory']['id'],
						];
					}
				}
			}
		}

		// we can't resolve all automatically since reportId != widgetId and the used action may differe etc...
		// we're hard coding some manually

		if ( 'Actions_get' === $report_metadata['uniqueId'] ) {
			return [
				'category'    => 'General_Visitors',
				'subcategory' => 'General_Overview',
			];
		} elseif ( 'Goals_get' === $report_metadata['uniqueId'] ) {
			return [
				'category'    => 'Goals_Goals',
				'subcategory' => 'General_Overview',
			];
		} elseif ( 'Goals_get_idGoal--ecommerceOrder' === $report_metadata['uniqueId'] ) {
			return [
				'category'    => 'Goals_Ecommerce',
				'subcategory' => 'General_Overview',
			];
		} elseif ( 'Goals_getItemsName' === $report_metadata['uniqueId'] ) {
			return [
				'category'    => 'Goals_Ecommerce',
				'subcategory' => 'Goals_Products',
			];
		}

		return [];
	}
}
