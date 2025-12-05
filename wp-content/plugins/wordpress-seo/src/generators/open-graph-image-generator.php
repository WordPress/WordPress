<?php

namespace Yoast\WP\SEO\Generators;

use Error;
use Yoast\WP\SEO\Context\Meta_Tags_Context;
use Yoast\WP\SEO\Helpers\Image_Helper;
use Yoast\WP\SEO\Helpers\Open_Graph\Image_Helper as Open_Graph_Image_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Url_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Values\Open_Graph\Images;

/**
 * Represents the generator class for the Open Graph images.
 */
class Open_Graph_Image_Generator implements Generator_Interface {

	/**
	 * The Open Graph image helper.
	 *
	 * @var Open_Graph_Image_Helper
	 */
	protected $open_graph_image;

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
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Images constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Open_Graph_Image_Helper $open_graph_image Image helper for Open Graph.
	 * @param Image_Helper            $image            The image helper.
	 * @param Options_Helper          $options          The options helper.
	 * @param Url_Helper              $url              The url helper.
	 */
	public function __construct(
		Open_Graph_Image_Helper $open_graph_image,
		Image_Helper $image,
		Options_Helper $options,
		Url_Helper $url
	) {
		$this->open_graph_image = $open_graph_image;
		$this->image            = $image;
		$this->options          = $options;
		$this->url              = $url;
	}

	/**
	 * Retrieves the images for an indexable.
	 *
	 * For legacy reasons some plugins might expect we filter a WPSEO_Opengraph_Image object. That might cause
	 * type errors. This is why we try/catch our filters.
	 *
	 * @param Meta_Tags_Context $context The context.
	 *
	 * @return array The images.
	 */
	public function generate( Meta_Tags_Context $context ) {
		$image_container        = $this->get_image_container();
		$backup_image_container = $this->get_image_container();

		try {
			/**
			 * Filter: wpseo_add_opengraph_images - Allow developers to add images to the Open Graph tags.
			 *
			 * @param Yoast\WP\SEO\Values\Open_Graph\Images $image_container The current object.
			 */
			\apply_filters( 'wpseo_add_opengraph_images', $image_container );
		} catch ( Error $error ) {
			$image_container = $backup_image_container;
		}

		$this->add_from_indexable( $context->indexable, $image_container );
		$backup_image_container = $image_container;

		try {
			/**
			 * Filter: wpseo_add_opengraph_additional_images - Allows to add additional images to the Open Graph tags.
			 *
			 * @param Yoast\WP\SEO\Values\Open_Graph\Images $image_container The current object.
			 */
			\apply_filters( 'wpseo_add_opengraph_additional_images', $image_container );
		} catch ( Error $error ) {
			$image_container = $backup_image_container;
		}

		$this->add_from_templates( $context, $image_container );
		$this->add_from_default( $image_container );

		return $image_container->get_images();
	}

	/**
	 * Retrieves the images for an author archive indexable.
	 *
	 * This is a custom method to address the case of Author Archives, since they always have an Open Graph image
	 * set in the indexable (even if it is an empty default Gravatar).
	 *
	 * @param Meta_Tags_Context $context The context.
	 *
	 * @return array The images.
	 */
	public function generate_for_author_archive( Meta_Tags_Context $context ) {
		$image_container = $this->get_image_container();

		$this->add_from_templates( $context, $image_container );
		if ( $image_container->has_images() ) {
			return $image_container->get_images();
		}

		return $this->generate( $context );
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
		if ( $indexable->open_graph_image_meta ) {
			$image_container->add_image_by_meta( $indexable->open_graph_image_meta );
			return;
		}

		if ( $indexable->open_graph_image_id ) {
			$image_container->add_image_by_id( $indexable->open_graph_image_id );
			return;
		}

		if ( $indexable->open_graph_image ) {
			$meta_data = [];
			if ( $indexable->open_graph_image_meta && \is_string( $indexable->open_graph_image_meta ) ) {
				$meta_data = \json_decode( $indexable->open_graph_image_meta, true );
			}

			$image_container->add_image(
				\array_merge(
					(array) $meta_data,
					[
						'url' => $indexable->open_graph_image,
					]
				)
			);
		}
	}

	/**
	 * Retrieves the default Open Graph image.
	 *
	 * @param Images $image_container The image container.
	 *
	 * @return void
	 */
	protected function add_from_default( Images $image_container ) {
		if ( $image_container->has_images() ) {
			return;
		}

		$default_image_id = $this->options->get( 'og_default_image_id', '' );
		if ( $default_image_id ) {
			$image_container->add_image_by_id( $default_image_id );

			return;
		}

		$default_image_url = $this->options->get( 'og_default_image', '' );
		if ( $default_image_url ) {
			$image_container->add_image_by_url( $default_image_url );
		}
	}

	/**
	 * Retrieves the default Open Graph image.
	 *
	 * @param Meta_Tags_Context $context         The context.
	 * @param Images            $image_container The image container.
	 *
	 * @return void
	 */
	protected function add_from_templates( Meta_Tags_Context $context, Images $image_container ) {
		if ( $image_container->has_images() ) {
			return;
		}

		if ( $context->presentation->open_graph_image_id ) {
			$image_container->add_image_by_id( $context->presentation->open_graph_image_id );
			return;
		}

		if ( $context->presentation->open_graph_image ) {
			$image_container->add_image_by_url( $context->presentation->open_graph_image );
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
		$image_container = new Images( $this->image, $this->url );
		$image_container->set_helpers( $this->open_graph_image );

		return $image_container;
	}
}
