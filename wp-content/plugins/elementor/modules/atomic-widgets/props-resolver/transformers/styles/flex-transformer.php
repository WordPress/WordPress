<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Flex_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		$grow = $value['flexGrow'] ?? null;
		$shrink = $value['flexShrink'] ?? null;
		$basis = $value['flexBasis'] ?? null;

		$has_grow = null !== $grow && '' !== $grow;
		$has_shrink = null !== $shrink && '' !== $shrink;
		$has_basis = null !== $basis && '' !== $basis;

		if ( ! $has_grow && ! $has_shrink && ! $has_basis ) {
			return null;
		}

		$basis_value = $this->transform_basis_value( $basis );

		if ( $has_grow && $has_shrink && $has_basis ) {
			return "{$grow} {$shrink} {$basis_value}";
		}

		if ( $has_grow && $has_shrink && ! $has_basis ) {
			return "{$grow} {$shrink}";
		}

		if ( $has_grow && ! $has_shrink && $has_basis ) {
			return "{$grow} 1 {$basis_value}";
		}

		if ( ! $has_grow && $has_shrink && $has_basis ) {
			return "0 {$shrink} {$basis_value}";
		}

		if ( $has_grow && ! $has_shrink && ! $has_basis ) {
			return "{$grow}";
		}

		if ( ! $has_grow && $has_shrink && ! $has_basis ) {
			return "0 {$shrink}";
		}

		if ( ! $has_grow && ! $has_shrink && $has_basis ) {
			return "0 1 {$basis_value}";
		}

		return null;
	}

	/**
	 * Transform basis value to string format
	 *
	 * @param mixed $basis The basis value
	 * @return string
	 */
	private function transform_basis_value( $basis ) {
		if ( is_array( $basis ) && isset( $basis['size'] ) ) {
			$unit = $basis['unit'] ?? '';
			return $basis['size'] . $unit;
		}

		return (string) $basis;
	}
}
