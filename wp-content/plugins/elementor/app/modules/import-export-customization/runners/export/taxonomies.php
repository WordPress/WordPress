<?php
namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\App\Modules\ImportExportCustomization\Utils as ImportExportUtils;

class Taxonomies extends Export_Runner_Base {

	public static function get_name(): string {
		return 'taxonomies';
	}

	public function should_export( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'content', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$customization = $data['customization']['content'] ?? null;
		if ( $customization ) {
			return $this->export_customization( $data, $customization );
		}

		return $this->export_all( $data );
	}

	public function export_customization( array $data, array $customization ) {
		$result = apply_filters( 'elementor/import-export-customization/export/taxonomies/customization', null, $data, $customization, $this );

		if ( is_array( $result ) ) {
			return $result;
		}

		return $this->export_all( $data );
	}

	public function export_all( array $data ) {
		$selected_custom_post_types = $data['customization']['content']['customPostTypes'] ?? null;
		$exclude_post_types = [];

		if ( is_array( $selected_custom_post_types ) && ! in_array( 'post', $selected_custom_post_types, true ) ) {
			$exclude_post_types[] = 'post';
		}

		$wp_builtin_post_types = ImportExportUtils::get_builtin_wp_post_types( $exclude_post_types );

		$post_types = is_array( $selected_custom_post_types )
			? array_merge( $wp_builtin_post_types, $selected_custom_post_types )
			: $wp_builtin_post_types;

		$export = $this->export_taxonomies( $post_types );

		$manifest_data['taxonomies'] = $export['manifest'];

		return [
			'files' => $export['files'],
			'manifest' => [
				$manifest_data,
			],
		];
	}

	private function export_taxonomies( array $post_types ) {
		$files = [];
		$manifest = [];

		$taxonomies = get_taxonomies();

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_obj = get_taxonomy( $taxonomy );
			$taxonomy_post_types = $taxonomy_obj->object_type;
			$intersected_post_types = array_intersect( $taxonomy_post_types, $post_types );

			if ( empty( $intersected_post_types ) ) {
				continue;
			}

			$data = $this->export_terms( $taxonomy );

			if ( empty( $data ) ) {
				continue;
			}

			foreach ( $intersected_post_types as $post_type ) {
				$manifest[ $post_type ][] = [
					'name'  => $taxonomy,
					'label' => $taxonomy_obj->label,
				];
			}

			$files[] = [
				'path' => 'taxonomies/' . $taxonomy,
				'data' => $data,
			];
		}

		return [
			'files' => $files,
			'manifest' => $manifest,
		];
	}

	public function export_terms( $taxonomy ) {
		$terms = get_terms( [
			'taxonomy' => (array) $taxonomy,
			'hide_empty' => false,
			'get' => 'all',
		] );

		$ordered_terms = $this->order_terms( $terms );

		if ( empty( $ordered_terms ) ) {
			return [];
		}

		$data = [];

		foreach ( $ordered_terms as $term ) {
			$data[] = [
				'term_id' => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
				'taxonomy' => $term->taxonomy,
				'description' => $term->description,
				'parent' => $term->parent,
			];
		}

		return $data;
	}
	/**
	 * Put terms in order with no child going before its parent.
	 */
	private function order_terms( array $terms ) {
		$ordered_terms = [];

		while ( $term = array_shift( $terms ) ) {
			$is_top_level = 0 === $term->parent;
			$is_parent_exits = isset( $ordered_terms[ $term->parent ] );

			if ( $is_top_level || $is_parent_exits ) {
				$ordered_terms[ $term->term_id ] = $term;
			} else {
				$terms[] = $term;
			}
		}

		return $ordered_terms;
	}
}
