<?php

namespace Elementor\Modules\GlobalClasses\ImportExport;

use Elementor\App\Modules\ImportExport\Processes\Export;
use Elementor\App\Modules\ImportExport\Processes\Import;

class Import_Export {
	const FILE_NAME = 'global-classes';

	public function register_hooks() {
		add_action( 'elementor/import-export/export-kit', function ( Export $export ) {
			$export->register( new Export_Runner() );
		} );

		add_action( 'elementor/import-export/import-kit', function ( Import $import ) {
			$import->register( new Import_Runner() );
		} );
	}
}
