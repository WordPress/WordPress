<?php

namespace Yoast\WP\SEO\Images\Application;

use DOMDocument;
use WP_HTML_Tag_Processor;

/**
 * The image content extractor.
 */
class Image_Content_Extractor {

	/**
	 * Gathers all images from content.
	 *
	 * @param string $content The content.
	 *
	 * @return int[] An associated array of image IDs, keyed by their URLs.
	 */
	public function gather_images( $content ) {

		/**
		 * Filter 'wpseo_force_creating_and_using_attachment_indexables' - Filters if we should use attachment indexables to find all content images. Instead of scanning the content.
		 *
		 * The default value is false.
		 *
		 * @since 21.1
		 */
		$should_not_parse_content = \apply_filters( 'wpseo_force_creating_and_using_attachment_indexables', false );
		/**
		 * Filter 'wpseo_force_skip_image_content_parsing' - Filters if we should force skip scanning the content to parse images.
		 * This filter can be used if the regex gives a faster result than scanning the code.
		 *
		 * The default value is false.
		 *
		 * @since 21.1
		 */
		$should_not_parse_content = \apply_filters( 'wpseo_force_skip_image_content_parsing', $should_not_parse_content );

		if ( ! $should_not_parse_content && \class_exists( WP_HTML_Tag_Processor::class ) ) {
			return $this->gather_images_wp( $content );
		}

		if ( ! $should_not_parse_content && \class_exists( DOMDocument::class ) ) {

			return $this->gather_images_DOMDocument( $content );
		}

		if ( \strpos( $content, 'src' ) === false ) {
			// Nothing to do.
			return [];
		}

		$images = [];
		$regexp = '<img\s[^>]*src=("??)([^" >]*?)\\1[^>]*>';
		// Used modifiers iU to match case insensitive and make greedy quantifiers lazy.
		if ( \preg_match_all( "/$regexp/iU", $content, $matches, \PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$images[ $match[2] ] = 0;
			}
		}

		return $images;
	}

	/**
	 * Gathers all images from content with WP's WP_HTML_Tag_Processor() and returns them along with their IDs, if
	 * possible.
	 *
	 * @param string $content The content.
	 *
	 * @return int[] An associated array of image IDs, keyed by their URL.
	 */
	protected function gather_images_wp( $content ) {
		$processor = new WP_HTML_Tag_Processor( $content );
		$images    = [];

		$query = [
			'tag_name' => 'img',
		];

		/**
		 * Filter 'wpseo_image_attribute_containing_id' - Allows filtering what attribute will be used to extract image IDs from.
		 *
		 * Defaults to "class", which is where WP natively stores the image IDs, in a `wp-image-<ID>` format.
		 *
		 * @api string The attribute to be used to extract image IDs from.
		 */
		$attribute = \apply_filters( 'wpseo_image_attribute_containing_id', 'class' );
		while ( $processor->next_tag( $query ) ) {
			$src_raw = $processor->get_attribute( 'src' );
			if ( ! $src_raw ) {
				continue;
			}

			$src     = \htmlentities( $src_raw, ( \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML401 ), \get_bloginfo( 'charset' ) );
			$classes = $processor->get_attribute( $attribute );
			$id      = $this->extract_id_of_classes( $classes );

			$images[ $src ] = $id;
		}

		return $images;
	}

	/**
	 * Gathers all images from content with DOMDocument() and returns them along with their IDs, if possible.
	 *
	 * @param string $content The content.
	 *
	 * @return int[] An associated array of image IDs, keyed by their URL.
	 */
	protected function gather_images_domdocument( $content ) {
		$images  = [];
		$charset = \get_bloginfo( 'charset' );

		/**
		 * Filter 'wpseo_image_attribute_containing_id' - Allows filtering what attribute will be used to extract image IDs from.
		 *
		 * Defaults to "class", which is where WP natively stores the image IDs, in a `wp-image-<ID>` format.
		 *
		 * @api string The attribute to be used to extract image IDs from.
		 */
		$attribute = \apply_filters( 'wpseo_image_attribute_containing_id', 'class' );

		\libxml_use_internal_errors( true );
		$post_dom = new DOMDocument();
		$post_dom->loadHTML( '<?xml encoding="' . $charset . '">' . $content );
		\libxml_clear_errors();

		foreach ( $post_dom->getElementsByTagName( 'img' ) as $img ) {
			$src     = \htmlentities( $img->getAttribute( 'src' ), ( \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML401 ), $charset );
			$classes = $img->getAttribute( $attribute );

			$id = $this->extract_id_of_classes( $classes );

			$images[ $src ] = $id;
		}

		return $images;
	}

	/**
	 * Extracts image ID out of the image's classes.
	 *
	 * @param string $classes The classes assigned to the image.
	 *
	 * @return int The ID that's extracted from the classes.
	 */
	protected function extract_id_of_classes( $classes ) {
		if ( ! $classes ) {
			return 0;
		}

		/**
		 * Filter 'wpseo_extract_id_pattern' - Allows filtering the regex patern to be used to extract image IDs from class/attribute names.
		 *
		 * Defaults to the pattern that extracts image IDs from core's `wp-image-<ID>` native format in image classes.
		 *
		 * @api string The regex pattern to be used to extract image IDs from class names. Empty string if the whole class/attribute should be returned.
		 */
		$pattern = \apply_filters( 'wpseo_extract_id_pattern', '/(?<!\S)wp-image-(\d+)(?!\S)/i' );

		if ( $pattern === '' ) {
			return (int) $classes;
		}

		$matches = [];

		if ( \preg_match( $pattern, $classes, $matches ) ) {

			return (int) $matches[1];
		}

		return 0;
	}
}
