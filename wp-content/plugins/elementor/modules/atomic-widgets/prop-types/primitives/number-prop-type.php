<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Primitives;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Number_Prop_Type extends Plain_Prop_Type {
	public static function get_key(): string {
		return 'number';
	}

	protected function validate_value( $value ): bool {
		return is_numeric( $value );
	}

	protected function sanitize_value( $value ) {
		return (int) $value;
	}
}
