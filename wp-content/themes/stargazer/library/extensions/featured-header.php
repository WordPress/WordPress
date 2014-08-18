<?php
/**
 * Featured Header - A script for allowing users to set a featured header image.
 *
 * This script was created to make it easy for theme developers to add featured header image 
 * functionality to their theme.  Featured headers are just a way of using the built-in WordPress 
 * post thumbnails (i.e., featured images) to replace the theme's header image on a per-post 
 * basis.  Therefore, a theme must add support for both the 'post-thumbnails' and 'custom-header' 
 * theme features.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   FeaturedHeader
 * @version   0.1.2
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2013 - 2014, Justin Tadlock
 * @link      http://justintadlock.com
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * The Featured_Header class allows users to create custom, per-post header images if their theme 
 * supports the WordPress 'custom-header' feature.  This class overwrites the header image on 
 * single post views and replaces it with the featured image if the dimensions are correct.
 *
 * @since 0.1.0
 */
class Featured_Header {

	/**
	 * Name of the custom header image size added via add_image_size().
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $size = 'featured-header';

	/**
	 * Width of the custom header image size.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    int
	 */
	public $width = 0;

	/**
	 * Height of the custom header image size.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    int
	 */
	public $height = 0;

	/**
	 * Whether to hard crop the custom header image size.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    bool
	 */
	public $crop = true;

	/**
	 * The URL of the header image.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $url = '';

	/**
	 * Constructor.  Sets up needed actions and filters.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* The theme should actually support the custom background feature. */
		if ( !current_theme_supports( 'post-thumbnails' ) )
			add_theme_support( 'post-thumbnails' );

		/* Add image size based off theme's custom header dimensions. */
		add_action( 'init', array( &$this, 'add_image_size' ) );

		/* Filter the header image. */
		add_filter( 'theme_mod_header_image', array( &$this, 'header_image' ) );

		/* Filter the header image data. */
		add_filter( 'theme_mod_header_image_data', array( &$this, 'header_image_data' ) );
	}

	/**
	 * Adds an image size using the add_image_size() function based off the dimensions of 
	 * the theme's 'custom-header dimensions.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function add_image_size() {

		/* Only add an image size if the theme supports the 'custom-header' feature. */
		if ( current_theme_supports( 'custom-header' ) ) {

			/* Apply filters to the featured header image size. */
			$this->size = apply_filters( 'featured_header_image_size', $this->size );

			/* Get the custom header width defined by the theme. */
			$this->width = apply_filters( 'featured_header_image_width', absint( get_theme_support( 'custom-header', 'width' ) ) );

			/* Get the custom header height defined by the theme. */
			$this->height = apply_filters( 'featured_header_image_height', absint( get_theme_support( 'custom-header', 'height' ) ) );

			/* If both the width and height are greater than '0', add the custom image size. */
			if ( 0 < $this->width && 0 < $this->height ) {
				add_image_size( $this->size, $this->width, $this->height, $this->crop );
			}
		}
	}

	/**
	 * Filters the 'theme_mod_header_image' hook.  Checks if there's a featured image with the 
	 * correct dimensions to replace the header image on single posts.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  string $url The URL of the header image.
	 * @return string
	 */
	public function header_image( $url ) {

		if ( is_singular() && 'remove-header' !== $url && 0 < $this->width && 0 < $this->height ) {

			/* Get the queried post ID. */
			$post_id = get_queried_object_id();

			/* Support featured headers added via post meta, following WP's '_thumbnail_id' format. */
			$featured_header_id = get_post_meta( $post_id, '_featured_header_id', true );

			/* Set the thumbnail Id. */
			$thumbnail_id = $featured_header_id ? absint( $featured_header_id ) : get_post_thumbnail_id( $post_id );

			/* If we have an ID, get the attachment image source. */
			if ( !empty( $thumbnail_id ) ) {

				$image = wp_get_attachment_image_src( $thumbnail_id, $this->size );

				/* If the image width/height match the dimensions we need, use the image. */
				if ( $image[1] == $this->width && $image[2] == $this->height )
					$this->url = $url = $image[0];
			}
		}

		return $url;
	}

	/**
	 * Filters the 'theme_mod_header_image_data' hook.  This is used to set the header image width 
	 * and height attributes if a featured header image was found.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  object|array $data Header image data (width, height, url, thumbnail_url).
	 * @return object
	 */
	public function header_image_data( $data ) {

		/* If a featured header image URL was set, add the width and height values. */
		if ( !empty( $this->url ) ) {

			/* Sometimes $data is an array and sometimes it's an object. That's weird. */
			if( is_array( $data ) ) {
				$data['width']  = $this->width;
				$data['height'] = $this->height;			
			} else {
				$data->width  = $this->width;
				$data->height = $this->height;
			}
		}

		return $data;
	}
}

new Featured_Header();
