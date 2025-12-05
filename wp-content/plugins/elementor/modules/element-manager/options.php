<?php
namespace Elementor\Modules\ElementManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Options {

	public static function get_disabled_elements() {
		return (array) get_option( 'elementor_disabled_elements', [] );
	}

	public static function update_disabled_elements( $elements ) {
		update_option( 'elementor_disabled_elements', (array) $elements );
	}

	public static function is_element_disabled( $element_name ) {
		return in_array( $element_name, self::get_disabled_elements() );
	}
}
