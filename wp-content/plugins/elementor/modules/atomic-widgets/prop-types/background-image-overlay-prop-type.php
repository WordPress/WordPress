<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Position_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Image_Overlay_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'background-image-overlay';
	}

	protected function define_shape(): array {
		return [
			'image' => Image_Prop_Type::make(),
			'repeat' => String_Prop_Type::make()->enum( [ 'repeat', 'repeat-x', 'repeat-y', 'no-repeat' ] ),
			'size' => Union_Prop_Type::make()
				->add_prop_type( String_Prop_Type::make()->enum( [ 'auto', 'cover', 'contain' ] ) )
				->add_prop_type( Background_Image_Overlay_Size_Scale_Prop_Type::make() ),
			'position' => Union_Prop_Type::make()
				->add_prop_type( String_Prop_Type::make()->enum( Position_Prop_Type::get_position_enum_values() ) )
				->add_prop_type( Background_Image_Position_Offset_Prop_Type::make() ),
			'attachment' => String_Prop_Type::make()->enum( [ 'fixed', 'scroll' ] ),
		];
	}
}
