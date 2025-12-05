<?php

namespace Elementor\Modules\Checklist\Steps;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Setup_Header extends Step_Base {
	const STEP_ID = 'setup_header';

	public function __construct( $module, $wordpress_adapter = null, $elementor_adapter = null, $should_promote = true ) {
		$promotion_data = $should_promote
			? $this->render_promotion()
			: null;

		parent::__construct( $module, $wordpress_adapter, $elementor_adapter, $promotion_data );
	}

	public function get_id(): string {
		return self::STEP_ID;
	}

	public function is_visible(): bool {
		if ( Utils::has_pro() ) {
			return false;
		}

		return parent::is_visible();
	}

	public function is_absolute_completed(): bool {
		$args = [
			'post_type' => 'elementor_library',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => '_elementor_template_type',
					'value' => 'header',
					'compare' => '=',
				],
				[
					'key' => '_elementor_conditions',
				],
			],
			'posts_per_page' => 1,
			'fields' => 'ids',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		];
		$query = $this->wordpress_adapter->get_query( $args );
		$header_templates = $query->posts ?? [];

		return count( $header_templates ) >= 1;
	}

	public function get_title(): string {
		return esc_html__( 'Set up a header', 'elementor' );
	}

	public function get_description(): string {
		return esc_html__( 'This element applies across different pages, so visitors can easily navigate around your site.', 'elementor' );
	}

	public function get_cta_text(): string {
		return esc_html__( 'Add a header', 'elementor' );
	}

	public function get_cta_url(): string {
		return '';
	}

	public function get_image_src(): string {
		return 'https://assets.elementor.com/checklist/v1/images/checklist-step-4.jpg';
	}

	public function get_is_completion_immutable(): bool {
		return false;
	}

	public function get_learn_more_url(): string {
		return 'https://go.elementor.com/app-website-checklist-header-article';
	}

	private function render_promotion() {
			return Filtered_Promotions_Manager::get_filtered_promotion_data(
				[
					'url' => 'https://go.elementor.com/go-pro-website-checklist-header',
					'text' => esc_html__( 'Upgrade Now', 'elementor' ),
					'icon' => 'default',
				],
				'elementor/checklist/promotion',
				'upgrade_url'
			);
	}
}
