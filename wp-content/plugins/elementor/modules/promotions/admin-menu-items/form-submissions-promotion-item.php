<?php

namespace Elementor\Modules\Promotions\AdminMenuItems;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Form_Submissions_Promotion_Item extends Base_Promotion_Template {
	public function get_name() {
		return 'submissions';
	}

	public function get_label() {
		return esc_html__( 'Submissions', 'elementor' );
	}

	public function get_page_title() {
		return esc_html__( 'Submissions', 'elementor' );
	}

	public function get_promotion_title(): string {
		return sprintf(
			/* translators: %s: `<br>` tag. */
			esc_html__( 'Create Forms and Collect Leads %s with Elementor Pro', 'elementor' ),
			'<br>'
		);
	}

	protected function get_content_lines(): array {
		return [
			esc_html__( 'Create single or multi-step forms to engage and convert visitors', 'elementor' ),
			esc_html__( 'Use any field to collect the information you need', 'elementor' ),
			esc_html__( 'Integrate your favorite marketing software*', 'elementor' ),
			esc_html__( 'Collect lead submissions directly within your WordPress Admin to manage, analyze and perform bulk actions on the submitted lead*', 'elementor' ),
		];
	}

	protected function get_cta_url(): string {
		return 'https://go.elementor.com/go-pro-submissions/';
	}

	protected function get_video_url(): string {
		return 'https://www.youtube-nocookie.com/embed/LNfnwba9C-8?si=JLHk3UAexnvTfU1a';
	}

	protected function get_side_note(): string {
		return esc_html__( '* Requires an Advanced subscription or higher', 'elementor' );
	}
}
