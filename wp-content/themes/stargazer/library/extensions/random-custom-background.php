<?php
/**
 * Random Custom Background - A script for handling random backgrounds.
 *
 * This script was created to make it simple for theme developers to set a random background for 
 * their theme instead of just a single background.  The script is just an extension of the WordPress 
 * 'custom-background' theme feature.  It allows the user to select a permanent background, but if 
 * no user background is set, the random background is shown.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   RandomCustomBackground
 * @version   0.2.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2012 - 2013, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Generates a random custom background and filters the 'theme_mod_background_*' hooks to 
 * overwrite the theme's set background.
 *
 * @since  0.1.0
 * @access public
 */
class Random_Custom_Background {

	/**
	 * The background color property.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $color = '';

	/**
	 * The background image property.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $image = '';

	/**
	 * The background repeat property.  Allowed: 'no-repeat', 'repeat', 'repeat-x', 'repeat-y'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $repeat = 'repeat';

	/**
	 * The vertical value of the background position property.  Allowed: 'top', 'bottom', 'center'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $position_y = 'top';

	/**
	 * The horizontal value of the background position property.  Allowed: 'left', 'right', 'center'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $position_x = 'left';

	/**
	 * The background attachment property.  Allowed: 'scroll', 'fixed'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $attachment = 'scroll';

	/**
	 * The background clip property.  Allowed: 'border-box', 'padding-box', 'content-box'.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $clip = 'border-box';

	/**
	 * The background origin property.  Allowed: 'padding-box', 'border-box', 'content-box'.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $origin = 'padding-box';

	/**
	 * The background size property.  Allowed: $length, $percentage, 'auto', 'contain', 'cover'.
	 *
	 * @since  0.2.0
	 * @access public
	 * @var    string
	 */
	public $size = 'auto';

	/**
	 * Constructor method.  Sets up the random background feature.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* The theme should actually support the custom background feature. */
		if ( !current_theme_supports( 'custom-background' ) )
			add_theme_support( 'custom-background', array( 'wp-head-callback' => '__return_false' ) );

		/* Generate a random background. */
		$this->generate_random_background();

		/* Get the user-selected background image. */
		$image = get_theme_mod( 'background_image', '' );

		/* Filter the background color. */
		add_filter( 'theme_mod_background_color', array( &$this, 'background_color' ) );

		/* Filter the background image. */
		add_filter( 'theme_mod_background_image', array( &$this, 'background_image' ) );

		/**
		 * If no background image is set by the user, set the properties related to the background 
		 * image.  The script must overwrite these values completely rather than relinquish control 
		 * to the user setting to work properly with the random image.
		 */
		if ( empty( $image ) && !empty( $this->image ) ) {
			add_filter( 'theme_mod_background_repeat',     array( &$this, 'background_repeat' ) );
			add_filter( 'theme_mod_background_position_y', array( &$this, 'background_position_y' ) );
			add_filter( 'theme_mod_background_position_x', array( &$this, 'background_position_x' ) );
			add_filter( 'theme_mod_background_attachment', array( &$this, 'background_attachment' ) );
			add_filter( 'theme_mod_background_clip',       array( &$this, 'background_clip' ) );
			add_filter( 'theme_mod_background_origin',     array( &$this, 'background_origin' ) );
			add_filter( 'theme_mod_background_size',       array( &$this, 'background_size' ) );
		}

		/* Get the custom background arguments. */
		$supports = get_theme_support( 'custom-background' );

		/* If '__return_false' is the wp_head callback, roll our own. */
		if ( isset( $supports[0]['wp-head-callback'] ) && '__return_false' == $supports[0]['wp-head-callback'] )
			add_action( 'wp_head', array( &$this, 'custom_background_callback' ) );
	}

	/**
	 * Generates a random background image from the theme's random images set.  Themes should 
	 * add a second parameter to register their backgrounds (an array of background arrays).
	 * add_theme_support( 'random-custom-background', $backgrounds ).
	 *
	 * Supported background arguments: 'image', 'color', 'repeat', 'position_y', 'position_x', 
	 * 'attachment', 'clip', 'origin', 'size'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function generate_random_background() {

		/* Get the theme-supported random background array. */
		$supports = get_theme_support( 'random-custom-background' );

		/* Get the random backgrounds set. */
		$backgrounds = isset( $supports[0] ) ? $supports[0] : array();

		/* Allow developers to overwrite the backgrounds. */
		$backgrounds = apply_filters( 'random_custom_backgrounds', $backgrounds );

		/* If no backgrounds are set, return. */
		if ( empty( $backgrounds ) )
			return;

		/* Count the number of backgrounds. */
		$count = count( $backgrounds );

		/* Generate a random background from the given set of backgrounds. */
		if ( 1 < $count ) {
			$random = mt_rand( 0, $count - 1 );
			$args = $backgrounds[ $random ];
		}

		/* Get the background arguments if there's just one background. */
		else {
			$args = $backgrounds[0];
		}

		/* Set the background properties. */
		$this->image      = !empty( $args['image'] )      ? $args['image']      : $this->image;
		$this->color      = !empty( $args['color'] )      ? $args['color']      : $this->color;
		$this->repeat     = !empty( $args['repeat'] )     ? $args['repeat']     : $this->repeat;
		$this->position_y = !empty( $args['position_y'] ) ? $args['position_y'] : $this->position_y;
		$this->position_x = !empty( $args['position_x'] ) ? $args['position_x'] : $this->position_x;
		$this->attachment = !empty( $args['attachment'] ) ? $args['attachment'] : $this->attachment;
		$this->clip       = !empty( $args['clip'] )       ? $args['clip']       : $this->clip;
		$this->origin     = !empty( $args['origin'] )     ? $args['origin']     : $this->origin;
		$this->size       = !empty( $args['size'] )       ? $args['size']       : $this->size;
	}

	/**
	 * Sets the background color.  This script will respect the user's background color setting.  
	 * If the user has set a color, the random color won't be used.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $color The background color property.
	 * @return string
	 */
	public function background_color( $color ) {

		/* Only return random color if the user hasn't chosen a color. */
		return empty( $color ) ? preg_replace( '/[^0-9a-fA-F]/', '', $this->color ) : $color;
	}

	/**
	 * Sets the background image.  This script will respect the user's background image setting.  
	 * If the user has set an image, the random image won't be used.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $image The background image property.
	 * @return string
	 */
	public function background_image( $image ) {

		/* Only return the random image if the user hasn't chosen an image. */
		return empty( $image ) ? esc_url( $this->image ) : $image;
	}

	/**
	 * Sets the background repeat property.  Only exectued if using a random background.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $repeat The background repeat property.
	 * @return string
	 */
	public function background_repeat( $repeat ) {
		return $this->repeat;
	}

	/**
	 * Sets the background vertical position.  This isn't technically supported in WordPress (as 
	 * of 3.5).  This method is only executed if using a random background image and the 
	 * custom_background_callback() method is executed (themes can also use it in custom 
	 * callbacks).
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $position_y The background vertical position.
	 * @return string
	 */
	public function background_position_y( $position_y ) {
		return $this->position_y;
	}

	/**
	 * Sets the background horizontal position.  Only exectued if using a random background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $position_x The background horizontal position.
	 * @return string
	 */
	public function background_position_x( $position_x ) {
		return $this->position_x;
	}

	/**
	 * Sets the background attachment property.  Only exectued if using a random background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $url The background attachment property.
	 * @return string
	 */
	public function background_attachment( $attachment ) {
		return $this->attachment;
	}

	/**
	 * Sets the background clip property.  This isn't technically supported in WordPress. This 
	 * method is only executed if using a random background image and the 
	 * custom_background_callback() method is executed (themes can also use it in custom 
	 * callbacks).
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $clip The background clip property.
	 * @return string
	 */
	public function background_clip( $clip ) {
		return $this->clip;
	}

	/**
	 * Sets the background origin property.  This isn't technically supported in WordPress. This 
	 * method is only executed if using a random background image and the 
	 * custom_background_callback() method is executed (themes can also use it in custom 
	 * callbacks).
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $origin The background origin property.
	 * @return string
	 */
	public function background_origin( $origin ) {
		return $this->origin;
	}

	/**
	 * Sets the background size property.  This isn't technically supported in WordPress. This 
	 * method is only executed if using a random background image and the 
	 * custom_background_callback() method is executed (themes can also use it in custom 
	 * callbacks).
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string $size The background size property.
	 * @return string
	 */
	public function background_size( $size ) {
		return $this->size;
	}

	/**
	 * Outputs the custom background style in the header.  This function is only executed if the value 
	 * of the 'wp-head-callback' for the 'custom-background' feature is set to '__return_false'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function custom_background_callback() {

		/* Get the background image. */
		$image = set_url_scheme( get_background_image() );

		/* Get the background color. */
		$color = get_background_color();

		/* If there is no image or color, bail. */
		if ( empty( $image ) && empty( $color ) )
			return;

		/* Set the background color. */
		$style = $color ? "background-color: #{$color};" : '';

		/* If there's a background image, add it. */
		if ( $image ) {

			/* Background image. */
			$style .= " background-image: url('{$image}');";

			/* Background repeat. */
			$repeat = get_theme_mod( 'background_repeat', 'repeat' );
			$repeat = in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ? $repeat : 'repeat';

			$style .= " background-repeat: {$repeat};";

			/* Background position. */
			$position_y = get_theme_mod( 'background_position_y', 'top' );
			$position_y = in_array( $position_y, array( 'top', 'center', 'bottom' ) ) ? $position_y : 'top';

			$position_x = get_theme_mod( 'background_position_x', 'left' );
			$position_x = in_array( $position_x, array( 'center', 'right', 'left' ) ) ? $position_x : 'left';

			$style .= " background-position: {$position_y} {$position_x};";

			/* Background attachment. */
			$attachment = get_theme_mod( 'background_attachment', 'scroll' );
			$attachment = in_array( $attachment, array( 'fixed', 'scroll' ) ) ? $attachment : 'scroll';

			$style .= " background-attachment: {$attachment};";

			/* Backgroud clip. */
			$clip = get_theme_mod( 'background_clip', 'border-box' );
			$clip = in_array( $clip, array( 'border-box', 'padding-box', 'content-box' ) ) ? $clip : 'border-box';

			$style .= " background-clip: {$clip};";

			/* Backgroud origin. */
			$origin = get_theme_mod( 'background_origin', 'padding-box' );
			$origin = in_array( $origin, array( 'padding-box', 'border-box', 'content-box' ) ) ? $origin : 'padding-box';

			$style .= " background-origin: {$origin};";

			/* Background size. */
			$size = get_theme_mod( 'background_size', 'auto' );
			$size = in_array( $size, array( 'auto', 'contain', 'cover' ) ) ? $size : preg_replace( "/[^0-9a-zA-Z%\s]/", '', $size );

			$style .= " background-size: {$size};";
		}

		/* Output the custom background style. */
		echo "\n" . '<style type="text/css" id="custom-background-css">body.custom-background{ ' . trim( $style ) . ' }</style>' . "\n";
	}
}

new Random_Custom_Background();

?>