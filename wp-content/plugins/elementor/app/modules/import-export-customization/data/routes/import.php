<?php
namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes;

use Elementor\App\Modules\ImportExportCustomization\Module as ImportExportCustomizationModule;
use Elementor\Plugin;
use Elementor\App\Modules\ImportExportCustomization\Data\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import extends Base_Route {

	protected function get_route(): string {
		return 'import';
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
			$session = $request->get_param( 'session' );

			if ( empty( $session ) ) {
				return Response::error( 'missing_session_id', 'Session ID is required.' );
			}

			$settings = [
				'include' => $request->get_param( 'include' ),
				'customization' => $request->get_param( 'customization' ),
			];

			$import = $module->import_kit( $session, $settings, true );

			Plugin::$instance->logger->get_logger()->info(
				sprintf( 'Selected import runners via REST API: %1$s',
					implode( ', ', $import['runners'] ?? [] )
				)
			);

			return Response::success( $import );

		} catch ( \Error $e ) {
			Plugin::$instance->logger->get_logger()->error( $e->getMessage(), [
				'meta' => [
					'trace' => $e->getTraceAsString(),
				],
			] );

			if ( $module->is_third_party_class( $e->getTrace()[0]['class'] ) ) {
				return Response::error( ImportExportCustomizationModule::THIRD_PARTY_ERROR, $e->getMessage() );
			}

			return Response::error( $e->getMessage(), 'import_error' );
		}
	}

	protected function get_args(): array {
		return [
			'session' => [
				'type' => 'string',
				'description' => 'Session ID for import operations',
				'required' => true,
			],
			'settings' => [
				'type' => 'object',
				'description' => 'Import settings',
				'required' => false,
				'default' => [],
			],
		];
	}
}
