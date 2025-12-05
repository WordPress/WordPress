<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Hue_Rotate_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'hue-rotate';
	}

	protected function define_shape(): array {
		return [
			'size' => Size_Prop_Type::make()
				->units( Size_Constants::hue_rotate_filter() )
				->default_unit( Size_Constants::UNIT_ANGLE_DEG )
				->default( [
					'size' => 0,
					'unit' => Size_Constants::UNIT_ANGLE_DEG,
				] ),
		];
	}
}
