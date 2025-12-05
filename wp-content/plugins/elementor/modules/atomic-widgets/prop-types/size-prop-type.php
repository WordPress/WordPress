<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Size_Constants;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Size_Prop_Type extends Object_Prop_Type {

	public static function get_supported_units(): array {
		return apply_filters( 'elementor/atomic-widgets/size/units', Size_Constants::all_supported_units() );
	}

	public function units( $units = 'all' ) {
		if ( 'all' === $units ) {
			$units = Size_Constants::all();
		}

		if ( is_array( $units ) ) {
			foreach ( $units as $unit ) {
				if ( ! is_string( $unit ) ) {
					Utils::safe_throw( 'All units must be strings.' );
				}
			}
		}

		$this->settings['available_units'] = $units;

		return $this;
	}

	public function default_unit( $unit ) {
		$this->settings['default_unit'] = $unit;

		return $this;
	}

	public function get_settings(): array {
		if ( ! array_key_exists( 'available_units', $this->settings ) ) {
			$this->units();
		}

		return parent::get_settings();
	}

	public static function get_key(): string {
		return 'size';
	}

	protected function validate_value( $value ): bool {
		if ( ! is_array( $value ) ||
			! array_key_exists( 'size', $value ) ||
			! array_key_exists( 'unit', $value ) ||
			empty( $value['unit'] ) ||
			! in_array( $value['unit'], static::get_supported_units(), true )
		) {
			return false;
		}

		switch ( $value['unit'] ) {
			case Size_Constants::UNIT_CUSTOM:
				return null !== $value['size'];
			case Size_Constants::UNIT_AUTO:
				return ! $value['size'];
			default:
				return (
					! in_array( $value['unit'], [ Size_Constants::UNIT_AUTO, Size_Constants::UNIT_CUSTOM ], true ) &&
					( ! empty( $value['size'] ) || 0 === $value['size'] ) &&
					is_numeric( $value['size'] )
				);
		}
	}

	public function sanitize_value( $value ) {
		$unit = sanitize_text_field( $value['unit'] );

		if ( ! in_array( $value['unit'], [ Size_Constants::UNIT_AUTO, Size_Constants::UNIT_CUSTOM ] ) ) {
			return [
				// The + operator cast the $value['size'] to numeric (either int or float - depends on the value)
				'size' => +$value['size'],
				'unit' => $unit,
			];
		}

		return [
			'size' => Size_Constants::UNIT_AUTO === $value['unit'] ? '' : sanitize_text_field( $value['size'] ),
			'unit' => $unit,
		];
	}

	protected function define_shape(): array {
		return [
			'unit' => String_Prop_Type::make()->enum( static::get_supported_units() )
				->required(),
			'size' => Union_Prop_Type::make()
				->add_prop_type( String_Prop_Type::make() )
				->add_prop_type( Number_Prop_Type::make() ),
		];
	}
}
