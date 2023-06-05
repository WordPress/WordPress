<?php

namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * ProductReviews class.
 */
class ProductReviews extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-reviews';

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
		ob_start();

		rewind_posts();
		while ( have_posts() ) {
			the_post();
			comments_template();
		}

		$reviews = ob_get_clean();

		$classname = $attributes['className'] ?? '';

		return sprintf(
			'<div class="wp-block-woocommerce-product-reviews %1$s">
				%2$s
			</div>',
			esc_attr( $classname ),
			$reviews
		);
	}
}
