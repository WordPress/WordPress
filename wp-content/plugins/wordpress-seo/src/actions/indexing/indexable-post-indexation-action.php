<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use wpdb;
use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Reindexing action for post indexables.
 */
class Indexable_Post_Indexation_Action extends Abstract_Indexing_Action {

	/**
	 * The transient cache key.
	 *
	 * @var string
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_total_unindexed_posts';

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
	 * The post helper.
	 *
	 * @var Post_Helper
	 */
	protected $post_helper;

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
	 * The latest version of Post Indexables.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Indexable_Post_Indexing_Action constructor
	 *
	 * @param Post_Type_Helper           $post_type_helper The post type helper.
	 * @param Indexable_Repository       $repository       The indexable repository.
	 * @param wpdb                       $wpdb             The WordPress database instance.
	 * @param Indexable_Builder_Versions $builder_versions The latest versions for each Indexable type.
	 * @param Post_Helper                $post_helper      The post helper.
	 */
	public function __construct(
		Post_Type_Helper $post_type_helper,
		Indexable_Repository $repository,
		wpdb $wpdb,
		Indexable_Builder_Versions $builder_versions,
		Post_Helper $post_helper
	) {
		$this->post_type_helper = $post_type_helper;
		$this->repository       = $repository;
		$this->wpdb             = $wpdb;
		$this->version          = $builder_versions->get_latest_version_for_type( 'post' );
		$this->post_helper      = $post_helper;
	}

	/**
	 * Creates indexables for unindexed posts.
	 *
	 * @return Indexable[] The created indexables.
	 */
	public function index() {
		$query = $this->get_select_query( $this->get_limit() );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Function get_select_query returns a prepared query.
		$post_ids = $this->wpdb->get_col( $query );

		$indexables = [];
		foreach ( $post_ids as $post_id ) {
			$indexables[] = $this->repository->find_by_id_and_type( (int) $post_id, 'post' );
		}

		if ( \count( $indexables ) > 0 ) {
			\delete_transient( static::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT );
		}

		return $indexables;
	}

	/**
	 * Returns the number of posts that will be indexed in a single indexing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_post_indexation_limit' - Allow filtering the amount of posts indexed during each indexing pass.
		 *
		 * @param int $limit The maximum number of posts indexed.
		 */
		$limit = \apply_filters( 'wpseo_post_indexation_limit', 25 );

		if ( ! \is_int( $limit ) || $limit < 1 ) {
			$limit = 25;
		}

		return $limit;
	}

	/**
	 * Builds a query for counting the number of unindexed posts.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_count_query() {
		$indexable_table = Model::get_table_name( 'Indexable' );

		$post_types             = $this->post_type_helper->get_indexable_post_types();
		$excluded_post_statuses = $this->post_helper->get_excluded_post_statuses();
		$replacements           = \array_merge(
			$post_types,
			$excluded_post_statuses
		);

		$replacements[] = $this->version;

		// Warning: If this query is changed, makes sure to update the query in get_select_query as well.
		// @phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		return $this->wpdb->prepare(
			"
			SELECT COUNT(P.ID)
			FROM {$this->wpdb->posts} AS P
			WHERE P.post_type IN (" . \implode( ', ', \array_fill( 0, \count( $post_types ), '%s' ) ) . ')
			AND P.post_status NOT IN (' . \implode( ', ', \array_fill( 0, \count( $excluded_post_statuses ), '%s' ) ) . ")
			AND P.ID not in (
				SELECT I.object_id from $indexable_table as I
				WHERE I.object_type = 'post'
				AND I.version = %d )",
			$replacements
		);
	}

	/**
	 * Builds a query for selecting the ID's of unindexed posts.
	 *
	 * @param bool $limit The maximum number of post IDs to return.
	 *
	 * @return string The prepared query string.
	 */
	protected function get_select_query( $limit = false ) {
		$indexable_table = Model::get_table_name( 'Indexable' );

		$post_types             = $this->post_type_helper->get_indexable_post_types();
		$excluded_post_statuses = $this->post_helper->get_excluded_post_statuses();
		$replacements           = \array_merge(
			$post_types,
			$excluded_post_statuses
		);
		$replacements[]         = $this->version;

		$limit_query = '';
		if ( $limit ) {
			$limit_query    = 'LIMIT %d';
			$replacements[] = $limit;
		}

		// Warning: If this query is changed, makes sure to update the query in get_count_query as well.
		// @phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
		return $this->wpdb->prepare(
			"
			SELECT P.ID
			FROM {$this->wpdb->posts} AS P
			WHERE P.post_type IN (" . \implode( ', ', \array_fill( 0, \count( $post_types ), '%s' ) ) . ')
			AND P.post_status NOT IN (' . \implode( ', ', \array_fill( 0, \count( $excluded_post_statuses ), '%s' ) ) . ")
			AND P.ID not in (
				SELECT I.object_id from $indexable_table as I
				WHERE I.object_type = 'post'
				AND I.version = %d )
			$limit_query",
			$replacements
		);
	}
}
