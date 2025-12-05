<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Given it's a very specific case.
namespace Yoast\WP\SEO\Services\Importing\Aioseo;

use Yoast\WP\SEO\Helpers\Aioseo_Helper;
use Yoast\WP\SEO\Helpers\Image_Helper;

/**
 * Provides AISOEO social images urls.
 */
class Aioseo_Social_Images_Provider_Service {

	/**
	 * The AIOSEO helper.
	 *
	 * @var Aioseo_Helper
	 */
	protected $aioseo_helper;

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * Class constructor.
	 *
	 * @param Aioseo_Helper $aioseo_helper The AIOSEO helper.
	 * @param Image_Helper  $image         The image helper.
	 */
	public function __construct( Aioseo_Helper $aioseo_helper, Image_Helper $image ) {
		$this->aioseo_helper = $aioseo_helper;
		$this->image         = $image;
	}

	/**
	 * Retrieves the default source of social images.
	 *
	 * @param string $social_setting The social settings we're working with, eg. open-graph or twitter.
	 *
	 * @return string The default source of social images.
	 */
	public function get_default_social_image_source( $social_setting ) {
		return $this->get_social_defaults( 'source', $social_setting );
	}

	/**
	 * Retrieves the default custom social image if there is any.
	 *
	 * @param string $social_setting The social settings we're working with, eg. open-graph or twitter.
	 *
	 * @return string The global default social image.
	 */
	public function get_default_custom_social_image( $social_setting ) {
		return $this->get_social_defaults( 'custom_image', $social_setting );
	}

	/**
	 * Retrieves social defaults, be it Default Post Image Source or Default Post Image.
	 *
	 * @param string $setting        The setting we want, eg. source or custom image.
	 * @param string $social_setting The social settings we're working with, eg. open-graph or twitter.
	 *
	 * @return string The social default.
	 */
	public function get_social_defaults( $setting, $social_setting ) {
		switch ( $setting ) {
			case 'source':
				$setting_key = 'defaultImageSourcePosts';
				break;
			case 'custom_image':
				$setting_key = 'defaultImagePosts';
				break;
			default:
				return '';
		}

		$aioseo_settings = $this->aioseo_helper->get_global_option();

		if ( $social_setting === 'og' ) {
			$social_setting = 'facebook';
		}

		if ( ! isset( $aioseo_settings['social'][ $social_setting ]['general'][ $setting_key ] ) ) {
			return '';
		}

		return $aioseo_settings['social'][ $social_setting ]['general'][ $setting_key ];
	}

	/**
	 * Retrieves the url of the first image in content.
	 *
	 * @param int $post_id The post id to extract the image from.
	 *
	 * @return string The url of the first image in content.
	 */
	public function get_first_image_in_content( $post_id ) {
		$image = $this->image->get_gallery_image( $post_id );

		if ( ! $image ) {
			$image = $this->image->get_post_content_image( $post_id );
		}

		return $image;
	}

	/**
	 * Retrieves the url of the first attached image.
	 *
	 * @param int $post_id The post id to extract the image from.
	 *
	 * @return string The url of the first attached image.
	 */
	public function get_first_attached_image( $post_id ) {
		if ( \get_post_type( $post_id ) === 'attachment' ) {
			return $this->image->get_attachment_image_source( $post_id, 'fullsize' );
		}

		$attachments = \get_children(
			[
				'post_parent'    => $post_id,
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
			]
		);

		if ( $attachments && ! empty( $attachments ) ) {
			return $this->image->get_attachment_image_source( \array_values( $attachments )[0]->ID, 'fullsize' );
		}

		return '';
	}

	/**
	 * Retrieves the url of the featured image.
	 *
	 * @param int $post_id The post id to extract the image from.
	 *
	 * @return string The url of the featured image.
	 */
	public function get_featured_image( $post_id ) {
		$feature_image_id = \get_post_thumbnail_id( $post_id );

		if ( $feature_image_id ) {
			return $this->image->get_attachment_image_source( $feature_image_id, 'fullsize' );
		}

		return '';
	}

	/**
	 * Retrieves the url of the first available image. Tries each image source to get one image.
	 *
	 * @param int $post_id The post id to extract the image from.
	 *
	 * @return string The url of the featured image.
	 */
	public function get_auto_image( $post_id ) {
		$image = $this->get_first_attached_image( $post_id );

		if ( ! $image ) {
			$image = $this->get_first_image_in_content( $post_id );
		}

		return $image;
	}
}
