<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\Core\Base\Document;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;
use Elementor\Modules\Library\Documents\Library_Document;

class Templates extends Export_Runner_Base {

	public static function get_name(): string {
		return 'templates';
	}

	public function should_export( array $data ) {
		return (
			Utils::has_pro() &&
			isset( $data['include'] ) &&
			in_array( 'templates', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$customization = $data['customization']['templates'] ?? null;

		if ( $customization ) {
			return $this->export_with_customization( $data, $customization );
		}

		return $this->export_all( $data );
	}

	private function export_with_customization( array $data, array $customization ) {
		$result = apply_filters( 'elementor/import-export-customization/export/templates/customization', null, $data, $customization, $this );

		if ( is_array( $result ) ) {
			return $result;
		}

		return $this->export_all( $data );
	}

	private function export_all( array $data ) {
		$template_types = array_values( Source_Local::get_template_types() );

		return $this->export_templates_by_types( $template_types, $data );
	}

	public function export_templates_by_types( array $template_types, array $data ) {
		$templates_manifest_data = [];
		$files = [];

		if ( ! empty( $template_types ) ) {
			$query_args = [
				'post_type' => Source_Local::CPT,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => [
					[
						'key' => Document::TYPE_META_KEY,
						'value' => $template_types,
					],
				],
			];

			$templates_query = new \WP_Query( $query_args );

			foreach ( $templates_query->posts as $template_post ) {
				$template_id = $template_post->ID;

				$template_document = Plugin::$instance->documents->get( $template_id );

				$templates_manifest_data[ $template_id ] = $template_document->get_export_summary();

				$files[] = [
					'path' => 'templates/' . $template_id,
					'data' => $template_document->get_export_data(),
				];
			}
		}

		$manifest_data['templates'] = $templates_manifest_data;

		$export_data = [
			'files' => $files,
			'manifest' => [
				$manifest_data,
			],
		];

		/**
		 * Filter the templates export data to allow adding additional data.
		 *
		 * @param array $export_data The export data structure with 'files' and 'manifest' keys.
		 * @param array $data The full export data.
		 * @param array|null $customization The customization settings for templates.
		 */
		$customization = $data['customization']['templates'] ?? null;
		$export_data = apply_filters( 'elementor/import-export-customization/export/templates_data', $export_data, $data, $customization );

		return $export_data;
	}
}
