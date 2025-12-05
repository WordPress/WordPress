<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Widget_Common extends Widget_Common_Base {

	public function get_name() {
		return 'common';
	}
}
