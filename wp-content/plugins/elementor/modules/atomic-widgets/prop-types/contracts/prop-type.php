<?php

namespace Elementor\Modules\AtomicWidgets\PropTypes\Contracts;

use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

interface Prop_Type extends \JsonSerializable {
	public static function get_key(): string;
	public function get_type(): string;
	public function get_default();
	public function validate( $value ): bool;
	public function sanitize( $value );
	public function get_meta(): array;
	public function get_meta_item( string $key, $default_value = null );
	public function get_settings(): array;
	public function get_setting( string $key, $default_value = null );
	public function set_dependencies( ?array $dependencies ): self;
	public function get_dependencies(): ?array;
}
