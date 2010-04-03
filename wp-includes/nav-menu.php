<?php
/**
 * Navigation Menu functions
 *
 * @package WordPress
 * @subpackage Nav_Menus
 * @since 3.0.0
 */

/**
 * Returns a navigation menu object.
 *
 * @since 3.0.0
 *
 * @param string $menu Menu id
 * @return mixed $menu|false Or WP_Error
 */
function wp_get_nav_menu_object( $menu ) {
	return is_nav_menu( $menu );
}

/**
 * Check if navigation menu exists.
 *
 * Returns the menu object, or false if the term doesn't exist.
 *
 * @since 3.0.0
 *
 * @param int|string $menu The menu to check
 * @return mixed Menu Object, if it exists. Else, false or WP_Error
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
 * Create a Navigation Menu.
 *
 * Optional args:
 * slug - the url friendly version of the nav menu.
 *
 * @since 3.0.0
 *
 * @param string $menu_name Menu Name
 * @param string $args Optional.
 * @return mixed Menu object on sucess|WP_Error on failure
 */
function wp_create_nav_menu( $menu_name, $args = array() ) {
	$menu_exists = get_term_by( 'name', $menu_name, 'nav_menu' );

	if ( $menu_exists )
		return new WP_Error( 'menu_exists', sprintf( __('A menu named <strong>%s</strong> already exists; please try another name.'), esc_html( $menu_exists->name ) ) );

	if ( isset($args['slug']) )
		$slug = $args['slug'];
	else
		$slug = $menu_name;

	$menu = wp_insert_term( $menu_name, 'nav_menu', array('slug' => $slug) );

	if ( is_wp_error($menu) )
		return $menu;

	$result = get_term( $menu['term_id'], 'nav_menu' );

	if ( $result && !is_wp_error($result) ) {
		do_action( 'wp_create_nav_menu', $menu['term_id'] );
		return $result;
	} else {
		return $result;
	}
}

/**
 * Delete a Navigation Menu.
 *
 * @since 3.0.0
 *
 * @param string $menu name|id|slug
 * @return mixed Menu object on sucess|WP_Error on failure
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

	$result = wp_delete_term( $menu->term_id, 'nav_menu' );

	if ( $result && !is_wp_error($result) ) {
		do_action( 'wp_delete_nav_menu', $menu->term_id );
		return $result;
	} else {
		return $result;
	}
}

/**
 * Returns all navigation menu objects.
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
 * Retrieve the HTML list content for nav menu items.
 *
 * @uses Walker_Nav_Menu to create HTML list content.
 * @since 2.1.0
 * @see Walker::walk() for parameters and return description.
 */
function walk_nav_menu_tree( $items, $depth, $r ) {
	$walker = ( empty($r->walker) ) ? new Walker_Nav_Menu : $r->walker;
	$args = array( $items, $depth, $r );

	return call_user_func_array(array(&$walker, 'walk'), $args);
}

/**
 * Adds all the navigation menu properties to the menu item.
 *
 * @since 3.0.0
 *
 * @param string $menu_item The menu item to modify
 * @param string $menu_item_type The menu item type (frontend, custom, post_type, taxonomy).
 * @param string $menu_item_object Optional. The menu item object type (post type or taxonomy).
 * @return object $menu_item The modified menu item.
 */
function wp_setup_nav_menu_item( $menu_item, $menu_item_type = null, $menu_item_object = '' ) {
	switch ( $menu_item_type ) {
		case 'frontend':
			$menu_item->db_id = (int) $menu_item->ID;
			$menu_item->object_id = get_post_meta( $menu_item->ID, '_menu_item_object_id', true );
			$menu_item->object = get_post_meta( $menu_item->ID, '_menu_item_object', true );
			$menu_item->type = get_post_meta( $menu_item->ID, '_menu_item_type', true );

			if ( 'post_type' == $menu_item->type ) {
				$object = get_post_type_object( $menu_item->object );
				$menu_item->append = $object->singular_label;

			} elseif ( 'taxonomy' == $menu_item->type ) {
				$object = get_taxonomy( $menu_item->object );
				$menu_item->append = $object->singular_label;

			} else {
				$menu_item->append = __('Custom');
			}

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_post_meta( $menu_item->ID, '_menu_item_url', true );
			$menu_item->target = get_post_meta( $menu_item->ID, '_menu_item_target', true );

			$menu_item->attr_title = strip_tags( $menu_item->post_excerpt );
			$menu_item->description = strip_tags( $menu_item->post_content );

			$menu_item->classes = get_post_meta( $menu_item->ID, '_menu_item_classes', true );
			$menu_item->xfn = get_post_meta( $menu_item->ID, '_menu_item_xfn', true );
			break;

		case 'custom':
			$menu_item->db_id = 0;
			$menu_item->object_id = (int) $menu_item->ID;
			$menu_item->object = 'custom';
			$menu_item->type = 'custom';
			$menu_item->append = __('custom');

			$menu_item->attr_title = strip_tags( $menu_item->post_excerpt );
			$menu_item->description = strip_tags( $menu_item->post_content );

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_post_meta( $menu_item->ID, '_menu_item_url', true );
			$menu_item->target = get_post_meta( $menu_item->ID, '_menu_item_target', true );
			$menu_item->classes = '';
			$menu_item->xfn = '';
			break;

		case 'post_type':
			$menu_item->db_id = 0;
			$menu_item->object_id = (int) $menu_item->ID;
			$menu_item->type = $menu_item_type;

			$object = get_post_type_object( $menu_item_object );
			$menu_item->object = $object->name;
			$menu_item->append = strtolower( $object->singular_label );

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_permalink( $menu_item->ID );
			$menu_item->target = '';

			$menu_item->attr_title = '';
			$menu_item->description = strip_tags( $menu_item->post_content );
			$menu_item->classes = '';
			$menu_item->xfn = '';
			break;

		case 'taxonomy':
			$menu_item->ID = $menu_item->term_id;
			$menu_item->db_id = 0;
			$menu_item->object_id = (int) $menu_item->term_id;
			$menu_item->post_parent = (int) $menu_item->parent;
			$menu_item->type = $menu_item_type;

			$object = get_taxonomy( $menu_item_object );
			$menu_item->object = $object->name;
			$menu_item->append = strtolower( $object->singular_label );

			$menu_item->title = $menu_item->name;
			$menu_item->url = get_term_link( $menu_item, $menu_item_object );
			$menu_item->target = '';
			$menu_item->attr_title = '';
			$menu_item->description = strip_tags( $menu_item->description );
			$menu_item->classes = '';
			$menu_item->xfn = '';
			break;
	}
	return $menu_item;
}
?>
