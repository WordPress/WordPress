<?php

namespace Elementor\Modules\Promotions;

use Elementor\Api;
use Elementor\Controls_Manager;
use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Base\Module as Base_Module;
use Elementor\Modules\Promotions\AdminMenuItems\Custom_Code_Promotion_Item;
use Elementor\Modules\Promotions\AdminMenuItems\Custom_Fonts_Promotion_Item;
use Elementor\Modules\Promotions\AdminMenuItems\Custom_Icons_Promotion_Item;
use Elementor\Modules\Promotions\AdminMenuItems\Form_Submissions_Promotion_Item;
use Elementor\Modules\Promotions\AdminMenuItems\Go_Pro_Promotion_Item;
use Elementor\Modules\Promotions\AdminMenuItems\Popups_Promotion_Item;
use Elementor\Modules\Promotions\Pointers\Birthday;
use Elementor\Modules\Promotions\Pointers\Black_Friday;
use Elementor\Widgets_Manager;
use Elementor\Utils;
use Elementor\Includes\EditorAssetsAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Base_Module {

	const ADMIN_MENU_PRIORITY = 100;

	const ADMIN_MENU_PROMOTIONS_PRIORITY = 120;

	public static function is_active() {
		return ! Utils::has_pro();
	}

	public function get_name() {
		return 'promotions';
	}

	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', function () {
			$this->handle_external_redirects();
		} );

		add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
			$this->register_menu_items( $admin_menu );
		}, static::ADMIN_MENU_PRIORITY );

		add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
			$this->register_promotion_menu_item( $admin_menu );
		}, static::ADMIN_MENU_PROMOTIONS_PRIORITY );

		add_action( 'elementor/widgets/register', function( Widgets_Manager $manager ) {
			foreach ( Api::get_promotion_widgets() as $widget_data ) {
				$manager->register( new Widgets\Pro_Widget_Promotion( [], [
					'widget_name' => $widget_data['name'],
					'widget_title' => $widget_data['title'],
				] ) );
			}
		} );

		if ( Birthday::should_display_notice() ) {
			new Birthday();
		}

		if ( Black_Friday::should_display_notice() ) {
			new Black_Friday();
		}

		if ( Utils::has_pro() ) {
			return;
		}

		add_action( 'elementor/controls/register', function ( Controls_Manager $controls_manager ) {
			$controls_manager->register( new Controls\Promotion_Control() );
		} );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_react_data' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_v4_alphachip' ] );
	}

	private function handle_external_redirects() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}

		if ( 'go_elementor_pro' === $_GET['page'] ) {
			wp_redirect( Go_Pro_Promotion_Item::get_url() );
			die;
		}
	}

	private function register_menu_items( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register( 'e-form-submissions', new Form_Submissions_Promotion_Item() );
		$admin_menu->register( 'elementor_custom_fonts', new Custom_Fonts_Promotion_Item() );
		$admin_menu->register( 'elementor_custom_icons', new Custom_Icons_Promotion_Item() );
		$admin_menu->register( 'elementor_custom_code', new Custom_Code_Promotion_Item() );
		$admin_menu->register( 'popup_templates', new Popups_Promotion_Item() );
	}

	private function register_promotion_menu_item( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register( 'go_elementor_pro', new Go_Pro_Promotion_Item() );
	}

	public function enqueue_react_data(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'e-react-promotions',
			ELEMENTOR_ASSETS_URL . 'js/e-react-promotions' . $min_suffix . '.js',
			[
				'react',
				'react-dom',
				'backbone-marionette',
				'elementor-editor-modules',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'e-react-promotions', 'elementor' );

		wp_localize_script(
			'e-react-promotions',
			'elementorPromotionsData',
			$this->get_app_js_config()
		);
	}

	public function enqueue_editor_v4_alphachip(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'editor-v4-opt-in-alphachip',
			ELEMENTOR_ASSETS_URL . 'js/editor-v4-opt-in-alphachip' . $min_suffix . '.js',
			[
				'react',
				'react-dom',
				'elementor-common',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);
	}

	private function get_app_js_config(): array {
		$editor_assets_api = new EditorAssetsAPI( $this->get_api_config() );
		$promotion_data = new PromotionData( $editor_assets_api );

		return $promotion_data->get_promotion_data();
	}

	private function get_api_config(): array {
		return [
			EditorAssetsAPI::ASSETS_DATA_URL => 'https://assets.elementor.com/free-to-pro-upsell/v1/free-to-pro-upsell.json',
			EditorAssetsAPI::ASSETS_DATA_TRANSIENT_KEY => '_elementor_free_to_pro_upsell',
			EditorAssetsAPI::ASSETS_DATA_KEY => 'free-to-pro-upsell',
		];
	}
}
