<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Seo;

interface Description_Data_Provider_Interface {

	/**
	 * Retrieves the description template.
	 *
	 * @return string The description template.
	 */
	public function get_description_template(): string;

	/**
	 * Determines the date to be displayed in the snippet preview.
	 *
	 * @return string
	 */
	public function get_description_date(): string;
}
