<?php

namespace Yoast\WP\SEO\Values\Open_Graph;

use Yoast\WP\SEO\Helpers\Open_Graph\Image_Helper as Open_Graph_Image_Helper;
use Yoast\WP\SEO\Values\Images as Base_Images;

/**
 * Value object for the Open Graph Images.
 */
class Images extends Base_Images {

	/**
	 * The Open Graph image helper.
	 *
	 * @var Open_Graph_Image_Helper
	 */
	protected $open_graph_image;

	/**
	 * Sets the helpers.
	 *
	 * @required
	 *
	 * @codeCoverageIgnore - Is handled by DI-container.
	 *
	 * @param Open_Graph_Image_Helper $open_graph_image Image helper for Open Graph.
	 *
	 * @return void
	 */
	public function set_helpers( Open_Graph_Image_Helper $open_graph_image ) {
		$this->open_graph_image = $open_graph_image;
	}

	/**
	 * Outputs the images.
	 *
	 * @codeCoverageIgnore - The method is empty, nothing to test.
	 *
	 * @return void
	 */
	public function show() {}

	/**
	 * Adds an image to the list by image ID.
	 *
	 * @param int $image_id The image ID to add.
	 *
	 * @return void
	 */
	public function add_image_by_id( $image_id ) {
		$attachment = $this->open_graph_image->get_image_by_id( $image_id );

		if ( $attachment ) {
			$this->add_image( $attachment );
		}
	}
}
