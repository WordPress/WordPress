<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Modules\AtomicWidgets\Module as Atomic_Widgets_Module;
use Elementor\Modules\GlobalClasses\Database\Global_Classes_Database_Updater;
use Elementor\Modules\GlobalClasses\ImportExport\Import_Export;
use Elementor\Modules\GlobalClasses\ImportExportCustomization\Import_Export_Customization;
use Elementor\Modules\GlobalClasses\Usage\Global_Classes_Usage;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	const NAME = 'e_classes';
	const ENFORCE_CAPABILITIES_EXPERIMENT = 'global_classes_should_enforce_capabilities';

	// TODO: Add global classes package
	const PACKAGES = [
		'editor-global-classes',
	];

	public function get_name() {
		return 'global-classes';
	}

	public function __construct() {
		parent::__construct();

		$this->register_features();

		$is_feature_active = Plugin::$instance->experiments->is_feature_active( self::NAME );
		$is_atomic_widgets_active = Plugin::$instance->experiments->is_feature_active( Atomic_Widgets_Module::EXPERIMENT_NAME );

		// TODO: When the `e_atomic_elements` feature is not hidden, add it as a dependency
		if ( $is_feature_active && $is_atomic_widgets_active ) {
			add_filter( 'elementor/editor/v2/packages', fn( $packages ) => $this->add_packages( $packages ) );

			( new Global_Classes_Usage() )->register_hooks();
			( new Global_Classes_REST_API() )->register_hooks();
			( new Atomic_Global_Styles() )->register_hooks();
			( new Global_Classes_Cleanup() )->register_hooks();
			( new Import_Export() )->register_hooks();
			( new Import_Export_Customization() )->register_hooks();
			( new Global_Classes_Database_Updater() )->register();
		}
	}

	private function register_features() {
		Plugin::$instance->experiments->add_feature([
			'name' => self::NAME,
			'title' => esc_html__( 'Global Classes', 'elementor' ),
			'description' => esc_html__( 'Enable global CSS classes.', 'elementor' ),
			'hidden' => true,
			'default' => Experiments_Manager::STATE_INACTIVE,
			'release_status' => Experiments_Manager::RELEASE_STATUS_ALPHA,
		]);

		Plugin::$instance->experiments->add_feature([
			'name' => self::ENFORCE_CAPABILITIES_EXPERIMENT,
			'title' => esc_html__( 'Enforce global classes capabilities', 'elementor' ),
			'description' => esc_html__( 'Enforce global classes capabilities.', 'elementor' ),
			'hidden' => true,
			'default' => Experiments_Manager::STATE_ACTIVE,
			'release_status' => Experiments_Manager::RELEASE_STATUS_DEV,
		]);
	}

	private function add_packages( $packages ) {
		return array_merge( $packages, self::PACKAGES );
	}
}
