<?php

namespace Yoast\WP\SEO\AI_Generator\User_Interface;

use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\AI_HTTP_Request\Infrastructure\API_Client;
use Yoast\WP\SEO\Conditionals\AI_Conditional;
use Yoast\WP\SEO\Conditionals\AI_Editor_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Application\Ai_Fix_Assessments_Upsell;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;

/**
 * Ai_Generator_Integration class.
 */
class Ai_Generator_Integration implements Integration_Interface {

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Represents the add-on manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * Holds the API client instance.
	 *
	 * @var API_Client
	 */
	private $api_client;

	/**
	 * Represents the current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Represents the options manager.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * Represents the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Represents the introductions seen repository.
	 *
	 * @var Introductions_Seen_Repository
	 */
	private $introductions_seen_repository;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ AI_Conditional::class, AI_Editor_Conditional::class ];
	}

	/**
	 * Constructs the class.
	 *
	 * @param WPSEO_Admin_Asset_Manager     $asset_manager                 The admin asset manager.
	 * @param WPSEO_Addon_Manager           $addon_manager                 The addon manager.
	 * @param API_Client                    $api_client                    The API client.
	 * @param Current_Page_Helper           $current_page_helper           The current page helper.
	 * @param Options_Helper                $options_helper                The options helper.
	 * @param User_Helper                   $user_helper                   The user helper.
	 * @param Introductions_Seen_Repository $introductions_seen_repository The introductions seen repository.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		API_Client $api_client,
		Current_Page_Helper $current_page_helper,
		Options_Helper $options_helper,
		User_Helper $user_helper,
		Introductions_Seen_Repository $introductions_seen_repository
	) {
		$this->asset_manager                 = $asset_manager;
		$this->addon_manager                 = $addon_manager;
		$this->api_client                    = $api_client;
		$this->current_page_helper           = $current_page_helper;
		$this->options_helper                = $options_helper;
		$this->user_helper                   = $user_helper;
		$this->introductions_seen_repository = $introductions_seen_repository;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		// Enqueue after Elementor_Premium integration, which re-registers the assets.
		\add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
	}

	/**
	 * Gets the subscription status for Yoast SEO Premium and Yoast WooCommerce SEO.
	 *
	 * @return array<string, bool>
	 */
	public function get_product_subscriptions() {
		return [
			'premiumSubscription'     => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ),
			'wooCommerceSubscription' => $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ),
		];
	}

	/**
	 * Returns the data that should be passed to the script.
	 *
	 * @return array<string|array<string>>
	 */
	public function get_script_data() {
		$user_id = $this->user_helper->get_current_user_id();

		return [
			'hasConsent'           => $this->user_helper->get_meta( $user_id, '_yoast_wpseo_ai_consent', true ),
			'productSubscriptions' => $this->get_product_subscriptions(),
			'hasSeenIntroduction'  => $this->introductions_seen_repository->is_introduction_seen( $user_id, AI_Fix_Assessments_Upsell::ID ),
			'requestTimeout'       => $this->api_client->get_request_timeout(),
			'isFreeSparks'         => $this->options_helper->get( 'ai_free_sparks_started_on', null ) !== null,
		];
	}

	/**
	 * Enqueues the required assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		$this->asset_manager->enqueue_script( 'ai-generator' );
		$this->asset_manager->localize_script( 'ai-generator', 'wpseoAiGenerator', $this->get_script_data() );
		$this->asset_manager->enqueue_style( 'ai-generator' );
	}
}
