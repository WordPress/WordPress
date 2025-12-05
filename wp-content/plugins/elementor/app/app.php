<?php
namespace Elementor\App;

use Elementor\App\AdminMenuItems\Theme_Builder_Menu_Item;
use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Experiments\Manager as ExperimentsManager;
use Elementor\Modules\WebCli\Module as WebCLIModule;
use Elementor\Core\Base\App as BaseApp;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\User;
use Elementor\Utils;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Core\Utils\Assets_Config_Provider;
use Elementor\Core\Utils\Collection;

use Elementor\App\Modules\ImportExport\Module as ImportExportModule;
use Elementor\App\Modules\KitLibrary\Module as KitLibraryModule;
use Elementor\App\Modules\ImportExportCustomization\Module as ImportExportCustomizationModule;
use Elementor\App\Modules\SiteEditor\Module as SiteEditorModule;
use Elementor\App\Modules\Onboarding\Module as OnboardingModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class App extends BaseApp {

	const PAGE_ID = 'elementor-app';

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'app';
	}

	public function get_base_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_ID . '&ver=' . ELEMENTOR_VERSION );
	}

	private function register_admin_menu( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register( static::PAGE_ID, new Theme_Builder_Menu_Item() );
	}

	public function fix_submenu( $menu ) {
		global $submenu;

		if ( is_multisite() && is_network_admin() ) {
			return $menu;
		}

		// Non admin role / custom wp menu.
		if ( empty( $submenu[ Source_Local::ADMIN_MENU_SLUG ] ) ) {
			return $menu;
		}

		// Hack to add a link to sub menu.
		foreach ( $submenu[ Source_Local::ADMIN_MENU_SLUG ] as &$item ) {
			if ( self::PAGE_ID === $item[2] ) {
				$item[2] = $this->get_settings( 'menu_url' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$item[4] = 'elementor-app-link'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		return $menu;
	}

	public function is_current() {
		return ( ! empty( $_GET['page'] ) && self::PAGE_ID === $_GET['page'] );
	}

	public function admin_init() {
		do_action( 'elementor/app/init', $this );

		// Add the introduction and user settings only when it is needed (when loading the app and not in the editor or admin pages)
		$this->set_settings( 'user', [
			'introduction' => (object) User::get_introduction_meta(),
			'is_administrator' => current_user_can( 'manage_options' ),
			'restrictions' => Plugin::$instance->role_manager->get_user_restrictions_array(),
		] );

		$this->enqueue_assets();

		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// Setup default heartbeat options
		// TODO: Enable heartbeat.
		add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
		} );

		$this->render();
		die;
	}

	protected function get_init_settings() {
		$referer = wp_get_referer();

		return [
			'menu_url' => $this->get_base_url() . '#site-editor/promotion',
			'assets_url' => ELEMENTOR_ASSETS_URL,
			'pages_url' => admin_url( 'edit.php?post_type=page' ),
			'return_url' => $referer ? $referer : admin_url(),
			'hasPro' => Utils::has_pro(),
			'admin_url' => admin_url(),
			'login_url' => wp_login_url(),
			'base_url' => $this->get_base_url(),
			'home_url' => home_url(),
			'promotion' => Filtered_Promotions_Manager::get_filtered_promotion_data(
				[ 'upgrade_url' => 'https://go.elementor.com/go-pro-theme-builder/' ],
				'elementor/site-editor/promotion',
				'upgrade_url'
			),
		];
	}

	private function render() {
		require __DIR__ . '/view.php';
	}

	/**
	 * Get Elementor editor theme color preference.
	 *
	 * Retrieve the user theme color preference as defined by editor preferences manager.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return string Preferred editor theme.
	 */
	private function get_elementor_ui_theme_preference() {
		$editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' );

		return $editor_preferences->get_model()->get_settings( 'ui_theme' );
	}

	/**
	 * Enqueue dark theme detection script.
	 *
	 * Enqueues an inline script that detects user-agent settings for dark mode and adds a complimentary class to the body tag.
	 *
	 * @since 3.0.0
	 * @access private
	 */
	private function enqueue_dark_theme_detection_script() {
		if ( 'auto' === $this->get_elementor_ui_theme_preference() ) {
			wp_add_inline_script( 'elementor-app',
				'if ( window.matchMedia && window.matchMedia( `(prefers-color-scheme: dark)` ).matches )
							{ document.body.classList.add( `eps-theme-dark` ); }' );
		}
	}

	private function register_packages() {
		$assets_config_provider = ( new Assets_Config_Provider() )
			->set_path_resolver( function ( $name ) {
				return ELEMENTOR_ASSETS_PATH . "js/packages/{$name}/{$name}.asset.php";
			} );

		Collection::make( [ 'ui', 'icons' ] )
			->each( function( $package ) use ( $assets_config_provider ) {
				$suffix = Utils::is_script_debug() ? '' : '.min';
				$config = $assets_config_provider->load( $package )->get( $package );

				if ( ! $config ) {
					return;
				}

				wp_register_script(
					$config['handle'],
					ELEMENTOR_ASSETS_URL . "js/packages/{$package}/{$package}{$suffix}.js",
					$config['deps'],
					ELEMENTOR_VERSION,
					true
				);
			} );
	}

	private function enqueue_assets() {
		Plugin::$instance->init_common();

		$this->register_packages();

		/** @var WebCLIModule $web_cli */
		$web_cli = Plugin::$instance->modules_manager->get_modules( 'web-cli' );
		$web_cli->register_scripts();

		Plugin::$instance->common->register_scripts();

		wp_register_style(
			'select2',
			$this->get_css_assets_url( 'e-select2', 'assets/lib/e-select2/css/' ),
			[],
			'4.0.6-rc.1'
		);

		Plugin::$instance->common->register_styles();

		wp_register_style(
			'select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.css',
			[],
			'4.0.6-rc.1'
		);

		wp_enqueue_style(
			'elementor-app',
			$this->get_css_assets_url( 'app', null, 'default', true ),
			[
				'select2',
				'elementor-icons',
				'elementor-common',
				'select2',
			],
			ELEMENTOR_VERSION
		);

		wp_enqueue_script(
			'elementor-app-packages',
			$this->get_js_assets_url( 'app-packages' ),
			[
				'wp-i18n',
				'react',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'select2',
			$this->get_js_assets_url( 'e-select2.full', 'assets/lib/e-select2/js/' ),
			[
				'jquery',
			],
			'4.0.6-rc.1',
			true
		);

		wp_enqueue_script(
			'elementor-app',
			$this->get_js_assets_url( 'app' ),
			[
				'wp-url',
				'wp-i18n',
				'elementor-v2-ui',
				'elementor-v2-icons',
				'react',
				'react-dom',
				'select2',
			],
			ELEMENTOR_VERSION,
			true
		);

		$this->enqueue_dark_theme_detection_script();

		wp_set_script_translations( 'elementor-app-packages', 'elementor' );
		wp_set_script_translations( 'elementor-app', 'elementor' );

		$this->print_config();
	}

	public function enqueue_app_loader() {
		wp_enqueue_script(
			'elementor-app-loader',
			$this->get_js_assets_url( 'app-loader' ),
			[
				'elementor-common',
			],
			ELEMENTOR_VERSION,
			true
		);

		$this->print_config( 'elementor-app-loader' );
	}

	private function register_import_export_customization_experiment() {
		Plugin::$instance->experiments->add_feature( [
			'name' => 'import-export-customization',
			'title' => esc_html__( 'Import/Export Customization', 'elementor' ),
			'description' => esc_html__( 'Enhanced import/export for website templates. Selectively include site content, templates, and settings with advanced granular control.', 'elementor' ),
			'release_status' => ExperimentsManager::RELEASE_STATUS_BETA,
			'default' => ExperimentsManager::STATE_ACTIVE,
		] );
	}

	public function __construct() {
		$this->register_import_export_customization_experiment();

		$this->add_component( 'site-editor', new SiteEditorModule() );

		if ( current_user_can( 'manage_options' ) || Utils::is_wp_cli() ) {
			$this->add_component( 'import-export', new ImportExportModule() );

			if ( Plugin::$instance->experiments->is_feature_active( 'import-export-customization' ) ) {
				$this->add_component( 'import-export-customization', new ImportExportCustomizationModule() );
			}

			// Kit library is depended on import-export
			$this->add_component( 'kit-library', new KitLibraryModule() );
		}

		$this->add_component( 'onboarding', new OnboardingModule() );

		add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
			$this->register_admin_menu( $admin_menu );
		}, Source_Local::ADMIN_MENU_PRIORITY + 10 );

		// Happens after WP plugin page validation.
		add_filter( 'add_menu_classes', [ $this, 'fix_submenu' ] );

		if ( $this->is_current() ) {
			add_action( 'admin_init', [ $this, 'admin_init' ], 0 );
		} else {
			add_action( 'elementor/common/after_register_scripts', [ $this, 'enqueue_app_loader' ] );
		}
	}
}
