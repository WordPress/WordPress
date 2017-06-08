<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Contact form configuration
 *
 * @filter us_config_cform
 */

return array(
	'fields' => array(
		'name' => array(
			'type' => 'textfield',
			'title' => '',
			'placeholder' => __( 'Name', 'us' ),
			'error' => __( 'Please enter your Name', 'us' ),
		),
		'email' => array(
			'type' => 'email',
			'title' => '',
			'placeholder' => __( 'Email', 'us' ),
			'error' => __( 'Please enter your Email', 'us' ),
		),
		'phone' => array(
			'type' => 'textfield',
			'title' => '',
			'placeholder' => __( 'Phone Number', 'us' ),
			'error' => __( 'Please enter your Phone Number', 'us' ),
		),
		'message' => array(
			'type' => 'textarea',
			'title' => '',
			'placeholder' => __( 'Message', 'us' ),
			'error' => __( 'Please enter a Message', 'us' ),
		),
		'captcha' => array(
			'type' => 'captcha',
			'title' => __( 'Just to prove you are a human, please solve the equation: ', 'us' ),
			'placeholder' => '',
			'error' => __( 'Please enter the equation result', 'us' ),
		),
	),
	'submit' => __( 'Send Message', 'us' ),
	'success' => __( 'Thank you! Your message was sent.', 'us' ),
	'error' => array(
		'empty_message' => __( 'Cannot send empty message. Please fill any of the fields.', 'us' ),
		'other' => __( 'Cannot send the message. Please contact the website administrator directly.', 'us' ),
	),
	'subject' => __( 'New message from %s', 'us' ),
);
