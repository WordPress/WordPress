<?php
namespace Elementor\Modules\Apps;

use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const PAGE_ID = 'elementor-apps';

	public function get_name() {
		return 'apps';
	}

	public function __construct() {
		parent::__construct();

		Admin_Pointer::add_hooks();

		add_action( 'elementor/admin/menu/register', function( Admin_Menu_Manager $admin_menu ) {
			$admin_menu->register( static::PAGE_ID, new Admin_Menu_Apps() );
		}, 115 );

		add_action( 'elementor/admin/menu/after_register', function ( Admin_Menu_Manager $admin_menu, array $hooks ) {
			if ( ! empty( $hooks[ static::PAGE_ID ] ) ) {
				add_action( "admin_print_scripts-{$hooks[ static::PAGE_ID ]}", [ $this, 'enqueue_assets' ] );
			}
		}, 10, 2 );

		add_filter( 'elementor/finder/categories', function( array $categories ) {
			$categories['site']['items']['apps'] = [
				'title' => esc_html__( 'Add-ons', 'elementor' ),
				'url' => admin_url( 'admin.php?page=' . static::PAGE_ID ),
				'icon' => 'apps',
				'keywords' => [ 'apps', 'addon', 'plugin', 'extension', 'integration' ],
			];

			return $categories;
		} );

		// Add the Elementor Apps link to the plugin install action links.
		add_filter( 'install_plugins_tabs', [ $this, 'add_elementor_plugin_install_action_link' ] );
		add_action( 'install_plugins_pre_elementor', [ $this, 'maybe_open_elementor_tab' ] );
		add_action( 'admin_print_styles-plugin-install.php', [ $this, 'add_plugins_page_styles' ] );
	}

	public function enqueue_assets() {
		add_filter( 'admin_body_class', [ $this, 'body_status_classes' ] );

		wp_enqueue_style(
			'elementor-apps',
			$this->get_css_assets_url( 'modules/apps/admin' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	public function body_status_classes( $admin_body_classes ) {
		$admin_body_classes .= ' elementor-apps-page';

		return $admin_body_classes;
	}

	public function add_elementor_plugin_install_action_link( $tabs ) {
		$tabs['elementor'] = esc_html__( 'For Elementor', 'elementor' );

		return $tabs;
	}

	public function maybe_open_elementor_tab() {
		if ( ! isset( $_GET['tab'] ) || 'elementor' !== $_GET['tab'] ) {
			return;
		}

		$elementor_url = add_query_arg( [
			'page' => static::PAGE_ID,
			'tab' => 'elementor',
			'ref' => 'plugins',
		], admin_url( 'admin.php' ) );

		wp_safe_redirect( $elementor_url );
		exit;
	}

	public function add_plugins_page_styles() {
		?>
		<style>
			.plugin-install-elementor > a::after {
				content: "";
				display: inline-block;
				background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.33321 3H12.9999V7.66667H11.9999V4.70711L8.02009 8.68689L7.31299 7.97978L11.2928 4H8.33321V3Z' fill='%23646970'/%3E%3Cpath d='M6.33333 4.1665H4.33333C3.8731 4.1665 3.5 4.5396 3.5 4.99984V11.6665C3.5 12.1267 3.8731 12.4998 4.33333 12.4998H11C11.4602 12.4998 11.8333 12.1267 11.8333 11.6665V9.6665' stroke='%23646970'/%3E%3C/svg%3E%0A");
				width: 16px;
				height: 16px;
				background-repeat: no-repeat;
				vertical-align: text-top;
				margin-left: 2px;
			}
			.plugin-install-elementor:hover > a::after {
				background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M8.33321 3H12.9999V7.66667H11.9999V4.70711L8.02009 8.68689L7.31299 7.97978L11.2928 4H8.33321V3Z' fill='%23135E96'/%3E%3Cpath d='M6.33333 4.1665H4.33333C3.8731 4.1665 3.5 4.5396 3.5 4.99984V11.6665C3.5 12.1267 3.8731 12.4998 4.33333 12.4998H11C11.4602 12.4998 11.8333 12.1267 11.8333 11.6665V9.6665' stroke='%23135E96'/%3E%3C/svg%3E%0A");
			}
		</style>
		<?php
	}
}
