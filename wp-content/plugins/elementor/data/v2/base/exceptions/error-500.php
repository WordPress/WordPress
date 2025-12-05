<?php
namespace Elementor\Data\V2\Base\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Error_500 extends Data_Exception {

	protected function get_http_error_code() {
		return 500;
	}

	public function get_code() {
		return 'internal-server-error';
	}

	public function get_message() {
		return 'Something went wrong';
	}
}
