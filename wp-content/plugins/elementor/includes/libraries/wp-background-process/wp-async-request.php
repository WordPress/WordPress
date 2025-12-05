<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: _deprecated_file( __FILE__, '3.0.7', '\Elementor\Core\Base\BackgroundProcess\WP_Async_Request' );

if ( ! class_exists( 'WP_Async_Request' ) ) {
	abstract class WP_Async_Request extends \Elementor\Core\Base\BackgroundProcess\WP_Async_Request {
	}
}
