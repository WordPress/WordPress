<?php
namespace Elementor\Modules\Promotions;

use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Includes\EditorAssetsAPI;
use Elementor\Utils;

class PromotionData {
	protected EditorAssetsAPI $editor_assets_api;

	public function __construct( EditorAssetsAPI $editor_assets_api ) {
		$this->editor_assets_api = $editor_assets_api;
	}

	public function get_promotion_data( $force_request = false ): array {
		$assets_data = $this->transform_assets_data( $force_request );

		return [
			Utils::ANIMATED_HEADLINE => $this->get_animated_headline_data( $assets_data ),
			Utils::VIDEO_PLAYLIST => $this->get_video_playlist_data( $assets_data ),
			Utils::CTA => $this->get_cta_button_data( $assets_data ),
			Utils::IMAGE_CAROUSEL => $this->get_image_carousel_data( $assets_data ),
			Utils::TESTIMONIAL_WIDGET => $this->get_testimonial_widget_data( $assets_data ),
		];
	}

	private function transform_assets_data( $force_request = false ) {
		$assets_data = $this->editor_assets_api->get_assets_data( $force_request );
		$transformed_data = [];

		foreach ( $assets_data as $asset ) {
			$transformed_data[ $asset['id'] ] = $asset['imageSrc'];
		}

		return $transformed_data;
	}

	private function get_animated_headline_data( $assets_data ) {
		$data = [
			'image' => esc_url( $assets_data[ Utils::ANIMATED_HEADLINE ] ?? '' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( 'Bring Headlines to Life', 'elementor' ),
			'description' => [
				esc_html__( 'Highlight key messages dynamically.', 'elementor' ),
				esc_html__( 'Apply rotating effects to text.', 'elementor' ),
				esc_html__( 'Fully customize your headlines.', 'elementor' ),
			],
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
			'upgrade_url' => 'https://go.elementor.com/go-pro-heading-widget-control/',
		];

		return $this->filter_data( Utils::ANIMATED_HEADLINE, $data );
	}

	private function get_video_playlist_data( $assets_data ) {
		$data = [
			'image' => esc_url( $assets_data[ Utils::VIDEO_PLAYLIST ] ?? '' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( 'Showcase Video Playlists', 'elementor' ),
			'description' => [
				esc_html__( 'Embed videos with full control.', 'elementor' ),
				esc_html__( 'Adjust layout and playback settings.', 'elementor' ),
				esc_html__( 'Seamlessly customize video appearance.', 'elementor' ),
			],
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
			'upgrade_url' => 'https://go.elementor.com/go-pro-video-widget-control/',
		];

		return $this->filter_data( Utils::VIDEO_PLAYLIST, $data );
	}

	private function get_cta_button_data( $assets_data ) {
		$data = [
			'image' => esc_url( $assets_data[ Utils::CTA ] ?? '' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( 'Boost Conversions with CTAs', 'elementor' ),
			'description' => [
				esc_html__( 'Combine text, buttons, and images.', 'elementor' ),
				esc_html__( 'Add hover animations and CSS effects.', 'elementor' ),
				esc_html__( 'Create unique, interactive designs.', 'elementor' ),
			],
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
			'upgrade_url' => 'https://go.elementor.com/go-pro-button-widget-control/',
		];

		return $this->filter_data( Utils::CTA, $data );
	}

	private function get_image_carousel_data( $assets_data ) {
		$data = [
			'image' => esc_url( $assets_data[ Utils::IMAGE_CAROUSEL ] ?? '' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( 'Design Custom Carousels', 'elementor' ),
			'description' => [
				esc_html__( 'Create flexible custom carousels.', 'elementor' ),
				esc_html__( 'Adjust transitions and animations.', 'elementor' ),
				esc_html__( 'Showcase multiple items with style.', 'elementor' ),
			],
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
			'upgrade_url' => 'https://go.elementor.com/go-pro-image-carousel-widget-control/',
		];

		return $this->filter_data( Utils::IMAGE_CAROUSEL, $data );
	}

	private function get_testimonial_widget_data( $assets_data ) {
		$data = [
			'image' => esc_url( $assets_data[ Utils::TESTIMONIAL_WIDGET ] ?? '' ),
			'image_alt' => esc_attr__( 'Upgrade', 'elementor' ),
			'title' => esc_html__( 'Upgrade Your Testimonials', 'elementor' ),
			'description' => [
				esc_html__( 'Display reviews in a rotating carousel.', 'elementor' ),
				esc_html__( 'Boost credibility with dynamic testimonials.', 'elementor' ),
				esc_html__( 'Customize layouts for visual appeal.', 'elementor' ),
			],
			'upgrade_text' => esc_html__( 'Upgrade Now', 'elementor' ),
			'upgrade_url' => 'https://go.elementor.com/go-pro-testimonial-widget-control/',
		];

		return $this->filter_data( Utils::TESTIMONIAL_WIDGET, $data );
	}

	private function filter_data( $widget_name, $asset_data ): array {
		return Filtered_Promotions_Manager::get_filtered_promotion_data( $asset_data, "elementor/widgets/{$widget_name}/custom_promotion", 'upgrade_url' );
	}
}
