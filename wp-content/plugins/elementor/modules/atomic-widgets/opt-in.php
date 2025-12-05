<?php

namespace Elementor\Modules\AtomicWidgets;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Modules\GlobalClasses\Module as GlobalClassesModule;
use Elementor\Modules\NestedElements\Module as NestedElementsModule;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;
use Elementor\Modules\Variables\Module as VariablesModule;
use Elementor\Plugin;

class Opt_In {
	const EXPERIMENT_NAME = 'e_opt_in_v4';

	const OPT_OUT_FEATURES = [
		self::EXPERIMENT_NAME,
		AtomicWidgetsModule::EXPERIMENT_NAME,
		GlobalClassesModule::NAME,
		VariablesModule::EXPERIMENT_NAME,
	];

	const OPT_IN_FEATURES = [
		self::EXPERIMENT_NAME,
		'container',
		NestedElementsModule::EXPERIMENT_NAME,
		AtomicWidgetsModule::EXPERIMENT_NAME,
		GlobalClassesModule::NAME,
		VariablesModule::EXPERIMENT_NAME,
	];

	public function init() {
		$this->register_feature();

		add_action( 'elementor/ajax/register_actions', fn( Ajax $ajax ) => $this->add_ajax_actions( $ajax ) );
	}

	private function register_feature() {
		Plugin::$instance->experiments->add_feature([
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Editor V4', 'elementor' ),
			'description' => esc_html__( 'Enable Editor V4.', 'elementor' ),
			'hidden' => true,
			'default' => Experiments_Manager::STATE_INACTIVE,
			'release_status' => Experiments_Manager::RELEASE_STATUS_ALPHA,
		]);
	}

	private function opt_out_v4() {
		foreach ( self::OPT_OUT_FEATURES as $feature ) {
			$feature_key = Plugin::$instance->experiments->get_feature_option_key( $feature );
			update_option( $feature_key, Experiments_Manager::STATE_INACTIVE );
		}
	}

	private function opt_in_v4() {
		foreach ( self::OPT_IN_FEATURES as $feature ) {
			$feature_key = Plugin::$instance->experiments->get_feature_option_key( $feature );
			update_option( $feature_key, Experiments_Manager::STATE_ACTIVE );
		}
	}

	public function ajax_opt_out_v4() {
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new \Exception( 'Permission denied' );
		}

		$this->opt_out_v4();
	}

	public function ajax_opt_in_v4() {
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new \Exception( 'Permission denied' );
		}

		$this->opt_in_v4();
	}

	private function add_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'editor_v4_opt_in', fn() => $this->ajax_opt_in_v4() );
		$ajax->register_ajax_action( 'editor_v4_opt_out', fn() => $this->ajax_opt_out_v4() );
	}
}
