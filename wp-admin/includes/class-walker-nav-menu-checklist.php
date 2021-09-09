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
	 * @param array|false $fields Database fields to use.
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
	 * @since 5.9.0 Renamed `$item` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 *
	 * @global int        $_nav_menu_placeholder
	 * @global int|string $nav_menu_selected_id
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item. Used for padding.
	 * @param stdClass $args              Not used.
	 * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		global $_nav_menu_placeholder, $nav_menu_selected_id;

		// Restores the more descriptive, specific name for use within this method.
		$menu_item = $data_object;

		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? (int) $_nav_menu_placeholder - 1 : -1;
		$possible_object_id    = isset( $menu_item->post_type ) && 'nav_menu_item' === $menu_item->post_type ? $menu_item->object_id : $_nav_menu_placeholder;
		$possible_db_id        = ( ! empty( $menu_item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $menu_item->ID : 0;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox"' . wp_nav_menu_disabled_check( $nav_menu_selected_id, false ) . ' class="menu-item-checkbox';

		if ( ! empty( $menu_item->front_or_home ) ) {
			$output .= ' add-to-top';
		}

		$output .= '" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="' . esc_attr( $menu_item->object_id ) . '" /> ';

		if ( ! empty( $menu_item->label ) ) {
			$title = $menu_item->label;
		} elseif ( isset( $menu_item->post_type ) ) {
			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $menu_item->post_title, $menu_item->ID );
		}

		$output .= isset( $title ) ? esc_html( $title ) : esc_html( $menu_item->title );

		if ( empty( $menu_item->label ) && isset( $menu_item->post_type ) && 'page' === $menu_item->post_type ) {
			// Append post states.
			$output .= _post_states( $menu_item, false );
		}

		$output .= '</label>';

		// Menu item hidden fields.
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="' . esc_attr( $menu_item->object ) . '" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="' . esc_attr( $menu_item->menu_item_parent ) . '" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="' . esc_attr( $menu_item->type ) . '" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="' . esc_attr( $menu_item->title ) . '" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="' . esc_attr( $menu_item->url ) . '" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $possible_object_id . '][menu-item-target]" value="' . esc_attr( $menu_item->target ) . '" />';
		$output .= '<input type="hidden" class="menu-item-attr-title" name="menu-item[' . $possible_object_id . '][menu-item-attr-title]" value="' . esc_attr( $menu_item->attr_title ) . '" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $possible_object_id . '][menu-item-classes]" value="' . esc_attr( implode( ' ', $menu_item->classes ) ) . '" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $possible_object_id . '][menu-item-xfn]" value="' . esc_attr( $menu_item->xfn ) . '" />';
	}

}
