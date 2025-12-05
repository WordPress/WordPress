<?php
namespace Elementor\Modules\AdminTopBar;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Plugin;
use Elementor\Core\Base\App as BaseApp;
use Elementor\Core\Experiments\Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseApp {

	/**
	 * @return bool
	 */
	public static function is_active() {
		return is_admin();
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return 'admin-top-bar';
	}

	private function render_admin_top_bar() {
		?>
		<div id="e-admin-top-bar-root">
		</div>
		<?php
	}

	/**
	 * Enqueue admin scripts
	 */
	private function enqueue_scripts() {
		wp_enqueue_style( 'elementor-admin-top-bar-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', [], ELEMENTOR_VERSION );

		wp_enqueue_style( 'elementor-admin-top-bar', $this->get_css_assets_url( 'admin-top-bar' ), [], ELEMENTOR_VERSION );

		/**
		 * Before admin top bar enqueue scripts.
		 *
		 * Fires before Elementor admin top bar scripts are enqueued.
		 *
		 * @since 3.19.0
		 */
		do_action( 'elementor/admin_top_bar/before_enqueue_scripts', $this );

		wp_enqueue_script( 'elementor-admin-top-bar', $this->get_js_assets_url( 'admin-top-bar' ), [
			'elementor-common',
			'react',
			'react-dom',
			'tipsy',
		], ELEMENTOR_VERSION, true );

		wp_set_script_translations( 'elementor-admin-top-bar', 'elementor' );

		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script( 'tipsy', ELEMENTOR_ASSETS_URL . 'lib/tipsy/tipsy' . $min_suffix . '.js', [
			'jquery',
		], '1.0.0', true );

		$this->print_config();
	}

	private function add_frontend_settings() {
		$settings = [];
		$settings['is_administrator'] = current_user_can( 'manage_options' );

		// TODO: Find a better way to add apps page url to the admin top bar.
		$settings['apps_url'] = admin_url( 'admin.php?page=elementor-apps' );
		$settings['promotion'] = [
			'text' => __( 'Upgrade Now', 'elementor' ),
			'url' => 'https://go.elementor.com/wp-dash-admin-top-bar-upgrade/',
		];

		$settings['promotion'] = Filtered_Promotions_Manager::get_filtered_promotion_data(
			$settings['promotion'],
			'elementor/admin_top_bar/go_pro_promotion',
			'url'
		);

		$current_screen = get_current_screen();

		/** @var \Elementor\Core\Common\Modules\Connect\Apps\Library $library */
		$library = Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' );
		if ( $library ) {
			$settings = array_merge( $settings, [
				'is_user_connected' => $library->is_connected(),
				'connect_url' => $library->get_admin_url( 'authorize', [
					'utm_source' => 'top-bar',
					'utm_medium' => 'wp-dash',
					'utm_campaign' => 'connect-account',
					'utm_content' => $current_screen->id,
					'source' => 'generic',
				] ),
			] );
		}

		$this->set_settings( $settings );

		do_action( 'elementor/admin-top-bar/init', $this );
	}

	private function is_top_bar_active() {
		$current_screen = get_current_screen();

		if ( ! $current_screen ) {
			return false;
		}

		$is_elementor_page = strpos( $current_screen->id ?? '', 'elementor' ) !== false;
		$is_elementor_post_type_page = strpos( $current_screen->post_type ?? '', 'elementor' ) !== false;

		return apply_filters(
			'elementor/admin-top-bar/is-active',
			$is_elementor_page || $is_elementor_post_type_page,
			$current_screen
		);
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'current_screen', function () {
			if ( ! $this->is_top_bar_active() ) {
				return;
			}

			$this->add_frontend_settings();

			add_action( 'in_admin_header', function () {
				$this->render_admin_top_bar();
			} );

			add_action( 'admin_enqueue_scripts', function () {
				$this->enqueue_scripts();
			} );
		} );
	}
}
