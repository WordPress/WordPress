<?php

namespace Yoast\WP\SEO\Helpers\Twitter;

use Yoast\WP\SEO\Helpers\Image_Helper as Base_Image_Helper;

/**
 * A helper object for Twitter images.
 */
class Image_Helper {

	/**
	 * The base image helper.
	 *
	 * @var Base_Image_Helper
	 */
	private $image;

	/**
	 * Image_Helper constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Base_Image_Helper $image The image helper.
	 */
	public function __construct( Base_Image_Helper $image ) {
		$this->image = $image;
	}

	/**
	 * The image size to use for Twitter.
	 *
	 * @return string Image size string.
	 */
	public function get_image_size() {
		/**
		 * Filter: 'wpseo_twitter_image_size' - Allow changing the Twitter Card image size.
		 *
		 * @param string $featured_img Image size string.
		 */
		return (string) \apply_filters( 'wpseo_twitter_image_size', 'full' );
	}

	/**
	 * Retrieves an image url by its id.
	 *
	 * @param int $image_id The image id.
	 *
	 * @return string The image url. Empty string if the attachment is not valid.
	 */
	public function get_by_id( $image_id ) {
		if ( ! $this->image->is_valid_attachment( $image_id ) ) {
			return '';
		}

		return $this->image->get_attachment_image_source( $image_id, $this->get_image_size() );
	}
}
