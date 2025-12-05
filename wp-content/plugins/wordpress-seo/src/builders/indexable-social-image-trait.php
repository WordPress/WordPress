<?php

namespace Yoast\WP\SEO\Builders;

use WPSEO_Utils;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Open_Graph\Image_Helper as Open_Graph_Image_Helper;
use Yoast\WP\SEO\Helpers\Twitter\Image_Helper as Twitter_Image_Helper;
use Yoast\WP\SEO\Models\Indexable;

/**
 * Trait for determine the social image to use in the indexable.
 *
 * Represents the trait used in builders for handling social images.
 */
trait Indexable_Social_Image_Trait {

	/**
	 * The image helper.
	 *
	 * @var Image_Helper
	 */
	protected $image;

	/**
	 * The Open Graph image helper.
	 *
	 * @var Open_Graph_Image_Helper
	 */
	protected $open_graph_image;

	/**
	 * The Twitter image helper.
	 *
	 * @var Twitter_Image_Helper
	 */
	protected $twitter_image;

	/**
	 * Sets the helpers for the trait.
	 *
	 * @required
	 *
	 * @param Image_Helper            $image            The image helper.
	 * @param Open_Graph_Image_Helper $open_graph_image The Open Graph image helper.
	 * @param Twitter_Image_Helper    $twitter_image    The Twitter image helper.
	 *
	 * @return void
	 */
	public function set_social_image_helpers(
		Image_Helper $image,
		Open_Graph_Image_Helper $open_graph_image,
		Twitter_Image_Helper $twitter_image
	) {
		$this->image            = $image;
		$this->open_graph_image = $open_graph_image;
		$this->twitter_image    = $twitter_image;
	}

	/**
	 * Sets the alternative on an indexable.
	 *
	 * @param array     $alternative_image The alternative image to set.
	 * @param Indexable $indexable         The indexable to set image for.
	 *
	 * @return void
	 */
	protected function set_alternative_image( array $alternative_image, Indexable $indexable ) {
		if ( ! empty( $alternative_image['image_id'] ) ) {
			if ( ! $indexable->open_graph_image_source && ! $indexable->open_graph_image_id ) {
				$indexable->open_graph_image_id     = $alternative_image['image_id'];
				$indexable->open_graph_image_source = $alternative_image['source'];

				$this->set_open_graph_image_meta_data( $indexable );
			}

			if ( ! $indexable->twitter_image && ! $indexable->twitter_image_id ) {
				$indexable->twitter_image        = $this->twitter_image->get_by_id( $alternative_image['image_id'] );
				$indexable->twitter_image_id     = $alternative_image['image_id'];
				$indexable->twitter_image_source = $alternative_image['source'];
			}
		}

		if ( ! empty( $alternative_image['image'] ) ) {
			if ( ! $indexable->open_graph_image_source && ! $indexable->open_graph_image_id ) {
				$indexable->open_graph_image        = $alternative_image['image'];
				$indexable->open_graph_image_source = $alternative_image['source'];
			}

			if ( ! $indexable->twitter_image && ! $indexable->twitter_image_id ) {
				$indexable->twitter_image        = $alternative_image['image'];
				$indexable->twitter_image_source = $alternative_image['source'];
			}
		}
	}

	/**
	 * Sets the Open Graph image meta data for an og image
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return void
	 */
	protected function set_open_graph_image_meta_data( Indexable $indexable ) {
		if ( ! $indexable->open_graph_image_id ) {
			return;
		}

		$image = $this->open_graph_image->get_image_by_id( $indexable->open_graph_image_id );

		if ( ! empty( $image ) ) {
			$indexable->open_graph_image      = $image['url'];
			$indexable->open_graph_image_meta = WPSEO_Utils::format_json_encode( $image );
		}
	}

	/**
	 * Handles the social images.
	 *
	 * @param Indexable $indexable The indexable to handle.
	 *
	 * @return void
	 */
	protected function handle_social_images( Indexable $indexable ) {
		// When the image or image id is set.
		if ( $indexable->open_graph_image || $indexable->open_graph_image_id ) {
			$indexable->open_graph_image_source = 'set-by-user';

			$this->set_open_graph_image_meta_data( $indexable );
		}

		if ( $indexable->twitter_image || $indexable->twitter_image_id ) {
			$indexable->twitter_image_source = 'set-by-user';
		}

		if ( $indexable->twitter_image_id ) {
			$indexable->twitter_image = $this->twitter_image->get_by_id( $indexable->twitter_image_id );
		}

		// When image sources are set already.
		if ( $indexable->open_graph_image_source && $indexable->twitter_image_source ) {
			return;
		}

		$alternative_image = $this->find_alternative_image( $indexable );
		if ( ! empty( $alternative_image ) ) {
			$this->set_alternative_image( $alternative_image, $indexable );
		}
	}

	/**
	 * Resets the social images.
	 *
	 * @param Indexable $indexable The indexable to set images for.
	 *
	 * @return void
	 */
	protected function reset_social_images( Indexable $indexable ) {
		$indexable->open_graph_image        = null;
		$indexable->open_graph_image_id     = null;
		$indexable->open_graph_image_source = null;
		$indexable->open_graph_image_meta   = null;

		$indexable->twitter_image        = null;
		$indexable->twitter_image_id     = null;
		$indexable->twitter_image_source = null;
	}
}
