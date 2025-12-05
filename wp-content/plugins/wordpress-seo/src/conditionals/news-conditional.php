<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is only met when news SEO is activated.
 */
class News_Conditional implements Conditional {

	/**
	 * Returns whether or not this conditional is met.
	 *
	 * @return bool Whether or not the conditional is met.
	 */
	public function is_met() {
		return \defined( 'WPSEO_NEWS_VERSION' );
	}
}
