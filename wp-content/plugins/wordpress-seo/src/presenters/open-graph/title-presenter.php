<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Open Graph title.
 */
class Title_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:title';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * Run the title content through replace vars, the `wpseo_opengraph_title` filter and sanitization.
	 *
	 * @return string The filtered title.
	 */
	public function get() {
		$title = $this->replace_vars( $this->presentation->open_graph_title );

		/**
		 * Filter: 'wpseo_opengraph_title' - Allow changing the Yoast SEO generated title.
		 *
		 * @param string                 $title        The title.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$title = \trim( (string) \apply_filters( 'wpseo_opengraph_title', $title, $this->presentation ) );
		return $this->helpers->string->strip_all_tags( $title );
	}
}
