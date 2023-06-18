<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * Mini Cart class.
 *
 * @internal
 */
class MiniCartContents extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'mini-cart-contents';

	/**
	 * Get the editor script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 *
	 * @return array|string;
	 */
	protected function get_block_type_editor_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name ),
			'dependencies' => [ 'wc-blocks' ],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 *
	 * @return null
	 */
	protected function get_block_type_script( $key = null ) {
		// The frontend script is a dependency of the Mini Cart block so it's
		// already lazy-loaded.
		return null;
	}

	/**
	 * Render the markup for the Mini Cart contents block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		if ( is_admin() || WC()->is_rest_api_request() ) {
			// In the editor we will display the placeholder, so no need to
			// print the markup.
			return '';
		}

		return $content;
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 */
	protected function enqueue_assets( array $attributes ) {
		parent::enqueue_assets( $attributes );
		$text_color = StyleAttributesUtils::get_text_color_class_and_style( $attributes );
		$bg_color   = StyleAttributesUtils::get_background_color_class_and_style( $attributes );

		$styles = array(
			array(
				'selector'   => '.wc-block-mini-cart__drawer .components-modal__header',
				'properties' => array(
					array(
						'property' => 'color',
						'value'    => $text_color ? $text_color['value'] : false,
					),
				),
			),
			array(
				'selector'   => array(
					'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-checkout',
					'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-checkout:hover',
					'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-checkout:focus',
					'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-cart.wc-block-components-button:hover',
					'.wc-block-mini-cart__footer .wc-block-mini-cart__footer-actions .wc-block-mini-cart__footer-cart.wc-block-components-button:focus',
					'.wc-block-mini-cart__shopping-button a:hover',
					'.wc-block-mini-cart__shopping-button a:focus',
				),
				'properties' => array(
					array(
						'property' => 'color',
						'value'    => $bg_color ? $bg_color['value'] : false,
					),
					array(
						'property' => 'border-color',
						'value'    => $text_color ? $text_color['value'] : false,
					),
					array(
						'property' => 'background-color',
						'value'    => $text_color ? $text_color['value'] : false,
					),
				),
			),
		);

		$parsed_style = '';
		if ( array_key_exists( 'width', $attributes ) ) {
			$parsed_style .= ':root{--drawer-width: ' . esc_html( $attributes['width'] ) . '}';
		}

		foreach ( $styles as $style ) {
			$selector = is_array( $style['selector'] ) ? implode( ',', $style['selector'] ) : $style['selector'];

			$properties = array_filter(
				$style['properties'],
				function( $property ) {
					return $property['value'];
				}
			);

			if ( ! empty( $properties ) ) {
				$parsed_style .= $selector . '{';
				foreach ( $properties as $property ) {
					$parsed_style .= sprintf( '%1$s:%2$s;', $property['property'], $property['value'] );
				}
				$parsed_style .= '}';
			}
		}

		wp_add_inline_style(
			'wc-blocks-style',
			$parsed_style
		);
	}

	/**
	 * Get list of Mini Cart block & its inner-block types.
	 *
	 * @return array;
	 */
	public static function get_mini_cart_block_types() {
		$block_types = [];

		$block_types[] = 'MiniCartContents';
		$block_types[] = 'EmptyMiniCartContentsBlock';
		$block_types[] = 'FilledMiniCartContentsBlock';
		$block_types[] = 'MiniCartFooterBlock';
		$block_types[] = 'MiniCartItemsBlock';
		$block_types[] = 'MiniCartProductsTableBlock';
		$block_types[] = 'MiniCartShoppingButtonBlock';
		$block_types[] = 'MiniCartCartButtonBlock';
		$block_types[] = 'MiniCartCheckoutButtonBlock';
		$block_types[] = 'MiniCartTitleBlock';
		$block_types[] = 'MiniCartTitleItemsCounterBlock';
		$block_types[] = 'MiniCartTitleLabelBlock';

		return $block_types;
	}

}
