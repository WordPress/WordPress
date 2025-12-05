<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * CreateIndexableSubpagesIndex class.
 */
class CreateIndexableSubpagesIndex extends Migration {

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
		$this->change_column(
			$this->get_table_name(),
			'post_status',
			'string',
			[
				'null'  => true,
				'limit' => 20,
			]
		);
		$this->add_index(
			$this->get_table_name(),
			[ 'post_parent', 'object_type', 'post_status', 'object_id' ],
			[ 'name' => 'subpages' ]
		);
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$this->change_column(
			$this->get_table_name(),
			'post_status',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$this->remove_index(
			$this->get_table_name(),
			[ 'post_parent', 'object_type', 'post_status', 'object_id' ],
			[ 'name' => 'subpages' ]
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
