<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Filter_Pairs;

/**
 * This class describes the product category filter pair.
 */
class Product_Category_Filter_Pair implements Filter_Pairs_Interface {

	/**
	 * Gets the filtering taxonomy.
	 *
	 * @return string The filtering taxonomy.
	 */
	public function get_filtering_taxonomy(): string {
		return 'product_cat';
	}

	/**
	 * Gets the filtered content type.
	 *
	 * @return string The filtered content type.
	 */
	public function get_filtered_content_type(): string {
		return 'product';
	}
}
