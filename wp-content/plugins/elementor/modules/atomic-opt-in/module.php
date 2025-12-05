<?php

namespace Elementor\Modules\AtomicOptIn;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Modules\AtomicWidgets\Opt_In as Atomic_Widgets_Opt_In;
use Elementor\Plugin;

class Module extends BaseModule {
	const EXPERIMENT_NAME = 'e_opt_in_v4_page';
	const MODULE_NAME = 'editor-v4-opt-in';
	const WELCOME_POPOVER_DISPLAYED_OPTION = '_e_welcome_popover_displayed';

	public function get_name() {
		return 'atomic-opt-in';
	}

	public static function get_experimental_data(): array {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Editor v4 (Opt In Page)', 'elementor' ),
			'description' => esc_html__( 'Enable the settings Opt In page', 'elementor' ),
			'hidden' => true,
			'default' => Experiments_Manager::STATE_ACTIVE,
			'release_status' => Experiments_Manager::RELEASE_STATUS_ALPHA,
		];
	}

	public function get_opt_in_css_assets_url( string $path ) {
		return $this->get_css_assets_url( $path );
	}

	public function __construct() {
		( new PanelChip() )->init();

		if ( ! Plugin::$instance->experiments->is_feature_active( self::EXPERIMENT_NAME ) ) {
			return;
		}

		( new Atomic_Widgets_Opt_In() )->init();
		( new OptInPage( $this ) )->init();

		if ( ! $this->is_atomic_experiment_active() ) {
			return;
		}

		( new WelcomeScreen() )->init();
	}

	public function is_atomic_experiment_active(): bool {
		return Plugin::$instance->experiments->is_feature_active( Atomic_Widgets_Opt_In::EXPERIMENT_NAME );
	}
}
