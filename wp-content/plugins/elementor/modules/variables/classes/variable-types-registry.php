<?php

namespace Elementor\Modules\Variables\Classes;

use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type;
use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Variable_Types_Registry {
	private array $types = [];

	public function register( string $key, Transformable_Prop_Type $prop_type ): void {
		if ( isset( $this->types[ $key ] ) ) {
			throw new InvalidArgumentException( esc_html( "Key '{$key}' is already registered." ) );
		}

		$this->types[ $key ] = $prop_type;
	}

	public function get( $key ) {
		return $this->types[ $key ] ?? null;
	}

	public function all(): array {
		return $this->types;
	}
}
