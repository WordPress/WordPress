<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * FeaturedProduct class.
 */
class FeaturedProduct extends FeaturedItem {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'featured-product';

	/**
	 * Returns the featured product.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return \WP_Term|null
	 */
	protected function get_item( $attributes ) {
		$id = absint( $attributes['productId'] ?? 0 );

		$product = wc_get_product( $id );
		if ( ! $product ) {
			return null;
		}

		return $product;
	}

	/**
	 * Returns the name of the featured product.
	 *
	 * @param \WC_Product $product Product object.
	 * @return string
	 */
	protected function get_item_title( $product ) {
		return $product->get_title();
	}

	/**
	 * Returns the featured product image URL.
	 *
	 * @param \WC_Product $product Product object.
	 * @param string      $size    Image size, defaults to 'full'.
	 * @return string
	 */
	protected function get_item_image( $product, $size = 'full' ) {
		$image = '';
		if ( $product->get_image_id() ) {
			$image = wp_get_attachment_image_url( $product->get_image_id(), $size );
		} elseif ( $product->get_parent_id() ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			if ( $parent_product ) {
				$image = wp_get_attachment_image_url( $parent_product->get_image_id(), $size );
			}
		}

		return $image;
	}

	/**
	 * Renders the featured product attributes.
	 *
	 * @param \WC_Product $product Product object.
	 * @param array       $attributes Block attributes. Default empty array.
	 * @return string
	 */
	protected function render_attributes( $product, $attributes ) {
		$title = sprintf(
			'<h2 class="wc-block-featured-product__title">%s</h2>',
			wp_kses_post( $product->get_title() )
		);

		if ( $product->is_type( 'variation' ) ) {
			$title .= sprintf(
				'<h3 class="wc-block-featured-product__variation">%s</h3>',
				wp_kses_post( wc_get_formatted_variation( $product, true, true, false ) )
			);
		}

		$desc_str = sprintf(
			'<div class="wc-block-featured-product__description">%s</div>',
			wc_format_content( wp_kses_post( $product->get_short_description() ? $product->get_short_description() : wc_trim_string( $product->get_description(), 400 ) ) )
		);

		$price_str = sprintf(
			'<div class="wc-block-featured-product__price">%s</div>',
			wp_kses_post( $product->get_price_html() )
		);

		$output = $title;
		if ( $attributes['showDesc'] ) {
			$output .= $desc_str;
		}
		if ( $attributes['showPrice'] ) {
			$output .= $price_str;
		}

		return $output;
	}
}
