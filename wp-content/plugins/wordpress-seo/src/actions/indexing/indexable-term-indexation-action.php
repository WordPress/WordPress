<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use wpdb;
use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Reindexing action for term indexables.
 */
class Indexable_Term_Indexation_Action extends Abstract_Indexing_Action {

	/**
	 * The transient cache key.
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_total_unindexed_terms';

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
	protected $taxonomy;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $repository;

	/**
	 * The WordPress database instance.
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The latest version of the Indexable term builder
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Indexable_Term_Indexation_Action constructor
	 *
	 * @param Taxonomy_Helper            $taxonomy         The taxonomy helper.
	 * @param Indexable_Repository       $repository       The indexable repository.
	 * @param wpdb                       $wpdb             The WordPress database instance.
	 * @param Indexable_Builder_Versions $builder_versions The latest versions of all indexable builders.
	 */
	public function __construct(
		Taxonomy_Helper $taxonomy,
		Indexable_Repository $repository,
		wpdb $wpdb,
		Indexable_Builder_Versions $builder_versions
	) {
		$this->taxonomy   = $taxonomy;
		$this->repository = $repository;
		$this->wpdb       = $wpdb;
		$this->version    = $builder_versions->get_latest_version_for_type( 'term' );
	}

	/**
	 * Creates indexables for unindexed terms.
	 *
	 * @return Indexable[] The created indexables.
	 */
	public function index() {
		$query = $this->get_select_query( $this->get_limit() );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_select_query returns a prepared query.
		$term_ids = ( $query === '' ) ? [] : $this->wpdb->get_col( $query );

		$indexables = [];
		foreach ( $term_ids as $term_id ) {
			$indexables[] = $this->repository->find_by_id_and_type( (int) $term_id, 'term' );
		}

		if ( \count( $indexables ) > 0 ) {
			\delete_transient( static::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT );
		}

		return $indexables;
	}

	/**
	 * Returns the number of terms that will be indexed in a single indexing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_term_indexation_limit' - Allow filtering the number of terms indexed during each indexing pass.
		 *
		 * @param int $limit The maximum number of terms indexed.
		 */
		$limit = \apply_filters( 'wpseo_term_indexation_limit', 25 );

		if ( ! \is_int( $limit ) || $limit < 1 ) {
			$limit = 25;
		}

		return $limit;
	}

	/**
	 * Builds a query for counting the number of unindexed terms.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_count_query() {
		$indexable_table   = Model::get_table_name( 'Indexable' );
		$taxonomy_table    = $this->wpdb->term_taxonomy;
		$public_taxonomies = $this->taxonomy->get_indexable_taxonomies();

		if ( empty( $public_taxonomies ) ) {
			return '';
		}

		$taxonomies_placeholders = \implode( ', ', \array_fill( 0, \count( $public_taxonomies ), '%s' ) );

		$replacements = [ $this->version ];
		\array_push( $replacements, ...$public_taxonomies );

		// Warning: If this query is changed, makes sure to update the query in get_count_query as well.
		return $this->wpdb->prepare(
			"
			SELECT COUNT(term_id)
			FROM {$taxonomy_table} AS T
			LEFT JOIN $indexable_table AS I
				ON T.term_id = I.object_id
				AND I.object_type = 'term'
				AND I.version = %d
			WHERE I.object_id IS NULL
				AND taxonomy IN ($taxonomies_placeholders)",
			$replacements
		);
	}

	/**
	 * Builds a query for selecting the ID's of unindexed terms.
	 *
	 * @param bool $limit The maximum number of term IDs to return.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_select_query( $limit = false ) {
		$indexable_table   = Model::get_table_name( 'Indexable' );
		$taxonomy_table    = $this->wpdb->term_taxonomy;
		$public_taxonomies = $this->taxonomy->get_indexable_taxonomies();

		if ( empty( $public_taxonomies ) ) {
			return '';
		}

		$placeholders = \implode( ', ', \array_fill( 0, \count( $public_taxonomies ), '%s' ) );

		$replacements = [ $this->version ];
		\array_push( $replacements, ...$public_taxonomies );

		$limit_query = '';
		if ( $limit ) {
			$limit_query    = 'LIMIT %d';
			$replacements[] = $limit;
		}

		// Warning: If this query is changed, makes sure to update the query in get_count_query as well.
		return $this->wpdb->prepare(
			"
			SELECT term_id
			FROM {$taxonomy_table} AS T
			LEFT JOIN $indexable_table AS I
				ON T.term_id = I.object_id
				AND I.object_type = 'term'
				AND I.version = %d
			WHERE I.object_id IS NULL
				AND taxonomy IN ($placeholders)
			$limit_query",
			$replacements
		);
	}
}
