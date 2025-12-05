<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;

/**
 * Reindexing action for term link indexables.
 */
class Term_Link_Indexing_Action extends Abstract_Link_Indexing_Action {

	/**
	 * The transient name.
	 *
	 * @var string
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_unindexed_term_link_count';

	/**
	 * The transient cache key for limited counts.
	 *
	 * @var string
	 */
	public const UNINDEXED_LIMITED_COUNT_TRANSIENT = self::UNINDEXED_COUNT_TRANSIENT . '_limited';

	/**
	 * The post type helper.
	 *
	 * @var Taxonomy_Helper
	 */
	protected $taxonomy_helper;

	/**
	 * Sets the required helper.
	 *
	 * @required
	 *
	 * @param Taxonomy_Helper $taxonomy_helper The taxonomy helper.
	 *
	 * @return void
	 */
	public function set_helper( Taxonomy_Helper $taxonomy_helper ) {
		$this->taxonomy_helper = $taxonomy_helper;
	}

	/**
	 * Returns objects to be indexed.
	 *
	 * @return array Objects to be indexed.
	 */
	protected function get_objects() {
		$query = $this->get_select_query( $this->get_limit() );

		if ( $query === '' ) {
			return [];
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_select_query returns a prepared query.
		$terms = $this->wpdb->get_results( $query );

		return \array_map(
			static function ( $term ) {
				return (object) [
					'id'      => (int) $term->term_id,
					'type'    => 'term',
					'content' => $term->description,
				];
			},
			$terms
		);
	}

	/**
	 * Builds a query for counting the number of unindexed term links.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_count_query() {
		$public_taxonomies = $this->taxonomy_helper->get_indexable_taxonomies();

		if ( empty( $public_taxonomies ) ) {
			return '';
		}

		$placeholders    = \implode( ', ', \array_fill( 0, \count( $public_taxonomies ), '%s' ) );
		$indexable_table = Model::get_table_name( 'Indexable' );

		// Warning: If this query is changed, makes sure to update the query in get_select_query as well.
		return $this->wpdb->prepare(
			"
			SELECT COUNT(T.term_id)
			FROM {$this->wpdb->term_taxonomy} AS T
			LEFT JOIN $indexable_table AS I
				ON T.term_id = I.object_id
				AND I.object_type = 'term'
				AND I.link_count IS NOT NULL
			WHERE I.object_id IS NULL
				AND T.taxonomy IN ($placeholders)",
			$public_taxonomies
		);
	}

	/**
	 * Builds a query for selecting the ID's of unindexed term links.
	 *
	 * @param int|false $limit The maximum number of term link IDs to return.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_select_query( $limit = false ) {
		$public_taxonomies = $this->taxonomy_helper->get_indexable_taxonomies();

		if ( empty( $public_taxonomies ) ) {
			return '';
		}

		$indexable_table = Model::get_table_name( 'Indexable' );
		$replacements    = $public_taxonomies;

		$limit_query = '';
		if ( $limit ) {
			$limit_query    = 'LIMIT %d';
			$replacements[] = $limit;
		}

		// Warning: If this query is changed, makes sure to update the query in get_count_query as well.
		return $this->wpdb->prepare(
			"
			SELECT T.term_id, T.description
			FROM {$this->wpdb->term_taxonomy} AS T
			LEFT JOIN $indexable_table AS I
				ON T.term_id = I.object_id
				AND I.object_type = 'term'
				AND I.link_count IS NOT NULL
			WHERE I.object_id IS NULL
				AND T.taxonomy IN (" . \implode( ', ', \array_fill( 0, \count( $public_taxonomies ), '%s' ) ) . ")
			$limit_query",
			$replacements
		);
	}
}
