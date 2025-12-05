<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Image_Src_Transformer extends Transformer_Base {

	/**
	 * This transformer (or rather this prop type) exists only to support dynamic images.
	 * Currently, the dynamic tags that return images return it with id & url no matter
	 * what, so we need to keep the same structure in the props.
	 */
	public function transform( $value, Props_Resolver_Context $context ) {
		return [
			'id' => isset( $value['id'] ) ? (int) $value['id'] : null,
			'url' => $value['url'] ?? null,
		];
	}
}
