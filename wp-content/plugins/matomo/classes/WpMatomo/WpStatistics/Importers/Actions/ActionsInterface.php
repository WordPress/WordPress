<?php

namespace WpMatomo\WpStatistics\Importers\Actions;

use Piwik\Date;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
interface ActionsInterface {
	/**
	 * @param Date $date
	 *
	 * @return null
	 */
	public function import_records( Date $date );
}
