<?php


namespace Action_Scheduler\Migration;

/**
 * Class Runner
 *
 * @package Action_Scheduler\Migration
 *
 * @since 3.0.0
 *
 * @codeCoverageIgnore
 */
class Runner {
	/** @var ActionScheduler_Store */
	private $source_store;

	/** @var ActionScheduler_Store */
	private $destination_store;

	/** @var ActionScheduler_Logger */
	private $source_logger;

	/** @var ActionScheduler_Logger */
	private $destination_logger;

	/** @var BatchFetcher */
	private $batch_fetcher;

	/** @var ActionMigrator */
	private $action_migrator;

	/** @var LogMigrator */
	private $log_migrator;

	/** @var ProgressBar */
	private $progress_bar;

	/**
	 * Runner constructor.
	 *
	 * @param Config $config Migration configuration object.
	 */
	public function __construct( Config $config ) {
		$this->source_store       = $config->get_source_store();
		$this->destination_store  = $config->get_destination_store();
		$this->source_logger      = $config->get_source_logger();
		$this->destination_logger = $config->get_destination_logger();

		$this->batch_fetcher = new BatchFetcher( $this->source_store );
		if ( $config->get_dry_run() ) {
			$this->log_migrator    = new DryRun_LogMigrator( $this->source_logger, $this->destination_logger );
			$this->action_migrator = new DryRun_ActionMigrator( $this->source_store, $this->destination_store, $this->log_migrator );
		} else {
			$this->log_migrator    = new LogMigrator( $this->source_logger, $this->destination_logger );
			$this->action_migrator = new ActionMigrator( $this->source_store, $this->destination_store, $this->log_migrator );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$this->progress_bar = $config->get_progress_bar();
		}
	}

	/**
	 * Run migration batch.
	 *
	 * @param int $batch_size Optional batch size. Default 10.
	 *
	 * @return int Size of batch processed.
	 */
	public function run( $batch_size = 10 ) {
		$batch = $this->batch_fetcher->fetch( $batch_size );
		$batch_size = count( $batch );

		if ( ! $batch_size ) {
			return 0;
		}

		if ( $this->progress_bar ) {
			/* translators: %d: amount of actions */
			$this->progress_bar->set_message( sprintf( _n( 'Migrating %d action', 'Migrating %d actions', $batch_size, 'woocommerce' ), number_format_i18n( $batch_size ) ) );
			$this->progress_bar->set_count( $batch_size );
		}

		$this->migrate_actions( $batch );

		return $batch_size;
	}

	/**
	 * Migration a batch of actions.
	 *
	 * @param array $action_ids List of action IDs to migrate.
	 */
	public function migrate_actions( array $action_ids ) {
		do_action( 'action_scheduler/migration_batch_starting', $action_ids );

		\ActionScheduler::logger()->unhook_stored_action();
		$this->destination_logger->unhook_stored_action();

		foreach ( $action_ids as $source_action_id ) {
			$destination_action_id = $this->action_migrator->migrate( $source_action_id );
			if ( $destination_action_id ) {
				$this->destination_logger->log( $destination_action_id, sprintf(
					/* translators: 1: source action ID 2: source store class 3: destination action ID 4: destination store class */
					__( 'Migrated action with ID %1$d in %2$s to ID %3$d in %4$s', 'woocommerce' ),
					$source_action_id,
					get_class( $this->source_store ),
					$destination_action_id,
					get_class( $this->destination_store )
				) );
			}

			if ( $this->progress_bar ) {
				$this->progress_bar->tick();
			}
		}

		if ( $this->progress_bar ) {
			$this->progress_bar->finish();
		}

		\ActionScheduler::logger()->hook_stored_action();

		do_action( 'action_scheduler/migration_batch_complete', $action_ids );
	}

	/**
	 * Initialize destination store and logger.
	 */
	public function init_destination() {
		$this->destination_store->init();
		$this->destination_logger->init();
	}
}
