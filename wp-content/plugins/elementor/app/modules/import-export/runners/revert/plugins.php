<?php

namespace Elementor\App\Modules\ImportExport\Runners\Revert;

class Plugins extends Revert_Runner_Base {

	public static function get_name(): string {
		return 'plugins';
	}

	public function should_revert( array $data ): bool {
		return false;
	}

	public function revert( array $data ) {}
}
