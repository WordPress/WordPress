<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\Core\Utils\Collection;

class Plugins extends Export_Runner_Base {

	public static function get_name(): string {
		return 'plugins';
	}

	public function should_export( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'plugins', $data['include'], true ) &&
			is_array( $data['selected_plugins'] )
		);
	}

	public function export( array $data ) {
		$customization = $data['customization']['plugins'] ?? null;

		if ( $customization ) {
			$enabled_plugin_keys = Collection::make( $customization )->filter()->keys();

			$plugins = Collection::make( $data['selected_plugins'] )
				->filter( function( $plugin_data, $plugin_key ) use ( $enabled_plugin_keys ) {
					return $enabled_plugin_keys->contains( $plugin_key );
				} )
				->all();
		} else {
			$plugins = $data['selected_plugins'];
		}

		return [
			'manifest' => [
				[ 'plugins' => array_values( $plugins ) ],
			],
			'files' => [],
		];
	}
}
