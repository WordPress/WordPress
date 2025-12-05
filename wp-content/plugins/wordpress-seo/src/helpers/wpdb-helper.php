<?php

namespace Yoast\WP\SEO\Helpers;

use wpdb;

/**
 * A helper object for the wpdb.
 */
class Wpdb_Helper {

	/**
	 * The WordPress database instance.
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Constructs a Wpdb_Helper instance.
	 *
	 * @param wpdb $wpdb The WordPress database instance.
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Check if table exists.
	 *
	 * @param string $table The table to be checked.
	 *
	 * @return bool Whether the table exists.
	 */
	public function table_exists( $table ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$table_exists = $this->wpdb->get_var( "SHOW TABLES LIKE '{$table}'" );
		if ( \is_wp_error( $table_exists ) || $table_exists === null ) {
			return false;
		}

		return true;
	}
}
