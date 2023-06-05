<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductNew class.
 */
class ProductNew extends AbstractProductGrid {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-new';

	/**
	 * Set args specific to this block
	 *
	 * @param array $query_args Query args.
	 */
	protected function set_block_query_args( &$query_args ) {
		$query_args['orderby'] = 'date';
		$query_args['order']   = 'DESC';
	}
}
