<?php
namespace Elementor\Core\Frontend\RenderModes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Render_Mode_Normal extends Render_Mode_Base {
	/**
	 * @return string
	 */
	public static function get_name() {
		return 'normal';
	}

	/**
	 * Anyone can access the normal render mode.
	 *
	 * @return bool
	 */
	public function get_permissions_callback() {
		return true;
	}
}
