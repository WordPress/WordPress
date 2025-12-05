<?php

namespace Yoast\WP\SEO\Presenters;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Presenter class for the document title.
 */
class Title_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'title';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = '<title>%s</title>';

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'html';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		// This ensures backwards compatibility with other plugins using this filter as well.
		\add_filter( 'pre_get_document_title', [ $this, 'get_title' ], 15 );
		$title = \wp_get_document_title();
		\remove_filter( 'pre_get_document_title', [ $this, 'get_title' ], 15 );
		return $title;
	}

	/**
	 * Returns a tag in the head.
	 *
	 * @return string The tag.
	 */
	public function present() {
		$value = $this->get();

		if ( \is_string( $value ) && $value !== '' ) {
			return \sprintf( $this->tag_format, $this->escape_value( $value ) );
		}

		return '';
	}

	/**
	 * Returns the presentation title.
	 *
	 * @return string The title.
	 */
	public function get_title() {
		$title = $this->replace_vars( $this->presentation->title );

		/**
		 * Filter: 'wpseo_title' - Allow changing the Yoast SEO generated title.
		 *
		 * @param string                 $title        The title.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$title = \apply_filters( 'wpseo_title', $title, $this->presentation );
		$title = $this->helpers->string->strip_all_tags( $title );
		return \trim( $title );
	}
}
