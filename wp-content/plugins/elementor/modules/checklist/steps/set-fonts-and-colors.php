<?php

namespace Elementor\Modules\Checklist\Steps;

use Elementor\Core\DocumentTypes\Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Set_Fonts_And_Colors extends Step_Base {
	const STEP_ID = 'set_fonts_and_colors';

	public function get_id(): string {
		return self::STEP_ID;
	}

	public function is_absolute_completed(): bool {
		$settings = $this->elementor_adapter->get_kit_settings();
		$custom_color = $settings['custom_colors'] ?? '';
		$custom_fonts = $settings['custom_typography'] ?? '';

		return ! empty( $custom_color ) && ! empty( $custom_fonts );
	}

	public function get_title(): string {
		return __( 'Set up your Global Fonts & Colors', 'elementor' );
	}

	public function get_description(): string {
		return esc_html__( 'Global colors and fonts ensure a cohesive look across your site. Start by defining one color and one font.', 'elementor' );
	}

	public function get_cta_text(): string {
		return esc_html__( 'Go to Site Identity', 'elementor' );
	}

	public function get_cta_url(): string {
		$settings = $this->elementor_adapter->get_kit_settings();

		$tab = ! $settings['custom_colors'] ? 'global-typography' : 'global-colors';

		return Page::get_site_settings_url_config( $tab )['url'];
	}

	public function get_is_completion_immutable(): bool {
		return false;
	}

	public function get_image_src(): string {
		return 'https://assets.elementor.com/checklist/v1/images/checklist-step-2.jpg';
	}

	public function get_learn_more_url(): string {
		return 'http://go.elementor.com/app-website-checklist-global-article';
	}
}
