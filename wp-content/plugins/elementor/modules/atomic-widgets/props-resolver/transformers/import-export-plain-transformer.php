<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import_Export_Plain_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ) {
		$prop_type = $context->get_prop_type();

		return $prop_type::generate( $value, $context->is_disabled() );
	}
}
