<?php
/**
 * Navigation Menu functions
 *
 * @package WordPress
 * @subpackage Navigation Menus
 * @since 3.0.0
 */

/**
 * Returns a Navigation Menu object
 *
 * @since 3.0.0
 *
 * @param string $menu Menu id
 * @return mixed $menu|false
 */
function wp_get_nav_menu_object( $menu ) {
	return is_nav_menu( $menu );
}

/**
 * Check if Menu exists.
 *
 * Returns the menu object, or false if the term doesn't exist.
 *
 * @since 3.0.0
 *
 * @param int|string $menu The menu to check
 * @return mixed Menu Object, if exists.
 */
function is_nav_menu( $menu ) {
	if ( !$menu )
		return false;

	$menu_obj = get_term( $menu, 'nav_menu' );

	if ( !$menu_obj )
		$menu_obj = get_term_by( 'slug', $menu, 'nav_menu' );

	if ( !$menu_obj )
		$menu_obj = get_term_by( 'name', $menu, 'nav_menu' );

	if ( !$menu_obj ) {
		$menu_obj = false;
	}

	return $menu_obj;
}

/**
 * Returns all Navigation Menu objects.
 *
 * @since 3.0.0
 *
 * @return array menu objects
 */
function wp_get_nav_menus() {
	return get_terms( 'nav_menu', array( 'hide_empty' => false, 'orderby' => 'id' ) );
}

/**
 * Creates a navigation menu.
 *
 * Optional args:
 * slug - the url friendly version of the nav menu.
 *
 * @since 3.0.0
 *
 * @param string $menu_name Menu Name
 * @param string $args Optional.
 * @return mixed Menu object|WP_Error
 */
function wp_create_nav_menu( $menu_name, $args = array() ) {
	$menu_exists = get_term_by( 'name', $menu_name, 'nav_menu' );

	if ( $menu_exists )
		return new WP_Error( 'menu_exists', sprintf( __('A menu named &#8220;%s&#8221; already exists; please try another name.'), esc_html( $menu_exists->name ) ) );

	if ( isset($args['slug']) )
		$slug = $args['slug'];
	else
		$slug = $menu_name;

	$menu = wp_insert_term( $menu_name, 'nav_menu', array('slug' => $slug) );

	if ( is_wp_error($menu) )
		return $menu;

	return get_term( $menu['term_id'], 'nav_menu') ;
}

/**
 * Deletes a navigation menu.
 *
 * @since 3.0.0
 *
 * @param string $menu name|id|slug
 * @return bool true on success, else false.
 */
function wp_delete_nav_menu( $menu ) {
	$menu = wp_get_nav_menu_object( $menu );
	if ( !$menu  )
		return false;

	$menu_objects = get_objects_in_term( $menu->term_id, 'nav_menu' );
	if ( !empty( $menu_objects ) ) {
		foreach ( $menu_objects as $item ) {
			wp_delete_post( $item );
		}
	}
	wp_delete_term( $menu->term_id, 'nav_menu' );
}

/**
 * Creates a nav menu named 'Menu 1'.
 *
 * This function is called if the user doesn't have any nav menus.
 * It grabs the first 14 pages, and creates a new default menu.
 *
 * @since 3.0.0
 */
function wp_create_default_nav_menu() {
	$menu = wp_create_nav_menu( __('Menu 1'), array('slug' => 'menu-1') );

	if ( is_wp_error($menu) )
		return;

	$pages = get_pages( array('parent' => 0, 'number' => 15) );
	$counter = 1;
	foreach ( $pages as $page ) {
		$item = array( 'post_status' => 'publish', 'post_type' => 'nav_menu_item', 'menu_order' => $counter, 'tax_input' => array( 'nav_menu' => $menu->name), 'post_title' => addslashes($page->post_title) );
		$item_id = wp_insert_post($item, true);
		update_post_meta( $item_id, 'menu_type', 'page' );
		update_post_meta( $item_id, 'object_id', $page->ID );
		update_post_meta( $item_id, 'menu_new_window', 0 );
		$counter++;
	}
}

/**
 * Returns the menu items of a navigation menu.
 *
 * @since 3.0.0
 *
 * @param string $menu menu name, id, or slug
 * @param string $args 
 * @return mixed $items array of menu items, else false.
 */
function wp_get_nav_menu_items( $menu, $args = array() ) {
	$menu = wp_get_nav_menu_object( $menu );
	
	if ( !$menu )
		return false;
	
	$items = get_objects_in_term( $menu->term_id, 'nav_menu' );

	if ( ! empty( $items ) ) {
		$defaults = array( 'orderby' => 'menu_order', 'post_type' => 'nav_menu_item', 'post_status' => 'publish', 'output' => ARRAY_A, 'output_key' => 'menu_order' );
		$args = wp_parse_args( $args, $defaults );
		if ( count( $items ) > 1 )	
			$args['include'] = implode( ',', $items );
		else
			$args['include'] = $items[0];

		$items = get_posts( $args );

		if ( ARRAY_A == $args['output'] ) {
			$output = array();
			foreach ( $items as $item ) {
				$output[$item->$args['output_key']] = $item;
			}
			unset( $items );
			ksort( $output );
			return $output;
		}
	}
	return $items;
}

/**
 * Returns a menu item with all it's nessecary data populated.
 *
 * @since 3.0.0
 *
 * @param string $menu_item Menu item.
 * @param string $type item|category|page.
 * @param string $position position of the menu item.
 * @return $menu_item
 */
function wp_setup_nav_menu_item( $menu_item, $type = 'item', $position = 0 ) {
	global $parent_menu_order;

	if ( 'item' == $type ) {
		$menu_item->type = get_post_meta( $menu_item->ID, 'menu_type', true );
		$menu_item->object_id = get_post_meta( $menu_item->ID, 'object_id', true );
		$menu_item->target = ( get_post_meta( $menu_item->ID, 'menu_new_window', true ) ) ? 'target="_blank"' : '';
		if ( isset( $parent_menu_order[ $menu_item->post_parent ] ) )
			$menu_item->parent_item = $parent_menu_order[ $menu_item->post_parent ];
		else
			$menu_item->parent_item = 0;
	} elseif ( 'category' == $type ) {
		$menu_item->type = $type;
		$menu_item->object_id = $menu_item->term_id;
		$menu_item->ID = $menu_item->term_id;
		$menu_item->parent_item = $menu_item->parent;
		$menu_item->menu_order = $position;
	} elseif ( 'page' == $type ) {
		$menu_item->type = $type;
		$menu_item->object_id = $menu_item->ID;
		$menu_item->parent_item = $menu_item->post_parent;
		$menu_item->menu_order = $position;
	}

	switch ( $menu_item->type ) {
		case 'page' :
			$menu_item->link = get_page_link( $menu_item->object_id );

			if ( $menu_item->post_title == '' )
				$menu_item->title = get_the_title( $menu_item->object_id );
			else
				$menu_item->title = $menu_item->post_title;

			if ( $menu_item->post_content == '' )
				$menu_item->description = get_post_meta( $menu_item->ID, 'page-description', true );
			else
				$menu_item->description = $menu_item->post_content;
			$menu_item->append = _x('Page', 'menu nav item type');
			break;
		case 'category' :
			$menu_item->link = get_category_link( $menu_item->object_id );

			if ( empty($menu_item->post_title) ) {
				$title_raw = get_category( $menu_item->object_id );
				$menu_item->title =  $title_raw->cat_name;
			} else {
				$menu_item->title = $menu_item->post_title;
			}

			if ( empty($menu_item->post_content) )
				$menu_item->description = strip_tags( category_description( $menu_item->object_id ) );
			else
				$menu_item->description = $menu_item->post_content;
			$menu_item->append = _x('Category', 'menu nav item type');
			break;
		case 'custom' :
		default :
			$menu_item->link = esc_url_raw( get_post_meta( $menu_item->ID, 'menu_link', true ) );
			$menu_item->title =  $menu_item->post_title;
			$menu_item->description = $menu_item->post_content;
			$menu_item->append = _x('Custom', 'menu nav item type');
			break;
	}

	$menu_item->li_class = '';
	global $wp_query;
	
	if ( $menu_item->object_id == $wp_query->get_queried_object_id() )
		$menu_item->li_class = ' current_page_item';

	$menu_item->anchor_title = '';
/* @todo: update to use tax/post data

			//SET anchor title
			if (isset($wp_custom_nav_menu_items->custom_anchor_title)) {
				$anchor_title = $wp_custom_nav_menu_items->custom_anchor_title;
			}
			else {
				$anchor_title = $title;
			}

			if ($queried_id == $wp_custom_nav_menu_items->post_id) {
				$li_class = 'class="current_page_item"';
			}

			if (isset($wp_custom_nav_menu_items->new_window)) {
				if ($wp_custom_nav_menu_items->new_window > 0) {
					$target = 'target="_blank"';
				}
				else {
					$target = '';
				}
			}
*/

	return $menu_item;
}
?>
