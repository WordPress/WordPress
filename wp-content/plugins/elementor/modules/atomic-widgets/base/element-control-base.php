<?php

namespace Elementor\Modules\AtomicWidgets\Base;

use JsonSerializable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Element_Control_Base implements JsonSerializable {
	private $label = null;
	private $meta = null;

	abstract public function get_type(): string;

	abstract public function get_props(): array;

	public static function make(): self {
		return new static();
	}

	public function set_label( string $label ): self {
		$this->label = $label;

		return $this;
	}

	public function get_label(): string {
		return $this->label;
	}

	public function set_meta( $meta ): self {
		$this->meta = $meta;

		return $this;
	}

	public function get_meta(): array {
		return $this->meta;
	}

	public function jsonSerialize(): array {
		return [
			'type' => 'element-control',
			'value' => [
				'label' => $this->get_label(),
				'meta' => $this->get_meta(),
				'type' => $this->get_type(),
				'props' => $this->get_props(),
			],
		];
	}
}
