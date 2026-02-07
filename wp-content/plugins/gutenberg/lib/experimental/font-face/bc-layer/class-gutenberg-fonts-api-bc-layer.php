<?php
/**
 * Fonts API BC Layer helpers.
 *
 * BACKPORT NOTE: Do not backport this file to Core.
 * This file is now part of the API's Backwards-Compatibility (BC) layer.
 *
 * @package    Gutenberg
 * @subpackage Fonts API
 * @since      15.7.0
 */

/**
 * Class Gutenberg_Fonts_API_BC_Layer
 *
 * BACKPORT NOTE: Do not backport this file to Core.
 *
 * @since X.X.X
 * @deprecated 16.3.0 Fonts API is not supported.
 */
class Gutenberg_Fonts_API_BC_Layer {

	/**
	 * Determines if the given fonts array is the deprecated array structure.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Structure check is not functional.
	 *
	 * @return bool False.
	 */
	public static function is_deprecated_structure() {
		_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		return false;
	}

	/**
	 * Migrates deprecated fonts structure into new API data structure,
	 * i.e. variations grouped by their font-family.
	 *
	 * @since X.X.X
	 * @deprecated 16.3.0 Migrate is not supported.
	 *
	 * @return array Empty array.
	 */
	public static function migrate_deprecated_structure() {
		_deprecated_function( __METHOD__, 'Gutenberg 16.3.0' );
		return array();
	}
}
