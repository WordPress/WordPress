<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

/**
 * WPSEO_Content_Images.
 */
class WPSEO_Content_Images {

	/**
	 * Retrieves images from the post content.
	 *
	 * @param int          $post_id The post ID.
	 * @param WP_Post|null $post    The post object.
	 *
	 * @return array An array of images found in this post.
	 */
	public function get_images( $post_id, $post = null ) {
		return $this->get_images_from_content( $this->get_post_content( $post_id, $post ) );
	}

	/**
	 * Grabs the images from the content.
	 *
	 * @param string $content The post content string.
	 *
	 * @return array An array of image URLs.
	 */
	public function get_images_from_content( $content ) {
		if ( ! is_string( $content ) ) {
			return [];
		}

		$content_images = $this->get_img_tags_from_content( $content );

		$images = array_map( [ $this, 'get_img_tag_source' ], $content_images );
		$images = array_filter( $images );
		$images = array_unique( $images );
		$images = array_values( $images ); // Reset the array keys.
		return $images;
	}

	/**
	 * Gets the image tags from a given content string.
	 *
	 * @param string $content The content to search for image tags.
	 *
	 * @return array An array of `<img>` tags.
	 */
	private function get_img_tags_from_content( $content ) {
		if ( strpos( $content, '<img' ) === false ) {
			return [];
		}

		preg_match_all( '`<img [^>]+>`', $content, $matches );
		if ( isset( $matches[0] ) ) {
			return $matches[0];
		}

		return [];
	}

	/**
	 * Retrieves the image URL from an image tag.
	 *
	 * @param string $image Image HTML element.
	 *
	 * @return string|bool The image URL on success, false on failure.
	 */
	private function get_img_tag_source( $image ) {
		preg_match( '`src=(["\'])(.*?)\1`', $image, $matches );
		if ( isset( $matches[2] ) && filter_var( $matches[2], FILTER_VALIDATE_URL ) ) {
			return $matches[2];
		}
		return false;
	}

	/**
	 * Retrieves the post content we want to work with.
	 *
	 * @param int                $post_id The post ID.
	 * @param WP_Post|array|null $post    The post.
	 *
	 * @return string The content of the supplied post.
	 */
	private function get_post_content( $post_id, $post ) {
		if ( $post === null ) {
			$post = get_post( $post_id );
		}

		if ( $post === null ) {
			return '';
		}

		/**
		 * Filter: 'wpseo_pre_analysis_post_content' - Allow filtering the content before analysis.
		 *
		 * @param string  $post_content The Post content string.
		 * @param WP_Post $post         The current post.
		 */
		$content = apply_filters( 'wpseo_pre_analysis_post_content', $post->post_content, $post );

		if ( ! is_string( $content ) ) {
			$content = '';
		}

		return $content;
	}
}
