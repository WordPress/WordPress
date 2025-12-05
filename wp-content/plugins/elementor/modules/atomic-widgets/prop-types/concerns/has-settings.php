<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Concerns;

use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Has_Settings {
	protected array $settings = [];

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function setting( $key, $value ) {
		$this->settings[ $key ] = $value;

		return $this;
	}

	public function get_settings(): array {
		return $this->settings;
	}

	public function get_setting( string $key, $default_value = null ) {
		return array_key_exists( $key, $this->settings ) ? $this->settings[ $key ] : $default_value;
	}
}
