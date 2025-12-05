<?php
namespace Elementor\Data\V2\Base\Exceptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Error_Exception extends Data_Exception {
	public function __construct( \WP_Error $wp_error ) {
		parent::__construct( $wp_error->get_error_message(), $wp_error->get_error_code(), [
			'status' => $wp_error->get_error_code(),
		] );
	}
}
