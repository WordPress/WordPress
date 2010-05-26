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

		$classes = array( 'menu-item', 'menu-item-type-' . $item->type );
		if ( !empty($item->classes) )
			$classes = array_merge($classes, explode(' ', $item->classes) );

		if ( 'custom' != $item->type ) {
			$classes[] = 'menu-item-object-' . $item->object;
			$classes[] = 'menu-item-object-' . $item->type . '-' . $item->object_id;
			if ( 'post_type' == $item->type && 'page' == $item->object ) {
				// Back compat classes for pages to match wp_page_menu()
				$classes[] = 'page_item';
				$classes[] = 'page-item-' . $item->object_id;
				if ( ! empty( $item->classes ) ) {
					if ( in_array('current-menu-item', $classes) )
						$classes[] = 'current_page_item';
					if ( in_array('current-menu-parent', $classes) )
						$classes[] = 'current_page_parent';
					if ( in_array('current-menu-ancestor', $classes) )
						$classes[] = 'current_page_ancestor';
				}
			}
		} elseif ( 'custom' == $item->type && in_array('current-menu-item', $classes) && in_array('menu-item-home', $classes) ) {
			// Back compat for home limk to match wp_page_menu()
			$classes[] = 'current_page_item';
		}

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

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
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
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="'. esc_attr( $item->object_id ) .'" /> ';
		$output .= esc_html( $item->title ) .'</label>';

		// Menu item hidden fields
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="'. esc_attr( $item->object ) .'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="'. esc_attr( $item->menu_item_parent ) .'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="'. esc_attr( $item->type ) .'" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="'. esc_attr( $item->title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="'. esc_attr( $item->url ) .'" />';
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
 * menu - The menu that is desired.  Accepts (matching in order) id, slug, name. Defaults to blank.
 * menu_class - CSS class to use for the ul container of the menu list. Defaults to 'menu'.
 * container - Whether to wrap the ul, and what to wrap it with. Defaults to 'div'.
 * conatiner_class - the class that is applied to the container. Defaults to blank.
 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
 * before - Text before the link text.
 * after - Text after the link text.
 * link_before - Text before the link.
 * link_after - Text after the link.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 * depth - how many levels of the hierarchy are to be included.  0 means all.  Defaults to 0.
 * walker - allows a custom walker to be specified.
 * context - the context the menu is used in.
 * theme_location - the location in the theme to be used.  Must be registered with register_nav_menu() in order to be selectable by the user.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'menu_class' => 'menu', 'echo' => true,
	'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '',
	'depth' => 0, 'walker' => '', 'context' => 'frontend', 'theme_location' => '' );

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_nav_menu_args', $args );
	$args = (object) $args;

	// Get the nav menu based on the requested menu
	$menu = wp_get_nav_menu_object( $args->menu );

	// Get the nav menu based on the theme_location
	if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) )
		$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

	// get the first menu that has items if we still can't find a menu
	if ( ! $menu && !$args->theme_location ) {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			if ( $menu_items = wp_get_nav_menu_items($menu_maybe->term_id) ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}

	// If the menu exists, get its items.
	if ( $menu && ! is_wp_error($menu) && !isset($menu_items) )
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
	$container_allowedtags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );

	if ( in_array( $args->container, $container_allowedtags ) ) {
		$class = $args->container_class ? ' class="' . esc_attr($args->container_class) . '"' : ' class="menu-'. $menu->slug .'-container"';
		$nav_menu .= '<'. $args->container . $class .'>';
	}

	// Set up the $menu_item variables
	if ( 'frontend' == $args->context )
		_wp_menu_item_classes_by_context( $menu_items );

	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $key => $menu_item )
		$sorted_menu_items[$menu_item->menu_order] = $menu_item;
	
	unset($menu_items);

	$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
	unset($sorted_menu_items);

	// Attributes
	$attributes  = ' id="menu-' . $menu->slug . '"';
	$attributes .= $args->menu_class ? ' class="'. $args->menu_class .'"' : '';

	$nav_menu .= '<ul'. $attributes .'>';

	// Allow plugins to hook into the menu to add their own <li>'s
	$items = apply_filters( 'wp_nav_menu_items', $items, $args );
	$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );
	$nav_menu .= $items;
	unset($items);

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
 * Add the class property classes for the current frontend context, if applicable.
 *
 * @access private
 * @since 3.0
 *
 * @param array $menu_items The current menu item objects to which to add the class property information.
 */
function _wp_menu_item_classes_by_context( &$menu_items = array() ) {
	global $wp_query;

	$queried_object = $wp_query->get_queried_object();
	$queried_object_id = (int) $wp_query->queried_object_id;

	$active_object = '';
	$active_parent_item_ids = array();
	$active_parent_object_ids = array();
	$possible_object_parents = array();
	$home_page_id = (int) get_option( 'page_for_posts' );
	
	if ( $wp_query->is_singular && ! empty( $queried_object->post_type ) && ! is_post_type_hierarchical( $queried_object->post_type ) ) {
		foreach ( (array) get_object_taxonomies( $queried_object->post_type ) as $taxonomy ) {
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$terms = wp_get_object_terms( $queried_object_id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( is_array( $terms ) )
					$possible_object_parents = array_merge( $possible_object_parents, $terms );
			}
		}
	} elseif ( ! empty( $queried_object->post_type ) && is_post_type_hierarchical( $queried_object->post_type ) ) {
		_get_post_ancestors( $queried_object );
	}

	$possible_object_parents = array_filter( $possible_object_parents );

	foreach ( (array) $menu_items as $key => $menu_item ) {
		// if the menu item corresponds to a taxonomy term for the currently-queried non-hierarchical post object
		if ( $wp_query->is_singular && 'taxonomy' == $menu_item->type && in_array( $menu_item->object_id, $possible_object_parents ) ) {
			$active_parent_object_ids[] = (int) $menu_item->object_id;
			$active_parent_item_ids[] = (int) $menu_item->db_id;
			$active_object = $queried_object->post_type;
		
		// if the menu item corresponds to the currently-queried post or taxonomy object
		} elseif (
			$menu_item->object_id == $queried_object_id &&
			( 
				( ! empty( $home_page_id ) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id = $menu_item->object_id ) ||
				( 'post_type' == $menu_item->type && $wp_query->is_singular ) ||
				( 'taxonomy' == $menu_item->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ) ) 
			) 
		) {
			$menu_items[$key]->classes = trim( $menu_item->classes . ' ' . 'current-menu-item' );
			$active_parent_item_ids[] = (int) $menu_item->menu_item_parent;
			$active_parent_object_ids[] = (int) $menu_item->post_parent;
			$active_object = $menu_item->object;

		// if the menu item corresponds to the currently-requested URL
		} elseif ( 'custom' == $menu_item->object ) {
			$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$item_url = strpos( $menu_item->url, '#' ) ? substr( $menu_item->url, 0, strpos( $menu_item->url, '#' ) ) : $menu_item->url;
			if ( $item_url == $current_url ) {
				$menu_items[$key]->classes = trim( $menu_item->classes . ' ' . 'current-menu-item' );
				if ( untrailingslashit($current_url) == home_url() )
					$menu_items[$key]->classes .= ' menu-item-home';
				$active_parent_item_ids[] = (int) $menu_item->menu_item_parent;
				$active_parent_object_ids[] = (int) $menu_item->post_parent;
				$active_object = $menu_item->object;
			}
		}
	}

	$active_parent_item_ids = array_filter( array_unique( $active_parent_item_ids ) );
	$active_parent_object_ids = array_filter( array_unique( $active_parent_object_ids ) );

	// set parent's class
	foreach ( (array) $menu_items as $key => $parent_item ) {
		if ( 
			isset( $parent_item->type ) && 
			'post_type' == $parent_item->type && 
			! empty( $queried_object->post_type ) &&
			is_post_type_hierarchical( $queried_object->post_type ) && 
			in_array( $parent_item->object_id, $queried_object->ancestors )
		)
			$menu_items[$key]->classes = trim( $parent_item->classes . ' ' . 'current-' . $queried_object->post_type . '-ancestor current-menu-ancestor' );
		if ( in_array( $parent_item->db_id, $active_parent_item_ids ) )
			$menu_items[$key]->classes = trim( $parent_item->classes . ' ' . 'current-menu-parent' );
		if ( in_array( $parent_item->object_id, $active_parent_object_ids ) )
			$menu_items[$key]->classes = trim( $parent_item->classes . ' ' . 'current-' . $active_object . '-parent' );
	}
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
