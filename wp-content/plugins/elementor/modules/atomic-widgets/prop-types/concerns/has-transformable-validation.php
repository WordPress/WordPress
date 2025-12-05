<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Has_Transformable_Validation {
	protected function is_transformable( $value ): bool {
		$satisfies_basic_shape = (
			is_array( $value ) &&
			array_key_exists( '$$type', $value ) &&
			array_key_exists( 'value', $value ) &&
			static::get_key() === $value['$$type']
		);

		$supports_disabling = (
			! isset( $value['disabled'] ) ||
			is_bool( $value['disabled'] )
		);

		return (
			$satisfies_basic_shape &&
			$supports_disabling
		);
	}

	abstract public static function get_key(): string;
}
