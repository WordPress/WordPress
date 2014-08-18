<?php
/**
 * Cleaner Caption - Cleans up the WP [caption] shortcode.
 *
 * WordPress adds an inline style to its [caption] shortcode which specifically adds 10px of extra width to 
 * captions, making theme authors jump through hoops to design captioned elements to their liking.  This extra
 * width makes the assumption that all captions should have 10px of extra padding to account for a box that 
 * wraps the element.  This script changes the width to match that of the 'width' attribute passed in through
 * the shortcode, allowing themes to better handle how their captions are designed.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   CleanerCaption
 * @version   0.3.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2011 - 2014, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Filter the caption shortcode output. */
add_filter( 'img_caption_shortcode', 'cleaner_caption', 10, 3 );

/**
 * Cleans up the default WordPress [caption] shortcode.  The main purpose of this function is to remove the 
 * inline styling WP adds, which creates 10px of padding around captioned elements.
 *
 * @since 0.1.0
 * @access private
 * @param string $output The output of the default caption (empty string at this point).
 * @param array $attr Array of arguments for the [caption] shortcode.
 * @param string $content The content placed after the opening [caption] tag and before the closing [/caption] tag.
 * @return string $output The formatted HTML for the caption.
 */
function cleaner_caption( $output, $attr, $content ) {

	/* We're not worried abut captions in feeds, so just return the output here. */
	if ( is_feed() )
		return $output;

	/* Set up the default arguments. */
	$defaults = array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => ''
	);

	/* Allow developers to override the default arguments. */
	$defaults = apply_filters( 'cleaner_caption_defaults', $defaults );

	/* Apply filters to the arguments. */
	$attr = apply_filters( 'cleaner_caption_args', $attr );

	/* Merge the defaults with user input. */
	$attr = shortcode_atts( $defaults, $attr, 'caption' );

	/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags. */
	if ( 1 > $attr['width'] || empty( $attr['caption'] ) )
		return $content;

	/* Set up the attributes for the caption <div>. */
	$attributes  = !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '';
	$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';

	/* Caption width filter hook from WP core. */
	$caption_width = apply_filters( 'img_caption_shortcode_width', $attr['width'], $attr, $content );

	/* If there's a width, add the inline style for it. */
	if ( 0 < $caption_width )
		$attributes .= ' style="max-width: ' . esc_attr( $caption_width ) . 'px"';

	/* Open the caption <div>. */
	$output = '<figure' . $attributes .'>';

	/* Allow shortcodes for the content the caption was created for. */
	$output .= do_shortcode( $content );

	/* Append the caption text. */
	$output .= '<figcaption class="wp-caption-text">' . $attr['caption'] . '</figcaption>';

	/* Close the caption </div>. */
	$output .= '</figure>';

	/* Return the formatted, clean caption. */
	return apply_filters( 'cleaner_caption', $output );
}
