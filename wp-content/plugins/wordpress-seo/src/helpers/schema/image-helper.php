<?php

namespace Yoast\WP\SEO\Helpers\Schema;

use Yoast\WP\SEO\Helpers\Image_Helper as Main_Image_Helper;

/**
 * Class Image_Helper.
 */
class Image_Helper {

	/**
	 * The HTML helper.
	 *
	 * @var HTML_Helper
	 */
	private $html;

	/**
	 * The language helper.
	 *
	 * @var Language_Helper
	 */
	private $language;

	/**
	 * Represents the main image helper.
	 *
	 * @var Main_Image_Helper
	 */
	private $image;

	/**
	 * Image_Helper constructor.
	 *
	 * @codeCoverageIgnore It handles dependencies.
	 *
	 * @param HTML_Helper       $html     The HTML helper.
	 * @param Language_Helper   $language The language helper.
	 * @param Main_Image_Helper $image    The 'main' image helper.
	 */
	public function __construct( HTML_Helper $html, Language_Helper $language, Main_Image_Helper $image ) {
		$this->html     = $html;
		$this->language = $language;
		$this->image    = $image;
	}

	/**
	 * Find an image based on its URL and generate a Schema object for it.
	 *
	 * @param string $schema_id      The `@id` to use for the returned image.
	 * @param string $url            The image URL to base our object on.
	 * @param string $caption        An optional caption.
	 * @param bool   $add_hash       Whether a hash will be added as a suffix in the @id.
	 * @param bool   $use_link_table Whether the SEO Links table will be used to retrieve the id.
	 *
	 * @return array Schema ImageObject array.
	 */
	public function generate_from_url( $schema_id, $url, $caption = '', $add_hash = false, $use_link_table = true ) {
		$attachment_id = $this->image->get_attachment_by_url( $url, $use_link_table );
		if ( $attachment_id > 0 ) {
			return $this->generate_from_attachment_id( $schema_id, $attachment_id, $caption, $add_hash );
		}

		return $this->simple_image_object( $schema_id, $url, $caption, $add_hash );
	}

	/**
	 * Retrieve data about an image from the database and use it to generate a Schema object.
	 *
	 * @param string $schema_id     The `@id` to use for the returned image.
	 * @param int    $attachment_id The attachment to retrieve data from.
	 * @param string $caption       The caption string, if there is one.
	 * @param bool   $add_hash      Whether a hash will be added as a suffix in the @id.
	 *
	 * @return array Schema ImageObject array.
	 */
	public function generate_from_attachment_id( $schema_id, $attachment_id, $caption = '', $add_hash = false ) {
		$data = $this->generate_object();
		$url  = $this->image->get_attachment_image_url( $attachment_id, 'full' );

		$id_suffix = ( $add_hash ) ? \md5( $url ) : '';

		$data['@id']        = $schema_id . $id_suffix;
		$data['url']        = $url;
		$data['contentUrl'] = $url;
		$data               = $this->add_image_size( $data, $attachment_id );
		$data               = $this->add_caption( $data, $attachment_id, $caption );

		return $data;
	}

	/**
	 * Retrieve data about an image from the database and use it to generate a Schema object.
	 *
	 * @param string $schema_id       The `@id` to use for the returned image.
	 * @param array  $attachment_meta The attachment metadata.
	 * @param string $caption         The caption string, if there is one.
	 * @param bool   $add_hash        Whether a hash will be added as a suffix in the @id.
	 *
	 * @return array Schema ImageObject array.
	 */
	public function generate_from_attachment_meta( $schema_id, $attachment_meta, $caption = '', $add_hash = false ) {
		$data = $this->generate_object();

		$id_suffix = ( $add_hash ) ? \md5( $attachment_meta['url'] ) : '';

		$data['@id']        = $schema_id . $id_suffix;
		$data['url']        = $attachment_meta['url'];
		$data['contentUrl'] = $data['url'];
		$data['width']      = $attachment_meta['width'];
		$data['height']     = $attachment_meta['height'];
		if ( ! empty( $caption ) ) {
			$data['caption'] = $this->html->smart_strip_tags( $caption );
		}

		return $data;
	}

	/**
	 * If we can't find $url in our database, we output a simple ImageObject.
	 *
	 * @param string $schema_id The `@id` to use for the returned image.
	 * @param string $url       The image URL.
	 * @param string $caption   A caption, if set.
	 * @param bool   $add_hash  Whether a hash will be added as a suffix in the @id.
	 *
	 * @return array Schema ImageObject array.
	 */
	public function simple_image_object( $schema_id, $url, $caption = '', $add_hash = false ) {
		$data = $this->generate_object();

		$id_suffix = ( $add_hash ) ? \md5( $url ) : '';

		$data['@id']        = $schema_id . $id_suffix;
		$data['url']        = $url;
		$data['contentUrl'] = $url;

		if ( ! empty( $caption ) ) {
			$data['caption'] = $this->html->smart_strip_tags( $caption );
		}

		return $data;
	}

	/**
	 * Retrieves an image's caption if set, or uses the alt tag if that's set.
	 *
	 * @param array  $data          An ImageObject Schema array.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $caption       The caption string, if there is one.
	 *
	 * @return array An imageObject with width and height set if available.
	 */
	private function add_caption( $data, $attachment_id, $caption = '' ) {
		if ( $caption !== '' ) {
			$data['caption'] = $caption;

			return $data;
		}

		$caption = $this->image->get_caption( $attachment_id );
		if ( ! empty( $caption ) ) {
			$data['caption'] = $this->html->smart_strip_tags( $caption );

			return $data;
		}

		return $data;
	}

	/**
	 * Generates our bare bone ImageObject.
	 *
	 * @return array an empty ImageObject
	 */
	private function generate_object() {
		$data = [
			'@type' => 'ImageObject',
		];

		$data = $this->language->add_piece_language( $data );

		return $data;
	}

	/**
	 * Adds image's width and height.
	 *
	 * @param array $data          An ImageObject Schema array.
	 * @param int   $attachment_id Attachment ID.
	 *
	 * @return array An imageObject with width and height set if available.
	 */
	private function add_image_size( $data, $attachment_id ) {
		$image_meta = $this->image->get_metadata( $attachment_id );
		if ( empty( $image_meta['width'] ) || empty( $image_meta['height'] ) ) {
			return $data;
		}
		$data['width']  = $image_meta['width'];
		$data['height'] = $image_meta['height'];

		return $data;
	}
}
