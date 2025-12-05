<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Size_Constants {
	const UNIT_PX = 'px';
	const UNIT_PERCENT = '%';
	const UNIT_EM = 'em';
	const UNIT_REM = 'rem';
	const UNIT_VW = 'vw';
	const UNIT_VH = 'vh';
	const UNIT_AUTO = 'auto';
	const UNIT_CUSTOM = 'custom';
	const UNIT_SECOND = 's';
	const UNIT_MILLI_SECOND = 'ms';
	const UNIT_ANGLE_DEG = 'deg';

	const LENGTH_UNITS = [
		self::UNIT_PX,
		self::UNIT_EM,
		self::UNIT_REM,
		self::UNIT_VW,
		self::UNIT_VH,
	];

	const TIME_UNITS = [ self::UNIT_SECOND, self::UNIT_MILLI_SECOND ];
	const EXTENDED_UNITS = [ self::UNIT_AUTO, self::UNIT_CUSTOM ];
	const VIEWPORT_MIN_MAX_UNITS = [ 'vmin', 'vmax' ];
	const ANGLE_UNITS = [ self::UNIT_ANGLE_DEG, 'rad', 'grad', 'turn' ];

	public static function all_supported_units(): array {
		return array_merge(
			self::all(),
			self::ANGLE_UNITS,
			self::TIME_UNITS,
			self::EXTENDED_UNITS,
			self::VIEWPORT_MIN_MAX_UNITS,
		);
	}

	public static function all(): array {
		return [
			...self::LENGTH_UNITS,
			self::UNIT_PERCENT,
			self::UNIT_AUTO,
		];
	}

	private static function units_without_auto(): array {
		return [ ...self::LENGTH_UNITS, self::UNIT_PERCENT ];
	}

	public static function layout() {
		return self::units_without_auto();
	}

	public static function spacing(): array {
		return self::units_without_auto();
	}

	public static function position(): array {
		return self::units_without_auto();
	}

	public static function anchor_offset() {
		return self::LENGTH_UNITS;
	}

	public static function typography(): array {
		return self::units_without_auto();
	}

	public static function stroke_width() {
		return [
			self::UNIT_PX,
			self::UNIT_EM,
			self::UNIT_REM,
		];
	}

	public static function transition() {
		return self::TIME_UNITS;
	}

	public static function border(): array {
		return self::units_without_auto();
	}


	public static function opacity(): array {
		return [ self::UNIT_PERCENT ];
	}

	public static function box_shadow(): array {
		return self::units_without_auto();
	}

	public static function rotate(): array {
		return self::ANGLE_UNITS;
	}

	public static function transform(): array {
		return self::units_without_auto();
	}

	public static function drop_shadow() {
		return self::LENGTH_UNITS;
	}

	public static function blur_filter() {
		return self::LENGTH_UNITS;
	}

	public static function intensity_filter() {
		return [ self::UNIT_PERCENT ];
	}

	public static function color_tone_filter() {
		return [ self::UNIT_PERCENT ];
	}

	public static function hue_rotate_filter() {
		return self::ANGLE_UNITS;
	}
}
