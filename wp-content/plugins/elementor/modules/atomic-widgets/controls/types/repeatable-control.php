<?php
namespace Elementor\Modules\AtomicWidgets\Controls\Types;

use Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Repeatable_Control extends Atomic_Control_Base {

	private string $child_control_type;
	private object $child_control_props;
	private bool $show_duplicate = true;
	private bool $show_toggle = true;
	private string $repeater_label;
	private ?object $initial_values;
	private ?string $pattern_label;
	private ?string $placeholder;
	private ?string $prop_key = '';

	public function get_type(): string {
		return 'repeatable';
	}

	public function set_child_control_type( $control_type ): self {
		$this->child_control_type = $control_type;

		return $this;
	}

	public function set_child_control_props( $control_props ): self {
		$this->child_control_props = (object) $control_props;

		return $this;
	}

	public function hide_duplicate(): self {
		$this->show_duplicate = false;

		return $this;
	}

	public function hide_toggle(): self {
		$this->show_toggle = false;

		return $this;
	}

	public function set_initialValues( $initial_values ): self {
		$this->initial_values = (object) $initial_values;

		return $this;
	}

	public function set_patternLabel( $pattern_label ): self {
		$this->pattern_label = $pattern_label;

		return $this;
	}

	public function set_repeaterLabel( string $label ): self {
		$this->repeater_label = $label;

		return $this;
	}

	public function set_placeholder( string $placeholder ): self {
		$this->placeholder = $placeholder;

		return $this;
	}

	public function set_prop_key( string $prop_key ): self {
		$this->prop_key = $prop_key;

		return $this;
	}

	public function get_props(): array {
		return [
			'childControlType'   => $this->child_control_type,
			'childControlProps'  => $this->child_control_props,
			'showDuplicate'      => $this->show_duplicate,
			'showToggle'         => $this->show_toggle,
			'initialValues'      => $this->initial_values,
			'patternLabel'       => $this->pattern_label,
			'repeaterLabel'      => $this->repeater_label,
			'placeholder'        => $this->placeholder,
			'propKey'            => $this->prop_key,
		];
	}
}
