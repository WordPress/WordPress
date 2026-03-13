<?php

namespace HelloTheme\Modules\AdminHome\Components;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ajax_Handler {

	public function __construct() {
		add_action( 'wp_ajax_ehe_install_elementor', [ $this, 'install_elementor' ] );
	}

	public function install_elementor() {
		wp_ajax_install_plugin();
	}
}
