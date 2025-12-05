<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_Fonts_Promotion_Item extends Base_Promotion_Template {
	public function get_name() {
		return 'custom_fonts';
	}

	public function get_label() {
		return esc_html__( 'Custom Fonts', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Custom Fonts', 'elementor' );
	}

	protected function get_promotion_title(): string {
		return esc_html__( 'Stay on brand with a Custom Font', 'elementor' );
	}

	protected function get_content_lines(): array {
		return [
			esc_html__( 'Upload any font to keep your website true to your brand', 'elementor' ),
			sprintf(
				/* translators: %s: br  */
				esc_html__( 'Remain GDPR compliant with Custom Fonts that let you disable %s Google Fonts from your website', 'elementor' ),
				'<br />'
			),
		];
	}

	protected function get_cta_url(): string {
		return 'https://go.elementor.com/go-pro-custom-fonts/';
	}

	protected function get_video_url(): string {
		return 'https://www.youtube-nocookie.com/embed/j_guJkm28eY?si=cdd2TInwuGDTtCGD';
	}
}
