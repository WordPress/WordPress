<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * AttributeFilter class.
 */
class StockFilter extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name        = 'stock-filter';
	const STOCK_STATUS_QUERY_VAR = 'filter_stock_status';

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $stock_statuses  Any stock statuses that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $stock_statuses = [] ) {
		parent::enqueue_data( $stock_statuses );
		$this->asset_data_registry->add( 'stockStatusOptions', wc_get_product_stock_status_options(), true );
		$this->asset_data_registry->add( 'hideOutOfStockItems', 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ), true );

	}

	/**
	 * Get Stock status query variables values.
	 */
	public static function get_stock_status_query_var_values() {
		return array_keys( wc_get_product_stock_status_options() );
	}
}
