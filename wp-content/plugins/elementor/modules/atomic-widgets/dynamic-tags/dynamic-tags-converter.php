<?php

namespace Elementor\Modules\AtomicWidgets\DynamicTags;

use Elementor\Modules\AtomicWidgets\Image\Placeholder_Image;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Base\Plain_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Query_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dynamic_Tags_Converter {

	/**
	 * @param array $control
	 * @return Plain_Prop_Type|Object_Prop_Type|null
	 */
	public static function convert_control_to_prop_type( array $control ) {
		$control_type = $control['type'];

		switch ( $control_type ) {
			case 'text':
			case 'textarea':
				$prop_type = String_Prop_Type::make()
					->default( $control['default'] ?? null );
				break;

			case 'select':
				$prop_type = String_Prop_Type::make()
					->default( $control['default'] ?? null );

				if ( ! isset( $control['collection_id'] ) || empty( $control['collection_id'] ) ) {
					$prop_type->enum( array_keys( $control['options'] ?? [] ) );
				}
				break;

			case 'date_time':
				$prop_type = String_Prop_Type::make()
					->default( $control['default'] ?? null );
				break;

			case 'number':
				$prop_type = Number_Prop_Type::make()
					->set_required( $control['required'] ?? false )
					->default( $control['default'] ?? null );
				break;

			case 'switcher':
				$default = $control['default'];

				$prop_type = Boolean_Prop_Type::make()
					->default( 'yes' === $default || true === $default );
				break;

			case 'choose':
				$prop_type = String_Prop_Type::make()
					->default( $control['default'] ?? null )
					->enum( array_keys( $control['options'] ?? [] ) );
				break;

			case 'query':
				$prop_type = Query_Prop_Type::make()
					->set_required( $control['required'] ?? false )
					->default( $control['default'] ?? null );
				break;

			case 'media':
				$prop_type = Image_Prop_Type::make()
					->default_url( Placeholder_Image::get_placeholder_image() )
					->default_size( 'full' )
					->set_shape_meta( 'src', [ 'isDynamic' => true ] );
				break;

			default:
				return null;
		}

		$prop_type->set_dependencies( self::create_dependencies_from_condition( $control['condition'] ?? null ) );

		return $prop_type;
	}

	private static function create_dependencies_from_condition( $condition ): ?array {
		if ( ! is_array( $condition ) || empty( $condition ) ) {
			return null;
		}

		$manager = Dependency_Manager::make( Dependency_Manager::RELATION_AND );

		foreach ( $condition as $raw_key => $value ) {
			$is_negated = false !== strpos( (string) $raw_key, '!' );
			$key = rtrim( (string) $raw_key, '!' );
			$path = self::parse_condition_path( $key );

			if ( is_array( $value ) ) {
				$manager->where( [
					'operator' => $is_negated ? 'nin' : 'in',
					'path' => $path,
					'value' => $value,
				] );
				continue;
			}

			$manager->where( [
				'operator' => $is_negated ? 'ne' : 'eq',
				'path' => $path,
				'value' => $value,
			] );
		}

		return $manager->get();
	}

	private static function parse_condition_path( string $key ): array {
		if ( false === strpos( $key, '[' ) ) {
			return [ $key ];
		}

		$key = str_replace( ']', '', $key );
		$tokens = explode( '[', $key );

		return array_values( array_filter( $tokens, static fn( $t ) => '' !== $t ) );
	}
}
