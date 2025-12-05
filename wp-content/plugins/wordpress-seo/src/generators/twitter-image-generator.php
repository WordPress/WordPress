<?php

namespace Yoast\WP\SEO\Generators;

use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Twitter\Image_Helper as Twitter_Image_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Twitter\Images;

/**
 * Represents the generator class for the Twitter images.
 */
class Twitter_Image_Generator implements Generator_Interface {

	/**
	 * The Twitter image helper.
	 *
	 * @var Twitter_Image_Helper
	 */
	protected $twitter_image;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	protected $url;

	/**
	 * Twitter_Image_Generator constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Image_Helper         $image         The image helper.
	 * @param Url_Helper           $url           The url helper.
	 * @param Twitter_Image_Helper $twitter_image The Twitter image helper.
	 */
	public function __construct( Image_Helper $image, Url_Helper $url, Twitter_Image_Helper $twitter_image ) {
		$this->image         = $image;
		$this->url           = $url;
		$this->twitter_image = $twitter_image;
	}

	/**
	 * Retrieves the images for an indexable.
	 *
	 * @param Meta_Tags_Context $context The context.
	 *
	 * @return array<array<string, string|int>> The images.
	 */
	public function generate( Meta_Tags_Context $context ) {
		$image_container = $this->get_image_container();

		$this->add_from_indexable( $context->indexable, $image_container );

		return $image_container->get_images();
	}

	/**
	 * Adds an image based on the given indexable.
	 *
	 * @param Indexable $indexable       The indexable.
	 * @param Images    $image_container The image container.
	 *
	 * @return void
	 */
	protected function add_from_indexable( Indexable $indexable, Images $image_container ) {
		if ( $indexable->twitter_image_id ) {
			$image_container->add_image_by_id( $indexable->twitter_image_id );
			return;
		}

		if ( $indexable->twitter_image ) {
			$image_container->add_image_by_url( $indexable->twitter_image );
		}
	}

	/**
	 * Retrieves an instance of the image container.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return Images The image container.
	 */
	protected function get_image_container() {
		$image_container             = new Images( $this->image, $this->url );
		$image_container->image_size = $this->twitter_image->get_image_size();

		$image_container->set_helpers( $this->twitter_image );

		return $image_container;
	}
}
