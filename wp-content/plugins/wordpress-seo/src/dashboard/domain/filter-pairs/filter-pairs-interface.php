<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Filter_Pairs;

/**
 * This interface describes a Filter Pair implementation.
 */
interface Filter_Pairs_Interface {

	/**
	 * Gets the filtering taxonomy.
	 *
	 * @return string
	 */
	public function get_filtering_taxonomy(): string;

	/**
	 * Gets the filtered content type.
	 *
	 * @return string
	 */
	public function get_filtered_content_type(): string;
}
