<?php
namespace Elementor\TemplateLibrary;

use Elementor\Core\Base\Document;
use Elementor\Core\Utils\Exceptions;
use Elementor\Modules\CloudLibrary\Connect\Cloud_Library;
use Elementor\Modules\CloudLibrary\Documents\Cloud_Template_Preview;
use Elementor\Plugin;
use Elementor\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Source_Cloud extends Source_Base {
	const FOLDER_RESOURCE_TYPE = 'FOLDER';
	const TEMPLATE_RESOURCE_TYPE = 'TEMPLATE';

	protected function get_app(): Cloud_Library {
		$cloud_library_app = Plugin::$instance->common->get_component( 'connect' )->get_app( 'cloud-library' );

		if ( ! $cloud_library_app ) {
			$error_message = esc_html__( 'Cloud-Library is not instantiated.', 'elementor' );

			throw new \Exception( $error_message, Exceptions::FORBIDDEN ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $cloud_library_app;
	}

	public function get_id(): string {
		return 'cloud';
	}

	public function get_title(): string {
		return esc_html__( 'Cloud Library', 'elementor' );
	}

	public function register_data() {}

	public function supports_quota(): bool {
		return true;
	}

	public function get_items( $args = [] ) {
		return $this->get_app()->get_resources( $args );
	}

	public function get_item_children( array $args = [] ) {
		return $this->get_app()->get_resources( [ 'parentId' => $args['template_id'] ] );
	}

	public function get_item( $id ) {
		return $this->get_app()->get_resource( [ 'id' => $id ] );
	}

	public function get_data( array $args ) {
		$data = $this->get_app()->get_resource( [ 'id' => $args['template_id'] ] );

		if ( is_wp_error( $data ) || empty( $data['content'] ) ) {
			return $data;
		}

		$decoded_data = json_decode( $data['content'], true );
		$data['content'] = $decoded_data['content'];

		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		if ( ! empty( $args['editor_post_id'] ) ) {
			$post_id = $args['editor_post_id'];
			$document = Plugin::$instance->documents->get( $post_id );
			if ( $document ) {
				$data['content'] = $document->get_elements_raw_data( $data['content'], true );
			}
		}

		if ( ! empty( $args['with_page_settings'] ) ) {
			$data['page_settings'] = $decoded_data['page_settings'];
		}

		// After the upload complete, set the elementor upload state back to false
		Plugin::$instance->uploads_manager->set_elementor_upload_state( false );

		return $data;
	}

	public function delete_template( $template_id ) {
		return $this->get_app()->delete_resource( $template_id );
	}

	public function save_item( $template_data ): int {
		$app = $this->get_app();

		$resource_data = $this->format_resource_item_for_create( $template_data );

		$response = $app->post_resource( $resource_data );

		return (int) $response['id'];
	}

	private function format_resource_item_for_create( $template_data ) {
		return [
			'title' => $template_data['title'] ?? esc_html__( '(no title)', 'elementor' ),
			'type' => self::TEMPLATE_RESOURCE_TYPE,
			'templateType' => $template_data['type'],
			'parentId' => ! empty( $template_data['parentId'] ) ? (int) $template_data['parentId'] : null,
			'content' => wp_json_encode( [
				'content' => $template_data['content'],
				'page_settings' => $template_data['page_settings'],
			] ),
			'hasPageSettings' => ! empty( $template_data['page_settings'] ),
		];
	}

	public function save_folder( array $folder_data = [] ) {
		$app = $this->get_app();

		$resource_data = [
			'title' => $folder_data['title'] ?? esc_html__( 'New Folder', 'elementor' ),
			'type' => self::FOLDER_RESOURCE_TYPE,
			'templateType' => 'folder',
			'parentId' => null,
		];

		$response = $app->post_resource( $resource_data );

		return (int) $response['id'];
	}

	public function update_item( $template_data ) {
		return $this->get_app()->update_resource( $template_data );
	}

	public function search_templates( array $args = [] ) {
		return $this->get_app()->get_resources( $args );
	}

	public function export_template( $id ) {
		$data = $this->get_app()->get_resource( [ 'id' => $id ] );

		if ( is_wp_error( $data ) ) {
			return new \WP_Error( 'export_template_error', 'An error has occured' );
		}

		if ( static::TEMPLATE_RESOURCE_TYPE === $data['type'] ) {
			$this->handle_export_file( $data );
		}

		if ( static::FOLDER_RESOURCE_TYPE === $data['type'] ) {
			$this->handle_export_folder( $id );
		}
	}

	protected function handle_export_file( array $data ): void {
		$file_data = $this->prepare_template_export( $data );

		if ( is_wp_error( $file_data ) ) {
			return;
		}

		$this->send_file_headers( $file_data['name'], strlen( $file_data['content'] ) );

		$this->serve_file( $file_data['content'] );
	}

	protected function handle_export_folder( int $folder_id ) {
		$data = $this->get_item_children( [ 'template_id' => $folder_id ] );

		if ( empty( $data['templates'] ) ) {
			throw new \Exception( 'Folder does not have any templates to export' );
		}

		$template_ids = array_map( fn( $template ) => $template['template_id'], $data['templates'] );

		$this->export_multiple_templates( $template_ids );
	}

	private function prepare_template_export( $data ) {
		if ( empty( $data['content'] ) ) {
			throw new \Exception( 'Template data not found' );
		}

		$data['content'] = json_decode( $data['content'], true );

		if ( empty( $data['content']['content'] ) ) {
			throw new \Exception( 'The template is empty' );
		}

		$export_data = [
			'content' => $data['content']['content'],
			'page_settings' => $data['content']['page_settings'] ?? [],
			'version' => DB::DB_VERSION,
			'title' => $data['title'],
			'type' => $data['templateType'],
		];

		return [
			'name' => 'elementor-' . $data['id'] . '-' . gmdate( 'Y-m-d' ) . '.json',
			'content' => wp_json_encode( $export_data ),
		];
	}

	public function export_multiple_templates( array $template_ids ) {
		$files = [];
		$temp_path = Plugin::$instance->uploads_manager->create_unique_dir();

		foreach ( $template_ids as $template_id ) {
			$files[] = $this->get_file_item( $template_id, $temp_path );
		}

		if ( empty( $files ) ) {
			throw new \Exception( 'There are no files to export (probably all the requested templates are empty).' );
		}

		list( $zip_archive_filename, $zip_complete_path ) = $this->handle_zip_file( $temp_path, $files );

		$this->send_file_headers( $zip_archive_filename, $this->filesize( $zip_complete_path ) );

		$this->serve_zip( $zip_complete_path );

		Plugin::$instance->uploads_manager->remove_file_or_dir( $temp_path );
	}

	protected function handle_zip_file( string $temp_path, array $files ): array {
		if ( ! class_exists( 'ZipArchive' ) ) {
			throw new \Error( 'ZipArchive module missing' );
		}

		$zip_archive_filename = 'elementor-templates-' . gmdate( 'Y-m-d' ) . '.zip';

		$zip_archive = new \ZipArchive();

		$zip_complete_path = $temp_path . '/' . $zip_archive_filename;

		$zip_archive->open( $zip_complete_path, \ZipArchive::CREATE );

		foreach ( $files as $file ) {
			$zip_archive->addFile( $file['path'], $file['name'] );
		}

		$zip_archive->close();

		return [ $zip_archive_filename, $zip_complete_path ];
	}

	private function get_file_item( $template_id, string $temp_path ) {
		$data      = $this->get_app()->get_resource( [ 'id' => $template_id ] );
		$file_data = $this->prepare_template_export( $data );

		if ( is_wp_error( $file_data ) ) {
			return;
		}

		$complete_path = $temp_path . $file_data['name'];

		$put_contents = file_put_contents( $complete_path, $file_data['content'] );

		if ( ! $put_contents ) {
			throw new \Exception( sprintf( 'Cannot create file "%s".', esc_html( $file_data['name'] ) ) );
		}

		return [
			'path' => $complete_path,
			'name' => $file_data['name'],
		];
	}

	public function move_template_to_folder( array $args = [] ) {
		$move_args = [
			'title' => $args['title'],
			'id' => $args['from_template_id'],
			'parentId' => ! empty( $args['parentId'] ) ? (int) $args['parentId'] : '',
		];

		return $this->update_item( $move_args );
	}

	public function move_bulk_templates_to_folder( array $args = [] ) {
		$move_args = [
			'ids' => $args['from_template_id'],
			'parentId' => ! empty( $args['parentId'] ) ? (int) $args['parentId'] : null,
		];

		return $this->get_app()->bulk_move_templates( $move_args );
	}

	public function save_item_preview( $template_id, $data ) {
		return $this->get_app()->update_resource_preview( $template_id, $data );
	}

	public function mark_preview_as_failed( $template_id, $data ) {
		return $this->get_app()->mark_preview_as_failed( $template_id, $data );
	}

	/**
	 * @param int $template_id
	 * @return Document|\WP_Error
	 * @throws \Exception If the user has no permission or the post is not found.
	 */
	public function create_document_for_preview( int $template_id ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( Exceptions::FORBIDDEN, esc_html__( 'You do not have permission to create preview documents.', 'elementor' ) );
		}

		$cloud_library_app = $this->get_app();

		$template = $cloud_library_app->get_resource( [ 'id' => $template_id ] );

		if ( is_wp_error( $template ) ) {
			$error_message = $template->get_error_message();
			throw new \Exception( esc_html( $error_message ), Exceptions::FORBIDDEN );  // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$decoded_content = json_decode( $template['content'], true );

		return $this->save_document_for_preview( [
			'content' => $decoded_content['content'],
			'page_settings' => $decoded_content['page_settings'],
		] );
	}

	protected function save_document_for_preview( $template_content ) {
		$template_data = [
			'title' => esc_html__( '(no title)', 'elementor' ),
			'page_settings' => $template_content['page_settings'] ?? [],
			'status' => 'draft',
			'type' => 'container',
		];

		$document = Plugin::$instance->documents->create(
			Cloud_Template_Preview::TYPE,
			[
				'post_title' => $template_data['title'],
				'post_status' => $template_data['status'],
			]
		);

		if ( is_wp_error( $document ) ) {
			wp_die();
		}

		$template_data['content'] = $this->replace_elements_ids( $template_content['content'] );

		$document->save( [
			'elements' => $template_data['content'],
			'settings' => $template_data['page_settings'],
		] );

		do_action( 'elementor/template-library/after_save_template', $document->get_main_id(), $template_data );
		do_action( 'elementor/template-library/after_update_template', $document->get_main_id(), $template_data );

		return $document;
	}

	public function bulk_delete_items( array $template_ids ) {
		return $this->get_app()->bulk_delete_resources( $template_ids );
	}


	public function bulk_undo_delete_items( array $template_ids ) {
		return $this->get_app()->bulk_undo_delete_resources( $template_ids );
	}

	public function save_bulk_items( array $data = [] ) {
		$items = [];

		foreach ( $data as $template_data ) {
			$items[] = $this->format_resource_item_for_create( $template_data );
		}

		return $this->get_app()->post_bulk_resources( $items );
	}

	public function get_bulk_items( array $args = [] ) {
		return $this->get_app()->get_bulk_resources_with_content( $args );
	}

	public function get_quota() {
		return $this->get_app()->get_quota();
	}

	public function import_template( $name, $path ) {
		if ( empty( $path ) ) {
			return new \WP_Error( 'file_error', 'Please upload a file to import' );
		}

		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		$items = [];

		$quota = $this->get_quota();

		if ( is_wp_error( $quota ) ) {
			return $quota;
		}

		if ( $quota['currentUsage'] >= $quota['threshold'] ) {
			return new \WP_Error( 'quota_error', 'The upload failed because you’ve saved the maximum templates already.' );
		}

		if ( 'zip' === pathinfo( $name, PATHINFO_EXTENSION ) ) {
			$extracted_files = Plugin::$instance->uploads_manager->extract_and_validate_zip( $path, [ 'json' ] );

			if ( is_wp_error( $extracted_files ) ) {
				Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

				return $extracted_files;
			}

			$items_to_save = [];

			foreach ( $extracted_files['files'] as $file_path ) {
				// Skip macOS metadata files and folders
				if ( false !== strpos( $file_path, '__MACOSX' ) || '.' === basename( $file_path )[0] ) {
					continue;
				}

				$prepared = $this->prepare_import_template_data( $file_path );

				if ( is_wp_error( $prepared ) ) {
					// Skip failed templates
					continue;
				}

				$items_to_save[] = $this->format_resource_item_for_create( $prepared );
			}

			$is_quota_valid = $this->validate_quota( $items_to_save );

			if ( is_wp_error( $is_quota_valid ) ) {
				return $is_quota_valid;
			}

			if ( ! $is_quota_valid ) {
				return new \WP_Error( 'quota_error', 'The upload failed because it will pass the maximum templates you can save.' );
			}

			$items = $this->get_app()->post_bulk_resources( $items_to_save );

			Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );
		} else {
			$prepared = $this->prepare_import_template_data( $path );

			if ( is_wp_error( $prepared ) ) {
				return $prepared;
			}

			$is_quota_valid = $this->validate_quota( [ $prepared ] );

			if ( is_wp_error( $is_quota_valid ) ) {
				return $is_quota_valid;
			}

			if ( ! $is_quota_valid ) {
				return new \WP_Error( 'quota_error', 'The upload failed because it will pass the maximum templates you can save.' );
			}

			$item = $this->get_app()->post_resource( $this->format_resource_item_for_create( $prepared ) );

			if ( is_wp_error( $item ) ) {
				return $item;
			}

			$items[] = $item;
		}

		return $items;
	}

	public function validate_quota( $items ) {
		$quota = $this->get_quota();

		if ( is_wp_error( $quota ) ) {
			return $quota;
		}

		return $quota['currentUsage'] + count( $items ) <= $quota['threshold'];
	}
}
