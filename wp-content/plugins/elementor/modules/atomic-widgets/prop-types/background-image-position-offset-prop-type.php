<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Image_Position_Offset_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'background-image-position-offset';
	}

	protected function define_shape(): array {
		$units = Size_Constants::position();

		return [
			'x' => Size_Prop_Type::make()->units( $units ),
			'y' => Size_Prop_Type::make()->units( $units ),
		];
	}
}
