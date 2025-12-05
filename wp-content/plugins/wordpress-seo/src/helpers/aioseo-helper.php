<?php

namespace Yoast\WP\SEO\Helpers;

use wpdb;

/**
 * The AIOSEO Helper.
 */
class Aioseo_Helper {

	/**
	 * The WordPress database instance.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The wpdb helper.
	 *
	 * @var Wpdb_Helper
	 */
	protected $wpdb_helper;

	/**
	 * Class constructor.
	 *
	 * @param wpdb        $wpdb        The WordPress database instance.
	 * @param Wpdb_Helper $wpdb_helper The wpdb helper.
	 */
	public function __construct( wpdb $wpdb, Wpdb_Helper $wpdb_helper ) {
		$this->wpdb        = $wpdb;
		$this->wpdb_helper = $wpdb_helper;
	}

	/**
	 * Retrieves the AIOSEO table name along with the db prefix.
	 *
	 * @return string The AIOSEO table name along with the db prefix.
	 */
	public function get_table() {
		return $this->wpdb->prefix . 'aioseo_posts';
	}

	/**
	 * Determines if the AIOSEO database table exists.
	 *
	 * @return bool True if the table is found.
	 */
	public function aioseo_exists() {
		return $this->wpdb_helper->table_exists( $this->get_table() ) === true;
	}

	/**
	 * Retrieves the option where the global settings exist.
	 *
	 * @return array The option where the global settings exist.
	 */
	public function get_global_option() {
		return \json_decode( \get_option( 'aioseo_options', '' ), true );
	}
}
