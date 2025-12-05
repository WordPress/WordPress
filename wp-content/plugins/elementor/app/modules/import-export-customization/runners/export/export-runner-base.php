<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\App\Modules\ImportExportCustomization\Runners\Runner_Interface;

abstract class Export_Runner_Base implements Runner_Interface {

	/**
	 * By the passed data we should decide if we want to run the export function of the runner or not.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	abstract public function should_export( array $data );

	/**
	 * Main function of the runner export process.
	 *
	 * @param array $data Necessary data for the export process.
	 *
	 * @return array{files: array, manifest: array}
	 * The files that should be part of the kit and the relevant manifest data.
	 */
	abstract public function export( array $data );
}
