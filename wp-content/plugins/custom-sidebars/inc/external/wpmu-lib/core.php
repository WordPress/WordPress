<?php
/*
Plugin Name: WPMU Dev code library
Plugin URI:  http://premium.wpmudev.org/
Description: Framework to support creating WordPress plugins and themes.
Version:     1.0.5
Author:      WPMU DEV
Author URI:  http://premium.wpmudev.org/
Textdomain:  wpmu-lib
*/

$dirname = dirname( __FILE__ ) . '/';
$class_file = 'functions-wpmulib.php';

if ( ! class_exists( 'TheLib' ) && file_exists( $dirname . $class_file ) ) {
	require_once( $dirname . $class_file );
}
