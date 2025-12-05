<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Base;

use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Concerns;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Plain_Prop_Type implements Transformable_Prop_Type {
	const KIND = 'plain';

	use Concerns\Has_Default;
	use Concerns\Has_Generate;
	use Concerns\Has_Meta;
	use Concerns\Has_Required_Setting;
	use Concerns\Has_Settings;
	use Concerns\Has_Transformable_Validation;

	private ?array $dependencies = null;

	/**
	 * @return static
	 */
	public static function make() {
		return new static();
	}

	public function get_type(): string {
		return 'plain';
	}

	public function validate( $value ): bool {
		if ( is_null( $value ) || ( $this->is_transformable( $value ) && empty( $value['value'] ) ) ) {
			return ! $this->is_required();
		}

		return (
			$this->is_transformable( $value ) &&
			$this->validate_value( $value['value'] )
		);
	}

	public function sanitize( $value ) {
		$value['value'] = $this->sanitize_value( $value['value'] );

		return $value;
	}

	public function jsonSerialize(): array {
		return [
			'kind' => static::KIND,
			'key' => static::get_key(),
			'default' => $this->get_default(),
			'meta' => (object) $this->get_meta(),
			'settings' => (object) $this->get_settings(),
			'dependencies' => $this->get_dependencies(),
		];
	}

	abstract public static function get_key(): string;

	abstract protected function validate_value( $value ): bool;

	abstract protected function sanitize_value( $value );

	public function set_dependencies( ?array $dependencies ): self {
		$this->dependencies = empty( $dependencies ) ? null : $dependencies;

		return $this;
	}

	public function get_dependencies(): ?array {
		return $this->dependencies;
	}
}
