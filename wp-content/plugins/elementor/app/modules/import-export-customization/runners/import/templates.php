<?php
namespace Elementor\App\Modules\ImportExportCustomization\Runners\Import;

use Elementor\App\Modules\ImportExportCustomization\Utils as ImportExportUtils;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;
use Elementor\Modules\Library\Documents\Library_Document;

class Templates extends Import_Runner_Base {
	private $import_session_id;
	private $import_session_metadata = [];

	public static function get_name(): string {
		return 'templates';
	}

	public function should_import( array $data ) {
		return (
			Utils::has_pro() &&
			isset( $data['include'] ) &&
			in_array( 'templates', $data['include'], true ) &&
			! empty( $data['extracted_directory_path'] ) &&
			! empty( $data['manifest']['templates'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$customization = $data['customization']['templates'] ?? null;

		if ( $customization ) {
			return $this->import_with_customization( $data, $imported_data, $customization );
		}

		return $this->import_all( $data, $imported_data );
	}

	private function import_with_customization( array $data, array $imported_data, array $customization ) {
		$result = apply_filters( 'elementor/import-export-customization/import/templates/customization', null, $data, $imported_data, $customization, $this );

		if ( is_array( $result ) ) {
			return $result;
		}

		return $this->import_all( $data, $imported_data );
	}

	private function import_all( array $data, array $imported_data ) {
		$template_types = array_keys( Plugin::$instance->documents->get_document_types( [
			'is_editable' => true,
			'show_in_library' => true,
			'export_group' => Library_Document::EXPORT_GROUP,
		] ) );

		$result = $this->process_templates_import( $data, $template_types );

		/**
		 * Filter the templates import result to allow 3rd parties to add their own imported templates.
		 *
		 * @param array $result The import result structure with 'templates' key containing succeed/failed/succeed_summary.
		 * @param array $data The full import data.
		 * @param array|null $customization The customization settings for templates.
		 * @param object $runner The runner instance.
		 */
		$customization = $data['customization']['templates'] ?? null;
		$result = apply_filters( 'elementor/import-export-customization/import/templates_result', $result, $data, $customization, $this );

		return $result;
	}

	public function process_templates_import( array $data, array $template_types ) {
		$this->import_session_id = $data['session_id'];

		$path = $data['extracted_directory_path'] . 'templates/';
		$templates = $data['manifest']['templates'];

		$result['templates'] = [
			'succeed' => [],
			'failed' => [],
			'succeed_summary' => [],
		];

		foreach ( $templates as $id => $template_settings ) {
			if ( ! empty( $template_types ) && ! in_array( $template_settings['doc_type'], $template_types, true ) ) {
				continue;
			}

			try {
				$template_data = ImportExportUtils::read_json_file( $path . $id );
				$import = $this->import_template( $id, $template_settings, $template_data );

				$result['templates']['succeed'][ $id ] = $import;
				$result['templates']['succeed_summary'][ $template_settings['doc_type'] ] = ( $result['templates']['succeed_summary'][ $template_settings['doc_type'] ] ?? 0 ) + 1;
			} catch ( \Exception $error ) {
				$result['templates']['failed'][ $id ] = $error->getMessage();
			}
		}

		return $result;
	}

	public function import_template( $id, array $template_settings, array $template_data ) {
		$doc_type = $template_settings['doc_type'];

		$new_document = Plugin::$instance->documents->create(
			$doc_type,
			[
				'post_title' => $template_settings['title'],
				'post_type' => Source_Local::CPT,
				'post_status' => 'publish',
			]
		);

		if ( is_wp_error( $new_document ) ) {
			throw new \Exception( esc_html( $new_document->get_error_message() ) );
		}

		$template_data['import_settings'] = $template_settings;
		$template_data['id'] = $id;

		$new_attachment_callback = function( $attachment_id ) {
			$this->set_session_post_meta( $attachment_id, $this->import_session_id );
		};

		add_filter( 'elementor/template_library/import_images/new_attachment', $new_attachment_callback );

		$new_document->import( $template_data );

		remove_filter( 'elementor/template_library/import_images/new_attachment', $new_attachment_callback );

		$document_id = $new_document->get_main_id();

		$this->set_session_post_meta( $document_id, $this->import_session_id );

		return $document_id;
	}

	public function get_import_session_metadata(): array {
		return $this->import_session_metadata;
	}

	public function add_import_session_metadata( $key, $metadata ) {
		$this->import_session_metadata[ $key ] = $metadata;
	}
}
