<?php

namespace Elementor\Modules\AtomicWidgets\Image;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Sizes {

	public static function get_keys() {
		return array_map(
			fn( $size ) => $size['value'],
			static::get_all()
		);
	}

	public static function get_all(): array {
		$wp_image_sizes = static::get_wp_image_sizes();

		$image_sizes = [];

		foreach ( $wp_image_sizes as $size_key => $size_attributes ) {

			$control_title = ucwords( str_replace( '_', ' ', $size_key ) );

			if ( is_array( $size_attributes ) ) {
				$control_title .= sprintf( ' - %d*%d', $size_attributes['width'], $size_attributes['height'] );
			}

			$image_sizes[] = [
				'label' => $control_title,
				'value' => $size_key,
			];
		}

		$image_sizes[] = [
			'label' => esc_html__( 'Full', 'elementor' ),
			'value' => 'full',
		];

		return $image_sizes;
	}

	private static function get_wp_image_sizes() {
		$default_image_sizes = get_intermediate_image_sizes();
		$additional_sizes = wp_get_additional_image_sizes();

		$image_sizes = [];

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = [
				'width' => (int) get_option( $size . '_size_w' ),
				'height' => (int) get_option( $size . '_size_h' ),
				'crop' => (bool) get_option( $size . '_crop' ),
			];
		}

		if ( $additional_sizes ) {
			$image_sizes = array_merge( $image_sizes, $additional_sizes );
		}

		// /** This filter is documented in wp-admin/includes/media.php */
		return apply_filters( 'image_size_names_choose', $image_sizes );
	}
}
