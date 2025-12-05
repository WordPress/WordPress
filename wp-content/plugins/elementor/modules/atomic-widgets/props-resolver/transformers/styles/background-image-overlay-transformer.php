<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver\Transformers\Styles;

use Elementor\Modules\AtomicWidgets\PropsResolver\Props_Resolver_Context;
use Elementor\Modules\AtomicWidgets\PropsResolver\Transformer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Background_Image_Overlay_Transformer extends Transformer_Base {
	const DEFAULT_IMAGE = 'none';
	const DEFAULT_REPEAT = 'repeat';
	const DEFAULT_ATTACHMENT = 'scroll';
	const DEFAULT_SIZE = 'auto auto';
	const DEFAULT_POSITION = '0% 0%';

	public function transform( $value, Props_Resolver_Context $context ) {
		if ( ! isset( $value['image'] ) ) {
			return '';
		}

		$image_url = $value['image']['src'];

		return [
			'src' => "url(\"$image_url\")",
			'repeat' => $value['repeat'] ?? null,
			'attachment' => $value['attachment'] ?? null,
			'size' => $value['size'] ?? null,
			'position' => $value['position'] ?? null,
		];
	}
}
