<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Layout_Direction_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'layout-direction';
	}

	protected function define_shape(): array {
		$units = Size_Constants::layout();

		return [
			'column' => Size_Prop_Type::make()->units( $units ),
			'row' => Size_Prop_Type::make()->units( $units ),
		];
	}
}
