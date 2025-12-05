<?php
namespace Elementor\Modules\AtomicWidgets\Controls\Types;

use Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Toggle_Control extends Atomic_Control_Base {
	private array $options = [];
	private bool $full_width = false;
	private string $size = 'tiny';
	private bool $exclusive = true;
	private bool $convert_options = false;

	public function get_type(): string {
		return 'toggle';
	}

	public function add_options( array $control_options ): self {
		$this->options = [];

		foreach ( $control_options as $value => $config ) {
			$this->options[] = [
				'value' => $value,
				'label' => $config['title'] ?? $value,
				'icon' => $config['atomic-icon'] ?? null,
				'showTooltip' => true,
				'exclusive' => false,
			];
		}

		return $this;
	}

	public function set_size( string $size ): self {
		$allowed_sizes = [ 'tiny', 'small', 'medium', 'large' ];

		if ( in_array( $size, $allowed_sizes, true ) ) {
			$this->size = $size;
		}

		return $this;
	}

	public function set_exclusive( bool $exclusive ): self {
		$this->exclusive = $exclusive;

		return $this;
	}

	/**
	 * Whether to convert the v3 options to v4 compatible
	 *
	 * @param bool $convert_options
	 * @return $this
	 */
	public function set_convert_options( bool $convert_options ): self {
		$this->convert_options = $convert_options;

		return $this;
	}

	public function get_props(): array {
		return [
			'options' => $this->options,
			'fullWidth' => $this->full_width,
			'size' => $this->size,
			'exclusive' => $this->exclusive,
			'convertOptions' => $this->convert_options,
		];
	}
}
