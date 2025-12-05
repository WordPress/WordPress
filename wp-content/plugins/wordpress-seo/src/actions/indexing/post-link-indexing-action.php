<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;

/**
 * Reindexing action for post link indexables.
 */
class Post_Link_Indexing_Action extends Abstract_Link_Indexing_Action {

	/**
	 * The transient name.
	 *
	 * @var string
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_unindexed_post_link_count';

	/**
	 * The transient cache key for limited counts.
	 *
	 * @var string
	 */
	public const UNINDEXED_LIMITED_COUNT_TRANSIENT = self::UNINDEXED_COUNT_TRANSIENT . '_limited';

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * Sets the required helper.
	 *
	 * @required
	 *
	 * @param Post_Type_Helper $post_type_helper The post type helper.
	 *
	 * @return void
	 */
	public function set_helper( Post_Type_Helper $post_type_helper ) {
		$this->post_type_helper = $post_type_helper;
	}

	/**
	 * Returns objects to be indexed.
	 *
	 * @return array Objects to be indexed.
	 */
	protected function get_objects() {
		$query = $this->get_select_query( $this->get_limit() );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_select_query returns a prepared query.
		$posts = $this->wpdb->get_results( $query );

		return \array_map(
			static function ( $post ) {
				return (object) [
					'id'      => (int) $post->ID,
					'type'    => 'post',
					'content' => $post->post_content,
				];
			},
			$posts
		);
	}

	/**
	 * Builds a query for counting the number of unindexed post links.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_count_query() {
		$public_post_types = $this->post_type_helper->get_indexable_post_types();
		$indexable_table   = Model::get_table_name( 'Indexable' );
		$links_table       = Model::get_table_name( 'SEO_Links' );

		// Warning: If this query is changed, makes sure to update the query in get_select_query as well.
		return $this->wpdb->prepare(
			"SELECT COUNT(P.ID)
			FROM {$this->wpdb->posts} AS P
			LEFT JOIN $indexable_table AS I
				ON P.ID = I.object_id
				AND I.link_count IS NOT NULL
				AND I.object_type = 'post'
			LEFT JOIN $links_table AS L
				ON L.post_id = P.ID
				AND L.target_indexable_id IS NULL
				AND L.type = 'internal'
				AND L.target_post_id IS NOT NULL
				AND L.target_post_id != 0
			WHERE ( I.object_id IS NULL OR L.post_id IS NOT NULL )
				AND P.post_status = 'publish'
				AND P.post_type IN (" . \implode( ', ', \array_fill( 0, \count( $public_post_types ), '%s' ) ) . ')',
			$public_post_types
		);
	}

	/**
	 * Builds a query for selecting the ID's of unindexed post links.
	 *
	 * @param int|false $limit The maximum number of post link IDs to return.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_select_query( $limit = false ) {
		$public_post_types = $this->post_type_helper->get_indexable_post_types();
		$indexable_table   = Model::get_table_name( 'Indexable' );
		$links_table       = Model::get_table_name( 'SEO_Links' );
		$replacements      = $public_post_types;

		$limit_query = '';
		if ( $limit ) {
			$limit_query    = 'LIMIT %d';
			$replacements[] = $limit;
		}

		// Warning: If this query is changed, makes sure to update the query in get_count_query as well.
		return $this->wpdb->prepare(
			"
			SELECT P.ID, P.post_content
			FROM {$this->wpdb->posts} AS P
			LEFT JOIN $indexable_table AS I
				ON P.ID = I.object_id
				AND I.link_count IS NOT NULL
				AND I.object_type = 'post'
			LEFT JOIN $links_table AS L
				ON L.post_id = P.ID
				AND L.target_indexable_id IS NULL
				AND L.type = 'internal'
				AND L.target_post_id IS NOT NULL
				AND L.target_post_id != 0
			WHERE ( I.object_id IS NULL OR L.post_id IS NOT NULL )
				AND P.post_status = 'publish'
				AND P.post_type IN (" . \implode( ', ', \array_fill( 0, \count( $public_post_types ), '%s' ) ) . ")
			$limit_query",
			$replacements
		);
	}
}
