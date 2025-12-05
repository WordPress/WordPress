<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\PropsResolver\Multi_Props;
use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;
use phpDocumentor\Reflection\Types\Callable_;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Multi_Props_Transformer extends Transformer_Base {
	private $key_generator;

	private array $keys;

	public function __construct( array $keys, callable $key_generator ) {
		$this->keys = $keys;
		$this->key_generator = $key_generator;
	}

	public function transform( $value, Props_Resolver_Context $context ) {
		$values = Collection::make( $this->keys )
			->filter( fn ( $key ) => isset( $value[ $key ] ) )
			->map_with_keys( function( $key ) use ( $value, $context ) {
				$new_key = call_user_func( $this->key_generator, $context->get_key(), $key );
				$new_value = $value[ $key ];

				return [ $new_key => $new_value ];
			} )
			->all();

		return Multi_Props::generate( $values );
	}
}
