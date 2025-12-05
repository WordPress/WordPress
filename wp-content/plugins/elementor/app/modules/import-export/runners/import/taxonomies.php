<?php

namespace Elementor\App\Modules\ImportExport\Runners\Import;

use Elementor\App\Modules\ImportExport\Utils as ImportExportUtils;

class Taxonomies extends Import_Runner_Base {

	private $import_session_id;

	public static function get_name(): string {
		return 'taxonomies';
	}

	public function should_import( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'content', $data['include'], true ) &&
			! empty( $data['extracted_directory_path'] ) &&
			! empty( $data['manifest']['taxonomies'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$path = $data['extracted_directory_path'] . 'taxonomies/';
		$this->import_session_id = $data['session_id'];

		$wp_builtin_post_types = ImportExportUtils::get_builtin_wp_post_types();
		$selected_custom_post_types = isset( $data['selected_custom_post_types'] ) ? $data['selected_custom_post_types'] : [];
		$post_types = array_merge( $wp_builtin_post_types, $selected_custom_post_types );

		$result = [];

		foreach ( $post_types as $post_type ) {
			if ( empty( $data['manifest']['taxonomies'][ $post_type ] ) ) {
				continue;
			}

			$result['taxonomies'][ $post_type ] = $this->import_taxonomies( $data['manifest']['taxonomies'][ $post_type ], $path );
		}

		return $result;
	}

	private function import_taxonomies( array $taxonomies, $path ) {
		$result = [];
		$imported_taxonomies = [];

		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			if ( ! empty( $imported_taxonomies[ $taxonomy ] ) ) {
				$result[ $taxonomy ] = $imported_taxonomies[ $taxonomy ];
				continue;
			}

			$taxonomy_data = ImportExportUtils::read_json_file( $path . $taxonomy );
			if ( empty( $taxonomy_data ) ) {
				continue;
			}

			$import = $this->import_taxonomy( $taxonomy_data );
			$result[ $taxonomy ] = $import;
			$imported_taxonomies[ $taxonomy ] = $import;
		}

		return $result;
	}

	private function import_taxonomy( array $taxonomy_data ) {
		$terms = [];

		foreach ( $taxonomy_data as $term ) {
			$old_slug = $term['slug'];

			$existing_term = term_exists( $term['slug'], $term['taxonomy'] );
			if ( $existing_term ) {
				if ( 'nav_menu' === $term['taxonomy'] ) {
					$term = $this->handle_duplicated_nav_menu_term( $term );
				} else {
					$terms[] = [
						'old_id' => (int) $term['term_id'],
						'new_id' => (int) $existing_term['term_id'],
						'old_slug' => $old_slug,
						'new_slug' => $term['slug'],
					];
					continue;
				}
			}

			$parent = $this->get_term_parent( $term, $terms );

			$args = [
				'slug' => $term['slug'],
				'description' => wp_slash( $term['description'] ),
				'parent' => (int) $parent,
			];

			$new_term = wp_insert_term( wp_slash( $term['name'] ), $term['taxonomy'], $args );
			if ( ! is_wp_error( $new_term ) ) {
				$this->set_session_term_meta( (int) $new_term['term_id'], $this->import_session_id );

				$terms[] = [
					'old_id' => $term['term_id'],
					'new_id' => (int) $new_term['term_id'],
					'old_slug' => $old_slug,
					'new_slug' => $term['slug'],
				];
			}
		}

		return $terms;
	}

	private function handle_duplicated_nav_menu_term( $term ) {
		do {
			$term['slug'] = $term['slug'] . '-duplicate';
			$term['name'] = $term['name'] . ' duplicate';
		} while ( term_exists( $term['slug'], 'nav_menu' ) );

		return $term;
	}

	private function get_term_parent( $term, array $imported_terms ) {
		$parent = $term['parent'];
		if ( 0 !== $parent && ! empty( $imported_terms ) ) {
			foreach ( $imported_terms as $imported_term ) {
				if ( $parent === $imported_term['old_id'] ) {
					$parent_term = term_exists( $imported_term['new_id'], $term['taxonomy'] );
					break;
				}
			}

			if ( isset( $parent_term['term_id'] ) ) {
				return $parent_term['term_id'];
			}
		}

		return 0;
	}
}
