<?php

namespace Yoast\WP\SEO\Plans\User_Interface;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\General\User_Interface\General_Page_Integration;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Plans\Application\Add_Ons_Collector;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Adds the plans page to the Yoast admin menu.
 */
class Plans_Page_Integration implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The page name.
	 */
	public const PAGE = 'wpseo_licenses';

	/**
	 * The assets name.
	 */
	public const ASSETS_NAME = 'plans';

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Holds the Add_Ons_Collector.
	 *
	 * @var Add_Ons_Collector
	 */
	private $add_ons_collector;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the Short_Link_Helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * Holds the Admin_Conditional.
	 *
	 * @var Admin_Conditional
	 */
	private $admin_conditional;

	/**
	 * The promotion manager.
	 *
	 * @var Promotion_Manager
	 */
	private $promotion_manager;

	/**
	 * Constructs the instance.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager       The WPSEO_Admin_Asset_Manager.
	 * @param Add_Ons_Collector         $add_ons_collector   The Add_Ons_Collector.
	 * @param Current_Page_Helper       $current_page_helper The Current_Page_Helper.
	 * @param Short_Link_Helper         $short_link_helper   The Short_Link_Helper.
	 * @param Admin_Conditional         $admin_conditional   The Admin_Conditional.
	 * @param Promotion_Manager         $promotion_manager   The promotion manager.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Add_Ons_Collector $add_ons_collector,
		Current_Page_Helper $current_page_helper,
		Short_Link_Helper $short_link_helper,
		Admin_Conditional $admin_conditional,
		Promotion_Manager $promotion_manager
	) {
		$this->asset_manager       = $asset_manager;
		$this->add_ons_collector   = $add_ons_collector;
		$this->current_page_helper = $current_page_helper;
		$this->short_link_helper   = $short_link_helper;
		$this->admin_conditional   = $admin_conditional;
		$this->promotion_manager   = $promotion_manager;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Add page with priority 7 to add it above the workouts.
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_page' ], 7 );
		\add_filter( 'wpseo_network_submenu_pages', [ $this, 'add_page' ], 7 );

		// Are we on our page?
		if ( $this->admin_conditional->is_met() && $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
		}
	}

	/**
	 * Adds the page to the (currently) last position in the array.
	 *
	 * @param array<string, array<string, array<static|string>>> $pages The pages.
	 *
	 * @return array<string, array<string, array<static|string>>> The pages.
	 */
	public function add_page( $pages ) {
		$pages[] = [
			General_Page_Integration::PAGE,
			'',
			\__( 'Plans', 'wordpress-seo' ),
			'wpseo_manage_options',
			self::PAGE,
			[ $this, 'display_page' ],
		];

		return $pages;
	}

	/**
	 * Displays the page.
	 *
	 * @return void
	 */
	public function display_page() {
		echo '<div id="yoast-seo-plans"></div>';
	}

	/**
	 * Enqueues the assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Remove the emoji script as it is incompatible with both React and any contenteditable fields.
		\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		$this->asset_manager->enqueue_script( self::ASSETS_NAME );
		$this->asset_manager->enqueue_style( self::ASSETS_NAME );
		$this->asset_manager->localize_script( self::ASSETS_NAME, 'wpseoScriptData', $this->get_script_data() );
	}

	/**
	 * Creates the script data.
	 *
	 * @return array<string,array<string, string|bool|array<string, string>>> The script data.
	 */
	private function get_script_data(): array {
		return [
			'addOns'            => $this->add_ons_collector->to_array(),
			'linkParams'        => $this->short_link_helper->get_query_params(),
			'preferences'       => [
				'isRtl' => \is_rtl(),
			],
			'currentPromotions' => $this->promotion_manager->get_current_promotions(),
		];
	}

	/**
	 * Removes all current WP notices.
	 *
	 * @return void
	 */
	public function remove_notices() {
		\remove_all_actions( 'admin_notices' );
		\remove_all_actions( 'user_admin_notices' );
		\remove_all_actions( 'network_admin_notices' );
		\remove_all_actions( 'all_admin_notices' );
	}
}
