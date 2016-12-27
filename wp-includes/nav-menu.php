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
 * @param int|string|WP_Term $menu Menu ID, slug, or name - or the menu object.
 * @return WP_Term|false False if $menu param isn't supplied or term does not exist, menu object if successful.
 */
function wp_get_nav_menu_object( $menu ) {
	$menu_obj = false;

	if ( is_object( $menu ) ) {
		$menu_obj = $menu;
	}

	if ( $menu && ! $menu_obj ) {
		$menu_obj = get_term( $menu, 'nav_menu' );

		if ( ! $menu_obj ) {
			$menu_obj = get_term_by( 'slug', $menu, 'nav_menu' );
		}

		if ( ! $menu_obj ) {
			$menu_obj = get_term_by( 'name', $menu, 'nav_menu' );
		}
	}

	if ( ! $menu_obj || is_wp_error( $menu_obj ) ) {
		$menu_obj = false;
	}

	/**
	 * Filters the nav_menu term retrieved for wp_get_nav_menu_object().
	 *
	 * @since 4.3.0
	 *
	 * @param object|false $menu_obj Term from nav_menu taxonomy, or false if nothing had been found.
	 * @param string       $menu     The menu ID, slug, or name passed to wp_get_nav_menu_object().
	 */
	return apply_filters( 'wp_get_nav_menu_object', $menu_obj, $menu );
}

/**
 * Check if the given ID is a navigation menu.
 *
 * Returns true if it is; false otherwise.
 *
 * @since 3.0.0
 *
 * @param int|string $menu The menu to check (ID, slug, or name).
 * @return bool Whether the menu exists.
 */
function is_nav_menu( $menu ) {
	if ( ! $menu )
		return false;

	$menu_obj = wp_get_nav_menu_object( $menu );

	if (
		$menu_obj &&
		! is_wp_error( $menu_obj ) &&
		! empty( $menu_obj->taxonomy ) &&
		'nav_menu' == $menu_obj->taxonomy
	)
		return true;

	return false;
}

/**
 * Registers navigation menu locations for a theme.
 *
 * @since 3.0.0
 *
 * @global array $_wp_registered_nav_menus
 *
 * @param array $locations Associative array of menu location identifiers (like a slug) and descriptive text.
 */
function register_nav_menus( $locations = array() ) {
	global $_wp_registered_nav_menus;

	add_theme_support( 'menus' );

	$_wp_registered_nav_menus = array_merge( (array) $_wp_registered_nav_menus, $locations );
}

/**
 * Unregisters a navigation menu location for a theme.
 *
 * @since 3.1.0
 * @global array $_wp_registered_nav_menus
 *
 * @param string $location The menu location identifier.
 * @return bool True on success, false on failure.
 */
function unregister_nav_menu( $location ) {
	global $_wp_registered_nav_menus;

	if ( is_array( $_wp_registered_nav_menus ) && isset( $_wp_registered_nav_menus[$location] ) ) {
		unset( $_wp_registered_nav_menus[$location] );
		if ( empty( $_wp_registered_nav_menus ) ) {
			_remove_theme_support( 'menus' );
		}
		return true;
	}
	return false;
}

/**
 * Registers a navigation menu location for a theme.
 *
 * @since 3.0.0
 *
 * @param string $location    Menu location identifier, like a slug.
 * @param string $description Menu location descriptive text.
 */
function register_nav_menu( $location, $description ) {
	register_nav_menus( array( $location => $description ) );
}
/**
 * Retrieves all registered navigation menu locations in a theme.
 *
 * @since 3.0.0
 *
 * @global array $_wp_registered_nav_menus
 *
 * @return array Registered navigation menu locations. If none are registered, an empty array.
 */
function get_registered_nav_menus() {
	global $_wp_registered_nav_menus;
	if ( isset( $_wp_registered_nav_menus ) )
		return $_wp_registered_nav_menus;
	return array();
}

/**
 * Retrieves all registered navigation menu locations and the menus assigned to them.
 *
 * @since 3.0.0
 *
 * @return array Registered navigation menu locations and the menus assigned them.
 *               If none are registered, an empty array.
 */

function get_nav_menu_locations() {
	$locations = get_theme_mod( 'nav_menu_locations' );
	return ( is_array( $locations ) ) ? $locations : array();
}

/**
 * Determines whether a registered nav menu location has a menu assigned to it.
 *
 * @since 3.0.0
 *
 * @param string $location Menu location identifier.
 * @return bool Whether location has a menu.
 */
function has_nav_menu( $location ) {
	$has_nav_menu = false;

	$registered_nav_menus = get_registered_nav_menus();
	if ( isset( $registered_nav_menus[ $location ] ) ) {
		$locations = get_nav_menu_locations();
		$has_nav_menu = ! empty( $locations[ $location ] );
	}

	/**
	 * Filters whether a nav menu is assigned to the specified location.
	 *
	 * @since 4.3.0
	 *
	 * @param bool   $has_nav_menu Whether there is a menu assigned to a location.
	 * @param string $location     Menu location.
	 */
	return apply_filters( 'has_nav_menu', $has_nav_menu, $location );
}

/**
 * Determines whether the given ID is a nav menu item.
 *
 * @since 3.0.0
 *
 * @param int $menu_item_id The ID of the potential nav menu item.
 * @return bool Whether the given ID is that of a nav menu item.
 */
function is_nav_menu_item( $menu_item_id = 0 ) {
	return ( ! is_wp_error( $menu_item_id ) && ( 'nav_menu_item' == get_post_type( $menu_item_id ) ) );
}

/**
 * Creates a navigation menu.
 *
 * Note that `$menu_name` is expected to be pre-slashed.
 *
 * @since 3.0.0
 *
 * @param string $menu_name Menu name.
 * @return int|WP_Error Menu ID on success, WP_Error object on failure.
 */
function wp_create_nav_menu( $menu_name ) {
	// expected_slashed ($menu_name)
	return wp_update_nav_menu_object( 0, array( 'menu-name' => $menu_name ) );
}

/**
 * Delete a Navigation Menu.
 *
 * @since 3.0.0
 *
 * @param string $menu Menu ID, slug, or name.
 * @return bool|WP_Error True on success, false or WP_Error object on failure.
 */
function wp_delete_nav_menu( $menu ) {
	$menu = wp_get_nav_menu_object( $menu );
	if ( ! $menu )
		return false;

	$menu_objects = get_objects_in_term( $menu->term_id, 'nav_menu' );
	if ( ! empty( $menu_objects ) ) {
		foreach ( $menu_objects as $item ) {
			wp_delete_post( $item );
		}
	}

	$result = wp_delete_term( $menu->term_id, 'nav_menu' );

	// Remove this menu from any locations.
	$locations = get_nav_menu_locations();
	foreach ( $locations as $location => $menu_id ) {
		if ( $menu_id == $menu->term_id )
			$locations[ $location ] = 0;
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	if ( $result && !is_wp_error($result) )

		/**
		 * Fires after a navigation menu has been successfully deleted.
		 *
		 * @since 3.0.0
		 *
		 * @param int $term_id ID of the deleted menu.
		 */
		do_action( 'wp_delete_nav_menu', $menu->term_id );

	return $result;
}

/**
 * Save the properties of a menu or create a new menu with those properties.
 *
 * Note that `$menu_data` is expected to be pre-slashed.
 *
 * @since 3.0.0
 *
 * @param int   $menu_id   The ID of the menu or "0" to create a new menu.
 * @param array $menu_data The array of menu data.
 * @return int|WP_Error Menu ID on success, WP_Error object on failure.
 */
function wp_update_nav_menu_object( $menu_id = 0, $menu_data = array() ) {
	// expected_slashed ($menu_data)
	$menu_id = (int) $menu_id;

	$_menu = wp_get_nav_menu_object( $menu_id );

	$args = array(
		'description' => ( isset( $menu_data['description'] ) ? $menu_data['description']  : '' ),
		'name'        => ( isset( $menu_data['menu-name']   ) ? $menu_data['menu-name']    : '' ),
		'parent'      => ( isset( $menu_data['parent']      ) ? (int) $menu_data['parent'] : 0  ),
		'slug'        => null,
	);

	// double-check that we're not going to have one menu take the name of another
	$_possible_existing = get_term_by( 'name', $menu_data['menu-name'], 'nav_menu' );
	if (
		$_possible_existing &&
		! is_wp_error( $_possible_existing ) &&
		isset( $_possible_existing->term_id ) &&
		$_possible_existing->term_id != $menu_id
	) {
		return new WP_Error( 'menu_exists',
			/* translators: %s: menu name */
			sprintf( __( 'The menu name %s conflicts with another menu name. Please try another.' ),
				'<strong>' . esc_html( $menu_data['menu-name'] ) . '</strong>'
			)
		);
	}

	// menu doesn't already exist, so create a new menu
	if ( ! $_menu || is_wp_error( $_menu ) ) {
		$menu_exists = get_term_by( 'name', $menu_data['menu-name'], 'nav_menu' );

		if ( $menu_exists ) {
			return new WP_Error( 'menu_exists',
				/* translators: %s: menu name */
				sprintf( __( 'The menu name %s conflicts with another menu name. Please try another.' ),
					'<strong>' . esc_html( $menu_data['menu-name'] ) . '</strong>'
				)
			);
		}

		$_menu = wp_insert_term( $menu_data['menu-name'], 'nav_menu', $args );

		if ( is_wp_error( $_menu ) )
			return $_menu;

		/**
		 * Fires after a navigation menu is successfully created.
		 *
		 * @since 3.0.0
		 *
		 * @param int   $term_id   ID of the new menu.
		 * @param array $menu_data An array of menu data.
		 */
		do_action( 'wp_create_nav_menu', $_menu['term_id'], $menu_data );

		return (int) $_menu['term_id'];
	}

	if ( ! $_menu || ! isset( $_menu->term_id ) )
		return 0;

	$menu_id = (int) $_menu->term_id;

	$update_response = wp_update_term( $menu_id, 'nav_menu', $args );

	if ( is_wp_error( $update_response ) )
		return $update_response;

	$menu_id = (int) $update_response['term_id'];

	/**
	 * Fires after a navigation menu has been successfully updated.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $menu_id   ID of the updated menu.
	 * @param array $menu_data An array of menu data.
	 */
	do_action( 'wp_update_nav_menu', $menu_id, $menu_data );
	return $menu_id;
}

/**
 * Save the properties of a menu item or create a new one.
 *
 * The menu-item-title, menu-item-description, and menu-item-attr-title are expected
 * to be pre-slashed since they are passed directly into `wp_insert_post()`.
 *
 * @since 3.0.0
 *
 * @param int   $menu_id         The ID of the menu. Required. If "0", makes the menu item a draft orphan.
 * @param int   $menu_item_db_id The ID of the menu item. If "0", creates a new menu item.
 * @param array $menu_item_data  The menu item's data.
 * @return int|WP_Error The menu item's database ID or WP_Error object on failure.
 */
function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $menu_item_data = array() ) {
	$menu_id = (int) $menu_id;
	$menu_item_db_id = (int) $menu_item_db_id;

	// make sure that we don't convert non-nav_menu_item objects into nav_menu_item objects
	if ( ! empty( $menu_item_db_id ) && ! is_nav_menu_item( $menu_item_db_id ) )
		return new WP_Error( 'update_nav_menu_item_failed', __( 'The given object ID is not that of a menu item.' ) );

	$menu = wp_get_nav_menu_object( $menu_id );

	if ( ! $menu && 0 !== $menu_id ) {
		return new WP_Error( 'invalid_menu_id', __( 'Invalid menu ID.' ) );
	}

	if ( is_wp_error( $menu ) ) {
		return $menu;
	}

	$defaults = array(
		'menu-item-db-id' => $menu_item_db_id,
		'menu-item-object-id' => 0,
		'menu-item-object' => '',
		'menu-item-parent-id' => 0,
		'menu-item-position' => 0,
		'menu-item-type' => 'custom',
		'menu-item-title' => '',
		'menu-item-url' => '',
		'menu-item-description' => '',
		'menu-item-attr-title' => '',
		'menu-item-target' => '',
		'menu-item-classes' => '',
		'menu-item-xfn' => '',
		'menu-item-status' => '',
	);

	$args = wp_parse_args( $menu_item_data, $defaults );

	if ( 0 == $menu_id ) {
		$args['menu-item-position'] = 1;
	} elseif ( 0 == (int) $args['menu-item-position'] ) {
		$menu_items = 0 == $menu_id ? array() : (array) wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'publish,draft' ) );
		$last_item = array_pop( $menu_items );
		$args['menu-item-position'] = ( $last_item && isset( $last_item->menu_order ) ) ? 1 + $last_item->menu_order : count( $menu_items );
	}

	$original_parent = 0 < $menu_item_db_id ? get_post_field( 'post_parent', $menu_item_db_id ) : 0;

	if ( 'custom' != $args['menu-item-type'] ) {
		/* if non-custom menu item, then:
			* use original object's URL
			* blank default title to sync with original object's
		*/

		$args['menu-item-url'] = '';

		$original_title = '';
		if ( 'taxonomy' == $args['menu-item-type'] ) {
			$original_parent = get_term_field( 'parent', $args['menu-item-object-id'], $args['menu-item-object'], 'raw' );
			$original_title = get_term_field( 'name', $args['menu-item-object-id'], $args['menu-item-object'], 'raw' );
		} elseif ( 'post_type' == $args['menu-item-type'] ) {

			$original_object = get_post( $args['menu-item-object-id'] );
			$original_parent = (int) $original_object->post_parent;
			$original_title = $original_object->post_title;
		} elseif ( 'post_type_archive' == $args['menu-item-type'] ) {
			$original_object = get_post_type_object( $args['menu-item-object'] );
			if ( $original_object ) {
				$original_title = $original_object->labels->archives;
			}
		}

		if ( $args['menu-item-title'] == $original_title )
			$args['menu-item-title'] = '';

		// hack to get wp to create a post object when too many properties are empty
		if ( '' ==  $args['menu-item-title'] && '' == $args['menu-item-description'] )
			$args['menu-item-description'] = ' ';
	}

	// Populate the menu item object
	$post = array(
		'menu_order' => $args['menu-item-position'],
		'ping_status' => 0,
		'post_content' => $args['menu-item-description'],
		'post_excerpt' => $args['menu-item-attr-title'],
		'post_parent' => $original_parent,
		'post_title' => $args['menu-item-title'],
		'post_type' => 'nav_menu_item',
	);

	$update = 0 != $menu_item_db_id;

	// New menu item. Default is draft status
	if ( ! $update ) {
		$post['ID'] = 0;
		$post['post_status'] = 'publish' == $args['menu-item-status'] ? 'publish' : 'draft';
		$menu_item_db_id = wp_insert_post( $post );
		if ( ! $menu_item_db_id	|| is_wp_error( $menu_item_db_id ) )
			return $menu_item_db_id;

		/**
		 * Fires immediately after a new navigation menu item has been added.
		 *
		 * @since 4.4.0
		 *
		 * @see wp_update_nav_menu_item()
		 *
		 * @param int   $menu_id         ID of the updated menu.
		 * @param int   $menu_item_db_id ID of the new menu item.
		 * @param array $args            An array of arguments used to update/add the menu item.
		 */
		do_action( 'wp_add_nav_menu_item', $menu_id, $menu_item_db_id, $args );
	}

	// Associate the menu item with the menu term
	// Only set the menu term if it isn't set to avoid unnecessary wp_get_object_terms()
	 if ( $menu_id && ( ! $update || ! is_object_in_term( $menu_item_db_id, 'nav_menu', (int) $menu->term_id ) ) ) {
		wp_set_object_terms( $menu_item_db_id, array( $menu->term_id ), 'nav_menu' );
	}

	if ( 'custom' == $args['menu-item-type'] ) {
		$args['menu-item-object-id'] = $menu_item_db_id;
		$args['menu-item-object'] = 'custom';
	}

	$menu_item_db_id = (int) $menu_item_db_id;

	update_post_meta( $menu_item_db_id, '_menu_item_type', sanitize_key($args['menu-item-type']) );
	update_post_meta( $menu_item_db_id, '_menu_item_menu_item_parent', strval( (int) $args['menu-item-parent-id'] ) );
	update_post_meta( $menu_item_db_id, '_menu_item_object_id', strval( (int) $args['menu-item-object-id'] ) );
	update_post_meta( $menu_item_db_id, '_menu_item_object', sanitize_key($args['menu-item-object']) );
	update_post_meta( $menu_item_db_id, '_menu_item_target', sanitize_key($args['menu-item-target']) );

	$args['menu-item-classes'] = array_map( 'sanitize_html_class', explode( ' ', $args['menu-item-classes'] ) );
	$args['menu-item-xfn'] = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['menu-item-xfn'] ) ) );
	update_post_meta( $menu_item_db_id, '_menu_item_classes', $args['menu-item-classes'] );
	update_post_meta( $menu_item_db_id, '_menu_item_xfn', $args['menu-item-xfn'] );
	update_post_meta( $menu_item_db_id, '_menu_item_url', esc_url_raw($args['menu-item-url']) );

	if ( 0 == $menu_id )
		update_post_meta( $menu_item_db_id, '_menu_item_orphaned', (string) time() );
	elseif ( get_post_meta( $menu_item_db_id, '_menu_item_orphaned' ) )
		delete_post_meta( $menu_item_db_id, '_menu_item_orphaned' );

	// Update existing menu item. Default is publish status
	if ( $update ) {
		$post['ID'] = $menu_item_db_id;
		$post['post_status'] = 'draft' == $args['menu-item-status'] ? 'draft' : 'publish';
		wp_update_post( $post );
	}

	/**
	 * Fires after a navigation menu item has been updated.
	 *
	 * @since 3.0.0
	 *
	 * @see wp_update_nav_menu_item()
	 *
	 * @param int   $menu_id         ID of the updated menu.
	 * @param int   $menu_item_db_id ID of the updated menu item.
	 * @param array $args            An array of arguments used to update a menu item.
	 */
	do_action( 'wp_update_nav_menu_item', $menu_id, $menu_item_db_id, $args );

	return $menu_item_db_id;
}

/**
 * Returns all navigation menu objects.
 *
 * @since 3.0.0
 * @since 4.1.0 Default value of the 'orderby' argument was changed from 'none'
 *              to 'name'.
 *
 * @param array $args Optional. Array of arguments passed on to get_terms().
 *                    Default empty array.
 * @return array Menu objects.
 */
function wp_get_nav_menus( $args = array() ) {
	$defaults = array( 'hide_empty' => false, 'orderby' => 'name' );
	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filters the navigation menu objects being returned.
	 *
	 * @since 3.0.0
	 *
	 * @see get_terms()
	 *
	 * @param array $menus An array of menu objects.
	 * @param array $args  An array of arguments used to retrieve menu objects.
	 */
	return apply_filters( 'wp_get_nav_menus', get_terms( 'nav_menu',  $args), $args );
}

/**
 * Return if a menu item is valid.
 *
 * @link https://core.trac.wordpress.org/ticket/13958
 *
 * @since 3.2.0
 * @access private
 *
 * @param object $item The menu item to check.
 * @return bool False if invalid, otherwise true.
 */
function _is_valid_nav_menu_item( $item ) {
	return empty( $item->_invalid );
}

/**
 * Return all menu items of a navigation menu.
 *
 * @since 3.0.0
 *
 * @global string $_menu_item_sort_prop
 * @staticvar array $fetched
 *
 * @param string $menu Menu name, ID, or slug.
 * @param array  $args Optional. Arguments to pass to get_posts().
 * @return false|array $items Array of menu items, otherwise false.
 */
function wp_get_nav_menu_items( $menu, $args = array() ) {
	$menu = wp_get_nav_menu_object( $menu );

	if ( ! $menu ) {
		return false;
	}

	static $fetched = array();

	$items = get_objects_in_term( $menu->term_id, 'nav_menu' );
	if ( is_wp_error( $items ) ) {
		return false;
	}

	$defaults = array( 'order' => 'ASC', 'orderby' => 'menu_order', 'post_type' => 'nav_menu_item',
		'post_status' => 'publish', 'output' => ARRAY_A, 'output_key' => 'menu_order', 'nopaging' => true );
	$args = wp_parse_args( $args, $defaults );
	$args['include'] = $items;

	if ( ! empty( $items ) ) {
		$items = get_posts( $args );
	} else {
		$items = array();
	}

	// Get all posts and terms at once to prime the caches
	if ( empty( $fetched[$menu->term_id] ) || wp_using_ext_object_cache() ) {
		$fetched[$menu->term_id] = true;
		$posts = array();
		$terms = array();
		foreach ( $items as $item ) {
			$object_id = get_post_meta( $item->ID, '_menu_item_object_id', true );
			$object    = get_post_meta( $item->ID, '_menu_item_object',    true );
			$type      = get_post_meta( $item->ID, '_menu_item_type',      true );

			if ( 'post_type' == $type )
				$posts[$object][] = $object_id;
			elseif ( 'taxonomy' == $type)
				$terms[$object][] = $object_id;
		}

		if ( ! empty( $posts ) ) {
			foreach ( array_keys($posts) as $post_type ) {
				get_posts( array('post__in' => $posts[$post_type], 'post_type' => $post_type, 'nopaging' => true, 'update_post_term_cache' => false) );
			}
		}
		unset($posts);

		if ( ! empty( $terms ) ) {
			foreach ( array_keys($terms) as $taxonomy ) {
				get_terms( $taxonomy, array(
					'include' => $terms[ $taxonomy ],
					'hierarchical' => false,
				) );
			}
		}
		unset($terms);
	}

	$items = array_map( 'wp_setup_nav_menu_item', $items );

	if ( ! is_admin() ) { // Remove invalid items only in front end
		$items = array_filter( $items, '_is_valid_nav_menu_item' );
	}

	if ( ARRAY_A == $args['output'] ) {
		$items = wp_list_sort( $items, array(
			$args['output_key'] => 'ASC',
		) );
		$i = 1;
		foreach ( $items as $k => $item ) {
			$items[$k]->{$args['output_key']} = $i++;
		}
	}

	/**
	 * Filters the navigation menu items being returned.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $items An array of menu item post objects.
	 * @param object $menu  The menu object.
	 * @param array  $args  An array of arguments used to retrieve menu item objects.
	 */
	return apply_filters( 'wp_get_nav_menu_items', $items, $menu, $args );
}

/**
 * Decorates a menu item object with the shared navigation menu item properties.
 *
 * Properties:
 * - ID:               The term_id if the menu item represents a taxonomy term.
 * - attr_title:       The title attribute of the link element for this menu item.
 * - classes:          The array of class attribute values for the link element of this menu item.
 * - db_id:            The DB ID of this item as a nav_menu_item object, if it exists (0 if it doesn't exist).
 * - description:      The description of this menu item.
 * - menu_item_parent: The DB ID of the nav_menu_item that is this item's menu parent, if any. 0 otherwise.
 * - object:           The type of object originally represented, such as "category," "post", or "attachment."
 * - object_id:        The DB ID of the original object this menu item represents, e.g. ID for posts and term_id for categories.
 * - post_parent:      The DB ID of the original object's parent object, if any (0 otherwise).
 * - post_title:       A "no title" label if menu item represents a post that lacks a title.
 * - target:           The target attribute of the link element for this menu item.
 * - title:            The title of this menu item.
 * - type:             The family of objects originally represented, such as "post_type" or "taxonomy."
 * - type_label:       The singular label used to describe this type of menu item.
 * - url:              The URL to which this menu item points.
 * - xfn:              The XFN relationship expressed in the link of this menu item.
 * - _invalid:         Whether the menu item represents an object that no longer exists.
 *
 * @since 3.0.0
 *
 * @param object $menu_item The menu item to modify.
 * @return object $menu_item The menu item with standard menu item properties.
 */
function wp_setup_nav_menu_item( $menu_item ) {
	if ( isset( $menu_item->post_type ) ) {
		if ( 'nav_menu_item' == $menu_item->post_type ) {
			$menu_item->db_id = (int) $menu_item->ID;
			$menu_item->menu_item_parent = ! isset( $menu_item->menu_item_parent ) ? get_post_meta( $menu_item->ID, '_menu_item_menu_item_parent', true ) : $menu_item->menu_item_parent;
			$menu_item->object_id = ! isset( $menu_item->object_id ) ? get_post_meta( $menu_item->ID, '_menu_item_object_id', true ) : $menu_item->object_id;
			$menu_item->object = ! isset( $menu_item->object ) ? get_post_meta( $menu_item->ID, '_menu_item_object', true ) : $menu_item->object;
			$menu_item->type = ! isset( $menu_item->type ) ? get_post_meta( $menu_item->ID, '_menu_item_type', true ) : $menu_item->type;

			if ( 'post_type' == $menu_item->type ) {
				$object = get_post_type_object( $menu_item->object );
				if ( $object ) {
					$menu_item->type_label = $object->labels->singular_name;
				} else {
					$menu_item->type_label = $menu_item->object;
					$menu_item->_invalid = true;
				}

				if ( 'trash' === get_post_status( $menu_item->object_id ) ) {
					$menu_item->_invalid = true;
				}

				$menu_item->url = get_permalink( $menu_item->object_id );

				$original_object = get_post( $menu_item->object_id );
				/** This filter is documented in wp-includes/post-template.php */
				$original_title = apply_filters( 'the_title', $original_object->post_title, $original_object->ID );

				if ( '' === $original_title ) {
					/* translators: %d: ID of a post */
					$original_title = sprintf( __( '#%d (no title)' ), $original_object->ID );
				}

				$menu_item->title = '' == $menu_item->post_title ? $original_title : $menu_item->post_title;

			} elseif ( 'post_type_archive' == $menu_item->type ) {
				$object =  get_post_type_object( $menu_item->object );
				if ( $object ) {
					$menu_item->title = '' == $menu_item->post_title ? $object->labels->archives : $menu_item->post_title;
					$post_type_description = $object->description;
				} else {
					$menu_item->_invalid = true;
					$post_type_description = '';
				}

				$menu_item->type_label = __( 'Post Type Archive' );
				$post_content = wp_trim_words( $menu_item->post_content, 200 );
				$post_type_description = '' == $post_content ? $post_type_description : $post_content;
				$menu_item->url = get_post_type_archive_link( $menu_item->object );
			} elseif ( 'taxonomy' == $menu_item->type ) {
				$object = get_taxonomy( $menu_item->object );
				if ( $object ) {
					$menu_item->type_label = $object->labels->singular_name;
				} else {
					$menu_item->type_label = $menu_item->object;
					$menu_item->_invalid = true;
				}

				$term_url = get_term_link( (int) $menu_item->object_id, $menu_item->object );
				$menu_item->url = !is_wp_error( $term_url ) ? $term_url : '';

				$original_title = get_term_field( 'name', $menu_item->object_id, $menu_item->object, 'raw' );
				if ( is_wp_error( $original_title ) )
					$original_title = false;
				$menu_item->title = '' == $menu_item->post_title ? $original_title : $menu_item->post_title;

			} else {
				$menu_item->type_label = __('Custom Link');
				$menu_item->title = $menu_item->post_title;
				$menu_item->url = ! isset( $menu_item->url ) ? get_post_meta( $menu_item->ID, '_menu_item_url', true ) : $menu_item->url;
			}

			$menu_item->target = ! isset( $menu_item->target ) ? get_post_meta( $menu_item->ID, '_menu_item_target', true ) : $menu_item->target;

			/**
			 * Filters a navigation menu item's title attribute.
			 *
			 * @since 3.0.0
			 *
			 * @param string $item_title The menu item title attribute.
			 */
			$menu_item->attr_title = ! isset( $menu_item->attr_title ) ? apply_filters( 'nav_menu_attr_title', $menu_item->post_excerpt ) : $menu_item->attr_title;

			if ( ! isset( $menu_item->description ) ) {
				/**
				 * Filters a navigation menu item's description.
				 *
				 * @since 3.0.0
				 *
				 * @param string $description The menu item description.
				 */
				$menu_item->description = apply_filters( 'nav_menu_description', wp_trim_words( $menu_item->post_content, 200 ) );
			}

			$menu_item->classes = ! isset( $menu_item->classes ) ? (array) get_post_meta( $menu_item->ID, '_menu_item_classes', true ) : $menu_item->classes;
			$menu_item->xfn = ! isset( $menu_item->xfn ) ? get_post_meta( $menu_item->ID, '_menu_item_xfn', true ) : $menu_item->xfn;
		} else {
			$menu_item->db_id = 0;
			$menu_item->menu_item_parent = 0;
			$menu_item->object_id = (int) $menu_item->ID;
			$menu_item->type = 'post_type';

			$object = get_post_type_object( $menu_item->post_type );
			$menu_item->object = $object->name;
			$menu_item->type_label = $object->labels->singular_name;

			if ( '' === $menu_item->post_title ) {
				/* translators: %d: ID of a post */
				$menu_item->post_title = sprintf( __( '#%d (no title)' ), $menu_item->ID );
			}

			$menu_item->title = $menu_item->post_title;
			$menu_item->url = get_permalink( $menu_item->ID );
			$menu_item->target = '';

			/** This filter is documented in wp-includes/nav-menu.php */
			$menu_item->attr_title = apply_filters( 'nav_menu_attr_title', '' );

			/** This filter is documented in wp-includes/nav-menu.php */
			$menu_item->description = apply_filters( 'nav_menu_description', '' );
			$menu_item->classes = array();
			$menu_item->xfn = '';
		}
	} elseif ( isset( $menu_item->taxonomy ) ) {
		$menu_item->ID = $menu_item->term_id;
		$menu_item->db_id = 0;
		$menu_item->menu_item_parent = 0;
		$menu_item->object_id = (int) $menu_item->term_id;
		$menu_item->post_parent = (int) $menu_item->parent;
		$menu_item->type = 'taxonomy';

		$object = get_taxonomy( $menu_item->taxonomy );
		$menu_item->object = $object->name;
		$menu_item->type_label = $object->labels->singular_name;

		$menu_item->title = $menu_item->name;
		$menu_item->url = get_term_link( $menu_item, $menu_item->taxonomy );
		$menu_item->target = '';
		$menu_item->attr_title = '';
		$menu_item->description = get_term_field( 'description', $menu_item->term_id, $menu_item->taxonomy );
		$menu_item->classes = array();
		$menu_item->xfn = '';

	}

	/**
	 * Filters a navigation menu item object.
	 *
	 * @since 3.0.0
	 *
	 * @param object $menu_item The menu item object.
	 */
	return apply_filters( 'wp_setup_nav_menu_item', $menu_item );
}

/**
 * Get the menu items associated with a particular object.
 *
 * @since 3.0.0
 *
 * @param int    $object_id   The ID of the original object.
 * @param string $object_type The type of object, such as "taxonomy" or "post_type."
 * @param string $taxonomy    If $object_type is "taxonomy", $taxonomy is the name of the tax that $object_id belongs to
 * @return array The array of menu item IDs; empty array if none;
 */
function wp_get_associated_nav_menu_items( $object_id = 0, $object_type = 'post_type', $taxonomy = '' ) {
	$object_id = (int) $object_id;
	$menu_item_ids = array();

	$query = new WP_Query;
	$menu_items = $query->query(
		array(
			'meta_key' => '_menu_item_object_id',
			'meta_value' => $object_id,
			'post_status' => 'any',
			'post_type' => 'nav_menu_item',
			'posts_per_page' => -1,
		)
	);
	foreach ( (array) $menu_items as $menu_item ) {
		if ( isset( $menu_item->ID ) && is_nav_menu_item( $menu_item->ID ) ) {
			$menu_item_type = get_post_meta( $menu_item->ID, '_menu_item_type', true );
			if (
				'post_type' == $object_type &&
				'post_type' == $menu_item_type
			) {
				$menu_item_ids[] = (int) $menu_item->ID;
			} elseif (
				'taxonomy' == $object_type &&
				'taxonomy' == $menu_item_type &&
				get_post_meta( $menu_item->ID, '_menu_item_object', true ) == $taxonomy
			) {
				$menu_item_ids[] = (int) $menu_item->ID;
			}
		}
	}

	return array_unique( $menu_item_ids );
}

/**
 * Callback for handling a menu item when its original object is deleted.
 *
 * @since 3.0.0
 * @access private
 *
 * @param int $object_id The ID of the original object being trashed.
 *
 */
function _wp_delete_post_menu_item( $object_id = 0 ) {
	$object_id = (int) $object_id;

	$menu_item_ids = wp_get_associated_nav_menu_items( $object_id, 'post_type' );

	foreach ( (array) $menu_item_ids as $menu_item_id ) {
		wp_delete_post( $menu_item_id, true );
	}
}

/**
 * Serves as a callback for handling a menu item when its original object is deleted.
 *
 * @since 3.0.0
 * @access private
 *
 * @param int    $object_id Optional. The ID of the original object being trashed. Default 0.
 * @param int    $tt_id     Term taxonomy ID. Unused.
 * @param string $taxonomy  Taxonomy slug.
 */
function _wp_delete_tax_menu_item( $object_id = 0, $tt_id, $taxonomy ) {
	$object_id = (int) $object_id;

	$menu_item_ids = wp_get_associated_nav_menu_items( $object_id, 'taxonomy', $taxonomy );

	foreach ( (array) $menu_item_ids as $menu_item_id ) {
		wp_delete_post( $menu_item_id, true );
	}
}

/**
 * Automatically add newly published page objects to menus with that as an option.
 *
 * @since 3.0.0
 * @access private
 *
 * @param string $new_status The new status of the post object.
 * @param string $old_status The old status of the post object.
 * @param object $post       The post object being transitioned from one status to another.
 */
function _wp_auto_add_pages_to_menu( $new_status, $old_status, $post ) {
	if ( 'publish' != $new_status || 'publish' == $old_status || 'page' != $post->post_type )
		return;
	if ( ! empty( $post->post_parent ) )
		return;
	$auto_add = get_option( 'nav_menu_options' );
	if ( empty( $auto_add ) || ! is_array( $auto_add ) || ! isset( $auto_add['auto_add'] ) )
		return;
	$auto_add = $auto_add['auto_add'];
	if ( empty( $auto_add ) || ! is_array( $auto_add ) )
		return;

	$args = array(
		'menu-item-object-id' => $post->ID,
		'menu-item-object' => $post->post_type,
		'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish',
	);

	foreach ( $auto_add as $menu_id ) {
		$items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'publish,draft' ) );
		if ( ! is_array( $items ) )
			continue;
		foreach ( $items as $item ) {
			if ( $post->ID == $item->object_id )
				continue 2;
		}
		wp_update_nav_menu_item( $menu_id, 0, $args );
	}
}
