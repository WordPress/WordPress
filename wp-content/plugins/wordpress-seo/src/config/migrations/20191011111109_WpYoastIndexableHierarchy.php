<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class WpYoastIndexableHierarchy.
 */
class WpYoastIndexableHierarchy extends Migration {

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

		$indexable_table = $this->create_table( $table_name, [ 'id' => false ] );

		$indexable_table->column(
			'indexable_id',
			'integer',
			[
				'primary_key' => true,
				'unsigned'    => true,
				'null'        => true,
				'limit'       => 11,
			]
		);
		$indexable_table->column(
			'ancestor_id',
			'integer',
			[
				'primary_key' => true,
				'unsigned'    => true,
				'null'        => true,
				'limit'       => 11,
			]
		);
		$indexable_table->column(
			'depth',
			'integer',
			[
				'unsigned' => true,
				'null'     => true,
				'limit'    => 11,
			]
		);
		$indexable_table->finish();

		$this->add_index( $table_name, 'indexable_id', [ 'name' => 'indexable_id' ] );
		$this->add_index( $table_name, 'ancestor_id', [ 'name' => 'ancestor_id' ] );
		$this->add_index( $table_name, 'depth', [ 'name' => 'depth' ] );
	}

	/**
	 * Migration up.
	 *
	 * @return void
	 */
	public function down() {
		$this->drop_table( $this->get_table_name() );
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
