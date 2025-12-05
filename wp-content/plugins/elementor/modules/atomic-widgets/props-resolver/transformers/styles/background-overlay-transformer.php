<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Multi_Props;
use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Overlay_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		$normalized_values = $this->normalize_overlay_values( $value );

		if ( empty( $normalized_values ) ) {
			return null;
		}

		return [
			'background-image' => $this->get_values_string( $normalized_values, 'src', Background_Image_Overlay_Transformer::DEFAULT_IMAGE, true ),
			'background-repeat' => $this->get_values_string( $normalized_values, 'repeat', Background_Image_Overlay_Transformer::DEFAULT_REPEAT ),
			'background-attachment' => $this->get_values_string( $normalized_values, 'attachment', Background_Image_Overlay_Transformer::DEFAULT_ATTACHMENT ),
			'background-size' => $this->get_values_string( $normalized_values, 'size', Background_Image_Overlay_Transformer::DEFAULT_SIZE ),
			'background-position' => $this->get_values_string( $normalized_values, 'position', Background_Image_Overlay_Transformer::DEFAULT_POSITION ),
		];
	}

	private function normalize_overlay_values( $overlays ): array {
		$mapped_values = array_map( function( $value ) {
			if ( is_string( $value ) ) {
				return [
					'src' => $value,
					'repeat' => null,
					'attachment' => null,
					'size' => null,
					'position' => null,
				];
			}

			return $value;
		}, $overlays );

		return array_filter( $mapped_values, function( $value ) {
			return is_array( $value ) && ! empty( $value['src'] );
		} );
	}

	private function get_values_string( $value, string $prop, string $default_value, bool $prevent_unification = false ) {
		$is_empty = empty( array_filter( $value, function ( array $item ) use ( $prop ) {
			return isset( $item[ $prop ] ) && ! is_null( $item[ $prop ] );
		} ) );

		if ( $is_empty ) {
			return $default_value;
		}

		$formatted_values = array_map( function ( $item ) use ( $prop, $default_value ) {
			if ( is_string( $item ) ) {
				return $default_value;
			}

			if ( ! is_array( $item ) ) {
				return $default_value;
			}

			return $item[ $prop ] ?? $default_value;
		}, $value );

		if ( ! $prevent_unification ) {
			$all_same = count( array_unique( $formatted_values ) ) === 1;

			if ( $all_same ) {
				return $formatted_values[0];
			}
		}

		return implode( ',', $formatted_values );
	}
}
