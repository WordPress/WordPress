<?php
/**
 * SVG icons related functions
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

/**
 * Gets the SVG code for a given icon.
 */
function twentynineteen_get_icon_svg( $icon, $size = 24 ) {
	return TwentyNineteen_SVG_Icons::get_svg( 'ui', $icon, $size );
}

/**
 * Gets the SVG code for a given social icon.
 */
function twentynineteen_get_social_icon_svg( $icon, $size = 24 ) {
	return TwentyNineteen_SVG_Icons::get_svg( 'social', $icon, $size );
}

/**
 * Detects the social network from a URL and returns the SVG code for its icon.
 */
function twentynineteen_get_social_link_svg( $uri, $size = 24 ) {
	return TwentyNineteen_SVG_Icons::get_social_link_svg( $uri, $size );
}

/**
 * Display SVG icons in social links menu.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string  $item_output The menu item output with social icon.
 */
function twentynineteen_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'social' === $args->theme_location ) {
		$svg = twentynineteen_get_social_link_svg( $item->url, 26 );
		if ( empty( $svg ) ) {
			$svg = twentynineteen_get_icon_svg( 'link' );
		}
		$item_output = str_replace( $args->link_after, '</span>' . $svg, $item_output );
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'twentynineteen_nav_menu_social_icons', 10, 4 );
