<?php
namespace WpMatomo\WpStatistics\Importers\Actions;

use Piwik\Common;
use Piwik\Plugins\VisitTime\Archiver;
use Piwik\Date;
use WpMatomo\WpStatistics\DataConverters\VisitsTimeConverter;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 */
class VisitsTimeImporter extends RecordImporter implements ActionsInterface {

	const PLUGIN_NAME = 'VisitTime';

	public function import_records( Date $date ) {
		$visits = $this->get_visitors( $date );

		$this->logger->debug( 'Import {nb_visits} visits...', [ 'nb_visits' => count( $visits ) ] );
		if ( $visits ) {
			$visits = VisitsTimeConverter::convert( $visits );
			$this->insert_record( Archiver::SERVER_TIME_RECORD_NAME, $visits );
		}
		Common::destroy( $visits );

		return $visits;
	}
}
