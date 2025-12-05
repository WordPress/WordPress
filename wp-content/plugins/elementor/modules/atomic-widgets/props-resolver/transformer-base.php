<?php
namespace Elementor\Modules\AtomicWidgets\PropsResolver;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Transformer_Base {
	abstract public function transform( $value, Props_Resolver_Context $context );
}
