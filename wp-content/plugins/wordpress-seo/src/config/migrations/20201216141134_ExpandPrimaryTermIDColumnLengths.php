<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * ExpandPrimaryTermIDColumnLengths class.
 */
class ExpandPrimaryTermIDColumnLengths extends Migration {

	/**
	 * The plugin this migration belongs to.
	 *
	 * @var string
	 */
	public static $plugin = 'free';

	/**
	 * The columns to change the column type and length of.
	 *
	 * @var string[]
	 */
	protected static $columns_to_change = [
		'post_id',
		'term_id',
	];

	/**
	 * Migration up.
	 *
	 * @return void
	 */
	public function up() {
		foreach ( self::$columns_to_change as $column ) {
			$this->change_column(
				$this->get_table_name(),
				$column,
				'biginteger',
				[ 'limit' => 20 ]
			);
		}
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
	}

	/**
	 * Retrieves the table name to use for storing indexables.
	 *
	 * @return string The table name to use.
	 */
	protected function get_table_name() {
		return Model::get_table_name( 'Primary_Term' );
	}
}
