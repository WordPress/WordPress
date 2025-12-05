<?php

namespace Yoast\WP\SEO\Integrations;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\User_Can_Manage_Wpseo_Options_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Class Support_Integration.
 */
class Support_Integration implements Integration_Interface {

	public const PAGE = 'wpseo_page_support';

	public const CAPABILITY = 'wpseo_manage_options';

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
	 * Holds the WooCommerce_Conditional.
	 *
	 * @var WooCommerce_Conditional
	 */
	private $woocommerce_conditional;

	/**
	 * Holds the WPSEO_Addon_Manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Constructs Support_Integration.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager           The WPSEO_Admin_Asset_Manager.
	 * @param Current_Page_Helper       $current_page_helper     The Current_Page_Helper.
	 * @param Product_Helper            $product_helper          The Product_Helper.
	 * @param Short_Link_Helper         $shortlink_helper        The Short_Link_Helper.
	 * @param WooCommerce_Conditional   $woocommerce_conditional The WooCommerce_Conditional.
	 * @param WPSEO_Addon_Manager       $addon_manager           The WPSEO_Addon_Manager.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Current_Page_Helper $current_page_helper,
		Product_Helper $product_helper,
		Short_Link_Helper $shortlink_helper,
		WooCommerce_Conditional $woocommerce_conditional,
		WPSEO_Addon_Manager $addon_manager
	) {
		$this->asset_manager           = $asset_manager;
		$this->current_page_helper     = $current_page_helper;
		$this->product_helper          = $product_helper;
		$this->shortlink_helper        = $shortlink_helper;
		$this->woocommerce_conditional = $woocommerce_conditional;
		$this->addon_manager           = $addon_manager;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string>
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
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_page' ], \PHP_INT_MAX );

		// Are we on the settings page?
		if ( $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
		}
	}

	/**
	 * Adds the page.
	 *
	 * @param array<array<string|callable>> $pages The pages.
	 *
	 * @return array<array<string|callable>> The pages.
	 */
	public function add_page( array $pages ) {
		$pages[] = [
			'wpseo_dashboard',
			'',
			\__( 'Support', 'wordpress-seo' ),
			self::CAPABILITY,
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
		echo '<div id="yoast-seo-support"></div>';
	}

	/**
	 * Enqueues the assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Remove the emoji script as it is incompatible with both React and any contenteditable fields.
		\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		$this->asset_manager->enqueue_script( 'support' );
		$this->asset_manager->enqueue_style( 'support' );
		if ( \YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
			$this->asset_manager->enqueue_style( 'black-friday-banner' );
		}
		$this->asset_manager->localize_script( 'support', 'wpseoScriptData', $this->get_script_data() );
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
	 * @return array<string, array<array<string, bool|string|array<string>>, bool, string>> The script data.
	 */
	public function get_script_data() {
		return [
			'preferences'       => [
				'hasPremiumSubscription' => $this->addon_manager->has_active_addons() && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ),
				'hasWooSeoSubscription'  => $this->addon_manager->has_active_addons() && $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ),
				'isRtl'                  => \is_rtl(),
				'pluginUrl'              => \plugins_url( '', \WPSEO_FILE ),
				'upsellSettings'         => [
					'actionId'     => 'load-nfd-ctb',
					'premiumCtbId' => 'f6a84663-465f-4cb5-8ba5-f7a6d72224b2',
				],
				'isWooCommerceActive'    => $this->woocommerce_conditional->is_met(),
			],
			'linkParams'        => $this->shortlink_helper->get_query_params(),
			'currentPromotions' => \YoastSEO()->classes->get( Promotion_Manager::class )->get_current_promotions(),
		];
	}
}
