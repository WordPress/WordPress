<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductOnSale class.
 */
class ProductOnSale extends AbstractProductGrid {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-on-sale';

	/**
	 * Set args specific to this block
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_block_query_args( &$query_args ) {
		$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
	}
	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_attributes() {
		return array_merge(
			parent::get_block_type_attributes(),
			array(
				'className' => $this->get_schema_string(),
				'orderby'   => $this->get_schema_orderby(),
			)
		);
	}
}
