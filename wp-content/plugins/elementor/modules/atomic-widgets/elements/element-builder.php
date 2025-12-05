<?php

namespace Elementor\Modules\AtomicWidgets\Elements;

class Element_Builder {
	protected $element_type;
	protected $settings = [];
	protected $is_locked = false;
	protected $children = [];
	protected $editor_settings = [];

	public static function make( string $element_type ) {
		return new self( $element_type );
	}

	private function __construct( string $element_type ) {
		$this->element_type = $element_type;
	}

	public function settings( array $settings ) {
		$this->settings = $settings;
		return $this;
	}

	public function is_locked( $is_locked ) {
		$this->is_locked = $is_locked;
		return $this;
	}

	public function editor_settings( array $editor_settings ) {
		$this->editor_settings = $editor_settings;
		return $this;
	}

	public function children( array $children ) {
		$this->children = $children;
		return $this;
	}

	public function build() {
		$element_data = [
			'elType' => $this->element_type,
			'settings' => $this->settings,
			'isLocked' => $this->is_locked,
			'editor_settings' => $this->editor_settings,
			'elements' => $this->children,
		];

		return $element_data;
	}
}
