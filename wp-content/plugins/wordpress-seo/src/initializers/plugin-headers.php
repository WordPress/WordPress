<?php

namespace Yoast\WP\SEO\Initializers;

use Yoast\WP\SEO\Conditionals\No_Conditionals;

/**
 * Adds custom headers to the list of plugin headers to read.
 */
class Plugin_Headers implements Initializer_Interface {

	use No_Conditionals;

	/**
	 * Hooks into the list of the plugin headers.
	 *
	 * @return void
	 */
	public function initialize() {
		\add_filter( 'extra_plugin_headers', [ $this, 'add_requires_yoast_seo_header' ] );
	}

	/**
	 * Add the `Requires Yoast SEO` header to the list of headers.
	 *
	 * @param array<string> $headers The headers.
	 *
	 * @return array<string> The updated headers.
	 */
	public function add_requires_yoast_seo_header( $headers ) {
		$headers[] = 'Requires Yoast SEO';
		return $headers;
	}
}
