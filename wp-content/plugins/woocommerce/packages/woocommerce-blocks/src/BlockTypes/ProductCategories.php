<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;

/**
 * ProductCategories class.
 */
class ProductCategories extends AbstractDynamicBlock {


	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-categories';

	/**
	 * Default attribute values, should match what's set in JS `registerBlockType`.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'hasCount'       => true,
		'hasImage'       => false,
		'hasEmpty'       => false,
		'isDropdown'     => false,
		'isHierarchical' => true,
	);

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	protected function get_block_type_attributes() {
		return array_merge(
			parent::get_block_type_attributes(),
			array(
				'align'          => $this->get_schema_align(),
				'className'      => $this->get_schema_string(),
				'hasCount'       => $this->get_schema_boolean( true ),
				'hasImage'       => $this->get_schema_boolean( false ),
				'hasEmpty'       => $this->get_schema_boolean( false ),
				'isDropdown'     => $this->get_schema_boolean( false ),
				'isHierarchical' => $this->get_schema_boolean( true ),
				'textColor'      => $this->get_schema_string(),
				'fontSize'       => $this->get_schema_string(),
				'lineHeight'     => $this->get_schema_string(),
				'style'          => array( 'type' => 'object' ),
			)
		);
	}

	/**
	 * Render the Product Categories List block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		$uid        = uniqid( 'product-categories-' );
		$categories = $this->get_categories( $attributes );

		if ( empty( $categories ) ) {
			return '';
		}

		if ( ! empty( $content ) ) {
			// Deal with legacy attributes (before this was an SSR block) that differ from defaults.
			if ( strstr( $content, 'data-has-count="false"' ) ) {
				$attributes['hasCount'] = false;
			}
			if ( strstr( $content, 'data-is-dropdown="true"' ) ) {
				$attributes['isDropdown'] = true;
			}
			if ( strstr( $content, 'data-is-hierarchical="false"' ) ) {
				$attributes['isHierarchical'] = false;
			}
			if ( strstr( $content, 'data-has-empty="true"' ) ) {
				$attributes['hasEmpty'] = true;
			}
		}

		$classes_and_styles = StyleAttributesUtils::get_classes_and_styles_by_attributes(
			$attributes,
			array( 'line_height', 'text_color', 'font_size' )
		);

		$classes = $this->get_container_classes( $attributes ) . ' ' . $classes_and_styles['classes'];
		$styles  = $classes_and_styles['styles'];

		$output  = '<div class="wp-block-woocommerce-product-categories ' . esc_attr( $classes ) . '" style="' . esc_attr( $styles ) . '">';
		$output .= ! empty( $attributes['isDropdown'] ) ? $this->renderDropdown( $categories, $attributes, $uid ) : $this->renderList( $categories, $attributes, $uid );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get the list of classes to apply to this block.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return string space-separated list of classes.
	 */
	protected function get_container_classes( $attributes = array() ) {

		$classes = array( 'wc-block-product-categories' );

		if ( isset( $attributes['align'] ) ) {
			$classes[] = "align{$attributes['align']}";
		}

		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}

		if ( $attributes['isDropdown'] ) {
			$classes[] = 'is-dropdown';
		} else {
			$classes[] = 'is-list';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get categories (terms) from the db.
	 *
	 * @param array $attributes Block attributes. Default empty array.
	 * @return array
	 */
	protected function get_categories( $attributes ) {
		$hierarchical = wc_string_to_bool( $attributes['isHierarchical'] );
		$categories   = get_terms(
			'product_cat',
			[
				'hide_empty'   => ! $attributes['hasEmpty'],
				'pad_counts'   => true,
				'hierarchical' => true,
			]
		);

		if ( ! is_array( $categories ) || empty( $categories ) ) {
			return [];
		}

		// This ensures that no categories with a product count of 0 is rendered.
		if ( ! $attributes['hasEmpty'] ) {
			$categories = array_filter(
				$categories,
				function( $category ) {
					return 0 !== $category->count;
				}
			);
		}

		return $hierarchical ? $this->build_category_tree( $categories ) : $categories;
	}

	/**
	 * Build hierarchical tree of categories.
	 *
	 * @param array $categories List of terms.
	 * @return array
	 */
	protected function build_category_tree( $categories ) {
		$categories_by_parent = [];

		foreach ( $categories as $category ) {
			if ( ! isset( $categories_by_parent[ 'cat-' . $category->parent ] ) ) {
				$categories_by_parent[ 'cat-' . $category->parent ] = [];
			}
			$categories_by_parent[ 'cat-' . $category->parent ][] = $category;
		}

		$tree = $categories_by_parent['cat-0'];
		unset( $categories_by_parent['cat-0'] );

		foreach ( $tree as $category ) {
			if ( ! empty( $categories_by_parent[ 'cat-' . $category->term_id ] ) ) {
				$category->children = $this->fill_category_children( $categories_by_parent[ 'cat-' . $category->term_id ], $categories_by_parent );
			}
		}

		return $tree;
	}

	/**
	 * Build hierarchical tree of categories by appending children in the tree.
	 *
	 * @param array $categories List of terms.
	 * @param array $categories_by_parent List of terms grouped by parent.
	 * @return array
	 */
	protected function fill_category_children( $categories, $categories_by_parent ) {
		foreach ( $categories as $category ) {
			if ( ! empty( $categories_by_parent[ 'cat-' . $category->term_id ] ) ) {
				$category->children = $this->fill_category_children( $categories_by_parent[ 'cat-' . $category->term_id ], $categories_by_parent );
			}
		}
		return $categories;
	}

	/**
	 * Render the category list as a dropdown.
	 *
	 * @param array $categories List of terms.
	 * @param array $attributes Block attributes. Default empty array.
	 * @param int   $uid Unique ID for the rendered block, used for HTML IDs.
	 * @return string Rendered output.
	 */
	protected function renderDropdown( $categories, $attributes, $uid ) {
		$aria_label = empty( $attributes['hasCount'] ) ?
			__( 'List of categories', 'woocommerce' ) :
			__( 'List of categories with their product counts', 'woocommerce' );

		$output = '
			<div class="wc-block-product-categories__dropdown">
				<label
				class="screen-reader-text"
					for="' . esc_attr( $uid ) . '-select"
				>
					' . esc_html__( 'Select a category', 'woocommerce' ) . '
				</label>
				<select aria-label="' . esc_attr( $aria_label ) . '" id="' . esc_attr( $uid ) . '-select">
					<option value="false" hidden>
						' . esc_html__( 'Select a category', 'woocommerce' ) . '
					</option>
					' . $this->renderDropdownOptions( $categories, $attributes, $uid ) . '
				</select>
			</div>
			<button
				type="button"
				class="wc-block-product-categories__button"
				aria-label="' . esc_html__( 'Go to category', 'woocommerce' ) . '"
				onclick="const url = document.getElementById( \'' . esc_attr( $uid ) . '-select\' ).value; if ( \'false\' !== url ) document.location.href = url;"
			>
				<svg
					aria-hidden="true"
					role="img"
					focusable="false"
					class="dashicon dashicons-arrow-right-alt2"
					xmlns="http://www.w3.org/2000/svg"
					width="20"
					height="20"
					viewBox="0 0 20 20"
				>
					<path d="M6 15l5-5-5-5 1-2 7 7-7 7z" />
				</svg>
			</button>
		';
		return $output;
	}

	/**
	 * Render dropdown options list.
	 *
	 * @param array $categories List of terms.
	 * @param array $attributes Block attributes. Default empty array.
	 * @param int   $uid Unique ID for the rendered block, used for HTML IDs.
	 * @param int   $depth Current depth.
	 * @return string Rendered output.
	 */
	protected function renderDropdownOptions( $categories, $attributes, $uid, $depth = 0 ) {
		$output = '';

		foreach ( $categories as $category ) {
			$output .= '
				<option value="' . esc_attr( get_term_link( $category->term_id, 'product_cat' ) ) . '">
					' . str_repeat( '&minus;', $depth ) . '
					' . esc_html( $category->name ) . '
					' . $this->getCount( $category, $attributes ) . '
				</option>
				' . ( ! empty( $category->children ) ? $this->renderDropdownOptions( $category->children, $attributes, $uid, $depth + 1 ) : '' ) . '
			';
		}

		return $output;
	}

	/**
	 * Render the category list as a list.
	 *
	 * @param array $categories List of terms.
	 * @param array $attributes Block attributes. Default empty array.
	 * @param int   $uid Unique ID for the rendered block, used for HTML IDs.
	 * @param int   $depth Current depth.
	 * @return string Rendered output.
	 */
	protected function renderList( $categories, $attributes, $uid, $depth = 0 ) {
		$classes = [
			'wc-block-product-categories-list',
			'wc-block-product-categories-list--depth-' . absint( $depth ),
		];
		if ( ! empty( $attributes['hasImage'] ) ) {
			$classes[] = 'wc-block-product-categories-list--has-images';
		}
		$output = '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $this->renderListItems( $categories, $attributes, $uid, $depth ) . '</ul>';

		return $output;
	}

	/**
	 * Render a list of terms.
	 *
	 * @param array $categories List of terms.
	 * @param array $attributes Block attributes. Default empty array.
	 * @param int   $uid Unique ID for the rendered block, used for HTML IDs.
	 * @param int   $depth Current depth.
	 * @return string Rendered output.
	 */
	protected function renderListItems( $categories, $attributes, $uid, $depth = 0 ) {
		$output = '';

		$link_color_class_and_style = StyleAttributesUtils::get_link_color_class_and_style( $attributes );

		$link_color_style = isset( $link_color_class_and_style['style'] ) ? $link_color_class_and_style['style'] : '';

		foreach ( $categories as $category ) {
			$output .= '
				<li class="wc-block-product-categories-list-item">
					<a style="' . esc_attr( $link_color_style ) . '" href="' . esc_attr( get_term_link( $category->term_id, 'product_cat' ) ) . '">'
						. $this->get_image_html( $category, $attributes )
						. '<span class="wc-block-product-categories-list-item__name">' . esc_html( $category->name ) . '</span>'
					. '</a>'
					. $this->getCount( $category, $attributes )
					. ( ! empty( $category->children ) ? $this->renderList( $category->children, $attributes, $uid, $depth + 1 ) : '' ) . '
				</li>
			';
		}

		return preg_replace( '/\r|\n/', '', $output );
	}

	/**
	 * Returns the category image html
	 *
	 * @param \WP_Term $category Term object.
	 * @param array    $attributes Block attributes. Default empty array.
	 * @param string   $size Image size, defaults to 'woocommerce_thumbnail'.
	 * @return string
	 */
	public function get_image_html( $category, $attributes, $size = 'woocommerce_thumbnail' ) {
		if ( empty( $attributes['hasImage'] ) ) {
			return '';
		}

		$image_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( ! $image_id ) {
			return '<span class="wc-block-product-categories-list-item__image wc-block-product-categories-list-item__image--placeholder">' . wc_placeholder_img( 'woocommerce_thumbnail' ) . '</span>';
		}

		return '<span class="wc-block-product-categories-list-item__image">' . wp_get_attachment_image( $image_id, 'woocommerce_thumbnail' ) . '</span>';
	}

	/**
	 * Get the count, if displaying.
	 *
	 * @param object $category Term object.
	 * @param array  $attributes Block attributes. Default empty array.
	 * @return string
	 */
	protected function getCount( $category, $attributes ) {
		if ( empty( $attributes['hasCount'] ) ) {
			return '';
		}

		if ( $attributes['isDropdown'] ) {
			return '(' . absint( $category->count ) . ')';
		}

		$screen_reader_text = sprintf(
			/* translators: %s number of products in cart. */
			_n( '%d product', '%d products', absint( $category->count ), 'woocommerce' ),
			absint( $category->count )
		);

		return '<span class="wc-block-product-categories-list-item-count">'
			. '<span aria-hidden="true">' . absint( $category->count ) . '</span>'
			. '<span class="screen-reader-text">' . esc_html( $screen_reader_text ) . '</span>'
		. '</span>';
	}
}
