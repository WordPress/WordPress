<?php

namespace Yoast\WP\SEO\Presenters\Webmaster;

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Presenter class for the Ahrefs verification setting.
 */
class Ahrefs_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The tag key name.
	 *
	 * @var string
	 */
	protected $key = 'ahrefs-site-verification';

	/**
	 * Retrieves the Ahrefs site verification value from the settings.
	 *
	 * @return string The Ahrefs site verification value.
	 */
	public function get() {
		return $this->helpers->options->get( 'ahrefsverify', '' );
	}
}
