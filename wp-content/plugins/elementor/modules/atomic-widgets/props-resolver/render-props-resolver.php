<?php

namespace Elementor\Modules\AtomicWidgets\PropsResolver;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Render_Props_Resolver extends Props_Resolver {
	/**
	 * Each transformer can return a value that is also a transformable value,
	 * which means that it can be transformed again by another transformer.
	 * This constant defines the maximum depth of transformations to avoid infinite loops.
	 */
	const TRANSFORM_DEPTH_LIMIT = 3;

	const CONTEXT_SETTINGS = 'settings';
	const CONTEXT_STYLES = 'styles';

	public static function for_styles(): self {
		return static::instance( self::CONTEXT_STYLES );
	}

	public static function for_settings(): self {
		return static::instance( self::CONTEXT_SETTINGS );
	}

	public function resolve( array $schema, array $props ): array {
		$resolved = [];

		foreach ( $schema as $key => $prop_type ) {
			if ( ! ( $prop_type instanceof Prop_Type ) ) {
				continue;
			}

			$transformed = $this->resolve_item(
				$props[ $key ] ?? $prop_type->get_default(),
				$key,
				$prop_type
			);

			if ( Multi_Props::is( $transformed ) ) {
				$resolved = array_merge( $resolved, Multi_Props::get_value( $transformed ) );

				continue;
			}

			$resolved[ $key ] = $transformed;
		}

		return $resolved;
	}

	protected function resolve_item( $value, $key, Prop_Type $prop_type, int $depth = 0 ) {
		if ( null === $value ) {
			return null;
		}

		if ( ! $this->is_transformable( $value ) ) {
			return $value;
		}

		if ( $depth >= self::TRANSFORM_DEPTH_LIMIT ) {
			return null;
		}

		if ( isset( $value['disabled'] ) && true === $value['disabled'] ) {
			return null;
		}

		$transformed = $this->transform( $value, $key, $prop_type );

		return $this->resolve_item( $transformed, $key, $prop_type, $depth + 1 );
	}
}
