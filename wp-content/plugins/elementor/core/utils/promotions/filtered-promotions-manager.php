<?php

namespace Elementor\Core\Utils\Promotions;

use function DI\string;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filtered_Promotions_Manager {

	/**
	 * @param array  $promotion_data
	 * @param string $filter_name
	 * @param string $url_key
	 * @return array
	 */
	public static function get_filtered_promotion_data( array $promotion_data, string $filter_name, string $url_key, string $url_sub_key = '' ): array {
		$new_promotion_data = apply_filters( $filter_name, $promotion_data );

		if ( ! is_array( $new_promotion_data ) ) {
			return $promotion_data;
		}

		$new_promotion_data = self::retain_original_keys( $new_promotion_data, $promotion_data );

		$new_promotion_data = self::filter_invalid_url( $new_promotion_data, $url_key, $url_sub_key );

		return array_replace( $promotion_data, $new_promotion_data );
	}

	private static function domain_is_on_elementor_dot_com( $url ): bool {
		$domain = wp_parse_url( $url, PHP_URL_HOST );

		return isset( $domain ) && str_contains( $domain, 'elementor.com' );
	}

	private static function filter_invalid_url( $new_promotion_data, string $url_key, string $url_sub_key ) {
		if ( ! isset( $new_promotion_data[ $url_key ] ) ) {
			return $new_promotion_data;
		}

		if ( empty( $url_sub_key ) ) {
			$new_promotion_data = self::filter_invalid_url_in_flat_array( $new_promotion_data, $url_key );
		} else {
			$new_promotion_data[ $url_key ] = self::filter_invalid_url_in_flat_array( $new_promotion_data[ $url_key ], $url_sub_key );
		}

		return $new_promotion_data;
	}

	private static function filter_invalid_url_in_flat_array( array $new_promotion_data, string $url_key ): array {
		if ( ! self::domain_is_on_elementor_dot_com( $new_promotion_data[ $url_key ] ) ) {
			unset( $new_promotion_data[ $url_key ] );
		} else {
			$new_promotion_data[ $url_key ] = esc_url( $new_promotion_data[ $url_key ] );
		}

		return $new_promotion_data;
	}

	private static function retain_original_keys( array $new_promotion_data, array $promotion_data ): array {
		return array_intersect_key( $new_promotion_data, array_flip( array_keys( $promotion_data ) ) );
	}
}
