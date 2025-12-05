<?php

namespace Elementor\App\Modules\ImportExport\Runners\Export;

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
		$manifest_data['plugins'] = $data['selected_plugins'];

		return [
			'manifest' => [
				$manifest_data,
			],
			'files' => [],
		];
	}
}
