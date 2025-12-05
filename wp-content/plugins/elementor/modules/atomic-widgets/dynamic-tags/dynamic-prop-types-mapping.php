<?php

namespace Elementor\Modules\AtomicWidgets\DynamicTags;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Transformable_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Src_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Url_Prop_Type;
use Elementor\Modules\DynamicTags\Module as V1_Dynamic_Tags_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dynamic_Prop_Types_Mapping {

	public static function make(): self {
		return new static();
	}

	/**
	 * @param array<string, Prop_Type> $schema
	 *
	 * @return array<string, Prop_Type>
	 */
	public function get_modified_prop_types( array $schema ): array {
		$result = [];

		foreach ( $schema as $key => $prop_type ) {
			if ( ! ( $prop_type instanceof Prop_Type ) ) {
				$result[ $key ] = $prop_type;

				continue;
			}

			$result[ $key ] = $this->get_modified_prop_type( $prop_type );
		}

		return $result;
	}

	/**
	 * Change prop type into a union prop type if the original prop type supports dynamic tags.
	 *
	 * @param Prop_Type $prop_type
	 *
	 * @return Prop_Type|Union_Prop_Type
	 */
	private function get_modified_prop_type( Prop_Type $prop_type ) {
		$transformable_prop_types = $prop_type instanceof Union_Prop_Type ?
			$prop_type->get_prop_types() :
			[ $prop_type ];

		$categories = [];

		foreach ( $transformable_prop_types as $transformable_prop_type ) {
			if ( $transformable_prop_type instanceof Object_Prop_Type ) {
				$transformable_prop_type->set_shape(
					$this->get_modified_prop_types( $transformable_prop_type->get_shape() )
				);
			}

			if ( $transformable_prop_type instanceof Array_Prop_Type ) {
				$transformable_prop_type->set_item_type(
					$this->get_modified_prop_type( $transformable_prop_type->get_item_type() )
				);
			}

			// When the prop type is originally a union, we need to merge all the categories
			// of each prop type in the union and create one dynamic prop type with all the categories.
			$categories = array_merge( $categories, $this->get_related_categories( $transformable_prop_type ) );
		}

		if ( empty( $categories ) ) {
			return $prop_type;
		}

		$dynamic_prop_type = Dynamic_Prop_Type::make()->categories( $categories );
		$union_prop_type = $prop_type;

		if ( $prop_type instanceof Transformable_Prop_Type ) {
			$union_prop_type = Union_Prop_Type::create_from( $prop_type );
		}

		$union_prop_type->add_prop_type( $dynamic_prop_type );

		return $union_prop_type;
	}

	private function get_related_categories( Transformable_Prop_Type $prop_type ): array {
		if ( ! $prop_type->get_meta_item( Dynamic_Prop_Type::META_KEY, true ) ) {
			return [];
		}

		if ( $prop_type instanceof Number_Prop_Type ) {
			return [ V1_Dynamic_Tags_Module::NUMBER_CATEGORY ];
		}

		if ( $prop_type instanceof Image_Src_Prop_Type ) {
			return [ V1_Dynamic_Tags_Module::IMAGE_CATEGORY ];
		}

		if ( $prop_type instanceof String_Prop_Type && empty( $prop_type->get_enum() ) ) {
			return [ V1_Dynamic_Tags_Module::TEXT_CATEGORY ];
		}

		if ( $prop_type instanceof Url_Prop_Type ) {
			return [ V1_Dynamic_Tags_Module::URL_CATEGORY ];
		}

		return [];
	}
}
