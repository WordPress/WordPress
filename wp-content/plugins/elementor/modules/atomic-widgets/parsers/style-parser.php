<?php

namespace Elementor\Modules\AtomicWidgets\Parsers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Modules\AtomicWidgets\Opt_In;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Core\Utils\Api\Parse_Result;

class Style_Parser {
	const VALID_TYPES = [
		'class',
	];

	const VALID_STATES = [
		'hover',
		'active',
		'focus',
		null,
	];

	private array $schema;

	public function __construct( array $schema ) {
		$this->schema = $schema;
	}

	public static function make( array $schema ): self {
		return new static( $schema );
	}

	/**
	 * @param array $style
	 * the style object to validate
	 */
	private function validate( array $style ): Parse_Result {
		$validated_style = $style;
		$result = Parse_Result::make();

		if ( ! isset( $style['id'] ) || ! is_string( $style['id'] ) ) {
			$result->errors()->add( 'id', 'missing_or_invalid' );
		}

		if ( ! isset( $style['type'] ) || ! in_array( $style['type'], self::VALID_TYPES, true ) ) {
			$result->errors()->add( 'type', 'missing_or_invalid' );
		}

		if ( ! isset( $style['label'] ) || ! is_string( $style['label'] ) ) {
			$result->errors()->add( 'label', 'missing_or_invalid' );
		} elseif ( Plugin::$instance->experiments->is_feature_active( Opt_In::EXPERIMENT_NAME ) ) {
			$label_validation = $this->validate_style_label( $style['label'] );

			if ( ! $label_validation['is_valid'] ) {
				$result->errors()->add( 'label', $label_validation['error_message'] );
			}
		}

		if ( ! isset( $style['variants'] ) || ! is_array( $style['variants'] ) ) {
			$result->errors()->add( 'variants', 'missing_or_invalid' );

			unset( $validated_style['variants'] );

			return $result->wrap( $validated_style );
		}

		$props_parser = Props_Parser::make( $this->schema );

		foreach ( $style['variants'] as $variant_index => $variant ) {
			if ( ! isset( $variant['meta'] ) ) {
				$result->errors()->add( 'meta', 'missing' );

				continue;
			}

			$meta_result = $this->validate_meta( $variant['meta'] );
			$custom_css_result = $this->validate_custom_css( $variant );

			$result->errors()->merge( $meta_result->errors(), 'meta' );
			$result->errors()->merge( $custom_css_result->errors(), 'custom_css' );

			if ( $meta_result->is_valid() ) {
				$variant_result = $props_parser->validate( $variant['props'] );

				$result->errors()->merge( $variant_result->errors(), "variants[$variant_index]" );

				$validated_style['variants'][ $variant_index ]['props'] = $variant_result->unwrap();
			} else {
				unset( $validated_style['variants'][ $variant_index ] );
			}
		}

		return $result->wrap( $validated_style );
	}

	private function validate_style_label( string $label ): array {
		$label = strtolower( $label );

		$reserved_class_names = [ 'container' ];

		if ( strlen( $label ) > 50 ) {
			return [
				'is_valid' => false,
				'error_message' => 'class_name_too_long',
			];
		}

		if ( strlen( $label ) < 2 ) {
			return [
				'is_valid' => false,
				'error_message' => 'class_name_too_short',
			];
		}

		if ( in_array( $label, $reserved_class_names, true ) ) {
			return [
				'is_valid' => false,
				'error_message' => 'reserved_class_name',
			];
		}

		$regexes = [
			[
				'pattern' => '/^(|[^0-9].*)$/',
				'message' => 'class_name_starts_with_digit',
			],
			[
				'pattern' => '/^\S*$/',
				'message' => 'class_name_contains_spaces',
			],
			[
				'pattern' => '/^(|[a-zA-Z0-9_-]+)$/',
				'message' => 'class_name_invalid_chars',
			],
			[
				'pattern' => '/^(?!--).*/',
				'message' => 'class_name_double_hyphen',
			],
			[
				'pattern' => '/^(?!-[0-9])/',
				'message' => 'class_name_starts_with_hyphen_digit',
			],
		];

		foreach ( $regexes as $rule ) {
			if ( ! preg_match( $rule['pattern'], $label ) ) {
				return [
					'is_valid' => false,
					'error_message' => $rule['message'],
				];
			}
		}
		return [
			'is_valid' => true,
			'error_message' => null,
		];
	}

	private function validate_meta( $meta ): Parse_Result {
		$result = Parse_Result::make();

		if ( ! is_array( $meta ) ) {
			$result->errors()->add( 'meta', 'invalid_type' );

			return $result;
		}

		if ( ! array_key_exists( 'state', $meta ) || ! in_array( $meta['state'], self::VALID_STATES, true ) ) {
			$result->errors()->add( 'state', 'missing_or_invalid_value' );

			return $result;
		}

		// TODO: Validate breakpoint based on the existing breakpoints in the system [EDS-528]
		if ( ! isset( $meta['breakpoint'] ) || ! is_string( $meta['breakpoint'] ) ) {
			$result->errors()->add( 'breakpoint', 'missing_or_invalid_value' );

			return $result;
		}

		return $result;
	}

	private function validate_custom_css( array $variant ): Parse_Result {
		$result = Parse_Result::make();

		if ( ! empty( $variant['custom_css']['raw'] ) && (
				! is_string( $variant['custom_css']['raw'] ) ||
				null === Utils::decode_string( $variant['custom_css']['raw'], null )
			)
		) {
			$result->errors()->add( 'custom_css', 'invalid_type' );
		}

		return $result;
	}

	private function sanitize_meta( $meta ) {
		if ( ! is_array( $meta ) ) {
			return [];
		}

		if ( isset( $meta['breakpoint'] ) ) {
			$meta['breakpoint'] = sanitize_key( $meta['breakpoint'] );
		}

		return $meta;
	}

	private function sanitize_custom_css( array $variant ) {
		if ( empty( $variant['custom_css']['raw'] ) ) {
			return null;
		}

		$custom_css = Utils::decode_string( $variant['custom_css']['raw'] );
		$custom_css = sanitize_textarea_field( $custom_css );
		$custom_css = [ 'raw' => Utils::encode_string( $custom_css ) ];

		return empty( $custom_css['raw'] ) ? null : $custom_css;
	}

	/**
	 * @param array $style
	 * the style object to sanitize
	 */
	private function sanitize( array $style ): Parse_Result {
		$props_parser = Props_Parser::make( $this->schema );

		if ( isset( $style['label'] ) ) {
			$style['label'] = sanitize_text_field( $style['label'] );
		}

		if ( isset( $style['id'] ) ) {
			$style['id'] = sanitize_key( $style['id'] );
		}

		if ( ! empty( $style['variants'] ) ) {
			foreach ( $style['variants'] as $variant_index => $variant ) {
				$style['variants'][ $variant_index ]['props'] = $props_parser->sanitize( $variant['props'] )->unwrap();
				$style['variants'][ $variant_index ]['meta'] = $this->sanitize_meta( $variant['meta'] );
				$style['variants'][ $variant_index ]['custom_css'] = $this->sanitize_custom_css( $variant );
			}
		}

		return Parse_Result::make()->wrap( $style );
	}

	/**
	 * @param array $style
	 * the style object to parse
	 */
	public function parse( array $style ): Parse_Result {
		$validate_result = $this->validate( $style );

		$sanitize_result = $this->sanitize( $validate_result->unwrap() );

		$sanitize_result->errors()->merge( $validate_result->errors() );

		return $sanitize_result;
	}
}
