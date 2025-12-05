<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Filters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Backdrop_Filter_Prop_Type extends Filter_Prop_Type {

	public static function get_key(): string {
		return 'backdrop-filter';
	}
}
