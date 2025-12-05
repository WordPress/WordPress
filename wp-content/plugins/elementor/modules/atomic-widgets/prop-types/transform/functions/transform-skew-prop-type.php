<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Traits\Dimensional_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Skew_Prop_Type extends Object_Prop_Type {
	use Dimensional_Prop_Type;

	public static function get_key(): string {
		return 'transform-skew';
	}

	protected function units(): ?array {
		return Size_Constants::rotate();
	}

	protected function get_default_value_unit(): string {
		return Size_Constants::UNIT_ANGLE_DEG;
	}

	protected function get_dimensions(): array {
		return [ 'x', 'y' ];
	}
}
