<?php

namespace Elementor\Modules\AtomicWidgets\ImportExport;

use Elementor\Modules\AtomicWidgets\Elements\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\ImportExport\Modifiers\Settings_Props_Modifier;
use Elementor\Modules\AtomicWidgets\ImportExport\Modifiers\Styles_Ids_Modifier;
use Elementor\Modules\AtomicWidgets\ImportExport\Modifiers\Styles_Props_Modifier;
use Elementor\Modules\AtomicWidgets\PropsResolver\Import_Export_Props_Resolver;
use Elementor\Modules\AtomicWidgets\Styles\Style_Schema;
use Elementor\Modules\AtomicWidgets\Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Import_Export {
	public function register_hooks() {
		add_filter(
			'elementor/template_library/sources/local/import/elements',
			fn( $elements ) => $this->run( $elements, Import_Export_Props_Resolver::for_import() )
		);

		add_filter(
			'elementor/template_library/sources/cloud/import/elements',
			fn( $elements ) => $this->run( $elements, Import_Export_Props_Resolver::for_import() )
		);

		add_filter(
			'elementor/template_library/sources/local/export/elements',
			fn( $elements ) => $this->run( $elements, Import_Export_Props_Resolver::for_export() )
		);

		add_filter(
			'elementor/document/element/replace_id',
			fn( $element ) => $this->replace_styles_ids( $element )
		);
	}

	private function run( $elements, Import_Export_Props_Resolver $props_resolver ) {
		if ( empty( $elements ) || ! is_array( $elements ) ) {
			return $elements;
		}

		return Plugin::$instance->db->iterate_data( $elements, function ( $element ) use ( $props_resolver ) {
			$element_instance = Plugin::$instance->elements_manager->create_element_instance( $element );

			/** @var Atomic_Element_Base | Atomic_Widget_Base $element_instance */
			if ( ! Utils::is_atomic( $element_instance ) ) {
				return $element;
			}

			$runners = [
				Settings_Props_Modifier::make( $props_resolver, $element_instance::get_props_schema() ),
				Styles_Props_Modifier::make( $props_resolver, Style_Schema::get() ),
			];

			foreach ( $runners as $runner ) {
				$element = $runner->run( $element );
			}

			return $element;
		} );
	}

	private function replace_styles_ids( $element ) {
		if ( empty( $element ) || ! is_array( $element ) ) {
			return $element;
		}

		$element_instance = Plugin::$instance->elements_manager->create_element_instance( $element );

		/** @var Atomic_Element_Base | Atomic_Widget_Base $element_instance */
		if ( ! Utils::is_atomic( $element_instance ) ) {
			return $element;
		}

		return Styles_Ids_Modifier::make()->run( $element );
	}
}
