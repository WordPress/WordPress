<?php

/**
 * Class ActionScheduler_LoggerSchema
 *
 * @codeCoverageIgnore
 *
 * Creates a custom table for storing action logs
 */
class ActionScheduler_LoggerSchema extends ActionScheduler_Abstract_Schema {
	const LOG_TABLE = 'actionscheduler_logs';

	/**
	 * @var int Increment this value to trigger a schema update.
	 */
	protected $schema_version = 3;

	public function __construct() {
		$this->tables = [
			self::LOG_TABLE,
		];
	}

	/**
	 * Performs additional setup work required to support this schema.
	 */
	public function init() {
		add_action( 'action_scheduler_before_schema_update', array( $this, 'update_schema_3_0' ), 10, 2 );
	}

	protected function get_table_definition( $table ) {
		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		switch ( $table ) {

			case self::LOG_TABLE:

				$default_date = ActionScheduler_StoreSchema::DEFAULT_DATE;
				return "CREATE TABLE $table_name (
				        log_id bigint(20) unsigned NOT NULL auto_increment,
				        action_id bigint(20) unsigned NOT NULL,
				        message text NOT NULL,
				        log_date_gmt datetime NULL default '{$default_date}',
				        log_date_local datetime NULL default '{$default_date}',
				        PRIMARY KEY  (log_id),
				        KEY action_id (action_id),
				        KEY log_date_gmt (log_date_gmt)
				        ) $charset_collate";

			default:
				return '';
		}
	}

	/**
	 * Update the logs table schema, allowing datetime fields to be NULL.
	 *
	 * This is needed because the NOT NULL constraint causes a conflict with some versions of MySQL
	 * configured with sql_mode=NO_ZERO_DATE, which can for instance lead to tables not being created.
	 *
	 * Most other schema updates happen via ActionScheduler_Abstract_Schema::update_table(), however
	 * that method relies on dbDelta() and this change is not possible when using that function.
	 *
	 * @param string $table Name of table being updated.
	 * @param string $db_version The existing schema version of the table.
	 */
	public function update_schema_3_0( $table, $db_version ) {
		global $wpdb;

		if ( 'actionscheduler_logs' !== $table || version_compare( $db_version, '3', '>=' ) ) {
			return;
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_name   = $wpdb->prefix . 'actionscheduler_logs';
		$table_list   = $wpdb->get_col( "SHOW TABLES LIKE '{$table_name}'" );
		$default_date = ActionScheduler_StoreSchema::DEFAULT_DATE;

		if ( ! empty( $table_list ) ) {
			$query = "
				ALTER TABLE {$table_name}
				MODIFY COLUMN log_date_gmt datetime NULL default '{$default_date}',
				MODIFY COLUMN log_date_local datetime NULL default '{$default_date}'
			";
			$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
