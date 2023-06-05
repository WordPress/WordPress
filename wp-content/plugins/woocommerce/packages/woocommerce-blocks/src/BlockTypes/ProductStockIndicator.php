<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductStockIndicator class.
 */
class ProductStockIndicator extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-stock-indicator';

	/**
	 * API version name.
	 *
	 * @var string
	 */
	protected $api_version = '2';

	/**
	 * Get block supports. Shared with the frontend.
	 * IMPORTANT: If you change anything here, make sure to update the JS file too.
	 *
	 * @return array
	 */
	protected function get_block_type_supports() {
		return array(
			'color'                  =>
			array(
				'link'       => false,
				'background' => false,
				'text'       => true,
			),
			'typography'             =>
			array(
				'fontSize' => true,
			),
			'__experimentalSelector' => '.wc-block-components-product-stock-indicator',
		);
	}

	/**
	 * Register script and style assets for the block type before it is registered.
	 *
	 * This registers the scripts; it does not enqueue them.
	 */
	protected function register_block_type_assets() {
		return null;
	}

	/**
	 * Register the context.
	 */
	protected function get_block_type_uses_context() {
		return [ 'query', 'queryId', 'postId' ];
	}

	/**
	 * Get stock text based on stock. For example:
	 * - In stock
	 * - Out of stock
	 * - Available on backorder
	 * - 2 left in stock
	 *
	 * @param [bool]     $is_in_stock Whether the product is in stock.
	 * @param [bool]     $is_low_stock Whether the product is low in stock.
	 * @param [int|null] $low_stock_amount The amount of stock that is considered low.
	 * @param [bool]     $is_on_backorder Whether the product is on backorder.
	 * @return string Stock text.
	 */
	protected static function getTextBasedOnStock( $is_in_stock, $is_low_stock, $low_stock_amount, $is_on_backorder ) {
		if ( $is_low_stock ) {
			return sprintf(
				/* translators: %d is number of items in stock for product */
				__( '%d left in stock', 'woocommerce' ),
				$low_stock_amount
			);
		} elseif ( $is_on_backorder ) {
			return __( 'Available on backorder', 'woocommerce' );
		} elseif ( $is_in_stock ) {
			return __( 'In stock', 'woocommerce' );
		} else {
			return __( 'Out of stock', 'woocommerce' );
		}
	}



	/**
	 * Include and render the block.
	 *
	 * @param array    $attributes Block attributes. Default empty array.
	 * @param string   $content    Block content. Default empty string.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		if ( ! empty( $content ) ) {
			parent::register_block_type_assets();
			$this->register_chunk_translations( [ $this->block_name ] );
			return $content;
		}

		$post_id         = $block->context['postId'];
		$product         = wc_get_product( $post_id );
		$is_in_stock     = $product->is_in_stock();
		$is_on_backorder = $product->is_on_backorder();

		$low_stock_amount = $product->get_low_stock_amount();
		$total_stock      = $product->get_stock_quantity();
		$is_low_stock     = $low_stock_amount && $total_stock <= $low_stock_amount;

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );

		$classnames  = isset( $classes_and_styles['classes'] ) ? ' ' . $classes_and_styles['classes'] . ' ' : '';
		$classnames .= isset( $attributes['className'] ) ? ' ' . $attributes['className'] . ' ' : '';
		$classnames .= ! $is_in_stock ? ' wc-block-components-product-stock-indicator--out-of-stock ' : '';
		$classnames .= $is_in_stock ? ' wc-block-components-product-stock-indicator--in-stock ' : '';
		$classnames .= $is_low_stock ? ' wc-block-components-product-stock-indicator--low-stock ' : '';
		$classnames .= $is_on_backorder ? ' wc-block-components-product-stock-indicator--available-on-backorder ' : '';

		$output  = '';
		$output .= '<div class="wc-block-components-product-stock-indicator ' . esc_attr( $classnames ) . '"';
		$output .= isset( $classes_and_styles['styles'] ) ? ' style="' . esc_attr( $classes_and_styles['styles'] ) . '"' : '';
		$output .= '>';
		$output .= wp_kses_post( self::getTextBasedOnStock( $is_in_stock, $is_low_stock, $low_stock_amount, $is_on_backorder ) );
		$output .= '</div>';

		return $output;

	}
}
