<?php

namespace Yoast\WP\SEO\Presentations;

use Yoast\WP\SEO\Helpers\Pagination_Helper;

/**
 * Class Indexable_Date_Archive_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Date_Archive_Presentation extends Indexable_Presentation {

	/**
	 * Holds the Pagination_Helper instance.
	 *
	 * @var Pagination_Helper
	 */
	protected $pagination;

	/**
	 * Indexable_Date_Archive_Presentation constructor.
	 *
	 * @param Pagination_Helper $pagination The pagination helper.
	 */
	public function __construct( Pagination_Helper $pagination ) {
		$this->pagination = $pagination;
	}

	/**
	 * Generates the canonical.
	 *
	 * @return string The canonical.
	 */
	public function generate_canonical() {
		$canonical = $this->current_page->get_date_archive_permalink();

		$current_page = $this->pagination->get_current_archive_page_number();
		if ( $current_page > 1 ) {
			return $this->pagination->get_paginated_url( $canonical, $current_page );
		}

		return $canonical;
	}

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();

		if ( $this->options->get( 'noindex-archive-wpseo', false ) ) {
			$robots['index'] = 'noindex';
		}

		return $this->filter_robots( $robots );
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

		return $this->options->get_title_default( 'title-archive-wpseo' );
	}

	/**
	 * Generates the rel prev.
	 *
	 * @return string The rel prev value.
	 */
	public function generate_rel_prev() {
		if ( $this->pagination->is_rel_adjacent_disabled() ) {
			return '';
		}

		$current_page = \max( 1, $this->pagination->get_current_archive_page_number() );
		// Check if there is a previous page.
		if ( $current_page === 1 ) {
			return '';
		}
		// Check if the previous page is the first page.
		if ( $current_page === 2 ) {
			return $this->current_page->get_date_archive_permalink();
		}

		return $this->pagination->get_paginated_url( $this->current_page->get_date_archive_permalink(), ( $current_page - 1 ) );
	}

	/**
	 * Generates the rel next.
	 *
	 * @return string The rel prev next.
	 */
	public function generate_rel_next() {
		if ( $this->pagination->is_rel_adjacent_disabled() ) {
			return '';
		}

		$current_page = \max( 1, $this->pagination->get_current_archive_page_number() );
		if ( $this->pagination->get_number_of_archive_pages() <= $current_page ) {
			return '';
		}

		return $this->pagination->get_paginated_url( $this->current_page->get_date_archive_permalink(), ( $current_page + 1 ) );
	}

	/**
	 * Generates the open graph url.
	 *
	 * @return string The open graph url.
	 */
	public function generate_open_graph_url() {
		return $this->current_page->get_date_archive_permalink();
	}
}
