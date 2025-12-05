<?php

namespace Yoast\WP\SEO\Presenters;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;

/**
 * Presenter class for the canonical.
 */
class Canonical_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'canonical';

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::LINK_REL_HREF;

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'url';

	/**
	 * Run the canonical content through the `wpseo_canonical` filter.
	 *
	 * @return string The filtered canonical.
	 */
	public function get() {
		if ( \in_array( 'noindex', $this->presentation->robots, true ) ) {
			return '';
		}

		/**
		 * Filter: 'wpseo_canonical' - Allow filtering of the canonical URL put out by Yoast SEO.
		 *
		 * @param string                 $canonical    The canonical URL.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		return \urldecode( \trim( (string) \apply_filters( 'wpseo_canonical', $this->presentation->canonical, $this->presentation ) ) );
	}
}
