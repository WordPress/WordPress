<?php
namespace Elementor\Modules\KitElementsDefaults;

use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Modules\KitElementsDefaults\Data\Controller;
use Elementor\Plugin;
use Elementor\Modules\KitElementsDefaults\ImportExport\Import_Export;
use Elementor\Modules\KitElementsDefaults\ImportExportCustomization\Import_Export_Customization;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const META_KEY = '_elementor_elements_default_values';

	public function get_name() {
		return 'kit-elements-defaults';
	}

	private function enqueue_scripts() {
		wp_enqueue_script(
			'elementor-kit-elements-defaults-editor',
			$this->get_js_assets_url( 'kit-elements-defaults-editor' ),
			[
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
				'wp-i18n',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-kit-elements-defaults-editor', 'elementor' );
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/editor/before_enqueue_scripts', function () {
			$this->enqueue_scripts();
		} );

		Plugin::$instance->data_manager_v2->register_controller( new Controller() );

		( new Usage() )->register();

		if ( is_admin() ) {
			( new Import_Export() )->register();
			( new Import_Export_Customization() )->register();
		}
	}
}
