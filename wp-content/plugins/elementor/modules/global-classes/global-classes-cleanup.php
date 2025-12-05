<?php

namespace Elementor\Modules\GlobalClasses;

use Elementor\Core\Base\Document;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\GlobalClasses\Utils\Atomic_Elements_Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Global_Classes_Cleanup {

	public function register_hooks() {
		add_action(
			'elementor/global_classes/update',
			fn( $context, $new_value, $prev_value ) => $this->on_classes_update( $new_value, $prev_value ),
			10,
			3
		);
	}

	private function on_classes_update( $new_value, $prev_value ) {
		$deleted_classes_ids = $this->get_deleted_classes_ids( $new_value, $prev_value );

		if ( ! empty( $deleted_classes_ids ) ) {
			Plugin::$instance->db->iterate_elementor_documents(
				fn( $document, $elements_data ) => $this->unapply_deleted_classes( $document, $elements_data, $deleted_classes_ids )
			);
		}
	}

	private function get_deleted_classes_ids( $new_value, $prev_value ) {
		$prev_ids = array_keys( $prev_value['items'] );
		$new_ids = array_keys( $new_value['items'] );

		return array_values(
			array_diff( $prev_ids, $new_ids )
		);
	}

	private function unapply_deleted_classes( $document, $elements_data, $deleted_classes_ids ) {
		$elements_data = Plugin::$instance->db->iterate_data( $elements_data, function( $element_data ) use ( $deleted_classes_ids ) {
			$element_type = Atomic_Elements_Utils::get_element_type( $element_data );
			$element_instance = Atomic_Elements_Utils::get_element_instance( $element_type );

			if ( ! Atomic_Elements_Utils::is_atomic_element( $element_instance ) ) {
				return $element_data;
			}

			/** @var Atomic_Element_Base | Atomic_Widget_Base $element_instance */
			return $this->unapply_classes_from_element( $element_instance->get_props_schema(), $element_data, $deleted_classes_ids );
		} );

		$document->update_json_meta( Document::ELEMENTOR_DATA_META_KEY, $elements_data );
	}

	private function unapply_classes_from_element( $props_schema, $element_data, $deleted_classes_ids ) {
		foreach ( $props_schema as $prop ) {
			if ( ! Atomic_Elements_Utils::is_classes_prop( $prop ) ) {
				continue;
			}

			$current_classes = $element_data['settings'][ $prop->get_key() ] ?? null;

			if ( ! $current_classes ) {
				continue;
			}

			$element_data['settings'][ $prop->get_key() ]['value'] = Collection::make( $current_classes['value'] )
				->filter( fn( $class_name ) => ! in_array( $class_name, $deleted_classes_ids, true ) )
				->values();
		}

		return $element_data;
	}
}
