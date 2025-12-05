<?php

namespace Elementor\App\Modules\ImportExport\Runners\Revert;

class Templates extends Revert_Runner_Base {
	/**
	 * The implement of this runner is part of the Pro plugin.
	 */
	public static function get_name(): string {
		return 'templates';
	}

	public function should_revert( array $data ): bool {
		return false;
	}

	public function revert( array $data ) { }
}
