<?php
namespace Elementor\Data\V2\Base\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Data_Exception extends \Exception {
	protected $custom_data = [
		'code' => '',
		'data' => [],
	];

	public function get_code() {
		return 'reset-http-error';
	}

	public function get_message() {
		return '501 Not Implemented';
	}

	public function get_data() {
		return [
			'status' => $this->get_http_error_code(), // 'status' is used by WP to pass the http error code.
		];
	}

	public function to_wp_error() {
		return new \WP_Error( $this->custom_data['code'], $this->message, $this->custom_data['data'] );
	}

	protected function get_http_error_code() {
		return 501; // 501 Not Implemented
	}

	protected function apply() {}

	public function __construct( $message = '', $code = '', $data = [] ) {
		$this->message = empty( $message ) ? $this->get_message() : $message;
		$this->custom_data['code'] = empty( $code ) ? $this->get_code() : $code;
		$this->custom_data['data'] = empty( $data ) ? $this->get_data() : $data;

		parent::__construct( $this->message, 0, null );

		$this->apply();
	}
}
