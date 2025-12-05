<?php

namespace Elementor\Modules\AtomicWidgets\ImportExport\Modifiers;

use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Styles_Ids_Modifier {
	private Collection $old_to_new_ids;

	public static function make() {
		return new self();
	}

	public function run( array $element ) {
		$this->old_to_new_ids = Collection::make();

		$element = $this->replace_styles_ids( $element );
		$element = $this->replace_references( $element );

		return $element;
	}

	private function replace_styles_ids( array $element ) {
		if ( empty( $element['styles'] ) || empty( $element['id'] ) ) {
			return $element;
		}

		$styles = Collection::make( $element['styles'] )->map_with_keys( function ( $style, $id ) use ( $element ) {
			$style['id'] = $this->generate_id( $element['id'], $id );

			return [ $style['id'] => $style ];
		} )->all();

		$element['styles'] = $styles;

		return $element;
	}

	private function replace_references( array $element ) {
		if ( empty( $element['settings'] ) ) {
			return $element;
		}

		$element['settings'] = Collection::make( $element['settings'] )->map( function ( $setting ) {
			if ( ! $setting || ! Classes_Prop_Type::make()->validate( $setting ) ) {
				return $setting;
			}

			$setting['value'] = Collection::make( $setting['value'] )
				->map( fn( $style_id ) => $this->old_to_new_ids->get( $style_id ) ?? $style_id )
				->all();

			return $setting;
		} )->all();

		return $element;
	}

	private function generate_id( $element_id, $old_id ): string {
		$id = Utils::generate_id( "e-{$element_id}-", $this->old_to_new_ids->values() );

		$this->old_to_new_ids[ $old_id ] = $id;

		return $id;
	}
}
