<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Filter_Transformer extends Transformer_Base {
	public function transform( $filters, Props_Resolver_Context $context ) {
		$filter_strings = array_map( [ $this, 'map_to_filter_string' ], $filters );
		return implode( ' ', $filter_strings );
	}

	private function map_to_filter_string( $filter ): string {
		$func = $filter['func'];
		$args = $filter['args'];

		if ( 'drop-shadow' === $func ) {
			$x_axis = $args['xAxis'] ?? '0px';
			$y_axis = $args['yAxis'] ?? '0px';
			$blur   = $args['blur'] ?? '10px';
			$color  = $args['color'] ?? 'transparent';
			return "drop-shadow({$x_axis} {$y_axis} {$blur} {$color})";
		}

		return $func . '(' . $args['size'] . ')';
	}
}
