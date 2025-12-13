<?php
/*
 * Functions for the child theme.
 */


/**
 * Supply the defaults for font options.
 */
function child_demo_default_fonts( $defaults ) {
	return array_merge( $defaults, array( 'body' => 'Prompt', 'titles' => 'Salsa' ) );
}
add_filter( 'twenty8teen_default_fonts', 'child_demo_default_fonts' );

/**
 * Supply the defaults for color options.
 */
function child_demo_default_colors( $defaults ) {
	return array_merge( $defaults, array(
		'header_textcolor' => '#5d0b0b',
		'background_color' => '#eeeeff',
		'accent_color' => '#c95836',
		'body_textcolor' => '#3f0000',
		'link_color' => '#0b3fa8',
	) );
}
add_filter( 'twenty8teen_default_colors', 'child_demo_default_colors' );

/**
 * Supply the defaults for size options.
 */
function child_demo_default_sizes( $defaults ) {
	return array_merge( $defaults, array(
		'featured_size_archives' => 'none',
		'featured_size_single' => 'medium',
		'excerpt_length' => 40,
		'font_size_adjust' => 0.38,
	) );
}
add_filter( 'twenty8teen_default_sizes', 'child_demo_default_sizes' );

/**
 * Supply the defaults for boolean options.
 */
function child_demo_default_booleans( $defaults ) {
	return array_merge( $defaults, array(
		'show_full_content' => false,
		'show_header' => true,
		'show_vignette' => false,
		'show_icons' => false,
		'show_as_cards' => false,
		'switch_sidebar' => false,
		'show_header_imagebehind' => false,
		'start_in_tableview' => true,
		'show_sidebar' => true,
		'use_posttype_parts' => true,
	) );
}
add_filter( 'twenty8teen_default_booleans', 'child_demo_default_booleans' );

/**
 * Supply the defaults for identimage options.
 */
function child_demo_default_identimages( $defaults ) {
	return array_merge( $defaults, array(
		'show_header_identimage' => 'none',
		'show_entry_header_identimage' => 'none',
		'show_featured_identimage' => 'repeating-conic',
		'identimage_alpha' => 0.25,
		'featured_image_classes' => 'shadow',
	) );
}
add_filter( 'twenty8teen_default_identimages', 'child_demo_default_identimages' );

/**
 * Supply the defaults for area class options.
 */
function child_demo_default_area_classes( $defaults ) {
	return array_merge( $defaults, array(
		'header' => 'slab semi-white semi-black semi-accent noise',
		'main' => '',
		'content' => '',
		'comments' => 'font-smaller',
		'sidebar' => '',
		'widgets' => 'semi-white semi-black noise box',
		'footer' => 'font-smaller',
	) );
}
add_filter( 'twenty8teen_default_area_classes', 'child_demo_default_area_classes' );


/**
 * Indicate to not show author on posts.
 */
function child_demo_posttypes_no_author( $post_types ) {
	$post_types[] = 'post';
	return $post_types;
}
add_filter( 'twenty8teen_posttypes_no_author', 'child_demo_posttypes_no_author' );

/**
 * Supply a preset for the blog (latest posts) page and search page.
 */
function child_demo_option_preset( $values, $which ) {
	if ( 'blog' == $which && 0 == count( $values ) ) { //count=0 if user didn't save it in database
		$values['featured_size_archives'] = 'thumbnail';
		$values['start_in_tableview'] = false;
	}
	if ( 'search' == $which && 0 == count( $values ) ) {
		$values['featured_size_archives'] = 'none';
		$values['show_sidebar'] = false;
	}
	return $values;
}
add_filter( 'twenty8teen_option_preset', 'child_demo_option_preset', 10, 2 );

/**
 * Apply a preset for the blog (latest posts) page and search page.
 */
function child_demo_conditional_presets( $presets ) {
	if ( is_home() ) {
		$presets[] = 'blog';
	}
	if ( is_search() ) {
		$presets[] = 'search';
	}
	return $presets;
}
add_filter( 'twenty8teen_conditional_presets', 'child_demo_conditional_presets' );
