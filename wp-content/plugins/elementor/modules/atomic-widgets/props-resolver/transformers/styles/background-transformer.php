<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Multi_Props;
use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		$overlay = $value['background-overlay'] ?? [];
		$color = $value['color'] ?? null;
		$clip = $value['clip'] ?? null;

		return Multi_Props::generate( array_merge( $overlay, [
			'background-color' => $color,
			'background-clip' => $clip,
		] ) );
	}
}
