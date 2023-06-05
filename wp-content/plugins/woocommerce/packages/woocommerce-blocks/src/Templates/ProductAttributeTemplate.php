<?php

namespace Automattic\WooCommerce\Blocks\Templates;

/**
 * ProductAttributeTemplate class.
 *
 * @internal
 */
class ProductAttributeTemplate {
	const SLUG = 'taxonomy-product_attribute';

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
		add_filter( 'taxonomy_template_hierarchy', array( $this, 'update_taxonomy_template_hierarchy' ), 1, 3 );
	}

	/**
	 * Renders the Product by Attribute template for product attributes taxonomy pages.
	 *
	 * @param array $templates Templates that match the product attributes taxonomy.
	 */
	public function update_taxonomy_template_hierarchy( $templates ) {
		$queried_object = get_queried_object();
		if ( taxonomy_is_product_attribute( $queried_object->taxonomy ) && wc_current_theme_is_fse_theme() ) {
			array_splice( $templates, count( $templates ) - 1, 0, self::SLUG );
		}

		return $templates;
	}
}
