<?php

namespace Yoast\WP\SEO\Presenters\Twitter;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Twitter image.
 */
class Image_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'twitter:image';

	/**
	 * The method of escaping to use.
	 *
	 * @var string
	 */
	protected $escaping = 'url';

	/**
	 * Run the Twitter image value through the `wpseo_twitter_image` filter.
	 *
	 * @return string The filtered Twitter image.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_twitter_image' - Allow changing the Twitter Card image.
		 *
		 * @param string                 $twitter_image Image URL string.
		 * @param Indexable_Presentation $presentation  The presentation of an indexable.
		 */
		return (string) \apply_filters( 'wpseo_twitter_image', $this->presentation->twitter_image, $this->presentation );
	}
}
