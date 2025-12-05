<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Group control interface.
 *
 * An interface for Elementor group control.
 *
 * @since 1.0.0
 */
interface Group_Control_Interface {

	/**
	 * Get group control type.
	 *
	 * Retrieve the group control type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_type();
}
