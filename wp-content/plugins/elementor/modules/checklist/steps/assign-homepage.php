<?php

namespace Elementor\Modules\Checklist\Steps;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Assign_Homepage extends Step_Base {
	const STEP_ID = 'assign_homepage';

	public function get_id(): string {
		return self::STEP_ID;
	}

	public function is_absolute_completed(): bool {
		$front_page_id = (int) ( $this->wordpress_adapter->get_option( 'page_on_front' ) ?? 0 );

		return (bool) $front_page_id;
	}

	public function get_title(): string {
		return esc_html__( 'Assign a homepage', 'elementor' );
	}

	public function get_description(): string {
		return esc_html__( 'Before your launch, make sure to assign a homepage so visitors have a clear entry point into your site.', 'elementor' );
	}

	public function get_cta_text(): string {
		return esc_html__( 'Assign homepage', 'elementor' );
	}

	public function get_cta_url(): string {
		return admin_url( 'options-reading.php' );
	}

	public function get_is_completion_immutable(): bool {
		return false;
	}

	public function get_image_src(): string {
		return 'https://assets.elementor.com/checklist/v1/images/checklist-step-6.jpg';
	}

	public function get_learn_more_url(): string {
		return 'http://go.elementor.com/app-website-checklist-assign-home-article';
	}
}
