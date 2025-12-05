<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class AddIndexableObjectIdAndTypeIndex.
 */
class AddIndexableObjectIdAndTypeIndex extends Migration {

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
		$this->add_index(
			$this->get_table_name(),
			[
				'object_id',
				'object_type',
			],
			[
				'name' => 'object_id_and_type',
			]
		);
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$this->remove_index(
			$this->get_table_name(),
			[
				'object_id',
				'object_type',
			],
			[
				'name' => 'object_id_and_type',
			]
		);
	}

	/**
	 * Retrieves the table name to use for storing indexables.
	 *
	 * @return string The table name to use.
	 */
	protected function get_table_name() {
		return Model::get_table_name( 'Indexable' );
	}
}
