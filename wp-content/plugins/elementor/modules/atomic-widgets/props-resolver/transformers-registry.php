<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver;

use Elementor\Core\Utils\Collection;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Transformers_Registry extends Collection {
	private ?Transformer_Base $fallback = null;

	public function register( string $key, Transformer_Base $transformer ): self {
		$this->items[ $key ] = $transformer;

		return $this;
	}

	public function register_fallback( Transformer_Base $transformer ): self {
		$this->fallback = $transformer;

		return $this;
	}

	public function get( $key, $fallback = null ) {
		return parent::get( $key, $fallback ?? $this->fallback );
	}
}
