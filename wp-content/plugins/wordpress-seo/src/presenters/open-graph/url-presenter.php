<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Open Graph URL.
 */
class Url_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:url';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'attribute';

	/**
	 * Run the url content through the `wpseo_opengraph_url` filter.
	 *
	 * @return string The filtered url.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_opengraph_url' - Allow changing the Yoast SEO generated open graph URL.
		 *
		 * @param string                 $url          The open graph URL.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \urldecode( (string) \apply_filters( 'wpseo_opengraph_url', $this->presentation->open_graph_url, $this->presentation ) );
	}
}
