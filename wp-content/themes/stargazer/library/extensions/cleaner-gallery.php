<?php
/**
 * Cleaner Gallery - A valid image gallery script for WordPress.
 *
 * Cleaner Gallery was created to clean up the invalid HTML and remove the inline styles of the default 
 * implementation of the WordPress [gallery] shortcode.  This has the obvious benefits of creating 
 * sites with clean, valid code.  But, it also allows developers to more easily create custom styles for 
 * galleries within their themes.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package   CleanerGallery
 * @version   1.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cleaner Gallery class.  This wraps everything up nicely.
 *
 * @since  1.1.0
 */
final class Cleaner_Gallery {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.1.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Array of all the arguments for the gallery.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    array
	 */
	public $args = array();

	/**
	 * Instance of the gallery for this post. Used so that galleries don't have duplicate IDs 
	 * when there's multiple galleries in a post.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    int
	 */
	public $gallery_instance = 0;

	/**
	 * Type of gallery currently being viewed.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    string
	 */
	public $gallery_type = '';

	/**
	 * Array of all the media mime types in the current gallery.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    array
	 */
	public $mime_types = array();

	/**
	 * Whether a gallery item has a caption. This changes per image.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    bool
	 */
	public $has_caption = false;

	/**
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Filter the post gallery shortcode output. */
		add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 2 );
	}

	/**
	 * Filter for the 'post_gallery' hook, which is run when WordPress' [gallery] shortcode is 
	 * executed.  This is the main function that handles the output of the new gallery.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  string  $output
	 * @param  array   $attr
	 * @return string
	 */
	public function gallery_shortcode( $output, $attr ) {

		/* We're not worried about galleries in feeds, so just return the output here. */
		if ( is_feed() )
			return $output;

		/* Filters to add Schema.org microdata support. */
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'attachment_image_attributes' ), 5, 2 );
		add_filter( 'wp_get_attachment_link',             array( $this, 'get_attachment_link'         ), 5    );

		/* Iterate the gallery istance. */
		$this->gallery_instance++;

		/* Set the gallery item iterator to 0. */
		$i = 0;

		/* Set up the arguments. */
		$this->set_up_args( $attr );

		/* Set up the query arguments for getting the attachments. */
		$children = array(
			'post_status'      => 'inherit',
			'post_type'        => 'attachment',
			'post_mime_type'   => $this->args['mime_type'],
			'order'            => $this->args['order'],
			'orderby'          => $this->args['orderby'],
			'exclude'          => $this->args['exclude'],
			'include'          => $this->args['include'],
			'numberposts'      => $this->args['numberposts'],
			'offset'           => $this->args['offset'],
			'suppress_filters' => true
		);

		/* If specific IDs should not be included, use the get_children() function. */
		if ( empty( $this->args['include'] ) ) {
			$attachments = get_children( array_merge( array( 'post_parent' => $this->args['id'] ), $children ) );
		}

		/* If getting specific attachments by ID, use get_posts(). */
		else {
			$attachments = get_posts( $children );
		}

		/* If there are no attachments, return an empty string. */
		if ( empty( $attachments ) )
			return '';

		/* Count the number of attachments returned. */
		$attachment_count = count( $attachments );

		/* Allow developers to overwrite the number of columns. This can be useful for reducing columns with with fewer images than number of columns. */
		//$columns = ( ( $columns <= $attachment_count ) ? intval( $columns ) : intval( $attachment_count ) );
		$this->args['columns'] = apply_filters( 'cleaner_gallery_columns', intval( $this->args['columns'] ), $attachment_count, $this->args );

		/* Loop through each attachment. */
		foreach ( $attachments as $attachment ) {

			/* Open each gallery row. */
			if ( $this->args['columns'] > 0 && $i % $this->args['columns'] == 0 )
				$output .= "\n\t\t\t\t<div class='gallery-row gallery-col-{$this->args['columns']} gallery-clear'>";

			/* Get the gallery item. */
			$output .= $this->get_gallery_item( $attachment );

			/* Close gallery row. */
			if ( $this->args['columns'] > 0 && ++$i % $this->args['columns'] == 0 )
				$output .= "\n\t\t\t\t</div>";
		}

		/* Close gallery row. */
		if ( $this->args['columns'] > 0 && $i % $this->args['columns'] !== 0 )
			$output .= "\n\t\t\t</div>";

		/* Remove filters for Schema.org microdata support. */
		remove_filter( 'wp_get_attachment_image_attributes', array( $this, 'attachment_image_attributes' ) );
		remove_filter( 'wp_get_attachment_link',             array( $this, 'get_attachment_link'         ) );

		/* Gallery attributes. */
		$gallery_attr  = sprintf( "id='%s'", esc_attr( $this->args['id'] ) . '-' . esc_attr( $this->gallery_instance ) );
		$gallery_attr .= sprintf( " class='gallery gallery-%s gallery-columns-%s gallery-size-%s'", esc_attr( $this->args['id'] ), esc_attr( $this->args['columns'] ), sanitize_html_class( $this->args['size'] ) );
		$gallery_attr .= sprintf( " itemscope itemtype='%s'", esc_attr( $this->get_gallery_itemtype() ) );

		/* Return out very nice, valid HTML gallery. */
		return "\n\t\t\t" . sprintf( '<div %s>', $gallery_attr ) . $output . "\n\t\t\t</div><!-- .gallery -->\n";
	}

	/**
	 * Method for setting up, parsing, and providing filter hooks for the arguments.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public function set_up_args( $attr ) {

		/* Orderby. */
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}

		/* Default gallery settings. */
		$defaults = array(
			'order'       => 'ASC',
			'orderby'     => 'menu_order ID',
			'id'          => get_the_ID(),
			'mime_type'   => 'image',
			'link'        => '',
			'itemtag'     => 'figure',
			'icontag'     => 'div',
			'captiontag'  => 'figcaption',
			'columns'     => 3,
			'size'        => has_image_size( 'post-thumbnail' ) ? 'post-thumbnail' : 'thumbnail',
			'ids'         => '',
			'include'     => '',
			'exclude'     => '',
			'numberposts' => -1,
			'offset'      => ''
		);

		/* Apply filters to the default arguments. */
		$defaults = apply_filters( 'cleaner_gallery_defaults', $defaults );

		/* Merge the defaults with user input.  */
		$this->args = shortcode_atts( $defaults, $attr );

		/* Apply filters to the arguments. */
		$this->args = apply_filters( 'cleaner_gallery_args', $this->args );

		/* Make sure the post ID is a valid integer. */
		$this->args['id'] = intval( $this->args['id'] );

		/* Properly escape the gallery tags. */
		$this->args['itemtag']    = tag_escape( $this->args['itemtag'] );
		$this->args['icontag']    = tag_escape( $this->args['icontag'] );
		$this->args['captiontag'] = tag_escape( $this->args['captiontag'] );
	}

	/**
	 * Formats and returns the gallery item.  The gallery item is composed of both the gallery 
	 * icon (image) and gallery caption.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  object  $attachment
	 * @return string
	 */
	public function get_gallery_item( $attachment ) {

		/* Get the mime type for the current attachment. */
		list( $type, $subtype ) = false !== strpos( $attachment->post_mime_type, '/' ) ? explode( '/', $attachment->post_mime_type ) : array( $attachment->post_mime_type, '' );

		/* Add the mime type to the array of mime types for the gallery. */
		$this->mime_types[] = $type;

		/* Set up the itemtype for the media based off the mime type. */
		if ( 'image' === $type )
			$itemtype = 'http://schema.org/ImageObject';
		elseif ( 'video' === $type )
			$itemtype = 'http://schema.org/VideoObject';
		elseif ( 'audio' === $type )
			$itemtype = 'http://schema.org/AudioObject';
		else
			$itemtype = 'http://schema.org/MediaObject';

		/* Open each gallery item. */
		$output = "\n\t\t\t\t\t<{$this->args['itemtag']} class='gallery-item col-{$this->args['columns']}' itemprop='associatedMedia' itemscope itemtype='{$itemtype}'>";

		/* Get the gallery caption first b/c we need it for 'aria-describedby'. */
		$caption = $this->get_gallery_caption( $attachment );

		/* Get the gallery icon. */
		$icon = $this->get_gallery_icon( $attachment );

		/* Add the icon and caption. */
		$output .= $icon . $caption;

		/* Close individual gallery item. */
		$output .= "\n\t\t\t\t\t</{$this->args['itemtag']}>";

		return $output;
	}

	/**
	 * Gets the gallery icon, which is the gallery image. Formats the output.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  object  $attachment
	 * @return string
	 */
	public function get_gallery_icon( $attachment ) {

		/* Get the image size. */
		$size = $this->args['size'];

		/* Get the image attachment meta. */
		$image_meta  = wp_get_attachment_metadata( $attachment->ID );

		/* Get the image orientation (portrait|landscape) based off the width and height. */
		$orientation = '';

		/* If the size for the current attachment exists and both the width and height are defined. */
		if ( isset( $image_meta['sizes'][ $size ] ) && isset( $image_meta['sizes'][ $size ]['height'], $image_meta['sizes'][ $size ]['width'] ) ) {
			$orientation = ( $image_meta['sizes'][ $size ]['height'] > $image_meta['sizes'][ $size ]['width'] ) ? 'portrait' : 'landscape';
		}

		/* Else, if both the width and height are defined, set the orientation. */
		elseif ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}

		/* Open the gallery icon element. */
		$output = "\n\t\t\t\t\t\t<{$this->args['icontag']} class='gallery-icon {$orientation}'>";

		/* Get the image if it should link to the image file. */
		if ( isset( $this->args['link'] ) && 'file' == $this->args['link'] ) {
			$image = wp_get_attachment_link( $attachment->ID, $size, false, true );
		}

		/* Else if, get the image if it should link to nothing. */
		elseif ( isset( $this->args['link'] ) && 'none' == $this->args['link'] ) {
			$image = wp_get_attachment_image( $attachment->ID, $size, false );
		}

		/* Else, get the image (links to attachment page). */
		else {
			$image = wp_get_attachment_link( $attachment->ID, $size, true, true );
		}

		/* Apply filters over the image itself. */
		$output .= apply_filters( 'cleaner_gallery_image', $image, $attachment->ID, $this->args, $this->gallery_instance );

		/* Close the gallery icon element. */
		$output .= "</{$this->args['icontag']}>";

		/* Return the gallery icon output. */
		return $output;
	}

	/**
	 * Gets the gallery image caption and formats it.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  object  $attachment
	 * @return string
	 */
	public function get_gallery_caption( $attachment ) {

		/* Get the caption. */
		$caption = apply_filters( 'cleaner_gallery_caption', wptexturize( $attachment->post_excerpt ), $attachment->ID, $this->args, $this->gallery_instance );

		/* If image caption is set, format and return. */
		if ( !empty( $caption ) ) {
			$this->has_caption = true;
			return "\n\t\t\t\t\t\t" . sprintf( '<%1$s id="%2$s" class="gallery-caption" itemprop="caption">%3$s</%1$s>', $this->args['captiontag'], esc_attr( "figcaption-{$this->args['id']}-{$attachment->ID}" ), $caption );
		}

		/* Return an empty string if there's no caption. */
		$this->has_caption = false;
		return '';
	}

	/**
	 * Gets the gallery's itemptype.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return string
	 */
	public function get_gallery_itemtype() {

		/* Make sure the array of mime types is unique. */
		$this->mime_types = array_unique( $this->mime_types );

		/* Get a count of the different mime types. */
		$mime_count = count( $this->mime_types );

		/* If the only mime type is 'image'. */
		if ( 1 === $mime_count && 'image' === $this->mime_types[0] )
			$itemtype = 'http://schema.org/ImageGallery';

		/* If the only mime type is 'video'. */
		elseif ( 1 === $mime_count && 'video' === $this->mime_types[0] )
			$itemtype = 'http://schema.org/VideoGallery';

		/* Else, set up a generall "collection". */
		else
			$itemtype = 'http://schema.org/CollectionPage';

		/* Return the itemtype. */
		return $itemtype;
	}

	/**
	 * Filters the gallery image attributes and adds the 'itemprop' attribute.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  array  $attr
	 * @param  object $attachment
	 * @return array
	 */
	public function attachment_image_attributes( $attr, $attachment ) {

		if ( true === $this->has_caption )
			$attr['aria-describedby'] = esc_attr( "figcaption-{$this->args['id']}-{$attachment->ID}" );

		$attr['itemprop'] = 'thumbnail';

		return $attr;
	}

	/**
	 * Filters the attachment link and adds the 'itemprop' attribute.
	 *
	 * @since  1.1.0
	 * @access public
	 * @param  string  $link
	 * @return string
	 */
	public function get_attachment_link( $link ) {

		return preg_replace( '/(<a.*?)>/i', '$1 itemprop="contentURL">', $link );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Cleaner_Gallery::get_instance();

/**
 * @since  0.9.0
 */
function cleaner_gallery( $output, $attr ) {}
