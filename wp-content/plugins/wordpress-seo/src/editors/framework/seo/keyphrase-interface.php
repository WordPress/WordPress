<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Seo;

interface Keyphrase_Interface {

	/**
	 * Counts the number of given keyphrase used for other posts other than the given post_id.
	 *
	 * @return array<string> The keyphrase and the associated posts that use it.
	 */
	public function get_focus_keyphrase_usage(): array;
}
