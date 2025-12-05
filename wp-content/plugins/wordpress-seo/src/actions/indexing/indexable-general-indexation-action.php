<?php

namespace Yoast\WP\SEO\Actions\Indexing;

use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * General reindexing action for indexables.
 */
class Indexable_General_Indexation_Action implements Indexation_Action_Interface, Limited_Indexing_Action_Interface {

	/**
	 * The transient cache key.
	 */
	public const UNINDEXED_COUNT_TRANSIENT = 'wpseo_total_unindexed_general_items';

	/**
	 * Represents the indexables repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Indexable_General_Indexation_Action constructor.
	 *
	 * @param Indexable_Repository $indexable_repository The indexables repository.
	 */
	public function __construct( Indexable_Repository $indexable_repository ) {
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Returns the total number of unindexed objects.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_total_unindexed() {
		$transient = \get_transient( static::UNINDEXED_COUNT_TRANSIENT );
		if ( $transient !== false ) {
			return (int) $transient;
		}

		$indexables_to_create = $this->query();

		$result = \count( $indexables_to_create );

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
	 * Returns a limited number of unindexed posts.
	 *
	 * @param int $limit Limit the maximum number of unindexed posts that are counted.
	 *
	 * @return int|false The limited number of unindexed posts. False if the query fails.
	 */
	public function get_limited_unindexed_count( $limit ) {
		return $this->get_total_unindexed();
	}

	/**
	 * Creates indexables for unindexed system pages, the date archive, and the homepage.
	 *
	 * @return Indexable[] The created indexables.
	 */
	public function index() {
		$indexables           = [];
		$indexables_to_create = $this->query();

		if ( isset( $indexables_to_create['404'] ) ) {
			$indexables[] = $this->indexable_repository->find_for_system_page( '404' );
		}

		if ( isset( $indexables_to_create['search'] ) ) {
			$indexables[] = $this->indexable_repository->find_for_system_page( 'search-result' );
		}

		if ( isset( $indexables_to_create['date_archive'] ) ) {
			$indexables[] = $this->indexable_repository->find_for_date_archive();
		}
		if ( isset( $indexables_to_create['home_page'] ) ) {
			$indexables[] = $this->indexable_repository->find_for_home_page();
		}

		\set_transient( static::UNINDEXED_COUNT_TRANSIENT, 0, \DAY_IN_SECONDS );

		return $indexables;
	}

	/**
	 * Returns the number of objects that will be indexed in a single indexing pass.
	 *
	 * @return int The limit.
	 */
	public function get_limit() {
		// This matches the maximum number of indexables created by this action.
		return 4;
	}

	/**
	 * Check which indexables already exist and return the values of the ones to create.
	 *
	 * @return array The indexable types to create.
	 */
	private function query() {
		$indexables_to_create = [];
		if ( ! $this->indexable_repository->find_for_system_page( '404', false ) ) {
			$indexables_to_create['404'] = true;
		}

		if ( ! $this->indexable_repository->find_for_system_page( 'search-result', false ) ) {
			$indexables_to_create['search'] = true;
		}

		if ( ! $this->indexable_repository->find_for_date_archive( false ) ) {
			$indexables_to_create['date_archive'] = true;
		}

		$need_home_page_indexable = ( (int) \get_option( 'page_on_front' ) === 0 && \get_option( 'show_on_front' ) === 'posts' );

		if ( $need_home_page_indexable && ! $this->indexable_repository->find_for_home_page( false ) ) {
			$indexables_to_create['home_page'] = true;
		}

		return $indexables_to_create;
	}
}
