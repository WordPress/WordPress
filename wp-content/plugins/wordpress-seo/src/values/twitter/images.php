<?php

namespace Yoast\WP\SEO\Values\Twitter;

use Yoast\WP\SEO\Helpers\Twitter\Image_Helper as Twitter_Image_Helper;
use Yoast\WP\SEO\Values\Images as Base_Images;

/**
 * Value object for the twitter Images.
 */
class Images extends Base_Images {

	/**
	 * The twitter image helper.
	 *
	 * @var Twitter_Image_Helper
	 */
	protected $twitter_image;

	/**
	 * Sets the helpers.
	 *
	 * @required
	 *
	 * @codeCoverageIgnore - Is handled by DI-container.
	 *
	 * @param Twitter_Image_Helper $twitter_image Image helper for twitter.
	 *
	 * @return void
	 */
	public function set_helpers( Twitter_Image_Helper $twitter_image ) {
		$this->twitter_image = $twitter_image;
	}

	/**
	 * Adds an image to the list by image ID.
	 *
	 * @param int $image_id The image ID to add.
	 *
	 * @return void
	 */
	public function add_image_by_id( $image_id ) {
		$attachment = $this->twitter_image->get_by_id( $image_id );

		if ( $attachment ) {
			$this->add_image( $attachment );
		}
	}
}
