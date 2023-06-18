<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductImage class.
 */
class ProductImage extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-image';

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
			'__experimentalBorder'   =>
			array(
				'radius'                          => true,
				'__experimentalSkipSerialization' => true,
			),
			'typography'             =>
			array(
				'fontSize'                        => true,
				'__experimentalSkipSerialization' => true,
			),
			'spacing'                =>
			array(
				'margin'                          => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalSelector' => '.wc-block-components-product-image',
		);
	}

	/**
	 * It is necessary to register and enqueues assets during the render phase because we want to load assets only if the block has the content.
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
	 * Get the block's attributes.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return array  Block attributes merged with defaults.
	 */
	private function parse_attributes( $attributes ) {
		// These should match what's set in JS `registerBlockType`.
		$defaults = array(
			'showProductLink'         => true,
			'showSaleBadge'           => true,
			'saleBadgeAlign'          => 'right',
			'imageSizing'             => 'single',
			'productId'               => 'number',
			'isDescendentOfQueryLoop' => 'false',
		);

		return wp_parse_args( $attributes, $defaults );
	}

	/**
	 * Render on Sale Badge.
	 *
	 * @param \WC_Product $product Product object.
	 * @param array       $attributes Attributes.
	 * @return string
	 */
	private function render_on_sale_badge( $product, $attributes ) {
		if ( ! $product->is_on_sale() || false === $attributes['showSaleBadge'] ) {
			return '';
		}

		$font_size = StyleAttributesUtils::get_font_size_class_and_style( $attributes );

		$on_sale_badge = sprintf(
			'
		<div class="wc-block-components-product-sale-badge wc-block-components-product-sale-badge--align-%s wc-block-grid__product-onsale %s" style="%s">
			<span aria-hidden="true">%s</span>
			<span class="screen-reader-text">Product on sale</span>
		</div>
	',
			$attributes['saleBadgeAlign'],
			isset( $font_size['class'] ) ? esc_attr( $font_size['class'] ) : '',
			isset( $font_size['style'] ) ? esc_attr( $font_size['style'] ) : '',
			esc_html__( 'Sale', 'woocommerce' )
		);
		return $on_sale_badge;
	}

	/**
	 * Render anchor.
	 *
	 * @param \WC_Product $product       Product object.
	 * @param string      $on_sale_badge Return value from $render_image.
	 * @param string      $product_image Return value from $render_on_sale_badge.
	 * @param array       $attributes    Attributes.
	 * @return string
	 */
	private function render_anchor( $product, $on_sale_badge, $product_image, $attributes ) {
		$product_permalink = $product->get_permalink();

		$pointer_events = false === $attributes['showProductLink'] ? 'pointer-events: none;' : '';

		return sprintf(
			'<a href="%1$s" style="%2$s">%3$s %4$s</a>',
			$product_permalink,
			$pointer_events,
			$on_sale_badge,
			$product_image
		);
	}



	/**
	 * Render Image.
	 *
	 * @param \WC_Product $product Product object.
	 * @param array       $attributes Parsed attributes.
	 * @return string
	 */
	private function render_image( $product, $attributes ) {
		$image_type = 'single' == $attributes['imageSizing'] ? 'woocommerce_single' : 'woocommerce_thumbnail';
		$image_info = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), $image_type );

		if ( ! isset( $image_info[0] ) ) {
			// The alt text is left empty on purpose, as it's considered a decorative image.
			// More can be found here: https://www.w3.org/WAI/tutorials/images/decorative/.
			// Github discussion for a context: https://github.com/woocommerce/woocommerce-blocks/pull/7651#discussion_r1019560494.
			return sprintf( '<img src="%s" alt="" />', wc_placeholder_img_src( $image_type ) );
		}

		return sprintf(
			'<img data-testid="product-image" alt="%s" src="%s">',
			$product->get_title(),
			$image_info[0]
		);
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		$this->asset_data_registry->add( 'is_block_theme_enabled', wc_current_theme_is_fse_theme(), false );
	}


	/**
	 * Include and render the block
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
		$parsed_attributes = $this->parse_attributes( $attributes );

		$border_radius      = StyleAttributesUtils::get_border_radius_class_and_style( $attributes );
		$margin             = StyleAttributesUtils::get_margin_class_and_style( $attributes );
		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );

		$post_id = $block->context['postId'];
		$product = wc_get_product( $post_id );

		if ( $product ) {
			return sprintf(
				'<div class="wc-block-components-product-image wc-block-grid__product-image %1$s" style="%2$s">
					%3$s
				</div>',
				esc_attr( $classes_and_styles['classes'] ),
				esc_attr( $classes_and_styles['styles'] ),
				$this->render_anchor(
					$product,
					$this->render_on_sale_badge( $product, $parsed_attributes ),
					$this->render_image( $product, $parsed_attributes ),
					$parsed_attributes
				)
			);

		}
	}
}
