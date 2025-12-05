<?php
namespace Elementor\Includes\Settings\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Get_Help_Menu_Item implements Admin_Menu_Item_With_Page {
	const URL = 'https://go.elementor.com/docs-admin-menu/';

	public function is_visible() {
		return true;
	}

	public function get_parent_slug() {
		return Settings::PAGE_ID;
	}

	public function get_label() {
		return esc_html__( 'Get Help', 'elementor' );
	}

	public function get_page_title() {
		return '';
	}

	public function get_capability() {
		return 'manage_options';
	}

	public function render() {
		// Redirects from the settings page on `admin_init`.
		die;
	}
}
