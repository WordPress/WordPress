<?php
namespace Elementor\Modules\Styleguide\Controls;

use Elementor\Control_Switcher;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Switcher extends Control_Switcher {

	const CONTROL_TYPE = 'global-style-switcher';

	/**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `global-style-switcher`.
	 *
	 * @since 3.13.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return self::CONTROL_TYPE;
	}
}
