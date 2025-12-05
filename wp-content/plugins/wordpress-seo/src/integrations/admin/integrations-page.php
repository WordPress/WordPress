<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Easy_Digital_Downloads;
use SeriouslySimplePodcasting\Integrations\Yoast\Schema\PodcastEpisode;
use TEC\Events\Integrations\Plugins\WordPress_SEO\Events_Schema;
use WP_Recipe_Maker;
use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Jetpack_Conditional;
use Yoast\WP\SEO\Conditionals\Third_Party\Elementor_Activated_Conditional;
use Yoast\WP\SEO\Dashboard\Infrastructure\Endpoints\Site_Kit_Consent_Management_Endpoint;
use Yoast\WP\SEO\Dashboard\Infrastructure\Integrations\Site_Kit;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Woocommerce_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Integrations_Page class
 */
class Integrations_Page implements Integration_Interface {

	/**
	 * The Woocommerce helper.
	 *
	 * @var Woocommerce_Helper
	 */
	private $woocommerce_helper;

	/**
	 * The admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The elementor conditional.
	 *
	 * @var Elementor_Activated_Conditional
	 */
	private $elementor_conditional;

	/**
	 * The jetpack conditional.
	 *
	 * @var Jetpack_Conditional
	 */
	private $jetpack_conditional;

	/**
	 * The site kit integration configuration data.
	 *
	 * @var Site_Kit
	 */
	private $site_kit_integration_data;

	/**
	 * The site kit consent management endpoint.
	 *
	 * @var Site_Kit_Consent_Management_Endpoint
	 */
	private $site_kit_consent_management_endpoint;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Workouts_Integration constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager            $admin_asset_manager                  The admin asset manager.
	 * @param Options_Helper                       $options_helper                       The options helper.
	 * @param Woocommerce_Helper                   $woocommerce_helper                   The WooCommerce helper.
	 * @param Elementor_Activated_Conditional      $elementor_conditional                The elementor conditional.
	 * @param Jetpack_Conditional                  $jetpack_conditional                  The Jetpack conditional.
	 * @param Site_Kit                             $site_kit_integration_data            The site kit integration
	 *                                                                                   configuration data.
	 * @param Site_Kit_Consent_Management_Endpoint $site_kit_consent_management_endpoint The site kit consent
	 *                                                                                   management endpoint.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $admin_asset_manager,
		Options_Helper $options_helper,
		Woocommerce_Helper $woocommerce_helper,
		Elementor_Activated_Conditional $elementor_conditional,
		Jetpack_Conditional $jetpack_conditional,
		Site_Kit $site_kit_integration_data,
		Site_Kit_Consent_Management_Endpoint $site_kit_consent_management_endpoint
	) {
		$this->admin_asset_manager                  = $admin_asset_manager;
		$this->options_helper                       = $options_helper;
		$this->woocommerce_helper                   = $woocommerce_helper;
		$this->elementor_conditional                = $elementor_conditional;
		$this->jetpack_conditional                  = $jetpack_conditional;
		$this->site_kit_integration_data            = $site_kit_integration_data;
		$this->site_kit_consent_management_endpoint = $site_kit_consent_management_endpoint;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_submenu_page' ], 10 );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
	}

	/**
	 * Adds the integrations submenu page.
	 *
	 * @param array $submenu_pages The Yoast SEO submenu pages.
	 *
	 * @return array The filtered submenu pages.
	 */
	public function add_submenu_page( $submenu_pages ) {
		$integrations_page = [
			'wpseo_dashboard',
			'',
			\__( 'Integrations', 'wordpress-seo' ),
			'wpseo_manage_options',
			'wpseo_integrations',
			[ $this, 'render_target' ],
		];

		\array_splice( $submenu_pages, 1, 0, [ $integrations_page ] );

		return $submenu_pages;
	}

	/**
	 * Enqueue the integrations app.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Date is not processed or saved.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wpseo_integrations' ) {
			return;
		}

		$this->admin_asset_manager->enqueue_style( 'admin-css' );
		$this->admin_asset_manager->enqueue_style( 'tailwind' );
		$this->admin_asset_manager->enqueue_style( 'monorepo' );

		$this->admin_asset_manager->enqueue_script( 'integrations-page' );

		$woocommerce_seo_file = 'wpseo-woocommerce/wpseo-woocommerce.php';
		$acf_seo_file         = 'acf-content-analysis-for-yoast-seo/yoast-acf-analysis.php';
		$acf_seo_file_github  = 'yoast-acf-analysis/yoast-acf-analysis.php';
		$algolia_file         = 'wp-search-with-algolia/algolia.php';
		$old_algolia_file     = 'search-by-algolia-instant-relevant-results/algolia.php';

		$addon_manager             = new WPSEO_Addon_Manager();
		$woocommerce_seo_installed = $addon_manager->is_installed( WPSEO_Addon_Manager::WOOCOMMERCE_SLUG );

		$woocommerce_seo_active   = \is_plugin_active( $woocommerce_seo_file );
		$woocommerce_active       = $this->woocommerce_helper->is_active();
		$acf_seo_installed        = \file_exists( \WP_PLUGIN_DIR . '/' . $acf_seo_file );
		$acf_seo_github_installed = \file_exists( \WP_PLUGIN_DIR . '/' . $acf_seo_file_github );
		$acf_seo_active           = \is_plugin_active( $acf_seo_file );
		$acf_seo_github_active    = \is_plugin_active( $acf_seo_file_github );
		$acf_active               = \class_exists( 'acf' );
		$algolia_active           = \is_plugin_active( $algolia_file );
		$edd_active               = \class_exists( Easy_Digital_Downloads::class );
		$old_algolia_active       = \is_plugin_active( $old_algolia_file );
		$tec_active               = \class_exists( Events_Schema::class );
		$ssp_active               = \class_exists( PodcastEpisode::class );
		$wp_recipe_maker_active   = \class_exists( WP_Recipe_Maker::class );
		$mastodon_active          = $this->is_mastodon_active();

		$woocommerce_seo_activate_url = \wp_nonce_url(
			\self_admin_url( 'plugins.php?action=activate&plugin=' . $woocommerce_seo_file ),
			'activate-plugin_' . $woocommerce_seo_file
		);

		if ( $acf_seo_installed ) {
			$acf_seo_activate_url = \wp_nonce_url(
				\self_admin_url( 'plugins.php?action=activate&plugin=' . $acf_seo_file ),
				'activate-plugin_' . $acf_seo_file
			);
		}
		else {
			$acf_seo_activate_url = \wp_nonce_url(
				\self_admin_url( 'plugins.php?action=activate&plugin=' . $acf_seo_file_github ),
				'activate-plugin_' . $acf_seo_file_github
			);
		}

		$acf_seo_install_url = \wp_nonce_url(
			\self_admin_url( 'update.php?action=install-plugin&plugin=acf-content-analysis-for-yoast-seo' ),
			'install-plugin_acf-content-analysis-for-yoast-seo'
		);

		$this->admin_asset_manager->localize_script(
			'integrations-page',
			'wpseoIntegrationsData',
			[
				'semrush_integration_active'         => $this->options_helper->get( 'semrush_integration_active', true ),
				'allow_semrush_integration'          => $this->options_helper->get( 'allow_semrush_integration_active', true ),
				'algolia_integration_active'         => $this->options_helper->get( 'algolia_integration_active', false ),
				'allow_algolia_integration'          => $this->options_helper->get( 'allow_algolia_integration_active', true ),
				'wincher_integration_active'         => $this->options_helper->get( 'wincher_integration_active', true ),
				'allow_wincher_integration'          => null,
				'elementor_integration_active'       => $this->elementor_conditional->is_met(),
				'jetpack_integration_active'         => $this->jetpack_conditional->is_met(),
				'woocommerce_seo_installed'          => $woocommerce_seo_installed,
				'woocommerce_seo_active'             => $woocommerce_seo_active,
				'woocommerce_active'                 => $woocommerce_active,
				'woocommerce_seo_activate_url'       => $woocommerce_seo_activate_url,
				'acf_seo_installed'                  => $acf_seo_installed || $acf_seo_github_installed,
				'acf_seo_active'                     => $acf_seo_active || $acf_seo_github_active,
				'acf_active'                         => $acf_active,
				'acf_seo_activate_url'               => $acf_seo_activate_url,
				'acf_seo_install_url'                => $acf_seo_install_url,
				'algolia_active'                     => $algolia_active || $old_algolia_active,
				'edd_integration_active'             => $edd_active,
				'ssp_integration_active'             => $ssp_active,
				'tec_integration_active'             => $tec_active,
				'wp-recipe-maker_integration_active' => $wp_recipe_maker_active,
				'mastodon_active'                    => $mastodon_active,
				'is_multisite'                       => \is_multisite(),
				'plugin_url'                         => \plugins_url( '', \WPSEO_FILE ),
				'site_kit_configuration'             => $this->site_kit_integration_data->to_array(),
				'site_kit_consent_management_url'    => $this->site_kit_consent_management_endpoint->get_url(),
			]
		);
	}

	/**
	 * Renders the target for the React to mount to.
	 *
	 * @return void
	 */
	public function render_target() {
		?>
		<div class="wrap yoast wpseo-admin-page page-wpseo">
			<div class="wp-header-end" style="height: 0; width: 0;"></div>
			<div id="wpseo-integrations"></div>
		</div>
		<?php
	}

	/**
	 * Checks whether the Mastodon profile field has been filled in.
	 *
	 * @return bool
	 */
	private function is_mastodon_active() {
		return \apply_filters( 'wpseo_mastodon_active', false );
	}
}
