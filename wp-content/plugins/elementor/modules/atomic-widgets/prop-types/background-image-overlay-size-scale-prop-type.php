<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Image_Overlay_Size_Scale_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'background-image-size-scale';
	}

	protected function define_shape(): array {
		return [
			'width' => Size_Prop_Type::make(),
			'height' => Size_Prop_Type::make(),
		];
	}
}
