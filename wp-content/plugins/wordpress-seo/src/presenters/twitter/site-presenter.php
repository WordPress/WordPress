<?php

namespace Yoast\WP\SEO\Presenters\Twitter;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Twitter site tag.
 */
class Site_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'twitter:site';

	/**
	 * Run the Twitter site through the `wpseo_twitter_site` filter.
	 *
	 * @return string The filtered Twitter site.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_twitter_site' - Allow changing the Twitter site account as output in the Twitter card by Yoast SEO.
		 *
		 * @param string                 $twitter_site Twitter site account string.
		 * @param Indexable_Presentation $presentation The presentation of an indexable.
		 */
		$twitter_site = \apply_filters( 'wpseo_twitter_site', $this->presentation->twitter_site, $this->presentation );
		$twitter_site = $this->get_twitter_id( $twitter_site );

		if ( ! \is_string( $twitter_site ) || $twitter_site === '' ) {
			return '';
		}

		return '@' . $twitter_site;
	}

	/**
	 * Checks if the given id is actually an id or a url and if url, distills the id from it.
	 *
	 * Solves issues with filters returning urls and theme's/other plugins also adding a user meta
	 * twitter field which expects url rather than an id (which is what we expect).
	 *
	 * @param string $id Twitter ID or url.
	 *
	 * @return string|bool Twitter ID or false if it failed to get a valid Twitter ID.
	 */
	private function get_twitter_id( $id ) {
		if ( \preg_match( '`([A-Za-z0-9_]{1,25})$`', $id, $match ) ) {
			return $match[1];
		}

		return false;
	}
}
