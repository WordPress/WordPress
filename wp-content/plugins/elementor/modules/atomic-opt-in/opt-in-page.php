<?php

namespace Elementor\Modules\AtomicOptIn;

use Elementor\Plugin;
use Elementor\Settings;
use Elementor\User;
use Elementor\Utils;

class OptInPage {
	private Module $module;

	public function __construct( Module $module ) {
		$this->module = $module;
	}

	public function init() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->register_assets();
		$this->add_settings_tab();
	}

	private function register_assets() {
		$page_id = Settings::PAGE_ID;

		add_action( "elementor/admin/after_create_settings/{$page_id}", [ $this, 'enqueue_scripts' ] );
		add_action( "elementor/admin/after_create_settings/{$page_id}", [ $this, 'enqueue_styles' ] );
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			Module::MODULE_NAME,
			$this->module->get_opt_in_css_assets_url( 'modules/editor-v4-opt-in/opt-in' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	public function enqueue_scripts() {
		$min_suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			Module::MODULE_NAME,
			ELEMENTOR_ASSETS_URL . 'js/editor-v4-opt-in' . $min_suffix . '.js',
			[
				'react',
				'react-dom',
				'elementor-common',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_localize_script(
			Module::MODULE_NAME,
			'elementorSettingsEditor4OptIn',
			$this->prepare_data()
		);

		wp_set_script_translations( Module::MODULE_NAME, 'elementor' );
	}

	private function prepare_data() {
		$create_new_post_type = User::is_current_user_can_edit_post_type( 'page' ) ? 'page' : 'post';

		return [
			'features' => [
				'editor_v4' => $this->module->is_atomic_experiment_active(),
			],
			'urls' => [
				'start_building' => esc_url( Plugin::$instance->documents->get_create_new_post_url( $create_new_post_type ) ),
			],
		];
	}

	private function add_settings_tab() {
		$page_id = Settings::PAGE_ID;

		add_action( "elementor/admin/after_create_settings/{$page_id}", function( Settings $settings ) {
			$this->add_new_tab_to( $settings );
		}, 11 );
	}

	private function add_new_tab_to( Settings $settings ) {
		$settings->add_tab( Module::MODULE_NAME, [
			'label' => esc_html__( 'Editor V4', 'elementor' ),
			'sections' => [
				'opt-in' => [
					'callback' => function() {
						echo '<div id="page-editor-v4-opt-in"></div>';
					},
					'fields' => [],
				],
			],
		] );
	}
}
