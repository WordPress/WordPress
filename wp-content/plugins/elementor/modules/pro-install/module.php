<?php
namespace Elementor\Modules\ProInstall;

use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	public function get_name() {
		return 'pro-install';
	}

	public static function is_active() {
		return ! Utils::has_pro() && current_user_can( 'manage_options' );
	}

	public function __construct() {
		parent::__construct();

		add_action( 'admin_post_elementor_do_pro_install', [ $this, 'admin_post_elementor_do_pro_install' ] );

		add_action( 'elementor/connect/apps/register', function ( ConnectModule $connect_module ) {
			$connect_module->register_app( 'pro-install', Connect::get_class_name() );
		} );

		add_action( 'elementor/admin/menu/register', function( Admin_Menu_Manager $admin_menu ) {
			$admin_menu->register(
				'elementor-connect-account',
				new Pro_Install_Menu_Item(
					$this->get_connect_app(),
					$this->get_pro_install_page_assets(),
				)
			);
		}, 116 );
	}

	private function get_connect_app(): Connect {
		return Plugin::$instance->common->get_component( 'connect' )->get_app( 'pro-install' );
	}

	public function admin_post_elementor_do_pro_install() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'elementor' ) );
		}

		check_admin_referer( 'elementor_do_pro_install' );

		$app = $this->get_connect_app();
		$download_link = $app->get_download_link();

		if ( empty( $download_link ) ) {
			wp_die( esc_html__( 'There are no available subscriptions at the moment.', 'elementor' ) );
		}

		$plugin_installer = new Plugin_Installer( 'elementor-pro', $download_link );
		$response = $plugin_installer->install();

		if ( is_wp_error( $response ) ) {
			wp_die( esc_html( $response->get_error_message() ) );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=elementor-license' ) );
	}

	private function get_pro_install_page_assets(): array {
		return [
			'elementor-pro-install-events',
			$this->get_js_assets_url( 'pro-install-events' ),
			[ 'elementor-common' ],
			ELEMENTOR_VERSION,
			true,
		];
	}
}
