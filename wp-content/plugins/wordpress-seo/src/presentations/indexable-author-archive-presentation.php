<?php

namespace Yoast\WP\SEO\Presentations;

use Yoast\WP\SEO\Helpers\Author_Archive_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;

/**
 * Class Indexable_Author_Archive_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Author_Archive_Presentation extends Indexable_Presentation {

	use Archive_Adjacent;

	/**
	 * Holds the post type helper instance.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type;

	/**
	 * Holds the author archive helper instance.
	 *
	 * @var Author_Archive_Helper
	 */
	protected $author_archive;

	/**
	 * Indexable_Author_Archive_Presentation constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Post_Type_Helper      $post_type      The post type helper.
	 * @param Author_Archive_Helper $author_archive The author archive helper.
	 */
	public function __construct( Post_Type_Helper $post_type, Author_Archive_Helper $author_archive ) {
		$this->post_type      = $post_type;
		$this->author_archive = $author_archive;
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

		if ( ! $this->permalink ) {
			return '';
		}

		$current_page = $this->pagination->get_current_archive_page_number();
		if ( $current_page > 1 ) {
			return $this->pagination->get_paginated_url( $this->permalink, $current_page );
		}

		return $this->permalink;
	}

	/**
	 * Generates the title.
	 *
	 * @return string The title.
	 */
	public function generate_title() {
		if ( $this->model->title ) {
			return $this->model->title;
		}

		$option_titles_key = 'title-author-wpseo';
		$title             = $this->options->get( $option_titles_key );
		if ( $title ) {
			return $title;
		}

		return $this->options->get_title_default( $option_titles_key );
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

		$option_titles_key = 'metadesc-author-wpseo';
		$description       = $this->options->get( $option_titles_key );
		if ( $description ) {
			return $description;
		}

		return $this->options->get_title_default( $option_titles_key );
	}

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();

		// Global option: "Show author archives in search results".
		if ( $this->options->get( 'noindex-author-wpseo', false ) ) {
			$robots['index'] = 'noindex';
			return $this->filter_robots( $robots );
		}

		$current_author = \get_userdata( $this->model->object_id );

		// Safety check. The call to `get_user_data` could return false (called in `get_queried_object`).
		if ( $current_author === false ) {
			$robots['index'] = 'noindex';
			return $this->filter_robots( $robots );
		}

		$author_archive_post_types = $this->author_archive->get_author_archive_post_types();

		// Global option: "Show archives for authors without posts in search results".
		if ( $this->options->get( 'noindex-author-noposts-wpseo', false ) && $this->user->count_posts( $current_author->ID, $author_archive_post_types ) === 0 ) {
			$robots['index'] = 'noindex';
			return $this->filter_robots( $robots );
		}

		// User option: "Do not allow search engines to show this author's archives in search results".
		if ( $this->user->get_meta( $current_author->ID, 'wpseo_noindex_author', true ) === 'on' ) {
			$robots['index'] = 'noindex';
			return $this->filter_robots( $robots );
		}

		return $this->filter_robots( $robots );
	}

	/**
	 * Generates the Open Graph type.
	 *
	 * @return string The Open Graph type.
	 */
	public function generate_open_graph_type() {
		return 'profile';
	}

	/**
	 * Generates the open graph images.
	 *
	 * @return array The open graph images.
	 */
	public function generate_open_graph_images() {
		if ( $this->context->open_graph_enabled === false ) {
			return [];
		}

		return $this->open_graph_image_generator->generate_for_author_archive( $this->context );
	}

	/**
	 * Generates the source.
	 *
	 * @return array The source.
	 */
	public function generate_source() {
		return [ 'post_author' => $this->model->object_id ];
	}
}
