<?php
namespace Elementor\Modules\KitElementsDefaults\ImportExport;

use Elementor\App\Modules\ImportExport\Processes\Export;
use Elementor\App\Modules\ImportExport\Processes\Import;
use Elementor\Modules\KitElementsDefaults\ImportExport\Runners\Export as Export_Runner;
use Elementor\Modules\KitElementsDefaults\ImportExport\Runners\Import as Import_Runner;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import_Export {
	const FILE_NAME = 'kit-elements-defaults';

	public function register() {
		// Revert kit is working by default, using the site-settings runner.

		add_action( 'elementor/import-export/export-kit', function ( Export $export ) {
			$export->register( new Export_Runner() );
		} );

		add_action( 'elementor/import-export/import-kit', function ( Import $import ) {
			$import->register( new Import_Runner() );
		} );
	}
}
