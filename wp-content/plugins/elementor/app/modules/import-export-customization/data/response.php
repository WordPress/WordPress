<?php

namespace Elementor\App\Modules\ImportExportCustomization\Data;

class Response {
	private array $data;
	private array $meta;

	public function __construct( array $data, array $meta = [] ) {
		$this->data = $data;
		$this->meta = $meta;
	}

	public static function success( array $data, array $meta = [] ): \WP_REST_Response {
		$response = new self( $data, $meta );
		return $response->to_wp_rest_response( 200 );
	}

	public static function error( string $code, $message, array $meta = [] ): \WP_REST_Response {
		$response = new self([
			'code' => $code,
			'message' => $message,
		], $meta);

		return $response->to_wp_rest_response( 500 );
	}

	private function to_array(): array {
		return [
			'data' => $this->data,
			'meta' => $this->meta,
		];
	}

	private function to_wp_rest_response( int $status_code = 200 ): \WP_REST_Response {
		return new \WP_REST_Response( $this->to_array(), $status_code );
	}
}
