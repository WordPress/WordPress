<?php

namespace Yoast\WP\SEO\Values;

use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;

/**
 * Class Images
 *
 * Value object for the Images.
 */
class Images {

	/**
	 * The image size.
	 *
	 * @var string
	 */
	public $image_size = 'full';

	/**
	 * Holds the images that have been put out as image.
	 *
	 * @var array
	 */
	protected $images = [];

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
	 * Images constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Image_Helper $image The image helper.
	 * @param Url_Helper   $url   The url helper.
	 */
	public function __construct( Image_Helper $image, Url_Helper $url ) {
		$this->image = $image;
		$this->url   = $url;
	}

	/**
	 * Adds an image to the list by image ID.
	 *
	 * @param int $image_id The image ID to add.
	 *
	 * @return void
	 */
	public function add_image_by_id( $image_id ) {
		$image = $this->image->get_attachment_image_source( $image_id, $this->image_size );
		if ( $image ) {
			$this->add_image( $image );
		}
	}

	/**
	 * Adds an image to the list by image ID.
	 *
	 * @param string $image_meta JSON encoded image meta.
	 *
	 * @return void
	 */
	public function add_image_by_meta( $image_meta ) {
		$this->add_image( (array) \json_decode( $image_meta ) );
	}

	/**
	 * Return the images array.
	 *
	 * @return array The images.
	 */
	public function get_images() {
		return $this->images;
	}

	/**
	 * Check whether we have images or not.
	 *
	 * @return bool True if we have images, false if we don't.
	 */
	public function has_images() {
		return ! empty( $this->images );
	}

	/**
	 * Adds an image based on a given URL.
	 *
	 * @param string $url The given URL.
	 *
	 * @return number|null Returns the found image ID if it exists. Otherwise -1.
	 *                     If the URL is empty we return null.
	 */
	public function add_image_by_url( $url ) {
		if ( empty( $url ) ) {
			return null;
		}

		$image_id = $this->image->get_attachment_by_url( $url );

		if ( $image_id ) {
			$this->add_image_by_id( $image_id );

			return $image_id;
		}

		$this->add_image( $url );

		return -1;
	}

	/**
	 * Adds an image to the list of images.
	 *
	 * @param string|array $image Image array.
	 *
	 * @return void
	 */
	public function add_image( $image ) {
		if ( \is_string( $image ) ) {
			$image = [ 'url' => $image ];
		}

		if ( ! \is_array( $image ) || empty( $image['url'] ) || ! \is_string( $image['url'] ) ) {
			return;
		}

		if ( $this->url->is_relative( $image['url'] ) && $image['url'][0] === '/' ) {
			$image['url'] = $this->url->build_absolute_url( $image['url'] );
		}

		if ( \array_key_exists( $image['url'], $this->images ) ) {
			return;
		}

		$this->images[ $image['url'] ] = $image;
	}
}
