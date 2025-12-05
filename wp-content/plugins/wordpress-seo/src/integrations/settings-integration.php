<?php

namespace Yoast\WP\SEO\Integrations;

use WP_Post;
use WP_Post_Type;
use WP_Taxonomy;
use WP_User;
use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use WPSEO_Admin_Editor_Specific_Replace_Vars;
use WPSEO_Admin_Recommended_Replace_Vars;
use WPSEO_Option_Titles;
use WPSEO_Options;
use WPSEO_Replace_Vars;
use WPSEO_Shortlinker;
use WPSEO_Sitemaps_Router;
use Yoast\WP\SEO\Conditionals\Settings_Conditional;
use Yoast\WP\SEO\Config\Schema_Types;
use Yoast\WP\SEO\Content_Type_Visibility\Application\Content_Type_Visibility_Dismiss_Notifications;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Schema\Article_Helper;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Helpers\Woocommerce_Helper;
use Yoast\WP\SEO\Llms_Txt\Application\Configuration\Llms_Txt_Configuration;
use Yoast\WP\SEO\Llms_Txt\Application\Health_Check\File_Runner;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\Content\Manual_Post_Collection;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Class Settings_Integration.
 */
class Settings_Integration implements Integration_Interface {

	public const PAGE = 'wpseo_page_settings';

	/**
	 * Holds the included WordPress options.
	 *
	 * @var string[]
	 */
	public const WP_OPTIONS = [ 'blogdescription' ];

	/**
	 * Holds the allowed option groups.
	 *
	 * @var array
	 */
	public const ALLOWED_OPTION_GROUPS = [ 'wpseo', 'wpseo_titles', 'wpseo_social', 'wpseo_llmstxt' ];

	/**
	 * Holds the disallowed settings, per option group.
	 *
	 * @var array
	 */
	public const DISALLOWED_SETTINGS = [
		'wpseo'        => [
			'myyoast-oauth',
			'semrush_tokens',
			'custom_taxonomy_slugs',
			'import_cursors',
			'workouts_data',
			'configuration_finished_steps',
			'importing_completed',
			'wincher_tokens',
			'least_readability_ignore_list',
			'least_seo_score_ignore_list',
			'most_linked_ignore_list',
			'least_linked_ignore_list',
			'indexables_page_reading_list',
			'show_new_content_type_notification',
			'new_post_types',
			'new_taxonomies',
		],
		'wpseo_titles' => [
			'company_logo_meta',
			'person_logo_meta',
		],
	];

	/**
	 * Holds the disabled on multisite settings, per option group.
	 *
	 * @var array
	 */
	public const DISABLED_ON_MULTISITE_SETTINGS = [
		'wpseo' => [
			'deny_search_crawling',
			'deny_wp_json_crawling',
			'deny_adsbot_crawling',
			'deny_ccbot_crawling',
			'deny_google_extended_crawling',
			'deny_gptbot_crawling',
			'enable_llms_txt',
		],
	];

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Holds the WPSEO_Replace_Vars.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	protected $replace_vars;

	/**
	 * Holds the Schema_Types.
	 *
	 * @var Schema_Types
	 */
	protected $schema_types;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	protected $current_page_helper;

	/**
	 * Holds the Post_Type_Helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * Holds the Language_Helper.
	 *
	 * @var Language_Helper
	 */
	protected $language_helper;

	/**
	 * Holds the Taxonomy_Helper.
	 *
	 * @var Taxonomy_Helper
	 */
	protected $taxonomy_helper;

	/**
	 * Holds the Product_Helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * Holds the Woocommerce_Helper.
	 *
	 * @var Woocommerce_Helper
	 */
	protected $woocommerce_helper;

	/**
	 * Holds the Article_Helper.
	 *
	 * @var Article_Helper
	 */
	protected $article_helper;

	/**
	 * Holds the User_Helper.
	 *
	 * @var User_Helper
	 */
	protected $user_helper;

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Holds the Content_Type_Visibility_Dismiss_Notifications instance.
	 *
	 * @var Content_Type_Visibility_Dismiss_Notifications
	 */
	protected $content_type_visibility;

	/**
	 * Holds the Llms_Txt_Configuration instance.
	 *
	 * @var Llms_Txt_Configuration
	 */
	protected $llms_txt_configuration;

	/**
	 * The manual post collection.
	 *
	 * @var Manual_Post_Collection
	 */
	private $manual_post_collection;

	/**
	 * Runs the health check.
	 *
	 * @var File_Runner
	 */
	private $runner;

	/**
	 * Constructs Settings_Integration.
	 *
	 * @param WPSEO_Admin_Asset_Manager                     $asset_manager           The WPSEO_Admin_Asset_Manager.
	 * @param WPSEO_Replace_Vars                            $replace_vars            The WPSEO_Replace_Vars.
	 * @param Schema_Types                                  $schema_types            The Schema_Types.
	 * @param Current_Page_Helper                           $current_page_helper     The Current_Page_Helper.
	 * @param Post_Type_Helper                              $post_type_helper        The Post_Type_Helper.
	 * @param Language_Helper                               $language_helper         The Language_Helper.
	 * @param Taxonomy_Helper                               $taxonomy_helper         The Taxonomy_Helper.
	 * @param Product_Helper                                $product_helper          The Product_Helper.
	 * @param Woocommerce_Helper                            $woocommerce_helper      The Woocommerce_Helper.
	 * @param Article_Helper                                $article_helper          The Article_Helper.
	 * @param User_Helper                                   $user_helper             The User_Helper.
	 * @param Options_Helper                                $options                 The options helper.
	 * @param Content_Type_Visibility_Dismiss_Notifications $content_type_visibility The Content_Type_Visibility_Dismiss_Notifications instance.
	 * @param Llms_Txt_Configuration                        $llms_txt_configuration  The Llms_Txt_Configuration instance.
	 * @param Manual_Post_Collection                        $manual_post_collection  The manual post collection.
	 * @param File_Runner                                   $runner                  The file runner.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		WPSEO_Replace_Vars $replace_vars,
		Schema_Types $schema_types,
		Current_Page_Helper $current_page_helper,
		Post_Type_Helper $post_type_helper,
		Language_Helper $language_helper,
		Taxonomy_Helper $taxonomy_helper,
		Product_Helper $product_helper,
		Woocommerce_Helper $woocommerce_helper,
		Article_Helper $article_helper,
		User_Helper $user_helper,
		Options_Helper $options,
		Content_Type_Visibility_Dismiss_Notifications $content_type_visibility,
		Llms_Txt_Configuration $llms_txt_configuration,
		Manual_Post_Collection $manual_post_collection,
		File_Runner $runner
	) {
		$this->asset_manager           = $asset_manager;
		$this->replace_vars            = $replace_vars;
		$this->schema_types            = $schema_types;
		$this->current_page_helper     = $current_page_helper;
		$this->taxonomy_helper         = $taxonomy_helper;
		$this->post_type_helper        = $post_type_helper;
		$this->language_helper         = $language_helper;
		$this->product_helper          = $product_helper;
		$this->woocommerce_helper      = $woocommerce_helper;
		$this->article_helper          = $article_helper;
		$this->user_helper             = $user_helper;
		$this->options                 = $options;
		$this->content_type_visibility = $content_type_visibility;
		$this->llms_txt_configuration  = $llms_txt_configuration;
		$this->manual_post_collection  = $manual_post_collection;
		$this->runner                  = $runner;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Settings_Conditional::class ];
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
		\add_filter( 'admin_menu', [ $this, 'add_settings_saved_page' ] );

		// Are we saving the settings?
		if ( $this->current_page_helper->get_current_admin_page() === 'options.php' ) {
			$post_action = '';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
			if ( isset( $_POST['action'] ) && \is_string( $_POST['action'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information.
				$post_action = \wp_unslash( $_POST['action'] );
			}
			$option_page = '';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
			if ( isset( $_POST['option_page'] ) && \is_string( $_POST['option_page'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information.
				$option_page = \wp_unslash( $_POST['option_page'] );
			}

			if ( $post_action === 'update' && $option_page === self::PAGE ) {
				\add_action( 'admin_init', [ $this, 'register_setting' ] );
				\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
			}

			return;
		}

		// Are we on the settings page?
		if ( $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			\add_action( 'admin_init', [ $this, 'register_setting' ] );
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
		}
	}

	/**
	 * Registers the different options under the setting.
	 *
	 * @return void
	 */
	public function register_setting() {
		foreach ( WPSEO_Options::$options as $name => $instance ) {
			if ( \in_array( $name, self::ALLOWED_OPTION_GROUPS, true ) ) {
				\register_setting( self::PAGE, $name );
			}
		}
		// Only register WP options when the user is allowed to manage them.
		if ( \current_user_can( 'manage_options' ) ) {
			foreach ( self::WP_OPTIONS as $name ) {
				\register_setting( self::PAGE, $name );
			}
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
			1,
			0,
			[
				[
					'wpseo_dashboard',
					'',
					\__( 'Settings', 'wordpress-seo' ),
					'wpseo_manage_options',
					self::PAGE,
					[ $this, 'display_page' ],
				],
			]
		);

		return $pages;
	}

	/**
	 * Adds a dummy page.
	 *
	 * Because the options route NEEDS to redirect to something.
	 *
	 * @param array $pages The pages.
	 *
	 * @return array The pages.
	 */
	public function add_settings_saved_page( $pages ) {
		$runner = $this->runner;
		\add_submenu_page(
			'',
			'',
			'',
			'wpseo_manage_options',
			self::PAGE . '_saved',
			static function () use ( $runner ) {
				// Add success indication to HTML response.
				$success = empty( \get_settings_errors() ) ? 'true' : 'false';
				echo \esc_html( "{{ yoast-success: $success }}" );

				$runner->run();
				if ( ! $runner->is_successful() ) {
					$failure_reason = $runner->get_generation_failure_reason();
					echo \esc_html( "{{ yoast-llms-txt-generation-failure: $failure_reason }}" );
				}
			}
		);

		return $pages;
	}

	/**
	 * Displays the page.
	 *
	 * @return void
	 */
	public function display_page() {
		echo '<div id="yoast-seo-settings"></div>';
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
		$this->asset_manager->enqueue_script( 'new-settings' );
		$this->asset_manager->enqueue_style( 'new-settings' );
		if ( \YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
			$this->asset_manager->enqueue_style( 'black-friday-banner' );
		}
		$this->asset_manager->localize_script( 'new-settings', 'wpseoScriptData', $this->get_script_data() );
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
	protected function get_script_data() {
		$default_setting_values = $this->get_default_setting_values();
		$settings               = $this->get_settings( $default_setting_values );
		$post_types             = $this->post_type_helper->get_indexable_post_type_objects();
		$taxonomies             = $this->taxonomy_helper->get_indexable_taxonomy_objects();

		// Check if attachments are included in indexation.
		if ( ! \array_key_exists( 'attachment', $post_types ) ) {
			// Always include attachments in the settings, to let the user enable them again.
			$attachment_object = \get_post_type_object( 'attachment' );
			if ( ! empty( $attachment_object ) ) {
				$post_types['attachment'] = $attachment_object;
			}
		}
		// Check if post formats are included in indexation.
		if ( ! \array_key_exists( 'post_format', $taxonomies ) ) {
			// Always include post_format in the settings, to let the user enable them again.
			$post_format_object = \get_taxonomy( 'post_format' );
			if ( ! empty( $post_format_object ) ) {
				$taxonomies['post_format'] = $post_format_object;
			}
		}

		$transformed_post_types = $this->transform_post_types( $post_types );
		$transformed_taxonomies = $this->transform_taxonomies( $taxonomies, \array_keys( $transformed_post_types ) );

		// Check if there is a new content type to show notification only once in the settings.
		$show_new_content_type_notification = $this->content_type_visibility->maybe_add_settings_notification();

		return [
			'settings'                       => $this->transform_settings( $settings ),
			'defaultSettingValues'           => $default_setting_values,
			'disabledSettings'               => $this->get_disabled_settings( $settings ),
			'endpoint'                       => \admin_url( 'options.php' ),
			'nonce'                          => \wp_create_nonce( self::PAGE . '-options' ),
			'separators'                     => WPSEO_Option_Titles::get_instance()->get_separator_options_for_display(),
			'replacementVariables'           => $this->get_replacement_variables(),
			'schema'                         => $this->get_schema( $transformed_post_types ),
			'preferences'                    => $this->get_preferences( $settings ),
			'linkParams'                     => WPSEO_Shortlinker::get_query_params(),
			'postTypes'                      => $transformed_post_types,
			'taxonomies'                     => $transformed_taxonomies,
			'fallbacks'                      => $this->get_fallbacks(),
			'showNewContentTypeNotification' => $show_new_content_type_notification,
			'currentPromotions'              => \YoastSEO()->classes->get( Promotion_Manager::class )->get_current_promotions(),
			'llmsTxt'                        => $this->llms_txt_configuration->get_configuration(),
			'initialLlmTxtPages'             => $this->get_site_llms_txt_pages( $settings ),
		];
	}

	/**
	 * Retrieves the preferences.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The preferences.
	 */
	protected function get_preferences( $settings ) {
		$shop_page_id             = $this->woocommerce_helper->get_shop_page_id();
		$homepage_is_latest_posts = \get_option( 'show_on_front' ) === 'posts';
		$page_on_front            = \get_option( 'page_on_front' );
		$page_for_posts           = \get_option( 'page_for_posts' );

		$addon_manager          = new WPSEO_Addon_Manager();
		$woocommerce_seo_active = \is_plugin_active( $addon_manager->get_plugin_file( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG ) );

		if ( empty( $page_on_front ) ) {
			$page_on_front = $page_for_posts;
		}

		$business_settings_url = \get_admin_url( null, 'admin.php?page=wpseo_local' );
		if ( \defined( 'WPSEO_LOCAL_FILE' ) ) {
			$local_options      = \get_option( 'wpseo_local' );
			$multiple_locations = $local_options['use_multiple_locations'];
			$same_organization  = $local_options['multiple_locations_same_organization'];
			if ( $multiple_locations === 'on' && $same_organization !== 'on' ) {
				$business_settings_url = \get_admin_url( null, 'edit.php?post_type=wpseo_locations' );
			}
		}

		return [
			'isPremium'                     => $this->product_helper->is_premium(),
			'isRtl'                         => \is_rtl(),
			'isNetworkAdmin'                => \is_network_admin(),
			'isMainSite'                    => \is_main_site(),
			'isMultisite'                   => \is_multisite(),
			'isWooCommerceActive'           => $this->woocommerce_helper->is_active(),
			'isLocalSeoActive'              => \defined( 'WPSEO_LOCAL_FILE' ),
			'isNewsSeoActive'               => \defined( 'WPSEO_NEWS_FILE' ),
			'isWooCommerceSEOActive'        => $woocommerce_seo_active,
			'siteUrl'                       => \get_bloginfo( 'url' ),
			'siteTitle'                     => \get_bloginfo( 'name' ),
			'sitemapUrl'                    => WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' ),
			'hasWooCommerceShopPage'        => $shop_page_id !== -1,
			'editWooCommerceShopPageUrl'    => \get_edit_post_link( $shop_page_id, 'js' ),
			'wooCommerceShopPageSettingUrl' => \get_admin_url( null, 'admin.php?page=wc-settings&tab=products' ),
			'localSeoPageSettingUrl'        => $business_settings_url,
			'homepageIsLatestPosts'         => $homepage_is_latest_posts,
			'homepagePageEditUrl'           => \get_edit_post_link( $page_on_front, 'js' ),
			'homepagePostsEditUrl'          => \get_edit_post_link( $page_for_posts, 'js' ),
			'createUserUrl'                 => \admin_url( 'user-new.php' ),
			'createPageUrl'                 => \admin_url( 'post-new.php?post_type=page' ),
			'editUserUrl'                   => \admin_url( 'user-edit.php' ),
			'editTaxonomyUrl'               => \admin_url( 'edit-tags.php' ),
			'generalSettingsUrl'            => \admin_url( 'options-general.php' ),
			'companyOrPersonMessage'        => \apply_filters( 'wpseo_knowledge_graph_setting_msg', '' ),
			'currentUserId'                 => \get_current_user_id(),
			'canCreateUsers'                => \current_user_can( 'create_users' ),
			'canCreatePages'                => \current_user_can( 'edit_pages' ),
			'canEditUsers'                  => \current_user_can( 'edit_users' ),
			'canManageOptions'              => \current_user_can( 'manage_options' ),
			'userLocale'                    => \str_replace( '_', '-', \get_user_locale() ),
			'pluginUrl'                     => \plugins_url( '', \WPSEO_FILE ),
			'showForceRewriteTitlesSetting' => ! \current_theme_supports( 'title-tag' ) && ! ( \function_exists( 'wp_is_block_theme' ) && \wp_is_block_theme() ),
			'upsellSettings'                => $this->get_upsell_settings(),
			'siteRepresentsPerson'          => $this->get_site_represents_person( $settings ),
			'siteBasicsPolicies'            => $this->get_site_basics_policies( $settings ),
		];
	}

	/**
	 * Retrieves the currently represented person.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The currently represented person.
	 */
	protected function get_site_represents_person( $settings ) {
		$person = [
			'id'   => false,
			'name' => '',
		];

		if ( isset( $settings['wpseo_titles']['company_or_person_user_id'] ) ) {
			$person['id'] = $settings['wpseo_titles']['company_or_person_user_id'];
			$user         = \get_userdata( $person['id'] );
			if ( $user instanceof WP_User ) {
				$person['name'] = $user->get( 'display_name' );
			}
		}

		return $person;
	}

	/**
	 * Get site policy data.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The policy data.
	 */
	private function get_site_basics_policies( $settings ) {
		$policies = [];

		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['publishing_principles_id'], 'publishing_principles_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['ownership_funding_info_id'], 'ownership_funding_info_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['actionable_feedback_policy_id'], 'actionable_feedback_policy_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['corrections_policy_id'], 'corrections_policy_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['ethics_policy_id'], 'ethics_policy_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['diversity_policy_id'], 'diversity_policy_id' );
		$policies = $this->maybe_add_policy( $policies, $settings['wpseo_titles']['diversity_staffing_report_id'], 'diversity_staffing_report_id' );

		return $policies;
	}

	/**
	 * Adds policy data if it is present.
	 *
	 * @param array  $policies The existing policy data.
	 * @param int    $policy   The policy id to check.
	 * @param string $key      The option key name.
	 *
	 * @return array<int, string> The policy data.
	 */
	private function maybe_add_policy( $policies, $policy, $key ) {
		$policy_array = [
			'id'   => 0,
			'name' => \__( 'None', 'wordpress-seo' ),
		];

		if ( isset( $policy ) && \is_int( $policy ) ) {
			$policy_array['id'] = $policy;
			$post               = \get_post( $policy );
			if ( $post instanceof WP_Post ) {
				if ( $post->post_status !== 'publish' || $post->post_password !== '' ) {
					return $policies;
				}
				$policy_array['name'] = $post->post_title;
			}
		}

		$policies[ $key ] = $policy_array;

		return $policies;
	}

	/**
	 * Adds page if it is present.
	 *
	 * @param array<int, string> $pages   The existing pages.
	 * @param int                $page_id The page id to check.
	 * @param string             $key     The option key name.
	 *
	 * @return array<int, string> The policy data.
	 */
	private function maybe_add_page( $pages, $page_id, $key ) {
		if ( isset( $page_id ) && \is_int( $page_id ) && $page_id !== 0 ) {
			$post = $this->manual_post_collection->get_content_type_entry( $page_id );
			if ( $post === null ) {
				return $pages;
			}

			$pages[ $key ] = [
				'id'    => $page_id,
				'title' => ( $post->get_title() ) ? $post->get_title() : $post->get_slug(),
				'slug'  => $post->get_slug(),
			];
		}

		return $pages;
	}

	/**
	 * Get site llms.txt pages.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array<string, array<string, int|string>> The llms.txt pages.
	 */
	private function get_site_llms_txt_pages( $settings ) {
		$llms_txt_pages = [];

		$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $settings['wpseo_llmstxt']['about_us_page'], 'about_us_page' );
		$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $settings['wpseo_llmstxt']['contact_page'], 'contact_page' );
		$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $settings['wpseo_llmstxt']['terms_page'], 'terms_page' );
		$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $settings['wpseo_llmstxt']['privacy_policy_page'], 'privacy_policy_page' );
		$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $settings['wpseo_llmstxt']['shop_page'], 'shop_page' );

		if ( isset( $settings['wpseo_llmstxt']['other_included_pages'] ) && \is_array( $settings['wpseo_llmstxt']['other_included_pages'] ) ) {
			foreach ( $settings['wpseo_llmstxt']['other_included_pages'] as $key => $page_id ) {
				$llms_txt_pages = $this->maybe_add_page( $llms_txt_pages, $page_id, 'other_included_pages-' . $key );
			}
		}

		return $llms_txt_pages;
	}

	/**
	 * Returns settings for the Call to Buy (CTB) buttons.
	 *
	 * @return array<string> The array of CTB settings.
	 */
	public function get_upsell_settings() {
		return [
			'actionId'     => 'load-nfd-ctb',
			'premiumCtbId' => 'f6a84663-465f-4cb5-8ba5-f7a6d72224b2',
		];
	}

	/**
	 * Retrieves the default setting values.
	 *
	 * These default values are currently being used in the UI for dummy fields.
	 * Dummy fields should not expose or reflect the actual data.
	 *
	 * @return array The default setting values.
	 */
	protected function get_default_setting_values() {
		$defaults = [];

		// Add Yoast settings.
		foreach ( WPSEO_Options::$options as $option_name => $instance ) {
			if ( \in_array( $option_name, self::ALLOWED_OPTION_GROUPS, true ) ) {
				$option_instance          = WPSEO_Options::get_option_instance( $option_name );
				$defaults[ $option_name ] = ( $option_instance ) ? $option_instance->get_defaults() : [];
			}
		}
		// Add WP settings.
		foreach ( self::WP_OPTIONS as $option_name ) {
			$defaults[ $option_name ] = '';
		}

		// Remove disallowed settings.
		foreach ( self::DISALLOWED_SETTINGS as $option_name => $disallowed_settings ) {
			foreach ( $disallowed_settings as $disallowed_setting ) {
				unset( $defaults[ $option_name ][ $disallowed_setting ] );
			}
		}

		if ( \defined( 'WPSEO_LOCAL_FILE' ) ) {
			$defaults = $this->get_defaults_from_local_seo( $defaults );
		}

		return $defaults;
	}

	/**
	 * Retrieves the organization schema values from Local SEO for defaults in Site representation fields.
	 * Specifically for the org-vat-id, org-tax-id, org-email and org-phone options.
	 *
	 * @param array<string|int|bool> $defaults The settings defaults.
	 *
	 * @return array<string|int|bool> The settings defaults with local seo overides.
	 */
	protected function get_defaults_from_local_seo( $defaults ) {
		$local_options      = \get_option( 'wpseo_local' );
		$multiple_locations = $local_options['use_multiple_locations'];
		$same_organization  = $local_options['multiple_locations_same_organization'];
		$shared_info        = $local_options['multiple_locations_shared_business_info'];
		if ( $multiple_locations !== 'on' || ( $multiple_locations === 'on' && $same_organization === 'on' && $shared_info === 'on' ) ) {
			$defaults['wpseo_titles']['org-vat-id'] = $local_options['location_vat_id'];
			$defaults['wpseo_titles']['org-tax-id'] = $local_options['location_tax_id'];
			$defaults['wpseo_titles']['org-email']  = $local_options['location_email'];
			$defaults['wpseo_titles']['org-phone']  = $local_options['location_phone'];
		}

		if ( \wpseo_has_primary_location() ) {
			$primary_location = $local_options['multiple_locations_primary_location'];

			$location_keys = [
				'org-phone'  => [
					'is_overridden' => '_wpseo_is_overridden_business_phone',
					'value'         => '_wpseo_business_phone',
				],
				'org-email'  => [
					'is_overridden' => '_wpseo_is_overridden_business_email',
					'value'         => '_wpseo_business_email',
				],
				'org-tax-id' => [
					'is_overridden' => '_wpseo_is_overridden_business_tax_id',
					'value'         => '_wpseo_business_tax_id',
				],
				'org-vat-id' => [
					'is_overridden' => '_wpseo_is_overridden_business_vat_id',
					'value'         => '_wpseo_business_vat_id',
				],
			];

			foreach ( $location_keys as $key => $meta_keys ) {
				$is_overridden = ( $shared_info === 'on' ) ? \get_post_meta( $primary_location, $meta_keys['is_overridden'], true ) : false;
				if ( $is_overridden === 'on' || $shared_info !== 'on' ) {
					$post_meta_value                  = \get_post_meta( $primary_location, $meta_keys['value'], true );
					$defaults['wpseo_titles'][ $key ] = ( $post_meta_value ) ? $post_meta_value : '';
				}
			}
		}

		return $defaults;
	}

	/**
	 * Retrieves the settings and their values.
	 *
	 * @param array $default_setting_values The default setting values.
	 *
	 * @return array The settings.
	 */
	protected function get_settings( $default_setting_values ) {
		$settings = [];

		// Add Yoast settings.
		foreach ( WPSEO_Options::$options as $option_name => $instance ) {
			if ( \in_array( $option_name, self::ALLOWED_OPTION_GROUPS, true ) ) {
				$settings[ $option_name ] = \array_merge( $default_setting_values[ $option_name ], WPSEO_Options::get_option( $option_name ) );
			}
		}
		// Add WP settings.
		foreach ( self::WP_OPTIONS as $option_name ) {
			$settings[ $option_name ] = \get_option( $option_name );
		}

		// Remove disallowed settings.
		foreach ( self::DISALLOWED_SETTINGS as $option_name => $disallowed_settings ) {
			foreach ( $disallowed_settings as $disallowed_setting ) {
				unset( $settings[ $option_name ][ $disallowed_setting ] );
			}
		}

		return $settings;
	}

	/**
	 * Transforms setting values.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The settings.
	 */
	protected function transform_settings( $settings ) {
		if ( isset( $settings['wpseo_titles']['breadcrumbs-sep'] ) ) {
			/**
			 * The breadcrumbs separator default value is the HTML entity `&raquo;`.
			 * Which does not get decoded in our JS, while it did in our Yoast form. Decode it here as an exception.
			 */
			$settings['wpseo_titles']['breadcrumbs-sep'] = \html_entity_decode(
				$settings['wpseo_titles']['breadcrumbs-sep'],
				( \ENT_NOQUOTES | \ENT_HTML5 ),
				'UTF-8'
			);
		}

		/**
		 * Decode some WP options.
		 */
		$settings['blogdescription'] = \html_entity_decode(
			$settings['blogdescription'],
			( \ENT_NOQUOTES | \ENT_HTML5 ),
			'UTF-8'
		);

		if ( isset( $settings['wpseo_llmstxt']['other_included_pages'] ) ) {
			// Append an empty page to the other included pages, so that we manage to show an empty field in the UI.
			$settings['wpseo_llmstxt']['other_included_pages'][] = 0;
		}

		return $settings;
	}

	/**
	 * Retrieves the disabled settings.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The settings.
	 */
	protected function get_disabled_settings( $settings ) {
		$disabled_settings = [];
		$site_language     = $this->language_helper->get_language();

		foreach ( WPSEO_Options::$options as $option_name => $instance ) {
			if ( ! \in_array( $option_name, self::ALLOWED_OPTION_GROUPS, true ) ) {
				continue;
			}

			$disabled_settings[ $option_name ] = [];
			$option_instance                   = WPSEO_Options::get_option_instance( $option_name );
			if ( $option_instance === false ) {
				continue;
			}
			foreach ( $settings[ $option_name ] as $setting_name => $setting_value ) {
				if ( $option_instance->is_disabled( $setting_name ) ) {
					$disabled_settings[ $option_name ][ $setting_name ] = 'network';
				}
			}
		}

		// Remove disabled on multisite settings.
		if ( \is_multisite() ) {
			foreach ( self::DISABLED_ON_MULTISITE_SETTINGS as $option_name => $disabled_ms_settings ) {
				if ( \array_key_exists( $option_name, $disabled_settings ) ) {
					foreach ( $disabled_ms_settings as $disabled_ms_setting ) {
						$disabled_settings[ $option_name ][ $disabled_ms_setting ] = 'multisite';
					}
				}
			}
		}

		if ( \array_key_exists( 'wpseo', $disabled_settings ) && ! $this->language_helper->has_inclusive_language_support( $site_language ) ) {
			$disabled_settings['wpseo']['inclusive_language_analysis_active'] = 'language';
		}

		return $disabled_settings;
	}

	/**
	 * Retrieves the replacement variables.
	 *
	 * @return array The replacement variables.
	 */
	protected function get_replacement_variables() {
		$recommended_replace_vars = new WPSEO_Admin_Recommended_Replace_Vars();
		$specific_replace_vars    = new WPSEO_Admin_Editor_Specific_Replace_Vars();
		$replacement_variables    = $this->replace_vars->get_replacement_variables_with_labels();

		return [
			'variables'   => $replacement_variables,
			'recommended' => $recommended_replace_vars->get_recommended_replacevars(),
			'specific'    => $specific_replace_vars->get(),
			'shared'      => $specific_replace_vars->get_generic( $replacement_variables ),
		];
	}

	/**
	 * Retrieves the schema.
	 *
	 * @param array $post_types The post types.
	 *
	 * @return array The schema.
	 */
	protected function get_schema( array $post_types ) {
		$schema = [];

		foreach ( $this->schema_types->get_article_type_options() as $article_type ) {
			$schema['articleTypes'][ $article_type['value'] ] = [
				'label' => $article_type['name'],
				'value' => $article_type['value'],
			];
		}

		foreach ( $this->schema_types->get_page_type_options() as $page_type ) {
			$schema['pageTypes'][ $page_type['value'] ] = [
				'label' => $page_type['name'],
				'value' => $page_type['value'],
			];
		}

		$schema['articleTypeDefaults'] = [];
		$schema['pageTypeDefaults']    = [];
		foreach ( $post_types as $name => $post_type ) {
			$schema['articleTypeDefaults'][ $name ] = WPSEO_Options::get_default( 'wpseo_titles', "schema-article-type-$name" );
			$schema['pageTypeDefaults'][ $name ]    = WPSEO_Options::get_default( 'wpseo_titles', "schema-page-type-$name" );
		}

		return $schema;
	}

	/**
	 * Transforms the post types, to represent them.
	 *
	 * @param WP_Post_Type[] $post_types The WP_Post_Type array to transform.
	 *
	 * @return array The post types.
	 */
	protected function transform_post_types( $post_types ) {
		$transformed    = [];
		$new_post_types = $this->options->get( 'new_post_types', [] );
		foreach ( $post_types as $post_type ) {
			$transformed[ $post_type->name ] = [
				'name'                 => $post_type->name,
				'route'                => $this->get_route( $post_type->name, $post_type->rewrite, $post_type->rest_base ),
				'label'                => $post_type->label,
				'singularLabel'        => $post_type->labels->singular_name,
				'hasArchive'           => $this->post_type_helper->has_archive( $post_type ),
				'hasSchemaArticleType' => $this->article_helper->is_article_post_type( $post_type->name ),
				'menuPosition'         => $post_type->menu_position,
				'isNew'                => \in_array( $post_type->name, $new_post_types, true ),
			];
		}

		\uasort( $transformed, [ $this, 'compare_post_types' ] );

		return $transformed;
	}

	/**
	 * Compares two post types.
	 *
	 * @param array $a The first post type.
	 * @param array $b The second post type.
	 *
	 * @return int The order.
	 */
	protected function compare_post_types( $a, $b ) {
		if ( $a['menuPosition'] === null && $b['menuPosition'] !== null ) {
			return 1;
		}
		if ( $a['menuPosition'] !== null && $b['menuPosition'] === null ) {
			return -1;
		}

		if ( $a['menuPosition'] === null && $b['menuPosition'] === null ) {
			// No position specified, order alphabetically by label.
			return \strnatcmp( $a['label'], $b['label'] );
		}

		return ( ( $a['menuPosition'] < $b['menuPosition'] ) ? -1 : 1 );
	}

	/**
	 * Transforms the taxonomies, to represent them.
	 *
	 * @param WP_Taxonomy[] $taxonomies      The WP_Taxonomy array to transform.
	 * @param string[]      $post_type_names The post type names.
	 *
	 * @return array The taxonomies.
	 */
	protected function transform_taxonomies( $taxonomies, $post_type_names ) {
		$transformed    = [];
		$new_taxonomies = $this->options->get( 'new_taxonomies', [] );
		foreach ( $taxonomies as $taxonomy ) {
			$transformed[ $taxonomy->name ] = [
				'name'          => $taxonomy->name,
				'route'         => $this->get_route( $taxonomy->name, $taxonomy->rewrite, $taxonomy->rest_base ),
				'label'         => $taxonomy->label,
				'showUi'        => $taxonomy->show_ui,
				'singularLabel' => $taxonomy->labels->singular_name,
				'postTypes'     => \array_filter(
					$taxonomy->object_type,
					static function ( $object_type ) use ( $post_type_names ) {
						return \in_array( $object_type, $post_type_names, true );
					}
				),
				'isNew'         => \in_array( $taxonomy->name, $new_taxonomies, true ),
			];
		}

		\uasort(
			$transformed,
			static function ( $a, $b ) {
				return \strnatcmp( $a['label'], $b['label'] );
			}
		);

		return $transformed;
	}

	/**
	 * Gets the route from a name, rewrite and rest_base.
	 *
	 * @param string $name      The name.
	 * @param array  $rewrite   The rewrite data.
	 * @param string $rest_base The rest base.
	 *
	 * @return string The route.
	 */
	protected function get_route( $name, $rewrite, $rest_base ) {
		$route = $name;
		if ( isset( $rewrite['slug'] ) ) {
			$route = $rewrite['slug'];
		}
		if ( ! empty( $rest_base ) ) {
			$route = $rest_base;
		}
		// Always strip leading slashes.
		while ( \substr( $route, 0, 1 ) === '/' ) {
			$route = \substr( $route, 1 );
		}

		return $route;
	}

	/**
	 * Retrieves the fallbacks.
	 *
	 * @return array The fallbacks.
	 */
	protected function get_fallbacks() {
		$site_logo_id = \get_option( 'site_logo' );
		if ( ! $site_logo_id ) {
			$site_logo_id = \get_theme_mod( 'custom_logo' );
		}
		if ( ! $site_logo_id ) {
			$site_logo_id = '0';
		}

		return [
			'siteLogoId' => $site_logo_id,
		];
	}
}
