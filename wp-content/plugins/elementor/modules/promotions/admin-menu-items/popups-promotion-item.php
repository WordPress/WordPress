<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Popups_Promotion_Item extends Base_Promotion_Item {

	private array $promotion_data;

	public function __construct() {
		$this->promotion_data = [
			'title' => esc_html__( 'Get Popup Builder', 'elementor' ),
			'content' => esc_html__(
				'The Popup Builder lets you take advantage of all the amazing features in Elementor, so you can build beautiful & highly converting popups. Get Elementor Pro and start designing your popups today.',
				'elementor'
			),
			'action_button' => [
				'text' => esc_html__( 'Upgrade Now', 'elementor' ),
				'url' => 'https://go.elementor.com/go-pro-popup-builder/',
			],
		];

		$this->promotion_data = Filtered_Promotions_Manager::get_filtered_promotion_data( $this->promotion_data, 'elementor/templates/popup', 'action_button', 'url' );
	}

	public function get_parent_slug() {
		return Source_Local::ADMIN_MENU_SLUG;
	}

	public function get_name() {
		return 'popups';
	}

	public function get_label() {
		return esc_html__( 'Popups', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Popups', 'elementor' );
	}

	public function get_promotion_title() {
		return $this->promotion_data['title'];
	}

	public function get_promotion_description() {
		return $this->promotion_data['content'];
	}

	public function get_cta_url() {
		return $this->promotion_data['action_button']['url'];
	}

	public function get_cta_text() {
		return $this->promotion_data['action_button']['text'];
	}
}
