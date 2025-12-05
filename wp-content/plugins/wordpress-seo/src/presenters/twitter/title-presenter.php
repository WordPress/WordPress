<?php

namespace Yoast\WP\SEO\Presenters\Twitter;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Twitter title.
 */
class Title_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'twitter:title';

	/**
	 * Run the Twitter title through replace vars and the `wpseo_twitter_title` filter.
	 *
	 * @return string The filtered Twitter title.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_twitter_title' - Allow changing the Twitter title.
		 *
		 * @param string                 $twitter_title The Twitter title.
		 * @param Indexable_Presentation $presentation  The presentation of an indexable.
		 */
		return \trim( \apply_filters( 'wpseo_twitter_title', $this->replace_vars( $this->presentation->twitter_title ), $this->presentation ) );
	}
}
