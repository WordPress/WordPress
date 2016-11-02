<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Tablets and mobiles missing settings are inherited from default state settings
 */
global $us_template_directory_uri;
return array(

	'simple_1' => array(
		'title' => 'Simple 1',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 60,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array( 'image:1', 'text:1' ),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
				'middle_sticky_height' => 50,
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
				'middle_sticky_height' => 50,
			),
		),
		// Only the values that differ from the elements' defautls
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'text:1' => array(
				'text' => 'LOGO',
			),
		),
	),
	
	'simple_2' => array(
		'title' => 'Simple 2',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 60,
				'middle_fullwidth' => 1,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array( 'image:1' ),
				'middle_center' => array( 'menu:1' ),
				'middle_right' => array( 'socials:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
				'middle_sticky_height' => 50,
			),
			'layout' => array(
				'middle_center' => array(),
				'middle_right' => array( 'menu:1', 'socials:1' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'top_show' => 1,
				'top_height' => 36,
				'top_sticky_height' => 0,
				'middle_height' => 50,
			),
			'layout' => array(
				'top_center' => array( 'socials:1' ),
				'middle_right' => array( 'menu:1' ),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height' => 30,
				'height_sticky' => 30,
				'design_options' => array(
					'margin_right_tablets' => 0,
					'margin_right_mobiles' => 0,
				),
			),
			'menu:1' => array(
				'font_size' => 18,
				'indents' => 60,
			),
			'socials:1' => array(
				'hover' => 'none',
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
				'size' => 20,
				'design_options' => array(
					'margin_right_default' => '-10px',
					'margin_right_tablets' => '-8px',
				),
			),
		),
	),
	
	'simple_3' => array(
		'title' => 'Simple 3',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 80,
				'middle_sticky_height' => 50,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array( 'image:1' ),
				'middle_right' => array( 'menu:1', 'btn:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
				'middle_sticky_height' => 50,
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height' => 30,
				'height_sticky' => 30,
				'design_options' => array(
					'margin_right_tablets' => 0,
					'margin_right_mobiles' => 0,
				),
			),
			'menu:1' => array(
				'font_size' => 17,
			),
			'btn:1' => array(
				'label' => '<strong>BUY NOW</strong>',
				'link' => '#',
				'size' => 15,
				'size_tablets' => 15,
				'design_options' => array(
					'margin_left_mobiles' => 0,
				),
			),
		),
	),
	
	'simple_4' => array(
		'title' => 'Simple 4',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'transparent' => 1,
				'top_show' => 0,
				'middle_height' => 100,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array( 'image:1' ),
				'middle_right' => array( 'menu:1', 'search:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'sticky' => 0,
				'middle_height' => 80,
			),
		),
		'mobiles' => array(
			'options' => array(
				'sticky' => 0,
				'middle_height' => 50,
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'img_transparent' => $us_template_directory_uri . '/framework/img/us-logo-white.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height' => 30,
				'height_sticky' => 30,
				'design_options' => array(
					'margin_right_tablets' => 0,
					'margin_right_mobiles' => 0,
				),
			),
			'menu:1' => array(
				'dropdown_font_size' => 13,
				'mobile_dropdown_font_size' => 13,
				'mobile_width' => 1023,
			),
			'search:1' => array(
				'layout' => 'simple',
				'width_tablets' => 240,
			),
		),
	),

	'extended_1' => array(
		'title' => 'Extended 1',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 1,
				'top_height' => 36,
				'top_sticky_height' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 60,
				'bottom_show' => 0,
			),
			'layout' => array(
				'top_left' => array( 'text:2', 'text:3', 'text:4', 'dropdown:1' ),
				'top_right' => array( 'socials:1' ),
				'middle_left' => array( 'image:1', 'text:1' ),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
			),
			'layout' => array(
				'top_left' => array( 'text:2', 'text:3', 'text:4' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'top_show' => 0,
				'middle_height' => 50,
				'middle_sticky_height' => 50,
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'text:1' => array(
				'text' => 'LOGO',
			),
			'text:2' => array(
				'text' => '+321 123 4567',
				'icon' => 'phone',
			),
			'text:3' => array(
				'text' => 'info@test.com',
				'link' => 'mailto:info@example.com',
				'icon' => 'envelope',
			),
			'text:4' => array(
				'text' => 'Custom Link',
				'link' => '#',
				'icon' => 'star-o',
			),
			'socials:1' => array(
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
			),
			'dropdown:1' => array(
				'link_title' => 'Dropdown',
				'link_1_label' => 'First item',
				'link_1_url' => '#',
				'link_2_label' => 'Second item',
				'link_2_url' => '#',
				'link_3_label' => 'Third item',
				'link_3_url' => '#',
			),
		),
	),

	'extended_2' => array(
		'title' => 'Extended 2',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 0,
				'bottom_show' => 1,
			),
			'layout' => array(
				'middle_left' => array( 'image:1', 'text:1' ),
				'middle_right' => array( 'dropdown:1', 'text:2', 'text:3', 'text:4', 'socials:1' ),
				'bottom_left' => array( 'menu:1' ),
				'bottom_right' => array( 'search:1', 'cart:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 50,
				'middle_sticky_height' => 50,
			),
			'layout' => array(
				'middle_left' => array(),
				'middle_center' => array( 'image:1', 'text:1' ),
				'middle_right' => array(),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
			),
			'layout' => array(
				'middle_left' => array(),
				'middle_center' => array( 'image:1', 'text:1' ),
				'middle_right' => array(),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'search:1' => array(
				'layout' => 'modern',
			),
			'socials:1' => array(
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
			),
			'text:1' => array(
				'text' => 'LOGO',
			),
			'text:2' => array(
				'text' => '+321 123 4567',
				'icon' => 'phone',
			),
			'text:3' => array(
				'text' => 'info@test.com',
				'link' => 'mailto:info@example.com',
				'icon' => 'envelope',
			),
			'text:4' => array(
				'text' => 'Custom Link',
				'link' => '#',
				'icon' => 'star-o',
			),
			'dropdown:1' => array(
				'link_title' => 'Dropdown',
				'link_1_label' => 'First item',
				'link_1_url' => '#',
				'link_2_label' => 'Second item',
				'link_2_url' => '#',
				'link_3_label' => 'Third item',
				'link_3_url' => '#',
			),
		),
	),

	'extended_3' => array(
		'title' => 'Extended 3',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 50,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array( 'image:1' ),
				'middle_right' => array( 'vwrapper:1' ),
				'vwrapper:1' => array( 'hwrapper:1', 'hwrapper:2' ),
				'hwrapper:1' => array( 'dropdown:1', 'text:2', 'text:3', 'socials:1' ),
				'hwrapper:2' => array( 'menu:1', 'search:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'top_show' => 1,
				'middle_height' => 80,
			),
			'layout' => array(
				'top_center' => array( 'dropdown:1', 'text:2', 'text:3', 'socials:1' ),
				'middle_right' => array( 'menu:1', 'search:1' ),
				'vwrapper:1' => array(),
				'hwrapper:1' => array(),
				'hwrapper:2' => array(),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'top_show' => 0,
				'middle_height' => 50,
			),
			'layout' => array(
				'top_center' => array( 'dropdown:1', 'text:2', 'text:3', 'socials:1' ),
				'middle_right' => array( 'menu:1', 'search:1' ),
				'vwrapper:1' => array(),
				'hwrapper:1' => array(),
				'hwrapper:2' => array(),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height_sticky' => 25,
			),
			'vwrapper:1' => array(
				'alignment' => 'right',
			),
			'hwrapper:1' => array(
				'alignment' => 'right',
				'design_options' => array(
					'margin_top_default' => '10px',
					'margin_bottom_default' => '10px',
					'hide_for_sticky' => 1,
				),
			),
			'hwrapper:2' => array(
				'alignment' => 'right',
			),
			'menu:1' => array(
				'font_size' => 18,
			),
			'text:2' => array(
				'text' => 'info@test.com',
				'link' => 'mailto:info@example.com',
				'icon' => 'envelope',
			),
			'text:3' => array(
				'text' => '+321 123 4567',
				'icon' => 'phone',
			),
			'dropdown:1' => array(
				'link_title' => 'Dropdown',
				'link_1_label' => 'First item',
				'link_1_url' => '#',
				'link_2_label' => 'Second item',
				'link_2_url' => '#',
				'link_3_label' => 'Third item',
				'link_3_url' => '#',
			),
			'socials:1' => array(
				'size' => 15,
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
			),
		),
	),

	'extended_4' => array(
		'title' => 'Extended 4',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 120,
				'middle_sticky_height' => 60,
				'bottom_show' => 1,
			),
			'layout' => array(
				'middle_left' => array( 'image:1' ),
				'middle_right' => array( 'vwrapper:1' ),
				'bottom_left' => array( 'menu:1' ),
				'bottom_right' => array( 'dropdown:1', 'cart:1' ),
				'vwrapper:1' => array( 'hwrapper:1', 'search:1' ),
				'hwrapper:1' => array( 'socials:1', 'text:2', 'text:3' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 60,
			),
			'layout' => array(
				'vwrapper:1' => array( 'search:1' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
				'middle_sticky_height' => 0,
			),
			'layout' => array(
				'vwrapper:1' => array( 'search:1' ),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'vwrapper:1' => array(
				'alignment' => 'right',
			),
			'hwrapper:1' => array(
				'alignment' => 'right',
				'design_options' => array(
					'hide_for_sticky' => 1,
				),
			),
			'search:1' => array(
				'text' => 'In search of...',
				'layout' => 'simple',
				'width' => 538,
				'width_tablets' => 340,
			),
			'socials:1' => array(
				'style' => 'colored',
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
			),
			'text:2' => array(
				'text' => 'info@test.com',
				'link' => 'mailto:info@example.com',
				'icon' => 'envelope',
				'size' => 18,
				'design_options' => array(
					'margin_left_default' => '30px',
				),
			),
			'text:3' => array(
				'text' => '+321 123 4567',
				'icon' => 'phone',
				'size' => 18,
				'design_options' => array(
					'margin_left_default' => '30px',
				),
			),
			'dropdown:1' => array(
				'link_title' => 'My Account',
				'link_1_label' => 'Orders',
				'link_1_url' => '#',
				'link_2_label' => 'Favorites',
				'link_2_url' => '#',
				'link_3_label' => 'Sign In',
				'link_3_url' => '#',
				'size' => 16,
			),
			'cart:1' => array(
				'size' => 24,
				'size_tablets' => 22,
				'design_options' => array(
					'margin_left_default' => '10px',
				),
			),
		),
	),

	'centered_1' => array(
		'title' => 'Centered 1',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 50,
				'bottom_show' => 1,
			),
			'layout' => array(
				'middle_center' => array( 'image:1', 'text:1' ),
				'bottom_center' => array( 'menu:1', 'search:1', 'cart:1' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 50,
				'middle_sticky_height' => 0,
			),
			'layout' => array(
				'bottom_left' => array( 'menu:1' ),
				'bottom_center' => array(),
				'bottom_right' => array( 'search:1', 'cart:1' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
				'middle_sticky_height' => 0,
			),
			'layout' => array(
				'bottom_left' => array( 'menu:1' ),
				'bottom_center' => array(),
				'bottom_right' => array( 'search:1', 'cart:1' ),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'text:1' => array(
				'text' => 'LOGO',
			),
			'search:1' => array(
				'layout' => 'fullscreen',
			),
		),
	),

	'centered_2' => array(
		'title' => 'Centered 2',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'transparent' => 1,
				'top_show' => 0,
				'middle_height' => 120,
				'middle_sticky_height' => 50,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_center' => array( 'additional_menu:1', 'image:1', 'additional_menu:2' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 70,
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'middle_height' => 50,
			),
			'layout' => array(
				'middle_center' => array( 'additional_menu:1', 'additional_menu:2' ),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/admin/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height' => 80,
				'height_tablets' => 60,
				'height_sticky' => 40,
				'height_sticky_tablets' => 40,
				'design_options' => array(
					'margin_left_default' => '50px',
					'margin_right_default' => '50px',
					'margin_left_tablets' => '40px',
					'margin_right_tablets' => '40px',
				),
			),
			'additional_menu:1' => array(
				'source' => 'left',
				'size' => 15,
				'size_tablets' => 15,
				'indents' => 50,
				'indents_tablets' => 40,
			),
			'additional_menu:2' => array(
				'source' => 'right',
				'size' => 15,
				'size_tablets' => 15,
				'indents' => 50,
				'indents_tablets' => 40,
			),
		),
	),

	'triple_1' => array(
		'title' => 'Triple 1',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'top_show' => 1,
				'top_height' => 32,
				'top_sticky_height' => 0,
				'middle_height' => 100,
				'middle_sticky_height' => 0,
				'bottom_show' => 1,
			),
			'layout' => array(
				'top_left' => array( 'additional_menu:1' ),
				'top_right' => array( 'text:2' ),
				'middle_left' => array( 'image:1' ),
				'middle_center' => array( 'search:1' ),
				'middle_right' => array( 'vwrapper:1' ),
				'bottom_left' => array( 'menu:1' ),
				'bottom_right' => array( 'cart:1' ),
				'vwrapper:1' => array( 'text:3', 'text:4' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
				'middle_sticky_height' => 60,
			),
		),
		'mobiles' => array(
			'options' => array(
				'scroll_breakpoint' => 50,
				'top_show' => 0,
				'middle_height' => 50,
				'middle_sticky_height' => 50,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_center' => array(),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
				'bottom_left' => array(),
				'bottom_right' => array(),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
			),
			'vwrapper:1' => array(
				'alignment' => 'right',
			),
			'search:1' => array(
				'text' => 'I\'m shopping for...',
				'layout' => 'simple',
				'width' => 440,
				'width_tablets' => 240,
			),
			'additional_menu:1' => array(
				'source' => 'shop',
			),
			'text:2' => array(
				'text' => 'My Account',
				'link' => '#',
				'icon' => 'user',
			),
			'text:3' => array(
				'text' => '<strong>+321 123 4567</strong>',
				'icon' => 'phone',
				'size' => 24,
				'size_tablets' => 20,
				'design_options' => array(
					'margin_bottom_default' => 0,
					'margin_bottom_tablets' => 0,
				),
			),
			'text:4' => array(
				'text' => 'Call from 9pm to 7am (Mon-Fri)',
				'size' => 11,
				'size_tablets' => 11,
				'size_mobiles' => 11,
			),
		),
	),

	'triple_2' => array(
		'title' => 'Triple 2',
		'default' => array(
			'options' => array(
				'orientation' => 'hor',
				'sticky' => 0,
				'top_show' => 1,
				'top_height' => 40,
				'middle_height' => 100,
				'middle_sticky_height' => 0,
				'bottom_show' => 1,
			),
			'layout' => array(
				'top_left' => array( 'text:7' ),
				'top_center' => array( 'text:8' ),
				'top_right' => array( 'btn:1', 'btn:2' ),
				'middle_left' => array( 'image:1', 'search:1' ),
				'middle_right' => array( 'vwrapper:1', 'text:2', 'text:3', 'cart:1' ),
				'bottom_left' => array( 'menu:1' ),
				'bottom_right' => array( 'text:4' ),
				'vwrapper:1' => array( 'text:5', 'text:6' ),
			),
		),
		'tablets' => array(
			'options' => array(
				'middle_height' => 80,
			),
			'layout' => array(
				'top_center' => array(),
				'middle_right' => array( 'text:2', 'text:3', 'cart:1' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'sticky' => 1,
				'scroll_breakpoint' => 50,
				'top_sticky_height' => 0,
				'middle_height' => 50,
				'middle_sticky_height' => 50,
				'bottom_show' => 0,
			),
			'layout' => array(
				'top_left' => array(),
				'top_center' => array( 'btn:1', 'btn:2' ),
				'top_right' => array(),
				'middle_left' => array( 'image:1' ),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
				'bottom_left' => array(),
				'bottom_right' => array(),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'design_options' => array(
					'margin_right_default' => '10%',
				),
			),
			'btn:1' => array(
				'label' => 'SIGN IN',
				'link' => '/my-account/',
				'style' => 'outlined',
				'size' => 12,
				'size_tablets' => 12,
				'color_bg' => '#fff',
				'color_text' => '#fff',
			),
			'btn:2' => array(
				'label' => 'REGISTER',
				'link' => '/my-account/',
				'size' => 12,
				'size_tablets' => 12,
				'color_bg' => '#fff',
				'color_text' => '#666',
				'design_options' => array(
					'margin_left_default' => '10px',
					'margin_left_tablets' => '10px',
					'margin_left_mobiles' => '0',
				),
			),
			'search:1' => array(
				'text' => 'I\'m shopping for...',
				'layout' => 'simple',
				'width' => 380,
				'width_tablets' => 300,
				'design_options' => array(
					'margin_right_default' => '0',
				),
			),
			'text:2' => array(
				'text' => '',
				'icon' => 'phone',
				'size' => 26,
				'size_tablets' => 24,
				'design_options' => array(
					'margin_left_default' => '10%',
				),
			),
			'text:3' => array(
				'text' => '<strong>+321 123 4567<br>+321 123 4568</strong>',
				'size' => 16,
				'size_tablets' => 15,
				'design_options' => array(
					'margin_left_default' => '10px',
					'margin_left_tablets' => '10px',
				),
			),
			'text:4' => array(
				'text' => 'Special Offers',
				'link' => '#',
				'color' => '#f66',
				'size' => 16,
				'size_tablets' => 16,
			),
			'text:5' => array(
				'text' => 'Shipping & Delivery',
				'link' => '#',
				'icon' => 'ship',
				'color' => '#23ccaa',
				'size' => 14,
				'design_options' => array(
					'margin_bottom_default' => '4px',
					'margin_bottom_tablets' => '4px',
				),
			),
			'text:6' => array(
				'text' => 'Order Status',
				'link' => '#',
				'icon' => 'truck',
				'color' => '#23ccaa',
				'size' => 14,
			),
			'text:7' => array(
				'text' => 'Change Location',
				'link' => '#',
				'icon' => 'map-marker',
				'size' => 15,
			),
			'text:8' => array(
				'text' => 'Some short description or notification or something else',
			),
			'cart:1' => array(
				'icon' => 'shopping-basket',
				'size' => 24,
				'design_options' => array(
					'margin_left_default' => '9%',
					'margin_left_tablets' => '5%',
				),
			),
		),
	),

	'vertical_1' => array(
		'title' => 'Vertical 1',
		'default' => array(
			'options' => array(
				'orientation' => 'ver',
				'top_show' => 0,
				'bottom_show' => 0,
			),
			'layout' => array(
				'middle_left' => array(
					'image:1',
					'text:1',
					'menu:1',
					'search:1',
					'cart:1',
					'text:2',
					'text:3',
					'text:4',
					'socials:1',
					'dropdown:1',
				),
			),
		),
		'tablets' => array(
			'options' => array(
				'orientation' => 'hor',
				'middle_height' => 80,
			),
			'layout' => array(
				'top_center' => array( 'text:2', 'text:3', 'text:4', 'dropdown:1', 'socials:1' ),
				'middle_left' => array( 'image:1', 'text:1' ),
				'middle_center' => array(),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
			),
		),
		'mobiles' => array(
			'options' => array(
				'orientation' => 'hor',
				'middle_height' => 50,
			),
			'layout' => array(
				'top_center' => array( 'text:2', 'text:3', 'text:4', 'dropdown:1', 'socials:1' ),
				'middle_left' => array( 'image:1', 'text:1' ),
				'middle_center' => array(),
				'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'design_options' => array(
					'margin_top_default' => '30px',
					'margin_bottom_default' => '30px',
				),
			),
			'menu:1' => array(
				'design_options' => array(
					'margin_bottom_default' => '30px',
				),
			),
			'text:1' => array(
				'text' => 'LOGO',
			),
			'text:2' => array(
				'text' => '+321 123 4567',
				'icon' => 'phone',
				'design_options' => array(
					'margin_bottom_default' => '10px',
				),
			),
			'text:3' => array(
				'text' => 'info@test.com',
				'link' => 'mailto:info@example.com',
				'icon' => 'envelope',
				'design_options' => array(
					'margin_bottom_default' => '10px',
				),
			),
			'text:4' => array(
				'text' => 'Custom Link',
				'link' => '#',
				'icon' => 'star-o',
			),
			'socials:1' => array(
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
				'linkedin' => '#',
				'youtube' => '#',
			),
			'dropdown:1' => array(
				'link_title' => 'Dropdown',
				'link_1_label' => 'First item',
				'link_1_url' => '#',
				'link_2_label' => 'Second item',
				'link_2_url' => '#',
				'link_3_label' => 'Third item',
				'link_3_url' => '#',
			),
		),
	),

	'vertical_2' => array(
		'title' => 'Vertical 2',
		'default' => array(
			'options' => array(
				'orientation' => 'ver',
				'width' => 250,
				'top_show' => 0,
				'bottom_show' => 1,
			),
			'layout' => array(
				'middle_left' => array(
					'image:1',
					'menu:1',
					'search:1',
					'cart:1',
				),
				'bottom_left' => array(
					'text:2',
					'socials:1',
				),
			),
		),
		'tablets' => array(
			'options' => array(
				'orientation' => 'ver',
				'width' => 250,
				'top_show' => 0,
				'bottom_show' => 1,
			),
		),
		'mobiles' => array(
			'options' => array(
				'orientation' => 'ver',
				'top_show' => 0,
				'bottom_show' => 1,
			),
		),
		'data' => array(
			'image:1' => array(
				'img' => $us_template_directory_uri . '/framework/admin/img/us-logo.png',
				'link' => function_exists( 'icl_get_home_url' ) ? icl_get_home_url() : esc_url( home_url( '/' ) ),
				'height' => 90,
				'height_tablets' => 90,
				'height_mobiles' => 60,
				'design_options' => array(
					'margin_top_default' => '50px',
					'margin_bottom_default' => '50px',
					'margin_top_tablets' => '30px',
					'margin_bottom_tablets' => '30px',
				),
			),
			'menu:1' => array(
				'font_size' => 18,
				'indents' => 50,
				'design_options' => array(
					'margin_bottom_default' => '10px',
					'margin_bottom_tablets' => '10px',
					'margin_bottom_mobiles' => '0',
				),
			),
			'search:1' => array(
				'layout' => 'modern',
				'width' => 234,
				'width_tablets' => 234,
				'design_options' => array(
					'margin_bottom_default' => '10px',
					'margin_bottom_tablets' => '10px',
					'margin_bottom_mobiles' => '0',
				),
			),
			'text:2' => array(
				'text' => '+321 123 4567',
				'size' => 18,
				'size_tablets' => 18,
				'size_mobiles' => 16,
			),
			'socials:1' => array(
				'facebook' => '#',
				'google' => '#',
				'twitter' => '#',
			),
		),
	),

);
