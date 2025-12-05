<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Seo;

interface Title_Data_Provider_Interface {

	/**
	 * Retrieves the title template.
	 *
	 * @param bool $fallback Whether to return the hardcoded fallback if the template value is empty. Default true.
	 *
	 * @return string The title template.
	 */
	public function get_title_template( bool $fallback = true ): string;
}
