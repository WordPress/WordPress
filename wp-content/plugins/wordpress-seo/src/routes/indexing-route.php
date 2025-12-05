<?php

namespace Yoast\WP\SEO\Routes;

use Exception;
use WP_Error;
use WP_REST_Response;
use Yoast\WP\SEO\Actions\Indexing\Indexable_General_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Indexing_Complete_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Type_Archive_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexation_Action_Interface;
use Yoast\WP\SEO\Actions\Indexing\Indexing_Complete_Action;
use Yoast\WP\SEO\Actions\Indexing\Indexing_Prepare_Action;
use Yoast\WP\SEO\Actions\Indexing\Post_Link_Indexing_Action;
use Yoast\WP\SEO\Actions\Indexing\Term_Link_Indexing_Action;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Indexing_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Main;

/**
 * Indexing_Route class.
 *
 * Indexing route for indexables.
 */
class Indexing_Route extends Abstract_Indexation_Route {

	use No_Conditionals;

	/**
	 * The indexing complete route constant.
	 *
	 * @var string
	 */
	public const COMPLETE_ROUTE = 'indexing/complete';

	/**
	 * The full indexing complete route constant.
	 *
	 * @var string
	 */
	public const FULL_COMPLETE_ROUTE = Main::API_V1_NAMESPACE . '/' . self::COMPLETE_ROUTE;

	/**
	 * The indexables complete route constant.
	 *
	 * @var string
	 */
	public const INDEXABLES_COMPLETE_ROUTE = 'indexing/indexables-complete';

	/**
	 * The full indexing complete route constant.
	 *
	 * @var string
	 */
	public const FULL_INDEXABLES_COMPLETE_ROUTE = Main::API_V1_NAMESPACE . '/' . self::INDEXABLES_COMPLETE_ROUTE;

	/**
	 * The indexing prepare route constant.
	 *
	 * @var string
	 */
	public const PREPARE_ROUTE = 'indexing/prepare';

	/**
	 * The full indexing prepare route constant.
	 *
	 * @var string
	 */
	public const FULL_PREPARE_ROUTE = Main::API_V1_NAMESPACE . '/' . self::PREPARE_ROUTE;

	/**
	 * The posts route constant.
	 *
	 * @var string
	 */
	public const POSTS_ROUTE = 'indexing/posts';

	/**
	 * The full posts route constant.
	 *
	 * @var string
	 */
	public const FULL_POSTS_ROUTE = Main::API_V1_NAMESPACE . '/' . self::POSTS_ROUTE;

	/**
	 * The terms route constant.
	 *
	 * @var string
	 */
	public const TERMS_ROUTE = 'indexing/terms';

	/**
	 * The full terms route constant.
	 *
	 * @var string
	 */
	public const FULL_TERMS_ROUTE = Main::API_V1_NAMESPACE . '/' . self::TERMS_ROUTE;

	/**
	 * The terms route constant.
	 *
	 * @var string
	 */
	public const POST_TYPE_ARCHIVES_ROUTE = 'indexing/post-type-archives';

	/**
	 * The full terms route constant.
	 *
	 * @var string
	 */
	public const FULL_POST_TYPE_ARCHIVES_ROUTE = Main::API_V1_NAMESPACE . '/' . self::POST_TYPE_ARCHIVES_ROUTE;

	/**
	 * The general route constant.
	 *
	 * @var string
	 */
	public const GENERAL_ROUTE = 'indexing/general';

	/**
	 * The full general route constant.
	 *
	 * @var string
	 */
	public const FULL_GENERAL_ROUTE = Main::API_V1_NAMESPACE . '/' . self::GENERAL_ROUTE;

	/**
	 * The posts route constant.
	 *
	 * @var string
	 */
	public const POST_LINKS_INDEXING_ROUTE = 'link-indexing/posts';

	/**
	 * The full posts route constant.
	 *
	 * @var string
	 */
	public const FULL_POST_LINKS_INDEXING_ROUTE = Main::API_V1_NAMESPACE . '/' . self::POST_LINKS_INDEXING_ROUTE;

	/**
	 * The terms route constant.
	 *
	 * @var string
	 */
	public const TERM_LINKS_INDEXING_ROUTE = 'link-indexing/terms';

	/**
	 * The full terms route constant.
	 *
	 * @var string
	 */
	public const FULL_TERM_LINKS_INDEXING_ROUTE = Main::API_V1_NAMESPACE . '/' . self::TERM_LINKS_INDEXING_ROUTE;

	/**
	 * The post indexing action.
	 *
	 * @var Indexable_Post_Indexation_Action
	 */
	protected $post_indexation_action;

	/**
	 * The term indexing action.
	 *
	 * @var Indexable_Term_Indexation_Action
	 */
	protected $term_indexation_action;

	/**
	 * The post type archive indexing action.
	 *
	 * @var Indexable_Post_Type_Archive_Indexation_Action
	 */
	protected $post_type_archive_indexation_action;

	/**
	 * Represents the general indexing action.
	 *
	 * @var Indexable_General_Indexation_Action
	 */
	protected $general_indexation_action;

	/**
	 * The prepare indexing action.
	 *
	 * @var Indexing_Prepare_Action
	 */
	protected $prepare_indexing_action;

	/**
	 * The indexable indexing complete action.
	 *
	 * @var Indexable_Indexing_Complete_Action
	 */
	protected $indexable_indexing_complete_action;

	/**
	 * The indexing complete action.
	 *
	 * @var Indexing_Complete_Action
	 */
	protected $indexing_complete_action;

	/**
	 * The post link indexing action.
	 *
	 * @var Post_Link_Indexing_Action
	 */
	protected $post_link_indexing_action;

	/**
	 * The term link indexing action.
	 *
	 * @var Term_Link_Indexing_Action
	 */
	protected $term_link_indexing_action;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * Indexing_Route constructor.
	 *
	 * @param Indexable_Post_Indexation_Action              $post_indexation_action              The post indexing action.
	 * @param Indexable_Term_Indexation_Action              $term_indexation_action              The term indexing action.
	 * @param Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation_action The post type archive indexing action.
	 * @param Indexable_General_Indexation_Action           $general_indexation_action           The general indexing action.
	 * @param Indexable_Indexing_Complete_Action            $indexable_indexing_complete_action  The complete indexing action.
	 * @param Indexing_Complete_Action                      $indexing_complete_action            The complete indexing action.
	 * @param Indexing_Prepare_Action                       $prepare_indexing_action             The prepare indexing action.
	 * @param Post_Link_Indexing_Action                     $post_link_indexing_action           The post link indexing action.
	 * @param Term_Link_Indexing_Action                     $term_link_indexing_action           The term link indexing action.
	 * @param Options_Helper                                $options_helper                      The options helper.
	 * @param Indexing_Helper                               $indexing_helper                     The indexing helper.
	 */
	public function __construct(
		Indexable_Post_Indexation_Action $post_indexation_action,
		Indexable_Term_Indexation_Action $term_indexation_action,
		Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation_action,
		Indexable_General_Indexation_Action $general_indexation_action,
		Indexable_Indexing_Complete_Action $indexable_indexing_complete_action,
		Indexing_Complete_Action $indexing_complete_action,
		Indexing_Prepare_Action $prepare_indexing_action,
		Post_Link_Indexing_Action $post_link_indexing_action,
		Term_Link_Indexing_Action $term_link_indexing_action,
		Options_Helper $options_helper,
		Indexing_Helper $indexing_helper
	) {
		$this->post_indexation_action              = $post_indexation_action;
		$this->term_indexation_action              = $term_indexation_action;
		$this->post_type_archive_indexation_action = $post_type_archive_indexation_action;
		$this->general_indexation_action           = $general_indexation_action;
		$this->indexable_indexing_complete_action  = $indexable_indexing_complete_action;
		$this->indexing_complete_action            = $indexing_complete_action;
		$this->prepare_indexing_action             = $prepare_indexing_action;
		$this->options_helper                      = $options_helper;
		$this->post_link_indexing_action           = $post_link_indexing_action;
		$this->term_link_indexing_action           = $term_link_indexing_action;
		$this->indexing_helper                     = $indexing_helper;
	}

	/**
	 * Registers the routes used to index indexables.
	 *
	 * @return void
	 */
	public function register_routes() {
		$route_args = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'index_posts' ],
			'permission_callback' => [ $this, 'can_index' ],
		];
		\register_rest_route( Main::API_V1_NAMESPACE, self::POSTS_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'index_terms' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::TERMS_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'index_post_type_archives' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::POST_TYPE_ARCHIVES_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'index_general' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::GENERAL_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'prepare' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::PREPARE_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'indexables_complete' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::INDEXABLES_COMPLETE_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'complete' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::COMPLETE_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'index_post_links' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::POST_LINKS_INDEXING_ROUTE, $route_args );

		$route_args['callback'] = [ $this, 'index_term_links' ];
		\register_rest_route( Main::API_V1_NAMESPACE, self::TERM_LINKS_INDEXING_ROUTE, $route_args );
	}

	/**
	 * Indexes a number of unindexed posts.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_posts() {
		return $this->run_indexation_action( $this->post_indexation_action, self::FULL_POSTS_ROUTE );
	}

	/**
	 * Indexes a number of unindexed terms.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_terms() {
		return $this->run_indexation_action( $this->term_indexation_action, self::FULL_TERMS_ROUTE );
	}

	/**
	 * Indexes a number of unindexed post type archive pages.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_post_type_archives() {
		return $this->run_indexation_action( $this->post_type_archive_indexation_action, self::FULL_POST_TYPE_ARCHIVES_ROUTE );
	}

	/**
	 * Indexes a number of unindexed general items.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_general() {
		return $this->run_indexation_action( $this->general_indexation_action, self::FULL_GENERAL_ROUTE );
	}

	/**
	 * Indexes a number of posts for post links.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_post_links() {
		return $this->run_indexation_action( $this->post_link_indexing_action, self::FULL_POST_LINKS_INDEXING_ROUTE );
	}

	/**
	 * Indexes a number of terms for term links.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function index_term_links() {
		return $this->run_indexation_action( $this->term_link_indexing_action, self::FULL_TERM_LINKS_INDEXING_ROUTE );
	}

	/**
	 * Prepares the indexation.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function prepare() {
		$this->prepare_indexing_action->prepare();

		return $this->respond_with( [], false );
	}

	/**
	 * Completes the indexable indexation.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function indexables_complete() {
		$this->indexable_indexing_complete_action->complete();

		return $this->respond_with( [], false );
	}

	/**
	 * Completes the indexation.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function complete() {
		$this->indexing_complete_action->complete();

		return $this->respond_with( [], false );
	}

	/**
	 * Whether or not the current user is allowed to index.
	 *
	 * @return bool Whether or not the current user is allowed to index.
	 */
	public function can_index() {
		return \current_user_can( 'edit_posts' );
	}

	/**
	 * Runs an indexing action and returns the response.
	 *
	 * @param Indexation_Action_Interface $indexation_action The indexing action.
	 * @param string                      $url               The url of the indexing route.
	 *
	 * @return WP_REST_Response|WP_Error The response, or an error when running the indexing action failed.
	 */
	protected function run_indexation_action( Indexation_Action_Interface $indexation_action, $url ) {
		try {
			return parent::run_indexation_action( $indexation_action, $url );
		} catch ( Exception $exception ) {
			$this->indexing_helper->indexing_failed();

			return new WP_Error(
				'wpseo_error_indexing',
				$exception->getMessage(),
				[ 'stackTrace' => $exception->getTraceAsString() ]
			);
		}
	}
}
