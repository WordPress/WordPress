<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Move_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ): string {
		$default_move = '0px';

		return sprintf( 'translate3d(%s, %s, %s)', $value['x'] ?? $default_move, $value['y'] ?? $default_move, $value['z'] ?? $default_move );
	}
}
