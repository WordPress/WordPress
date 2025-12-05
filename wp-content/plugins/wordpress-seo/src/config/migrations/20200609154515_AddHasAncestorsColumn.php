<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\WordPress\Wrapper;

/**
 * Class AddHasAncestorsColumn.
 */
class AddHasAncestorsColumn extends Migration {

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
		$this->add_column(
			Model::get_table_name( 'Indexable' ),
			'has_ancestors',
			'boolean',
			[
				'default' => false,
			]
		);

		Wrapper::get_wpdb()->query(
			'
			UPDATE ' . Model::get_table_name( 'Indexable' ) . '
			SET has_ancestors = 1
			WHERE id IN ( SELECT indexable_id FROM ' . Model::get_table_name( 'Indexable_Hierarchy' ) . ' )
			'
		);
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$this->remove_column( Model::get_table_name( 'Indexable' ), 'has_ancestors' );
	}
}
