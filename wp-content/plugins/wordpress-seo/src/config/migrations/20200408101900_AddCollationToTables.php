<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class AddCollationToTables.
 */
class AddCollationToTables extends Migration {

	/**
	 * The plugin this migration belongs to.
	 *
	 * @var string
	 */
	public static $plugin = 'free';

	/**
	 * Migration up.
	 *
	 * @return void
	 */
	public function up() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		if ( empty( $charset_collate ) ) {
			return;
		}

		$tables = [
			Model::get_table_name( 'migrations' ),
			Model::get_table_name( 'Indexable' ),
			Model::get_table_name( 'Indexable_Hierarchy' ),
			Model::get_table_name( 'Primary_Term' ),
		];

		foreach ( $tables as $table ) {
			$this->query( 'ALTER TABLE ' . $table . ' CONVERT TO ' . \str_replace( 'DEFAULT ', '', $charset_collate ) );
		}
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		// No down required.
	}
}
