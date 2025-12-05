<?php

namespace WpMatomo\WpStatistics;

use Piwik\ArchiveProcessor\Parameters;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\DataAccess\ArchiveWriter;
use Piwik\Date;
use Piwik\Option;
use Piwik\Period\Factory;
use Piwik\Plugin\Manager;
use Piwik\Segment;
use Piwik\Site;
use Psr\Log\LoggerInterface;
use Piwik\Archive\ArchiveInvalidator;
use WP_STATISTICS\DB;
use WpMatomo\Db\Settings;
use WpMatomo\ScheduledTasks;
use WpMatomo\WpStatistics\Exceptions\MaxEndDateReachedException;
use WpMatomo\WpStatistics\Importers\Actions\RecordImporter;
/**
 * @package WpMatomo
 * @subpackage WpStatisticsImport
 *
 * phpcs:disable WordPress.DB
 */
class Importer {

	const IS_IMPORTED_FROM_WPS_NUMERIC = 'WpStatisticsImporter_isImportedFromWpStatistics';

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var array|null
	 */
	private $record_importers;

	/**
	 * @var string
	 */
	private $no_data_message_removed = false;

	/**
	 * @var Date
	 */
	private $end_date = null;

	private $should_rethrow = false;

	public function __construct( LoggerInterface $logger ) {
		$this->logger   = $logger;
		$this->end_date = $this->get_ending_date();
	}

	public function set_should_rethrow( $should_rethrow ) {
		$this->should_rethrow = $should_rethrow;
	}

	/**
	 * Returns the first date in the matomo records
	 *
	 * @return Date
	 */
	protected function get_ending_date() {
		global $wpdb;
		$db_settings = new Settings();
		$table       = $db_settings->prefix_table_name( 'log_visit' );
		$sql         = <<<SQL
SELECT min(visit_last_action_time) from $table
SQL;
		try {
			$row = $wpdb->get_row( $sql, ARRAY_N );
			if ( ! empty( $row[0] ) ) {
				return Date::factory( $row[0] );
			} else {
				return Date::yesterday();
			}
		} catch ( \Exception $e ) {
			return Date::yesterday();
		}
	}


	/**
	 * Returns the first date in the wpStatistics data
	 *
	 * @return \Piwik\Date
	 */
	protected function get_started() {
		global $wpdb;
		$table = DB::table( 'visit' );
		if ( ! empty( $table ) ) {
			$sql = <<<SQL
SELECT min(last_visit) from $table
SQL;
		} else {
			$table = DB::table( 'visitor' );

			$sql = <<<SQL
SELECT min(last_view) from $table
SQL;
		}

		$row  = $wpdb->get_row( $sql, ARRAY_N );
		$date = $row[0];
		if ( empty( $date ) ) {
			return Date::yesterday();
		}

		return Date::factory( $date );
	}

	/**
	 * Update the first date in the configuration.
	 * Otherwise records are here but the date picker does not allow to select these dates
	 *
	 * @param int  $id_site
	 * @param Date $date
	 *
	 * @return void
	 */
	private function adjust_matomo_date( $id_site, Date $date ) {
		global $wpdb;
		$db_settings  = new Settings();
		$prefix_table = $db_settings->prefix_table_name( 'site' );
		$wpdb->update( $prefix_table, [ 'ts_created' => $date->toString( 'Y-m-d h:i:s' ) ], [ 'idsite' => $id_site ] );
	}

	public function import( $id_site, $archive = true ) {
		$end   = $this->end_date;
		$start = $this->get_started();

		$this->adjust_matomo_date( $id_site, $start );
		try {
			$this->no_data_message_removed = false;

			$end_plus_one = $end->addDay( 1 );

			if ( $start->getTimestamp() >= $end_plus_one->getTimestamp() ) {
				throw new \InvalidArgumentException( "Invalid date range, start date is later than end date: {$start},{$end}" );
			}
			$record_importers = $this->get_record_importers();
			$site             = new Site( $id_site );
			// phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed
			for ( $date = $start; $date->getTimestamp() < $end_plus_one->getTimestamp(); $date = $date->addDay( 1 ) ) {
				$this->logger->notice(
					'Importing data for date {date}...',
					[
						'date' => $date->toString(),
					]
				);

				try {
					$this->import_day( $site, $date, $record_importers );
				} finally {
					// force delete all tables in case they aren't all freed
					\Piwik\DataTable\Manager::getInstance()->deleteAll();
				}
			}
			unset( $record_importers );
		} catch ( MaxEndDateReachedException $ex ) {
			$this->logger->info( 'Max end date reached. This occurs in Matomo for WordPress installs when the importer tries to import days on or after the day Matomo for WordPress installed.' );

			if ( true === $archive ) {
				// by launching the archiver now the weekly, monthly and yearly archives should be generated right away and it won't
				// take up to an hour. Also by running it on the cli we have less risk that this long running archiving process times out
				$this->logger->info( 'Matomo Analytics starting the report generation of weekly, monthly and yearly reports. This may take a while.' );
				$scheduled_tasks = new ScheduledTasks( \WpMatomo::$settings );
				$scheduled_tasks->archive();
			}
			$this->logger->info( 'Matomo Analytics report generation finished' );

			return true;
		} catch ( \Exception $ex ) {
			$this->on_error( $ex );
			return true;
		}

		return false;
	}

	/**
	 * For use in record_importers that need to archive data for segments.
	 *
	 * @var RecordImporter[] $record_importers
	 * @throws MaxEndDateReachedException In case we have reach the end date to proceed.
	 */
	public function import_day( Site $site, Date $date, $record_importers ) {
		if ( $this->end_date && $this->end_date->isEarlier( $date ) ) {
			throw new MaxEndDateReachedException();
		}
		$archive_writer = $this->make_archive_writer( $site, $date );
		$archive_writer->initNewArchive();

		$record_inserter = new RecordInserter( $archive_writer );

		foreach ( $record_importers as $plugin => $record_importer ) {
			if ( ! $record_importer->supports_site() ) {
				continue;
			}

			$this->logger->info(
				'Importing data for the {plugin} plugin.',
				[
					'plugin' => $plugin,
				]
			);

			$record_importer->set_record_inserter( $record_inserter );

			$record_importer->import_records( $date );

			// since we recorded some data, at some time, remove the no data message
			if ( ! $this->no_data_message_removed ) {
				$this->remove_no_data_message( $site->getId() );
				$this->no_data_message_removed = true;
			}
		}

		$archive_writer->insertRecord( self::IS_IMPORTED_FROM_WPS_NUMERIC, 1 );
		$archive_writer->finalizeArchive();

		$invalidator                    = StaticContainer::get( ArchiveInvalidator::class );
		$invalidator->markArchivesAsInvalidated(
			[ $site->getId() ],
			[ $date ],
			'week',
			null,
			false,
			false,
			null,
			$ignore_purge_log_data_date = true
		);

		Common::destroy( $archive_writer );
	}

	private function make_archive_writer( Site $site, Date $date, $segment = '' ) {
		$period  = Factory::build( 'day', $date );
		$segment = new Segment( $segment, [ $site->getId() ] );

		$params = new Parameters( $site, $period, $segment );
		return new ArchiveWriter( $params );
	}

	/**
	 * @return RecordImporter[]
	 * @throws \Exception In case importer has no plugin name.
	 */
	private function get_record_importers() {
		if ( empty( $this->record_importers ) ) {
			$record_importers = Config::get_importers();

			$this->record_importers = [];
			foreach ( $record_importers as $record_importer_class ) {
				if ( ! defined( $record_importer_class . '::PLUGIN_NAME' ) ) {
					throw new \Exception( "The $record_importer_class record importer is missing the PLUGIN_NAME constant." );
				}

				$namespace   = explode( '\\', $record_importer_class );
				$plugin_name = array_pop( $namespace );
				if ( $this->is_plugin_unavailable( $record_importer_class::PLUGIN_NAME ) ) {
					continue;
				}

				$this->record_importers[ $plugin_name ] = $record_importer_class;
			}
		}

		$instances = [];
		foreach ( $this->record_importers as $plugin_name => $class_name ) {
			$instances[ $plugin_name ] = new $class_name( $this->logger );
		}
		return $instances;
	}

	private function remove_no_data_message( $id_site ) {
		$had_traffic_key = 'SitesManagerHadTrafficInPast_' . (int) $id_site;
		Option::set( $had_traffic_key, 1 );
	}

	private function is_plugin_unavailable( $plugin_name ) {
		return ! Manager::getInstance()->isPluginActivated( $plugin_name )
			|| ! Manager::getInstance()->isPluginLoaded( $plugin_name )
			|| ! Manager::getInstance()->isPluginInFilesystem( $plugin_name );
	}

	private function on_error( \Exception $ex ) {
		$this->logger->info( 'Unexpected Error: {ex}', [ 'ex' => $ex ] );

		if ( $this->should_rethrow ) {
			throw $ex;
		}
	}
}
