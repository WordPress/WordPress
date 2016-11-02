<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's options
 *
 * @filter us_config_meta-boxes
 */
$custom_post_types = us_get_option( 'custom_post_types_support' );

$titlebar_common_fields = array(
	'us_titlebar_subtitle' => array(
		'title' => __( 'Description (shown next to Page Title)', 'us' ),
		'type' => 'text',
		'std' => '',
		'show_if' => array( 'us_titlebar_content', '!=', 'hide' ),
	),
	'us_titlebar_size' => array(
		'title' => __( 'Title Bar Size', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => __( 'Default (from Theme Options)', 'us' ),
			'small' => __( 'Small', 'us' ),
			'medium' => __( 'Medium', 'us' ),
			'large' => __( 'Large', 'us' ),
			'huge' => __( 'Huge', 'us' ),
		),
		'std' => '',
		'show_if' => array( 'us_titlebar_content', '!=', 'hide' ),
	),
	'us_titlebar_color' => array(
		'title' => __( 'Title Bar Color Style', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => __( 'Default (from Theme Options)', 'us' ),
			'default' => __( 'Content colors', 'us' ),
			'alternate' => __( 'Alternate Content colors', 'us' ),
			'primary' => __( 'Primary bg & White text', 'us' ),
			'secondary' => __( 'Secondary bg & White text', 'us' ),
		),
		'std' => '',
		'show_if' => array( 'us_titlebar_content', '!=', 'hide' ),
	),
	'us_titlebar_image' => array(
		'title' => __( 'Background Image', 'us' ),
		'type' => 'upload',
		'extension' => 'png,jpg,jpeg,gif,svg',
		'show_if' => array( 'us_titlebar_content', '!=', 'hide' ),
	),
	'us_titlebar_image_size' => array(
		'title' => __( 'Background Image Size', 'us' ),
		'type' => 'select',
		'options' => array(
			'cover' => __( 'Cover - Image will cover the whole area', 'us' ),
			'contain' => __( 'Contain - Image will fit inside the area', 'us' ),
			'initial' => __( 'Initial', 'us' ),
		),
		'std' => 'cover',
		'show_if' => array(
			array( 'us_titlebar_content', '!=', 'hide' ),
			'and',
			array( 'us_titlebar_image', '!=', '' ),
		),
	),
	'us_titlebar_image_parallax' => array(
		'title' => __( 'Parallax Effect', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => __( 'None', 'us' ),
			'vertical' => __( 'Vertical Parallax', 'us' ),
			'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
			'horizontal' => __( 'Horizontal Parallax', 'us' ),
			'still' => __( 'Still (Image doesn\'t move)', 'us' ),
		),
		'std' => '',
		'show_if' => array(
			array( 'us_titlebar_content', '!=', 'hide' ),
			'and',
			array( 'us_titlebar_image', '!=', '' ),
		),
	),
	'us_titlebar_overlay_color' => array(
		'title' => __( 'Overlay Color', 'us' ),
		'type' => 'color',
		'show_if' => array(
			array( 'us_titlebar_content', '!=', 'hide' ),
			'and',
			array( 'us_titlebar_image', '!=', '' ),
		),
	),
);

global $wp_registered_sidebars;
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

$sidebars_options = array_merge( array( '' => __( 'Default (from Theme Options)', 'us' ) ), $sidebars_options );

return array(
	// Blog Post settings
	array(
		'id' => 'us_post_settings',
		'title' => __( 'Featured Image Layout', 'us' ),
		'post_types' => array( 'post' ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			'us_post_preview_layout' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'basic' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
					'none' => __( 'No Preview', 'us' ),
				),
				'std' => '',
			),
		),
	),
	// Sidebar settings
	array(
		'id' => 'us_sidebar_settings',
		'title' => __( 'Sidebar', 'us' ),
		'post_types' => array_merge( array( 'post', 'page', 'us_portfolio', 'product' ), $custom_post_types ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			'us_sidebar' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'none' => __( 'No Sidebar', 'us' ),
					'right' => __( 'Right', 'us' ),
					'left' => __( 'Left', 'us' ),
				),
				'std' => '',
			),
			'us_sidebar_id' => array(
				'title' => __( 'Sidebar Content', 'us' ),
				'description' => sprintf( __( 'This dropdown list shows the Widget Areas, which you can populate on the %sWidgets%s page.', 'us' ), '<a target="_blank" href="' . admin_url() . 'widgets.php">', '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => '',
				'show_if' => array( 'us_sidebar', '!=', 'none' ),
			),
		),
	),
	// Header settings
	array(
		'id' => 'us_header_settings',
		'title' => __( 'Header Options', 'us' ),
		'post_types' => array_merge( array( 'post', 'page', 'us_portfolio', 'product' ), $custom_post_types ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			'us_header_remove' => array(
				'type' => 'switch',
				'text' => __( 'Remove header on this page', 'us' ),
				'std' => 0,
			),
			'us_header_pos' => array(
				'title' => __( 'Sticky Header', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'fixed' => __( 'Sticky on this page', 'us' ),
					'static' => __( 'Not sticky on this page', 'us' ),
				),
				'std' => '',
				'show_if' => array( 'us_header_remove', '=', FALSE ),
			),
			'us_header_bg' => array(
				'title' => __( 'Transparent Header', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'transparent' => __( 'Transparent on this page', 'us' ),
					'solid' => __( 'Not transparent on this page', 'us' ),
				),
				'std' => '',
				'show_if' => array( 'us_header_remove', '=', FALSE ),
			),
			'us_header_sticky_pos' => array(
				'title' => __( 'Sticky Header Initial Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'At the Top of this page', 'us' ),
					'bottom' => __( 'At the Bottom of the first content row', 'us' ),
					'above' => __( 'Above the first content row', 'us' ),
					'below' => __( 'Below the first content row', 'us' ),
				),
				'std' => '',
				'show_if' => array(
					array( 'us_header_remove', '=', FALSE ),
					'and',
					array( 'us_header_pos', '!=', 'static' ),
				),
			),
		),
	),
	// Titlebar settings
	array(
		'id' => 'us_titlebar_settings',
		'title' => __( 'Title Bar Options', 'us' ),
		'post_types' => array_merge( array( 'page', 'product', 'post' ), $custom_post_types ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array_merge( array(
			'us_titlebar_content' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'all' => __( 'Title, Description, Breadcrumbs', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => '',
			),
		), $titlebar_common_fields ),
	),
	// Titlebar settings for Portfolio Items
	array(
		'id' => 'us_titlebar_settings_portfolio',
		'title' => __( 'Title Bar Options', 'us' ),
		'post_types' => array( 'us_portfolio' ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array_merge( array(
			'us_titlebar_content' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'all' => __( 'Title, Description, Arrows', 'us' ),
					'caption' => __( 'Title, Description', 'us' ),
					'hide' => __( 'Hide Title Bar', 'us' ),
				),
				'std' => '',
			),
		), $titlebar_common_fields ),
	),
	// Footer settings
	array(
		'id' => 'us_footer_settings',
		'title' => __( 'Footer Options', 'us' ),
		'post_types' => array_merge( array( 'post', 'page', 'us_portfolio', 'product' ), $custom_post_types ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			'us_footer_show_top' => array(
				'title' => __( 'Show widgets area', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'show' => __( 'Show', 'us' ),
					'hide' => __( 'Hide', 'us' ),
				),
				'std' => '',
			),
			'us_footer_show_bottom' => array(
				'title' => __( 'Show copyright and menu area', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'show' => __( 'Show', 'us' ),
					'hide' => __( 'Hide', 'us' ),
				),
				'std' => '',
			),
		),
	),
	// Portfolio Item settings
	array(
		'id' => 'us_portfolio_settings',
		'title' => __( 'Portfolio Tile Options', 'us' ),
		'post_types' => array( 'us_portfolio' ),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			'us_tile_action' => array(
				'title' => __( 'A click on the tile of Portfolio Grid will open:', 'us' ),
				'type' => 'radio',
				'options' => array(
					'page' => __( 'Portfolio Item Page', 'us' ),
					'lightbox' => __( 'Portfolio Item Image in a lightbox', 'us' ),
					'link' => __( 'Custom Link', 'us' ),
				),
				'std' => 'page',
			),
			'us_tile_link' => array(
				'type' => 'link',
				'placeholder' => __( 'Paste URL', 'us' ),
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'us_tile_action', '=', 'link' ),
			),
			'us_tile_description' => array(
				'title' => __( 'Item Tile Description', 'us' ),
				'description' => __( 'This text will be shown in the relevant tile of Portfolio Grid', 'us' ),
				'type' => 'text',
				'std' => '',
			),
			'us_tile_size' => array(
				'title' => __( 'Item Tile Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'1x1' => '1x1',
					'2x1' => '2x1',
					'1x2' => '1x2',
					'2x2' => '2x2',
				),
				'std' => '1x1',
			),
			'us_tile_bg_color' => array(
				'title' => __( 'Item Tile Background Color', 'us' ),
				'type' => 'color',
			),
			'us_tile_text_color' => array(
				'title' => __( 'Item Tile Text Color', 'us' ),
				'type' => 'color',
			),
			'us_tile_additional_image' => array(
				'title' => __( 'Additional Tile Image on hover (optional)', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
			),
		),
	),
);
