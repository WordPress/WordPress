<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Key_Value_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'key-value';
	}

	protected function define_shape(): array {
		return [
			'key' => String_Prop_Type::make(),
			'value' => String_Prop_Type::make(),
		];
	}
}
