<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Open Graph description.
 */
class Description_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:description';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * Run the Open Graph description through replace vars and the `wpseo_opengraph_desc` filter and sanitization.
	 *
	 * @return string The filtered description.
	 */
	public function get() {
		$meta_og_description = $this->replace_vars( $this->presentation->open_graph_description );

		/**
		 * Filter: 'wpseo_opengraph_desc' - Allow changing the Yoast SEO generated Open Graph description.
		 *
		 * @param string                 $description  The description.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$meta_og_description = \apply_filters( 'wpseo_opengraph_desc', $meta_og_description, $this->presentation );
		$meta_og_description = $this->helpers->string->strip_all_tags( \stripslashes( $meta_og_description ) );
		return \trim( $meta_og_description );
	}
}
