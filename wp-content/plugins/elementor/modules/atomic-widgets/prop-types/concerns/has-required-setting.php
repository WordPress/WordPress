<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns;

use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Has_Required_Setting {
	protected function is_required(): bool {
		return $this->get_setting( 'required', false );
	}

	public function required() {
		$this->setting( 'required', true );

		return $this;
	}

	public function optional() {
		$this->setting( 'required', false );

		return $this;
	}

	public function set_required( bool $required ) {
		$this->setting( 'required', $required );

		return $this;
	}

	abstract public function get_setting( string $key, $default_value = null );

	abstract public function setting( $key, $value );
}
