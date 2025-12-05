<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Selection_Size_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'selection-size';
	}

	protected function define_shape(): array {
		return [
			'selection' => Key_Value_Prop_Type::make()->required(),
			'size' => Size_Prop_Type::make()
				->units( Size_Constants::transition() )
				->default_unit( Size_Constants::UNIT_MILLI_SECOND )
				->required(),
		];
	}
}
