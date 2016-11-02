<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's options
 *
 * @filter us_config_theme-options
 */

global $us_template_directory_uri, $wp_registered_sidebars;
// Getting Sidebars
$sidebars_options = array();

if ( is_array( $wp_registered_sidebars ) && ! empty( $wp_registered_sidebars ) ) {
	foreach ( $wp_registered_sidebars as $sidebar ) {
		if ( $sidebar['id'] == 'default_sidebar' ) { // If it is default sidebar ...
			$sidebars_options = array_merge( array( $sidebar['id'] => $sidebar['name'] ), $sidebars_options ); // adding it to beginning of default array

		} else {
			$sidebars_options[ $sidebar['id'] ] = $sidebar['name'];
		}
	}
}

// Getting Custom Post Types
$post_type_args = array(
	'public' => TRUE,
	'_builtin' => FALSE,
);
$post_types = get_post_types( $post_type_args, 'objects', 'and' );
$supported_post_types = array( 'forum', 'topic', 'reply', 'product', 'us_portfolio', 'tribe_events' );
$custom_post_types_support_values = array();
foreach ( $post_types as $post_type_name => $post_type ) {
	if ( ! in_array( $post_type_name, $supported_post_types ) ) {
		$custom_post_types_support_values[ $post_type_name ] = $post_type->labels->singular_name;
	}
}

return array(
	'generalsettings' => array(
		'title' => __( 'General Settings', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/settings.png',
		'fields' => array(
			'preloader' => array(
				'title' => __( 'Preloader Screen', 'us' ),
				'type' => 'select',
				'options' => array(
					'disabled' => __( 'Disabled', 'us' ),
					'1' => sprintf( __( 'Shows Preloader %d', 'us' ), 1 ),
					'2' => sprintf( __( 'Shows Preloader %d', 'us' ), 2 ),
					'3' => sprintf( __( 'Shows Preloader %d', 'us' ), 3 ),
					'4' => sprintf( __( 'Shows Preloader %d', 'us' ), 4 ),
					'5' => sprintf( __( 'Shows Preloader %d', 'us' ), 5 ),
					'custom' => __( 'Shows Custom Image', 'us' ),
				),
				'std' => 'disabled',
			),
			'preloader_image' => array(
				'title' => '',
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'classes' => 'for_above',
				'show_if' => array( 'preloader', '=', 'custom' ),
			),
			'page_sidebar' => array(
				'title' => __( 'Sidebar Position by default', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'none',
			),
			'page_sidebar_id' => array(
				'title' => __( 'Sidebar Content by default', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'classes' => 'desc_1',
			),
			'custom_post_types_support' => array(
				'title' => __( 'Support of Custom Post Types', 'us' ),
				'description' => __( 'Mark the needed custom post type, if you want to enable Header, Sidebar, Title Bar and Footer options for it.', 'us' ),
				'type' => 'checkboxes',
				'options' => $custom_post_types_support_values,
				'classes' => ( count( $custom_post_types_support_values ) == 0 ) ? 'hidden' : '',
				'std' => array(),
			),
			'rounded_corners' => array(
				'title' => __( 'Rounded Corners', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable rounded corners of theme elements', 'us' ),
				'std' => 1,
			),
			'links_underline' => array(
				'title' => __( 'Links Underline', 'us' ),
				'type' => 'switch',
				'text' => __( 'Underline text links on hover', 'us' ),
				'std' => 0,
			),
			'generate_css_file' => array(
				'title' => __( 'Theme Options CSS File', 'us' ),
				'type' => 'switch',
				'text' => __( 'Store Theme Options generated styles in a separate CSS file', 'us' ),
				'std' => 0,
			),
			'og_enabled' => array(
				'title' => __( 'Open Graph Data', 'us' ),
				'type' => 'switch',
				'text' => __( 'Create meta-data for social sharings and other Open Graph usages', 'us' ),
				'std' => 1,
			),
			'enable_unsupported_vc_shortcodes' => array(
				'title' => __( 'Unsupported Features of Visual Composer', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable theme-disabled VC\'s shortcodes and features', 'us' ),
				'std' => 0,
				'place_if' => class_exists( 'Vc_Manager' ),
			),
			'enable_unsupported_vc_shortcodes_info' => array(
				'description' => __( '<strong>Note</strong>: Enabling the theme-disabled VC\'s shortcodes and features will reduce your website load speed and performance. Also some of the theme-disabled shortcodes could be not fully supported by the theme and/or not styled properly.', 'us' ),
				'type' => 'message',
				'classes' => 'color_blue for_above',
				'place_if' => class_exists( 'Vc_Manager' ),
				'show_if' => array( 'enable_unsupported_vc_shortcodes', '=', TRUE ),
			),
			'custom_html' => array(
				'title' => __( 'Custom HTML', 'us' ),
				'description' => __( 'Paste your custom code here, it will be added into the footer section of your site. You can use JS code with &lt;script&gt;&lt;/script&gt; tags. Also you can add Google Analytics or other tracking code into this field.', 'us' ),
				'type' => 'html',
				'classes' => 'title_top desc_2',
			),
		),
	),
	'layoutoptions' => array(
		'title' => __( 'Layout Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/layout.png',
		'fields' => array(
			'responsive_layout' => array(
				'title' => __( 'Responsive Layout', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable responsive layout', 'us' ),
				'std' => 1,
			),
			'canvas_layout' => array(
				'title' => __( 'Site Canvas Layout', 'us' ),
				'type' => 'imgradio',
				'options' => array(
					'wide' => 'framework/admin/img/usof/canvas-wide.png',
					'boxed' => 'framework/admin/img/usof/canvas-boxed.png',
				),
				'std' => 'wide',
			),
			'color_body_bg' => array(
				'type' => 'color',
				'title' => __( 'Body Background Color', 'us' ),
				'std' => '#eeeeee',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'body_bg_image' => array(
				'title' => __( 'Body Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'wrapper_body_bg_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array(
					array( 'canvas_layout', '=', 'boxed' ),
					'and',
					array( 'body_bg_image', '!=', '' ),
				),
			),
			'body_bg_image_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'select',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Repeat Horizontally', 'us' ),
					'repeat-y' => __( 'Repeat Vertically', 'us' ),
					'no-repeat' => __( 'Do Not Repeat', 'us' ),
				),
				'std' => 'repeat',
				'classes' => 'cols_2_first title_top',
			),
			'body_bg_image_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'top left' => __( 'Top Left', 'us' ),
					'top center' => __( 'Top Center', 'us' ),
					'top right' => __( 'Top Right', 'us' ),
					'center left' => __( 'Center Left', 'us' ),
					'center center' => __( 'Center Center', 'us' ),
					'center right' => __( 'Center Right', 'us' ),
					'bottom left' => __( 'Bottom Left', 'us' ),
					'bottom center' => __( 'Bottom Center', 'us' ),
					'bottom right' => __( 'Bottom Right', 'us' ),
				),
				'std' => 'top_center',
				'classes' => 'cols_2_last title_top',
			),
			'body_bg_image_attachment' => array(
				'title' => __( 'Background Image Attachment', 'us' ),
				'type' => 'select',
				'options' => array(
					'scroll' => __( 'Scroll', 'us' ),
					'fixed' => __( 'Fixed', 'us' ),
				),
				'std' => 'scroll',
				'classes' => 'cols_2_first title_top',
			),
			'body_bg_image_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'select',
				'options' => array(
					'cover' => __( 'Cover - Image will cover the whole area', 'us' ),
					'contain' => __( 'Contain - Image will fit inside the area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'cols_2_last title_top',
			),
			'wrapper_body_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'site_canvas_width' => array(
				'title' => __( 'Site Canvas Width', 'us' ),
				'type' => 'slider',
				'min' => 1000,
				'max' => 1700,
				'step' => 10,
				'std' => 1300,
				'postfix' => 'px',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'site_content_width' => array(
				'title' => __( 'Site Content Width', 'us' ),
				'type' => 'slider',
				'min' => 900,
				'max' => 1600,
				'step' => 10,
				'std' => 1140,
				'postfix' => 'px',
			),
			'sidebar_width' => array(
				'title' => __( 'Sidebar Width', 'us' ),
				'description' => __( 'This option is applied for pages with sidebar only', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 50,
				'std' => 25,
				'postfix' => '%',
			),
			'content_width' => array(
				'title' => __( 'Content Width', 'us' ),
				'description' => __( 'This option is applied for pages with sidebar only', 'us' ),
				'type' => 'slider',
				'min' => 50,
				'max' => 80,
				'std' => 70,
				'postfix' => '%',
			),
			'columns_stacking_width' => array(
				'title' => __( 'Columns Stacking Width', 'us' ),
				'description' => __( 'When screen width is less than this value, all columns within a row will become a single column.', 'us' ),
				'type' => 'slider',
				'min' => 767,
				'max' => 1024,
				'std' => 767,
				'postfix' => 'px',
			),
			'disable_effects_width' => array(
				'title' => __( 'Effects Disabling Width', 'us' ),
				'description' => __( 'When screen width is less than this value, vertical parallax and animation of elements appearance will be disabled.', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1024,
				'std' => 900,
				'postfix' => 'px',
			),
		),
	),
	'styling' => array(
		'title' => __( 'Styling', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/style.png',
		'fields' => array(
			'color_style' => array(
				'title' => __( 'Choose Website Color Scheme', 'us' ),
				'type' => 'style_scheme',
			),
			
			// Parent wrapper for all color groups
			'change_colors_start' => array(
				'type' => 'wrapper_start',
			),
			
			'change_header_colors_start' => array(
				'title' => __( 'Header colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_header_top_bg' => array(
				'type' => 'color',
				'text' => __( 'Top Area Background Color', 'us' ),
			),
			'color_header_top_text' => array(
				'type' => 'color',
				'text' => __( 'Top Area Text Color', 'us' ),
			),
			'color_header_top_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Top Area Text Hover Color', 'us' ),
			),
			'color_header_middle_bg' => array(
				'type' => 'color',
				'text' => __( 'Main Area Background Color', 'us' ),
			),
			'color_header_middle_text' => array(
				'type' => 'color',
				'text' => __( 'Main Area Text Color', 'us' ),
			),
			'color_header_middle_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Main Area Text Hover Color', 'us' ),
			),
			'color_header_bottom_bg' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Background Color', 'us' ),
			),
			'color_header_bottom_text' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Text Color', 'us' ),
			),
			'color_header_bottom_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Text Hover Color', 'us' ),
			),
			'color_header_transparent_text' => array(
				'type' => 'color',
				'text' => __( 'Transparent Header Text Color', 'us' ),
			),
			'color_header_transparent_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Transparent Header Hover Text Color', 'us' ),
			),
			'color_header_search_bg' => array(
				'type' => 'color',
				'text' => __( 'Search Field Background Color', 'us' ),
			),
			'color_header_search_text' => array(
				'type' => 'color',
				'text' => __( 'Search Field Text Color', 'us' ),
			),
			'change_header_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_menu_colors_start' => array(
				'title' => __( 'Main Menu colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_menu_transparent_active_text' => array(
				'type' => 'color',
				'text' => __( 'Transparent Menu Active Text Color', 'us' ),
			),
			'color_menu_active_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Active Background Color', 'us' ),
			),
			'color_menu_active_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Active Text Color', 'us' ),
			),
			'color_menu_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Hover Background Color', 'us' ),
			),
			'color_menu_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Hover Text Color', 'us' ),
			),
			'color_drop_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Background Color', 'us' ),
			),
			'color_drop_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Text Color', 'us' ),
			),
			'color_drop_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Hover Background Color', 'us' ),
			),
			'color_drop_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Hover Text Color', 'us' ),
			),
			'color_drop_active_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Active Background Color', 'us' ),
			),
			'color_drop_active_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Active Text Color', 'us' ),
			),
			'color_menu_button_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Background Color', 'us' ),
			),
			'color_menu_button_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Text Color', 'us' ),
			),
			'color_menu_button_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Hover Background Color', 'us' ),
			),
			'color_menu_button_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Hover Text Color', 'us' ),
			),
			'change_menu_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_content_colors_start' => array(
				'title' => __( 'Content colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_content_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_content_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_content_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_content_heading' => array(
				'type' => 'color',
				'text' => __( 'Heading Color', 'us' ),
			),
			'color_content_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_content_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_content_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'color_content_primary' => array(
				'type' => 'color',
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_content_secondary' => array(
				'type' => 'color',
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_content_faded' => array(
				'type' => 'color',
				'text' => __( 'Faded Elements Color', 'us' ),
			),
			'change_content_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_alt_content_colors_start' => array(
				'title' => __( 'Alternate Content colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_alt_content_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_alt_content_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_alt_content_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_alt_content_heading' => array(
				'type' => 'color',
				'text' => __( 'Heading Color', 'us' ),
			),
			'color_alt_content_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_alt_content_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_alt_content_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'color_alt_content_primary' => array(
				'type' => 'color',
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_alt_content_secondary' => array(
				'type' => 'color',
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_alt_content_faded' => array(
				'type' => 'color',
				'text' => __( 'Faded Elements Color', 'us' ),
			),
			'change_alt_content_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_subfooter_colors_start' => array(
				'title' => __( 'Top Footer colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_subfooter_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_subfooter_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_subfooter_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_subfooter_heading' => array(
				'type' => 'color',
				'text' => __( 'Heading Color', 'us' ),
			),
			'color_subfooter_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_subfooter_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_subfooter_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'change_subfooter_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_footer_colors_start' => array(
				'title' => __( 'Bottom Footer colors', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'type_toggle',
			),
			'color_footer_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_footer_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_footer_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_footer_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'change_footer_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'change_colors_end' => array(
				'type' => 'wrapper_end',
			),
			
			'custom_css' => array(
				'title' => __( 'Quick CSS', 'us' ),
				'type' => 'css',
				'classes' => 'title_top desc_1',
			),
		),
	),
	'headeroptions' => array(
		'title' => __( 'Header Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/header.png',
		'fields' => array(
			'header_options_layout' => array(
				'title' => __( 'Header Layout', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'header_layout' => array(
				'type' => 'imgradio',
				'options' => array(
					'simple_1' => 'framework/admin/img/usof/ht-standard.png',
					'extended_1' => 'framework/admin/img/usof/ht-extended.png',
					'extended_2' => 'framework/admin/img/usof/ht-advanced.png',
					'centered_1' => 'framework/admin/img/usof/ht-centered.png',
					'vertical_1' => 'framework/admin/img/usof/ht-sided.png',
				),
				'std' => 'simple_1',
				'classes' => 'title_top',
			),
			'header_sticky' => array(
				'title' => __( 'Sticky Header', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'default' => __( 'On Desktops', 'us' ),
					'tablets' => __( 'On Tablets', 'us' ),
					'mobiles' => __( 'On Mobiles', 'us' ),
				),
				'description' => __( 'Fix the header at the top of a page during scroll on all pages', 'us' ),
				'std' => array( 'default', 'tablets', 'mobiles' ),
				'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
			),
			'header_transparent' => array(
				'title' => __( 'Transparent Header', 'us' ),
				'type' => 'switch',
				'text' => __( 'Make the header transparent at its initial position on all pages', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
			),
			'header_fullwidth' => array(
				'title' => __( 'Full Width Content', 'us' ),
				'type' => 'switch',
				'text' => __( 'Stretch header content to the screen width', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
			),
			'header_top_height' => array(
				'title' => __( 'Top Area Height', 'us' ),
				'type' => 'slider',
				'min' => 36,
				'max' => 300,
				'std' => 40,
				'postfix' => 'px',
				'show_if' => array( 'header_layout', '=', 'extended_1' ),
			),
			'header_top_sticky_height' => array(
				'title' => __( 'Top Sticky Area Height', 'us' ),
				'type' => 'slider',
				'min' => 0,
				'max' => 300,
				'std' => 0,
				'postfix' => 'px',
				'show_if' => array(
					array( 'header_sticky', 'has', 'default' ),
					'and',
					array( 'header_layout', '=', 'extended_1' ),
				),
			),
			'header_middle_height' => array(
				'title' => __( 'Main Area Height', 'us' ),
				'type' => 'slider',
				'min' => 50,
				'max' => 300,
				'std' => 100,
				'postfix' => 'px',
				'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
			),
			'header_middle_sticky_height' => array(
				'title' => __( 'Main Sticky Area Height', 'us' ),
				'type' => 'slider',
				'min' => 0,
				'max' => 300,
				'std' => 50,
				'postfix' => 'px',
				'show_if' => array(
					array( 'header_sticky', 'has', 'default' ),
					'and',
					array( 'header_layout', '!=', 'vertical_1' ),
				),
			),
			'header_bottom_height' => array(
				'title' => __( 'Bottom Area Height', 'us' ),
				'type' => 'slider',
				'min' => 36,
				'max' => 300,
				'std' => 50,
				'postfix' => 'px',
				'show_if' => array( 'header_layout', 'in', array( 'extended_2', 'centered_1' ) ),
			),
			'header_bottom_sticky_height' => array(
				'title' => __( 'Bottom Sticky Area Height', 'us' ),
				'type' => 'slider',
				'min' => 0,
				'max' => 300,
				'std' => 50,
				'postfix' => 'px',
				'show_if' => array(
					array( 'header_sticky', 'has', 'default' ),
					'and',
					array( 'header_layout', 'in', array( 'extended_2', 'centered_1' ) ),
				),
			),
			'header_main_width' => array(
				'title' => __( 'Header Width', 'us' ),
				'type' => 'slider',
				'min' => 200,
				'max' => 400,
				'std' => 300,
				'postfix' => 'px',
				'show_if' => array( 'header_layout', '=', 'vertical_1' ),
			),
			'header_invert_logo_pos' => array(
				'title' => __( 'Inverted Logo Position', 'us' ),
				'type' => 'switch',
				'text' => __( 'Place Logo to the right side of the Header', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', 'in', array( 'simple_1', 'extended_1', 'extended_2' ) ),
			),
			'header_scroll_breakpoint' => array(
				'title' => __( 'Header Scroll Breakpoint', 'us' ),
				'description' => __( 'This option sets scroll distance from the top of a page after which the sticky header will be shrunk', 'us' ),
				'type' => 'slider',
				'min' => 1,
				'max' => 200,
				'std' => 100,
				'postfix' => 'px',
				'show_if' => array(
					array( 'header_sticky', 'has', 'default' ),
					'and',
					array( 'header_layout', '!=', 'vertical_1' ),
				),
			),
			'header_options_elements' => array(
				'title' => __( 'Header Elements', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'header_search_show' => array(
				'type' => 'switch',
				'text' => __( 'Show Search Field', 'us' ),
				'std' => 1,
				'classes' => 'title_top',
			),
			'wrapper_search_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array( 'header_search_show', '=', TRUE ),
			),
			'header_search_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'simple' => __( 'Simple', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'fullwidth' => __( 'Full Width', 'us' ),
					'fullscreen' => __( 'Full Screen', 'us' ),
				),
				'std' => 'fullscreen',
			),
			'wrapper_search_end' => array(
				'type' => 'wrapper_end',
			),
			'header_contacts_show' => array(
				'type' => 'switch',
				'text' => __( 'Show Contacts', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
				'classes' => 'title_top',
			),
			'wrapper_contacts_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array(
					array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'and',
					array( 'header_contacts_show', '=', TRUE ),
				),
			),
			'header_contacts_phone' => array(
				'title' => __( 'Phone Number', 'us' ),
				'type' => 'text',
				'classes' => 'cols_2_first title_top',
			),
			'header_contacts_email' => array(
				'title' => __( 'Email', 'us' ),
				'type' => 'text',
				'classes' => 'cols_2_last title_top',
			),
			'header_contacts_custom_icon' => array(
				'title' => __( 'Custom Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="http://designjockey.github.io/material-design-fonticons/" target="_blank">Material Design</a>' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top desc_1',
			),
			'header_contacts_custom_text' => array(
				'title' => __( 'Custom Text', 'us' ),
				'description' => sprintf( __( 'You can use HTML tags in this field (e.g. %s for adding links)', 'us' ), '&lt;a href=""&gt;&lt;/a&gt;' ),
				'type' => 'text',
				'classes' => 'cols_2_last title_top desc_1',
			),
			'wrapper_contacts_end' => array(
				'type' => 'wrapper_end',
			),
			'header_socials_show' => array(
				'type' => 'switch',
				'text' => __( 'Show Social Links', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
				'classes' => 'title_top',
			),
			'wrapper_socials_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array(
					array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'and',
					array( 'header_socials_show', '=', TRUE ),
				),
			),
			'header_socials_facebook' => array(
				'placeholder' => 'Facebook',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_twitter' => array(
				'placeholder' => 'Twitter',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_google' => array(
				'placeholder' => 'Google+',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_linkedin' => array(
				'placeholder' => 'LinkedIn',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_youtube' => array(
				'placeholder' => 'YouTube',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_vimeo' => array(
				'placeholder' => 'Vimeo',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_flickr' => array(
				'placeholder' => 'Flickr',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_behance' => array(
				'placeholder' => 'Behance',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_instagram' => array(
				'placeholder' => 'Instagram',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_xing' => array(
				'placeholder' => 'Xing',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_pinterest' => array(
				'placeholder' => 'Pinterest',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_skype' => array(
				'placeholder' => 'Skype',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_dribbble' => array(
				'placeholder' => 'Dribbble',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_vk' => array(
				'placeholder' => 'Vkontakte',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_tumblr' => array(
				'placeholder' => 'Tumblr',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_soundcloud' => array(
				'placeholder' => 'SoundCloud',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_twitch' => array(
				'placeholder' => 'Twitch',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_yelp' => array(
				'placeholder' => 'Yelp',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_deviantart' => array(
				'placeholder' => 'DeviantArt',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_foursquare' => array(
				'placeholder' => 'Foursquare',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_github' => array(
				'placeholder' => 'GitHub',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_odnoklassniki' => array(
				'placeholder' => 'Odnoklassniki',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_s500px' => array(
				'placeholder' => '500px',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_houzz' => array(
				'placeholder' => 'Houzz',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_medium' => array(
				'placeholder' => 'Medium',
				'type' => 'text',
				'classes' => 'for_social cols_3_first',
			),
			'header_socials_tripadvisor' => array(
				'placeholder' => 'Tripadvisor',
				'type' => 'text',
				'classes' => 'for_social cols_3_middle',
			),
			'header_socials_rss' => array(
				'placeholder' => 'RSS',
				'type' => 'text',
				'classes' => 'for_social cols_3_last',
			),
			'header_socials_custom_url' => array(
				'title' => __( 'Custom Link', 'us' ),
				'type' => 'text',
				'classes' => 'cols_3_first title_top',
			),
			'header_socials_custom_icon' => array(
				'title' => __( 'Custom Link Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="http://designjockey.github.io/material-design-fonticons/" target="_blank">Material Design</a>' ),
				'type' => 'text',
				'classes' => 'cols_3_middle title_top desc_1',
			),
			'header_socials_custom_color' => array(
				'type' => 'color',
				'title' => __( 'Custom Link Color', 'us' ),
				'std' => '#1abc9c',
				'classes' => 'cols_3_last title_top',
			),
			'wrapper_socials_end' => array(
				'type' => 'wrapper_end',
			),
			'header_language_show' => array(
				'type' => 'switch',
				'text' => __( 'Show Dropdown', 'us' ),
				'std' => 0,
				'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
				'classes' => 'title_top',
			),
			'wrapper_lang_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array(
					array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'and',
					array( 'header_language_show', '=', TRUE ),
				),
			),
			'header_language_source' => array(
				'title' => __( 'Source', 'us' ),
				'type' => 'select',
				'options' => array(
					'own' => __( 'My own links', 'us' ),
					'wpml' => 'WPML',
				),
				'std' => 'own',
			),
			'header_link_title' => array(
				'title' => __( 'Links Title', 'us' ),
				'description' => __( 'This text will be shown as the first active item of the dropdown menu.', 'us' ),
				'type' => 'text',
				'show_if' => array( 'header_language_source', '=', 'own' ),
			),
			'header_link_qty' => array(
				'title' => __( 'Links Quantity', 'us' ),
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
				),
				'std' => '2',
				'show_if' => array( 'header_language_source', '=', 'own' ),
			),
			'header_link_1_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array( 'header_language_source', '=', 'own' ),
			),
			'header_link_1_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array( 'header_language_source', '=', 'own' ),
			),
			'header_link_2_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 1 ),
				),
			),
			'header_link_2_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 1 ),
				),
			),
			'header_link_3_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 2 ),
				),
			),
			'header_link_3_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 2 ),
				),
			),
			'header_link_4_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 3 ),
				),
			),
			'header_link_4_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 3 ),
				),
			),
			'header_link_5_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 4 ),
				),
			),
			'header_link_5_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 4 ),
				),
			),
			'header_link_6_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 5 ),
				),
			),
			'header_link_6_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 5 ),
				),
			),
			'header_link_7_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 6 ),
				),
			),
			'header_link_7_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 6 ),
				),
			),
			'header_link_8_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 7 ),
				),
			),
			'header_link_8_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 7 ),
				),
			),
			'header_link_9_label' => array(
				'placeholder' => __( 'Link Label', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'cols_2_first title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 8 ),
				),
			),
			'header_link_9_url' => array(
				'placeholder' => __( 'Link URL', 'us' ),
				'std' => '',
				'type' => 'text',
				'classes' => 'cols_2_last title_top',
				'show_if' => array(
					array( 'header_language_source', '=', 'own' ),
					'and',
					array( 'header_link_qty', '>', 8 ),
				),
			),
			'wrapper_lang_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),
	'logooptions' => array(
		'title' => __( 'Logo Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/logo.png',
		'fields' => array(
			'logo_type' => array(
				'title' => __( 'Logo Type', 'us' ),
				'type' => 'imgradio',
				'options' => array(
					'text' => 'framework/admin/img/usof/logo-text.png',
					'img' => 'framework/admin/img/usof/logo-img.png',
				),
				'std' => 'text',
			),
			'logo_text' => array(
				'title' => __( 'Logo Text', 'us' ),
				'description' => __( 'Add text which will be shown as logo. Better keep it short.', 'us' ),
				'type' => 'text',
				'std' => 'LOGO',
				'show_if' => array( 'logo_type', '=', 'text' ),
			),
			'logo_font_size' => array(
				'title' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 12,
				'max' => 50,
				'std' => 26,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'text' ),
			),
			'logo_font_size_tablets' => array(
				'title' => __( 'Font Size on Tablets', 'us' ),
				'description' => __( 'This option is enabled when screen width is less than 900px', 'us' ),
				'type' => 'slider',
				'min' => 12,
				'max' => 50,
				'std' => 24,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'text' ),
			),
			'logo_font_size_mobiles' => array(
				'title' => __( 'Font Size on Mobiles', 'us' ),
				'description' => __( 'This option is enabled when screen width is less than 600px', 'us' ),
				'type' => 'slider',
				'min' => 12,
				'max' => 50,
				'std' => 20,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'text' ),
			),
			'logo_image' => array(
				'title' => __( 'Logo Image', 'us' ),
				'description' => __( 'Maximum recommended size is 300px of height (also for retina displays)', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_height' => array(
				'title' => __( 'Height', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 300,
				'std' => 60,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_height_sticky' => array(
				'title' => __( 'Height in the Sticky Header', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 300,
				'std' => 60,
				'postfix' => 'px',
				'show_if' => array(
					array( 'logo_type', '=', 'img' ),
					'and',
					array( 'header_layout', '!=', 'vertical_1' ),
				),
			),
			'logo_height_tablets' => array(
				'title' => __( 'Height on Tablets', 'us' ),
				'description' => __( 'This option is enabled when screen width is less than 900px', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 300,
				'std' => 40,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_height_mobiles' => array(
				'title' => __( 'Height on Mobiles', 'us' ),
				'description' => __( 'This option is enabled when screen width is less than 600px', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 300,
				'std' => 30,
				'postfix' => 'px',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_additional_images' => array(
				'title' => __( 'Additional Logo Images (optional)', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_image_transparent' => array(
				'title' => __( 'For Transparent Header', 'us' ),
				'description' => __( 'This image will be shown instead of default when the header is transparent', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_image_tablets' => array(
				'title' => __( 'On Tablets', 'us' ),
				'description' => __( 'This image will be shown instead of default when screen width is less than 900px', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
			'logo_image_mobiles' => array(
				'title' => __( 'On Mobiles', 'us' ),
				'description' => __( 'This image will be shown instead of default when screen width is less than 600px', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'show_if' => array( 'logo_type', '=', 'img' ),
			),
		),
	),
	'menuoptions' => array(
		'title' => __( 'Menu Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/menu.png',
		'fields' => array(
			'menu_fontsize' => array(
				'title' => __( 'Main Items Font Size', 'us' ),
				'type' => 'slider',
				'min' => 12,
				'max' => 50,
				'std' => 16,
				'postfix' => 'px',
			),
			'menu_indents' => array(
				'title' => __( 'Main Items Indents', 'us' ),
				'description' => __( 'This option sets the distance between the neighboring menu items.', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 100,
				'step' => 2,
				'std' => 40,
				'postfix' => 'px',
			),
			'menu_height' => array(
				'title' => __( 'Main Items Height', 'us' ),
				'type' => 'switch',
				'text' => __( 'Stretch menu items to the full height of the header', 'us' ),
				'std' => 0,
			),
			'menu_hover_effect' => array(
				'title' => __( 'Main Items Hover Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => __( 'Simple', 'us' ),
					'underline' => __( 'Underline', 'us' ),
				),
				'std' => 'underline',
			),
			'menu_dropdown_effect' => array(
				'title' => __( 'Dropdown Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'opacity' => __( 'FadeIn', 'us' ),
					'height' => __( 'FadeIn + SlideDown', 'us' ),
					'mdesign' => __( 'Material Design Effect', 'us' ),
				),
				'std' => 'height',
			),
			'menu_sub_fontsize' => array(
				'title' => __( 'Sub Items Font Size', 'us' ),
				'type' => 'slider',
				'min' => 12,
				'max' => 50,
				'std' => 15,
				'postfix' => 'px',
			),
			'menu_mobile_width' => array(
				'title' => __( 'Mobile Menu at width', 'us' ),
				'description' => __( 'When screen width is less than this value, main menu transforms to mobile-friendly layout.', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 2000,
				'std' => 900,
				'postfix' => 'px',
			),
			'menu_togglable_type' => array(
				'title' => __( 'Mobile Menu Behaviour', 'us' ),
				'description' => __( 'When this option is disabled, sub items of mobile menu will open by click on arrows only.', 'us' ),
				'type' => 'switch',
				'text' => __( 'Open sub items by click on menu titles', 'us' ),
				'std' => 1,
			),
		),
	),
	'titlebaroptions' => array(
		'title' => __( 'Title Bar Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/titlebar.png',
		'fields' => array(
			'titlebar_heading' => array(
				'title' => __( 'Regular Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'titlebar_content' => array(
				'title' => __( 'Title Bar Content', 'us' ),
				'type' => 'select',
				'options' => array(
					'all' => __( 'Title, Description, Breadcrumbs', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => 'all',
			),
			'titlebar_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'large',
			),
			'titlebar_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
				),
				'std' => 'alternate',
			),
			'titlebar_portfolio_heading' => array(
				'title' => __( 'Portfolio Items', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'titlebar_portfolio_content' => array(
				'title' => __( 'Title Bar Content', 'us' ),
				'type' => 'select',
				'options' => array(
					'all' => __( 'Title, Description, Arrows', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => 'all',
			),
			'titlebar_portfolio_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'large',
			),
			'titlebar_portfolio_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
				),
				'std' => 'alternate',
			),
			'titlebar_archive_heading' => array(
				'title' => __( 'Archive, Search Results Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'titlebar_archive_content' => array(
				'title' => __( 'Title Bar Content', 'us' ),
				'type' => 'select',
				'options' => array(
					'all' => __( 'Title, Description, Breadcrumbs', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => 'all',
			),
			'titlebar_archive_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
			),
			'titlebar_archive_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
				),
				'std' => 'alternate',
			),
			'titlebar_post_heading' => array(
				'title' => __( 'Blog Posts', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'titlebar_post_content' => array(
				'title' => __( 'Title Bar Content', 'us' ),
				'type' => 'select',
				'options' => array(
					'all' => __( 'Title, Description, Breadcrumbs', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => 'hide',
			),
			'titlebar_post_title' => array(
				'title' => __( 'Title Bar Title', 'us' ),
				'type' => 'text',
				'std' => 'Blog',
				'show_if' => array( 'titlebar_post_content', '!=', 'hide' ),
			),
			'titlebar_post_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
			),
			'titlebar_post_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
				),
				'std' => 'alternate',
			),
		),
	),
	'footeroptions' => array(
		'title' => __( 'Footer Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/footer.png',
		'fields' => array(
			'footer_layout' => array(
				'title' => __( 'Footer Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'compact' => __( 'Compact', 'us' ),
					'modern' => __( 'Modern', 'us' ),
				),
				'std' => 'compact',
			),
			'footer_show_top' => array(
				'title' => __( 'Top Footer area', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show widgets area', 'us' ),
				'std' => 0,
			),
			'footer_columns' => array(
				'title' => __( 'Columns', 'us' ),
				'description' => sprintf( __( 'Set number of columns in the top Footer area. You can populate these columns with <a target="_blank" href="%s">widgets</a>.', 'us' ), admin_url() . 'widgets.php' ),
				'type' => 'radio',
				'show_if' => array( 'footer_show_top', '=', TRUE ),
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'std' => 3,
				'classes' => 'desc_1',
			),
			'footer_show_bottom' => array(
				'title' => __( 'Bottom Footer area', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show copyright and menu area', 'us' ),
				'std' => 1,
			),
			'footer_copyright' => array(
				'title' => __( 'Copyright Text', 'us' ),
				'type' => 'text',
				'std' => 'Any text goes here',
				'show_if' => array( 'footer_show_bottom', '=', TRUE ),
			),
		),
	),
	'typography' => array(
		'title' => __( 'Typography', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/font.png',
		'fields' => array(
			'body_font_options' => array(
				'title' => __( 'Regular Text', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'body_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec condimentum tellus purus condimentum pulvinar. Duis cursus bibendum dui, eget iaculis urna pharetra. Aenean semper nec ipsum vitae mollis.', 'us' ),
					'size' => '15px',
				),
				'std' => 'Open Sans|400,700',
			),
			'wrapper_body_font_start' => array(
				'type' => 'wrapper_start',
			),
			'body_font_desktop_sizes' => array(
				'title' => __( 'Sizes on Desktops', 'us' ),
				'type' => 'heading',
				'classes' => 'cols_2_first for_font',
			),
			'body_font_mobile_sizes' => array(
				'title' => __( 'Sizes on Mobiles', 'us' ),
				'type' => 'heading',
				'classes' => 'cols_2_last for_font',
			),
			'body_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '14',
				'classes' => 'cols_2_first for_font',
			),
			'body_fontsize_mobile' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '16',
				'classes' => 'cols_2_last for_font',
			),
			'body_lineheight' => array(
				'description' => __( 'Line height', 'us' ),
				'type' => 'text',
				'std' => '24',
				'classes' => 'cols_2_first for_font',
			),
			'body_lineheight_mobile' => array(
				'description' => __( 'Line height', 'us' ),
				'type' => 'text',
				'std' => '28',
				'classes' => 'cols_2_last for_font',
			),
			'wrapper_body_font_end' => array(
				'type' => 'wrapper_end',
			),
			
			'headings_options' => array(
				'title' => __( 'Headings', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'heading_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Heading Font Preview', 'us' ),
					'size' => '30px',
				),
				'std' => 'Open Sans|400,700',
			),
			'h1_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 1 ),
				'type' => 'wrapper_start',
			),
			'h1_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '40',
				'classes' => 'for_font',
			),
			'h1_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '30',
				'classes' => 'for_font',
			),
			'h1_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h1_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h1_end' => array(
				'type' => 'wrapper_end',
			),
			'h2_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 2 ),
				'type' => 'wrapper_start',
			),
			'h2_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '34',
				'classes' => 'for_font',
			),
			'h2_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '26',
				'classes' => 'for_font',
			),
			'h2_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h2_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h2_end' => array(
				'type' => 'wrapper_end',
			),
			'h3_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 3 ),
				'type' => 'wrapper_start',
			),
			'h3_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '28',
				'classes' => 'for_font',
			),
			'h3_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '24',
				'classes' => 'for_font',
			),
			'h3_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h3_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h3_end' => array(
				'type' => 'wrapper_end',
			),
			'h4_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 4 ),
				'type' => 'wrapper_start',
			),
			'h4_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '24',
				'classes' => 'for_font',
			),
			'h4_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '22',
				'classes' => 'for_font',
			),
			'h4_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h4_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h4_end' => array(
				'type' => 'wrapper_end',
			),
			'h5_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 5 ),
				'type' => 'wrapper_start',
			),
			'h5_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '20',
				'classes' => 'for_font',
			),
			'h5_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '20',
				'classes' => 'for_font',
			),
			'h5_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h5_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h5_end' => array(
				'type' => 'wrapper_end',
			),
			'h6_start' => array(
				'title' => sprintf( __( 'Heading %d', 'us' ), 6 ),
				'type' => 'wrapper_start',
			),
			'h6_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'text',
				'std' => '18',
				'classes' => 'for_font',
			),
			'h6_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'text',
				'std' => '18',
				'classes' => 'for_font',
			),
			'h6_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'text',
				'std' => '0',
				'classes' => 'for_font',
			),
			'h6_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => '',
				'classes' => 'for_font',
			),
			'h6_end' => array(
				'type' => 'wrapper_end',
			),
			
			'menu_font_options' => array(
				'title' => __( 'Main Menu Text', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'menu_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Home About Services Portfolio Contacts', 'us' ),
					'size' => '16px',
				),
				'std' => 'Open Sans|400,700',
			),
			'subset_option' => array(
				'title' => __( 'Subset', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'font_subset' => array(
				'description' => __( 'Select characters subset for Google fonts. <strong>Please note: some fonts does not support particular subsets!</strong>', 'us' ),
				'type' => 'select',
				'options' => array(
					'latin' => 'latin',
					'latin-ext' => 'latin-ext',
					'cyrillic' => 'cyrillic',
					'cyrillic-ext' => 'cyrillic-ext',
					'greek' => 'greek',
					'greek-ext' => 'greek-ext',
					'vietnamese' => 'vietnamese',
					'khmer' => 'khmer',
				),
				'std' => 'latin',
				'classes' => 'title_top desc_1',
			),
		),
	),
	'buttonoptions' => array(
		'title' => __( 'Buttons Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/button.png',
		'fields' => array(
			'button_preview' => array(
				'type' => 'button_preview',
				'classes' => 'title_top',
			),
			'button_text_style' => array(
				'title' => __( 'Text Styles', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'bold' => us_translate_with_external_domain( 'Bold' ),
					'uppercase' => __( 'Uppercase', 'us' ),
				),
				'std' => array( 'bold', 'uppercase' ),
			),
			'button_font' => array(
				'title' => __( 'Use Font from', 'us' ),
				'type' => 'radio',
				'options' => array(
					'body' => __( 'Regular Text', 'us' ),
					'heading' => __( 'Headings', 'us' ),
					'menu' => __( 'Main Menu Text', 'us' ),
				),
				'std' => 'body',
			),
			'button_fontsize' => array(
				'title' => __( 'Default Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 20,
				'std' => 15,
				'postfix' => 'px',
			),
			'button_letterspacing' => array(
				'title' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -3,
				'max' => 5,
				'std' => 0,
				'postfix' => 'px',
			),
			'button_height' => array(
				'title' => __( 'Relative Height', 'us' ),
				'type' => 'slider',
				'min' => 1.5,
				'max' => 5.0,
				'step' => 0.1,
				'std' => 2.8,
				'postfix' => 'em',
			),
			'button_width' => array(
				'title' => __( 'Relative Width', 'us' ),
				'type' => 'slider',
				'min' => 0.5,
				'max' => 5.0,
				'step' => 0.1,
				'std' => 1.8,
				'postfix' => 'em',
			),
			'button_border_radius' => array(
				'title' => __( 'Corners Radius', 'us' ),
				'type' => 'slider',
				'min' => 0.0,
				'max' => 2.5,
				'step' => 0.1,
				'std' => 0.3,
				'postfix' => 'em',
			),
		),
	),
	'portfoliooptions' => array(
		'title' => __( 'Portfolio Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/portfolio.png',
		'fields' => array(
			'portfolio_sidebar' => array(
				'title' => __( 'Sidebar Position on Portfolio Items', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'none',
			),
			'portfolio_sidebar_id' => array(
				'title' => __( 'Sidebar Content on Portfolio Items', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'classes' => 'desc_1',
			),
			'portfolio_comments' => array(
				'title' => __( 'Portfolio Comments', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable comments for Portfolio Item pages', 'us' ),
				'std' => 0,
			),
			'portfolio_sided_nav' => array(
				'title' => __( 'Sided Navigation', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show previous/next portfolio items on sides of the screen', 'us' ),
				'std' => 1,
			),
			'portfolio_prevnext_category' => array(
				'title' => __( 'Navigation Within a Category', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable previous/next portfolio item navigation within a category', 'us' ),
				'std' => 0,
			),
			'portfolio_slug' => array(
				'title' => __( 'Portfolio Slug', 'us' ),
				'type' => 'text',
				'std' => 'portfolio',
			),
			'portfolio_category_slug' => array(
				'title' => __( 'Portfolio Category Slug', 'us' ),
				'type' => 'text',
				'std' => 'portfolio_category',
			),
		),
	),
	'blogoptions' => array(
		'title' => __( 'Blog Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/blog.png',
		'fields' => array(
			'blog_options_post_pages' => array(
				'title' => __( 'Blog Posts', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'post_sidebar' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'post_preview_layout' => array(
				'title' => __( 'Featured Image Layout', 'us' ),
				'description' => __( 'This option sets Featured Image Layout for all post pages. You can set it for a separate certain post when editing it.', 'us' ),
				'type' => 'select',
				'options' => array(
					'basic' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
					'none' => __( 'No Preview', 'us' ),
				),
				'std' => 'basic',
			),
			'post_meta' => array(
				'title' => __( 'Post Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => __( 'Date', 'us' ),
					'author' => __( 'Author', 'us' ),
					'categories' => __( 'Categories', 'us' ),
					'comments' => __( 'Comments number', 'us' ),
					'tags' => __( 'Tags', 'us' ),
				),
				'std' => array( 'date', 'author', 'categories', 'comments', 'tags' ),
			),
			'post_sharing' => array(
				'title' => __( 'Sharing Buttons', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show block with sharing buttons', 'us' ),
				'std' => 0,
			),
			'post_sharing_type' => array(
				'title' => __( 'Buttons Type', 'us' ),
				'type' => 'select',
				'options' => array(
					'simple' => __( 'Simple', 'us' ),
					'solid' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
				),
				'std' => 'simple',
				'show_if' => array( 'post_sharing', '=', TRUE ),
			),
			'post_sharing_providers' => array(
				'title' => '',
				'type' => 'checkboxes',
				'options' => array(
					'email' => 'Email',
					'facebook' => 'Facebook',
					'twitter' => 'Twitter',
					'gplus' => 'Google+',
					'linkedin' => 'LinkedIn',
					'pinterest' => 'Pinterest',
					'vk' => 'Vkontakte',
				),
				'std' => array( 'facebook', 'twitter', 'gplus' ),
				'show_if' => array( 'post_sharing', '=', TRUE ),
			),
			'post_author_box' => array(
				'title' => __( 'Author Box', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show box with information about post author', 'us' ),
				'std' => 0,
			),
			'post_nav' => array(
				'title' => __( 'Prev/Next Navigation', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show links to previous/next posts', 'us' ),
				'std' => 0,
			),
			'post_nav_category' => array(
				'title' => __( 'Navigation Within a Category', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable previous/next posts navigation within a category', 'us' ),
				'std' => 0,
				'show_if' => array( 'post_nav', '=', TRUE ),
			),
			'post_related' => array(
				'title' => __( 'Related Posts', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show list of posts with same tags on every blog post', 'us' ),
				'std' => 1,
			),
			'post_related_layout' => array(
				'title' => __( 'Related Posts Layout', 'us' ),
				'type' => 'select',
				'show_if' => array( 'post_related', '=', TRUE ),
				'options' => array(
					'compact' => __( 'Compact (without preview)', 'us' ),
					'related' => __( 'Standard (3 columns with preview)', 'us' ),
				),
				'std' => 'compact',
			),
			'blog_options_front_page' => array(
				'title' => __( 'Blog Home Page', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'blog_sidebar' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'blog_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'classic',
			),
			'blog_masonry' => array(
				'type' => 'switch',
				'text' => __( 'Enable Masonry layout mode', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'blog_layout', 'in', array( 'classic', 'flat', 'tiles', 'cards' ) ),
			),
			'blog_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'blog_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => __( 'Excerpt', 'us' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => __( 'None', 'us' ),
				),
				'std' => 'excerpt',
			),
			'blog_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => __( 'Date', 'us' ),
					'author' => __( 'Author', 'us' ),
					'categories' => __( 'Categories', 'us' ),
					'comments' => __( 'Comments number', 'us' ),
					'tags' => __( 'Tags', 'us' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date', 'author', 'categories', 'comments', 'tags', 'read_more' ),
			),
			'blog_pagination' => array(
				'title' => __( 'Pagination', 'us' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),
			'blog_options_archive' => array(
				'title' => __( 'Archive Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'archive_sidebar' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'archive_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'smallcircle',
			),
			'archive_masonry' => array(
				'type' => 'switch',
				'text' => __( 'Enable Masonry layout mode', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'archive_layout', 'in', array( 'classic', 'flat', 'tiles', 'cards' ) ),
			),
			'archive_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'archive_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => __( 'Excerpt', 'us' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => __( 'None', 'us' ),
				),
				'std' => 'excerpt',
			),
			'archive_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => __( 'Date', 'us' ),
					'author' => __( 'Author', 'us' ),
					'categories' => __( 'Categories', 'us' ),
					'comments' => __( 'Comments number', 'us' ),
					'tags' => __( 'Tags', 'us' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date', 'author', 'comments', 'tags' ),
			),
			'archive_pagination' => array(
				'title' => __( 'Pagination', 'us' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),
			'blog_options_search_results' => array(
				'title' => __( 'Search Results Page', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'search_sidebar' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'search_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'compact',
			),
			'search_masonry' => array(
				'type' => 'switch',
				'text' => __( 'Enable Masonry layout mode', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'search_layout', 'in', array( 'classic', 'flat', 'tiles', 'cards' ) ),
			),
			'search_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'search_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => __( 'Excerpt', 'us' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => __( 'None', 'us' ),
				),
				'std' => 'excerpt',
			),
			'search_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => __( 'Date', 'us' ),
					'author' => __( 'Author', 'us' ),
					'categories' => __( 'Categories', 'us' ),
					'comments' => __( 'Comments number', 'us' ),
					'tags' => __( 'Tags', 'us' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date' ),
			),
			'search_pagination' => array(
				'title' => __( 'Pagination', 'us' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),
			'blog_options_excerpt' => array(
				'title' => __( 'More Options', 'us' ),
				'type' => 'heading',
				'classes' => 'align_center with_separator',
			),
			'excerpt_length' => array(
				'title' => __( 'Excerpt Length', 'us' ),
				'description' => __( 'This option sets amount of words in the Excerpt. To show all the words, leave this field blank.', 'us' ),
				'type' => 'text',
				'std' => '55',
			),
			'blog_img_size_start' => array(
				'title' => __( 'Blog Images Size', 'us' ),
				'type' => 'wrapper_start',
			),
			'blog_img_width' => array(
				'description' => 'X',
				'type' => 'text',
				'std' => '600',
				'classes' => 'for_font',
			),
			'blog_img_height' => array(
				'description' => 'px',
				'type' => 'text',
				'std' => '400',
				'classes' => 'for_font',
			),
			'blog_img_size_end' => array(
				'type' => 'wrapper_end',
			),
			'blog_img_size_info' => array(
				'description' => sprintf( __( 'Set custom size for images which are used as posts previews in blog with Classic, Flat, Cards, Tiles layouts and in Related Posts. After changing the values you need to %sregenerate thumbnails%s.', 'us' ), '<a target="_blank" href="' . admin_url() . 'plugin-install.php?tab=search&s=Regenerate+Thumbnails">', '</a>' ),
				'type' => 'message',
				'classes' => 'for_img_size',
			),
		),
	),
	'woocommerce' => array(
		'title' => __( 'WooCommerce', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/cart.png',
		'place_if' => class_exists( 'woocommerce' ),
		'fields' => array(
			'shop_titlebar_content' => array(
				'title' => __( 'Title Bar Content', 'us' ),
				'description' => __( 'This option is applied to all Shop pages and Product pages', 'us' ),
				'std' => 'hide',
				'type' => 'select',
				'options' => array(
					'all' => __( 'Title, Description, Breadcrumbs', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
			),
			'shop_sidebar' => array(
				'title' => __( 'Sidebar Position on Shop Pages', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'shop_sidebar_id' => array(
				'title' => __( 'Sidebar Content on Shop Pages', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'classes' => 'desc_1',
			),
			'product_sidebar' => array(
				'title' => __( 'Sidebar Position on Product Pages', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'product_sidebar_id' => array(
				'title' => __( 'Sidebar Content on Product Pages', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'classes' => 'desc_1',
			),
			'shop_listing_style' => array(
				'title' => __( 'Products Grid Style', 'us' ),
				'description' => __( 'This option sets style of products grid for all pages', 'us' ),
				'std' => 'standard',
				'type' => 'radio',
				'options' => array(
					'standard' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
				),
			),
			'shop_columns' => array(
				'title' => __( 'Products Grid Columns', 'us' ),
				'description' => __( 'This option sets products amount per row for Shop pages', 'us' ),
				'std' => '3',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'product_related_qty' => array(
				'title' => __( 'Related Products Quantity', 'us' ),
				'description' => __( 'This option sets Related Products quantity for Product pages and Cart page', 'us' ),
				'std' => '3',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'shop_cart' => array(
				'title' => __( 'Cart Page Style', 'us' ),
				'std' => 'compact',
				'type' => 'radio',
				'options' => array(
					'standard' => __( 'Standard', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
			),
			'shop_catalog' => array(
				'title' => __( 'Catalog Mode', 'us' ),
				'type' => 'switch',
				'text' => __( 'Disable ability to buy products via removing "Add to Cart" buttons', 'us' ),
				'std' => 0,
			),
		),
	),
	'bbpress' => array(
		'title' => 'bbPress',
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/comments.png',
		'place_if' => class_exists( 'bbPress' ),
		'fields' => array(
			'forum_sidebar' => array(
				'title' => __( 'Sidebar Position on Forum Pages', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => __( 'Left', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
				),
				'std' => 'right',
			),
			'forum_sidebar_id' => array(
				'title' => __( 'Sidebar Content on Forum Pages', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'classes' => 'desc_1',
			),
		),
	),
	'manageoptions' => array(
		'title' => __( 'Manage Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/backups.png',
		'fields' => array(
			'of_reset' => array(
				'title' => __( 'Reset Theme Options', 'us' ),
				'type' => 'reset',
			),
			'of_backup' => array(
				'title' => __( 'Backup Theme Options', 'us' ),
				'type' => 'backup',
			),
			'of_transfer' => array(
				'title' => __( 'Transfer Theme Options', 'us' ),
				'type' => 'transfer',
				'description' => __( 'You can transfer the saved options data between different installations by copying the text inside the text box. To import data from another installation, replace the data in the text box with the one from another installation and click "Import Options".', 'us' ),
			),
		),
	),
);
