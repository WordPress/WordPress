<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's demo-import settings
 *
 * @filter us_config_demo-import
 */
return array(
	'main' => array(
		'title' => 'Main Demo',
		'preview_url' => 'http://impreza.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Header Menu',
			'us_footer_menu' => 'Footer Menu',
		),
		'sliders' => array(
			'slider-home1.zip',
			'slider-home2.zip',
			'slider-home3.zip',
			'slider-portfolio.zip',
		),
		'sidebars' => array(
			'shop_sidebar' => 'Shop Sidebar',
			'bbpress_sidebar' => 'BBPress Sidebar',
			'sidebar-8' => 'FAQ',
			'sidebar-9' => 'Login',
		),
		'woocommerce' => TRUE,
	),
	'onepage' => array(
		'title' => 'One Page Demo',
		'preview_url' => 'http://impreza2.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Header Menu',
		),
	),
	'creative' => array(
		'title' => 'Creative Agency Demo',
		'preview_url' => 'http://impreza3.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Header Menu',
			'us_footer_menu' => 'Footer Menu',
		),
		'sliders' => array(
			'slider-main.zip',
		),
	),
	'portfolio' => array(
		'title' => 'Portfolio Demo',
		'preview_url' => 'http://impreza4.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Main',
		),
		'sliders' => array(
			'slider-home.zip',
			'slider-instagram.zip',
			'slider-portfolio-1.zip',
			'slider-portfolio-2.zip',
			'slider-portfolio-3.zip',
			'slider-portfolio-4.zip',
		),
	),
	'blog' => array(
		'title' => 'Blog Demo',
		'preview_url' => 'http://impreza5.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Header Menu',
			'us_footer_menu' => 'Footer Menu',
		),
		'sliders' => array(
			'slider-popular-posts.zip',
			'slider-posts-carousel.zip',
			'slider-recent_posts.zip',
			'slider-recent-posts-2.zip',
		),
	),
	'restaurant' => array(
		'title' => 'Restaurant Demo',
		'preview_url' => 'http://impreza6.us-themes.com/',
		'front_page' => 'Home',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Main',
			'us_footer_menu' => 'Main',
		),
		'sliders' => array(
			'slider-home.zip',
		),
	),
	'photography' => array(
		'title' => 'Photography Demo',
		'preview_url' => 'http://impreza7.us-themes.com/',
		'front_page' => 'Portrait Series',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Main',
		),
		'sliders' => array(
			'slider-portfolio-carousel.zip',
		),
	),
	'mobile-app' => array(
		'title' => 'Mobile App Demo',
		'preview_url' => 'http://impreza8.us-themes.com/',
		'front_page' => 'Variant 1',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Main',
		),
	),
	'mini-shop' => array(
		'title' => 'Minimal Shop Demo',
		'preview_url' => 'http://impreza9.us-themes.com/',
		'front_page' => 'Shop 1',
		'nav_menu_locations' => array(
			'us_main_menu' => 'Main',
		),
		'sliders' => array(
			'slider-products-1.zip',
			'slider-products-2.zip',
		),
		'woocommerce' => TRUE,
	),
);
