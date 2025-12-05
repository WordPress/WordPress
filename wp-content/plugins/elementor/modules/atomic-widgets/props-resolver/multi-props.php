<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Multi_Props {
	public static function is( $value ) {
		return (
			! empty( $value['$$multi-props'] ) &&
			true === $value['$$multi-props'] &&
			array_key_exists( 'value', $value )
		);
	}

	public static function generate( $value ) {
		return [
			'$$multi-props' => true,
			'value' => $value,
		];
	}

	public static function get_value( $value ) {
		return $value['value'] ?? null;
	}
}
