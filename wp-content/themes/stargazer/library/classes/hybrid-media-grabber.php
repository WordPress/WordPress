<?php
/**
 * Hybrid Media Grabber - A script for grabbing media related to a post.
 *
 * Hybrid Media Grabber is a script for pulling media either from the post content or attached to the 
 * post.  It's an attempt to consolidate the various methods that users have used over the years to 
 * embed media into their posts.  This script was written so that theme developers could grab that 
 * media and use it in interesting ways within their themes.  For example, a theme could get a video 
 * and display it on archive pages alongside the post excerpt or pull it out of the content to display 
 * it above the post on single post views.
 *
 * @package    Hybrid
 * @subpackage Classes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Wrapper function for the Hybrid_Media_Grabber class.  Returns the HTML output for the found media.
 *
 * @since  1.6.0
 * @access public
 * @param  array
 * @return string
 */
function hybrid_media_grabber( $args = array() ) {

	$media = new Hybrid_Media_Grabber( $args );

	return $media->get_media();
}

/**
 * Grabs media related to the post.
 *
 * @since  1.6.0
 * @access public
 * @return void
 */
class Hybrid_Media_Grabber {

	/**
	 * The HTML version of the media to return.
	 *
	 * @since  1.6.0
	 * @access public
	 * @var    string
	 */
	public $media = '';

	/**
	 * The original media taken from the post content.
	 *
	 * @since  1.6.0
	 * @access public
	 * @var    string
	 */
	public $original_media = '';

	/**
	 * The type of media to get.  Current supported types are 'audio' and 'video'.
	 *
	 * @since  1.6.0
	 * @access public
	 * @var    string
	 */
	public $type = 'video';

	/**
	 * Arguments passed into the class and parsed with the defaults.
	 *
	 * @since  1.6.0
	 * @access public
	 * @var    array
	 */
	public $args = array();

	/**
	 * The content to search for embedded media within.
	 *
	 * @since  1.6.0
	 * @access public
	 * @var    string
	 */
	public $content = '';

	/**
	 * Constructor method.  Sets up the media grabber.
	 *
	 * @since  1.6.0
	 * @access public
	 * @global object $wp_embed
	 * @global int    $content_width
	 * @return void
	 */
	public function __construct( $args = array() ) {
		global $wp_embed, $content_width;

		/* Use WP's embed functionality to handle the [embed] shortcode and autoembeds. */
		add_filter( 'hybrid_media_grabber_embed_shortcode_media', array( $wp_embed, 'run_shortcode' ) );
		add_filter( 'hybrid_media_grabber_autoembed_media',       array( $wp_embed, 'autoembed' ) );

		/* Don't return a link if embeds don't work. Need media or nothing at all. */
		add_filter( 'embed_maybe_make_link', '__return_false' );

		/* Set up the default arguments. */
		$defaults = array(
			'post_id'     => get_the_ID(),   // post ID (assumes within The Loop by default)
			'type'        => 'video',        // audio|video
			'before'      => '',             // HTML before the output
			'after'       => '',             // HTML after the output
			'split_media' => false,          // Splits the media from the post content
			'width'       => $content_width, // Custom width. Defaults to the theme's content width.
		);

		/* Set the object properties. */
		$this->args    = apply_filters( 'hybrid_media_grabber_args', wp_parse_args( $args, $defaults ) );
		$this->content = get_post_field( 'post_content', $this->args['post_id'] );
		$this->type    = isset( $this->args['type'] ) && in_array( $this->args['type'], array( 'audio', 'video' ) ) ? $this->args['type'] : 'video';

		/* Find the media related to the post. */
		$this->set_media();
	}

	/**
	 * Destructor method.  Removes filters we needed to add.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		remove_filter( 'embed_maybe_make_link', '__return_false' );
	}

	/**
	 * Basic method for returning the media found.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return string
	 */
	public function get_media() {
		return apply_filters( 'hybrid_media_grabber_media', $this->media, $this );
	}

	/**
	 * Tries several methods to find media related to the post.  Returns the found media.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function set_media() {

		/* Get the media if the post type is an attachment. */
		if ( 'attachment' === get_post_type( $this->args['post_id'] ) )
			$this->do_attachment_media();

		/* Find media in the post content based on WordPress' media-related shortcodes. */
		if ( empty( $this->media ) )
			$this->do_shortcode_media();

		/* If no media is found and autoembeds are enabled, check for autoembeds. */
		if ( empty( $this->media ) && get_option( 'embed_autourls' ) )
			$this->do_autoembed_media();

		/* If no media is found, check for media HTML within the post content. */
		if ( empty( $this->media ) )
			$this->do_embedded_media();

		/* If no media is found, check for media attached to the post. */
		if ( empty( $this->media ) )
			$this->do_attached_media();

		/* If media is found, let's run a few things. */
		if ( !empty( $this->media ) ) {

			/* Add the before HTML. */
			if ( isset( $this->args['before'] ) )
				$this->media = $this->args['before'] . $this->media;

			/* Add the after HTML. */
			if ( isset( $this->args['after'] ) )
				$this->media .= $this->args['after'];

			/* Split the media from the content. */
			if ( true === $this->args['split_media'] && !empty( $this->original_media ) )
				add_filter( 'the_content', array( $this, 'split_media' ), 5 );

			/* Filter the media dimensions. */
			$this->media = $this->filter_dimensions( $this->media );
		}
	}

	/**
	 * WordPress has a few shortcodes for handling embedding media:  [audio], [video], and [embed].  This 
	 * method figures out the shortcode used in the content.  Once it's found, the appropriate method for 
	 * the shortcode is executed.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function do_shortcode_media() {

		/* Finds matches for shortcodes in the content. */
		preg_match_all( '/' . get_shortcode_regex() . '/s', $this->content, $matches, PREG_SET_ORDER );

		/* If matches are found, loop through them and check if they match one of WP's media shortcodes. */
		if ( !empty( $matches ) ) {

			foreach ( $matches as $shortcode ) {

				/* Call the method related to the specific shortcode found and break out of the loop. */
				if ( in_array( $shortcode[2], array( 'playlist', 'embed', $this->type ) ) ) {
					call_user_func( array( $this, "do_{$shortcode[2]}_shortcode_media" ), $shortcode );
					break;
				}

				/* Check for Jetpack audio/video shortcodes. */
				elseif ( in_array( $shortcode[2], array( 'blip.tv', 'dailymotion', 'flickr', 'ted', 'vimeo', 'vine', 'youtube', 'wpvideo', 'soundcloud', 'bandcamp' ) ) ) {
					$this->do_jetpack_shortcode_media( $shortcode );
					break;
				}
			}
		}
	}

	/**
	 * Handles the output of the WordPress playlist feature.  This searches for the [playlist] shortcode 
	 * if it's used in the content.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function do_playlist_shortcode_media( $shortcode ) {

		$this->original_media = array_shift( $shortcode );

		$this->media = do_shortcode( $this->original_media );
	}

	/**
	 * Handles the HTML when the [embed] shortcode is used.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  array  $shortcode
	 * @return void
	 */
	public function do_embed_shortcode_media( $shortcode ) {

		$this->original_media = array_shift( $shortcode );

		$this->media = apply_filters(
			'hybrid_media_grabber_embed_shortcode_media',
			$this->original_media
		);
	}

	/**
	 * Handles the HTML when the [audio] shortcode is used.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  array  $shortcode
	 * @return void
	 */
	public function do_audio_shortcode_media( $shortcode ) {

		$this->original_media = array_shift( $shortcode );

		$this->media = do_shortcode( $this->original_media );
	}

	/**
	 * Handles the HTML when the [video] shortcode is used.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  array  $shortcode
	 * @return void
	 */
	public function do_video_shortcode_media( $shortcode ) {

		$this->original_media = array_shift( $shortcode );

		/* Need to filter dimensions here to overwrite WP's <div> surrounding the [video] shortcode. */
		$this->media = do_shortcode( $this->filter_dimensions( $this->original_media ) );
	}

	/**
	 * Handles the output of audio/video shortcodes included with the Jetpack plugin (or Jetpack 
	 * Slim) via the Shortcode Embeds feature.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function do_jetpack_shortcode_media( $shortcode ) {

		$this->original_media = array_shift( $shortcode );

		$this->media = do_shortcode( $this->original_media );
	}

	/**
	 * Uses WordPress' autoembed feature to automatically to handle media that's just input as a URL.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function do_autoembed_media() {

		preg_match_all( '|^\s*(https?://[^\s"]+)\s*$|im', $this->content, $matches, PREG_SET_ORDER );

		/* If URL matches are found, loop through them to see if we can get an embed. */
		if ( is_array( $matches ) ) {

			foreach ( $matches as $value ) {

				/* Let WP work its magic with the 'autoembed' method. */
				$embed = trim( apply_filters( 'hybrid_media_grabber_autoembed_media', $value[0] ) );

				if ( !empty( $embed ) ) {
					$this->original_media = $value[0];
					$this->media = $embed;
					break;
				}
			}
		}
	}

	/**
	 * Grabs media embbeded into the content within <iframe>, <object>, <embed>, and other HTML methods for 
	 * embedding media.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function do_embedded_media() {

		$embedded_media = get_media_embedded_in_content( $this->content );

		if ( !empty( $embedded_media ) )
			$this->media = $this->original_media = array_shift( $embedded_media );
	}

	/**
	 * Gets media attached to the post.  Then, uses the WordPress [audio] or [video] shortcode to handle 
	 * the HTML output of the media.
	 *
	 * @since  1.6.0
	 * @access public
	 * @return void
	 */
	public function do_attached_media() {

		/* Gets media attached to the post by mime type. */
		$attached_media = get_attached_media( $this->type, $this->args['post_id'] );

		/* If media is found. */
		if ( !empty( $attached_media ) ) {

			/* Get the first attachment/post object found for the post. */
			$post = array_shift( $attached_media );

			/* Gets the URI for the attachment (the media file). */
			$url = wp_get_attachment_url( $post->ID );

			/* Run the media as a shortcode using WordPress' built-in [audio] and [video] shortcodes. */
			$this->media = do_shortcode( "[{$this->type} src='{$url}']" );
		}
	}

	/**
	 * If the post type itself is an attachment, run the shortcode for the media type.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function do_attachment_media() {

		/* Gets the URI for the attachment (the media file). */
		$url = wp_get_attachment_url( $this->args['post_id'] );

		/* Run the media as a shortcode using WordPress' built-in [audio] and [video] shortcodes. */
		$this->media = do_shortcode( "[{$this->type} src='{$url}']" );
	}

	/**
	 * Removes the found media from the content.  The purpose of this is so that themes can retrieve the 
	 * media from the content and display it elsewhere on the page based on its design.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  string  $content
	 * @return string
	 */
	public function split_media( $content ) {

		remove_filter( 'the_content', array( $this, 'split_media' ), 5 );

		return str_replace( $this->original_media, '', $content );
	}

	/**
	 * Method for filtering the media's 'width' and 'height' attributes so that the theme can handle the 
	 * dimensions how it sees fit.
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  string  $html
	 * @return string
	 */
	public function filter_dimensions( $html ) {

		$_html = strip_tags( $html, '<object><embed><iframe><video>' );

		/* Find the attributes of the media. */
		$atts = wp_kses_hair( $_html, array( 'http', 'https' ) );

		/* Loop through the media attributes and add them in key/value pairs. */
		foreach ( $atts as $att )
			$media_atts[ $att['name'] ] = $att['value'];

		/* If no dimensions are found, just return the HTML. */
		if ( empty( $media_atts ) || !isset( $media_atts['width'] ) || !isset( $media_atts['height'] ) )
			return $html;

		/* Set the max width. */
		$max_width = $this->args['width'];

		/* Set the max height based on the max width and original width/height ratio. */
		$max_height = round( $max_width / ( $media_atts['width'] / $media_atts['height'] ) );

		/* Fix for Spotify embeds. */
		if ( !empty( $media_atts['src'] ) && preg_match( '#https?://(embed)\.spotify\.com/.*#i', $media_atts['src'], $matches ) )
			list( $max_width, $max_height ) = $this->spotify_dimensions( $media_atts );

		/* Calculate new media dimensions. */
		$dimensions = wp_expand_dimensions( 
			$media_atts['width'], 
			$media_atts['height'], 
			$max_width,
			$max_height
		);

		/* Allow devs to filter the final width and height of the media. */
		list( $width, $height ) = apply_filters( 
			'hybrid_media_grabber_dimensions', 
			$dimensions,                       // width/height array
			$media_atts,                       // media HTML attributes
			$this                              // media grabber object
		);

		/* Set up the patterns for the 'width' and 'height' attributes. */
		$patterns = array(
			'/(width=[\'"]).+?([\'"])/i',
			'/(height=[\'"]).+?([\'"])/i',
			'/(<div.+?style=[\'"].*?width:.+?).+?(px;.+?[\'"].*?>)/i'
		);

		/* Set up the replacements for the 'width' and 'height' attributes. */
		$replacements = array(
			'${1}' . $width . '${2}',
			'${1}' . $height . '${2}',
			'${1}' . $width . '${2}'
		);

		/* Filter the dimensions and return the media HTML. */
		return preg_replace( $patterns, $replacements, $html );
	}

	/**
	 * Fix for Spotify embeds because they're the only embeddable service that doesn't work that well 
	 * with custom-sized embeds.  So, we need to adjust this the best we can.  Right now, the only 
	 * embed size that works for full-width embeds is the "compact" player (height of 80).
	 *
	 * @since  1.6.0
	 * @access public
	 * @param  array   $media_atts
	 * @return array
	 */
	public function spotify_dimensions( $media_atts ) {

		$max_width  = $media_atts['width'];
		$max_height = $media_atts['height'];

		if ( 80 == $media_atts['height'] )
			$max_width  = $this->args['width'];

		return array( $max_width, $max_height );
	}
}
