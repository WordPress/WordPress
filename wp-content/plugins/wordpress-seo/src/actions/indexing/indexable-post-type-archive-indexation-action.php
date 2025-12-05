<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\SEO\Builders\Indexable_Builder;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Values\Indexables\Indexable_Builder_Versions;

/**
 * Reindexing action for post type archive indexables.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
class Indexable_Post_Type_Archive_Indexation_Action implements Indexation_Action_Interface, Limited_Indexing_Action_Interface {

	/**
	 * The transient cache key.
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_total_unindexed_post_type_archives';

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $repository;

	/**
	 * The indexable builder.
	 *
	 * @var Indexable_Builder
	 */
	protected $builder;

	/**
	 * The current version of the post type archive indexable builder.
	 *
	 * @var int
	 */
	protected $version;

	/**
	 * Indexation_Post_Type_Archive_Action constructor.
	 *
	 * @param Indexable_Repository       $repository The indexable repository.
	 * @param Indexable_Builder          $builder    The indexable builder.
	 * @param Post_Type_Helper           $post_type  The post type helper.
	 * @param Indexable_Builder_Versions $versions   The current versions of all indexable builders.
	 */
	public function __construct(
		Indexable_Repository $repository,
		Indexable_Builder $builder,
		Post_Type_Helper $post_type,
		Indexable_Builder_Versions $versions
	) {
		$this->repository = $repository;
		$this->builder    = $builder;
		$this->post_type  = $post_type;
		$this->version    = $versions->get_latest_version_for_type( 'post-type-archive' );
	}

	/**
	 * Returns the total number of unindexed post type archives.
	 *
	 * @param int|false $limit Limit the number of counted objects.
	 *                         False for "no limit".
	 *
	 * @return int The total number of unindexed post type archives.
	 */
	public function get_total_unindexed( $limit = false ) {
		$transient = \get_transient( static::UNINDEXED_COUNT_TRANSIENT );
		if ( $transient !== false ) {
			return (int) $transient;
		}

		\set_transient( static::UNINDEXED_COUNT_TRANSIENT, 0, \DAY_IN_SECONDS );

		$result = \count( $this->get_unindexed_post_type_archives( $limit ) );

		\set_transient( static::UNINDEXED_COUNT_TRANSIENT, $result, \DAY_IN_SECONDS );

		/**
		 * Action: 'wpseo_indexables_unindexed_calculated' - sets an option to timestamp when there are no unindexed indexables left.
		 *
		 * @internal
		 */
		\do_action( 'wpseo_indexables_unindexed_calculated', static::UNINDEXED_COUNT_TRANSIENT, $result );

		return $result;
	}

	/**
	 * Creates indexables for post type archives.
	 *
	 * @return Indexable[] The created indexables.
	 */
	public function index() {
		$unindexed_post_type_archives = $this->get_unindexed_post_type_archives( $this->get_limit() );

		$indexables = [];
		foreach ( $unindexed_post_type_archives as $post_type_archive ) {
			$indexables[] = $this->builder->build_for_post_type_archive( $post_type_archive );
		}

		if ( \count( $indexables ) > 0 ) {
			\delete_transient( static::UNINDEXED_COUNT_TRANSIENT );
		}

		return $indexables;
	}

	/**
	 * Returns the number of post type archives that will be indexed in a single indexing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_post_type_archive_indexation_limit' - Allow filtering the number of posts indexed during each indexing pass.
		 *
		 * @param int $limit The maximum number of posts indexed.
		 */
		$limit = \apply_filters( 'wpseo_post_type_archive_indexation_limit', 25 );

		if ( ! \is_int( $limit ) || $limit < 1 ) {
			$limit = 25;
		}

		return $limit;
	}

	/**
	 * Retrieves the list of post types for which no indexable for its archive page has been made yet.
	 *
	 * @param int|false $limit Limit the number of retrieved indexables to this number.
	 *
	 * @return array The list of post types for which no indexable for its archive page has been made yet.
	 */
	protected function get_unindexed_post_type_archives( $limit = false ) {
		$post_types_with_archive_pages = $this->get_post_types_with_archive_pages();
		$indexed_post_types            = $this->get_indexed_post_type_archives();

		$unindexed_post_types = \array_diff( $post_types_with_archive_pages, $indexed_post_types );

		if ( $limit ) {
			return \array_slice( $unindexed_post_types, 0, $limit );
		}

		return $unindexed_post_types;
	}

	/**
	 * Returns the names of all the post types that have archive pages.
	 *
	 * @return array The list of names of all post types that have archive pages.
	 */
	protected function get_post_types_with_archive_pages() {
		// We only want to index archive pages of public post types that have them.
		$post_types_with_archive = $this->post_type->get_indexable_post_archives();

		// We only need the post type names, not the objects.
		$post_types = [];
		foreach ( $post_types_with_archive as $post_type_with_archive ) {
			$post_types[] = $post_type_with_archive->name;
		}

		return $post_types;
	}

	/**
	 * Retrieves the list of post type names for which an archive indexable exists.
	 *
	 * @return array The list of names of post types with unindexed archive pages.
	 */
	protected function get_indexed_post_type_archives() {
		$results = $this->repository->query()
			->select( 'object_sub_type' )
			->where( 'object_type', 'post-type-archive' )
			->where_equal( 'version', $this->version )
			->find_array();

		if ( $results === false ) {
			return [];
		}

		$callback = static function ( $result ) {
			return $result['object_sub_type'];
		};

		return \array_map( $callback, $results );
	}

	/**
	 * Returns a limited number of unindexed posts.
	 *
	 * @param int $limit Limit the maximum number of unindexed posts that are counted.
	 *
	 * @return int|false The limited number of unindexed posts. False if the query fails.
	 */
	public function get_limited_unindexed_count( $limit ) {
		return $this->get_total_unindexed( $limit );
	}
}
