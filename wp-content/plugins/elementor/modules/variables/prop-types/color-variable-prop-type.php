<?php

namespace Elementor\Modules\Variables\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Color_Variable_Prop_Type extends String_Prop_Type {
	public static function get_key(): string {
		return 'global-color-variable';
	}
}
