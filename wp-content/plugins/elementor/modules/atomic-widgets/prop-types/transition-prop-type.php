<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transition_Prop_Type extends Array_Prop_Type {
	public static function get_key(): string {
		return 'transition';
	}

	protected function define_item_type(): Prop_Type {
		return Selection_Size_Prop_Type::make();
	}
}
