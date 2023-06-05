<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductDetails class.
 */
class ProductDetails extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-details';

	/**
	 * It isn't necessary register block assets because it is a server side block.
	 */
	protected function register_block_type_assets() {
		return null;
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content Block content.
	 * @param WP_Block $block Block instance.
	 *
	 * @return string Rendered block output.
	 */
	protected function render( $attributes, $content, $block ) {
		$tabs = $this->render_tabs();

		$classname = $attributes['className'] ?? '';

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );

		return sprintf(
			'<div class="wp-block-woocommerce-product-details %1$s %2$s">
				%3$s
			</div>',
			esc_attr( $classes_and_styles['classes'] ),
			esc_attr( $classname ),
			$tabs
		);
	}

	/**
	 * Gets the tabs with their content to be rendered by the block.
	 *
	 * @return string The tabs html to be rendered by the block
	 */
	protected function render_tabs() {
		ob_start();

		while ( have_posts() ) {
			the_post();
			woocommerce_output_product_data_tabs();
		}

		$tabs = ob_get_clean();

		return $tabs;
	}
}
