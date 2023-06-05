<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductButton class.
 */
class ProductButton extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-button';

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
				'background'                      => true,
				'link'                            => false,
				'text'                            => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalBorder'   =>
			array(
				'radius'                          => true,
				'__experimentalSkipSerialization' => true,
			),
			'typography'             =>
			array(
				'fontSize'                        => true,
				'__experimentalFontWeight'        => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalSelector' => '.wp-block-button.wc-block-components-product-button .wc-block-components-product-button__button',
		);
	}

	/**
	 * It is necessary to register and enqueue assets during the render phase because we want to load assets only if the block has the content.
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

		$post_id = $block->context['postId'];
		$product = wc_get_product( $post_id );

		if ( $product ) {
			$cart_redirect_after_add       = get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes';
			$ajax_add_to_cart_enabled      = get_option( 'woocommerce_enable_ajax_add_to_cart' ) === 'yes';
			$is_ajax_button                = $ajax_add_to_cart_enabled && ! $cart_redirect_after_add && $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock();
			$html_element                  = $is_ajax_button ? 'button' : 'a';
			$styles_and_classes            = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );
			$text_align_styles_and_classes = StyleAttributesUtils::get_text_align_class_and_style( $attributes );
			$classname                     = isset( $attributes['className'] ) ? $attributes['className'] : '';
			$custom_width_classes          = isset( $attributes['width'] ) ? 'has-custom-width wp-block-button__width-' . $attributes['width'] : '';
			$html_classes                  = implode(
				' ',
				array_filter(
					array(
						'wp-block-button__link',
						'wp-element-button',
						'wc-block-components-product-button__button',
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$is_ajax_button ? 'ajax_add_to_cart' : '',
						'product_type_' . $product->get_type(),
						$styles_and_classes['classes'],
					)
				)
			);
			/**
			 * Allow filtering of the add to cart button arguments.
			 *
			 * @since 9.7.0
			 */
			$args = apply_filters(
				'woocommerce_loop_add_to_cart_args',
				array(
					'class'      => $html_classes,
					'attributes' => array(
						'data-product_id'  => $product->get_id(),
						'data-product_sku' => $product->get_sku(),
						'aria-label'       => $product->add_to_cart_description(),
						'rel'              => 'nofollow',
					),
				),
				$product
			);

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
			}

			/**
			 * Filters the add to cart button class.
			 *
			 * @since 8.7.0
			 *
			 * @param string $class The class.
			 */
			return apply_filters(
				'woocommerce_loop_add_to_cart_link',
				sprintf(
					'<div class="wp-block-button wc-block-components-product-button %1$s %2$s">
					<%3$s href="%4$s" class="%5$s" style="%6$s" %7$s>%8$s</%3$s>
				</div>',
					esc_attr( $text_align_styles_and_classes['class'] ?? '' ),
					esc_attr( $classname . ' ' . $custom_width_classes ),
					$html_element,
					esc_url( $product->add_to_cart_url() ),
					isset( $args['class'] ) ? esc_attr( $args['class'] ) : '',
					esc_attr( $styles_and_classes['styles'] ),
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					esc_html( $product->add_to_cart_text() )
				),
				$product,
				$args
			);
		}
	}
}
