<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Filters;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions\Blur_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions\Color_Tone_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions\Drop_Shadow_Filter_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions\Hue_Rotate_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Filters\Functions\Intensity_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Css_Filter_Func_Prop_Type extends Object_Prop_Type {

	public static function get_key(): string {
		return 'css-filter-func';
	}

	protected function validate_value( $value ): bool {
		return true;
	}

	protected function define_shape(): array {
		return [
			'func' => String_Prop_Type::make()
				->enum( [ 'blur', 'brightness', 'contrast', 'grayscale', 'invert', 'saturate', 'sepia', 'hue-rotate', 'drop-shadow' ] )
				->default( 'blur' )
				->required(),
			'args' => Union_Prop_Type::make()
				->add_prop_type( Blur_Prop_Type::make() )
				->add_prop_type( Intensity_Prop_Type::make() )
				->add_prop_type( Hue_Rotate_Prop_Type::make() )
				->add_prop_type( Color_Tone_Prop_Type::make() )
				->add_prop_type( Drop_Shadow_Filter_Prop_Type::make() )
				->required(),
		];
	}
}
