<?php
/**
 * Handles the setup and usage of the WordPress custom backgrounds feature.
 *
 * @package    Stargazer
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2013 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/themes/stargazer
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Call late so child themes can override. */
add_action( 'after_setup_theme', 'stargazer_custom_background_setup', 15 );

/* Filter the background color late. */
add_filter( 'theme_mod_background_color', 'stargazer_background_color', 95 );

/* Register default background images. */
add_filter( 'hybrid_default_backgrounds', 'stargazer_default_backgrounds', 15 );

/**
 * Adds support for the WordPress 'custom-background' theme feature.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_custom_background_setup() {

	add_theme_support(
		'custom-background',
		array(
			'default-color'    => '2d2d2d',
			'default-image'    => '',
			'wp-head-callback' => 'stargazer_custom_background_callback',
		)
	);
}

/**
 * If the color is `ffffff` (white), return an empty string for the background color.  This is because the 
 * theme's main container's background is also white.  In this case, we drop some margins/padding so that 
 * the theme design flows better and doesn't appear that we have large, empty areas.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $color
 * @return string
 */
function stargazer_background_color( $color ) {
	return 'ffffff' === $color ? '' : $color;
}

/**
 * Registers custom backgrounds for the theme.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_default_backgrounds( $backgrounds ) {

	$_backgrounds = array(
		'orange-cross' => array(
			'url'           => '%s/images/backgrounds/orange-cross.png',
			'thumbnail_url' => '%s/images/backgrounds/orange-cross-thumb.png',
		),
		'star-field-dark' => array(
			'url'           => '%s/images/backgrounds/star-field-dark.jpg',
			'thumbnail_url' => '%s/images/backgrounds/star-field-dark-thumb.jpg',
		),
	);

	return array_merge( $backgrounds, $_backgrounds );
}


/**
 * This is a fix for when a user sets a custom background color with no custom background image.  What 
 * happens is the theme's background image hides the user-selected background color.  If a user selects a 
 * background image, we'll just use the WordPress custom background callback.  This also fixes WordPress 
 * not correctly handling the theme's default background color.
 *
 * @link http://core.trac.wordpress.org/ticket/16919
 * @link http://core.trac.wordpress.org/ticket/21510
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_custom_background_callback() {

	/* Get the background image. */
	$image = get_background_image();

	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}

	/* Get the background color. */
	$color = get_background_color();

	/* If no background color, return. */
	if ( empty( $color ) )
		return;

	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";

?>
<style type="text/css" id="custom-background-css">body.custom-background { <?php echo trim( $style ); ?> }</style>
<?php

}
