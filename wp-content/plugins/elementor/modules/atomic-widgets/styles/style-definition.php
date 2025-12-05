<?php

namespace Elementor\Modules\AtomicWidgets\Styles;

class Style_Definition {
	private string $type = 'class';
	private string $label = '';

	/** @var Style_Variant[] */
	private array $variants = [];

	public static function make(): self {
		return new self();
	}

	public function set_type( string $type ): self {
		$this->type = $type;

		return $this;
	}

	public function set_label( string $label ): self {
		$this->label = $label;

		return $this;
	}

	public function add_variant( Style_Variant $variant ): self {
		$this->variants[] = $variant->build();

		return $this;
	}

	public function build( string $id ): array {
		return [
			'id' => $id,
			'type' => $this->type,
			'label' => $this->label,
			'variants' => $this->variants,
		];
	}
}
