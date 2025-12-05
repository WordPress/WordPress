<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transition_Transformer extends Transformer_Base {
	const EMPTY_STRING = '';

	public function transform( $transitions, Props_Resolver_Context $context ) {
		if ( ! is_array( $transitions ) ) {
			return self::EMPTY_STRING;
		}

		$transition_strings = array_map( [ $this, 'map_to_transition_string' ], $transitions );
		$valid_transitions = array_filter( $transition_strings );

		return implode( ', ', $valid_transitions );
	}

	private function map_to_transition_string( $transition ): string {
		if ( empty( $transition['selection'] ) || empty( $transition['size'] ) ) {
			return self::EMPTY_STRING;
		}

		$selection = $transition['selection'];
		$size = $transition['size'];

		if ( empty( $selection['value'] ) ) {
			return self::EMPTY_STRING;
		}

		return trim( "{$selection['value']} {$size}" );
	}
}
