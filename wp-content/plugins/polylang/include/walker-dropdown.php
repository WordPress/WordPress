<?php

/**
 * Displays languages in a dropdown list
 *
 * @since 1.2
 */
class PLL_Walker_Dropdown extends Walker {
	var $db_fields = array( 'parent' => 'parent', 'id' => 'id' );

	/**
	 * Outputs one element
	 *
	 * @since 1.2
	 *
	 * @param string $output            Passed by reference. Used to append additional content.
	 * @param object $element           The data object.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $current_object_id ID of the current item.
	 */
	function start_el( &$output, $element, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$value = $args['value'];
		$output .= sprintf(
			"\t" . '<option value="%1$s"%2$s%3$s>%4$s</option>' . "\n",
			esc_attr( $element->$value ),
			method_exists( $element, 'get_locale' ) ? sprintf( ' lang="%s"', esc_attr( $element->get_locale( 'display' ) ) ) : '',
			isset( $args['selected'] ) && $args['selected'] === $element->$value ? ' selected="selected"' : '',
			esc_html( $element->name )
		);
	}

	/**
	 * Overrides Walker::display_element as expects an object with a parent property
	 *
	 * @since 1.2
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$element = (object) $element; // Make sure we have an object
		$element->parent = $element->id = 0; // Don't care about this
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Starts the output of the dropdown list
	 *
	 * @since 1.2
	 *
	 * List of parameters accepted in $args:
	 *
	 * flag     => display the selected language flag in front of the dropdown if set to 1, defaults to 0
	 * value    => the language field to use as value attribute, defaults to 'slug'
	 * selected => the selected value, mandatory
	 * name     => the select name attribute, defaults to 'lang_choice'
	 * id       => the select id attribute, defaults to $args['name']
	 * class    => the class attribute
	 * disabled => disables the dropdown if set to 1
	 *
	 * @param array $elements elements to display
	 * @param array $args
	 * @return string
	 */
	function walk( $elements, $args = array() ) {
		$output = '';
		$args = wp_parse_args( $args, array( 'value' => 'slug', 'name' => 'lang_choice' ) );

		if ( ! empty( $args['flag'] ) ) {
			$current = wp_list_filter( $elements, array( $args['value'] => $args['selected'] ) );
			$lang = reset( $current );
			$output = sprintf(
				'<span class="pll-select-flag">%s</span>',
				empty( $lang->flag ) ? esc_html( $lang->slug ) : $lang->flag
			);
		}

		$output .= sprintf(
			'<select name="%1$s"%2$s%3$s%4$s>' . "\n" . '%5$s' . "\n" . '</select>' . "\n",
			$name = esc_attr( $args['name'] ),
			isset( $args['id'] ) && ! $args['id'] ? '' : ' id="' . ( empty( $args['id'] ) ? $name : esc_attr( $args['id'] ) ) . '"',
			empty( $args['class'] ) ? '' : ' class="' . esc_attr( $args['class'] ) . '"',
			empty( $args['disabled'] ) ? '' : ' disabled="disabled"',
			parent::walk( $elements, -1, $args )
		);

		return $output;
	}
}
