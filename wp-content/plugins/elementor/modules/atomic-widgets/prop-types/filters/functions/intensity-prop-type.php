<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Intensity_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'intensity';
	}

	protected function define_shape(): array {
		return [
			'size' => Size_Prop_Type::make()
				->units( Size_Constants::intensity_filter() )
				->default_unit( Size_Constants::UNIT_PERCENT )
				->default( [
					'size' => 100,
					'unit' => Size_Constants::UNIT_PERCENT,
				] ),
		];
	}
}
