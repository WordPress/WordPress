<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The Events Calendar Support
 *
 * @link https://theeventscalendar.com/
 */

if ( function_exists( 'tribe_get_option' ) ) {
	$style_option = tribe_get_option( 'stylesheetOption', 'tribe' );
	if ( $style_option == 'skeleton' ) {
		add_action( 'wp_enqueue_scripts', 'us_dequeue_the_events_calendar_skeleton', 15 );
	}
}

function us_dequeue_the_events_calendar_skeleton() {
	wp_dequeue_style( 'tribe-events-bootstrap-datepicker-css' );
	wp_dequeue_style( 'tribe-events-custom-jquery-styles' );
	wp_dequeue_style( 'tribe-events-calendar-style' );

	global $us_template_directory_uri;
	wp_register_style( 'us-tribe-events', $us_template_directory_uri . '/css/us.tribe-events.css', array(), US_THEMEVERSION, 'all' );
	wp_enqueue_style( 'us-tribe-events' );
}
