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

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
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
 * Displays a navigation menu.
 *
 * Optional $args contents:
 *
 * menu - The menu that is desired.  Accepts (matching in order) id, slug, name. Defaults to blank.
 * menu_class - CSS class to use for the ul element which forms the menu. Defaults to 'menu'.
 * menu_id - The ID that is applied to the ul element which forms the menu. Defaults to the menu slug, incremented.
 * container - Whether to wrap the ul, and what to wrap it with. Defaults to 'div'.
 * container_class - the class that is applied to the container. Defaults to 'menu-{menu slug}-container'.
 * container_id - The ID that is applied to the container. Defaults to blank.
 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'. Set to false for no fallback.
 * before - Text before the link text.
 * after - Text after the link text.
 * link_before - Text before the link.
 * link_after - Text after the link.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 * depth - how many levels of the hierarchy are to be included.  0 means all.  Defaults to 0.
 * walker - allows a custom walker to be specified.
 * theme_location - the location in the theme to be used.  Must be registered with register_nav_menu() in order to be selectable by the user.
 * items_wrap - How the list items should be wrapped. Defaults to a ul with an id and class. Uses printf() format with numbered placeholders.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	static $menu_id_slugs = array();

	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
	'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
	'depth' => 0, 'walker' => '', 'theme_location' => '' );

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

	// If no menu was found or if the menu has no items and no location was requested, call the fallback_cb if it exists
	if ( ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) && !$args->theme_location ) )
		&& $args->fallback_cb && is_callable( $args->fallback_cb ) )
			return call_user_func( $args->fallback_cb, (array) $args );

	// If no fallback function was specified and the menu doesn't exists, bail.
	if ( !$menu || is_wp_error($menu) )
		return false;

	$nav_menu = $items = '';

	$show_container = false;
	if ( $args->container ) {
		$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
		if ( in_array( $args->container, $allowed_tags ) ) {
			$show_container = true;
			$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
			$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
			$nav_menu .= '<'. $args->container . $id . $class . '>';
		}
	}

	// Set up the $menu_item variables
	_wp_menu_item_classes_by_context( $menu_items );

	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $key => $menu_item )
		$sorted_menu_items[$menu_item->menu_order] = $menu_item;

	unset($menu_items);

	$sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

	$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
	unset($sorted_menu_items);

	// Attributes
	if ( ! empty( $args->menu_id ) ) {
		$slug = $args->menu_id;
	} else {
		$slug = 'menu-' . $menu->slug;
		while ( in_array( $slug, $menu_id_slugs ) ) {
			if ( preg_match( '#-(\d+)$#', $slug, $matches ) )
				$slug = preg_replace('#-(\d+)$#', '-' . ++$matches[1], $slug);
			else
				$slug = $slug . '-1';
		}
	}
	$menu_id_slugs[] = $slug;
	
	$wrap_class = $args->menu_class ? $args->menu_class : '';

	// Allow plugins to hook into the menu to add their own <li>'s
	$items = apply_filters( 'wp_nav_menu_items', $items, $args );
	$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );
	
	$nav_menu .= sprintf( $args->items_wrap, $slug, $wrap_class, $items );
	unset($items);

	if ( $show_container )
		$nav_menu .= '</' . $args->container . '>';

	$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

	if ( $args->echo )
		echo $nav_menu;
	else
		return $nav_menu;
}

/**
 * Add the class property classes for the current context, if applicable.
 *
 * @access private
 * @since 3.0
 *
 * @param array $menu_items The current menu item objects to which to add the class property information.
 */
function _wp_menu_item_classes_by_context( &$menu_items ) {
	global $wp_query;

	$queried_object = $wp_query->get_queried_object();
	$queried_object_id = (int) $wp_query->queried_object_id;

	$active_object = '';
	$active_ancestor_item_ids = array();
	$active_parent_item_ids = array();
	$active_parent_object_ids = array();
	$possible_taxonomy_ancestors = array();
	$possible_object_parents = array();
	$home_page_id = (int) get_option( 'page_for_posts' );

	if ( $wp_query->is_singular && ! empty( $queried_object->post_type ) && ! is_post_type_hierarchical( $queried_object->post_type ) ) {
		foreach ( (array) get_object_taxonomies( $queried_object->post_type ) as $taxonomy ) {
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$term_hierarchy = _get_term_hierarchy( $taxonomy );
				$terms = wp_get_object_terms( $queried_object_id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( is_array( $terms ) ) {
					$possible_object_parents = array_merge( $possible_object_parents, $terms );
					$term_to_ancestor = array();
					foreach ( (array) $term_hierarchy as $anc => $descs ) {
						foreach ( (array) $descs as $desc )
							$term_to_ancestor[ $desc ] = $anc;
					}

					foreach ( $terms as $desc ) {
						do {
							$possible_taxonomy_ancestors[ $taxonomy ][] = $desc;
							if ( isset( $term_to_ancestor[ $desc ] ) ) {
								$_desc = $term_to_ancestor[ $desc ];
								unset( $term_to_ancestor[ $desc ] );
								$desc = $_desc;
							} else {
								$desc = 0;
							}
						} while ( ! empty( $desc ) );
					}
				}
			}
		}
	} elseif ( ! empty( $queried_object->post_type ) && is_post_type_hierarchical( $queried_object->post_type ) ) {
		_get_post_ancestors( $queried_object );
	} elseif ( ! empty( $queried_object->taxonomy ) && is_taxonomy_hierarchical( $queried_object->taxonomy ) ) {
		$term_hierarchy = _get_term_hierarchy( $queried_object->taxonomy );
		$term_to_ancestor = array();
		foreach ( (array) $term_hierarchy as $anc => $descs ) {
			foreach ( (array) $descs as $desc )
				$term_to_ancestor[ $desc ] = $anc;
		}
		$desc = $queried_object->term_id;
		do {
			$possible_taxonomy_ancestors[ $queried_object->taxonomy ][] = $desc;
			if ( isset( $term_to_ancestor[ $desc ] ) ) {
				$_desc = $term_to_ancestor[ $desc ];
				unset( $term_to_ancestor[ $desc ] );
				$desc = $_desc;
			} else {
				$desc = 0;
			}
		} while ( ! empty( $desc ) );
	}

	$possible_object_parents = array_filter( $possible_object_parents );

	foreach ( (array) $menu_items as $key => $menu_item ) {

		$menu_items[$key]->current = false;

		$classes = (array) $menu_item->classes;
		$classes[] = 'menu-item';
		$classes[] = 'menu-item-type-' . $menu_item->type;
		$classes[] = 'menu-item-object-' . $menu_item->object;

		// if the menu item corresponds to a taxonomy term for the currently-queried non-hierarchical post object
		if ( $wp_query->is_singular && 'taxonomy' == $menu_item->type && in_array( $menu_item->object_id, $possible_object_parents ) ) {
			$active_parent_object_ids[] = (int) $menu_item->object_id;
			$active_parent_item_ids[] = (int) $menu_item->db_id;
			$active_object = $queried_object->post_type;

		// if the menu item corresponds to the currently-queried post or taxonomy object
		} elseif (
			$menu_item->object_id == $queried_object_id &&
			(
				( ! empty( $home_page_id ) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id == $menu_item->object_id ) ||
				( 'post_type' == $menu_item->type && $wp_query->is_singular ) ||
				( 'taxonomy' == $menu_item->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ) )
			)
		) {
			$classes[] = 'current-menu-item';
			$menu_items[$key]->current = true;
			$_anc_id = (int) $menu_item->db_id;

			while(
				( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) &&
				! in_array( $_anc_id, $active_ancestor_item_ids )
			) {
				$active_ancestor_item_ids[] = $_anc_id;
			}

			if ( 'post_type' == $menu_item->type && 'page' == $menu_item->object ) {
				// Back compat classes for pages to match wp_page_menu()
				$classes[] = 'page_item';
				$classes[] = 'page-item-' . $menu_item->object_id;
				$classes[] = 'current_page_item';
			}
			$active_parent_item_ids[] = (int) $menu_item->menu_item_parent;
			$active_parent_object_ids[] = (int) $menu_item->post_parent;
			$active_object = $menu_item->object;

		// if the menu item corresponds to the currently-requested URL
		} elseif ( 'custom' == $menu_item->object ) {
			$current_url = untrailingslashit( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$item_url = untrailingslashit( strpos( $menu_item->url, '#' ) ? substr( $menu_item->url, 0, strpos( $menu_item->url, '#' ) ) : $menu_item->url );
			$_indexless_current = untrailingslashit( preg_replace( '/index.php$/', '', $current_url ) );

			if ( in_array( $item_url, array( $current_url, $_indexless_current ) ) ) {
				$classes[] = 'current-menu-item';
				$menu_items[$key]->current = true;
				$_anc_id = (int) $menu_item->db_id;

				while(
					( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) &&
					! in_array( $_anc_id, $active_ancestor_item_ids )
				) {
					$active_ancestor_item_ids[] = $_anc_id;
				}

				if ( in_array( home_url(), array( untrailingslashit( $current_url ), untrailingslashit( $_indexless_current ) ) ) ) {
					// Back compat for home link to match wp_page_menu()
					$classes[] = 'current_page_item';
				}
				$active_parent_item_ids[] = (int) $menu_item->menu_item_parent;
				$active_parent_object_ids[] = (int) $menu_item->post_parent;
				$active_object = $menu_item->object;
			}

			if ( untrailingslashit($item_url) == home_url() )
				$classes[] = 'menu-item-home';
		}

		// back-compat with wp_page_menu: add "current_page_parent" to static home page link for any non-page query
		if ( ! empty( $home_page_id ) && 'post_type' == $menu_item->type && empty( $wp_query->is_page ) && $home_page_id == $menu_item->object_id )
			$classes[] = 'current_page_parent';

		$menu_items[$key]->classes = array_unique( $classes );
	}
	$active_ancestor_item_ids = array_filter( array_unique( $active_ancestor_item_ids ) );
	$active_parent_item_ids = array_filter( array_unique( $active_parent_item_ids ) );
	$active_parent_object_ids = array_filter( array_unique( $active_parent_object_ids ) );

	// set parent's class
	foreach ( (array) $menu_items as $key => $parent_item ) {
		$classes = (array) $parent_item->classes;
		$menu_items[$key]->current_item_ancestor = false;
		$menu_items[$key]->current_item_parent = false;

		if (
			isset( $parent_item->type ) &&
			(
				// ancestral post object
				(
					'post_type' == $parent_item->type &&
					! empty( $queried_object->post_type ) &&
					is_post_type_hierarchical( $queried_object->post_type ) &&
					in_array( $parent_item->object_id, $queried_object->ancestors )
				) ||

				// ancestral term
				(
					'taxonomy' == $parent_item->type &&
					isset( $possible_taxonomy_ancestors[ $parent_item->object ] ) &&
					in_array( $parent_item->object_id, $possible_taxonomy_ancestors[ $parent_item->object ] )
				)
			)
		) {
			$classes[] = empty( $queried_object->taxonomy ) ? 'current-' . $queried_object->post_type . '-ancestor' : 'current-' . $queried_object->taxonomy . '-ancestor';
		}

		if ( in_array(  intval( $parent_item->db_id ), $active_ancestor_item_ids ) ) {
			$classes[] = 'current-menu-ancestor';
			$menu_items[$key]->current_item_ancestor = true;
		}
		if ( in_array( $parent_item->db_id, $active_parent_item_ids ) ) {
			$classes[] = 'current-menu-parent';
			$menu_items[$key]->current_item_parent = true;
		}
		if ( in_array( $parent_item->object_id, $active_parent_object_ids ) )
			$classes[] = 'current-' . $active_object . '-parent';

		if ( 'post_type' == $parent_item->type && 'page' == $parent_item->object ) {
			// Back compat classes for pages to match wp_page_menu()
			if ( in_array('current-menu-parent', $classes) )
				$classes[] = 'current_page_parent';
			if ( in_array('current-menu-ancestor', $classes) )
				$classes[] = 'current_page_ancestor';
		}

		$menu_items[$key]->classes = array_unique( $classes );
	}
}

/**
 * Retrieve the HTML list content for nav menu items.
 *
 * @uses Walker_Nav_Menu to create HTML list content.
 * @since 3.0.0
 * @see Walker::walk() for parameters and return description.
 */
function walk_nav_menu_tree( $items, $depth, $r ) {
	$walker = ( empty($r->walker) ) ? new Walker_Nav_Menu : $r->walker;
	$args = array( $items, $depth, $r );

	return call_user_func_array( array(&$walker, 'walk'), $args );
}

/**
 * Prevents a menu item ID from being used more than once.
 *
 * @since 3.0.1
 * @access private
 */
function _nav_menu_item_id_use_once( $id, $item ) {
	static $_used_ids = array();
	if ( in_array( $item->ID, $_used_ids ) )
		return '';
	$_used_ids[] = $item->ID;
	return $id;
}
add_filter( 'nav_menu_item_id', '_nav_menu_item_id_use_once', 10, 2 );