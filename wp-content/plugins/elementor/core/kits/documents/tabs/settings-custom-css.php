<?php
namespace Elementor\Core\Kits\Documents\Tabs;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Settings_Custom_CSS extends Tab_Base {

	public function get_id() {
		return 'settings-custom-css';
	}

	public function get_title() {
		return esc_html__( 'Custom CSS', 'elementor' );
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-custom-css';
	}

	public function get_help_url() {
		return 'https://go.elementor.com/global-custom-css/';
	}

	protected function register_tab_controls() {
		Plugin::$instance->controls_manager->add_custom_css_controls( $this->parent, $this->get_id() );
	}
}
