<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class ResetIndexableHierarchyTable.
 */
class ResetIndexableHierarchyTable extends Migration {

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
		$this->query( 'TRUNCATE TABLE ' . $this->get_table_name() );
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		// Nothing to do.
	}

	/**
	 * Retrieves the table name to use.
	 *
	 * @return string The table name to use.
	 */
	protected function get_table_name() {
		return Model::get_table_name( 'Indexable_Hierarchy' );
	}
}
