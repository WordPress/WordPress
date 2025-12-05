<?php

namespace Yoast\WP\SEO\Config\Migrations;

use Yoast\WP\Lib\Migrations\Migration;
use Yoast\WP\Lib\Model;

/**
 * Indexable migration.
 */
class WpYoastIndexable extends Migration {

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
		$this->add_table();
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
	 * Creates the indexable table.
	 *
	 * @return void
	 */
	private function add_table() {
		$table_name = $this->get_table_name();

		$indexable_table = $this->create_table( $table_name );

		// Permalink.
		$indexable_table->column( 'permalink', 'mediumtext', [ 'null' => true ] );
		$indexable_table->column(
			'permalink_hash',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);

		// Object information.
		$indexable_table->column(
			'object_id',
			'integer',
			[
				'unsigned' => true,
				'null'     => true,
				'limit'    => 11,
			]
		);
		$indexable_table->column(
			'object_type',
			'string',
			[
				'null'  => false,
				'limit' => 32,
			]
		);
		$indexable_table->column(
			'object_sub_type',
			'string',
			[
				'null'  => true,
				'limit' => 32,
			]
		);

		// Ownership.
		$indexable_table->column(
			'author_id',
			'integer',
			[
				'unsigned' => true,
				'null'     => true,
				'limit'    => 11,
			]
		);
		$indexable_table->column(
			'post_parent',
			'integer',
			[
				'unsigned' => true,
				'null'     => true,
				'limit'    => 11,
			]
		);

		// Title and description.
		$indexable_table->column(
			'title',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column( 'description', 'text', [ 'null' => true ] );
		$indexable_table->column(
			'breadcrumb_title',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);

		// Post metadata: status, public, protected.
		$indexable_table->column(
			'post_status',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column(
			'is_public',
			'boolean',
			[
				'null'    => true,
				'default' => null,
			]
		);
		$indexable_table->column( 'is_protected', 'boolean', [ 'default' => false ] );
		$indexable_table->column(
			'has_public_posts',
			'boolean',
			[
				'null'    => true,
				'default' => null,
			]
		);

		$indexable_table->column(
			'number_of_pages',
			'integer',
			[
				'unsigned' => true,
				'null'     => true,
				'default'  => null,
				'limit'    => 11,
			]
		);

		$indexable_table->column( 'canonical', 'mediumtext', [ 'null' => true ] );

		// SEO and readability analysis.
		$indexable_table->column(
			'primary_focus_keyword',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column(
			'primary_focus_keyword_score',
			'integer',
			[
				'null'  => true,
				'limit' => 3,
			]
		);
		$indexable_table->column(
			'readability_score',
			'integer',
			[
				'null'  => true,
				'limit' => 3,
			]
		);
		$indexable_table->column( 'is_cornerstone', 'boolean', [ 'default' => false ] );

		// Robots.
		$indexable_table->column(
			'is_robots_noindex',
			'boolean',
			[
				'null'    => true,
				'default' => false,
			]
		);
		$indexable_table->column(
			'is_robots_nofollow',
			'boolean',
			[
				'null'    => true,
				'default' => false,
			]
		);
		$indexable_table->column(
			'is_robots_noarchive',
			'boolean',
			[
				'null'    => true,
				'default' => false,
			]
		);
		$indexable_table->column(
			'is_robots_noimageindex',
			'boolean',
			[
				'null'    => true,
				'default' => false,
			]
		);
		$indexable_table->column(
			'is_robots_nosnippet',
			'boolean',
			[
				'null'    => true,
				'default' => false,
			]
		);

		// Twitter.
		$indexable_table->column(
			'twitter_title',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column( 'twitter_image', 'mediumtext', [ 'null' => true ] );
		$indexable_table->column( 'twitter_description', 'mediumtext', [ 'null' => true ] );
		$indexable_table->column(
			'twitter_image_id',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column(
			'twitter_image_source',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);

		// Open-Graph.
		$indexable_table->column(
			'open_graph_title',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column( 'open_graph_description', 'mediumtext', [ 'null' => true ] );
		$indexable_table->column( 'open_graph_image', 'mediumtext', [ 'null' => true ] );
		$indexable_table->column(
			'open_graph_image_id',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column(
			'open_graph_image_source',
			'string',
			[
				'null'  => true,
				'limit' => 191,
			]
		);
		$indexable_table->column( 'open_graph_image_meta', 'text', [ 'null' => true ] );

		// Link count.
		$indexable_table->column(
			'link_count',
			'integer',
			[
				'null'  => true,
				'limit' => 11,
			]
		);
		$indexable_table->column(
			'incoming_link_count',
			'integer',
			[
				'null'  => true,
				'limit' => 11,
			]
		);

		// Prominent words.
		$indexable_table->column(
			'prominent_words_version',
			'integer',
			[
				'null'     => true,
				'limit'    => 11,
				'unsigned' => true,
				'default'  => null,
			]
		);

		$indexable_table->finish();

		$this->add_indexes( $table_name );

		$this->add_timestamps( $table_name );
	}

	/**
	 * Adds indexes to the indexable table.
	 *
	 * @param string $indexable_table_name The name of the indexable table.
	 *
	 * @return void
	 */
	private function add_indexes( $indexable_table_name ) {
		$this->add_index(
			$indexable_table_name,
			[
				'object_type',
				'object_sub_type',
			],
			[
				'name' => 'object_type_and_sub_type',
			]
		);

		$this->add_index(
			$indexable_table_name,
			'permalink_hash',
			[
				'name' => 'permalink_hash',
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
