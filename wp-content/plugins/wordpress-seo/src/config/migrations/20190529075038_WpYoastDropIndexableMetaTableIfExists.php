<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class WpYoastDropIndexableMetaTableIfExists.
 */
class WpYoastDropIndexableMetaTableIfExists extends Migration {

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
		$table_name = $this->get_table_name();

		// This can be done safely as it executes a DROP IF EXISTS.
		$this->drop_table( $table_name );
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		// No down required. This specific table should never exist.
	}

	/**
	 * Retrieves the table name to use.
	 *
	 * @return string The table name to use.
	 */
	protected function get_table_name() {
		return Model::get_table_name( 'Indexable_Meta' );
	}
}
