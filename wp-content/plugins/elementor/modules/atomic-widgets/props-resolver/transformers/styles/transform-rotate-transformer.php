<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Rotate_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ): string {
		$default_rotate = '0deg';

		return sprintf(
			'rotateX(%s) rotateY(%s) rotateZ(%s)',
			$value['x'] ?? $default_rotate,
			$value['y'] ?? $default_rotate,
			$value['z'] ?? $default_rotate
		);
	}
}
