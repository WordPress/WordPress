<?php

namespace Yoast\WP\SEO\Presentations;

use Yoast\WP\SEO\Helpers\Date_Helper;
use Yoast\WP\SEO\Helpers\Pagination_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;

/**
 * Class Indexable_Post_Type_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Post_Type_Presentation extends Indexable_Presentation {

	/**
	 * Holds the Post_Type_Helper instance.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type;

	/**
	 * Holds the Date_Helper instance.
	 *
	 * @var Date_Helper
	 */
	protected $date;

	/**
	 * Holds the Pagination_Helper instance.
	 *
	 * @var Pagination_Helper
	 */
	protected $pagination;

	/**
	 * Holds the Post_Helper instance.
	 *
	 * @var Post_Helper
	 */
	protected $post;

	/**
	 * Indexable_Post_Type_Presentation constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Post_Type_Helper  $post_type  The post type helper.
	 * @param Date_Helper       $date       The date helper.
	 * @param Pagination_Helper $pagination The pagination helper.
	 * @param Post_Helper       $post       The post helper.
	 */
	public function __construct(
		Post_Type_Helper $post_type,
		Date_Helper $date,
		Pagination_Helper $pagination,
		Post_Helper $post
	) {
		$this->post_type  = $post_type;
		$this->date       = $date;
		$this->pagination = $pagination;
		$this->post       = $post;
	}

	/**
	 * Generates the canonical.
	 *
	 * @return string The canonical.
	 */
	public function generate_canonical() {
		if ( $this->model->canonical ) {
			return $this->model->canonical;
		}

		$permalink = $this->permalink;

		// Fix paginated pages canonical, but only if the page is truly paginated.
		$current_page = $this->pagination->get_current_post_page_number();
		if ( $current_page > 1 ) {
			$number_of_pages = $this->model->number_of_pages;
			if ( $number_of_pages && $current_page <= $number_of_pages ) {
				$permalink = $this->get_paginated_url( $permalink, $current_page );
			}
		}

		return $this->url->ensure_absolute_url( $permalink );
	}

	/**
	 * Generates the rel prev.
	 *
	 * @return string The rel prev value.
	 */
	public function generate_rel_prev() {
		if ( $this->model->number_of_pages === null ) {
			return '';
		}

		if ( $this->pagination->is_rel_adjacent_disabled() ) {
			return '';
		}

		$current_page = \max( 1, $this->pagination->get_current_post_page_number() );

		// Check if there is a previous page.
		if ( $current_page < 2 ) {
			return '';
		}

		// Check if the previous page is the first page.
		if ( $current_page === 2 ) {
			return $this->model->permalink;
		}

		return $this->get_paginated_url( $this->model->permalink, ( $current_page - 1 ) );
	}

	/**
	 * Generates the rel next.
	 *
	 * @return string The rel prev next.
	 */
	public function generate_rel_next() {
		if ( $this->model->number_of_pages === null ) {
			return '';
		}

		if ( $this->pagination->is_rel_adjacent_disabled() ) {
			return '';
		}

		$current_page = \max( 1, $this->pagination->get_current_post_page_number() );
		if ( $this->model->number_of_pages <= $current_page ) {
			return '';
		}

		return $this->get_paginated_url( $this->model->permalink, ( $current_page + 1 ) );
	}

	/**
	 * Generates the open graph title.
	 *
	 * @return string The open graph title.
	 */
	public function generate_title() {
		if ( $this->model->title ) {
			return $this->model->title;
		}

		// Get SEO title as entered in Search appearance.
		$post_type = $this->model->object_sub_type;
		$title     = $this->options->get( 'title-' . $this->model->object_sub_type );
		if ( $title ) {
			return $title;
		}

		// Get installation default title.
		return $this->options->get_title_default( 'title-' . $post_type );
	}

	/**
	 * Generates the meta description.
	 *
	 * @return string The meta description.
	 */
	public function generate_meta_description() {
		if ( $this->model->description ) {
			return $this->model->description;
		}

		return $this->options->get( 'metadesc-' . $this->model->object_sub_type );
	}

	/**
	 * Generates the open graph description.
	 *
	 * @return string The open graph description.
	 */
	public function generate_open_graph_description() {
		if ( $this->model->open_graph_description ) {
			$open_graph_description = $this->model->open_graph_description;
		}

		if ( empty( $open_graph_description ) ) {
			// The helper applies a filter, but we don't have a default value at this stage so we pass an empty string.
			$open_graph_description = $this->values_helper->get_open_graph_description( '', $this->model->object_type, $this->model->object_sub_type );
		}

		if ( empty( $open_graph_description ) ) {
			$open_graph_description = $this->meta_description;
		}

		if ( empty( $open_graph_description ) ) {
			$open_graph_description = $this->post->get_the_excerpt( $this->model->object_id );
			$open_graph_description = $this->post->strip_shortcodes( $open_graph_description );
		}

		return $open_graph_description;
	}

	/**
	 * Generates the open graph images.
	 *
	 * @return array The open graph images.
	 */
	public function generate_open_graph_images() {
		if ( \post_password_required() ) {
			return [];
		}

		return parent::generate_open_graph_images();
	}

	/**
	 * Generates the Open Graph type.
	 *
	 * @return string The Open Graph type.
	 */
	public function generate_open_graph_type() {
		return 'article';
	}

	/**
	 * Generates the open graph article author.
	 *
	 * @return string The open graph article author.
	 */
	public function generate_open_graph_article_author() {
		if ( $this->model->object_sub_type !== 'post' ) {
			return '';
		}

		$post = $this->source;

		$open_graph_article_author = $this->user->get_the_author_meta( 'facebook', $post->post_author );

		if ( $open_graph_article_author ) {
			return $open_graph_article_author;
		}

		return '';
	}

	/**
	 * Generates the open graph article publisher.
	 *
	 * @return string The open graph article publisher.
	 */
	public function generate_open_graph_article_publisher() {
		$open_graph_article_publisher = $this->context->open_graph_publisher;

		if ( $open_graph_article_publisher ) {
			return $open_graph_article_publisher;
		}

		return '';
	}

	/**
	 * Generates the open graph article published time.
	 *
	 * @return string The open graph article published time.
	 */
	public function generate_open_graph_article_published_time() {
		if ( $this->model->object_sub_type !== 'post' ) {
			/**
			 * Filter: 'wpseo_opengraph_show_publish_date' - Allow showing publication date for other post types.
			 *
			 * @param bool   $show      Whether or not to show publish date.
			 * @param string $post_type The current URL's post type.
			 */
			if ( ! \apply_filters( 'wpseo_opengraph_show_publish_date', false, $this->post->get_post_type( $this->source ) ) ) {
				return '';
			}
		}

		return $this->date->format( $this->source->post_date_gmt );
	}

	/**
	 * Generates the open graph article modified time.
	 *
	 * @return string The open graph article modified time.
	 */
	public function generate_open_graph_article_modified_time() {
		if ( \strtotime( $this->source->post_modified_gmt ) > \strtotime( $this->source->post_date_gmt ) ) {
			return $this->date->format( $this->source->post_modified_gmt );
		}

		return '';
	}

	/**
	 * Generates the source.
	 *
	 * @return array The source.
	 */
	public function generate_source() {
		return \get_post( $this->model->object_id );
	}

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();
		$robots = \array_merge(
			$robots,
			[
				'imageindex' => ( $this->model->is_robots_noimageindex === true ) ? 'noimageindex' : null,
				'archive'    => ( $this->model->is_robots_noarchive === true ) ? 'noarchive' : null,
				'snippet'    => ( $this->model->is_robots_nosnippet === true ) ? 'nosnippet' : null,
			]
		);

		// No snippet means max snippet can be omitted.
		if ( $this->model->is_robots_nosnippet === true ) {
			$robots['max-snippet'] = null;
		}

		// No image index means max image preview can be omitted.
		if ( $this->model->is_robots_noimageindex === true ) {
			$robots['max-image-preview'] = null;
		}

		// When the post specific index is not set, look to the post status and default of the post type.
		if ( $this->model->is_robots_noindex === null ) {
			$post_status_private = \get_post_status( $this->model->object_id ) === 'private';
			$post_type_noindex   = ! $this->post_type->is_indexable( $this->model->object_sub_type );

			if ( $post_status_private || $post_type_noindex ) {
				$robots['index'] = 'noindex';
			}
		}

		return $this->filter_robots( $robots );
	}

	/**
	 * Generates the Twitter description.
	 *
	 * @return string The Twitter description.
	 */
	public function generate_twitter_description() {
		$twitter_description = parent::generate_twitter_description();

		if ( $twitter_description ) {
			return $twitter_description;
		}

		if ( $this->open_graph_description && $this->context->open_graph_enabled === true ) {
			return '';
		}

		return $this->post->get_the_excerpt( $this->model->object_id );
	}

	/**
	 * Generates the Twitter image.
	 *
	 * @return string The Twitter image.
	 */
	public function generate_twitter_image() {
		if ( \post_password_required() ) {
			return '';
		}

		return parent::generate_twitter_image();
	}

	/**
	 * Generates the Twitter creator.
	 *
	 * @return string The Twitter creator.
	 */
	public function generate_twitter_creator() {
		if ( $this->model->object_sub_type !== 'post' ) {
			return '';
		}

		$twitter_creator = \ltrim( \trim( \get_the_author_meta( 'twitter', $this->source->post_author ) ), '@' );

		/**
		 * Filter: 'wpseo_twitter_creator_account' - Allow changing the X account as output in the X card by Yoast SEO.
		 *
		 * @param string $twitter The twitter account name string.
		 */
		$twitter_creator = \apply_filters( 'wpseo_twitter_creator_account', $twitter_creator );

		if ( \is_string( $twitter_creator ) && $twitter_creator !== '' ) {
			return '@' . $twitter_creator;
		}

		$site_twitter = $this->options->get( 'twitter_site', '' );
		if ( \is_string( $site_twitter ) && $site_twitter !== '' ) {
			return '@' . $site_twitter;
		}

		return '';
	}

	/**
	 * Wraps the get_paginated_url pagination helper method.
	 *
	 * @codeCoverageIgnore A wrapper method.
	 *
	 * @param string $url  The un-paginated URL of the current archive.
	 * @param string $page The page number to add on to $url for the $link tag.
	 *
	 * @return string The paginated URL.
	 */
	protected function get_paginated_url( $url, $page ) {
		return $this->pagination->get_paginated_url( $url, $page, false );
	}
}
