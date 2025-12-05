<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Color_Stop_Prop_Type extends Object_Prop_Type {

	public static function get_key(): string {
		return 'color-stop';
	}

	protected function define_shape(): array {
		return [
			'color' => Color_Prop_Type::make(),
			'offset' => Number_Prop_Type::make(),
		];
	}
}
