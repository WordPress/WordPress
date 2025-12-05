<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_Icons_Promotion_Item extends Base_Promotion_Template {
	public function get_name() {
		return 'custom_icons';
	}

	public function get_label() {
		return esc_html__( 'Custom Icons', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Custom Icons', 'elementor' );
	}

	protected function get_promotion_title(): string {
		return sprintf(
			/* translators: %s: `<br>` tag. */
			esc_html__( 'Enjoy creative freedom %s with Custom Icons', 'elementor' ),
			'<br />'
		);
	}

	protected function get_content_lines(): array {
		return [
			sprintf(
				/* translators: %s: `<br>` tag. */
				esc_html__( 'Expand your icon library beyond FontAwesome and add icon %s libraries of your choice', 'elementor' ),
				'<br />'
			),
			esc_html__( 'Add any icon, anywhere on your website', 'elementor' ),
		];
	}

	protected function get_cta_url(): string {
		return 'https://go.elementor.com/go-pro-custom-icons/';
	}

	protected function get_video_url(): string {
		return 'https://www.youtube-nocookie.com/embed/PsowinxDWfM?si=SV9Z3TLz3_XEy5C6';
	}
}
