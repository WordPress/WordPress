<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * HandpickedProducts class.
 */
class HandpickedProducts extends AbstractProductGrid {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'handpicked-products';

	/**
	 * Set args specific to this block
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_block_query_args( &$query_args ) {
		$ids = array_map( 'absint', $this->attributes['products'] );

		$query_args['post__in']       = $ids;
		$query_args['posts_per_page'] = count( $ids );
	}

	/**
	 * Set visibility query args. Handpicked products will show hidden products if chosen.
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_visibility_query_args( &$query_args ) {
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$product_visibility_terms  = wc_get_product_visibility_term_ids();
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => array( $product_visibility_terms['outofstock'] ),
				'operator' => 'NOT IN',
			);
		}
	}

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_attributes() {
		return array(
			'align'             => $this->get_schema_align(),
			'alignButtons'      => $this->get_schema_boolean( false ),
			'className'         => $this->get_schema_string(),
			'columns'           => $this->get_schema_number( wc_get_theme_support( 'product_blocks::default_columns', 3 ) ),
			'orderby'           => $this->get_schema_orderby(),
			'products'          => $this->get_schema_list_ids(),
			'contentVisibility' => $this->get_schema_content_visibility(),
			'isPreview'         => $this->get_schema_boolean( false ),
		);
	}
}
