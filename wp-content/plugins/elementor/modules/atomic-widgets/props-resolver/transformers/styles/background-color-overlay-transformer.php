<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Color_Overlay_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		$color = $value['color'] ?? '';

		if ( ! $color ) {
			return null;
		}

		return "linear-gradient($color, $color)";
	}
}
