<?php
namespace Elementor\Modules\AtomicWidgets\Controls\Types;

use Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Number_Control extends Atomic_Control_Base {
	private ?string $placeholder    = null;
	private ?int $max               = null;
	private ?int $min               = null;
	private ?int $step              = null;
	private ?bool $should_force_int = null;

	public function get_type(): string {
		return 'number';
	}

	public function set_placeholder( string $placeholder ): self {
		$this->placeholder = $placeholder;

		return $this;
	}

	public function set_max( ?int $max ): self {
		$this->max = $max;

		return $this;
	}

	public function set_min( ?int $min ): self {
		$this->min = $min;

		return $this;
	}

	public function set_step( ?int $step ): self {
		$this->step = $step;

		return $this;
	}

	public function set_should_force_int( ?bool $should_force_int ): self {
		$this->should_force_int = $should_force_int ?? false;

		return $this;
	}

	public function get_props(): array {
		return [
			'placeholder'    => $this->placeholder,
			'max'            => $this->max,
			'min'            => $this->min,
			'step'           => $this->step,
			'shouldForceInt' => $this->should_force_int,
		];
	}
}
