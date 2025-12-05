<?php

namespace Yoast\WP\SEO\Presenters\Open_Graph;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Open Graph site name.
 */
class Site_Name_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'og:site_name';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * Runs the site name through the `wpseo_opengraph_site_name` filter.
	 *
	 * @return string The filtered site_name.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_opengraph_site_name' - Allow changing the Yoast SEO generated Open Graph site name.
		 *
		 * @param string                 $site_name    The site_name.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return (string) \trim( \apply_filters( 'wpseo_opengraph_site_name', $this->presentation->open_graph_site_name, $this->presentation ) );
	}
}
