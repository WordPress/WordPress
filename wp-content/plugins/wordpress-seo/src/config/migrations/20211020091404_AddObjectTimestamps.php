<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * AddObjectTimestamps class.
 */
class AddObjectTimestamps extends Migration {

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
			$this->get_table_name(),
			'object_last_modified',
			'datetime',
			[
				'null'    => true,
				'default' => null,
			]
		);
		$this->add_column(
			$this->get_table_name(),
			'object_published_at',
			'datetime',
			[
				'null'    => true,
				'default' => null,
			]
		);
		$this->add_index(
			$this->get_table_name(),
			[
				'object_published_at',
				'is_robots_noindex',
				'object_type',
				'object_sub_type',
			],
			[
				'name' => 'published_sitemap_index',
			]
		);
	}

	/**
	 * Migration down.
	 *
	 * @return void
	 */
	public function down() {
		$this->remove_column( $this->get_table_name(), 'object_last_modified' );
		$this->remove_column( $this->get_table_name(), 'object_published_at' );
		$this->remove_index(
			$this->get_table_name(),
			[
				'object_published_at',
				'is_robots_noindex',
				'object_type',
				'object_sub_type',
			],
			[
				'name' => 'published_sitemap_index',
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
