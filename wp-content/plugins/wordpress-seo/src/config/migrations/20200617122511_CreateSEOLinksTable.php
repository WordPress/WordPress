<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * CreateSEOLinksTable class.
 */
class CreateSEOLinksTable extends Migration {

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

		// The table may already have been created by legacy code.
		// If not, create it exactly as it was.
		if ( ! $adapter->table_exists( $table_name ) ) {
			$table = $this->create_table( $table_name, [ 'id' => false ] );
			$table->column(
				'id',
				'biginteger',
				[
					'primary_key'    => true,
					'limit'          => 20,
					'unsigned'       => true,
					'auto_increment' => true,
				]
			);
			$table->column( 'url', 'string', [ 'limit' => 255 ] );
			$table->column(
				'post_id',
				'biginteger',
				[
					'limit'    => 20,
					'unsigned' => true,
				]
			);
			$table->column(
				'target_post_id',
				'biginteger',
				[
					'limit'    => 20,
					'unsigned' => true,
				]
			);
			$table->column( 'type', 'string', [ 'limit' => 8 ] );
			$table->finish();
		}
		if ( ! $adapter->has_index( $table_name, [ 'post_id', 'type' ], [ 'name' => 'link_direction' ] ) ) {
			$this->add_index( $table_name, [ 'post_id', 'type' ], [ 'name' => 'link_direction' ] );
		}

		// Add these columns outside of the initial table creation as these did not exist on the legacy table.
		$this->add_column( $table_name, 'indexable_id', 'integer', [ 'unsigned' => true ] );
		$this->add_column( $table_name, 'target_indexable_id', 'integer', [ 'unsigned' => true ] );
		$this->add_column( $table_name, 'height', 'integer', [ 'unsigned' => true ] );
		$this->add_column( $table_name, 'width', 'integer', [ 'unsigned' => true ] );
		$this->add_column( $table_name, 'size', 'integer', [ 'unsigned' => true ] );
		$this->add_column( $table_name, 'language', 'string', [ 'limit' => 32 ] );
		$this->add_column( $table_name, 'region', 'string', [ 'limit' => 32 ] );

		$this->add_index( $table_name, [ 'indexable_id', 'type' ], [ 'name' => 'indexable_link_direction' ] );
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$this->drop_table( $this->get_table_name() );
	}

	/**
	 * Returns the SEO Links table name.
	 *
	 * @return string
	 */
	private function get_table_name() {
		return Model::get_table_name( 'SEO_Links' );
	}
}
