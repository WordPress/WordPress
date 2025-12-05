<?php

namespace Yoast\WP\SEO\Presentations;

/**
 * Class Indexable_Post_Type_Archive_Presentation.
 *
 * Presentation object for indexables.
 */
class Indexable_Post_Type_Archive_Presentation extends Indexable_Presentation {

	use Archive_Adjacent;

	/**
	 * Generates the canonical.
	 *
	 * @return string The canonical.
	 */
	public function generate_canonical() {
		$permalink = $this->permalink;
		if ( ! $permalink ) {
			return '';
		}

		$current_page = $this->pagination->get_current_archive_page_number();
		if ( $current_page > 1 ) {
			return $this->pagination->get_paginated_url( $permalink, $current_page );
		}

		return $permalink;
	}

	/**
	 * Generates the robots value.
	 *
	 * @return array The robots value.
	 */
	public function generate_robots() {
		$robots = $this->get_base_robots();

		if ( $this->options->get( 'noindex-ptarchive-' . $this->model->object_sub_type, false ) ) {
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

		$post_type = $this->model->object_sub_type;
		$title     = $this->options->get_title_default( 'title-ptarchive-' . $post_type );

		return $title;
	}

	/**
	 * Generates the source.
	 *
	 * @return array The source.
	 */
	public function generate_source() {
		return [ 'post_type' => $this->model->object_sub_type ];
	}
}
