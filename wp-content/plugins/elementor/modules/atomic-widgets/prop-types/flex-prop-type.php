<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Flex_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'flex';
	}

	protected function define_shape(): array {
		return [
			'flexGrow' => Number_Prop_Type::make(),
			'flexShrink' => Number_Prop_Type::make(),
			'flexBasis' => Size_Prop_Type::make(),
		];
	}
}
