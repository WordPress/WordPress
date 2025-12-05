<?php

namespace Yoast\WP\SEO\Commands;

use WP_CLI;
use WP_CLI\Utils;
use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Actions\Indexing\Indexable_General_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Indexing_Complete_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Type_Archive_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexation_Action_Interface;
use Yoast\WP\SEO\Actions\Indexing\Indexing_Prepare_Action;
use Yoast\WP\SEO\Actions\Indexing\Post_Link_Indexing_Action;
use Yoast\WP\SEO\Actions\Indexing\Term_Link_Indexing_Action;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Main;

/**
 * Command to generate indexables for all posts and terms.
 */
class Index_Command implements Command_Interface {

	/**
	 * The post indexation action.
	 *
	 * @var Indexable_Post_Indexation_Action
	 */
	private $post_indexation_action;

	/**
	 * The term indexation action.
	 *
	 * @var Indexable_Term_Indexation_Action
	 */
	private $term_indexation_action;

	/**
	 * The post type archive indexation action.
	 *
	 * @var Indexable_Post_Type_Archive_Indexation_Action
	 */
	private $post_type_archive_indexation_action;

	/**
	 * The general indexation action.
	 *
	 * @var Indexable_General_Indexation_Action
	 */
	private $general_indexation_action;

	/**
	 * The term link indexing action.
	 *
	 * @var Term_Link_Indexing_Action
	 */
	private $term_link_indexing_action;

	/**
	 * The post link indexing action.
	 *
	 * @var Post_Link_Indexing_Action
	 */
	private $post_link_indexing_action;

	/**
	 * The complete indexation action.
	 *
	 * @var Indexable_Indexing_Complete_Action
	 */
	private $complete_indexation_action;

	/**
	 * The indexing prepare action.
	 *
	 * @var Indexing_Prepare_Action
	 */
	private $prepare_indexing_action;

	/**
	 * Represents the indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Generate_Indexables_Command constructor.
	 *
	 * @param Indexable_Post_Indexation_Action              $post_indexation_action              The post indexation
	 *                                                                                           action.
	 * @param Indexable_Term_Indexation_Action              $term_indexation_action              The term indexation
	 *                                                                                           action.
	 * @param Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation_action The post type archive
	 *                                                                                           indexation action.
	 * @param Indexable_General_Indexation_Action           $general_indexation_action           The general indexation
	 *                                                                                           action.
	 * @param Indexable_Indexing_Complete_Action            $complete_indexation_action          The complete indexation
	 *                                                                                           action.
	 * @param Indexing_Prepare_Action                       $prepare_indexing_action             The prepare indexing
	 *                                                                                           action.
	 * @param Post_Link_Indexing_Action                     $post_link_indexing_action           The post link indexation
	 *                                                                                           action.
	 * @param Term_Link_Indexing_Action                     $term_link_indexing_action           The term link indexation
	 *                                                                                           action.
	 * @param Indexable_Helper                              $indexable_helper                    The indexable helper.
	 */
	public function __construct(
		Indexable_Post_Indexation_Action $post_indexation_action,
		Indexable_Term_Indexation_Action $term_indexation_action,
		Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation_action,
		Indexable_General_Indexation_Action $general_indexation_action,
		Indexable_Indexing_Complete_Action $complete_indexation_action,
		Indexing_Prepare_Action $prepare_indexing_action,
		Post_Link_Indexing_Action $post_link_indexing_action,
		Term_Link_Indexing_Action $term_link_indexing_action,
		Indexable_Helper $indexable_helper
	) {
		$this->post_indexation_action              = $post_indexation_action;
		$this->term_indexation_action              = $term_indexation_action;
		$this->post_type_archive_indexation_action = $post_type_archive_indexation_action;
		$this->general_indexation_action           = $general_indexation_action;
		$this->complete_indexation_action          = $complete_indexation_action;
		$this->prepare_indexing_action             = $prepare_indexing_action;
		$this->post_link_indexing_action           = $post_link_indexing_action;
		$this->term_link_indexing_action           = $term_link_indexing_action;
		$this->indexable_helper                    = $indexable_helper;
	}

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 */
	public static function get_namespace() {
		return Main::WP_CLI_NAMESPACE;
	}

	/**
	 * Indexes all your content to ensure the best performance.
	 *
	 * ## OPTIONS
	 *
	 * [--network]
	 * : Performs the indexation on all sites within the network.
	 *
	 * [--reindex]
	 * : Removes all existing indexables and then reindexes them.
	 *
	 * [--skip-confirmation]
	 * : Skips the confirmations (for automated systems).
	 *
	 * [--interval=<interval>]
	 * : The number of microseconds (millionths of a second) to wait between index actions.
	 * ---
	 * default: 500000
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp yoast index
	 *
	 * @when after_wp_load
	 *
	 * @param array|null $args       The arguments.
	 * @param array|null $assoc_args The associative arguments.
	 *
	 * @return void
	 */
	public function index( $args = null, $assoc_args = null ) {
		if ( ! $this->indexable_helper->should_index_indexables() ) {
			WP_CLI::log(
				\__( 'Your WordPress environment is running on a non-production site. Indexables can only be created on production environments. Please check your `WP_ENVIRONMENT_TYPE` settings.', 'wordpress-seo' )
			);

			return;
		}

		if ( ! isset( $assoc_args['network'] ) ) {
			$this->run_indexation_actions( $assoc_args );

			return;
		}

		$criteria = [
			'fields'   => 'ids',
			'spam'     => 0,
			'deleted'  => 0,
			'archived' => 0,
		];
		$blog_ids = \get_sites( $criteria );

		foreach ( $blog_ids as $blog_id ) {
			\switch_to_blog( $blog_id );
			\do_action( '_yoast_run_migrations' );
			$this->run_indexation_actions( $assoc_args );
			\restore_current_blog();
		}
	}

	/**
	 * Runs all indexation actions.
	 *
	 * @param array $assoc_args The associative arguments.
	 *
	 * @return void
	 */
	protected function run_indexation_actions( $assoc_args ) {
		// See if we need to clear all indexables before repopulating.
		if ( isset( $assoc_args['reindex'] ) ) {

			// Argument --skip-confirmation to prevent confirmation (for automated systems).
			if ( ! isset( $assoc_args['skip-confirmation'] ) ) {
				WP_CLI::confirm( 'This will clear all previously indexed objects. Are you certain you wish to proceed?' );
			}

			// Truncate the tables.
			$this->clear();

			// Delete the transients to make sure re-indexing runs every time.
			\delete_transient( Indexable_Post_Indexation_Action::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( Indexable_Post_Type_Archive_Indexation_Action::UNINDEXED_COUNT_TRANSIENT );
			\delete_transient( Indexable_Term_Indexation_Action::UNINDEXED_COUNT_TRANSIENT );
		}

		$indexation_actions = [
			'posts'              => $this->post_indexation_action,
			'terms'              => $this->term_indexation_action,
			'post type archives' => $this->post_type_archive_indexation_action,
			'general objects'    => $this->general_indexation_action,
			'post links'         => $this->post_link_indexing_action,
			'term links'         => $this->term_link_indexing_action,
		];

		$this->prepare_indexing_action->prepare();

		$interval = (int) $assoc_args['interval'];
		foreach ( $indexation_actions as $name => $indexation_action ) {
			$this->run_indexation_action( $name, $indexation_action, $interval );
		}

		$this->complete_indexation_action->complete();
	}

	/**
	 * Runs an indexation action.
	 *
	 * @param string                      $name              The name of the object to be indexed.
	 * @param Indexation_Action_Interface $indexation_action The indexation action.
	 * @param int                         $interval          Number of microseconds (millionths of a second) to wait between index actions.
	 *
	 * @return void
	 */
	protected function run_indexation_action( $name, Indexation_Action_Interface $indexation_action, $interval ) {
		$total = $indexation_action->get_total_unindexed();
		if ( $total > 0 ) {
			$limit    = $indexation_action->get_limit();
			$progress = Utils\make_progress_bar( 'Indexing ' . $name, $total );
			do {
				$indexables = $indexation_action->index();
				$count      = \count( $indexables );
				$progress->tick( $count );
				\usleep( $interval );
				Utils\wp_clear_object_cache();
			} while ( $count >= $limit );
			$progress->finish();
		}
	}

	/**
	 * Clears the database related to the indexables.
	 *
	 * @return void
	 */
	protected function clear() {
		global $wpdb;

		// For the PreparedSQLPlaceholders issue, see: https://github.com/WordPress/WordPress-Coding-Standards/issues/1903.
		// For the DirectDBQuery issue, see: https://github.com/WordPress/WordPress-Coding-Standards/issues/1947.
		// phpcs:disable WordPress.DB -- Table names should not be quoted and truncate queries can not be cached.
		$wpdb->query(
			$wpdb->prepare(
				'TRUNCATE TABLE %1$s',
				Model::get_table_name( 'Indexable' )
			)
		);
		$wpdb->query(
			$wpdb->prepare(
				'TRUNCATE TABLE %1$s',
				Model::get_table_name( 'Indexable_Hierarchy' )
			)
		);
		// phpcs:enable
	}
}
