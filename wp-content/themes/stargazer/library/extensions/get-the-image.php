<?php
/**
 * Get the Image - An advanced post image script for WordPress.
 *
 * Get the Image was created to be a highly-intuitive image script that displays post-specific images (an 
 * image-based representation of a post).  The script handles old-style post images via custom fields for 
 * backwards compatibility.  It also supports WordPress' built-in featured image functionality.  On top of 
 * those things, it can automatically set attachment images as the post image or scan the post content for 
 * the first image element used.  It can also fall back to a given default image.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   GetTheImage
 * @version   1.0.1
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://justintadlock.com/archives/2008/05/27/get-the-image-wordpress-plugin
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Adds theme support for WordPress 'featured images'. */
add_theme_support( 'post-thumbnails' );

/* Delete the cache when a post or post metadata is updated. */
add_action( 'save_post',         'get_the_image_delete_cache_by_post'        );
add_action( 'deleted_post_meta', 'get_the_image_delete_cache_by_meta', 10, 2 );
add_action( 'updated_post_meta', 'get_the_image_delete_cache_by_meta', 10, 2 );
add_action( 'added_post_meta',   'get_the_image_delete_cache_by_meta', 10, 2 );

/**
 * The main image function for displaying an image.  This is a wrapper for the Get_The_Image class. Use this 
 * function in themes rather than the class.
 *
 * @since  0.1.0
 * @access public
 * @param  array        $args  Arguments for how to load and display the image.
 * @return string|array        The HTML for the image. | Image attributes in an array.
 */
function get_the_image( $args = array() ) {

	$image = new Get_The_Image( $args );

	return $image->get_image();
}


/* === Internal Plugin Code: Don't use the below unless you know what you're doing. Expect breakage. === */


/**
 * Class for getting images related to a post.  Only use this class in your projects if you absolutely know 
 * what you're doing and expect your code to break in future versions.  Use the the `get_the_image()` 
 * wrapper function instead.  That's the reason it exists.
 *
 * @since  1.0.0
 * @access private
 */
final class Get_The_Image {

	/**
	 * Array of arguments passed in by the user and merged with the defaults.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $args  = array();

	/**
	 * Image arguments array filled by the class.  This is used to store data about the image (src, 
	 * width, height, etc.).  In some scenarios, it may not be set, particularly when getting the 
	 * raw image HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $image_args  = array();

	/**
	 * The image HTML to output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $image = '';

	/**
	 * Original image HTML.  This is set when splitting an image from the content.  By default, this 
	 * is only used when 'scan_raw' is set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $original_image = '';

	/**
	 * Constructor method.  This sets up and runs the show.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $args
	 * @return void
	 */
	public function __construct( $args = array() ) {
		global $wp_embed;

		/* Use WP's embed functionality to handle the [embed] shortcode and autoembeds. */
		add_filter( 'get_the_image_post_content', array( $wp_embed, 'run_shortcode' ) );
		add_filter( 'get_the_image_post_content', array( $wp_embed, 'autoembed'     ) );

		/* Set the default arguments. */
		$defaults = array(

			/* Post the image is associated with. */
			'post_id'            => get_the_ID(),

			/* Method order (see methods below). */
			'order'              => array( 'meta_key', 'featured', 'attachment', 'scan', 'scan_raw', 'callback', 'default' ),

			/* Methods of getting an image (in order). */
			'meta_key'           => array( 'Thumbnail', 'thumbnail' ), // array|string
			'featured'           => true,
			'attachment'         => true,
			'scan'               => false,
			'scan_raw'           => false, // Note: don't use the array format option with this.
			'callback'           => null,
			'default'            => false,

			/* Split image from post content (by default, only used with the 'scan_raw' option). */
			'split_content'      => false,

			/* Attachment-specific arguments. */
			'size'               => has_image_size( 'post-thumbnail' ) ? 'post-thumbnail' : 'thumbnail',

			/* Format/display of image. */
			'link_to_post'       => true,
			'image_class'        => false,
			'width'              => false,
			'height'             => false,
			'before'             => '',
			'after'              => '',

			/* Minimum allowed sizes. */
			'min_width'          => 0,
			'min_height'         => 0,

			/* Captions. */
			'caption'            => false, // Default WP [caption] requires a width.

			/* Saving the image. */
			'meta_key_save'      => false, // Save as metadata (string).
			'thumbnail_id_save'  => false, // Set 'featured image'.
			'cache'              => true,  // Cache the image.

			/* Return/echo image. */
			'format'             => 'img',
			'echo'               => true,

			/* Deprecated arguments. */
			'custom_key'         => null, // @deprecated 0.6.0 Use 'meta_key'.
			'default_size'       => null, // @deprecated 0.5.0 Use 'size'.
			'the_post_thumbnail' => null, // @deprecated 1.0.0 Use 'featured'.
			'image_scan'         => null, // @deprecated 1.0.0 Use 'scan' or 'scan_raw'.
			'default_image'      => null, // @deprecated 1.0.0 Use 'default'.
			'order_of_image'     => null, // @deprecated 1.0.0 No replacement.
		);

		/* Allow plugins/themes to filter the arguments. */
		$this->args = apply_filters(
			'get_the_image_args',
			wp_parse_args( $args, $defaults )
		);

		/* If no post ID, return. */
		if ( empty( $this->args['post_id'] ) )
			return false;

		/* === Handle deprecated arguments. === */

		/* If $default_size is given, overwrite $size. */
		if ( !is_null( $this->args['default_size'] ) )
			$this->args['size'] = $this->args['default_size'];

		/* If $custom_key is set, overwrite $meta_key. */
		if ( !is_null( $this->args['custom_key'] ) )
			$this->args['meta_key'] = $this->args['custom_key'];

		/* If 'the_post_thumbnail' is set, overwrite 'featured'. */
		if ( !is_null( $this->args['the_post_thumbnail'] ) )
			$this->args['featured'] = $this->args['the_post_thumbnail'];

		/* If 'image_scan' is set, overwrite 'scan'. */
		if ( !is_null( $this->args['image_scan'] ) )
			$this->args['scan'] = $this->args['image_scan'];

		/* If 'default_image' is set, overwrite 'default'. */
		if ( !is_null( $this->args['default_image'] ) )
			$this->args['default'] = $this->args['default_image'];

		/* === End deprecated arguments. === */

		/* If $format is set to 'array', don't link to the post. */
		if ( 'array' == $this->args['format'] )
			$this->args['link_to_post'] = false;

		/* Find images. */
		$this->find();

		/* Only used if $original_image is set. */
		if ( true === $this->args['split_content'] && !empty( $this->original_image ) )
			add_filter( 'the_content', array( $this, 'split_content' ), 9 );
	}

	/**
	 * Returns the image HTML or image array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_image() {

		/* Allow plugins/theme to override the final output. */
		$image_html = apply_filters( 'get_the_image', $this->image );

		/* If $format is set to 'array', return an array of image attributes. */
		if ( 'array' === $this->args['format'] ) {

			/* Set up a default empty array. */
			$out = array();

			/* Get the image attributes. */
			$atts = wp_kses_hair( $image_html, array( 'http', 'https' ) );

			/* Loop through the image attributes and add them in key/value pairs for the return array. */
			foreach ( $atts as $att )
				$out[ $att['name'] ] = $att['value'];

			/* Return the array of attributes. */
			return $out;
		}

		/* Or, if $echo is set to false, return the formatted image. */
		elseif ( false === $this->args['echo'] ) {
			return !empty( $image_html ) ? $this->args['before'] . $image_html . $this->args['after'] : $image_html;
		}

		/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
		if ( isset( $this->image_args['post_thumbnail_id'] ) )
			do_action( 'begin_fetch_post_thumbnail_html', $this->args['post_id'], $this->image_args['post_thumbnail_id'], $this->args['size'] );

		/* Display the image if we get to this point. */
		echo !empty( $image_html ) ? $this->args['before'] . $image_html . $this->args['after'] : $image_html;

		/* If there is a $post_thumbnail_id, do the actions associated with get_the_post_thumbnail(). */
		if ( isset( $this->image_args['post_thumbnail_id'] ) )
			do_action( 'end_fetch_post_thumbnail_html', $this->args['post_id'], $this->image_args['post_thumbnail_id'], $this->args['size'] );
	}

	/**
	 * Figures out if we have an image related to the post. Runs through the various methods of getting
	 * an image.  If there's a cached image, we'll just use that.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function find() {

		/* Get cache key based on $this->args. */
		$key = md5( serialize( compact( array_keys( $this->args ) ) ) );

		/* Check for a cached image. */
		$image_cache = wp_cache_get( $this->args['post_id'], 'get_the_image' );

		if ( !is_array( $image_cache ) )
			$image_cache = array();

		/* If there is no cached image, let's see if one exists. */
		if ( !isset( $image_cache[ $key ] ) || empty( $cache ) ) {

			foreach ( $this->args['order'] as $method ) {

				if ( !empty( $this->image ) || !empty( $this->image_args ) )
					break;

				if ( 'meta_key' === $method && !empty( $this->args['meta_key'] ) )
					$this->get_meta_key_image();

				elseif ( 'featured' === $method && true === $this->args['featured'] )
					$this->get_featured_image();

				elseif ( 'attachment' === $method && true === $this->args['attachment'] )
					$this->get_attachment_image();

				elseif ( 'scan' === $method && true === $this->args['scan'] )
					$this->get_scan_image();

				elseif ( 'scan_raw' === $method && true === $this->args['scan_raw'])
					$this->get_scan_raw_image();

				elseif ( 'callback' === $method && !is_null( $this->args['callback'] ) )
					$this->get_callback_image();

				elseif ( 'default' === $method && !empty( $this->args['default'] ) )
					$this->get_default_image();
			}

			/* Format the image HTML. */
			if ( empty( $this->image ) && !empty( $this->image_args ) )
				$this->format_image();

			/* If we have image HTML. */
			if ( !empty( $this->image ) ) {

				/* Save the image as metadata. */
				if ( !empty( $this->args['meta_key_save'] ) )
					$this->meta_key_save();

				/* Set the image cache for the specific post. */
				$image_cache[ $key ] = $this->image;
				wp_cache_set( $this->args['post_id'], $image_cache, 'get_the_image' );
			}
		}

		/* If an image was already cached for the post and arguments, use it. */
		else {
			$this->image = $image_cache[ $key ];
		}
	}

	/**
	 * Gets a image by post meta key.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_meta_key_image() {

		/* If $meta_key is not an array. */
		if ( !is_array( $this->args['meta_key'] ) )
			$this->args['meta_key'] = array( $this->args['meta_key'] );

		/* Loop through each of the given meta keys. */
		foreach ( $this->args['meta_key'] as $meta_key ) {

			/* Get the image URL by the current meta key in the loop. */
			$image = get_post_meta( $this->args['post_id'], $meta_key, true );

			/* If an image was found, break out of the loop. */
			if ( !empty( $image ) )
				break;
		}

		/* If there's an image and it is numeric, assume it is an attachment ID. */
		if ( !empty( $image ) && is_numeric( $image ) )
			$this->_get_image_attachment( absint( $image ) );

		/* Else, assume the image is a file URL. */
		elseif ( !empty( $image ) )
			$this->image_args = array( 'src' => $image );
	}

	/**
	 * Gets the featured image (i.e., WP's post thumbnail).
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_featured_image() {

		/* Check for a post image ID (set by WP as a custom field). */
		$post_thumbnail_id = get_post_thumbnail_id( $this->args['post_id'] );

		/* If no post image ID is found, return. */
		if ( empty( $post_thumbnail_id ) )
			return;

		/* Apply filters on post_thumbnail_size because this is a default WP filter used with its image feature. */
		$this->args['size'] = apply_filters( 'post_thumbnail_size', $this->args['size'] );

		/* Set the image args. */
		$this->_get_image_attachment( $post_thumbnail_id );

		/* Add the post thumbnail ID. */
		$this->image_args['post_thumbnail_id'] = $post_thumbnail_id;
	}

	/**
	 * Gets the first image attached to the post.  If the post itself is an attachment image, that will 
	 * be the image used.  This method also works with sub-attachments (images for audio/video attachments 
	 * are a good example).
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_attachment_image() {

		/* Check if the post itself is an image attachment. */
		if ( wp_attachment_is_image( $this->args['post_id'] ) ) {
			$attachment_id = $this->args['post_id'];
		}

		/* If the post is not an image attachment, check if it has any image attachments. */
		else {

			/* Get attachments for the inputted $post_id. */
			$attachments = get_children(
				array(
					'numberposts'      => 1,
					'post_parent'      => $this->args['post_id'],
					'post_status'      => 'inherit',
					'post_type'        => 'attachment',
					'post_mime_type'   => 'image',
					'order'            => 'ASC',
					'orderby'          => 'menu_order ID',
					'fields'           => 'ids'
				)
			);

			/* Check if any attachments were found. */
			if ( !empty( $attachments ) )
				$attachment_id = array_shift( $attachments );
		}

		if ( !empty( $attachment_id ) )
			$this->_get_image_attachment( $attachment_id );
	}

	/**
	 * Scans the post content for an image.  It first scans and checks for an image with the 
	 * "wp-image-xxx" ID.  If that exists, it'll grab the actual image attachment.  If not, it looks 
	 * for the image source.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_scan_image() {

		/* Get the post content. */
		$post_content = get_post_field( 'post_content', $this->args['post_id'] );

		/* Apply filters to content. */
		$post_content = apply_filters( 'get_the_image_post_content', $post_content );

		/* Check the content for `id="wp-image-%d"`. */
		preg_match( '/id=[\'"]wp-image-([\d]*)[\'"]/i', $post_content, $image_ids );

		/* Loop through any found image IDs. */
		if ( is_array( $image_ids ) ) {

			foreach ( $image_ids as $image_id ) {
				$this->_get_image_attachment( $image_id );

				if ( !empty( $this->image_args ) )
					return;
			}
		}

		/* Search the post's content for the <img /> tag and get its URL. */
		preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post_content, $matches );

		/* If there is a match for the image, set the image args. */
		if ( isset( $matches ) && !empty( $matches[1][0] ) )
			$this->image_args = array( 'src' => $matches[1][0] );
	}

	/**
	 * Scans the post content for a complete image.  This method will attempt to grab the complete 
	 * HTML for an image.  If an image is found, pretty much all arguments passed in may be ignored 
	 * in favor of getting the actual image used in the post content.  It works with both captions 
	 * and linked images.  However, it can't account for all possible HTML wrappers for images used 
	 * in all setups.
	 *
	 * This method was created for use with the WordPress "image" post format where theme authors 
	 * might want to pull the whole image from the content as the user added it.  It's also meant 
	 * to be used (not required) with the `split_content` option.
	 *
	 * Note: This option should not be used if returning the image as an array.  If that's desired, 
	 * use the `scan` option instead.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_scan_raw_image() {

		/* Get the post content. */
		$post_content = get_post_field( 'post_content', $this->args['post_id'] );

		/* Apply filters to content. */
		$post_content = apply_filters( 'get_the_image_post_content', $post_content );

		/* Finds matches for shortcodes in the content. */
		preg_match_all( '/' . get_shortcode_regex() . '/s', $post_content, $matches, PREG_SET_ORDER );

		if ( !empty( $matches ) ) {

			foreach ( $matches as $shortcode ) {

				if ( in_array( $shortcode[2], array( 'caption', 'wp_caption' ) ) ) {

					preg_match( '#id=[\'"]attachment_([\d]*)[\'"]|class=[\'"].*?wp-image-([\d]*).*?[\'"]#i', $shortcode[0], $matches );

					if ( !empty( $matches ) && isset( $matches[1] ) || isset( $matches[2] ) ) {

						$attachment_id = !empty( $matches[1] ) ? absint( $matches[1] ) : absint( $matches[2] );

						$image_src = wp_get_attachment_image_src( $attachment_id, $this->args['size'] );

						if ( !empty( $image_src ) ) {

							/* old-style captions. */
							if ( preg_match( '#.*?[\s]caption=[\'"](.+?)[\'"]#i', $shortcode[0], $caption_matches ) )
								$image_caption = trim( $caption_matches[1] );

							$caption_args = array(
								'width'   => $image_src[1],
								'align'   => 'center'
							);

							if ( !empty( $image_caption ) )
								$caption_args['caption'] = $image_caption;

							/* Set up the patterns for the 'src', 'width', and 'height' attributes. */
							$patterns = array(
								'/(src=[\'"]).+?([\'"])/i',
								'/(width=[\'"]).+?([\'"])/i',
								'/(height=[\'"]).+?([\'"])/i',
							);

							/* Set up the replacements for the 'src', 'width', and 'height' attributes. */
							$replacements = array(
								'${1}' . $image_src[0] . '${2}',
								'${1}' . $image_src[1] . '${2}',
								'${1}' . $image_src[2] . '${2}',
							);

							/* Filter the image attributes. */
							$shortcode_content = preg_replace( $patterns, $replacements, $shortcode[5] );

							$this->image          = img_caption_shortcode( $caption_args, $shortcode_content );
							$this->original_image = $shortcode[0];
							return;
						}
						else {
							$this->image          = do_shortcode( $shortcode[0] );
							$this->original_image = $shortcode[0];
							return;
						}
					}
				}
			}
		}

		/* Pull a raw HTML image + link if it exists. */
		if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)#is', $post_content, $matches ) )
			$this->image = $this->original_image = $matches[0];
	}

	/**
	 * Allows developers to create a custom callback function.  If the `callback` argument is set, theme 
	 * developers are expected to **always** return an array.  Even if nothing is found, return an empty 
	 * array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_callback_image() {
		$this->image_args = call_user_func( $this->args['callback'], $this->args );
	}

	/**
	 * Sets the default image.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_default_image() {
		$this->image_args = array( 'src' => $this->args['default'] );
	}

	/**
	 * Handles an image attachment.  Other methods rely on this method for getting the image data since 
	 * most images are actually attachments.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  int    $attachment_id
	 * @return void
	 */
	public function _get_image_attachment( $attachment_id ) {

		/* Get the attachment image. */
		$image = wp_get_attachment_image_src( $attachment_id, $this->args['size'] );

		/* Get the attachment alt text. */
		$alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

		/* Get the attachment caption. */
		$caption = get_post_field( 'post_excerpt', $attachment_id );

		/* Save the attachment as the 'featured image'. */
		if ( true === $this->args['thumbnail_id_save'] )
			$this->thumbnail_id_save( $attachment_id );

		/* Set the image args. */
		$this->image_args = array(
			'src'     => $image[0], 
			'width'   => $image[1],
			'height'  => $image[2],
			'alt'     => $alt, 
			'caption' => $caption 
		);
	}

	/**
	 * Formats the image HTML.  This method is only called if the `$image` property isn't set.  It uses 
	 * the `$image_args` property to set up the image.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function format_image() {

		/* If there is no image URL, return false. */
		if ( empty( $this->image_args['src'] ) )
			return;

		/* Check against min. width. If the image width is too small return. */
		if ( 0 < $this->args['min_width'] && isset( $this->image_args['width'] ) && $this->image_args['width'] < $this->args['min_width'] )
			return;

		/* Check against min. height. If the image height is too small return. */
		if ( 0 < $this->args['min_height'] && isset( $this->image_args['height'] ) && $this->image_args['height'] < $this->args['min_height'] )
			return;

		/* Empty classes array. */
		$classes = array();

		/* If there is alt text, set it.  Otherwise, default to the post title. */
		$image_alt = !empty( $this->image_args['alt'] ) ? $this->image_args['alt'] : get_post_field( 'post_title', $this->args['post_id'] );

		/* If there's a width/height for the image. */
		if ( isset( $this->image_args['width'] ) && isset( $this->image_args['height'] ) ) {

			/* Set a class based on the orientation. */
			$classes[] = ( $this->image_args['height'] > $this->image_args['width'] ) ? 'portrait' : 'landscape';

			/* If an explicit width/height is not set, use the info from the image. */
			if ( empty( $this->args['width'] ) && empty( $this->args['height'] ) ) {
				$this->args['width']  = $this->image_args['width'];
				$this->args['height'] = $this->image_args['height'];
			}
		}

		/* If there is a width or height, set them as HMTL-ready attributes. */
		$width  = $this->args['width']  ? ' width="' .  esc_attr( $this->args['width']  ) . '"' : '';
		$height = $this->args['height'] ? ' height="' . esc_attr( $this->args['height'] ) . '"' : '';

		/* Add the meta key(s) to the classes array. */
		if ( !empty( $this->args['meta_key'] ) )
			$classes = array_merge( $classes, (array)$this->args['meta_key'] );

		/* Add the $size to the class. */
		$classes[] = $this->args['size'];

		/* Get the custom image class. */
		if ( !empty( $this->args['image_class'] ) ) {

			if ( !is_array( $this->args['image_class'] ) )
				$this->args['image_class'] = preg_split( '#\s+#', $this->args['image_class'] );

			$classes = array_merge( $classes, $this->args['image_class'] );
		}

		/* Sanitize all the classes. */
		$classes = $this->sanitize_class( $classes );

		/* Join all the classes into a single string and make sure there are no duplicates. */
		$class = join( ' ', $classes );

		/* Add the image attributes to the <img /> element. */
		$html = sprintf( '<img src="%s" alt="%s" class="%s"%s itemprop="image" />', esc_attr( $this->image_args['src'] ), esc_attr( strip_tags( $image_alt ) ), $class, $width . $height );

		/* If $link_to_post is set to true, link the image to its post. */
		if ( $this->args['link_to_post'] )
			$html = '<a href="' . get_permalink( $this->args['post_id'] ) . '" title="' . esc_attr( get_post_field( 'post_title', $this->args['post_id'] ) ) . '">' . $html . '</a>';

		/* If there is a $post_thumbnail_id, apply the WP filters normally associated with get_the_post_thumbnail(). */
		if ( !empty( $this->image_args['post_thumbnail_id'] ) )
			$html = apply_filters( 'post_thumbnail_html', $html, $this->args['post_id'], $this->image_args['post_thumbnail_id'], $this->args['size'], '' );

		/* If we're showing a caption. */
		if ( true === $this->args['caption'] && !empty( $this->image_args['caption'] ) )
			$html = img_caption_shortcode( array( 'caption' => $this->image_args['caption'], 'width' => $this->args['width'] ), $html );

		$this->image = $html;
	}

	/**
	 * Saves the image source as metadata.  Saving the image as meta is actually quite a bit quicker 
	 * if the user doesn't have a persistent caching plugin available.  However, it doesn't play as 
	 * nicely with custom image sizes used across multiple themes where one might want to resize images. 
	 * This option should be reserved for advanced users only.  Don't use in publicly-distributed 
	 * themes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function meta_key_save() {

		/* If the $meta_key_save argument is empty or there is no image $url given, return. */
		if ( empty( $this->args['meta_key_save'] ) || empty( $this->image_args['src'] ) )
			return;

		/* Get the current value of the meta key. */
		$meta = get_post_meta( $this->args['post_id'], $this->args['meta_key_save'], true );

		/* If there is no value for the meta key, set a new value with the image $url. */
		if ( empty( $meta ) )
			add_post_meta( $this->args['post_id'], $this->args['meta_key_save'], $this->image_args['src'] );

		/* If the current value doesn't match the image $url, update it. */
		elseif ( $meta !== $this->image_args['src'] )
			update_post_meta( $this->args['post_id'], $this->args['meta_key_save'], $this->image_args['src'], $meta );
	}

	/**
	 * Saves the image attachment as the WordPress featured image.  This is useful for setting the 
	 * featured image for the post in the case that the user forgot to (win for client work!).  It 
	 * should not be used in publicly-distributed themes where you don't know how the user will be 
	 * setting up their site.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function thumbnail_id_save( $attachment_id ) {

		/* Save the attachment as the 'featured image'. */
		if ( true === $this->args['thumbnail_id_save'] )
			set_post_thumbnail( $this->args['post_id'], $attachment_id );
	}

	/**
	 * Sanitizes the image class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array   $classes
	 * @return array
	 */
	public function sanitize_class( $classes ) {

		$classes = array_map( 'strtolower',          $classes );
		$classes = array_map( 'sanitize_html_class', $classes );

		return array_unique( $classes );
	}

	/**
	 * Splits the original image HTML from the post content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $content
	 * @return string
	 */
	public function split_content( $content ) {

		remove_filter( 'the_content', array( $this, 'split_content' ), 9 );

		return str_replace( $this->original_image, '', $content );
	}
}

/**
 * Deletes the image cache for the specific post when the 'save_post' hook is fired.
 *
 * @since  0.7.0
 * @access private
 * @param  int      $post_id  The ID of the post to delete the cache for.
 * @return void
 */
function get_the_image_delete_cache_by_post( $post_id ) {
	wp_cache_delete( $post_id, 'get_the_image' );
}

/**
 * Deletes the image cache for a specific post when the 'added_post_meta', 'deleted_post_meta', 
 * or 'updated_post_meta' hooks are called.
 *
 * @since  0.7.0
 * @access private
 * @param  int      $meta_id  The ID of the metadata being updated.
 * @param  int      $post_id  The ID of the post to delete the cache for.
 * @return void
 */
function get_the_image_delete_cache_by_meta( $meta_id, $post_id ) {
	wp_cache_delete( $post_id, 'get_the_image' );
}


/* === Deprecated functions === */


/**
 * @since      0.1.0
 * @deprecated 0.3.0
 * @access     public
 */
function get_the_image_link() {
	_deprecated_function( __FUNCTION__, '0.3.0', 'get_the_image' );
	get_the_image( array( 'link_to_post' => true ) );
}

/**
 * @since      0.3.0
 * @deprecated 0.7.0
 * @access     private
 */
function image_by_custom_field() {}

/**
 * @since      0.4.0
 * @deprecated 0.7.0
 * @access     private
 */
function image_by_the_post_thumbnail() {}

/**
 * @since      0.3.0
 * @deprecated 0.7.0
 * @access     private
 */
function image_by_attachment() {}

/**
 * @since      0.3.0
 * @deprecated 0.7.0
 * @access     private
 */
function image_by_scan() {}

/**
 * @since      0.3.0
 * @deprecated 0.7.0
 * @access     private
 */
function image_by_default() {}

/**
 * @since      0.1.0
 * @deprecated 0.7.0
 * @access     private
 */
function display_the_image() {}

/**
 * @since      0.5.0
 * @deprecated 0.7.0
 * @access     private
 */
function get_the_image_delete_cache() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_by_meta_key() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_by_post_thumbnail() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_by_attachment() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_by_scan() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_by_default() {}

/**
 * @since      0.7.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_format() {}

/**
 * @since      0.6.0
 * @deprecated 1.0.0
 * @access     private
 */
function get_the_image_meta_key_save() {}
