<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Overlay_Prop_Type extends Array_Prop_Type {
	public static function get_key(): string {
		return 'background-overlay';
	}

	protected function define_item_type(): Prop_Type {
		return Union_Prop_Type::make()
			->add_prop_type( Background_Color_Overlay_Prop_Type::make() )
			->add_prop_type( Background_Image_Overlay_Prop_Type::make() )
			->add_prop_type( Background_Gradient_Overlay_Prop_Type::make() );
	}
}
