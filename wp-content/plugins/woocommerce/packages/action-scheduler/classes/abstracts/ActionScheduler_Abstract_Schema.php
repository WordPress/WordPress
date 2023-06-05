<?php


/**
 * Class ActionScheduler_Abstract_Schema
 *
 * @package Action_Scheduler
 *
 * @codeCoverageIgnore
 *
 * Utility class for creating/updating custom tables
 */
abstract class ActionScheduler_Abstract_Schema {

	/**
	 * @var int Increment this value in derived class to trigger a schema update.
	 */
	protected $schema_version = 1;

	/**
	 * @var string Schema version stored in database.
	 */
	protected $db_version;

	/**
	 * @var array Names of tables that will be registered by this class.
	 */
	protected $tables = [];

	/**
	 * Can optionally be used by concrete classes to carry out additional initialization work
	 * as needed.
	 */
	public function init() {}

	/**
	 * Register tables with WordPress, and create them if needed.
	 *
	 * @param bool $force_update Optional. Default false. Use true to always run the schema update.
	 *
	 * @return void
	 */
	public function register_tables( $force_update = false ) {
		global $wpdb;

		// make WP aware of our tables
		foreach ( $this->tables as $table ) {
			$wpdb->tables[] = $table;
			$name           = $this->get_full_table_name( $table );
			$wpdb->$table   = $name;
		}

		// create the tables
		if ( $this->schema_update_required() || $force_update ) {
			foreach ( $this->tables as $table ) {
				/**
				 * Allow custom processing before updating a table schema.
				 *
				 * @param string $table Name of table being updated.
				 * @param string $db_version Existing version of the table being updated.
				 */
				do_action( 'action_scheduler_before_schema_update', $table, $this->db_version );
				$this->update_table( $table );
			}
			$this->mark_schema_update_complete();
		}
	}

	/**
	 * @param string $table The name of the table
	 *
	 * @return string The CREATE TABLE statement, suitable for passing to dbDelta
	 */
	abstract protected function get_table_definition( $table );

	/**
	 * Determine if the database schema is out of date
	 * by comparing the integer found in $this->schema_version
	 * with the option set in the WordPress options table
	 *
	 * @return bool
	 */
	private function schema_update_required() {
		$option_name      = 'schema-' . static::class;
		$this->db_version = get_option( $option_name, 0 );

		// Check for schema option stored by the Action Scheduler Custom Tables plugin in case site has migrated from that plugin with an older schema
		if ( 0 === $this->db_version ) {

			$plugin_option_name = 'schema-';

			switch ( static::class ) {
				case 'ActionScheduler_StoreSchema' :
					$plugin_option_name .= 'Action_Scheduler\Custom_Tables\DB_Store_Table_Maker';
					break;
				case 'ActionScheduler_LoggerSchema' :
					$plugin_option_name .= 'Action_Scheduler\Custom_Tables\DB_Logger_Table_Maker';
					break;
			}

			$this->db_version = get_option( $plugin_option_name, 0 );

			delete_option( $plugin_option_name );
		}

		return version_compare( $this->db_version, $this->schema_version, '<' );
	}

	/**
	 * Update the option in WordPress to indicate that
	 * our schema is now up to date
	 *
	 * @return void
	 */
	private function mark_schema_update_complete() {
		$option_name = 'schema-' . static::class;

		// work around race conditions and ensure that our option updates
		$value_to_save = (string) $this->schema_version . '.0.' . time();

		update_option( $option_name, $value_to_save );
	}

	/**
	 * Update the schema for the given table
	 *
	 * @param string $table The name of the table to update
	 *
	 * @return void
	 */
	private function update_table( $table ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$definition = $this->get_table_definition( $table );
		if ( $definition ) {
			$updated = dbDelta( $definition );
			foreach ( $updated as $updated_table => $update_description ) {
				if ( strpos( $update_description, 'Created table' ) === 0 ) {
					do_action( 'action_scheduler/created_table', $updated_table, $table );
				}
			}
		}
	}

	/**
	 * @param string $table
	 *
	 * @return string The full name of the table, including the
	 *                table prefix for the current blog
	 */
	protected function get_full_table_name( $table ) {
		return $GLOBALS[ 'wpdb' ]->prefix . $table;
	}

	/**
	 * Confirms that all of the tables registered by this schema class have been created.
	 *
	 * @return bool
	 */
	public function tables_exist() {
		global $wpdb;

		$existing_tables = $wpdb->get_col( 'SHOW TABLES' );
		$expected_tables = array_map(
			function ( $table_name ) use ( $wpdb ) {
				return $wpdb->prefix . $table_name;
			},
			$this->tables
		);

		return count( array_intersect( $existing_tables, $expected_tables ) ) === count( $expected_tables );
	}
}
