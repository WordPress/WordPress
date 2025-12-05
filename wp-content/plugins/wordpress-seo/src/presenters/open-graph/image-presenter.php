<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

/**
 * Presenter class for the Open Graph image.
 */
class Image_Presenter extends Abstract_Indexable_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:image';

	/**
	 * Image tags that we output for each image.
	 *
	 * @var array<string>
	 */
	protected static $image_tags = [
		'width'  => 'width',
		'height' => 'height',
		'type'   => 'type',
	];

	/**
	 * Returns the image for a post.
	 *
	 * @return string The image tag.
	 */
	public function present() {
		$images = $this->get();

		if ( empty( $images ) ) {
			return '';
		}

		$return = '';
		foreach ( $images as $image_meta ) {
			$image_url = $image_meta['url'];

			if ( \is_attachment() ) {
				global $wp;
				$image_url = \home_url( $wp->request );
			}

			$class = \is_admin_bar_showing() ? ' class="yoast-seo-meta-tag"' : '';

			$return .= '<meta property="og:image" content="' . \esc_url( $image_url, null, 'attribute' ) . '"' . $class . ' />';

			foreach ( static::$image_tags as $key => $value ) {
				if ( empty( $image_meta[ $key ] ) ) {
					continue;
				}

				$return .= \PHP_EOL . "\t" . '<meta property="og:image:' . \esc_attr( $key ) . '" content="' . \esc_attr( $image_meta[ $key ] ) . '"' . $class . ' />';
			}
		}

		return $return;
	}

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return array<string, int> The raw value.
	 */
	public function get() {
		$images = [];

		foreach ( $this->presentation->open_graph_images as $open_graph_image ) {
			$images[] = \array_intersect_key(
				// First filter the object.
				$this->filter( $open_graph_image ),
				// Then strip all keys that aren't in the image tags or the url.
				\array_flip( \array_merge( static::$image_tags, [ 'url' ] ) )
			);
		}

		return \array_filter( $images );
	}

	/**
	 * Run the image content through the `wpseo_opengraph_image` filter.
	 *
	 * @param array<string, string|int> $image The image.
	 *
	 * @return array<string, string|int> The filtered image.
	 */
	protected function filter( $image ) {
		/**
		 * Filter: 'wpseo_opengraph_image' - Allow changing the Open Graph image url.
		 *
		 * @param string                 $image_url    The URL of the Open Graph image.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$image_url = \apply_filters( 'wpseo_opengraph_image', $image['url'], $this->presentation );
		if ( ! empty( $image_url ) && \is_string( $image_url ) ) {
			$image['url'] = \trim( $image_url );
		}

		$image_type = ( $image['type'] ?? '' );
		/**
		 * Filter: 'wpseo_opengraph_image_type' - Allow changing the Open Graph image type.
		 *
		 * @param string                 $image_type   The type of the Open Graph image.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$image_type = \apply_filters( 'wpseo_opengraph_image_type', $image_type, $this->presentation );
		if ( ! empty( $image_type ) && \is_string( $image_type ) ) {
			$image['type'] = \trim( $image_type );
		}
		else {
			$image['type'] = '';
		}

		$image_width = ( $image['width'] ?? '' );
		/**
		 * Filter: 'wpseo_opengraph_image_width' - Allow changing the Open Graph image width.
		 *
		 * @param int                    $image_width  The width of the Open Graph image.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$image_width = (int) \apply_filters( 'wpseo_opengraph_image_width', $image_width, $this->presentation );
		if ( ! empty( $image_width ) && $image_width > 0 ) {
			$image['width'] = $image_width;
		}
		else {
			$image['width'] = '';
		}

		$image_height = ( $image['height'] ?? '' );
		/**
		 * Filter: 'wpseo_opengraph_image_height' - Allow changing the Open Graph image height.
		 *
		 * @param int                    $image_height The height of the Open Graph image.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$image_height = (int) \apply_filters( 'wpseo_opengraph_image_height', $image_height, $this->presentation );
		if ( ! empty( $image_height ) && $image_height > 0 ) {
			$image['height'] = $image_height;
		}
		else {
			$image['height'] = '';
		}

		return $image;
	}
}
