<?php
namespace Elementor\Modules\KitElementsDefaults\ImportExport\Runners;

use Elementor\Modules\KitElementsDefaults\ImportExport\Import_Export;
use Elementor\Plugin;
use Elementor\Core\Utils\Collection;
use Elementor\Modules\KitElementsDefaults\Module;
use Elementor\Modules\KitElementsDefaults\Utils\Settings_Sanitizer;
use Elementor\App\Modules\ImportExport\Runners\Export\Export_Runner_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Export extends Export_Runner_Base {
	public static function get_name(): string {
		return 'elements-default-values';
	}

	public function should_export( array $data ) {
		// Together with site-settings.
		return (
			isset( $data['include'] ) &&
			in_array( 'settings', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		if ( ! $kit ) {
			return [
				'manifest' => [],
				'files' => [],
			];
		}

		$default_values = $kit->get_json_meta( Module::META_KEY );

		if ( ! $default_values ) {
			return [
				'manifest' => [],
				'files' => [],
			];
		}

		$sanitizer = new Settings_Sanitizer(
			Plugin::$instance->elements_manager,
			array_keys( Plugin::$instance->widgets_manager->get_widget_types() )
		);

		$default_values = ( new Collection( $default_values ) )
			->map( function ( $settings, $type ) use ( $sanitizer, $kit ) {
				return $sanitizer
					->for( $type )
					->using( $settings )
					->remove_invalid_settings()
					->kses_deep()
					->prepare_for_export( $kit )
					->get();
			} )
			->all();

		return [
			'files' => [
				'path' => Import_Export::FILE_NAME,
				'data' => $default_values,
			],
		];
	}
}
