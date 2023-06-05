<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProceedToCheckoutBlock class.
 */
class ProceedToCheckoutBlock extends AbstractInnerBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'proceed-to-checkout-block';


	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		$this->asset_data_registry->register_page_id( isset( $attributes['checkoutPageId'] ) ? $attributes['checkoutPageId'] : 0 );
	}
}
