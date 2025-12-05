<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Modules\AtomicWidgets\PropTypes\Concerns\Has_Default;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;

trait Dimensional_Prop_Type {
	use Has_Default;

	/**
	 * Defines the shape of the 3D prop type.
	 *
	 * @return array
	 */
	protected function define_shape(): array {
		$shape = [];

		foreach ( $this->get_dimensions() as $dimension ) {
			$shape[ $dimension ] = $this->get_prop_type( $dimension );
		}

		return $shape;
	}

	protected function get_prop_type( $bind ): Prop_Type {
		$prop_type = Size_Prop_Type::make();
		$units = $this->units( $bind );
		$default_value = $this->get_default_value_by_bind( $bind );

		if ( $units ) {
			$prop_type->units( $units );
		}

		if ( $default_value ) {
			$prop_type->default_unit( $default_value['unit'] );
			$prop_type->default( $default_value );
		}

		return $prop_type;
	}

	protected function get_default_value_unit(): string {
		return Size_Constants::UNIT_PX;
	}

	protected function get_default_value_size(): int {
		return 0;
	}

	protected function units(): ?array {
		return null;
	}

	protected function get_dimensions(): array {
		return [ 'x', 'y', 'z' ];
	}

	protected function get_default_value_by_bind(): ?array {
		return [
			'size' => $this->get_default_value_size(),
			'unit' => $this->get_default_value_unit(),
		];
	}
}
