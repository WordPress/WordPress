<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class AddColumnsToIndexables.
 */
class AddColumnsToIndexables extends Migration {

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
		$tables  = $this->get_tables();
		$blog_id = \get_current_blog_id();
		foreach ( $tables as $table ) {
			$this->add_column(
				$table,
				'blog_id',
				'biginteger',
				[
					'null'    => false,
					'limit'   => 20,
					'default' => $blog_id,
				]
			);
		}

		$attr_limit_32 = [
			'null'  => true,
			'limit' => 32,
		];
		$attr_limit_64 = [
			'null'  => true,
			'limit' => 64,
		];

		$indexable_table = $this->get_indexable_table();
		$this->add_column( $indexable_table, 'language', 'string', $attr_limit_32 );
		$this->add_column( $indexable_table, 'region', 'string', $attr_limit_32 );
		$this->add_column( $indexable_table, 'schema_page_type', 'string', $attr_limit_64 );
		$this->add_column( $indexable_table, 'schema_article_type', 'string', $attr_limit_64 );
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$tables = $this->get_tables();
		foreach ( $tables as $table ) {
			$this->remove_column( $table, 'blog_id' );
		}

		$indexable_table = $this->get_indexable_table();
		$this->remove_column( $indexable_table, 'language' );
		$this->remove_column( $indexable_table, 'region' );
		$this->remove_column( $indexable_table, 'schema_page_type' );
		$this->remove_column( $indexable_table, 'schema_article_type' );
	}

	/**
	 * Retrieves the Indexable table.
	 *
	 * @return string The Indexable table name.
	 */
	protected function get_indexable_table() {
		return Model::get_table_name( 'Indexable' );
	}

	/**
	 * Retrieves the table names to use.
	 *
	 * @return string[] The table names to use.
	 */
	protected function get_tables() {
		return [
			$this->get_indexable_table(),
			Model::get_table_name( 'Indexable_Hierarchy' ),
			Model::get_table_name( 'Primary_Term' ),
		];
	}
}
