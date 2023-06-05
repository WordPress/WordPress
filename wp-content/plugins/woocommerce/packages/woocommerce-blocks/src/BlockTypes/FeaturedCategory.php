<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

/**
 * FeaturedCategory class.
 */
class FeaturedCategory extends FeaturedItem {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'featured-category';

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_attributes() {
		return array_merge(
			parent::get_block_type_attributes(),
			array(
				'textColor'  => $this->get_schema_string(),
				'fontSize'   => $this->get_schema_string(),
				'lineHeight' => $this->get_schema_string(),
				'style'      => array( 'type' => 'object' ),
			)
		);
	}

	/**
	 * Returns the featured category.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return \WP_Term|null
	 */
	protected function get_item( $attributes ) {
		$id = absint( $attributes['categoryId'] ?? 0 );

		$category = get_term( $id, 'product_cat' );
		if ( ! $category || is_wp_error( $category ) ) {
			return null;
		}

		return $category;
	}

	/**
	 * Returns the name of the featured category.
	 *
	 * @param \WP_Term $category Featured category.
	 * @return string
	 */
	protected function get_item_title( $category ) {
		return $category->name;
	}

	/**
	 * Returns the featured category image URL.
	 *
	 * @param \WP_Term $category Term object.
	 * @param string   $size Image size, defaults to 'full'.
	 * @return string
	 */
	protected function get_item_image( $category, $size = 'full' ) {
		$image    = '';
		$image_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $image_id ) {
			$image = wp_get_attachment_image_url( $image_id, $size );
		}

		return $image;
	}

	/**
	 * Renders the featured category attributes.
	 *
	 * @param \WP_Term $category Term object.
	 * @param array    $attributes Block attributes. Default empty array.
	 * @return string
	 */
	protected function render_attributes( $category, $attributes ) {
		$title = sprintf(
			'<h2 class="wc-block-featured-category__title">%s</h2>',
			wp_kses_post( $category->name )
		);

		$desc_str = sprintf(
			'<div class="wc-block-featured-category__description">%s</div>',
			wc_format_content( wp_kses_post( $category->description ) )
		);

		$output = $title;
		if ( $attributes['showDesc'] ) {
			$output .= $desc_str;
		}

		return $output;
	}
}
