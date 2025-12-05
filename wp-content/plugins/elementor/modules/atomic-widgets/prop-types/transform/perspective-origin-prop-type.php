<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Transform;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Traits\Dimensional_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Perspective_Origin_Prop_Type extends Object_Prop_Type {
	use Dimensional_Prop_Type;

	public static function get_key(): string {
		return 'perspective-origin';
	}

	protected function units(): array {
		return [ Size_Constants::UNIT_PX, Size_Constants::UNIT_PERCENT, Size_Constants::UNIT_EM, Size_Constants::UNIT_REM ];
	}

	protected function get_dimensions(): array {
		return [ 'x', 'y' ];
	}

	protected function get_default_value_unit(): string {
		return Size_Constants::UNIT_PERCENT;
	}

	protected function get_default_value_size(): int {
		return 50;
	}
}
