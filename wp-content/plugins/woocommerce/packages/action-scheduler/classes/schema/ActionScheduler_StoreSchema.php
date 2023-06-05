<?php

/**
 * Class ActionScheduler_StoreSchema
 *
 * @codeCoverageIgnore
 *
 * Creates custom tables for storing scheduled actions
 */
class ActionScheduler_StoreSchema extends ActionScheduler_Abstract_Schema {
	const ACTIONS_TABLE = 'actionscheduler_actions';
	const CLAIMS_TABLE  = 'actionscheduler_claims';
	const GROUPS_TABLE  = 'actionscheduler_groups';
	const DEFAULT_DATE  = '0000-00-00 00:00:00';

	/**
	 * @var int Increment this value to trigger a schema update.
	 */
	protected $schema_version = 6;

	public function __construct() {
		$this->tables = [
			self::ACTIONS_TABLE,
			self::CLAIMS_TABLE,
			self::GROUPS_TABLE,
		];
	}

	/**
	 * Performs additional setup work required to support this schema.
	 */
	public function init() {
		add_action( 'action_scheduler_before_schema_update', array( $this, 'update_schema_5_0' ), 10, 2 );
	}

	protected function get_table_definition( $table ) {
		global $wpdb;
		$table_name       = $wpdb->$table;
		$charset_collate  = $wpdb->get_charset_collate();
		$max_index_length = 191; // @see wp_get_db_schema()
		$default_date     = self::DEFAULT_DATE;
		switch ( $table ) {

			case self::ACTIONS_TABLE:

				return "CREATE TABLE {$table_name} (
				        action_id bigint(20) unsigned NOT NULL auto_increment,
				        hook varchar(191) NOT NULL,
				        status varchar(20) NOT NULL,
				        scheduled_date_gmt datetime NULL default '{$default_date}',
				        scheduled_date_local datetime NULL default '{$default_date}',
				        args varchar($max_index_length),
				        schedule longtext,
				        group_id bigint(20) unsigned NOT NULL default '0',
				        attempts int(11) NOT NULL default '0',
				        last_attempt_gmt datetime NULL default '{$default_date}',
				        last_attempt_local datetime NULL default '{$default_date}',
				        claim_id bigint(20) unsigned NOT NULL default '0',
				        extended_args varchar(8000) DEFAULT NULL,
				        PRIMARY KEY  (action_id),
				        KEY hook (hook($max_index_length)),
				        KEY status (status),
				        KEY scheduled_date_gmt (scheduled_date_gmt),
				        KEY args (args($max_index_length)),
				        KEY group_id (group_id),
				        KEY last_attempt_gmt (last_attempt_gmt),
				        KEY `claim_id_status_scheduled_date_gmt` (`claim_id`, `status`, `scheduled_date_gmt`)
				        ) $charset_collate";

			case self::CLAIMS_TABLE:

				return "CREATE TABLE {$table_name} (
				        claim_id bigint(20) unsigned NOT NULL auto_increment,
				        date_created_gmt datetime NULL default '{$default_date}',
				        PRIMARY KEY  (claim_id),
				        KEY date_created_gmt (date_created_gmt)
				        ) $charset_collate";

			case self::GROUPS_TABLE:

				return "CREATE TABLE {$table_name} (
				        group_id bigint(20) unsigned NOT NULL auto_increment,
				        slug varchar(255) NOT NULL,
				        PRIMARY KEY  (group_id),
				        KEY slug (slug($max_index_length))
				        ) $charset_collate";

			default:
				return '';
		}
	}

	/**
	 * Update the actions table schema, allowing datetime fields to be NULL.
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
	public function update_schema_5_0( $table, $db_version ) {
		global $wpdb;

		if ( 'actionscheduler_actions' !== $table || version_compare( $db_version, '5', '>=' ) ) {
			return;
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_name   = $wpdb->prefix . 'actionscheduler_actions';
		$table_list   = $wpdb->get_col( "SHOW TABLES LIKE '{$table_name}'" );
		$default_date = self::DEFAULT_DATE;

		if ( ! empty( $table_list ) ) {
			$query = "
				ALTER TABLE {$table_name}
				MODIFY COLUMN scheduled_date_gmt datetime NULL default '{$default_date}',
				MODIFY COLUMN scheduled_date_local datetime NULL default '{$default_date}',
				MODIFY COLUMN last_attempt_gmt datetime NULL default '{$default_date}',
				MODIFY COLUMN last_attempt_local datetime NULL default '{$default_date}'
		";
			$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
