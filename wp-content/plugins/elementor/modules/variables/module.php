<?php

namespace Elementor\Modules\Variables;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as ExperimentsManager;
use Elementor\Modules\AtomicWidgets\Module as AtomicWidgetsModule;
use Elementor\Modules\Variables\Classes\Variable_Types_Registry;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	const MODULE_NAME = 'e-variables';
	const EXPERIMENT_NAME = 'e_variables';
	const EXPERIMENT_MANAGER_NAME = 'e_variables_manager';

	private Variable_Types_Registry $variable_types_registry;

	public function get_name() {
		return self::MODULE_NAME;
	}

	public static function get_experimental_data(): array {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Variables', 'elementor' ),
			'description' => esc_html__( 'Enable variables. (For this feature to work - Atomic Widgets must be active)', 'elementor' ),
			'hidden' => true,
			'default' => ExperimentsManager::STATE_ACTIVE,
			'release_status' => ExperimentsManager::RELEASE_STATUS_ALPHA,
		];
	}

	private function hooks() {
		return new Hooks();
	}

	public function __construct() {
		parent::__construct();

		if ( ! $this->is_experiment_active() ) {
			return;
		}
		$this->register_features();

		$this->hooks()->register();

		add_action( 'init', [ $this, 'init_variable_types_registry' ] );
	}

	private function register_features() {
		Plugin::$instance->experiments->add_feature([
			'name' => self::EXPERIMENT_MANAGER_NAME,
			'title' => esc_html__( 'Variables Manager', 'elementor' ),
			'description' => esc_html__( 'Enable variables manager. (For this feature to work - Variables must be active)', 'elementor' ),
			'hidden' => true,
			'default' => ExperimentsManager::STATE_ACTIVE,
			'release_status' => ExperimentsManager::RELEASE_STATUS_ALPHA,
		]);
	}

	private function is_experiment_active(): bool {
		return Plugin::$instance->experiments->is_feature_active( self::EXPERIMENT_NAME )
			&& Plugin::$instance->experiments->is_feature_active( AtomicWidgetsModule::EXPERIMENT_NAME );
	}

	public function init_variable_types_registry(): void {
		$this->variable_types_registry = new Variable_Types_Registry();

		do_action( 'elementor/variables/register', $this->variable_types_registry );
	}


	public function get_variable_types_registry(): Variable_Types_Registry {
		return $this->variable_types_registry;
	}
}
