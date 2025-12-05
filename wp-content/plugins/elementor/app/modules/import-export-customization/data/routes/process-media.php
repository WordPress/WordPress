<?php
namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes;

use Elementor\Plugin;
use Elementor\App\Modules\ImportExportCustomization\Data\Response;
use Elementor\App\Modules\ImportExportCustomization\Module as ImportExportCustomizationModule;
use Elementor\Modules\CloudKitLibrary\Module as CloudKitLibrary;
use Elementor\Utils as ElementorUtils;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Traits\Handles_Quota_Errors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Process_Media extends Base_Route {
	use Handles_Quota_Errors;

	protected function get_route(): string {
		return 'process-media';
	}

	protected function get_method(): string {
		return \WP_REST_Server::CREATABLE;
	}

	protected function callback( $request ): \WP_REST_Response {
		/**
		 * @var $module ImportExportCustomizationModule
		 */
		$module = Plugin::$instance->app->get_component( 'import-export-customization' );

		$cloud_kit_library_app = $this->get_cloud_kit_library_app();

		$media_urls = $request->get_param( 'media_urls' );
		$kit = $request->get_param( 'kit' );
		$quota = null;

		try {
			if ( empty( $media_urls ) || ! is_array( $media_urls ) ) {
				throw new \Error( 'Invalid media URLs provided' );
			}

			$media_collector = new \Elementor\TemplateLibrary\Classes\Media_Collector();
			$zip_path = $media_collector->process_media_collection( $media_urls );

			if ( $cloud_kit_library_app ) {
				$quota = $cloud_kit_library_app->get_quota();
				$cloud_kit_library_app->validate_storage_quota( filesize( $zip_path ), $quota );
			}

			if ( ! $zip_path ) {
				throw new \Error( 'Failed to process media' );
			}

			$zip_file = ElementorUtils::file_get_contents( $zip_path );

			$upload_success = false;
			if ( $cloud_kit_library_app ) {
				$upload_success = $cloud_kit_library_app->upload_content_file( $kit['mediaUploadUrl'], $zip_file );
				$cloud_kit_library_app->update_kit( $kit['id'], [ 'mediaFileId' => $upload_success ? $kit['mediaFileId'] : null ] );
			}

			$media_collector->cleanup();

			return Response::success( [
				'success' => true,
				'message' => 'Media processed and uploaded successfully',
			] );

		} catch ( \Error | \Exception $e ) {
			Plugin::$instance->logger->get_logger()->error( $e->getMessage(), [
				'meta' => [
					'trace' => $e->getTraceAsString(),
				],
			] );

			if ( $cloud_kit_library_app ) {
				$cloud_kit_library_app->update_kit( $kit['id'], [ 'mediaFileId' => null ] );
			}

			if ( $module->is_third_party_class( $e->getTrace()[0]['class'] ) ) {
				return Response::error( ImportExportCustomizationModule::THIRD_PARTY_ERROR, $e->getMessage() );
			}

			if ( $this->is_quota_error( $e->getMessage() ) ) {
				return $this->get_quota_error_response( $quota, $kit );
			}

			return Response::error( ImportExportCustomizationModule::MEDIA_PROCESSING_ERROR, $e->getMessage() );
		}
	}

	protected function get_args(): array {
		return [
			'media_urls' => [
				'type' => 'array',
				'description' => 'Array of media URLs to process',
				'required' => true,
				'items' => [
					'type' => 'string',
				],
			],
		];
	}
}
