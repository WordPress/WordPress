<?php
namespace Elementor\Modules\CloudKitLibrary\Connect;

use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Core\Utils\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Cloud_Kits extends Library {
	const THRESHOLD_UNLIMITED = -1;
	const FAILED_TO_FETCH_QUOTA_KEY = 'failed-to-fetch-quota';

	const FAILED_TO_UPLOAD_KIT = 'cloud-upload-failed';

	const INSUFFICIENT_QUOTA_KEY = 'insufficient-quota';

	const INSUFFICIENT_STORAGE_QUOTA = 'insufficient-storage-quota';

	public function get_title() {
		return esc_html__( 'Cloud Kits', 'elementor' );
	}

	protected function get_api_url(): string {
		return 'https://cloud-library.prod.builder.elementor.red/api/v1/cloud-library';
	}

	/**
	 * @return array|\WP_Error
	 */
	public function get_all( $args = [] ) {
		return $this->http_request( 'GET', 'kits', [], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	/**
	 * @return array|\WP_Error
	 */
	public function get_quota() {
		if ( ! $this->is_connected() ) {
			return new \WP_Error( 'not_connected', esc_html__( 'Not connected', 'elementor' ) );
		}

		return $this->http_request( 'GET', 'quota/kits', [], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	public function validate_quota( $quota ) {
		if ( is_wp_error( $quota ) ) {
			throw new \Error( static::FAILED_TO_FETCH_QUOTA_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$is_unlimited = self::THRESHOLD_UNLIMITED === $quota['threshold'];
		$has_quota = $quota['currentUsage'] < $quota['threshold'];

		if ( ! $is_unlimited && ! $has_quota ) {
			throw new \Error( static::INSUFFICIENT_QUOTA_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

	public function validate_storage_quota( $intended_usage, $quota ) {
		if ( is_wp_error( $quota ) ) {
			throw new \Error( static::FAILED_TO_FETCH_QUOTA_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		if ( empty( $quota['storage']['currentUsage'] ) ) {
			return;
		}

		$has_quota = $quota['storage']['currentUsage'] + $intended_usage < $quota['storage']['threshold'];

		if ( ! $has_quota ) {
			throw new \Error( static::INSUFFICIENT_STORAGE_QUOTA ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

	public function check_eligibility() {
		$quota = $this->get_quota();

		if ( is_wp_error( $quota ) ) {
			return [
				'is_eligible' => false,
				'subscription_id' => '',
			];
		}

		return [
			'is_eligible' => isset( $quota['threshold'] ) && 0 !== $quota['threshold'],
			'subscription_id' => ! empty( $quota['subscriptionId'] ) ? $quota['subscriptionId'] : '',
		];
	}

	public function create_kit( $title, $description, $content_file_data, $preview_file_data, array $includes, string $media_format = 'link', $file_size = 0 ) {
		$quota = $this->get_quota();
		$this->validate_quota( $quota );
		$this->validate_storage_quota( $file_size, $quota );

		$endpoint = 'kits';

		$boundary = wp_generate_password( 24, false );

		$headers = [
			'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
		];

		$body = $this->create_multipart_body(
			[
				'title' => $title,
				'description' => $description,
				'includes' => wp_json_encode( $includes ),
				'mediaFormat' => $media_format,
			],
			[
				'previewFile' => [
					'filename' => 'preview.png',
					'content' => $preview_file_data,
					'content_type' => 'image/png',
				],
			],
			$boundary
		);

		$payload = [
			'headers' => $headers,
			'body' => $body,
			'timeout' => 120,
		];

		$response = $this->http_request( 'POST', $endpoint, $payload, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );

		if ( is_wp_error( $response ) || empty( $response['id'] ) ) {
			throw new \Exception( static::FAILED_TO_UPLOAD_KIT, Exceptions::INTERNAL_SERVER_ERROR ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		if ( empty( $response['uploadUrl'] ) ) {
			$this->delete_kit( $response['id'] );
			throw new \Exception( static::FAILED_TO_UPLOAD_KIT, Exceptions::INTERNAL_SERVER_ERROR ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$upload_success = $this->upload_content_file( $response['uploadUrl'], $content_file_data );

		if ( ! $upload_success ) {
			$this->delete_kit( $response['id'] );
			throw new \Exception( static::FAILED_TO_UPLOAD_KIT, Exceptions::INTERNAL_SERVER_ERROR ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $response;
	}

	public function upload_content_file( $upload_url, $content_file_data ) {
		$upload_response = wp_remote_request( $upload_url, [
			'method' => 'PUT',
			'body' => $content_file_data,
			'headers' => [
				'Content-Type' => 'application/zip',
				'Content-Length' => strlen( $content_file_data ),
			],
			'timeout' => 120,
		] );

		if ( is_wp_error( $upload_response ) ) {
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $upload_response );

		return $response_code >= 200 && $response_code < 300;
	}

	public function get_kit( array $args ) {
		$args = array_merge_recursive( $args, [
			'timeout' => 60, // just in case if zip is big
		] );

		return $this->http_request( 'GET', 'kits/' . $args['id'], $args, [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	public function delete_kit( int $id ) {
		return $this->http_request( 'DELETE', 'kits/' . $id, [], [
			'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
		] );
	}

	private function create_multipart_body( $fields, $files, $boundary ): string {
		$eol = "\r\n";
		$body = '';

		foreach ( $fields as $name => $value ) {
			$body .= "--{$boundary}{$eol}";
			$body .= "Content-Disposition: form-data; name=\"{$name}\"{$eol}{$eol}";
			$body .= "{$value}{$eol}";
		}

		foreach ( $files as $name => $file ) {
			$filename = basename( $file['filename'] );
			$content_type = $file['content_type'];
			$content = $file['content'];

			$body .= "--{$boundary}{$eol}";
			$body .= "Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$filename}\"{$eol}";
			$body .= "Content-Type: {$content_type}{$eol}{$eol}";
			$body .= $content . $eol;
		}

		$body .= "--{$boundary}--{$eol}";

		return $body;
	}

	public function update_kit( $id, array $kit_data ) {
		$endpoint = 'kits/' . $id;

		$request = $this->http_request(
			'PATCH',
			$endpoint,
			[
				'body' => wp_json_encode( $kit_data ),
				'headers' => [
					'Content-Type' => 'application/json',
				],
			],
			[
				'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
			],
		);

		if ( is_wp_error( $request ) ) {
			return false;
		}

		return true;
	}

	protected function init() {}
}
