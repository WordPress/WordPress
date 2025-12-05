<?php
namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes\Traits;

use Elementor\App\Modules\ImportExportCustomization\Data\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Handles_Quota_Errors {

	protected function get_cloud_kit_library_app() {
		try {
			return \Elementor\Modules\CloudKitLibrary\Module::get_app();
		} catch ( \Exception | \Error $e ) {
			return null;
		}
	}

	private function is_quota_error( $error_message ) {
		return \Elementor\Modules\CloudKitLibrary\Connect\Cloud_Kits::INSUFFICIENT_STORAGE_QUOTA === $error_message;
	}

	private function get_quota_error_response( $quota, $kit_data ) {
		$max_size_gb = 0;
		if ( ! empty( $quota['storage']['threshold'] ) ) {
			$max_size_gb = round( $quota['storage']['threshold'] / ( 1024 * 1024 * 1024 ), 2 );
		}

		$filename = __( 'This file', 'elementor' );
		if ( ! empty( $kit_data['title'] ) ) {
			$filename = '"' . $kit_data['title'] . '"';
		} elseif ( ! empty( $kit_data['fileName'] ) ) {
			$filename = '"' . $kit_data['fileName'] . '"';
		}

		return Response::error(
			\Elementor\Modules\CloudKitLibrary\Connect\Cloud_Kits::INSUFFICIENT_STORAGE_QUOTA,
			[
				'replacements' => [
					'filename' => $filename,
					'maxSize' => $max_size_gb,
				],
			]
		);
	}
}
