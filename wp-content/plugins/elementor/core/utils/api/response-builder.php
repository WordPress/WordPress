<?php

namespace Elementor\Core\Utils\Api;

class Response_Builder {
	private $data;
	private int $status;
	private array $meta = [];

	const NO_CONTENT = 204;

	private function __construct( $data, $status ) {
		$this->data = $data;
		$this->status = $status;
	}

	public static function make( $data = null, $status = 200 ) {
		return new self( $data, $status );
	}

	public function set_meta( array $meta ) {
		$this->meta = $meta;

		return $this;
	}

	public function set_status( int $status ) {
		$this->status = $status;

		return $this;
	}

	public function no_content() {
		return $this->set_status( static::NO_CONTENT );
	}

	public function build() {
		$res_data = static::NO_CONTENT === $this->status
			? null
			: [
				'data' => $this->data,
				'meta' => $this->meta,
			];

		return new \WP_REST_Response( $res_data, $this->status );
	}
}
