<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Border_Width_Prop_Type extends Object_Prop_Type {
	public static function get_key(): string {
		return 'border-width';
	}

	protected function define_shape(): array {
		$units = Size_Constants::border();

		return [
			'block-start' => Size_Prop_Type::make()->required()->units( $units ),
			'block-end' => Size_Prop_Type::make()->required()->units( $units ),
			'inline-start' => Size_Prop_Type::make()->required()->units( $units ),
			'inline-end' => Size_Prop_Type::make()->required()->units( $units ),
		];
	}
}
