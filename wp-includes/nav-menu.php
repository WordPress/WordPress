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
 * Returns all menu items of a navigation menu.
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
 * Adds all the nav menu properties to the $menu_item.
 *
 * @since 3.0.0
 *
 * @param string $menu_item The menu item to modify
 * @param string $menu_item_type The menu item type (template, custom, post_type, taxonomy).
 * @param string $menu_item_object Optional. The menu item object type (post type or taxonomy).
 * @return object $menu_item The modtified menu item.
 */
function wp_setup_nav_menu_item( $menu_item, $menu_item_type = null, $menu_item_object = '' ) {
	global $wp_query;
	
	switch ( $menu_item_type ) {
		case 'frontend':
			$menu_item->db_id = (int) $menu_item->ID;
			$menu_item->object_id = get_post_meta( $menu_item->ID, 'menu_item_object_id', true );
			$menu_item->parent_id = (int) $menu_item->post_parent;
			$menu_item->type = get_post_meta( $menu_item->ID, 'menu_item_type', true );
			$menu_item->append = _x( get_post_meta( $menu_item->ID, 'menu_item_append', true ), 'nav menu item type' );
			
			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_post_meta( $menu_item->ID, 'menu_item_url', true );
			$menu_item->target = get_post_meta( $menu_item->ID, 'menu_item_target', true );
			
			$menu_item->attr_title = strip_tags( $menu_item->post_excerpt );
			$menu_item->description = strip_tags( $menu_item->post_content );
			
			$menu_item->classes = get_post_meta( $menu_item->ID, 'menu_item_classes', true );;
			$menu_item->xfn = get_post_meta( $menu_item->ID, 'menu_item_xfn', true );
			$menu_item->li_class = ( $menu_item->object_id == $wp_query->get_queried_object_id() ) ? ' current_page_item' : '';
			break;
			
		case 'custom':
			$menu_item->db_id = (int) $menu_item->ID;
			$menu_item->object_id = (int) $menu_item->ID;
			$menu_item->parent_id = (int) $menu_item->post_parent;
			$menu_item->type = 'custom'; //$menu_item_type
			$menu_item->append = _x( 'Custom', 'nav menu item type' );
			
			$menu_item->attr_title = strip_tags( $menu_item->post_excerpt );
			$menu_item->description = strip_tags( $menu_item->post_content );

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_post_meta( $menu_item->ID, 'menu_item_url', true );
			$menu_item->target = get_post_meta( $menu_item->ID, 'menu_item_target', true );
			break;
			
		case 'post_type':
			$menu_item->db_id = 0;
			$menu_item->object_id = (int) $menu_item->ID;
			$menu_item->parent_id = (int) $menu_item->post_parent;
			$menu_item->type = $menu_item_type;
			
			$object = get_post_type_object( $menu_item_object );
			$menu_item->append = _x( $object->singular_label, 'nav menu item type' );

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_permalink( $menu_item->ID );
			$menu_item->target = '_self';
			
			$menu_item->attr_title = '';
			$menu_item->description = strip_tags( $menu_item->post_content );
			break;
			
		case 'taxonomy':
			$menu_item->ID = $menu_item->term_id;
			$menu_item->db_id = 0;
			$menu_item->object_id = (int) $menu_item->term_id;
			$menu_item->parent_id = (int) $menu_item->parent;
			$menu_item->type = $menu_item_type;
			
			$object = get_taxonomy( $menu_item_object );
			$menu_item->append = _x( $object->singular_label, 'nav menu item type' );

			$menu_item->title = $menu_item->name;
			$menu_item->url = get_term_link( $menu_item, $menu_item_object );
			$menu_item->target = '_self';
			$menu_item->attr_title = '';
			$menu_item->description = strip_tags( $menu_item->description );
			break;
	}
	
	$menu_item->classes = get_post_meta( $menu_item->ID, 'menu_item_classes', true );
	$menu_item->xfn = get_post_meta( $menu_item->ID, 'menu_item_xfn', true );
	
	return $menu_item;
}
?>