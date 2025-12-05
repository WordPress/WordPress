<?php

namespace Elementor\App\Modules\ImportExport\Runners\Import;

use Elementor\App\Modules\ImportExport\Utils as ImportExportUtils;
use Elementor\Core\Utils\ImportExport\WP_Import;

class Wp_Content extends Import_Runner_Base {

	private $import_session_id;

	/**
	 * @var array
	 */
	private $selected_custom_post_types = [];

	public static function get_name(): string {
		return 'wp-content';
	}

	public function should_import( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'content', $data['include'], true ) &&
			! empty( $data['extracted_directory_path'] ) &&
			! empty( $data['manifest']['wp-content'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$this->import_session_id = $data['session_id'];

		$path = $data['extracted_directory_path'] . 'wp-content/';

		$post_types = $this->filter_post_types( $data['selected_custom_post_types'] );

		$taxonomies = $imported_data['taxonomies'] ?? [];
		$imported_terms = ImportExportUtils::map_old_new_term_ids( $imported_data );

		$result['wp-content'] = [];

		foreach ( $post_types as $post_type ) {
			$import = $this->import_wp_post_type(
				$path,
				$post_type,
				$imported_data,
				$taxonomies,
				$imported_terms
			);

			if ( empty( $import ) ) {
				continue;
			}

			$result['wp-content'][ $post_type ] = $import;
			$imported_data = array_merge( $imported_data, $result );
		}

		return $result;
	}

	private function import_wp_post_type( $path, $post_type, array $imported_data, array $taxonomies, array $imported_terms ) {
		$args = [
			'fetch_attachments' => true,
			'posts' => ImportExportUtils::map_old_new_post_ids( $imported_data ),
			'terms' => $imported_terms,
			'taxonomies' => ! empty( $taxonomies[ $post_type ] ) ? $taxonomies[ $post_type ] : [],
			'posts_meta' => [
				static::META_KEY_ELEMENTOR_IMPORT_SESSION_ID => $this->import_session_id,
			],
			'terms_meta' => [
				static::META_KEY_ELEMENTOR_IMPORT_SESSION_ID => $this->import_session_id,
			],
		];

		$file = $path . $post_type . '/' . $post_type . '.xml';

		if ( ! file_exists( $file ) ) {
			return [];
		}

		$wp_importer = new WP_Import( $file, $args );
		$result = $wp_importer->run();

		return $result['summary']['posts'];
	}

	private function filter_post_types( $selected_custom_post_types = [] ) {
		$wp_builtin_post_types = ImportExportUtils::get_builtin_wp_post_types();

		foreach ( $selected_custom_post_types as $custom_post_type ) {
			if ( post_type_exists( $custom_post_type ) ) {
				$this->selected_custom_post_types[] = $custom_post_type;
			}
		}

		$post_types = array_merge( $wp_builtin_post_types, $this->selected_custom_post_types );
		$post_types = $this->force_element_to_be_last_by_value( $post_types, 'nav_menu_item' );

		return $post_types;
	}

	public function get_import_session_metadata(): array {
		return [
			'custom_post_types' => $this->selected_custom_post_types,
		];
	}

	/**
	 * @param array $base_array The array we want to relocate his element.
	 * @param mixed $element    The value of the element in the array we want to shift to end of the array.
	 * @return mixed
	 */
	private function force_element_to_be_last_by_value( array $base_array, $element ) {
		$index = array_search( $element, $base_array, true );

		if ( false !== $index ) {
			unset( $base_array[ $index ] );
			$base_array[] = $element;
		}

		return $base_array;
	}
}
