<?php
namespace Elementor\Core\Files\File_Types;

use Elementor\Core\Utils\Exceptions;
use Elementor\Core\Utils\Svg\Svg_Sanitizer;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Svg extends Base {

	/**
	 * Inline svg attachment meta key
	 */
	const META_KEY = '_elementor_inline_svg';

	const SCRIPT_REGEX = '/(?:\w+script|data):/xi';

	/**
	 * Get File Extension
	 *
	 * Returns the file type's file extension
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return string - file extension
	 */
	public function get_file_extension() {
		return 'svg';
	}

	/**
	 * Get Mime Type
	 *
	 * Returns the file type's mime type
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return string mime type
	 */
	public function get_mime_type() {
		return 'image/svg+xml';
	}

	/**
	 * Sanitize SVG
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param $filename
	 * @return bool
	 */
	public function sanitize_svg( $filename ) {
		return ( new SVG_Sanitizer() )->sanitize_file( $filename );
	}

	/**
	 * Validate File
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param $file
	 * @return bool|\WP_Error
	 */
	public function validate_file( $file ) {
		if ( ! $this->sanitize_svg( $file['tmp_name'] ) ) {
			return new \WP_Error( Exceptions::FORBIDDEN, esc_html__( 'This file is not allowed for security reasons.', 'elementor' ) );
		}

		return true;
	}

	/**
	 * Sanitizer
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param $content
	 * @return bool|string
	 */
	public function sanitizer( $content ) {
		return ( new SVG_Sanitizer() )->sanitize( $content );
	}

	/**
	 * WP Prepare Attachment For J
	 *
	 * Runs on the `wp_prepare_attachment_for_js` filter.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param $attachment_data
	 * @param $attachment
	 * @param $meta
	 *
	 * @return mixed
	 */
	public function wp_prepare_attachment_for_js( $attachment_data, $attachment, $meta ) {
		if ( 'image' !== $attachment_data['type'] || 'svg+xml' !== $attachment_data['subtype'] || ! class_exists( 'SimpleXMLElement' ) ) {
			return $attachment_data;
		}

		$svg = self::get_inline_svg( $attachment->ID );

		if ( ! $svg ) {
			return $attachment_data;
		}

		try {
			$svg = new \SimpleXMLElement( $svg );
		} catch ( \Exception $e ) {
			return $attachment_data;
		}

		$src = $attachment_data['url'];
		$width = (int) $svg['width'];
		$height = (int) $svg['height'];

		// Media Gallery
		$attachment_data['image'] = compact( 'src', 'width', 'height' );
		$attachment_data['thumb'] = compact( 'src', 'width', 'height' );

		// Single Details of Image
		$attachment_data['sizes']['full'] = [
			'height' => $height,
			'width' => $width,
			'url' => $src,
			'orientation' => $height > $width ? 'portrait' : 'landscape',
		];
		return $attachment_data;
	}

	/**
	 * Set Svg Meta Data
	 *
	 * Adds dimensions metadata to uploaded SVG files, since WordPress doesn't do it.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function set_svg_meta_data( $data, $id ) {
		$attachment = get_post( $id ); // Filter makes sure that the post is an attachment.
		$mime_type = $attachment->post_mime_type;

		// If the attachment is an svg
		if ( 'image/svg+xml' === $mime_type ) {
			// If the svg metadata are empty or the width is empty or the height is empty.
			// then get the attributes from xml.
			if ( empty( $data ) || empty( $data['width'] ) || empty( $data['height'] ) ) {
				$attachment = wp_get_attachment_url( $id );
				$xml = simplexml_load_file( $attachment );

				if ( ! empty( $xml ) ) {
					$attr = $xml->attributes();
					$view_box = explode( ' ', $attr->viewBox );// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$data['width'] = isset( $attr->width ) && preg_match( '/\d+/', $attr->width, $value ) ? (int) $value[0] : ( 4 === count( $view_box ) ? (int) $view_box[2] : null );
					$data['height'] = isset( $attr->height ) && preg_match( '/\d+/', $attr->height, $value ) ? (int) $value[0] : ( 4 === count( $view_box ) ? (int) $view_box[3] : null );
				}
			}
		}

		return $data;
	}

	/**
	 * Delete Meta Cache
	 *
	 * Deletes the Inline SVG post meta entry.
	 *
	 * @since 3.5.0
	 * @access public
	 */
	public function delete_meta_cache() {
		delete_post_meta_by_key( self::META_KEY );
	}

	/**
	 * File Sanitizer Can Run
	 *
	 * Checks if the classes required for the file sanitizer are in memory.
	 *
	 * @since 3.5.0
	 * @access public
	 * @static
	 *
	 * @return bool
	 */
	public static function file_sanitizer_can_run() {
		return class_exists( 'DOMDocument' ) && class_exists( 'SimpleXMLElement' );
	}

	/**
	 * Get Inline SVG
	 *
	 * @since 3.5.0
	 * @access public
	 * @static
	 *
	 * @param $attachment_id
	 * @return bool|mixed|string
	 */
	public static function get_inline_svg( $attachment_id ) {
		$svg = get_post_meta( $attachment_id, self::META_KEY, true );

		if ( ! empty( $svg ) ) {
			$valid_svg = ( new SVG_Sanitizer() )->sanitize( $svg );

			return ( false === $valid_svg ) ? '' : $valid_svg;
		}

		$attachment_file = get_attached_file( $attachment_id );

		if ( ! file_exists( $attachment_file ) ) {
			return '';
		}

		$svg = Utils::file_get_contents( $attachment_file );

		$valid_svg = ( new SVG_Sanitizer() )->sanitize( $svg );

		if ( false === $valid_svg ) {
			return '';
		}

		if ( ! empty( $valid_svg ) ) {
			update_post_meta( $attachment_id, self::META_KEY, $valid_svg );
		}

		return $valid_svg;
	}

	public function __construct() {
		add_filter( 'wp_update_attachment_metadata', [ $this, 'set_svg_meta_data' ], 10, 2 );
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'wp_prepare_attachment_for_js' ], 10, 3 );
		add_action( 'elementor/core/files/clear_cache', [ $this, 'delete_meta_cache' ] );
	}
}
