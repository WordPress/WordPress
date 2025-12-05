<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WP_User;
use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use WPSEO_Option_Tab;
use WPSEO_Shortlinker;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\General\User_Interface\General_Page_Integration;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Social_Profiles_Helper;
use Yoast\WP\SEO\Helpers\Woocommerce_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Routes\Indexing_Route;

/**
 * First_Time_Configuration_Integration class
 */
class First_Time_Configuration_Integration implements Integration_Interface {

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * The shortlinker.
	 *
	 * @var WPSEO_Shortlinker
	 */
	private $shortlinker;

	/**
	 * The options' helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The social profiles helper.
	 *
	 * @var Social_Profiles_Helper
	 */
	private $social_profiles_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * The meta tags context helper.
	 *
	 * @var Meta_Tags_Context
	 */
	private $meta_tags_context;

	/**
	 * The WooCommerce helper.
	 *
	 * @var Woocommerce_Helper
	 */
	private $woocommerce_helper;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * First_Time_Configuration_Integration constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager $admin_asset_manager    The admin asset manager.
	 * @param WPSEO_Addon_Manager       $addon_manager          The addon manager.
	 * @param WPSEO_Shortlinker         $shortlinker            The shortlinker.
	 * @param Options_Helper            $options_helper         The options helper.
	 * @param Social_Profiles_Helper    $social_profiles_helper The social profile helper.
	 * @param Product_Helper            $product_helper         The product helper.
	 * @param Meta_Tags_Context         $meta_tags_context      The meta tags context helper.
	 * @param Woocommerce_Helper        $woocommerce_helper     The WooCommerce helper.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $admin_asset_manager,
		WPSEO_Addon_Manager $addon_manager,
		WPSEO_Shortlinker $shortlinker,
		Options_Helper $options_helper,
		Social_Profiles_Helper $social_profiles_helper,
		Product_Helper $product_helper,
		Meta_Tags_Context $meta_tags_context,
		Woocommerce_Helper $woocommerce_helper
	) {
		$this->admin_asset_manager    = $admin_asset_manager;
		$this->addon_manager          = $addon_manager;
		$this->shortlinker            = $shortlinker;
		$this->options_helper         = $options_helper;
		$this->social_profiles_helper = $social_profiles_helper;
		$this->product_helper         = $product_helper;
		$this->meta_tags_context      = $meta_tags_context;
		$this->woocommerce_helper     = $woocommerce_helper;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		\add_action( 'wpseo_settings_tabs_dashboard', [ $this, 'add_first_time_configuration_tab' ] );
	}

	/**
	 * Adds a dedicated tab in the General sub-page.
	 *
	 * @param WPSEO_Options_Tabs $dashboard_tabs Object representing the tabs of the General sub-page.
	 *
	 * @return void
	 */
	public function add_first_time_configuration_tab( $dashboard_tabs ) {
		$dashboard_tabs->add_tab(
			new WPSEO_Option_Tab(
				'first-time-configuration',
				\__( 'First-time configuration', 'wordpress-seo' ),
				[ 'save_button' => false ]
			)
		);
	}

	/**
	 * Adds the data for the first-time configuration to the wpseoFirstTimeConfigurationData object.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date is not processed or saved.
		if ( ! isset( $_GET['page'] ) || ( $_GET['page'] !== 'wpseo_dashboard' && $_GET['page'] !== General_Page_Integration::PAGE ) || \is_network_admin() ) {
			return;
		}

		$this->admin_asset_manager->enqueue_script( 'indexation' );
		$this->admin_asset_manager->enqueue_style( 'first-time-configuration' );
		$this->admin_asset_manager->enqueue_style( 'admin-css' );
		$this->admin_asset_manager->enqueue_style( 'monorepo' );

		$data = [
			'disabled'     => ! \YoastSEO()->helpers->indexable->should_index_indexables(),
			'amount'       => \YoastSEO()->helpers->indexing->get_filtered_unindexed_count(),
			'firstTime'    => ( \YoastSEO()->helpers->indexing->is_initial_indexing() === true ),
			'errorMessage' => '',
			'restApi'      => [
				'root'               => \esc_url_raw( \rest_url() ),
				'indexing_endpoints' => $this->get_endpoints(),
				'nonce'              => \wp_create_nonce( 'wp_rest' ),
			],
		];

		/**
		 * Filter: 'wpseo_indexing_data' Filter to adapt the data used in the indexing process.
		 *
		 * @param array $data The indexing data to adapt.
		 */
		$data = \apply_filters( 'wpseo_indexing_data', $data );

		$this->admin_asset_manager->localize_script( 'indexation', 'yoastIndexingData', $data );

		$person_id       = $this->get_person_id();
		$social_profiles = $this->get_social_profiles();

		// This filter is documented in admin/views/tabs/metas/paper-content/general/knowledge-graph.php.
		$knowledge_graph_message = \apply_filters( 'wpseo_knowledge_graph_setting_msg', '' );

		$finished_steps        = $this->get_finished_steps();
		$options               = $this->get_company_or_person_options();
		$selected_option_label = '';
		$filtered_options      = \array_filter(
			$options,
			function ( $item ) {
				return $item['value'] === $this->is_company_or_person();
			}
		);
		$selected_option       = \reset( $filtered_options );
		if ( \is_array( $selected_option ) ) {
			$selected_option_label = $selected_option['label'];
		}

		$data_ftc = [
			'canEditUser'                => $this->can_edit_profile( $person_id ),
			'companyOrPerson'            => $this->is_company_or_person(),
			'companyOrPersonLabel'       => $selected_option_label,
			'companyName'                => $this->get_company_name(),
			'fallbackCompanyName'        => $this->get_fallback_company_name( $this->get_company_name() ),
			'websiteName'                => $this->get_website_name(),
			'fallbackWebsiteName'        => $this->get_fallback_website_name( $this->get_website_name() ),
			'companyLogo'                => $this->get_company_logo(),
			'companyLogoFallback'        => $this->get_company_fallback_logo( $this->get_company_logo() ),
			'companyLogoId'              => $this->get_person_logo_id(),
			'finishedSteps'              => $finished_steps,
			'personId'                   => (int) $person_id,
			'personName'                 => $this->get_person_name(),
			'personLogo'                 => $this->get_person_logo(),
			'personLogoFallback'         => $this->get_person_fallback_logo( $this->get_person_logo() ),
			'personLogoId'               => $this->get_person_logo_id(),
			'siteTagline'                => $this->get_site_tagline(),
			'socialProfiles'             => [
				'facebookUrl'     => $social_profiles['facebook_site'],
				'twitterUsername' => $social_profiles['twitter_site'],
				'otherSocialUrls' => $social_profiles['other_social_urls'],
			],
			'isPremium'                  => $this->product_helper->is_premium(),
			'isWooCommerceActive'        => $this->woocommerce_helper->is_active(),
			'isWooCommerceSeoActive'     => $this->is_wooseo_active(),
			'tracking'                   => $this->has_tracking_enabled(),
			'isTrackingAllowedMultisite' => $this->is_tracking_enabled_multisite(),
			'isMainSite'                 => $this->is_main_site(),
			'companyOrPersonOptions'     => $options,
			'shouldForceCompany'         => $this->should_force_company(),
			'knowledgeGraphMessage'      => $knowledge_graph_message,
			'shortlinks'                 => [
				'gdpr'                     => $this->shortlinker->build_shortlink( 'https://yoa.st/gdpr-config-workout' ),
				'configIndexables'         => $this->shortlinker->build_shortlink( 'https://yoa.st/config-indexables' ),
				'configIndexablesBenefits' => $this->shortlinker->build_shortlink( 'https://yoa.st/config-indexables-benefits' ),
				'indexationLearnMore'      => $this->shortlinker->build_shortlink( 'https://yoa.st/ftc-indexation-premium-learn-more' ),
				'reprWoocommerceLearnMore' => $this->shortlinker->build_shortlink( 'https://yoa.st/ftc-representation-wooseo-learn-more' ),
				'reprLocalLearnMore'       => $this->shortlinker->build_shortlink( 'https://yoa.st/ftc-representation-local-learn-more' ),
				'finishLearnMore'          => $this->shortlinker->build_shortlink( 'https://yoa.st/ftc-finish-premium-learn-more' ),
			],
		];

		$this->admin_asset_manager->localize_script( 'general-page', 'wpseoFirstTimeConfigurationData', $data_ftc );
	}

	/**
	 * Retrieves a list of the endpoints to use.
	 *
	 * @return array The endpoints.
	 */
	protected function get_endpoints() {
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

	// ** Private functions ** //

	/**
	 * Returns the finished steps array.
	 *
	 * @return array An array with the finished steps.
	 */
	private function get_finished_steps() {
		return $this->options_helper->get( 'configuration_finished_steps', [] );
	}

	/**
	 * Returns the entity represented by the site.
	 *
	 * @return string The entity represented by the site.
	 */
	private function is_company_or_person() {
		return $this->options_helper->get( 'company_or_person', '' );
	}

	/**
	 * Gets the company name from the option in the database.
	 *
	 * @return string The company name.
	 */
	private function get_company_name() {
		return $this->options_helper->get( 'company_name', '' );
	}

	/**
	 * Gets the fallback company name from the option in the database if there is no company name.
	 *
	 * @param string $company_name The given company name by the user, default empty string.
	 *
	 * @return string|false The company name.
	 */
	private function get_fallback_company_name( $company_name ) {
		if ( $company_name ) {
			return false;
		}

		return \get_bloginfo( 'name' );
	}

	/**
	 * Gets the website name from the option in the database.
	 *
	 * @return string The website name.
	 */
	private function get_website_name() {
		return $this->options_helper->get( 'website_name', '' );
	}

	/**
	 * Gets the fallback website name from the option in the database if there is no website name.
	 *
	 * @param string $website_name The given website name by the user, default empty string.
	 *
	 * @return string|false The website name.
	 */
	private function get_fallback_website_name( $website_name ) {
		if ( $website_name ) {
			return false;
		}

		return \get_bloginfo( 'name' );
	}

	/**
	 * Gets the company logo from the option in the database.
	 *
	 * @return string The company logo.
	 */
	private function get_company_logo() {
		return $this->options_helper->get( 'company_logo', '' );
	}

	/**
	 * Gets the company logo id from the option in the database.
	 *
	 * @return string The company logo id.
	 */
	private function get_company_logo_id() {
		return $this->options_helper->get( 'company_logo_id', '' );
	}

	/**
	 * Gets the company logo url from the option in the database.
	 *
	 * @param string $company_logo The given company logo by the user, default empty.
	 *
	 * @return string|false The company logo URL.
	 */
	private function get_company_fallback_logo( $company_logo ) {
		if ( $company_logo ) {
			return false;
		}
		$logo_id = $this->meta_tags_context->fallback_to_site_logo();

		return \esc_url( \wp_get_attachment_url( $logo_id ) );
	}

	/**
	 * Gets the person id from the option in the database.
	 *
	 * @return int|null The person id, null if empty.
	 */
	private function get_person_id() {
		return $this->options_helper->get( 'company_or_person_user_id' );
	}

	/**
	 * Gets the person id from the option in the database.
	 *
	 * @return int|null The person id, null if empty.
	 */
	private function get_person_name() {
		$user = \get_userdata( $this->get_person_id() );
		if ( $user instanceof WP_User ) {
			return $user->get( 'display_name' );
		}

		return '';
	}

	/**
	 * Gets the person avatar from the option in the database.
	 *
	 * @return string The person logo.
	 */
	private function get_person_logo() {
		return $this->options_helper->get( 'person_logo', '' );
	}

	/**
	 * Gets the person logo url from the option in the database.
	 *
	 * @param string $person_logo The given person logo by the user, default empty.
	 *
	 * @return string|false The person logo URL.
	 */
	private function get_person_fallback_logo( $person_logo ) {
		if ( $person_logo ) {
			return false;
		}
		$logo_id = $this->meta_tags_context->fallback_to_site_logo();

		return \esc_url( \wp_get_attachment_url( $logo_id ) );
	}

	/**
	 * Gets the person logo id from the option in the database.
	 *
	 * @return string The person logo id.
	 */
	private function get_person_logo_id() {
		return $this->options_helper->get( 'person_logo_id', '' );
	}

	/**
	 * Gets the site tagline.
	 *
	 * @return string The site tagline.
	 */
	private function get_site_tagline() {
		return \get_bloginfo( 'description' );
	}

	/**
	 * Gets the social profiles stored in the database.
	 *
	 * @return string[] The social profiles.
	 */
	private function get_social_profiles() {
		return $this->social_profiles_helper->get_organization_social_profiles();
	}

	/**
	 * Checks whether tracking is enabled.
	 *
	 * @return bool True if tracking is enabled, false otherwise, null if in Free and conf. workout step not finished.
	 */
	private function has_tracking_enabled() {
		$default = false;

		if ( $this->product_helper->is_premium() ) {
			$default = true;
		}

		return $this->options_helper->get( 'tracking', $default );
	}

	/**
	 * Checks whether tracking option is allowed at network level.
	 *
	 * @return bool True if option change is allowed, false otherwise.
	 */
	private function is_tracking_enabled_multisite() {
		$default = true;

		if ( ! \is_multisite() ) {
			return $default;
		}

		return $this->options_helper->get( 'allow_tracking', $default );
	}

	/**
	 * Checks whether we are in a main site.
	 *
	 * @return bool True if it's the main site or a single site, false if it's a subsite.
	 */
	private function is_main_site() {
		return \is_main_site();
	}

	/**
	 * Gets the options for the Company or Person select.
	 * Returns only the company option if it is forced (by Local SEO), otherwise returns company and person option.
	 *
	 * @return array The options for the company-or-person select.
	 */
	private function get_company_or_person_options() {
		$options = [
			[
				'label' => \__( 'Organization', 'wordpress-seo' ),
				'value' => 'company',
				'id'    => 'company',
			],
			[
				'label' => \__( 'Person', 'wordpress-seo' ),
				'value' => 'person',
				'id'    => 'person',
			],
		];
		if ( $this->should_force_company() ) {
			$options = [
				[
					'label' => \__( 'Organization', 'wordpress-seo' ),
					'value' => 'company',
					'id'    => 'company',
				],
			];
		}

		return $options;
	}

	/**
	 * Checks whether we should force "Organization".
	 *
	 * @return bool
	 */
	private function should_force_company() {
		return $this->addon_manager->is_installed( WPSEO_Addon_Manager::LOCAL_SLUG );
	}

	/**
	 * Checks if the current user has the capability to edit a specific user.
	 *
	 * @param int $person_id The id of the person to edit.
	 *
	 * @return bool
	 */
	private function can_edit_profile( $person_id ) {
		return \current_user_can( 'edit_user', $person_id );
	}

	/**
	 * Checks if Yoast WooCommerce SEO is active.
	 *
	 * @return bool
	 */
	private function is_wooseo_active() {
		$addon_manager = new WPSEO_Addon_Manager();
		return $addon_manager->is_installed( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG );
	}
}
