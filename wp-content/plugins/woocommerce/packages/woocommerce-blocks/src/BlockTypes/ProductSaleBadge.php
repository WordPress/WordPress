<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductSaleBadge class.
 */
class ProductSaleBadge extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-sale-badge';

	/**
	 * API version name.
	 *
	 * @var string
	 */
	protected $api_version = '2';

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_supports() {
		return array(
			'color'                  =>
			array(
				'gradients'  => true,
				'background' => true,
				'link'       => true,
			),
			'typography'             =>
			array(
				'fontSize'                        => true,
				'lineHeight'                      => true,
				'__experimentalFontFamily'        => true,
				'__experimentalFontWeight'        => true,
				'__experimentalFontStyle'         => true,
				'__experimentalLetterSpacing'     => true,
				'__experimentalTextTransform'     => true,
				'__experimentalTextDecoration'    => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalBorder'   =>
			array(
				'color'  => true,
				'radius' => true,
				'width'  => true,
			),
			'spacing'                =>
			array(
				'margin'                          => true,
				'padding'                         => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalSelector' => '.wc-block-components-product-sale-badge',
		);
	}

	/**
	 * Overwrite parent method to prevent script registration.
	 *
	 * It is necessary to register and enqueues assets during the render
	 * phase because we want to load assets only if the block has the content.
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

		$post_id    = $block->context['postId'];
		$product    = wc_get_product( $post_id );
		$is_on_sale = $product->is_on_sale();

		if ( ! $is_on_sale ) {
			return null;
		}

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );
		$classname          = isset( $attributes['className'] ) ? $attributes['className'] : '';

		$output  = '<div class="wc-block-components-product-sale-badge '
								. esc_attr( $classes_and_styles['classes'] ) . ' '
								. esc_attr( $classname ) . '" '
						. 'style="' . esc_attr( $classes_and_styles['styles'] ) . '"'
					. '>';
		$output .= '<span class="wc-block-components-product-sale-badge__text" aria-hidden="true">' . __( 'Sale', 'woocommerce' ) . '</span>';
		$output .= '<span class="screen-reader-text">'
						. __( 'Product on sale', 'woocommerce' )
					. '</span>';
		$output .= '</div>';

		return $output;
	}
}
