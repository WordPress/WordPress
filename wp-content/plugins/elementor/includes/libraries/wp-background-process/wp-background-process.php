<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: _deprecated_file( __FILE__, '3.0.7', '\Elementor\Core\Base\BackgroundProcess\WP_Background_Process' );

if ( ! class_exists( 'WP_Background_Process' ) ) {
	abstract class WP_Background_Process extends \Elementor\Core\Base\BackgroundProcess\WP_Background_Process {
	}
}
