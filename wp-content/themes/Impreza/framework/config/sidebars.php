<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's sidebars
 *
 * @filter us_config_sidebars
 */

return array(
	'default_sidebar' => array(
		'name' => __( 'Basic Sidebar', 'us' ),
		'id' => 'default_sidebar',
		'description' => __( 'Predefined Widget Area', 'us' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	),
	'footer_first' => array(
		'name' => sprintf( __( 'Footer Column %d', 'us' ), 1 ),
		'id' => 'footer_first',
		'description' => __( 'Predefined Widget Area', 'us' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	),
	'footer_second' => array(
		'name' => sprintf( __( 'Footer Column %d', 'us' ), 2 ),
		'id' => 'footer_second',
		'description' => __( 'Predefined Widget Area', 'us' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	),
	'footer_third' => array(
		'name' => sprintf( __( 'Footer Column %d', 'us' ), 3 ),
		'id' => 'footer_third',
		'description' => __( 'Predefined Widget Area', 'us' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	),
	'footer_fourth' => array(
		'name' => sprintf( __( 'Footer Column %d', 'us' ), 4 ),
		'id' => 'footer_fourth',
		'description' => __( 'Predefined Widget Area', 'us' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	),
);
