<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Transform;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions\Transform_Move_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions\Transform_Scale_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions\Transform_Rotate_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions\Transform_Skew_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Functions_Prop_Type extends Array_Prop_Type {
	public static function get_key(): string {
		return 'transform-functions';
	}

	protected function define_item_type(): Prop_Type {
		return Union_Prop_Type::make()
			->add_prop_type( Transform_Move_Prop_Type::make() )
			->add_prop_type( Transform_Scale_Prop_Type::make() )
			->add_prop_type( Transform_Rotate_Prop_Type::make() )
			->add_prop_type( Transform_Skew_Prop_Type::make() );
	}
}
