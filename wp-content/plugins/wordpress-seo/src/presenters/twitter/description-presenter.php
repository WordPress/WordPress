<?php

namespace Yoast\WP\SEO\Presenters\Twitter;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Twitter description.
 */
class Description_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'twitter:description';

	/**
	 * Run the Twitter description through replace vars and the `wpseo_twitter_description` filter.
	 *
	 * @return string The filtered Twitter description.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_twitter_description' - Allow changing the Twitter description as output in the Twitter card by Yoast SEO.
		 *
		 * @param string                 $twitter_description The description string.
		 * @param Indexable_Presentation $presentation        The presentation of an indexable.
		 */
		return \apply_filters( 'wpseo_twitter_description', $this->replace_vars( $this->presentation->twitter_description ), $this->presentation );
	}
}
