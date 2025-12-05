<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns;

use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Has_Default {
	protected $default = null;

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function default( $value ) {
		$this->default = static::generate( $value );

		return $this;
	}

	public function get_default() {
		return $this->default;
	}

	abstract public static function generate( $value, $disable = false ): array;
}
