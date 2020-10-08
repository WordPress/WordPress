<?php
/**
 * Navigation Menu API: Walker_Nav_Menu_Checklist class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Create HTML list of nav menu input items.
 *
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Checklist extends Walker_Nav_Menu {
	/**
	 * @param array $fields
	 */
	public function __construct( $fields = false ) {
		if ( $fields ) {
			$this->db_fields = $fields;
		}
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of page. Used for padding.
	 * @param stdClass $args   Not used.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker_Nav_Menu::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of page. Used for padding.
	 * @param stdClass $args   Not used.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent</ul>";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @global int        $_nav_menu_placeholder
	 * @global int|string $nav_menu_selected_id
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   Not used.
	 * @param int      $id     Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		global $_nav_menu_placeholder, $nav_menu_selected_id;

		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? (int) $_nav_menu_placeholder - 1 : -1;
		$possible_object_id    = isset( $item->post_type ) && 'nav_menu_item' === $item->post_type ? $item->object_id : $_nav_menu_placeholder;
		$possible_db_id        = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox"' . wp_nav_menu_disabled_check( $nav_menu_selected_id, false ) . ' class="menu-item-checkbox';

		if ( ! empty( $item->front_or_home ) ) {
			$output .= ' add-to-top';
		}

		$output .= '" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="' . esc_attr( $item->object_id ) . '" /> ';

		if ( ! empty( $item->label ) ) {
			$title = $item->label;
		} elseif ( isset( $item->post_type ) ) {
			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $item->post_title, $item->ID );
		}

		$output .= isset( $title ) ? esc_html( $title ) : esc_html( $item->title );

		if ( empty( $item->label ) && isset( $item->post_type ) && 'page' === $item->post_type ) {
			// Append post states.
			$output .= _post_states( $item, false );
		}

		$output .= '</label>';

		// Menu item hidden fields.
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="' . esc_attr( $item->object ) . '" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="' . esc_attr( $item->menu_item_parent ) . '" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="' . esc_attr( $item->type ) . '" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="' . esc_attr( $item->title ) . '" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="' . esc_attr( $item->url ) . '" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $possible_object_id . '][menu-item-target]" value="' . esc_attr( $item->target ) . '" />';
		$output .= '<input type="hidden" class="menu-item-attr-title" name="menu-item[' . $possible_object_id . '][menu-item-attr-title]" value="' . esc_attr( $item->attr_title ) . '" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $possible_object_id . '][menu-item-classes]" value="' . esc_attr( implode( ' ', $item->classes ) ) . '" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $possible_object_id . '][menu-item-xfn]" value="' . esc_attr( $item->xfn ) . '" />';
	}

}
