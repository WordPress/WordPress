<?php
namespace Elementor\Modules\KitElementsDefaults\ImportExport\Runners;

use Elementor\Modules\KitElementsDefaults\ImportExport\Import_Export;
use Elementor\Plugin;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\KitElementsDefaults\Module;
use Elementor\App\Modules\ImportExport\Utils as ImportExportUtils;
use Elementor\Modules\KitElementsDefaults\Utils\Settings_Sanitizer;
use Elementor\App\Modules\ImportExport\Runners\Import\Import_Runner_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import extends Import_Runner_Base {
	public static function get_name(): string {
		return 'elements-default-values';
	}

	public function should_import( array $data ) {
		// Together with site-settings.
		return (
			isset( $data['include'] ) &&
			in_array( 'settings', $data['include'], true ) &&
			! empty( $data['site_settings']['settings'] ) &&
			! empty( $data['extracted_directory_path'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$kit = Plugin::$instance->kits_manager->get_active_kit();
		$file_name = Import_Export::FILE_NAME;
		$default_values = ImportExportUtils::read_json_file( "{$data['extracted_directory_path']}/{$file_name}.json" );

		if ( ! $kit || ! $default_values ) {
			return [];
		}

		$element_types = array_keys( Plugin::$instance->elements_manager->get_element_types() );
		$widget_types  = array_keys( Plugin::$instance->widgets_manager->get_widget_types() );

		$types = array_merge( $element_types, $widget_types );

		$sanitizer = new Settings_Sanitizer(
			Plugin::$instance->elements_manager,
			$widget_types
		);

		$default_values = ( new Collection( $default_values ) )
			->filter( function ( $settings, $type ) use ( $types ) {
				return in_array( $type, $types, true );
			} )
			->map( function ( $settings, $type ) use ( $sanitizer, $kit ) {
				return $sanitizer
					->for( $type )
					->using( $settings )
					->remove_invalid_settings()
					->kses_deep()
					->prepare_for_import( $kit )
					->get();
			} )
			->all();

		$kit->update_json_meta( Module::META_KEY, $default_values );

		return $default_values;
	}
}
