<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_Code_Promotion_Item extends Base_Promotion_Template {
	public function get_name() {
		return 'custom_code';
	}

	public function get_label() {
		return esc_html__( 'Custom Code', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Custom Code', 'elementor' );
	}

	protected function get_promotion_title(): string {
		return esc_html__( 'Enjoy Creative Freedom with Custom Code', 'elementor' );
	}

	protected function get_content_lines(): array {
		return [
			esc_html__( 'Add Custom Code snippets anywhere on your website, including the header or footer to measure your page’s performance*', 'elementor' ),
			esc_html__( 'Use Custom Code to create sophisticated custom interactions to engage visitors', 'elementor' ),
			esc_html__( 'Leverage Elementor AI to instantly generate Custom Code for Elementor', 'elementor' ),
		];
	}

	protected function get_side_note(): string {
		return esc_html__( '* Requires an Advanced subscription or higher', 'elementor' );
	}

	protected function get_cta_url(): string {
		return 'https://go.elementor.com/go-pro-custom-code/';
	}

	protected function get_video_url(): string {
		return 'https://www.youtube-nocookie.com/embed/IOovQd1hJUg?si=xeBJ_mRZxRH1l5O6';
	}
}
