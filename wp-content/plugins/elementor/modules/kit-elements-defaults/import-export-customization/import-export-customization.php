<?php
namespace Elementor\Modules\KitElementsDefaults\ImportExportCustomization;

use Elementor\App\Modules\ImportExportCustomization\Processes\Export;
use Elementor\App\Modules\ImportExportCustomization\Processes\Import;
use Elementor\Modules\KitElementsDefaults\ImportExportCustomization\Runners\Export as Export_Runner;
use Elementor\Modules\KitElementsDefaults\ImportExportCustomization\Runners\Import as Import_Runner;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import_Export_Customization {
	const FILE_NAME = 'kit-elements-defaults';

	public function register() {
		// Revert kit is working by default, using the site-settings runner.

		add_action( 'elementor/import-export-customization/export-kit', function ( Export $export ) {
			$export->register( new Export_Runner() );
		} );

		add_action( 'elementor/import-export-customization/import-kit', function ( Import $import ) {
			$import->register( new Import_Runner() );
		} );
	}
}
