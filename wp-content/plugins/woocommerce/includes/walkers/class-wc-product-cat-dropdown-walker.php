<?php
/**
 * WC_Product_Cat_Dropdown_Walker class
 *
 * @package WooCommerce\Classes\Walkers
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Product_Cat_Dropdown_Walker', false ) ) {
	return;
}

/**
 * Product category dropdown walker class.
 */
class WC_Product_Cat_Dropdown_Walker extends Walker {

	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type = 'category';

	/**
	 * DB fields to use.
	 *
	 * @var array
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @param object $cat               Category.
	 * @param int    $depth             Depth of category in reference to parents.
	 * @param array  $args              Arguments.
	 * @param int    $current_object_id Current object ID.
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

		if ( ! empty( $args['hierarchical'] ) ) {
			$pad = str_repeat( '&nbsp;', $depth * 3 );
		} else {
			$pad = '';
		}

		$cat_name = apply_filters( 'list_product_cats', $cat->name, $cat );
		$value    = ( isset( $args['value'] ) && 'id' === $args['value'] ) ? $cat->term_id : $cat->slug;
		$output  .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $value ) . '"';

		if ( $value === $args['selected'] || ( is_array( $args['selected'] ) && in_array( $value, $args['selected'], true ) ) ) {
			$output .= ' selected="selected"';
		}

		$output .= '>';
		$output .= esc_html( $pad . $cat_name );

		if ( ! empty( $args['show_count'] ) ) {
			$output .= '&nbsp;(' . absint( $cat->count ) . ')';
		}

		$output .= "</option>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max.
	 * depth and no ignore elements under that depth. It is possible to set the.
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
