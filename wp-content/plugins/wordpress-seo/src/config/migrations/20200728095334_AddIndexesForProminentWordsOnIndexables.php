<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * AddIndexesForProminentWordsOnIndexables class.
 */
class AddIndexesForProminentWordsOnIndexables extends Migration {

	/**
	 * The plugin this migration belongs to.
	 *
	 * @var string
	 */
	public static $plugin = 'free';

	/**
	 * The columns on which an index should be added.
	 *
	 * @var string[]
	 */
	private $columns_with_index = [
		'prominent_words_version',
		'object_type',
		'object_sub_type',
		'post_status',
	];

	/**
	 * Migration up.
	 *
	 * @return void
	 */
	public function up() {
		$table_name = $this->get_table_name();
		$adapter    = $this->get_adapter();

		if ( ! $adapter->has_index( $table_name, $this->columns_with_index, [ 'name' => 'prominent_words' ] ) ) {
			$this->add_index(
				$table_name,
				$this->columns_with_index,
				[
					'name' => 'prominent_words',
				]
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
	 * Retrieves the table name to use.
	 *
	 * @return string The table name to use.
	 */
	protected function get_table_name() {
		return Model::get_table_name( 'Indexable' );
	}
}
