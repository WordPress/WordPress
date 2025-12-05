<?php

namespace Yoast\WP\SEO\Presentations;

use Yoast\WP\SEO\Helpers\Pagination_Helper;

/**
 * Class Indexable_Static_Posts_Page_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Static_Posts_Page_Presentation extends Indexable_Post_Type_Presentation {

	use Archive_Adjacent;

	/**
	 * The pagination helper.
	 *
	 * @var Pagination_Helper
	 */
	protected $pagination;

	/**
	 * Generates the canonical.
	 *
	 * @return string The canonical.
	 */
	public function generate_canonical() {
		if ( $this->model->canonical ) {
			return $this->model->canonical;
		}

		$current_page = $this->pagination->get_current_archive_page_number();

		if ( $current_page > 1 ) {
			return $this->pagination->get_paginated_url( $this->permalink, $current_page );
		}

		return $this->permalink;
	}

	/**
	 * Generates the Open Graph URL.
	 *
	 * @return string The Open Graph URL.
	 */
	public function generate_open_graph_url() {
		return $this->permalink;
	}
}
