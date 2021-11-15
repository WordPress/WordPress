<?php
/**
 * Block navigation areas functions.
 *
 * @package WordPress
 */

/**
 * Registers the navigation areas supported by the current theme. The expected
 * shape of the argument is:
 * array(
 *     'primary'   => 'Primary',
 *     'secondary' => 'Secondary',
 *     'tertiary'  => 'Tertiary',
 * )
 *
 * @since 5.9.0
 *
 * @param array $new_areas Supported navigation areas.
 */
function register_navigation_areas( $new_areas ) {
	global $navigation_areas;
	$navigation_areas = $new_areas;
}

/**
 * Register the default navigation areas.
 *
 * @since 5.9.0
 * @access private
 */
function _wp_register_default_navigation_areas() {
	register_navigation_areas(
		array(
			'primary'   => _x( 'Primary', 'navigation area' ),
			'secondary' => _x( 'Secondary', 'navigation area' ),
			'tertiary'  => _x( 'Tertiary', 'navigation area' ),
		)
	);
}

/**
 * Returns the available navigation areas.
 *
 * @since 5.9.0
 *
 * @return array Registered navigation areas.
 */
function get_navigation_areas() {
	global $navigation_areas;
	return $navigation_areas;
}

/**
 * Migrates classic menus to a block-based navigation post on theme switch.
 * Assigns the created navigation post to the corresponding navigation area.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string   $new_name  Name of the new theme.
 * @param WP_Theme $new_theme New theme.
 * @param WP_Theme $old_theme Old theme.
 */
function _wp_migrate_menu_to_navigation_post( $new_name, WP_Theme $new_theme, WP_Theme $old_theme ) {
	// Do nothing when switching to a theme that does not support site editor.
	if ( ! wp_is_block_template_theme() ) {
		return;
	}

	// get_nav_menu_locations() calls get_theme_mod() which depends on the stylesheet option.
	// At the same time, switch_theme runs only after the stylesheet option was updated to $new_theme.
	// To retrieve theme mods of the old theme, the getter is hooked to get_option( 'stylesheet' ) so that we
	// get the old theme, which causes the get_nav_menu_locations to get the locations of the old theme.
	$get_old_theme_stylesheet = static function() use ( $old_theme ) {
		return $old_theme->get_stylesheet();
	};
	add_filter( 'option_stylesheet', $get_old_theme_stylesheet );

	$locations    = get_nav_menu_locations();
	$area_mapping = get_option( 'wp_navigation_areas', array() );

	foreach ( $locations as $location_name => $menu_id ) {
		// Get the menu from the location, skipping if there is no
		// menu or there was an error.
		$menu = wp_get_nav_menu_object( $menu_id );
		if ( ! $menu || is_wp_error( $menu ) ) {
			continue;
		}

		$menu_items = _wp_get_menu_items_at_location( $location_name );
		if ( empty( $menu_items ) ) {
			continue;
		}

		$post_name = 'classic_menu_' . $menu_id;

		// Get or create to avoid creating too many wp_navigation posts.
		$query          = new WP_Query;
		$matching_posts = $query->query(
			array(
				'name'           => $post_name,
				'post_status'    => 'publish',
				'post_type'      => 'wp_navigation',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $matching_posts ) ) {
			$navigation_post_id = $matching_posts[0];
		} else {
			$menu_items_by_parent_id = _wp_sort_menu_items_by_parent_id( $menu_items );
			$parsed_blocks           = _wp_parse_blocks_from_menu_items( $menu_items_by_parent_id[0], $menu_items_by_parent_id );
			$post_data               = array(
				'post_type'    => 'wp_navigation',
				'post_title'   => sprintf(
					/* translators: %s: the name of the menu, e.g. "Main Menu". */
					__( 'Classic menu: %s' ),
					$menu->name
				),
				'post_name'    => $post_name,
				'post_content' => serialize_blocks( $parsed_blocks ),
				'post_status'  => 'publish',
			);
			$navigation_post_id      = wp_insert_post( $post_data, true );
			// If wp_insert_post fails *at any time*, then bail out of the
			// entire migration attempt returning the WP_Error object.
			if ( is_wp_error( $navigation_post_id ) ) {
				return $navigation_post_id;
			}
		}

		$area_mapping[ $location_name ] = $navigation_post_id;
	}
	remove_filter( 'option_stylesheet', $get_old_theme_stylesheet );

	update_option( 'wp_navigation_areas', $area_mapping );
}

/**
 * Returns the menu items for a WordPress menu location.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string $location The menu location.
 * @return array Menu items for the location.
 */
function _wp_get_menu_items_at_location( $location ) {
	if ( empty( $location ) ) {
		return;
	}

	// Build menu data. The following approximates the code in `wp_nav_menu()`.

	// Find the location in the list of locations, returning early if the
	// location can't be found.
	$locations = get_nav_menu_locations();
	if ( ! isset( $locations[ $location ] ) ) {
		return;
	}

	// Get the menu from the location, returning early if there is no
	// menu or there was an error.
	$menu = wp_get_nav_menu_object( $locations[ $location ] );
	if ( ! $menu || is_wp_error( $menu ) ) {
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
	_wp_menu_item_classes_by_context( $menu_items );

	return $menu_items;
}

/**
 * Sorts a standard array of menu items into a nested structure keyed by the
 * id of the parent menu.
 *
 * @since 5.9.0
 * @access private
 *
 * @param array $menu_items Menu items to sort.
 * @return array An array keyed by the id of the parent menu where each element
 *               is an array of menu items that belong to that parent.
 */
function _wp_sort_menu_items_by_parent_id( $menu_items ) {
	$sorted_menu_items = array();
	foreach ( $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}
	unset( $menu_items, $menu_item );

	$menu_items_by_parent_id = array();
	foreach ( $sorted_menu_items as $menu_item ) {
		$menu_items_by_parent_id[ $menu_item->menu_item_parent ][] = $menu_item;
	}

	return $menu_items_by_parent_id;
}

/**
 * Turns menu item data into a nested array of parsed blocks
 *
 * @since 5.9.0
 * @access private
 *
 * @param array $menu_items               An array of menu items that represent
 *                                        an individual level of a menu.
 * @param array $menu_items_by_parent_id  An array keyed by the id of the
 *                                        parent menu where each element is an
 *                                        array of menu items that belong to
 *                                        that parent.
 * @return array An array of parsed block data.
 */
function _wp_parse_blocks_from_menu_items( $menu_items, $menu_items_by_parent_id ) {
	if ( empty( $menu_items ) ) {
		return array();
	}

	$blocks = array();

	foreach ( $menu_items as $menu_item ) {
		$class_name       = ! empty( $menu_item->classes ) ? implode( ' ', (array) $menu_item->classes ) : null;
		$id               = ( null !== $menu_item->object_id && 'custom' !== $menu_item->object ) ? $menu_item->object_id : null;
		$opens_in_new_tab = null !== $menu_item->target && '_blank' === $menu_item->target;
		$rel              = ( null !== $menu_item->xfn && '' !== $menu_item->xfn ) ? $menu_item->xfn : null;
		$kind             = null !== $menu_item->type ? str_replace( '_', '-', $menu_item->type ) : 'custom';

		$block = array(
			'blockName' => isset( $menu_items_by_parent_id[ $menu_item->ID ] ) ? 'core/navigation-submenu' : 'core/navigation-link',
			'attrs'     => array(
				'className'     => $class_name,
				'description'   => $menu_item->description,
				'id'            => $id,
				'kind'          => $kind,
				'label'         => $menu_item->title,
				'opensInNewTab' => $opens_in_new_tab,
				'rel'           => $rel,
				'title'         => $menu_item->attr_title,
				'type'          => $menu_item->object,
				'url'           => $menu_item->url,
			),
		);

		if ( isset( $menu_items_by_parent_id[ $menu_item->ID ] ) ) {
			$block['innerBlocks'] = _wp_parse_blocks_from_menu_items(
				$menu_items_by_parent_id[ $menu_item->ID ],
				$menu_items_by_parent_id
			);
		} else {
			$block['innerBlocks'] = array();
		}

		$block['innerContent'] = array_map( 'serialize_block', $block['innerBlocks'] );

		$blocks[] = $block;
	}

	return $blocks;
}
