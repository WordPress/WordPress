<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Icon_Font {

	/**
	 * Get Icon type.
	 *
	 * Retrieve the icon type.
	 *
	 * @access public
	 * @abstract
	 */
	abstract public function get_type();

	/**
	 * Enqueue Icon scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the Icon.
	 *
	 * @access public
	 */
	abstract public function enqueue();

	abstract public function get_css_prefix();

	abstract public function get_icons();

	public function __construct() {}
}
