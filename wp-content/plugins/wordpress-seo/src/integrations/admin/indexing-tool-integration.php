<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Conditionals\No_Tool_Selected_Conditional;
use Yoast\WP\SEO\Conditionals\Yoast_Tools_Page_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Indexing_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Indexing_Error_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Indexing_List_Item_Presenter;
use Yoast\WP\SEO\Routes\Importing_Route;
use Yoast\WP\SEO\Routes\Indexing_Route;
use Yoast\WP\SEO\Services\Importing\Importable_Detector_Service;

/**
 * Class Indexing_Tool_Integration. Bridge to the Javascript indexing tool on Yoast SEO Tools page.
 *
 * @package Yoast\WP\SEO\Integrations\Admin
 */
class Indexing_Tool_Integration implements Integration_Interface {

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Represents the indexables helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * Represents the indexing helper.
	 *
	 * @var Indexing_Helper
	 */
	protected $indexing_helper;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * The Importable Detector service.
	 *
	 * @var Importable_Detector_Service
	 */
	protected $importable_detector;

	/**
	 * The Importing Route class.
	 *
	 * @var Importing_Route
	 */
	protected $importing_route;

	/**
	 * Returns the conditionals based on which this integration should be active.
	 *
	 * @return array The array of conditionals.
	 */
	public static function get_conditionals() {
		return [
			Migrations_Conditional::class,
			No_Tool_Selected_Conditional::class,
			Yoast_Tools_Page_Conditional::class,
		];
	}

	/**
	 * Indexing_Integration constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager   $asset_manager       The admin asset manager.
	 * @param Indexable_Helper            $indexable_helper    The indexable helper.
	 * @param Short_Link_Helper           $short_link_helper   The short link helper.
	 * @param Indexing_Helper             $indexing_helper     The indexing helper.
	 * @param WPSEO_Addon_Manager         $addon_manager       The addon manager.
	 * @param Product_Helper              $product_helper      The product helper.
	 * @param Importable_Detector_Service $importable_detector The importable detector.
	 * @param Importing_Route             $importing_route     The importing route.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Indexable_Helper $indexable_helper,
		Short_Link_Helper $short_link_helper,
		Indexing_Helper $indexing_helper,
		WPSEO_Addon_Manager $addon_manager,
		Product_Helper $product_helper,
		Importable_Detector_Service $importable_detector,
		Importing_Route $importing_route
	) {
		$this->asset_manager       = $asset_manager;
		$this->indexable_helper    = $indexable_helper;
		$this->short_link_helper   = $short_link_helper;
		$this->indexing_helper     = $indexing_helper;
		$this->addon_manager       = $addon_manager;
		$this->product_helper      = $product_helper;
		$this->importable_detector = $importable_detector;
		$this->importing_route     = $importing_route;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_tools_overview_list_items_internal', [ $this, 'render_indexing_list_item' ], 10 );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
	}

	/**
	 * Enqueues the required scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->asset_manager->enqueue_script( 'indexation' );
		$this->asset_manager->enqueue_style( 'admin-css' );
		$this->asset_manager->enqueue_style( 'monorepo' );

		$data = [
			'disabled'                    => ! $this->indexable_helper->should_index_indexables(),
			'amount'                      => $this->indexing_helper->get_filtered_unindexed_count(),
			'firstTime'                   => ( $this->indexing_helper->is_initial_indexing() === true ),
			'errorMessage'                => $this->render_indexing_error(),
			'restApi'                     => [
				'root'                => \esc_url_raw( \rest_url() ),
				'indexing_endpoints'  => $this->get_indexing_endpoints(),
				'importing_endpoints' => $this->get_importing_endpoints(),
				'nonce'               => \wp_create_nonce( 'wp_rest' ),
			],
		];

		/**
		 * Filter: 'wpseo_indexing_data' Filter to adapt the data used in the indexing process.
		 *
		 * @param array $data The indexing data to adapt.
		 */
		$data = \apply_filters( 'wpseo_indexing_data', $data );

		$this->asset_manager->localize_script( 'indexation', 'yoastIndexingData', $data );
	}

	/**
	 * The error to show if optimization failed.
	 *
	 * @return string The error to show if optimization failed.
	 */
	protected function render_indexing_error() {
		$presenter = new Indexing_Error_Presenter(
			$this->short_link_helper,
			$this->product_helper,
			$this->addon_manager
		);

		return $presenter->present();
	}

	/**
	 * Determines if the site has a valid Premium subscription.
	 *
	 * @return bool If the site has a valid Premium subscription.
	 */
	protected function has_valid_premium_subscription() {
		return $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG );
	}

	/**
	 * Renders the indexing list item.
	 *
	 * @return void
	 */
	public function render_indexing_list_item() {
		if ( \current_user_can( 'manage_options' ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The output is correctly escaped in the presenter.
			echo new Indexing_List_Item_Presenter( $this->short_link_helper );
		}
	}

	/**
	 * Retrieves a list of the indexing endpoints to use.
	 *
	 * @return array The endpoints.
	 */
	protected function get_indexing_endpoints() {
		$endpoints = [
			'prepare'            => Indexing_Route::FULL_PREPARE_ROUTE,
			'terms'              => Indexing_Route::FULL_TERMS_ROUTE,
			'posts'              => Indexing_Route::FULL_POSTS_ROUTE,
			'archives'           => Indexing_Route::FULL_POST_TYPE_ARCHIVES_ROUTE,
			'general'            => Indexing_Route::FULL_GENERAL_ROUTE,
			'indexablesComplete' => Indexing_Route::FULL_INDEXABLES_COMPLETE_ROUTE,
			'post_link'          => Indexing_Route::FULL_POST_LINKS_INDEXING_ROUTE,
			'term_link'          => Indexing_Route::FULL_TERM_LINKS_INDEXING_ROUTE,
		];

		$endpoints = \apply_filters( 'wpseo_indexing_endpoints', $endpoints );

		$endpoints['complete'] = Indexing_Route::FULL_COMPLETE_ROUTE;

		return $endpoints;
	}

	/**
	 * Retrieves a list of the importing endpoints to use.
	 *
	 * @return array The endpoints.
	 */
	protected function get_importing_endpoints() {
		$available_actions   = $this->importable_detector->detect_importers();
		$importing_endpoints = [];

		foreach ( $available_actions as $plugin => $types ) {
			foreach ( $types as $type ) {
				$importing_endpoints[ $plugin ][] = $this->importing_route->get_endpoint( $plugin, $type );
			}
		}

		return $importing_endpoints;
	}
}
