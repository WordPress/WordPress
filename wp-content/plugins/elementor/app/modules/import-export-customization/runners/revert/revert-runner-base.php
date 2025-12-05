<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Revert;

use Elementor\App\Modules\ImportExportCustomization\Runners\Runner_Interface;

abstract class Revert_Runner_Base implements Runner_Interface {

	/**
	 * By the passed data we should decide if we want to run the revert function of the runner or not.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	abstract public function should_revert( array $data ): bool;

	/**
	 * Main function of the runner revert process.
	 *
	 * @param array $data Necessary data for the revert process.
	 */
	abstract public function revert( array $data );
}
