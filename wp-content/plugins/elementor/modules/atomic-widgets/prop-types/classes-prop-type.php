<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Classes_Prop_Type extends Plain_Prop_Type {
	public static function get_key(): string {
		return 'classes';
	}

	protected function validate_value( $value ): bool {
		if ( ! is_array( $value ) ) {
			return false;
		}

		foreach ( $value as $class_name ) {
			if ( ! is_string( $class_name ) || ! preg_match( '/^[a-z][a-z-_0-9]*$/i', $class_name ) ) {
				return false;
			}
		}

		return true;
	}

	protected function sanitize_value( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}

		$sanitized = array_map(function ( $class_name ) {
			if ( ! is_string( $class_name ) ) {
				return null;
			}
			return sanitize_text_field( $class_name );
		}, $value);

		return array_filter($sanitized, function ( $class_name ) {
			return ! empty( $class_name );
		});
	}
}
