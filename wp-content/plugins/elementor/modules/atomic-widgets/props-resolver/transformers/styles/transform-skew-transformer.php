<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transform_Skew_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ): string {
		$default_skew = '0deg';

		return sprintf(
			'skew(%s, %s)',
			$value['x'] ?? $default_skew,
			$value['y'] ?? $default_skew
		);
	}
}
