<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * ReplacePermalinkHashIndex class.
 */
class ReplacePermalinkHashIndex extends Migration {

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
		$adapter    = $this->get_adapter();

		if ( ! $adapter->has_table( $table_name ) ) {
			return;
		}

		$this->change_column(
			$table_name,
			'permalink_hash',
			'string',
			[
				'null'  => true,
				'limit' => 40,
			]
		);

		if ( $adapter->has_index( $table_name, [ 'permalink_hash' ], [ 'name' => 'permalink_hash' ] ) ) {
			$this->remove_index(
				$table_name,
				[
					'permalink_hash',
				],
				[
					'name' => 'permalink_hash',
				]
			);
		}

		if ( ! $adapter->has_index( $table_name, [ 'permalink_hash', 'object_type' ], [ 'name' => 'permalink_hash_and_object_type' ] ) ) {
			$this->add_index(
				$table_name,
				[
					'permalink_hash',
					'object_type',
				],
				[
					'name' => 'permalink_hash_and_object_type',
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
		$table_name = $this->get_table_name();
		$adapter    = $this->get_adapter();

		if ( ! $adapter->has_table( $table_name ) ) {
			return;
		}

		if ( $adapter->has_index( $table_name, [ 'permalink_hash', 'object_type' ], [ 'name' => 'permalink_hash_and_object_type' ] ) ) {
			$this->remove_index(
				$table_name,
				[
					'permalink_hash',
					'object_type',
				],
				[
					'name' => 'permalink_hash_and_object_type',
				]
			);
		}

		$this->change_column(
			$table_name,
			'permalink_hash',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);

		if ( ! $adapter->has_index( $table_name, [ 'permalink_hash' ], [ 'name' => 'permalink_hash' ] ) ) {
			$this->add_index(
				$table_name,
				[
					'permalink_hash',
				],
				[
					'name' => 'permalink_hash',
				]
			);
		}
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
