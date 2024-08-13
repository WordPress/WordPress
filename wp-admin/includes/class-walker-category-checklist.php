<?php
/**
 * Taxonomy API: Walker_Category_Checklist class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Core walker class to output an unordered list of category checkbox input elements.
 *
 * @since 2.5.1
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 */
class Walker_Category_Checklist extends Walker {
	public $tree_type = 'category';
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
	); // TODO: Decouple this.

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. See {@see wp_terms_checklist()}.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. See {@see wp_terms_checklist()}.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 * @since 5.9.0 Renamed `$category` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 *
	 * @param string  $output            Used to append additional content (passed by reference).
	 * @param WP_Term $data_object       The current term object.
	 * @param int     $depth             Depth of the term in reference to parents. Default 0.
	 * @param array   $args              An array of arguments. See {@see wp_terms_checklist()}.
	 * @param int     $current_object_id Optional. ID of the current term. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method.
		$category = $data_object;

		if ( empty( $args['taxonomy'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $args['taxonomy'];
		}

		if ( 'category' === $taxonomy ) {
			$name = 'post_category';
		} else {
			$name = 'tax_input[' . $taxonomy . ']';
		}

		$args['popular_cats'] = ! empty( $args['popular_cats'] ) ? array_map( 'intval', $args['popular_cats'] ) : array();

		$class = in_array( $category->term_id, $args['popular_cats'], true ) ? ' class="popular-category"' : '';

		$args['selected_cats'] = ! empty( $args['selected_cats'] ) ? array_map( 'intval', $args['selected_cats'] ) : array();

		if ( ! empty( $args['list_only'] ) ) {
			$aria_checked = 'false';
			$inner_class  = 'category';

			if ( in_array( $category->term_id, $args['selected_cats'], true ) ) {
				$inner_class .= ' selected';
				$aria_checked = 'true';
			}

			$output .= "\n" . '<li' . $class . '>' .
				'<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
				' tabindex="0" role="checkbox" aria-checked="' . $aria_checked . '">' .
				/** This filter is documented in wp-includes/category-template.php */
				esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</div>';
		} else {
			$is_selected         = in_array( $category->term_id, $args['selected_cats'], true );
			$is_disabled         = ! empty( $args['disabled'] );
			$li_element_id       = wp_unique_prefixed_id( "in-{$taxonomy}-{$category->term_id}-" );
			$checkbox_element_id = wp_unique_prefixed_id( "in-{$taxonomy}-{$category->term_id}-" );

			$output .= "\n<li id='" . esc_attr( $li_element_id ) . "'$class>" .
				'<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="' . esc_attr( $checkbox_element_id ) . '"' .
				checked( $is_selected, true, false ) .
				disabled( $is_disabled, true, false ) . ' /> ' .
				/** This filter is documented in wp-includes/category-template.php */
				esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</label>';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.5.1
	 * @since 5.9.0 Renamed `$category` to `$data_object` to match parent class for PHP 8 named parameter support.
	 *
	 * @param string  $output      Used to append additional content (passed by reference).
	 * @param WP_Term $data_object The current term object.
	 * @param int     $depth       Depth of the term in reference to parents. Default 0.
	 * @param array   $args        An array of arguments. See {@see wp_terms_checklist()}.
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
