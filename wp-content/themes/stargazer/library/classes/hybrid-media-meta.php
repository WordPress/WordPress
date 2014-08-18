<?php
/**
 * Class for getting and formatting attachment metadata.  The class currently handles attachment metadata for 
 * the image, audio, and video mime types.  It may handle other types in the future, depending on the direction 
 * of WordPress core.  The purpose of this class is wrap up the return values of the core WP function 
 * `wp_get_attachment_metadata()` into a more usuable format for theme authors so that they can easily display 
 * data related to media in their themes.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Wrapper function for the Hybrid_Media_Meta class.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function hybrid_media_meta( $args = array() ) {

	$meta = new Hybrid_Media_Meta( $args );

	return $meta->display();
}

/**
 * Class for getting and formatting attachment metadata.
 *
 * @since  2.0.0
 * @access public
 */
class Hybrid_Media_Meta {

	/**
	 * Arguments passed in.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $args  = array();

	/**
	 * Metadata from the wp_get_attachment_metadata() function.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $meta  = array();

	/**
	 * Array of items found and formatted.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $items = array();

	/**
	 * Sets up and runs the functionality for getting the attachment meta.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$defaults = array(
			'post_id' => get_the_ID(),
			'labels'  => array(),
			'echo'    => true,
		);

		$this->args = apply_filters( 'hybrid_media_meta_args', wp_parse_args( $args, $defaults ) );

		/* Get the attachment metadata. */
		$this->meta = wp_get_attachment_metadata( $this->args['post_id'] );

		/* If the attachment is an image. */
		if ( wp_attachment_is_image( $this->args['post_id'] ) )
			$this->image_meta();

		/* If the attachment is audio. */
		elseif ( hybrid_attachment_is_audio( $this->args['post_id'] ) )
			$this->audio_meta();

		/* If the attachment is video. */
		elseif ( hybrid_attachment_is_video( $this->args['post_id'] ) )
			$this->video_meta();
	}

	/**
	 * Returns the array of formatted meta items.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return array
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Returns or outputs a list of formatted attachment meta.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return string
	 */
	public function display() {

		$display = '';

		/* If the items array isn't empty. */
		if ( !empty( $this->items ) ) {

			/* Format each of the items. */
			foreach ( $this->items as $item )
				$display .= sprintf( '<li><span class="prep">%s</span> <span class="data">%s</span></li>', $item[1], $item[0] );

			/* Add the items to a list. */
			$display = '<ul class="media-meta">' . $display . '</ul>';
		}

		if ( true === $this->args['echo'] )
			echo $display;
		else
			return $display;
	}

	/**
	 * Adds and formats image metadata for the items array.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function image_meta() {

		/* If there's a width and height. */
		if ( !empty( $this->meta['width'] ) && !empty( $this->meta['height'] ) ) {

			$this->items['dimensions'] = array(
				/* Translators: Media dimensions - 1 is width and 2 is height. */
				'<a href="' . esc_url( wp_get_attachment_url() ) . '">' . sprintf( __( '%1$s &#215; %2$s', 'hybrid-core' ), number_format_i18n( absint( $this->meta['width'] ) ), number_format_i18n( absint( $this->meta['height'] ) ) ) . '</a>',
				__( 'Dimensions', 'hybrid-core' )
			);
		}

		/* If a timestamp exists, add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['created_timestamp'] ) )
			$this->items['created_timestamp'] = array( date_i18n( get_option( 'date_format' ), $this->meta['image_meta']['created_timestamp'] ), __( 'Date', 'hybrid-core' ) );

		/* If a camera exists, add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['camera'] ) )
			$this->items['camera'] = array( $this->meta['image_meta']['camera'], __( 'Camera', 'hybrid-core' ) );

		/* If an aperture exists, add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['aperture'] ) )
			$this->items['aperture'] = array( sprintf( '<sup>f</sup>&#8260;<sub>%s</sub>', $this->meta['image_meta']['aperture'] ), __( 'Aperture', 'hybrid-core' ) );

		/* If a focal length is set, add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['focal_length'] ) )
			/* Translators: Camera focal length. */
			$this->items['focal_length'] = array( sprintf( __( '%s mm', 'hybrid-core' ), $this->meta['image_meta']['focal_length'] ), __( 'Focal Length', 'hybrid-core' ) );

		/* If an ISO is set, add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['iso'] ) ) {
			$this->items['iso'] = array(
				$this->meta['image_meta']['iso'], 
				'<abbr title="' . __( 'International Organization for Standardization', 'hybrid-core' ) . '">' . __( 'ISO', 'hybrid-core' ) . '</abbr>'
			);
		}

		/* If a shutter speed is given, format the float into a fraction and add it to the $items array. */
		if ( !empty( $this->meta['image_meta']['shutter_speed'] ) ) {

			if ( ( 1 / $this->meta['image_meta']['shutter_speed'] ) > 1 ) {
				$shutter_speed = '<sup>' . number_format_i18n( 1 ) . '</sup>&#8260;';

				if ( number_format( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 1 ) ==  number_format( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 0 ) )
					$shutter_speed .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 0, '.', '' ) );
				else
					$shutter_speed .= sprintf( '<sub>%s</sub>', number_format_i18n( ( 1 / $this->meta['image_meta']['shutter_speed'] ), 1, '.', '' ) );
			} else {
				$shutter_speed = $this->meta['image_meta']['shutter_speed'];
			}

			/* Translators: Camera shutter speed. "sec" is an abbreviation for "seconds". */
			$this->items['shutter_speed'] = array( sprintf( __( '%s sec', 'hybrid-core' ), $shutter_speed ), __( 'Shutter Speed', 'hybrid-core' ) );
		}
	}

	/**
	 * Adds and formats audio metadata for the items array.
	 *
	 * Note that we're purposely leaving out the "transcript/lyrics" metadata in this instance.  This 
	 * is because it doesn't fit in well with how other metadata works on display.  There's a separate 
	 * function for that called `hybrid_get_audio_transcript()`.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function audio_meta() {

		/* Get ID3 keys (labels for metadata). */
		$id3_keys = wp_get_attachment_id3_keys( get_post( $this->args['post_id'] ) );

		/* Formated length of time the audio file runs. */
		if ( !empty( $this->meta['length_formatted'] ) )
			$this->items['length_formatted'] = array( $this->meta['length_formatted'], $id3_keys['length_formatted'] );

		/* Artist. */
		if ( !empty( $this->meta['artist'] ) )
			$this->items['artist'] = array( $this->meta['artist'], $id3_keys['artist'] );

		/* Composer. */
		if ( !empty( $this->meta['composer'] ) )
			$this->items['composer'] = array( $this->meta['composer'], $id3_keys['composer'] );

		/* Album. */
		if ( !empty( $this->meta['album'] ) )
			$this->items['album'] = array( $this->meta['album'], $id3_keys['album'] );

		/* Track number (should also be an album if this is set). */
		if ( !empty( $this->meta['track_number'] ) )
			$this->items['track_number'] = array( $this->meta['track_number'], $id3_keys['track_number'] );

		/* Year. */
		if ( !empty( $this->meta['year'] ) )
			$this->items['year'] = array( $this->meta['year'], $id3_keys['year'] );

		/* Genre. */
		if ( !empty( $this->meta['genre'] ) )
			$this->items['genre'] = array( $this->meta['genre'], $id3_keys['genre'] );

		/* File name.  We're linking this to the actual file URL. */
		$this->items['file_name'] = array( '<a href="' . esc_url( wp_get_attachment_url( $this->args['post_id'] ) ) . '">' . basename( get_attached_file( $this->args['post_id'] ) ) . '</a>', __( 'File Name', 'hybrid-core' ) );

		/* File size. */
		if ( !empty( $this->meta['filesize'] ) )
			$this->items['filesize'] = array( size_format( $this->meta['filesize'], 2 ), $id3_keys['filesize'] );

		/* File type (the metadata for this can be incorrect, so we're just looking at the actual file). */
		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->args['post_id'] ), $matches ) )
			$this->items['file_type'] = array( esc_html( strtoupper( $matches[1] ) ), __( 'File Type', 'hybrid-core' ) );

		/* Mime type. */
		if ( !empty( $this->meta['mime_type'] ) )
			$this->items['mime_type'] = array( $this->meta['mime_type'], $id3_keys['mime_type'] );
	}

	/**
	 * Adds and formats video meta data for the items array.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function video_meta() {

		/* Get ID3 keys (labels for metadata). */
		$id3_keys = wp_get_attachment_id3_keys( get_post( $this->args['post_id'] ) );

		/* Formated length of time the video file runs. */
		if ( !empty( $this->meta['length_formatted'] ) )
			$this->items['length_formatted'] = array( $this->meta['length_formatted'], $id3_keys['length_formatted'] );

		/* Dimensions (width x height in pixels). */
		if ( !empty( $this->meta['width'] ) && !empty( $this->meta['height'] ) )
			/* Translators: Media dimensions - 1 is width and 2 is height. */
			$this->items['dimensions'] = array( sprintf( __( '%1$s &#215; %2$s', 'hybrid-core' ), number_format_i18n( absint( $this->meta['width'] ) ), number_format_i18n( absint( $this->meta['height'] ) ) ), __( 'Dimensions', 'hybrid-core' ) );

		/* File name.  We're linking this to the actual file URL. */
		$this->items['file_name'] = array( '<a href="' . esc_url( wp_get_attachment_url( $this->args['post_id'] ) ) . '">' . basename( get_attached_file( $this->args['post_id'] ) ) . '</a>', __( 'File Name', 'hybrid-core' ) );

		/* File size. */
		if ( !empty( $this->meta['filesize'] ) )
			$this->items['filesize'] = array( size_format( $this->meta['filesize'], 2 ), $id3_keys['filesize'] );

		/* File type (the metadata for this can be incorrect, so we're just looking at the actual file). */
		if ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $this->args['post_id'] ), $matches ) )
			$this->items['file_type'] = array( esc_html( strtoupper( $matches[1] ) ), __( 'File Type', 'hybrid-core' ) );

		/* Mime type. */
		if ( !empty( $this->meta['mime_type'] ) )
			$this->items['mime_type'] = array( $this->meta['mime_type'], $id3_keys['mime_type'] );
	}
}
