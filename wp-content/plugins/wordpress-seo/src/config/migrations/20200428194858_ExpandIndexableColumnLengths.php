<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Class ExpandIndexableColumnLengths.
 */
class ExpandIndexableColumnLengths extends Migration {

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
		$this->change_column( $this->get_table_name(), 'title', 'text', [ 'null' => true ] );
		$this->change_column( $this->get_table_name(), 'open_graph_title', 'text', [ 'null' => true ] );
		$this->change_column( $this->get_table_name(), 'twitter_title', 'text', [ 'null' => true ] );
		$this->change_column( $this->get_table_name(), 'open_graph_image_source', 'text', [ 'null' => true ] );
		$this->change_column( $this->get_table_name(), 'twitter_image_source', 'text', [ 'null' => true ] );
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$attr_limit_191 = [
			'null'  => true,
			'limit' => 191,
		];

		$this->change_column(
			$this->get_table_name(),
			'title',
			'string',
			$attr_limit_191
		);
		$this->change_column(
			$this->get_table_name(),
			'opengraph_title',
			'string',
			$attr_limit_191
		);
		$this->change_column(
			$this->get_table_name(),
			'twitter_title',
			'string',
			$attr_limit_191
		);
		$this->change_column(
			$this->get_table_name(),
			'open_graph_image_source',
			'string',
			$attr_limit_191
		);
		$this->change_column(
			$this->get_table_name(),
			'twitter_image_source',
			'string',
			$attr_limit_191
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
