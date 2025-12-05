<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Filters;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filter_Prop_Type extends Array_Prop_Type {

	public static function get_key(): string {
		return 'filter';
	}

	protected function define_item_type(): Prop_Type {
		return Css_Filter_Func_Prop_Type::make();
	}
}
