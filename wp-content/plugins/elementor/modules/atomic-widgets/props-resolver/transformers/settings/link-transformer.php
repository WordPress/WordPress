<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Settings;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Link_Transformer extends Transformer_Base {
	public function transform( $value, Props_Resolver_Context $context ): ?array {
		$url = $this->extract_url( $value );

		$link_attrs = [
			'href' => $url,
			'target' => $value['isTargetBlank'] ? '_blank' : '_self',
		];

		return array_filter( $link_attrs );
	}

	private function extract_url( $value ): ?string {
		$destination = $value['destination'];
		$post = is_numeric( $destination ) ? get_post( $destination ) : null;

		return $post ? $post->guid : $destination;
	}
}
