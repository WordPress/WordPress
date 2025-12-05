<?php
namespace Elementor\App\Modules\KitLibrary;

use Elementor\App\Modules\KitLibrary\Data\Repository;
use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Admin\Menu\Main as MainMenu;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\App\Modules\KitLibrary\Connect\Kit_Library;
use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\App\Modules\KitLibrary\Data\Kits\Controller as Kits_Controller;
use Elementor\App\Modules\KitLibrary\Data\Taxonomies\Controller as Taxonomies_Controller;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Utils as ElementorUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	/**
	 * Get name.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'kit-library';
	}

	private function register_admin_menu( MainMenu $menu ) {
		$menu->add_submenu( [
			'page_title' => esc_html__( 'Website Templates', 'elementor' ),
			'menu_title' => '<span id="e-admin-menu__kit-library">' . esc_html__( 'Website Templates', 'elementor' ) . '</span>',
			'menu_slug' => Plugin::$instance->app->get_base_url() . '&source=wp_db_templates_menu#/kit-library',
			'index' => 40,
		] );
	}

	/**
	 * Register the admin menu the old way.
	 */
	private function register_admin_menu_legacy( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register(
			Plugin::$instance->app->get_base_url() . '&source=wp_db_templates_menu#/kit-library',
			new Kit_Library_Menu_Item()
		);
	}

	private function set_kit_library_settings() {
		if ( ! Plugin::$instance->common ) {
			return;
		}

		/** @var ConnectModule $connect */
		$connect = Plugin::$instance->common->get_component( 'connect' );

		/** @var Kit_Library $kit_library */
		$kit_library = $connect->get_app( 'kit-library' );

		Plugin::$instance->app->set_settings( 'kit-library', [
			'has_access_to_module' => current_user_can( 'manage_options' ),
			'subscription_plans' => $this->apply_filter_subscription_plans( $connect->get_subscription_plans( 'kit-library' ) ),
			'is_pro' => false,
			'is_library_connected' => $kit_library->is_connected(),
			'library_connect_url'  => $kit_library->get_admin_url( 'authorize', [
				'utm_source' => 'kit-library',
				'utm_medium' => 'wp-dash',
				'utm_campaign' => 'library-connect',
				'utm_term' => '%%page%%', // Will be replaced in the frontend.
			] ),
			'access_level' => ConnectModule::ACCESS_LEVEL_CORE,
			'access_tier' => ConnectModule::ACCESS_TIER_FREE,
			'plan_type' => ConnectModule::ACCESS_TIER_FREE,
			'app_url' => Plugin::$instance->app->get_base_url() . '#/' . $this->get_name(),
		] );
	}

	private function apply_filter_subscription_plans( array $subscription_plans ): array {
		foreach ( $subscription_plans as $key => $plan ) {
			if ( null === $plan['promotion_url'] ) {
				continue;
			}

			$subscription_plans[ $key ] = Filtered_Promotions_Manager::get_filtered_promotion_data(
				$plan,
				'elementor/kit_library/' . $key . '/promotion',
				'promotion_url'
			);
		}

		return $subscription_plans;
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		Plugin::$instance->data_manager_v2->register_controller( new Kits_Controller() );
		Plugin::$instance->data_manager_v2->register_controller( new Taxonomies_Controller() );

		$this->register_actions();

		do_action( 'elementor/kit_library/registered', $this );
	}

	public function register_actions() {
		// Assigning this action here since the repository is being loaded by demand.
		add_action( 'elementor/experiments/feature-state-change/container', [ Repository::class, 'clear_cache' ], 10, 0 );

		add_action( 'elementor/admin/menu/register', function( Admin_Menu_Manager $admin_menu ) {
			$this->register_admin_menu_legacy( $admin_menu );
		}, Source_Local::ADMIN_MENU_PRIORITY + 30 );

		add_action( 'elementor/connect/apps/register', function ( ConnectModule $connect_module ) {
			$connect_module->register_app( 'kit-library', Kit_Library::get_class_name() );
		} );

		add_action( 'elementor/init', function () {
			$this->set_kit_library_settings();
		}, 12 /** After the initiation of the connect kit library */ );

		add_action( 'template_redirect', [ $this, 'handle_kit_screenshot_generation' ] );
	}

	public function handle_kit_screenshot_generation() {
		$is_kit_preview = ElementorUtils::get_super_global_value( $_GET, 'kit_thumbnail' );
		$nonce = ElementorUtils::get_super_global_value( $_GET, 'nonce' );

		if ( $is_kit_preview ) {
			if ( ! wp_verify_nonce( $nonce, 'kit_thumbnail' ) ) {
				wp_die( esc_html__( 'Not Authorized', 'elementor' ), esc_html__( 'Error', 'elementor' ), 403 );
			}

			$suffix = ( ElementorUtils::is_script_debug() || ElementorUtils::is_elementor_tests() ) ? '' : '.min';

			show_admin_bar( false );

			wp_enqueue_script(
				'dom-to-image',
				ELEMENTOR_ASSETS_URL . "/lib/dom-to-image/js/dom-to-image{$suffix}.js",
				[],
				'2.6.0',
				true
			);

			wp_enqueue_script(
				'html2canvas',
				ELEMENTOR_ASSETS_URL . "/lib/html2canvas/js/html2canvas{$suffix}.js",
				[],
				'1.4.1',
				true
			);

			wp_enqueue_script(
				'cloud-library-screenshot',
				ELEMENTOR_ASSETS_URL . "/js/cloud-library-screenshot{$suffix}.js",
				[ 'dom-to-image', 'html2canvas', 'elementor-common', 'elementor-common-modules' ],
				ELEMENTOR_VERSION,
				true
			);

			$config = [
				'home_url' => home_url(),
				'kit_id' => uniqid(),
				'selector' => 'body',
			];

			wp_add_inline_script( 'cloud-library-screenshot', 'var ElementorScreenshotConfig = ' . wp_json_encode( $config ) . ';' );
		}
	}
}
