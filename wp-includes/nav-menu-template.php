<?php
/**
 * Navigation Menu template functions
 *
 * @package WordPress
 * @subpackage Nav_Menus
 * @since 3.0.0
 */

/**
 * Create HTML list of nav menu items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker
 */
class Walker_Nav_Menu extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	/**
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$classes = $value = '';

		$classes = array( 'menu-item', 'menu-item-type-'. $item->type, $item->classes );

		if ( 'custom' == $item->object ) {
			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$item_url = strpos( $item->url, '#' ) ? substr( $item->url, 0, strpos( $item->url, '#' ) ) : $item->url;
			if ( $item_url == $current_url )
				$classes[] = 'current-menu-item';
		} else {
			$classes[] = 'menu-item-object-'. $item->object;
			if ( $item->object_id == $wp_query->get_queried_object_id() )
				$classes[] = 'current-menu-item';
		}

		// @todo add classes for parent/child relationships

		$classes = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$classes = ' class="' . esc_attr( $classes ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $classes .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'wp_get_nav_menu_item', $item_output, $args );
	}

	/**
	 * @see Walker::end_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el(&$output, $item, $depth) {
		$output .= "</li>\n";
	}
}

/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Checklist extends Walker_Nav_Menu  {

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $_nav_menu_placeholder;

		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
		$possible_object_id = isset( $item->post_type ) && 'nav_menu_item' == $item->post_type ? $item->object_id : $_nav_menu_placeholder;
		$possible_db_id = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<ul class="potential-menu-item hide-if-no-js"><li><div>';
		$output .= '<span class="item-title">' . esc_html( $item->title ) . '</span>';
		$output .= '<span class="item-controls">';
		$output .= '<span class="item-type">' . esc_html( $item->append ) . '</span>';
		$output .= '<span class="item-edit">';
		$output .= '<img class="waiting" src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" />';
		$output .= '</span></span>';
		$output .= '</div><ul class="additional-menu-items"></ul><ul class="menu-item-transport"></ul></li></ul>';
		$output .= '<label class="menu-item-title hide-if-js">';
		$output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="'. esc_attr( $item->object_id ) .'" /> ';
		$output .= esc_html( $item->title ) .'</label>';

		// Menu item hidden fields
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="'. esc_attr( $item->object ) .'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="'. esc_attr( $item->menu_item_parent ) .'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="'. esc_attr( $item->type ) .'" />';
		$output .= '<input type="hidden" class="menu-item-append" name="menu-item[' . $possible_object_id . '][menu-item-append]" value="'. esc_attr( $item->append ) .'" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="'. esc_attr( $item->title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="'. esc_attr( $item->url ) .'" />';
		$output .= '<input type="hidden" class="menu-item-append" name="menu-item[' . $possible_object_id . '][menu-item-append]" value="'. esc_attr( $item->append ) .'" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $possible_object_id . '][menu-item-target]" value="'. esc_attr( $item->target ) .'" />';
		$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item[' . $possible_object_id . '][menu-item-attr_title]" value="'. esc_attr( $item->attr_title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-description" name="menu-item[' . $possible_object_id . '][menu-item-description]" value="'. esc_attr( $item->description ) .'" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $possible_object_id . '][menu-item-classes]" value="'. esc_attr( $item->classes ) .'" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $possible_object_id . '][menu-item-xfn]" value="'. esc_attr( $item->xfn ) .'" />';
	}
}

/**
 * Displays a navigation menu.
 *
 * Optional $args contents:
 *
 * id - The menu id. Defaults to blank.
 * slug - The menu slug. Defaults to blank.
 * menu_class - CSS class to use for the div container of the menu list. Defaults to 'menu'.
 * format - Whether to format the ul. Defaults to 'div'.
 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
 * before - Text before the link text.
 * after - Text after the link text.
 * link_before - Text before the link.
 * link_after - Text after the link.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 *
 * @todo show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'menu_class' => 'menu', 'echo' => true,
	'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '',
	'depth' => 0, 'walker' => '', 'context' => 'frontend' );

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_nav_menu_args', $args );
	$args = (object) $args;

	// Get the nav menu
	$menu = wp_get_nav_menu_object( $args->menu );

	// If we couldn't find a menu based off the name, id or slug,
	// get the first menu that has items.
	if ( ! $menu ) {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			if ( wp_get_nav_menu_items($menu_maybe->term_id) ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}

	// If the menu exists, get its items.
	if ( $menu && ! is_wp_error($menu) )
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

	// If no menu was found or if the menu has no items, call the fallback_cb
	if ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) ) ) {
		if ( 'frontend' == $args->context && ( function_exists($args->fallback_cb) || is_callable( $args->fallback_cb ) ) ) {
			return call_user_func( $args->fallback_cb, (array) $args );
		}
	}

	// If no fallback function was specified and the menu doesn't exists, bail.
	if ( !$menu || is_wp_error($menu) )
		return false;

	$nav_menu = '';
	$items = '';
	$container_allowedtags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'p', 'nav' ) );

	if ( in_array( $args->container, $container_allowedtags ) ) {
		$class = $args->container_class ? ' class="' . esc_attr($args->container_class) . '"' : ' class="menu-'. $menu->slug .'-container"';
		$nav_menu .= '<'. $args->container . $class .'>';
	}

	// Set up the $menu_item variables
	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $key => $menu_item )
		$sorted_menu_items[$menu_item->menu_order] = wp_setup_nav_menu_item( $menu_item );

	$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );

	// Attributes
	$attributes  = ' id="menu-' . $menu->slug . '"';
	$attributes .= $args->menu_class ? ' class="'. $args->menu_class .'"' : '';

	$nav_menu .= '<ul'. $attributes .'>';

	// Allow plugins to hook into the menu to add their own <li>'s
	if ( 'frontend' == $args->context ) {
		$items = apply_filters( 'wp_nav_menu_items', $items, $args );
		$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );
		$nav_menu .= $items;
	} else {
		$nav_menu .= $items;
	}

	$nav_menu .= '</ul>';

	if ( in_array( $args->container, $container_allowedtags ) )
		$nav_menu .= '</'. $args->container .'>';

	$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

	if ( $args->echo )
		echo $nav_menu;
	else
		return $nav_menu;
}

/**
 * Retrieve the HTML list content for nav menu items.
 *
 * @uses Walker_Nav_Menu to create HTML list content.
 * @since 2.1.0
 * @see Walker::walk() for parameters and return description.
 */
function walk_nav_menu_tree( $items, $depth, $r ) {
	$walker = ( empty($r->walker) ) ? new Walker_Nav_Menu : $r->walker;
	$args = array( $items, $depth, $r );

	return call_user_func_array( array(&$walker, 'walk'), $args );
}

?>
