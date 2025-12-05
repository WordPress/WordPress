<?php
namespace Elementor\App\Modules\ImportExportCustomization\Data\Routes;

use Elementor\App\Modules\ImportExportCustomization\Module as ImportExportCustomizationModule;
use Elementor\Plugin;
use Elementor\App\Modules\ImportExportCustomization\Data\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Import_Runner extends Base_Route {

	protected function get_route(): string {
		return 'import-runner';
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
			$session_id = $request->get_param( 'session' );
			$runner = $request->get_param( 'runner' );
			$module = Plugin::$instance->app->get_component( 'import-export-customization' );

			if ( empty( $session_id ) ) {
				return Response::error( 'Session ID is required.', 'missing_session_id' );
			}

			if ( empty( $runner ) ) {
				return Response::error( 'Runner name is required.', 'missing_runner_name' );
			}

			$import = $module->import_kit_by_runner( $session_id, $runner );

			if ( ! empty( $import['status'] ) ) {
				Plugin::$instance->logger->get_logger()->info(
					sprintf( 'Import runner completed via REST API: %1$s %2$s',
						$import['runner'] ?? $runner,
						( 'success' === $import['status'] ? '✓' : '✗' )
					)
				);
			}

			do_action( 'elementor/import-export-customization/import-kit/runner/after-run', $import );

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

			return Response::error( $e->getMessage(), 'import_runner_error' );
		}
	}

	protected function get_args(): array {
		return [
			'session' => [
				'type' => 'string',
				'description' => 'Session ID for import operations',
				'required' => true,
			],
			'runner' => [
				'type' => 'string',
				'description' => 'Runner name for import_runner action',
				'required' => true,
			],
		];
	}
}
