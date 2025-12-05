<?php
namespace Elementor\App\Modules\ImportExport\Runners\Import;

use Elementor\App\Modules\ImportExport\Utils as ImportExportUtils;
use Elementor\Plugin;

class Elementor_Content extends Import_Runner_Base {

	private $show_page_on_front;

	private $page_on_front_id;

	private $import_session_id;

	public function __construct() {
		$this->init_page_on_front_data();
	}

	public static function get_name(): string {
		return 'elementor-content';
	}

	public function should_import( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'content', $data['include'], true ) &&
			! empty( $data['manifest']['content'] ) &&
			! empty( $data['extracted_directory_path'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$result['content'] = [];
		$this->import_session_id = $data['session_id'];

		$elementor_post_types = ImportExportUtils::get_elementor_post_types();

		foreach ( $elementor_post_types as $post_type ) {
			if ( empty( $data['manifest']['content'][ $post_type ] ) ) {
				continue;
			}

			$posts_settings = $data['manifest']['content'][ $post_type ];
			$path = $data['extracted_directory_path'] . 'content/' . $post_type . '/';
			$imported_terms = ! empty( $imported_data['taxonomies'] )
				? ImportExportUtils::map_old_new_term_ids( $imported_data )
				: [];

			$result['content'][ $post_type ] = $this->import_elementor_post_type( $posts_settings, $path, $post_type, $imported_terms );
		}

		return $result;
	}

	private function import_elementor_post_type( array $posts_settings, $path, $post_type, array $imported_terms ) {
		$result = [
			'succeed' => [],
			'failed' => [],
		];

		foreach ( $posts_settings as $id => $post_settings ) {
			try {
				$post_data = ImportExportUtils::read_json_file( $path . $id );
				$import = $this->import_post( $post_settings, $post_data, $post_type, $imported_terms );

				if ( is_wp_error( $import ) ) {
					$result['failed'][ $id ] = $import->get_error_message();
					continue;
				}

				$result['succeed'][ $id ] = $import;
			} catch ( \Exception $error ) {
				$result['failed'][ $id ] = $error->getMessage();
			}
		}

		return $result;
	}

	private function import_post( array $post_settings, array $post_data, $post_type, array $imported_terms ) {
		$post_attributes = [
			'post_title' => $post_settings['title'],
			'post_type' => $post_type,
			'post_status' => 'publish',
		];

		if ( ! empty( $post_settings['excerpt'] ) ) {
			$post_attributes['post_excerpt'] = $post_settings['excerpt'];
		}

		$new_document = Plugin::$instance->documents->create(
			$post_settings['doc_type'],
			$post_attributes
		);

		if ( is_wp_error( $new_document ) ) {
			throw new \Exception( esc_html( $new_document->get_error_message() ) );
		}

		$post_data['import_settings'] = $post_settings;

		$new_attachment_callback = function( $attachment_id ) {
			$this->set_session_post_meta( $attachment_id, $this->import_session_id );
		};

		add_filter( 'elementor/template_library/import_images/new_attachment', $new_attachment_callback );

		$new_document->import( $post_data );

		remove_filter( 'elementor/template_library/import_images/new_attachment', $new_attachment_callback );

		$new_post_id = $new_document->get_main_id();

		if ( ! empty( $post_settings['terms'] ) ) {
			$this->set_post_terms( $new_post_id, $post_settings['terms'], $imported_terms );
		}

		if ( ! empty( $post_settings['show_on_front'] ) ) {
			$this->set_page_on_front( $new_post_id );
		}

		$this->set_session_post_meta( $new_post_id, $this->import_session_id );

		return $new_post_id;
	}

	private function set_post_terms( $post_id, array $terms, array $imported_terms ) {
		foreach ( $terms as $term ) {
			if ( ! isset( $imported_terms[ $term['term_id'] ] ) ) {
				continue;
			}

			wp_set_post_terms( $post_id, [ $imported_terms[ $term['term_id'] ] ], $term['taxonomy'], false );
		}
	}

	private function init_page_on_front_data() {
		$this->show_page_on_front = 'page' === get_option( 'show_on_front' );

		if ( $this->show_page_on_front ) {
			$this->page_on_front_id = (int) get_option( 'page_on_front' );
		}
	}

	private function set_page_on_front( $page_id ) {
		update_option( 'page_on_front', $page_id );

		if ( ! $this->show_page_on_front ) {
			update_option( 'show_on_front', 'page' );
		}
	}

	public function get_import_session_metadata(): array {
		return [
			'page_on_front' => $this->page_on_front_id ?? 0,
		];
	}
}
