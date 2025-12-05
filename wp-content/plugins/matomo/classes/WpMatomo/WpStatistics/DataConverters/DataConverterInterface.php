<?php
namespace WpMatomo\WpStatistics\DataConverters;

use Piwik\DataTable;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
interface DataConverterInterface {
	/**
	 * @param [] $wp_statistics_data
	 *
	 * @return DataTable
	 */
	public static function convert( array $wp_statistics_data);
}
