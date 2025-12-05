<?php
namespace Elementor\Modules\CloudLibrary\Connect;

use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Core\Utils\Exceptions;
use Elementor\Modules\CloudLibrary\Render_Mode_Preview;
use Elementor\TemplateLibrary\Source_Cloud;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Cloud_Library extends Library {
	public function get_title(): string {
		return esc_html__( 'Cloud Library', 'elementor' );
	}

	protected function get_api_url(): string {
		return 'https://cloud-library.prod.builder.elementor.red/api/v1/cloud-library';
	}

	public function get_resources( $args = [] ): array {
		$templates = [];

		$endpoint = 'resources';

		$query_string = http_build_query( [
			'limit' => isset( $args['limit'] ) ? (int) $args['limit'] : null,
			'offset' => isset( $args['offset'] ) ? (int) $args['offset'] : null,
			'search' => isset( $args['search'] ) ? $args['search'] : null,
			'parentId' => isset( $args['parentId'] ) ? $args['parentId'] : null,
			'templateType' => isset( $args['templateType'] ) ? $args['templateType'] : null,
			'orderBy' => isset( $args['orderby'] ) ? $args['orderby'] : null,
			'order' => isset( $args['order'] ) ? strtoupper( $args['order'] ) : null,
		] );

		$endpoint .= '?' . $query_string;

		$cloud_templates = $this->http_request( 'GET', $endpoint, $args, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $cloud_templates ) || ! is_array( $cloud_templates['data'] ) ) {
			return $templates;
		}

		foreach ( $cloud_templates['data'] as $cloud_template ) {
			$templates[] = $this->prepare_template( $cloud_template );
		}

		return [
			'templates' => $templates,
			'total' => $cloud_templates['total'],
		];
	}

	/**
	 * @return array|\WP_Error
	 */
	public function get_resource( array $args ) {
		return $this->http_request( 'GET', 'resources/' . $args['id'], $args, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	protected function prepare_template( array $template_data ): array {
		$template = [
			'template_id' => $template_data['id'],
			'source' => 'cloud',
			'type' => $template_data['templateType'],
			'subType' => $template_data['type'],
			'title' => $template_data['title'],
			'status' => $template_data['status'],
			'author' => $template_data['authorEmail'],
			'human_date' => date_i18n( get_option( 'date_format' ), strtotime( $template_data['createdAt'] ) ),
			'export_link' => $this->get_export_link( $template_data['id'] ),
			'hasPageSettings' => $template_data['hasPageSettings'],
			'parentId' => $template_data['parentId'],
			'preview_url' => esc_url_raw( $template_data['previewUrl'] ?? '' ),
			'generate_preview_url' => esc_url_raw( $this->generate_preview_url( $template_data ) ?? '' ),
		];

		if ( ! empty( $template_data['content'] ) ) {
			$template['content'] = $template_data['content'];
		}

		return $template;
	}

	private function generate_preview_url( $template_data ): ?string {
		if ( ! empty( $template_data['previewUrl'] ) ||
			Source_Cloud::FOLDER_RESOURCE_TYPE === $template_data['type'] ||
			empty( $template_data['id'] )
		) {
			return null;
		}

		$template_id = $template_data['id'];

		$query_args = [
			'render_mode_nonce' => wp_create_nonce( 'render_mode_' . $template_id ),
			'template_id' => $template_id,
			'render_mode' => Render_Mode_Preview::MODE,
		];

		return set_url_scheme( add_query_arg( $query_args, site_url() ) );
	}

	private function get_export_link( $template_id ) {
		return add_query_arg(
			[
				'action' => 'elementor_library_direct_actions',
				'library_action' => 'export_template',
				'source' => 'cloud',
				'_nonce' => wp_create_nonce( 'elementor_ajax' ),
				'template_id' => $template_id,
			],
			admin_url( 'admin-ajax.php' )
		);
	}

	public function post_resource( $data ): array {
		$resource = [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => wp_json_encode( $data ),
		];

		return $this->http_request( 'POST', 'resources', $resource, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	public function post_bulk_resources( $data ): array {
		$resource = [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => wp_json_encode( $data ),
			'timeout' => 120,
		];

		return $this->http_request( 'POST', 'resources/bulk', $resource, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	public function delete_resource( $template_id ): bool {
		$request = $this->http_request( 'DELETE', 'resources/' . $template_id );

		if ( isset( $request->errors[204] ) && 'No Content' === $request->errors[204][0] ) {
			return true;
		}

		if ( is_wp_error( $request ) ) {
			return false;
		}

		return true;
	}

	public function update_resource( array $template_data ) {
		$endpoint = 'resources/' . $template_data['id'];

		$request = $this->http_request( 'PATCH', $endpoint, [ 'body' => $template_data ], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		return true;
	}

	public function update_resource_preview( $template_id, $file_data ) {
		$endpoint = 'resources/' . $template_id . '/preview';

		$boundary = wp_generate_password( 24, false );

		$headers = [
			'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
		];

		$body = $this->generate_multipart_payload( $file_data, $boundary, $template_id . '_preview.png' );

		$payload = [
			'headers' => $headers,
			'body' => $body,
		];

		$response = $this->http_request( 'PATCH', $endpoint, $payload, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
			'timeout' => 120,
		]);

		if ( is_wp_error( $response ) || empty( $response['preview_url'] ) ) {
			$error_message = esc_html__( 'Failed to save preview.', 'elementor' );

			throw new \Exception( $error_message, Exceptions::INTERNAL_SERVER_ERROR ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $response['preview_url'];
	}

	public function mark_preview_as_failed( $template_id, $error ) {
		$endpoint = 'resources/' . $template_id . '/preview';

		$payload = [
			'body' => [
				'error' => $error,
			],
		];

		$response = $this->http_request( 'PATCH', $endpoint, $payload, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		]);

		if ( is_wp_error( $response ) ) {
			$error_message = esc_html__( 'Failed to mark preview as failed.', 'elementor' );

			throw new \Exception( $error_message, Exceptions::INTERNAL_SERVER_ERROR ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $response;
	}

	/**
	 * @param $file_data
	 * @param $boundary
	 * @param $file_name
	 * @return string
	 */
	private function generate_multipart_payload( $file_data, $boundary, $file_name ): string {
		$payload = '';

		// Append the file
		$payload .= "--{$boundary}\r\n";
		$payload .= 'Content-Disposition: form-data; name="file"; filename="' . esc_attr( $file_name ) . "\"\r\n";
		$payload .= "Content-Type: image/png\r\n\r\n";
		$payload .= $file_data . "\r\n";
		$payload .= "--{$boundary}--\r\n";

		return $payload;
	}

	public function bulk_delete_resources( $template_ids ) {
		$endpoint = 'resources/bulk';

		$endpoint .= '?ids=' . implode( ',', $template_ids );

		$response = $this->http_request( 'DELETE', $endpoint, [], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( isset( $response->errors[204] ) ) {
			return true;
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	public function bulk_undo_delete_resources( $template_ids ) {
		$endpoint = 'resources/bulk-delete/undo';

		$body = wp_json_encode( [ 'ids' => $template_ids ] );

		$request = [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => $body,
		];

		$response = $this->http_request( 'POST', $endpoint, $request, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	public function get_bulk_resources_with_content( $args = [] ): array {
		$templates = [];

		$endpoint = 'resources/bulk';

		$query_string = http_build_query( [
			'ids' => implode( ',', $args['from_template_id'] ),
		] );

		$endpoint .= '?' . $query_string;

		$cloud_templates = $this->http_request( 'GET', $endpoint, $args, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $cloud_templates ) || ! is_array( $cloud_templates ) ) {
			return $templates;
		}

		foreach ( $cloud_templates as $cloud_template ) {
			$templates[] = $this->prepare_template( $cloud_template );
		}

		return $templates;
	}

	public function bulk_move_templates( array $template_data ) {
		$endpoint = 'resources/move';
		$args = [
			'body'    => wp_json_encode( $template_data ),
			'headers' => [ 'Content-Type' => 'application/json' ],
		];

		$request = $this->http_request( 'PATCH', $endpoint, $args, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return array|\WP_Error
	 */
	public function get_quota() {
		if ( ! $this->is_connected() ) {
			return new \WP_Error( 'not_connected', esc_html__( 'Not connected', 'elementor' ) );
		}

		return $this->http_request( 'GET', 'quota', [], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	protected function init() {}
}
