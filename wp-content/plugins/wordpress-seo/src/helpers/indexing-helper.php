<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast\WP\SEO\Actions\Indexing\Indexable_General_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Type_Archive_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexation_Action_Interface;
use Yoast\WP\SEO\Actions\Indexing\Limited_Indexing_Action_Interface;
use Yoast\WP\SEO\Actions\Indexing\Post_Link_Indexing_Action;
use Yoast\WP\SEO\Actions\Indexing\Term_Link_Indexing_Action;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Integrations\Admin\Indexing_Notification_Integration;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast_Notification_Center;

/**
 * A helper object for indexing.
 */
class Indexing_Helper {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The date helper.
	 *
	 * @var Date_Helper
	 */
	protected $date_helper;

	/**
	 * The notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * The indexation actions.
	 *
	 * @var Indexation_Action_Interface[]|Limited_Indexing_Action_Interface[]
	 */
	protected $indexing_actions;

	/**
	 * The indexation actions that can be done in the background.
	 *
	 * @var Indexation_Action_Interface[]|Limited_Indexing_Action_Interface[]
	 */
	protected $background_indexing_actions;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Indexing_Helper constructor.
	 *
	 * @param Options_Helper            $options_helper      The options helper.
	 * @param Date_Helper               $date_helper         The date helper.
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Date_Helper $date_helper,
		Yoast_Notification_Center $notification_center
	) {
		$this->options_helper      = $options_helper;
		$this->date_helper         = $date_helper;
		$this->notification_center = $notification_center;
	}

	/**
	 * Sets the actions.
	 *
	 * @required
	 *
	 * @param Indexable_Post_Indexation_Action              $post_indexation              The post indexing action.
	 * @param Indexable_Term_Indexation_Action              $term_indexation              The term indexing action.
	 * @param Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation The posttype indexing action.
	 * @param Indexable_General_Indexation_Action           $general_indexation           The general indexing (homepage etc) action.
	 * @param Post_Link_Indexing_Action                     $post_link_indexing_action    The post crosslink indexing action.
	 * @param Term_Link_Indexing_Action                     $term_link_indexing_action    The term crossling indexing action.
	 *
	 * @return void
	 */
	public function set_indexing_actions(
		Indexable_Post_Indexation_Action $post_indexation,
		Indexable_Term_Indexation_Action $term_indexation,
		Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation,
		Indexable_General_Indexation_Action $general_indexation,
		Post_Link_Indexing_Action $post_link_indexing_action,
		Term_Link_Indexing_Action $term_link_indexing_action
	) {
		$this->indexing_actions = [
			$post_indexation,
			$term_indexation,
			$post_type_archive_indexation,
			$general_indexation,
			$post_link_indexing_action,
			$term_link_indexing_action,
		];

		// Coincidentally, the background indexing actions are the same with the Free indexing actions for now.
		$this->background_indexing_actions = $this->indexing_actions;
	}

	/**
	 * Sets the indexable repository for the indexing helper class.
	 *
	 * @required
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 *
	 * @return void
	 */
	public function set_indexable_repository( Indexable_Repository $indexable_repository ) {
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Prepares the indexing process by setting several database options and removing the indexing notification.
	 *
	 * @return void
	 */
	public function prepare() {
		$this->set_first_time( false );
		$this->set_started( $this->date_helper->current_time() );
		$this->remove_indexing_notification();
		// Do not set_reason here; if the process is cancelled, the reason to start indexing is still valid.
	}

	/**
	 * Sets several database options when the indexing process is finished.
	 *
	 * @return void
	 */
	public function complete() {
		$this->set_reason( '' );
		$this->set_started( null );
	}

	/**
	 * Sets appropriate flags when the indexing process fails.
	 *
	 * @return void
	 */
	public function indexing_failed() {
		$this->set_reason( Indexing_Reasons::REASON_INDEXING_FAILED );
		$this->set_started( null );
	}

	/**
	 * Sets the indexing reason.
	 *
	 * @param string $reason The indexing reason.
	 *
	 * @return void
	 */
	public function set_reason( $reason ) {
		$this->options_helper->set( 'indexing_reason', $reason );
		$this->remove_indexing_notification();
	}

	/**
	 * Removes any pre-existing notification, so that a new notification (with a possible new reason) can be added.
	 *
	 * @return void
	 */
	protected function remove_indexing_notification() {
		$this->notification_center->remove_notification_by_id(
			Indexing_Notification_Integration::NOTIFICATION_ID
		);
	}

	/**
	 * Determines whether an indexing reason has been set in the options.
	 *
	 * @return bool Whether an indexing reason has been set in the options.
	 */
	public function has_reason() {
		$reason = $this->get_reason();

		return ! empty( $reason );
	}

	/**
	 * Returns the indexing reason. The reason why the site-wide indexing process should be run.
	 *
	 * @return string The indexing reason, defaults to the empty string if no reason has been set.
	 */
	public function get_reason() {
		return $this->options_helper->get( 'indexing_reason', '' );
	}

	/**
	 * Sets the start time when the indexing process has started but not completed.
	 *
	 * @param int|bool $timestamp The start time when the indexing process has started but not completed, false otherwise.
	 *
	 * @return void
	 */
	public function set_started( $timestamp ) {
		$this->options_helper->set( 'indexing_started', $timestamp );
	}

	/**
	 * Gets the start time when the indexing process has started but not completed.
	 *
	 * @return int|bool The start time when the indexing process has started but not completed, false otherwise.
	 */
	public function get_started() {
		return $this->options_helper->get( 'indexing_started' );
	}

	/**
	 * Sets a boolean that indicates whether or not a site still has to be indexed for the first time.
	 *
	 * @param bool $is_first_time_indexing Whether or not a site still has to be indexed for the first time.
	 *
	 * @return void
	 */
	public function set_first_time( $is_first_time_indexing ) {
		$this->options_helper->set( 'indexing_first_time', $is_first_time_indexing );
	}

	/**
	 * Gets a boolean that indicates whether or not the site still has to be indexed for the first time.
	 *
	 * @return bool Whether the site still has to be indexed for the first time.
	 */
	public function is_initial_indexing() {
		return $this->options_helper->get( 'indexing_first_time', true );
	}

	/**
	 * Gets a boolean that indicates whether or not the indexing of the indexables has completed.
	 *
	 * @return bool Whether the indexing of the indexables has completed.
	 */
	public function is_finished_indexables_indexing() {
		return $this->options_helper->get( 'indexables_indexing_completed', false );
	}

	/**
	 * Returns the total number of unindexed objects.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_unindexed_count() {
		$unindexed_count = 0;

		foreach ( $this->indexing_actions as $indexing_action ) {
			$unindexed_count += $indexing_action->get_total_unindexed();
		}

		return $unindexed_count;
	}

	/**
	 * Returns the amount of un-indexed posts expressed in percentage, which will be needed to set a threshold.
	 *
	 * @param int $unindexed_count The number of unindexed objects.
	 *
	 * @return int The amount of unindexed posts expressed in percentage.
	 */
	public function get_unindexed_percentage( $unindexed_count ) {
		// Gets the amount of indexed objects in the site.
		$indexed_count = $this->indexable_repository->get_total_number_of_indexables();
		// The total amount of objects in the site.
		$total_objects_count = ( $indexed_count + $unindexed_count );

		return ( ( $unindexed_count / $total_objects_count ) * 100 );
	}

	/**
	 * Returns whether the SEO optimization button should show.
	 *
	 * @return bool Whether the SEO optimization button should show.
	 */
	public function should_show_optimization_button() {
		// Gets the amount of unindexed objects in the site.
		$unindexed_count = $this->get_filtered_unindexed_count();

		// If the amount of unidexed posts is <10 don't show configuration button.
		if ( $unindexed_count <= 10 ) {
			return false;
		}
		// If the amount of unidexed posts is >10, but the total amount of unidexed posts is ≤4% of the total amount of objects in the site, don't show configuration button.
		if ( $this->get_unindexed_percentage( $unindexed_count ) <= 4 ) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the total number of unindexed objects and applies a filter for third party integrations.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_filtered_unindexed_count() {
		$unindexed_count = $this->get_unindexed_count();

		/**
		 * Filter: 'wpseo_indexing_get_unindexed_count' - Allow changing the amount of unindexed objects.
		 *
		 * @param int $unindexed_count The amount of unindexed objects.
		 */
		return \apply_filters( 'wpseo_indexing_get_unindexed_count', $unindexed_count );
	}

	/**
	 * Returns a limited number of unindexed objects.
	 *
	 * @param int                                                               $limit   Limit the number of unindexed objects that are counted.
	 * @param Indexation_Action_Interface[]|Limited_Indexing_Action_Interface[] $actions The actions whose counts will be calculated.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_limited_unindexed_count( $limit, $actions = [] ) {
		$unindexed_count = 0;

		if ( empty( $actions ) ) {
			$actions = $this->indexing_actions;
		}

		foreach ( $actions as $action ) {
			$unindexed_count += $action->get_limited_unindexed_count( $limit - $unindexed_count + 1 );
			if ( $unindexed_count > $limit ) {
				return $unindexed_count;
			}
		}

		return $unindexed_count;
	}

	/**
	 * Returns the total number of unindexed objects and applies a filter for third party integrations.
	 *
	 * @param int $limit Limit the number of unindexed objects that are counted.
	 *
	 * @return int The total number of unindexed objects.
	 */
	public function get_limited_filtered_unindexed_count( $limit ) {
		$unindexed_count = $this->get_limited_unindexed_count( $limit, $this->indexing_actions );

		if ( $unindexed_count > $limit ) {
			return $unindexed_count;
		}

		/**
		 * Filter: 'wpseo_indexing_get_limited_unindexed_count' - Allow changing the amount of unindexed objects,
		 * and allow for a maximum number of items counted to improve performance.
		 *
		 * @param int       $unindexed_count The amount of unindexed objects.
		 * @param int|false $limit           Limit the number of unindexed objects that need to be counted.
		 *                                   False if it doesn't need to be limited.
		 */
		return \apply_filters( 'wpseo_indexing_get_limited_unindexed_count', $unindexed_count, $limit );
	}

	/**
	 * Returns the total number of unindexed objects that can be indexed in the background and applies a filter for third party integrations.
	 *
	 * @param int $limit Limit the number of unindexed objects that are counted.
	 *
	 * @return int The total number of unindexed objects that can be indexed in the background.
	 */
	public function get_limited_filtered_unindexed_count_background( $limit ) {
		$unindexed_count = $this->get_limited_unindexed_count( $limit, $this->background_indexing_actions );

		if ( $unindexed_count > $limit ) {
			return $unindexed_count;
		}

		/**
		 * Filter: 'wpseo_indexing_get_limited_unindexed_count_background' - Allow changing the amount of unindexed objects that can be indexed in the background,
		 * and allow for a maximum number of items counted to improve performance.
		 *
		 * @param int       $unindexed_count The amount of unindexed objects.
		 * @param int|false $limit           Limit the number of unindexed objects that need to be counted.
		 *                                   False if it doesn't need to be limited.
		 */
		return \apply_filters( 'wpseo_indexing_get_limited_unindexed_count_background', $unindexed_count, $limit );
	}
}
