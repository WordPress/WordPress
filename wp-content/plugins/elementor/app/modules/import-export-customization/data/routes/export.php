<?php
namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes;

use Elementor\Plugin;
use Elementor\App\Modules\ImportExportCustomization\Data\Response;
use Elementor\Utils as ElementorUtils;
use Elementor\App\Modules\ImportExportCustomization\Module as ImportExportCustomizationModule;
use Elementor\App\Modules\ImportExportCustomization\Processes\Import;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Traits\Handles_Quota_Errors;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Export extends Base_Route {
	use Handles_Quota_Errors;

	protected function get_route(): string {
		return 'export';
	}

	protected function get_method(): string {
		return \WP_REST_Server::CREATABLE;
	}

	protected function callback( $request ): \WP_REST_Response {
		/**
		 * @var $module ImportExportCustomizationModule
		 */
		$module = Plugin::$instance->app->get_component( 'import-export-customization' );

		try {
			$settings = [
				'include' => $request->get_param( 'include' ),
				'kitInfo' => $request->get_param( 'kitInfo' ),
				'screenShotBlob' => $request->get_param( 'screenShotBlob' ),
				'customization' => $request->get_param( 'customization' ),
				'plugins' => $request->get_param( 'plugins' ),
				'selectedCustomPostTypes' => $request->get_param( 'selectedCustomPostTypes' ),
			];

			$settings = array_filter( $settings );

			$source = $settings['kitInfo']['source'];

			$export = $module->export_kit( $settings );

			$file_name = $export['file_name'];
			$file_size = filesize( $file_name );
			$file = ElementorUtils::file_get_contents( $file_name );

			if ( ! $file ) {
				throw new \Error( Import::ZIP_FILE_ERROR_KEY );
			}

			Plugin::$instance->uploads_manager->remove_file_or_dir( dirname( $file_name ) );

			$result = apply_filters(
				'elementor/export/kit/export-result',
				[
					'manifest' => $export['manifest'],
					'file' => base64_encode( $file ),
					'media_urls' => $export['media_urls'],
				],
				$source,
				$export,
				$settings,
				$file,
				$file_size,
			);

			if ( is_wp_error( $result ) ) {
				throw new \Error( $result->get_error_message() );
			}

			return Response::success( $result );

		} catch ( \Error | \Exception $e ) {
			Plugin::$instance->logger->get_logger()->error( $e->getMessage(), [
				'meta' => [
					'trace' => $e->getTraceAsString(),
				],
			] );

			if ( $module->is_third_party_class( $e->getTrace()[0]['class'] ) ) {
				return Response::error( ImportExportCustomizationModule::THIRD_PARTY_ERROR, $e->getMessage() );
			}

			if ( $this->is_quota_error( $e->getMessage() ) ) {
				$quota = null;
				$cloud_kit_library_app = $this->get_cloud_kit_library_app();

				if ( $cloud_kit_library_app ) {
					try {
						$quota = $cloud_kit_library_app->get_quota();
					} catch ( \Exception | \Error $quota_error ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// Quota fetch failed, error message will use default value.
					}
				}

				return $this->get_quota_error_response( $quota, $settings['kitInfo'] ?? [] );
			}

			return Response::error( $e->getMessage(), 'export_error' );
		}
	}

	protected function get_args(): array {
		return [
			'include' => [
				'type' => 'array',
				'description' => 'Content types to include in export',
				'required' => false,
				'default' => [ 'templates', 'content', 'settings', 'plugins' ],
			],
			'kitInfo' => [
				'type' => 'object',
				'description' => 'Kit information',
				'required' => false,
				'default' => [
					'title' => 'Elementor Website Template',
					'description' => '',
					'source' => 'local',
				],
			],
			'screenShotBlob' => [
				'type' => [ 'string', 'null' ],
				'description' => 'Base64 encoded screenshot for cloud exports',
				'required' => false,
				'default' => null,
			],
			'customization' => [
				'type' => 'object',
				'description' => 'Customization settings for selective export',
				'required' => false,
				'default' => null,
				'properties' => [
					'settings' => [
						'type' => [ 'object', 'null' ],
						'description' => 'Site settings customization',
					],
					'templates' => [
						'type' => [ 'object', 'null' ],
						'description' => 'Templates customization',
					],
					'content' => [
						'type' => [ 'object', 'null' ],
						'description' => 'Content customization',
					],
					'plugins' => [
						'type' => [ 'object', 'null' ],
						'description' => 'Plugins customization',
					],
				],
			],
			'plugins' => [
				'type' => 'array',
				'description' => 'Selected plugins to export',
				'required' => false,
				'default' => [],
			],
			'selectedCustomPostTypes' => [
				'type' => 'array',
				'description' => 'Selected custom post types',
				'required' => false,
				'default' => [],
			],
		];
	}
}
