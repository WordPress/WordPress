<?php
namespace Elementor\App\Modules\ImportExportCustomization;

use Elementor\Core\Utils\Collection;
use Elementor\Core\Utils\Plugins_Manager;
use Elementor\Plugin;
use Elementor\App\Modules\KitLibrary\Connect\Kit_Library;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wp_Cli extends \WP_CLI_Command {

	const AVAILABLE_SETTINGS = [ 'include', 'overrideConditions', 'selectedCustomPostTypes', 'plugins' ];

	/**
	 * Export a Kit
	 *
	 * [--include]
	 *      Which type of content to include. Possible values are 'content', 'templates', 'site-settings'.
	 *      if this parameter won't be specified, All data types will be included.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp elementor kit export path/to/export-file-name.zip
	 *      - This will export all site data to the specified file name.
	 *
	 * 2. wp elementor kit export path/to/export-file-name.zip --include=kit-settings,content
	 *      - This will export only site settings and content.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function export( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( 'Please specify a file name' );
		}

		\WP_CLI::line( 'Kit export started.' );

		$export_settings = [];
		foreach ( $assoc_args as $key => $value ) {
			if ( ! in_array( $key, static::AVAILABLE_SETTINGS, true ) ) {
				continue;
			}

			$export_settings[ $key ] = explode( ',', $value );
		}

		try {
			/**
			 * Running the export process through the import-export module so the export property in the module will be available to use.
			 *
			 * @type  Module $import_export_module
			 */
			$import_export_module = Plugin::$instance->app->get_component( 'import-export' );
			$result = $import_export_module->export_kit( $export_settings );

			rename( $result['file_name'], $args[0] );
		} catch ( \Error $error ) {
			\WP_CLI::error( $error->getMessage() );
		}

		\WP_CLI::success( 'Kit exported successfully.' );
	}

	/**
	 * Import a Kit
	 *
	 * [--include]
	 *      Which type of content to include. Possible values are 'content', 'templates', 'site-settings'.
	 *      if this parameter won't be specified, All data types will be included.
	 *
	 * [--overrideConditions]
	 *      Templates ids to override conditions for.
	 *
	 * [--sourceType]
	 *      Which source type is used in the current session. Available values are 'local', 'remote', 'library'.
	 *      The default value is 'local'
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp elementor kit import path/to/elementor-kit.zip
	 *      - This will import the whole kit file content.
	 *
	 * 2. wp elementor kit import path/to/elementor-kit.zip --include=site-settings,content
	 *      - This will import only site settings and content.
	 *
	 * 3. wp elementor kit import path/to/elementor-kit.zip --overrideConditions=3478,4520
	 *      - This will import all content and will override conditions for the given template ids.
	 *
	 * 4. wp elementor kit import path/to/elementor-kit.zip --unfilteredFilesUpload=enable
	 *      - This will allow the import process to import unfiltered files.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function import( array $args, array $assoc_args ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			\WP_CLI::error( 'You must run this command as an admin user' );
		}

		if ( empty( $args[0] ) ) {
			\WP_CLI::error( 'Please specify a file to import' );
		}

		\WP_CLI::line( 'Kit import started' );

		$assoc_args = wp_parse_args( $assoc_args, [
			'sourceType' => 'local',
		] );

		$url = null;
		$file_path = $args[0];
		$import_settings = [];
		$import_settings['referrer'] = Module::REFERRER_LOCAL;

		switch ( $assoc_args['sourceType'] ) {
			case 'library':
				$url = $this->get_url_from_library( $file_path );
				$zip_path = $this->create_temp_file_from_url( $url );
				$import_settings['referrer'] = Module::REFERRER_KIT_LIBRARY;
				break;

			case 'remote':
				$zip_path = $this->create_temp_file_from_url( $file_path );
				break;

			case 'local':
				$zip_path = $file_path;
				break;

			default:
				\WP_CLI::error( 'Unknown source type.' );
				break;
		}

		if ( 'enable' === $assoc_args['unfilteredFilesUpload'] ) {
			Plugin::$instance->uploads_manager->enable_unfiltered_files_upload();
		}

		foreach ( $assoc_args as $key => $value ) {
			if ( ! in_array( $key, static::AVAILABLE_SETTINGS, true ) ) {
				continue;
			}

			$import_settings[ $key ] = explode( ',', $value );
		}

		try {
			\WP_CLI::line( 'Importing data...' );

			/**
			 * Running the import process through the import-export module so the import property in the module will be available to use.
			 *
			 * @type  Module $import_export_module
			 */
			$import_export_module = Plugin::$instance->app->get_component( 'import-export' );

			if ( ! $import_export_module ) {
				\WP_CLI::error( 'Import Export module is not available.' );
			}

			$import = $import_export_module->import_kit( $zip_path, $import_settings );

			$manifest_data = $import_export_module->import->get_manifest();

			/**
			 * Import Export Manifest Data
			 *
			 * Allows 3rd parties to read and edit the kit's manifest before it is used.
			 *
			 * @since 3.7.0
			 *
			 * @param array $manifest_data The Kit's Manifest data
			 */
			$manifest_data = apply_filters( 'elementor/import-export/wp-cli/manifest_data', $manifest_data );

			\WP_CLI::line( 'Removing temp files...' );

			// The file was created from remote or library request, it also should be removed.
			if ( $url ) {
				Plugin::$instance->uploads_manager->remove_file_or_dir( dirname( $zip_path ) );
			}

			\WP_CLI::success( 'Kit imported successfully' );
		} catch ( \Error $error ) {
			Plugin::$instance->logger->get_logger()->error( $error->getMessage(), [
				'meta' => [
					'trace' => $error->getTraceAsString(),
				],
			] );

			if ( $url ) {
				Plugin::$instance->uploads_manager->remove_file_or_dir( dirname( $zip_path ) );
			}

			\WP_CLI::error( $error->getMessage() );
		}
	}

	/**
	 * Revert last imported kit.
	 */
	public function revert() {
		\WP_CLI::line( 'Kit revert started.' );

		try {
			/**
			 * Running the revert process through the import-export module so the revert property in the module will be available to use.
			 *
			 * @type  Module $import_export_module
			 */
			$import_export_module = Plugin::$instance->app->get_component( 'import-export' );
			$import_export_module->revert_last_imported_kit();

		} catch ( \Error $error ) {
			\WP_CLI::error( $error->getMessage() );
		}

		\WP_CLI::success( 'Kit reverted successfully.' );
	}

	/**
	 * Helper to get kit url by the kit id
	 * TODO: Maybe extract it.
	 *
	 * @param $kit_id
	 *
	 * @return string
	 */
	private function get_url_from_library( $kit_id ) {
		/** @var Kit_Library $app */
		$app = Plugin::$instance->common->get_component( 'connect' )->get_app( 'kit-library' );

		if ( ! $app ) {
			\WP_CLI::error( 'Kit library app not found' );
		}

		$response = $app->download_link( $kit_id );

		if ( is_wp_error( $response ) ) {
			\WP_CLI::error( "Library Response: {$response->get_error_message()}" );
		}

		return $response->download_link;
	}

	/**
	 * Helper to get kit zip file path by the kit url
	 * TODO: Maybe extract it.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	private function create_temp_file_from_url( $url ) {
		\WP_CLI::line( 'Extracting zip archive...' );
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			\WP_CLI::error( "Download file url: {$response->get_error_message()}" );
		}

		if ( 200 !== $response['response']['code'] ) {
			\WP_CLI::error( "Download file url: {$response['response']['message']}" );
		}

		// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		$file = Plugin::$instance->uploads_manager->create_temp_file( $response['body'], 'kit.zip' );

		// After the upload complete, set the elementor upload state back to false.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( false );

		return $file;
	}
}
