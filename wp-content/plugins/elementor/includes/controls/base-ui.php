<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor base UI control.
 *
 * An abstract class for creating new UI controls in the panel.
 *
 * @abstract
 */
abstract class Base_UI_Control extends Base_Control {

	/**
	 * Get features.
	 *
	 * Retrieve the list of all the available features.
	 *
	 * @since 1.5.0
	 * @access public
	 * @static
	 *
	 * @return array Features array.
	 */
	public static function get_features() {
		return [ 'ui' ];
	}
}
