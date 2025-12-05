<?php
namespace Elementor\Data\V2\Base\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Error_404 extends Data_Exception {

	protected function get_http_error_code() {
		return 404;
	}

	public function get_code() {
		return 'not-found';
	}

	public function get_message() {
		return '404 not found';
	}
}
