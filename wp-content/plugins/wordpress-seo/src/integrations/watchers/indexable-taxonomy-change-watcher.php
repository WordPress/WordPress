<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Actions\Indexing\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Conditionals\Not_Admin_Ajax_Conditional;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Indexing_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;
use Yoast\WP\SEO\Integrations\Cleanup_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification_Center;

/**
 * Taxonomy watcher.
 *
 * Responds to changes in taxonomies public availability.
 */
class Indexable_Taxonomy_Change_Watcher implements Integration_Interface {

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Holds the Taxonomy_Helper instance.
	 *
	 * @var Taxonomy_Helper
	 */
	private $taxonomy_helper;

	/**
	 * The notifications center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Not_Admin_Ajax_Conditional::class, Admin_Conditional::class, Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Taxonomy_Change_Watcher constructor.
	 *
	 * @param Indexing_Helper           $indexing_helper     The indexing helper.
	 * @param Options_Helper            $options             The options helper.
	 * @param Taxonomy_Helper           $taxonomy_helper     The taxonomy helper.
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 * @param Indexable_Helper          $indexable_helper    The indexable helper.
	 */
	public function __construct(
		Indexing_Helper $indexing_helper,
		Options_Helper $options,
		Taxonomy_Helper $taxonomy_helper,
		Yoast_Notification_Center $notification_center,
		Indexable_Helper $indexable_helper
	) {
		$this->indexing_helper     = $indexing_helper;
		$this->options             = $options;
		$this->taxonomy_helper     = $taxonomy_helper;
		$this->notification_center = $notification_center;
		$this->indexable_helper    = $indexable_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'check_taxonomy_public_availability' ] );
	}

	/**
	 * Checks if one or more taxonomies change visibility.
	 *
	 * @return void
	 */
	public function check_taxonomy_public_availability() {

		// We have to make sure this is just a plain http request, no ajax/REST.
		if ( \wp_is_json_request() ) {
			return;
		}

		$public_taxonomies = $this->taxonomy_helper->get_indexable_taxonomies();

		$last_known_public_taxonomies = $this->options->get( 'last_known_public_taxonomies', [] );

		// Initializing the option on the first run.
		if ( empty( $last_known_public_taxonomies ) ) {
			$this->options->set( 'last_known_public_taxonomies', $public_taxonomies );
			return;
		}

		// We look for new public taxonomies.
		$newly_made_public_taxonomies = \array_diff( $public_taxonomies, $last_known_public_taxonomies );

		// We look fortaxonomies that from public have been made private.
		$newly_made_non_public_taxonomies = \array_diff( $last_known_public_taxonomies, $public_taxonomies );

		// Nothing to be done if no changes has been made to taxonomies.
		if ( empty( $newly_made_public_taxonomies ) && ( empty( $newly_made_non_public_taxonomies ) ) ) {
			return;
		}

		// Update the list of last known public taxonomies in the database.
		$this->options->set( 'last_known_public_taxonomies', $public_taxonomies );

		// There are new taxonomies that have been made public.
		if ( ! empty( $newly_made_public_taxonomies ) ) {

			// Force a notification requesting to start the SEO data optimization.
			\delete_transient( Indexable_Term_Indexation_Action::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( Indexable_Term_Indexation_Action::UNINDEXED_LIMITED_COUNT_TRANSIENT );

			$this->indexing_helper->set_reason( Indexing_Reasons::REASON_TAXONOMY_MADE_PUBLIC );
			\do_action( 'new_public_taxonomy_notifications', $newly_made_public_taxonomies );
		}

		// There are taxonomies that have been made private.
		if ( ! empty( $newly_made_non_public_taxonomies ) && $this->indexable_helper->should_index_indexables() ) {
			// Schedule a cron job to remove all the terms whose taxonomy has been made private.
			$cleanup_not_yet_scheduled = ! \wp_next_scheduled( Cleanup_Integration::START_HOOK );
			if ( $cleanup_not_yet_scheduled ) {
				\wp_schedule_single_event( ( \time() + ( \MINUTE_IN_SECONDS * 5 ) ), Cleanup_Integration::START_HOOK );
			}

			\do_action( 'clean_new_public_taxonomy_notifications', $newly_made_non_public_taxonomies );
		}
	}
}
