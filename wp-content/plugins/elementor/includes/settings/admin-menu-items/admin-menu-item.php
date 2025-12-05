<?php
namespace Elementor\Includes\Settings\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Admin_Menu_Item implements Admin_Menu_Item_With_Page {
	private $settings_page;

	public function __construct( Settings $settings_page ) {
		$this->settings_page = $settings_page;
	}

	public function is_visible() {
		return true;
	}

	public function get_parent_slug() {
		return $this->settings_page->home_module->is_experiment_active() ? 'elementor' : null;
	}

	public function get_label() {
		return $this->settings_page->home_module->is_experiment_active()
			? esc_html__( 'Settings', 'elementor' )
			: esc_html__( 'Elementor', 'elementor' );
	}

	public function get_page_title() {
		return $this->get_label();
	}

	public function get_position() {
		return '58.5';
	}

	public function get_capability() {
		return 'manage_options';
	}

	public function render() {
		$this->settings_page->display_settings_page();
	}
}
