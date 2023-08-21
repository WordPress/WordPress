<?php
/**
 * WP_Classic_To_Block_Menu_Converter class
 *
 * @package WordPress
 * @since 6.3.0
 */

/**
 * Converts a Classic Menu to Block Menu blocks.
 *
 * @since 6.3.0
 * @access public
 */
class WP_Classic_To_Block_Menu_Converter {

	/**
	 * Converts a Classic Menu to blocks.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_Term $menu The Menu term object of the menu to convert.
	 * @return string|WP_Error The serialized and normalized parsed blocks on success,
	 *                         an empty string when there are no menus to convert,
	 *                         or WP_Error on invalid menu.
	 */
	public static function convert( $menu ) {

		if ( ! is_nav_menu( $menu ) ) {
			return new WP_Error(
				'invalid_menu',
				__( 'The menu provided is not a valid menu.' )
			);
		}

		$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

		if ( empty( $menu_items ) ) {
			return '';
		}

		// Set up the $menu_item variables.
		// Adds the class property classes for the current context, if applicable.
		_wp_menu_item_classes_by_context( $menu_items );

		$menu_items_by_parent_id = static::group_by_parent_id( $menu_items );

		$first_menu_item = isset( $menu_items_by_parent_id[0] )
			? $menu_items_by_parent_id[0]
			: array();

		$inner_blocks = static::to_blocks(
			$first_menu_item,
			$menu_items_by_parent_id
		);

		return serialize_blocks( $inner_blocks );
	}

	/**
	 * Returns an array of menu items grouped by the id of the parent menu item.
	 *
	 * @since 6.3.0
	 *
	 * @param array $menu_items An array of menu items.
	 * @return array
	 */
	private static function group_by_parent_id( $menu_items ) {
		$menu_items_by_parent_id = array();

		foreach ( $menu_items as $menu_item ) {
			$menu_items_by_parent_id[ $menu_item->menu_item_parent ][] = $menu_item;
		}

		return $menu_items_by_parent_id;
	}

	/**
	 * Turns menu item data into a nested array of parsed blocks
	 *
	 * @since 6.3.0
	 *
	 * @param array $menu_items              An array of menu items that represent
	 *                                       an individual level of a menu.
	 * @param array $menu_items_by_parent_id An array keyed by the id of the
	 *                                       parent menu where each element is an
	 *                                       array of menu items that belong to
	 *                                       that parent.
	 * @return array An array of parsed block data.
	 */
	private static function to_blocks( $menu_items, $menu_items_by_parent_id ) {

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

			$block['innerBlocks']  = isset( $menu_items_by_parent_id[ $menu_item->ID ] )
			? static::to_blocks( $menu_items_by_parent_id[ $menu_item->ID ], $menu_items_by_parent_id )
			: array();
			$block['innerContent'] = array_map( 'serialize_block', $block['innerBlocks'] );

			$blocks[] = $block;
		}

		return $blocks;
	}
}
