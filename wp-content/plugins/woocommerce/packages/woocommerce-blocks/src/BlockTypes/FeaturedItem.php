<?php

namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * FeaturedItem class.
 */
abstract class FeaturedItem extends AbstractDynamicBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name;

	/**
	 * Default attribute values.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'align' => 'none',
	);

	/**
	 * Global style enabled for this block.
	 *
	 * @var array
	 */
	protected $global_style_wrapper = array(
		'background_color',
		'border_color',
		'border_radius',
		'border_width',
		'font_size',
		'padding',
		'text_color',
	);

	/**
	 * Returns the featured item.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return \WP_Term|\WC_Product|null
	 */
	abstract protected function get_item( $attributes );

	/**
	 * Returns the name of the featured item.
	 *
	 * @param \WP_Term|\WC_Product $item Item object.
	 * @return string
	 */
	abstract protected function get_item_title( $item );

	/**
	 * Returns the featured item image URL.
	 *
	 * @param \WP_Term|\WC_Product $item Item object.
	 * @param string               $size Image size, defaults to 'full'.
	 * @return string
	 */
	abstract protected function get_item_image( $item, $size = 'full' );

	/**
	 * Renders the featured item attributes.
	 *
	 * @param \WP_Term|\WC_Product $item       Item object.
	 * @param array                $attributes Block attributes. Default empty array.
	 * @return string
	 */
	abstract protected function render_attributes( $item, $attributes );

	/**
	 * Render the featured item block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		$item = $this->get_item( $attributes );
		if ( ! $item ) {
			return '';
		}

		$attributes = wp_parse_args( $attributes, $this->defaults );

		$attributes['height'] = $attributes['height'] ?? wc_get_theme_support( 'featured_block::default_height', 500 );

		$image_url = esc_url( $this->get_image_url( $attributes, $item ) );

		$styles  = $this->get_styles( $attributes );
		$classes = $this->get_classes( $attributes );

		$output  = sprintf( '<div class="%1$s wp-block-woocommerce-%2$s" style="%3$s">', esc_attr( trim( $classes ) ), $this->block_name, esc_attr( $styles ) );
		$output .= sprintf( '<div class="wc-block-%s__wrapper">', $this->block_name );
		$output .= $this->render_overlay( $attributes );

		if ( ! $attributes['isRepeated'] && ! $attributes['hasParallax'] ) {
			$output .= $this->render_image( $attributes, $item, $image_url );
		} else {
			$output .= $this->render_bg_image( $attributes, $image_url );
		}

		$output .= $this->render_attributes( $item, $attributes );
		$output .= sprintf( '<div class="wc-block-%s__link">%s</div>', $this->block_name, $content );
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Returns the url the item's image
	 *
	 * @param array                $attributes Block attributes. Default empty array.
	 * @param \WP_Term|\WC_Product $item       Item object.
	 *
	 * @return string
	 */
	private function get_image_url( $attributes, $item ) {
		$image_size = 'large';
		if ( 'none' !== $attributes['align'] || $attributes['height'] > 800 ) {
			$image_size = 'full';
		}

		if ( $attributes['mediaId'] ) {
			return wp_get_attachment_image_url( $attributes['mediaId'], $image_size );
		}

		return $this->get_item_image( $item, $image_size );
	}

	/**
	 * Renders the featured image as a div background.
	 *
	 * @param array  $attributes Block attributes. Default empty array.
	 * @param string $image_url  Item image url.
	 *
	 * @return string
	 */
	private function render_bg_image( $attributes, $image_url ) {
		$styles = $this->get_bg_styles( $attributes, $image_url );

		$classes = [ "wc-block-{$this->block_name}__background-image" ];

		if ( $attributes['hasParallax'] ) {
			$classes[] = ' has-parallax';
		}

		return sprintf( '<div class="%1$s" style="%2$s" /></div>', implode( ' ', $classes ), $styles );
	}

	/**
	 * Get the styles for the wrapper element (background image, color).
	 *
	 * @param array  $attributes Block attributes. Default empty array.
	 * @param string $image_url  Item image url.
	 *
	 * @return string
	 */
	public function get_bg_styles( $attributes, $image_url ) {
		$style = '';

		if ( $attributes['isRepeated'] || $attributes['hasParallax'] ) {
			$style .= "background-image: url($image_url);";
		}

		if ( ! $attributes['isRepeated'] ) {
			$style .= 'background-repeat: no-repeat;';

			$bg_size = 'cover' === $attributes['imageFit'] ? $attributes['imageFit'] : 'auto';
			$style  .= 'background-size: ' . $bg_size . ';';
		}

		if ( $this->hasFocalPoint( $attributes ) ) {
			$style .= sprintf(
				'background-position: %s%% %s%%;',
				$attributes['focalPoint']['x'] * 100,
				$attributes['focalPoint']['y'] * 100
			);
		}

		$global_style_style = StyleAttributesUtils::get_styles_by_attributes( $attributes, $this->global_style_wrapper );
		$style             .= $global_style_style;

		return $style;
	}

	/**
	 * Renders the featured image
	 *
	 * @param array                $attributes Block attributes. Default empty array.
	 * @param \WC_Product|\WP_Term $item       Item object.
	 * @param string               $image_url  Item image url.
	 *
	 * @return string
	 */
	private function render_image( $attributes, $item, string $image_url ) {
		$style = sprintf( 'object-fit: %s;', $attributes['imageFit'] );

		if ( $this->hasFocalPoint( $attributes ) ) {
			$style .= sprintf(
				'object-position: %s%% %s%%;',
				$attributes['focalPoint']['x'] * 100,
				$attributes['focalPoint']['y'] * 100
			);
		}

		if ( ! empty( $image_url ) ) {
			return sprintf(
				'<img alt="%1$s" class="wc-block-%2$s__background-image" src="%3$s" style="%4$s" />',
				wp_kses_post( $attributes['alt'] ?: $this->get_item_title( $item ) ),
				$this->block_name,
				$image_url,
				$style
			);
		}

		return '';
	}

	/**
	 * Get the styles for the wrapper element (background image, color).
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return string
	 */
	public function get_styles( $attributes ) {
		$style = '';

		$min_height = $attributes['minHeight'] ?? wc_get_theme_support( 'featured_block::default_height', 500 );

		if ( isset( $attributes['minHeight'] ) ) {
			$style .= sprintf( 'min-height:%dpx;', intval( $min_height ) );
		}

		$global_style_style = StyleAttributesUtils::get_styles_by_attributes( $attributes, $this->global_style_wrapper );
		$style             .= $global_style_style;

		return $style;
	}


	/**
	 * Get class names for the block container.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return string
	 */
	public function get_classes( $attributes ) {
		$classes = array( 'wc-block-' . $this->block_name );

		if ( isset( $attributes['align'] ) ) {
			$classes[] = "align{$attributes['align']}";
		}

		if ( isset( $attributes['dimRatio'] ) && ( 0 !== $attributes['dimRatio'] ) ) {
			$classes[] = 'has-background-dim';

			if ( 50 !== $attributes['dimRatio'] ) {
				$classes[] = 'has-background-dim-' . 10 * round( $attributes['dimRatio'] / 10 );
			}
		}

		if ( isset( $attributes['contentAlign'] ) && 'center' !== $attributes['contentAlign'] ) {
			$classes[] = "has-{$attributes['contentAlign']}-content";
		}

		if ( isset( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}

		$global_style_classes = StyleAttributesUtils::get_classes_by_attributes( $attributes, $this->global_style_wrapper );

		$classes[] = $global_style_classes;

		return implode( ' ', $classes );
	}

	/**
	 * Renders the block overlay
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 *
	 * @return string
	 */
	private function render_overlay( $attributes ) {
		if ( isset( $attributes['overlayGradient'] ) ) {
			$overlay_styles = sprintf( 'background-image: %s', $attributes['overlayGradient'] );
		} elseif ( isset( $attributes['overlayColor'] ) ) {
			$overlay_styles = sprintf( 'background-color: %s', $attributes['overlayColor'] );
		} else {
			$overlay_styles = 'background-color: #000000';
		}

		return sprintf( '<div class="background-dim__overlay" style="%s"></div>', esc_attr( $overlay_styles ) );
	}

	/**
	 * Returns whether the focal point is defined for the block.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 *
	 * @return bool
	 */
	private function hasFocalPoint( $attributes ): bool {
		return is_array( $attributes['focalPoint'] ) && 2 === count( $attributes['focalPoint'] );
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		parent::enqueue_data( $attributes );
		$this->asset_data_registry->add( 'min_height', wc_get_theme_support( 'featured_block::min_height', 500 ), true );
		$this->asset_data_registry->add( 'default_height', wc_get_theme_support( 'featured_block::default_height', 500 ), true );
	}
}
