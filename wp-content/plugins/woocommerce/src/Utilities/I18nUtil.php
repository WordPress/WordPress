<?php
/**
 * A class of utilities for dealing with internationalization.
 */

namespace Automattic\WooCommerce\Utilities;

/**
 * A class of utilities for dealing with internationalization.
 */
final class I18nUtil {
	/**
	 * A cache for the i18n units data.
	 *
	 * @var array $units
	 */
	private static $units;

	/**
	 * Get the translated label for a weight unit of measure.
	 *
	 * This will return the original input string if it isn't found in the units array. This way a custom unit of
	 * measure can be used even if it's not getting translated.
	 *
	 * @param string $weight_unit The abbreviated weight unit in English, e.g. kg.
	 *
	 * @return string
	 */
	public static function get_weight_unit_label( $weight_unit ) {
		if ( empty( self::$units ) ) {
			self::$units = include WC()->plugin_path() . '/i18n/units.php';
		}

		$label = $weight_unit;

		if ( ! empty( self::$units['weight'][ $weight_unit ] ) ) {
			$label = self::$units['weight'][ $weight_unit ];
		}

		return $label;
	}

	/**
	 * Get the translated label for a dimensions unit of measure.
	 *
	 * This will return the original input string if it isn't found in the units array. This way a custom unit of
	 * measure can be used even if it's not getting translated.
	 *
	 * @param string $dimensions_unit The abbreviated dimension unit in English, e.g. cm.
	 *
	 * @return string
	 */
	public static function get_dimensions_unit_label( $dimensions_unit ) {
		if ( empty( self::$units ) ) {
			self::$units = include WC()->plugin_path() . '/i18n/units.php';
		}

		$label = $dimensions_unit;

		if ( ! empty( self::$units['dimensions'][ $dimensions_unit ] ) ) {
			$label = self::$units['dimensions'][ $dimensions_unit ];
		}

		return $label;
	}
}
