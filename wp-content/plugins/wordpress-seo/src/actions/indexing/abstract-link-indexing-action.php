<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use wpdb;
use Yoast\WP\SEO\Builders\Indexable_Link_Builder;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Models\SEO_Links;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Reindexing action for link indexables.
 */
abstract class Abstract_Link_Indexing_Action extends Abstract_Indexing_Action {

	/**
	 * The link builder.
	 *
	 * @var Indexable_Link_Builder
	 */
	protected $link_builder;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

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
	 * Indexable_Post_Indexing_Action constructor
	 *
	 * @param Indexable_Link_Builder $link_builder     The indexable link builder.
	 * @param Indexable_Helper       $indexable_helper The indexable repository.
	 * @param Indexable_Repository   $repository       The indexable repository.
	 * @param wpdb                   $wpdb             The WordPress database instance.
	 */
	public function __construct(
		Indexable_Link_Builder $link_builder,
		Indexable_Helper $indexable_helper,
		Indexable_Repository $repository,
		wpdb $wpdb
	) {
		$this->link_builder     = $link_builder;
		$this->indexable_helper = $indexable_helper;
		$this->repository       = $repository;
		$this->wpdb             = $wpdb;
	}

	/**
	 * Builds links for indexables which haven't had their links indexed yet.
	 *
	 * @return SEO_Links[] The created SEO links.
	 */
	public function index() {
		$objects = $this->get_objects();

		$indexables = [];
		foreach ( $objects as $object ) {
			$indexable = $this->repository->find_by_id_and_type( $object->id, $object->type );
			if ( $indexable ) {
				$this->link_builder->build( $indexable, $object->content );
				$this->indexable_helper->save_indexable( $indexable );

				$indexables[] = $indexable;
			}
		}

		if ( \count( $indexables ) > 0 ) {
			\delete_transient( static::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( static::UNINDEXED_LIMITED_COUNT_TRANSIENT );
		}

		return $indexables;
	}

	/**
	 * In the case of term-links and post-links we want to use the total unindexed count, because using
	 * the limited unindexed count actually leads to worse performance.
	 *
	 * @param int|bool $limit Unused.
	 *
	 * @return int The total number of unindexed links.
	 */
	public function get_limited_unindexed_count( $limit = false ) {
		return $this->get_total_unindexed();
	}

	/**
	 * Returns the number of texts that will be indexed in a single link indexing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		/**
		 * Filter 'wpseo_link_indexing_limit' - Allow filtering the number of texts indexed during each link indexing pass.
		 *
		 * @param int $limit The maximum number of texts indexed.
		 */
		return \apply_filters( 'wpseo_link_indexing_limit', 5 );
	}

	/**
	 * Returns objects to be indexed.
	 *
	 * @return array Objects to be indexed, should be an array of objects with object_id, object_type and content.
	 */
	abstract protected function get_objects();
}
