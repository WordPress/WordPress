<?php
namespace Automattic\WooCommerce\Blocks\Templates;

/**
 * ProductSearchResultsTemplate class.
 *
 * @internal
 */
class ProductSearchResultsTemplate {

	const SLUG = 'product-search-results';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization method.
	 */
	protected function init() {
		add_filter( 'search_template_hierarchy', array( $this, 'update_search_template_hierarchy' ), 10, 3 );
	}

	/**
	 * When the search is for products and a block theme is active, render the Product Search Template.
	 *
	 * @param array $templates Templates that match the search hierarchy.
	 */
	public function update_search_template_hierarchy( $templates ) {
		if ( ( is_search() && is_post_type_archive( 'product' ) ) && wc_current_theme_is_fse_theme() ) {
			array_unshift( $templates, self::SLUG );
		}
		return $templates;
	}
}
