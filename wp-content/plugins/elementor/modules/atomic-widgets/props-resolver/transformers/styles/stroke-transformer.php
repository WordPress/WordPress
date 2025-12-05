<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Multi_Props;
use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Stroke_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		return Multi_Props::generate( [
			'-webkit-text-stroke' => $value['width'] . ' ' . $value['color'],
			'stroke' => $value['color'],
			'stroke-width' => $value['width'],
		] );
	}
}
