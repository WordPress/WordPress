<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Transform\Functions;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Traits\Dimensional_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Scale_Prop_Type extends Object_Prop_Type {
	use Dimensional_Prop_Type;

	public static function get_key(): string {
		return 'transform-scale';
	}

	protected function get_prop_type(): Prop_Type {
		return Number_Prop_Type::make()->default( 1 );
	}
}
