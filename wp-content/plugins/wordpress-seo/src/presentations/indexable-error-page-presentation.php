<?php

namespace Yoast\WP\SEO\Presentations;

/**
 * Class Indexable_Error_Page_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Error_Page_Presentation extends Indexable_Presentation {

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();

		$robots['index'] = 'noindex';

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

		return $this->options->get_title_default( 'title-404-wpseo' );
	}
}
