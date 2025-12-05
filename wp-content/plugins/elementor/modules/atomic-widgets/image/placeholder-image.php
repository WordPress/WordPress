<?php

namespace Elementor\Modules\AtomicWidgets\Image;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Placeholder_Image {

	public static function get_placeholder_image() {
		return ELEMENTOR_ASSETS_URL . 'images/placeholder-v4.svg';
	}

	public static function get_background_placeholder_image() {
		return ELEMENTOR_ASSETS_URL . 'images/background-placeholder.svg';
	}
}
