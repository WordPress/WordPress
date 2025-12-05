<?php

namespace Yoast\WP\SEO\Analytics\Application;

use WPSEO_Collection;
use Yoast\WP\SEO\Actions\Indexing\Indexation_Action_Interface;
use Yoast\WP\SEO\Analytics\Domain\Missing_Indexable_Bucket;
use Yoast\WP\SEO\Analytics\Domain\Missing_Indexable_Count;

/**
 * Manages the collection of the missing indexable data.
 *
 * @makePublic
 */
class Missing_Indexables_Collector implements WPSEO_Collection {

	/**
	 * All the indexation actions.
	 *
	 * @var array<Indexation_Action_Interface>
	 */
	private $indexation_actions;

	/**
	 * The collector constructor.
	 *
	 * @param Indexation_Action_Interface ...$indexation_actions All the Indexation actions.
	 */
	public function __construct( Indexation_Action_Interface ...$indexation_actions ) {
		$this->indexation_actions = $indexation_actions;
		$this->add_additional_indexing_actions();
	}

	/**
	 * Gets the data for the tracking collector.
	 *
	 * @return array The list of missing indexables.
	 */
	public function get() {
		$missing_indexable_bucket = new Missing_Indexable_Bucket();
		foreach ( $this->indexation_actions as $indexation_action ) {
			$missing_indexable_count = new Missing_Indexable_Count( \get_class( $indexation_action ), $indexation_action->get_total_unindexed() );
			$missing_indexable_bucket->add_missing_indexable_count( $missing_indexable_count );
		}

		return [ 'missing_indexables' => $missing_indexable_bucket->to_array() ];
	}

	/**
	 * Adds additional indexing actions to count from the 'wpseo_indexable_collector_add_indexation_actions' filter.
	 *
	 * @return void
	 */
	private function add_additional_indexing_actions() {
		/**
		 * Filter: Adds the possibility to add additional indexation actions to be included in the count routine.
		 *
		 * @internal
		 * @param Indexation_Action_Interface $actions This filter expects a list of Indexation_Action_Interface instances
		 *                                             and expects only Indexation_Action_Interface implementations to be
		 *                                             added to the list.
		 */
		$indexing_actions = (array) \apply_filters( 'wpseo_indexable_collector_add_indexation_actions', $this->indexation_actions );

		$this->indexation_actions = \array_filter(
			$indexing_actions,
			static function ( $indexing_action ) {
				return \is_a( $indexing_action, Indexation_Action_Interface::class );
			}
		);
	}
}
