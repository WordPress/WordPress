<?php

namespace Yoast\WP\SEO\Helpers\Open_Graph;

use Yoast\WP\SEO\Helpers\Image_Helper as Base_Image_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;

/**
 * A helper object for Open Graph images.
 */
class Image_Helper {

	/**
	 * The URL helper.
	 *
	 * @var Url_Helper
	 */
	private $url;

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
	 * @param Url_Helper        $url   The url helper.
	 * @param Base_Image_Helper $image The image helper.
	 */
	public function __construct( Url_Helper $url, Base_Image_Helper $image ) {
		$this->url   = $url;
		$this->image = $image;
	}

	/**
	 * Determines whether the passed URL is considered valid.
	 *
	 * @deprecated 22.4
	 * @codeCoverageIgnore
	 *
	 * @param array<array<string, string|int>> $image The image array.
	 *
	 * @return bool Whether or not the URL is a valid image.
	 */
	public function is_image_url_valid( array $image ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 22.4' );

		if ( empty( $image['url'] ) || ! \is_string( $image['url'] ) ) {
			return false;
		}

		$image_extension = $this->url->get_extension_from_url( $image['url'] );
		$is_valid        = $this->image->is_extension_valid( $image_extension );

		/**
		 * Filter: 'wpseo_opengraph_is_valid_image_url' - Allows extra validation for an image url.
		 *
		 * @param bool   $is_valid Current validation result.
		 * @param string $url      The image url to validate.
		 */
		return (bool) \apply_filters( 'wpseo_opengraph_is_valid_image_url', $is_valid, $image['url'] );
	}

	/**
	 * Retrieves the overridden image size value.
	 *
	 * @return string|null The image size when overriden by filter or null when not.
	 */
	public function get_override_image_size() {
		/**
		 * Filter: 'wpseo_opengraph_image_size' - Allow overriding the image size used
		 * for Open Graph sharing. If this filter is used, the defined size will always be
		 * used for the og:image. The image will still be rejected if it is too small.
		 *
		 * Only use this filter if you manually want to determine the best image size
		 * for the `og:image` tag.
		 *
		 * Use the `wpseo_image_sizes` filter if you want to use our logic. That filter
		 * can be used to add an image size that needs to be taken into consideration
		 * within our own logic.
		 *
		 * @param string|false $size Size string.
		 */
		return \apply_filters( 'wpseo_opengraph_image_size', null );
	}

	/**
	 * Retrieves the image data by a given attachment id.
	 *
	 * @param int $attachment_id The attachment id.
	 *
	 * @return array<string, string|int>|false The image data when found, `false` when not.
	 */
	public function get_image_by_id( $attachment_id ) {
		if ( ! $this->image->is_valid_attachment( $attachment_id ) ) {
			return false;
		}

		$override_image_size = $this->get_override_image_size();
		if ( $override_image_size ) {
			return $this->image->get_image( $attachment_id, $override_image_size );
		}

		return $this->image->get_best_attachment_variation( $attachment_id );
	}
}
