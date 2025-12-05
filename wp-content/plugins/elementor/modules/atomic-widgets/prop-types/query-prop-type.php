<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Query_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'query';
	}

	protected function define_shape(): array {
		return [
			'id' => Number_Prop_Type::make()
				->required(),
			'label' => String_Prop_Type::make(),
		];
	}

	public function get_default() {
		return null;
	}
}
