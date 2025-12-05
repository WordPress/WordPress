<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Domain\Seo;

/**
 * This class describes the SEO data interface.
 */
interface Seo_Plugin_Data_Interface {

	/**
	 * Returns the data as an array format.
	 *
	 * @return array<string>
	 */
	public function to_array(): array;

	/**
	 * Returns the data as an array format meant for legacy use.
	 *
	 * @return array<string>
	 */
	public function to_legacy_array(): array;
}
