<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Position_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Gradient_Overlay_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'background-gradient-overlay';
	}

	protected function define_shape(): array {
		return [
			'type' => String_Prop_Type::make()->enum( [ 'linear', 'radial' ] ),
			'angle' => Number_Prop_Type::make(),
			'stops' => Gradient_Color_Stop_Prop_Type::make(),
			'positions' => String_Prop_Type::make()->enum( self::get_position_enum_values() ),
		];
	}

	private static function get_position_enum_values(): array {
		return Position_Prop_Type::get_position_enum_values();
	}
}
