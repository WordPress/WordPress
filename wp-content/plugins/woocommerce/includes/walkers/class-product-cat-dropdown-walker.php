<?php
/**
 * WC_Product_Cat_Dropdown_Walker class.
 *
 * @extends 	Walker
 * @class 		WC_Product_Cat_Dropdown_Walker
 * @version		1.6.4
 * @package		WooCommerce/Classes/Walkers
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Product_Cat_Dropdown_Walker extends Walker {

	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id', 'slug' => 'slug' );

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category in reference to parents.
	 * @param integer $current_object_id
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

		if ( ! empty( $args['hierarchical'] ) )
			$pad = str_repeat('&nbsp;', $depth * 3);
		else
			$pad = '';

		$cat_name = apply_filters( 'list_product_cats', $cat->name, $cat );

		$value = isset( $args['value'] ) && $args['value'] == 'id' ? $cat->term_id : $cat->slug;

		$output .= "\t<option class=\"level-$depth\" value=\"" . $value . "\"";

		if ( $value == $args['selected'] || ( is_array( $args['selected'] ) && in_array( $value, $args['selected'] ) ) )
			$output .= ' selected="selected"';

		$output .= '>';

		$output .= $pad . __( $cat_name, 'woocommerce' );

		if ( ! empty( $args['show_count'] ) )
			$output .= '&nbsp;(' . $cat->count . ')';

		$output .= "</option>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		if ( ! $element || 0 === $element->count ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}	
}