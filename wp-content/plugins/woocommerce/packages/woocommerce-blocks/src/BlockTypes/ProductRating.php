<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;
/**
 * ProductRating class.
 */
class ProductRating extends AbstractBlock {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-rating';

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
				'text'                            => true,
				'background'                      => false,
				'link'                            => false,
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
				'padding'                         => true,
				'__experimentalSkipSerialization' => true,
			),
			'__experimentalSelector' => '.wc-block-components-product-rating',
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
	 * Filter the output from wc_get_rating_html.
	 *
	 * @param string $html   Star rating markup. Default empty string.
	 * @param float  $rating Rating being shown.
	 * @param int    $count  Total number of ratings.
	 * @return string
	 */
	public function filter_rating_html( $html, $rating, $count ) {
		$product_permalink = get_permalink();
		if ( 0 < $rating || false === $product_permalink ) {
			/* translators: %s: rating */
			$label = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );
			$html  = '<div class="wc-block-components-product-rating__stars wc-block-grid__product-rating__stars" role="img" aria-label="' . esc_attr( $label ) . '">' . wc_get_star_rating_html( $rating, $count ) . '</div>';
		} else {
			$product_review_url = esc_url( $product_permalink . '#reviews' );
			$html               = '<a class="wc-block-components-product-rating__link" href="' . $product_review_url . '">' . __( 'Add review', 'woocommerce' ) . '</a>';
		}

		return $html;
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

			$styles_and_classes            = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes );
			$text_align_styles_and_classes = StyleAttributesUtils::get_text_align_class_and_style( $attributes );

			add_filter(
				'woocommerce_product_get_rating_html',
				[ $this, 'filter_rating_html' ],
				10,
				3
			);

			$rating_html = wc_get_rating_html( $product->get_average_rating() );

			remove_filter(
				'woocommerce_product_get_rating_html',
				[ $this, 'filter_rating_html' ],
				10
			);

			return sprintf(
				'<div class="wc-block-components-product-rating wc-block-grid__product-rating %1$s %2$s" style="%3$s">
					%4$s
				</div>',
				esc_attr( $text_align_styles_and_classes['class'] ?? '' ),
				esc_attr( $styles_and_classes['classes'] ),
				esc_attr( $styles_and_classes['styles'] ?? '' ),
				$rating_html
			);
		}
	}
}
