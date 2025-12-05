<?php

namespace Elementor\Modules\Variables\Classes;

use Elementor\Modules\AtomicWidgets\PropTypes\Base\Array_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Union_Prop_Type;
use Elementor\Modules\Variables\PropTypes\Color_Variable_Prop_Type;
use Elementor\Modules\Variables\PropTypes\Font_Variable_Prop_Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Style_Schema {
	public function augment( array $schema ): array {
		foreach ( $schema as $key => $prop_type ) {
			$schema[ $key ] = $this->update( $prop_type );
		}

		if ( isset( $schema['font-family'] ) ) {
			$schema['font-family'] = $this->update_font_family( $schema['font-family'] );
		}

		return $schema;
	}

	private function update( $prop_type ) {
		if ( $prop_type instanceof Color_Prop_Type ) {
			return $this->update_color( $prop_type );
		}

		if ( $prop_type instanceof Union_Prop_Type ) {
			return $this->update_union( $prop_type );
		}

		if ( $prop_type instanceof Object_Prop_Type ) {
			return $this->update_object( $prop_type );
		}

		if ( $prop_type instanceof Array_Prop_Type ) {
			return $this->update_array( $prop_type );
		}

		return $prop_type;
	}

	private function update_font_family( String_Prop_Type $prop_type ): Union_Prop_Type {
		return Union_Prop_Type::create_from( $prop_type )
			->add_prop_type( Font_Variable_Prop_Type::make() );
	}

	private function update_color( Color_Prop_Type $color_prop_type ): Union_Prop_Type {
		return Union_Prop_Type::create_from( $color_prop_type )
			->add_prop_type( Color_Variable_Prop_Type::make() );
	}

	private function update_array( Array_Prop_Type $array_prop_type ): Array_Prop_Type {
		return $array_prop_type->set_item_type(
			$this->update( $array_prop_type->get_item_type() )
		);
	}

	private function update_object( Object_Prop_Type $object_prop_type ): Object_Prop_Type {
		return $object_prop_type->set_shape(
			$this->augment( $object_prop_type->get_shape() )
		);
	}

	private function update_union( Union_Prop_Type $union_prop_type ): Union_Prop_Type {
		$new_union = Union_Prop_Type::make();
		$dependencies = $union_prop_type->get_dependencies();
		$new_union->set_dependencies( $dependencies );

		foreach ( $union_prop_type->get_prop_types() as $prop_type ) {
			$updated = $this->update( $prop_type );

			if ( $updated instanceof Union_Prop_Type ) {
				foreach ( $updated->get_prop_types() as $updated_prop_type ) {
					$new_union->add_prop_type( $updated_prop_type );
				}

				continue;
			}

			$new_union->add_prop_type( $updated );
		}

		return $new_union;
	}
}
