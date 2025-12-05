<?php

namespace WpMatomo\WpStatistics;

/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class Config {

	const WP_STATISTICS_DATE_FORMAT = 'Y-m-d';

	public static function get_importers() {
		return [
			'WpMatomo\WpStatistics\Importers\Actions\PagesImporter',
			'WpMatomo\WpStatistics\Importers\Actions\ReferrersImporter',
			'WpMatomo\WpStatistics\Importers\Actions\UserCountryImporter',
			'WpMatomo\WpStatistics\Importers\Actions\DeviceDetectionImporter',
			'WpMatomo\WpStatistics\Importers\Actions\VisitorsImporter',
			'WpMatomo\WpStatistics\Importers\Actions\VisitsTimeImporter',
		];
	}
}
