<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\App\Modules\ImportExportCustomization\Compatibility\Customization;
use Elementor\App\Modules\ImportExportCustomization\Utils as ImportExportUtils;
use Elementor\Core\Utils\ImportExport\WP_Exporter;

class Wp_Content extends Export_Runner_Base {

	public static function get_name(): string {
		return 'wp-content';
	}

	public function should_export( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'content', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$customization = $data['customization']['content'] ?? null;
		$exclude_post_types = [];

		if ( isset( $customization['customPostTypes'] ) && ! in_array( 'post', $customization['customPostTypes'], true ) ) {
			$exclude_post_types[] = 'post';
		}

		$post_types = ImportExportUtils::get_builtin_wp_post_types( $exclude_post_types );

		$post_types = apply_filters( 'elementor/import-export-customization/wp-content/post-types/customization', $post_types, $data, $customization );

		$custom_post_types = isset( $data['selected_custom_post_types'] ) ? $data['selected_custom_post_types'] : [];

		$files = [];
		$manifest_data = [];

		foreach ( $post_types as $post_type ) {
			$export = $this->export_wp_post_type( $post_type, $customization );

			if ( ! empty( $export['file'] ) ) {
				$files[] = $export['file'];
			}

			$manifest_data['wp-content'][ $post_type ] = $export['manifest_data'];
		}

		foreach ( $custom_post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			$manifest_data['custom-post-type-title'][ $post_type ] = [
				'name' => $post_type_object->name,
				'label' => $post_type_object->label,
			];

			// handled in the previous loop
			if ( 'post' === $post_type ) {
				continue;
			}
			$export = $this->export_wp_post_type( $post_type, $customization );

			if ( ! empty( $export['file'] ) ) {
				$files[] = $export['file'];
			}

			$manifest_data['wp-content'][ $post_type ] = $export['manifest_data'];
		}

		return [
			'files' => $files,
			'manifest' => [
				$manifest_data,
			],
		];
	}

	private function export_wp_post_type( $post_type, $customization ) {
		$exporter_args = [
			'content' => $post_type,
			'status' => 'publish',
			'meta_query' => [
				[
					'key' => static::META_KEY_ELEMENTOR_EDIT_MODE,
					'compare' => 'NOT EXISTS',
				],
			],
			'include_post_featured_image_as_attachment' => true,
		];

		if ( 'pages' !== $post_type ) {
			$exporter_args['limit'] = 20;
		}

		$exporter_args = apply_filters( 'elementor/import-export-customization/export/wp-content/query-args/customization', $exporter_args, $post_type, $customization );

		$wp_exporter = new WP_Exporter( $exporter_args );

		$export_result = $wp_exporter->run();

		return [
			'file' => [
				'path' => 'wp-content/' . $post_type . '/' . $post_type . '.xml',
				'data' => $export_result['xml'],
			],
			'manifest_data' => $export_result['posts'],
		];
	}
}
