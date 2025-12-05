<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Scale_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ): string {
		return sprintf( 'scale3d(%s, %s, %s)', $value['x'] ?? 1, $value['y'] ?? 1, $value['z'] ?? 1 );
	}
}
