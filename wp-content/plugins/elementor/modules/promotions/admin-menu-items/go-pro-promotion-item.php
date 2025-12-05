<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

use Elementor\Core\Admin\Menu\Interfaces\Admin_Menu_Item_With_Page;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Settings;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Go_Pro_Promotion_Item implements Admin_Menu_Item_With_Page {
	const URL = 'https://go.elementor.com/pro-admin-menu/';

	public function get_name() {
		return 'admin_menu_promo';
	}

	public function is_visible() {
		return true;
	}

	public function get_parent_slug() {
		return Settings::PAGE_ID;
	}

	public function get_label() {
		$upgrade_text = esc_html__( 'Upgrade', 'elementor' );

		if ( Utils::is_sale_time() ) {
			$upgrade_text = esc_html__( 'Upgrade Sale Now', 'elementor' );
		}

		return apply_filters( 'elementor/admin_menu/custom_promotion', [ 'upgrade_text' => $upgrade_text ] )['upgrade_text'] ?? $upgrade_text;
	}

	public function get_page_title() {
		return '';
	}

	public function get_capability() {
		return 'manage_options';
	}

	public static function get_url() {
		$url = self::URL;
		$filtered_url = apply_filters( 'elementor/admin_menu/custom_promotion', [ 'upgrade_url' => $url ] )['upgrade_url'] ?? '';
		$promotion_data = Filtered_Promotions_Manager::get_filtered_promotion_data( [ 'upgrade_url' => $filtered_url ], 'elementor/admin_menu/custom_promotion', 'upgrade_url' );
		return $promotion_data ['upgrade_url'];
	}

	public function render() {
		// Redirects from the module on `admin_init`.
		die;
	}
}
