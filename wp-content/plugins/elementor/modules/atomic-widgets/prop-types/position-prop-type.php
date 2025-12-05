<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Position_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'object-position';
	}

	protected function define_shape(): array {
		$units = Size_Constants::position();

		return [
			'x' => Size_Prop_Type::make()->units( $units ),
			'y' => Size_Prop_Type::make()->units( $units ),
		];
	}

	public static function get_position_enum_values(): array {
		return [
			'center center',
			'center left',
			'center right',
			'top center',
			'top left',
			'top right',
			'bottom center',
			'bottom left',
			'bottom right',
		];
	}
}
