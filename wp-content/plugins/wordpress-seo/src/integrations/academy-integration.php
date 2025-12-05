<?php

namespace Yoast\WP\SEO\Integrations;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\User_Can_Manage_Wpseo_Options_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;

/**
 * Class Academy_Integration.
 */
class Academy_Integration implements Integration_Interface {

	public const PAGE = 'wpseo_page_academy';

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the Product_Helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Holds the Short_Link_Helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $shortlink_helper;

	/**
	 * Constructs Academy_Integration.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager       The WPSEO_Admin_Asset_Manager.
	 * @param Current_Page_Helper       $current_page_helper The Current_Page_Helper.
	 * @param Product_Helper            $product_helper      The Product_Helper.
	 * @param Short_Link_Helper         $shortlink_helper    The Short_Link_Helper.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Current_Page_Helper $current_page_helper,
		Product_Helper $product_helper,
		Short_Link_Helper $shortlink_helper
	) {
		$this->asset_manager       = $asset_manager;
		$this->current_page_helper = $current_page_helper;
		$this->product_helper      = $product_helper;
		$this->shortlink_helper    = $shortlink_helper;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class, User_Can_Manage_Wpseo_Options_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Add page.
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_page' ] );

		// Are we on the settings page?
		if ( $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
		}
	}

	/**
	 * Adds the page.
	 *
	 * @param array $pages The pages.
	 *
	 * @return array The pages.
	 */
	public function add_page( $pages ) {
		\array_splice(
			$pages,
			3,
			0,
			[
				[
					'wpseo_dashboard',
					'',
					\__( 'Academy', 'wordpress-seo' ),
					'wpseo_manage_options',
					self::PAGE,
					[ $this, 'display_page' ],
				],
			]
		);

		return $pages;
	}

	/**
	 * Displays the page.
	 *
	 * @return void
	 */
	public function display_page() {
		echo '<div id="yoast-seo-academy"></div>';
	}

	/**
	 * Enqueues the assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Remove the emoji script as it is incompatible with both React and any contenteditable fields.
		\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		\wp_enqueue_media();
		$this->asset_manager->enqueue_script( 'academy' );
		$this->asset_manager->enqueue_style( 'academy' );
		$this->asset_manager->localize_script( 'academy', 'wpseoScriptData', $this->get_script_data() );
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

	/**
	 * Creates the script data.
	 *
	 * @return array The script data.
	 */
	public function get_script_data() {
		$addon_manager = new WPSEO_Addon_Manager();

		$woocommerce_seo_active = $addon_manager->is_installed( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG );
		$local_seo_active       = $addon_manager->is_installed( WPSEO_Addon_Manager::LOCAL_SLUG );

		return [
			'preferences' => [
				'isPremium'      => $this->product_helper->is_premium(),
				'isWooActive'    => $woocommerce_seo_active,
				'isLocalActive'  => $local_seo_active,
				'isRtl'          => \is_rtl(),
				'pluginUrl'      => \plugins_url( '', \WPSEO_FILE ),
				'upsellSettings' => [
					'actionId'     => 'load-nfd-ctb',
					'premiumCtbId' => 'f6a84663-465f-4cb5-8ba5-f7a6d72224b2',
				],
			],
			'linkParams'  => $this->shortlink_helper->get_query_params(),
		];
	}
}
