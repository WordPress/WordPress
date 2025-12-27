<?php
/**
 * Extend WordPress core's rendering of menus to support block-based menus.
 *
 * @package gutenberg
 */

/**
 * Shim that hooks into `wp_update_nav_menu_item` and makes it so that nav menu
 * items support a 'content' field. This field contains HTML and is used by nav
 * menu items with `type` set to `'block'`.
 *
 * Specifically, this shim makes it so that:
 *
 * 1) The `wp_update_nav_menu_item()` function supports setting
 * `'menu-item-content'` on a menu item. When merged to Core, this functionality
 * should exist in `wp_update_nav_menu_item()`.
 *
 * 2) Updating a menu via nav-menus.php supports setting `'menu-item-content'`
 * on a menu item. When merged to Core, this functionality should exist in
 * `wp_nav_menu_update_menu_items()`.
 *
 * 3) The `customize_save` ajax action supports setting `'content'` on a nav
 * menu item. When merged to Core, this functionality should exist in
 * `WP_Customize_Manager::save()`.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @global WP_Customize_Manager $wp_customize
 *
 * @param int   $menu_id         ID of the updated menu.
 * @param int   $menu_item_db_id ID of the new menu item.
 * @param array $args            An array of arguments used to update/add the menu item.
 */
function gutenberg_update_nav_menu_item_content( $menu_id, $menu_item_db_id, $args ) {
	global $wp_customize;

	// Support setting content in nav-menus.php by grabbing the value from
	// $_POST. This belongs in `wp_nav_menu_update_menu_items()`.
	if ( isset( $_POST['menu-item-content'][ $menu_item_db_id ] ) ) {
		$args['menu-item-content'] = wp_unslash( $_POST['menu-item-content'][ $menu_item_db_id ] );
	}

	// Support setting content in customize_save admin-ajax.php requests by
	// grabbing the unsanitized $_POST values. This belongs in
	// `WP_Customize_Manager::save()`.
	if ( isset( $wp_customize ) ) {
		$values = $wp_customize->unsanitized_post_values();
		if ( isset( $values[ "nav_menu_item[$menu_item_db_id]" ]['content'] ) ) {
			if ( is_string( $values[ "nav_menu_item[$menu_item_db_id]" ]['content'] ) ) {
				$args['menu-item-content'] = $values[ "nav_menu_item[$menu_item_db_id]" ]['content'];
			} elseif ( isset( $values[ "nav_menu_item[$menu_item_db_id]" ]['content']['raw'] ) ) {
				$args['menu-item-content'] = $values[ "nav_menu_item[$menu_item_db_id]" ]['content']['raw'];
			}
		}
	}

	// Everything else belongs in `wp_update_nav_menu_item()`.

	$defaults = array(
		'menu-item-content' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	update_post_meta( $menu_item_db_id, '_menu_item_content', wp_slash( $args['menu-item-content'] ) );
}
add_action( 'wp_update_nav_menu_item', 'gutenberg_update_nav_menu_item_content', 10, 3 );

/**
 * Shim that hooks into `wp_setup_nav_menu_items` and makes it so that nav menu
 * items have a 'content' field. This field contains HTML and is used by nav
 * menu items with `type` set to `'block'`.
 *
 * Specifically, this shim makes it so that the `wp_setup_nav_menu_item()`
 * function sets `content` on the returned menu item. When merged to Core, this
 * functionality should exist in `wp_setup_nav_menu_item()`.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param object $menu_item The menu item object.
 *
 * @return object Updated menu item object.
 */
function gutenberg_setup_block_nav_menu_item( $menu_item ) {
	if ( 'block' === $menu_item->type ) {
		$menu_item->type_label = __( 'Block', 'gutenberg' );
		$menu_item->content    = ! isset( $menu_item->content ) ? get_post_meta( $menu_item->db_id, '_menu_item_content', true ) : $menu_item->content;

		// Set to make the menu item display nicely in nav-menus.php.
		$menu_item->object = 'block';
		$menu_item->title  = __( 'Block', 'gutenberg' );
	}

	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'gutenberg_setup_block_nav_menu_item' );

/**
 * Shim that hooks into `walker_nav_menu_start_el` and makes it so that the
 * default walker which renders a menu will correctly render the HTML associated
 * with any navigation menu item that has `type` set to `'block`'.
 *
 * Specifically, this shim makes it so that `Walker_Nav_Menu::start_el()`
 * renders the `content` of a nav menu item when its `type` is `'block'`. When
 * merged to Core, this functionality should exist in
 * `Walker_Nav_Menu::start_el()`.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of menu item. Used for padding.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 *
 * @return string The menu item's updated HTML output.
 */
function gutenberg_output_block_nav_menu_item( $item_output, $item, $depth, $args ) {
	if ( 'block' === $item->type ) {
		$item_output = $args->before;
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= apply_filters( 'the_content', $item->content );
		$item_output .= $args->after;
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'gutenberg_output_block_nav_menu_item', 10, 4 );

/**
 * Shim that prevents menu items with type `'block'` from being rendered in the
 * frontend when the theme does not support block menus.
 *
 * Specifically, this shim makes it so that `wp_nav_menu()` will remove any menu
 * items that have a `type` of `'block'` from `$sorted_menu_items`. When merged
 * to Core, this functionality should exist in `wp_nav_menu()`.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param array $menu_items The menu items, sorted by each menu item's menu order.
 *
 * @return array Updated menu items, sorted by each menu item's menu order.
 */
function gutenberg_remove_block_nav_menu_items( $menu_items ) {
	// We should uncomment the line below when the block-nav-menus feature becomes stable.
	// @see https://github.com/WordPress/gutenberg/issues/34265.
	/*if ( current_theme_supports( 'block-nav-menus' ) ) {*/
	if ( false ) {
		return $menu_items;
	}

	return array_filter(
		$menu_items,
		static function ( $menu_item ) {
			return 'block' !== $menu_item->type;
		}
	);
}
add_filter( 'wp_nav_menu_objects', 'gutenberg_remove_block_nav_menu_items', 10 );

/**
 * Recursively converts a list of menu items into a list of blocks. This is a
 * helper function used by `gutenberg_output_block_nav_menu()`.
 *
 * Transformation depends on the menu item type. Link menu items are turned into
 * a `core/navigation-link` block. Block menu items are simply parsed.
 *
 * @param array $menu_items The menu items to convert, sorted by each menu item's menu order.
 * @param array $menu_items_by_parent_id All menu items, indexed by their parent's ID.
 *
 * @return array Updated menu items, sorted by each menu item's menu order.
 */
function gutenberg_convert_menu_items_to_blocks(
	$menu_items,
	&$menu_items_by_parent_id
) {
	if ( empty( $menu_items ) ) {
		return array();
	}

	$blocks = array();

	foreach ( $menu_items as $menu_item ) {
		if ( 'block' === $menu_item->type ) {
			$parsed_blocks = parse_blocks( $menu_item->content );

			if ( count( $parsed_blocks ) ) {
				$block = $parsed_blocks[0];
			} else {
				$block = array(
					'blockName' => 'core/freeform',
					'attrs'     => array(
						'originalContent' => $menu_item->content,
					),
				);
			}
		} else {
			$block = array(
				'blockName' => 'core/navigation-link',
				'attrs'     => array(
					'label' => $menu_item->title,
					'url'   => $menu_item->url,
				),
			);
		}

		$block['innerBlocks'] = gutenberg_convert_menu_items_to_blocks(
			isset( $menu_items_by_parent_id[ $menu_item->ID ] )
					? $menu_items_by_parent_id[ $menu_item->ID ]
					: array(),
			$menu_items_by_parent_id
		);

		$blocks[] = $block;
	}

	return $blocks;
}

/**
 * Shim that causes `wp_nav_menu()` to output a Navigation block instead of a
 * nav menu when the theme supports block menus. The Navigation block is
 * constructed by transforming the stored tree of menu items into a tree of
 * blocks.
 *
 * Specifically, this shim makes it so that `wp_nav_menu()` returns early when
 * the theme supports block menus. When merged to Core, this functionality
 * should exist in `wp_nav_menu()` after `$sorted_menu_items` is set. The
 * duplicated code (marked using BEGIN and END) can be deleted.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param string|null $output Nav menu output to short-circuit with. Default null.
 * @param stdClass    $args   An object containing wp_nav_menu() arguments.
 *
 * @return string|null Nav menu output to short-circuit with.
 */
function gutenberg_output_block_nav_menu( $output, $args ) {
	// We should uncomment the line below when the block-nav-menus feature becomes stable.
	// @see https://github.com/WordPress/gutenberg/issues/34265.
	/*if ( ! current_theme_supports( 'block-nav-menus' ) ) {*/
	if ( true ) {
		return null;
	}

	// BEGIN: Code that already exists in wp_nav_menu().

	// Get the nav menu based on the requested menu.
	$menu = wp_get_nav_menu_object( $args->menu );

	// Get the nav menu based on the theme_location.
	$locations = get_nav_menu_locations();
	if ( ! $menu && $args->theme_location && $locations && isset( $locations[ $args->theme_location ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
	}

	// Get the first menu that has items if we still can't find a menu.
	if ( ! $menu && ! $args->theme_location ) {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			$menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) );
			if ( $menu_items ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}

	if ( empty( $args->menu ) ) {
		$args->menu = $menu;
	}

	// If the menu exists, get its items.
	if ( $menu && ! is_wp_error( $menu ) && ! isset( $menu_items ) ) {
		$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
	}

	// Set up the $menu_item variables.
	_wp_menu_item_classes_by_context( $menu_items );

	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}

	unset( $menu_items, $menu_item );

	// END: Code that already exists in wp_nav_menu().

	$menu_items_by_parent_id = array();
	foreach ( $sorted_menu_items as $menu_item ) {
		$menu_items_by_parent_id[ $menu_item->menu_item_parent ][] = $menu_item;
	}

	$block_attributes = array();
	if ( isset( $args->block_attributes ) ) {
		$block_attributes = $args->block_attributes;
	}

	$navigation_block = array(
		'blockName'   => 'core/navigation',
		'attrs'       => $block_attributes,
		'innerBlocks' => gutenberg_convert_menu_items_to_blocks(
			isset( $menu_items_by_parent_id[0] )
				? $menu_items_by_parent_id[0]
				: array(),
			$menu_items_by_parent_id
		),
	);

	return render_block( $navigation_block );
}
add_filter( 'pre_wp_nav_menu', 'gutenberg_output_block_nav_menu', 10, 2 );

/**
 * Shim that makes nav-menus.php nicely display a menu item with its `type` set to
 * `'block'`.
 *
 * Specifically, this shim makes it so that `Walker_Nav_Menu_Edit::start_el()`
 * outputs extra form fields. When merged to Core, this markup should exist in
 * `Walker_Nav_Menu_Edit::start_el()`.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param int     $item_id Menu item ID.
 * @param WP_Post $item    Menu item data object.
 */
function gutenberg_output_block_menu_item_custom_fields( $item_id, $item ) {
	if ( 'block' === $item->type ) {
		?>
		<p class="field-content description description-wide">
			<label for="edit-menu-item-content-<?php echo $item_id; ?>">
				<?php _e( 'Content', 'gutenberg' ); ?><br />
				<textarea id="edit-menu-item-content-<?php echo $item_id; ?>" class="widefat" rows="3" cols="20" name="menu-item-content[<?php echo $item_id; ?>]" readonly><?php echo esc_textarea( trim( $item->content ) ); ?></textarea>
			</label>
		</p>
		<?php
	}
}
add_action( 'wp_nav_menu_item_custom_fields', 'gutenberg_output_block_menu_item_custom_fields', 10, 2 );

/**
 * Shim that adds extra styling to nav-menus.php. This lets us style menu items
 * that have a `type` set to `'block'`. When merged to Core, this CSS should be
 * moved to nav-menus.css.
 *
 * This shim can be removed when the Gutenberg plugin requires a WordPress
 * version that has the ticket below.
 *
 * @see https://core.trac.wordpress.org/ticket/50544
 *
 * @param string $hook The current admin page.
 */
function gutenberg_add_block_menu_item_styles_to_nav_menus( $hook ) {
	if ( 'nav-menus.php' === $hook ) {
		$css = <<<CSS
			/**
			 * HACK: We're hiding the description field using CSS because this
			 * cannot be done using a filter. When merged to Core, we should
			 * actually remove the field from
			 * `Walker_Nav_Menu_Edit::start_el()`.
			 */
			.menu-item-block .description:not(.field-content) {
				display: none;
			}
CSS;
		wp_add_inline_style( 'nav-menus', $css );
	}
}
add_action( 'admin_enqueue_scripts', 'gutenberg_add_block_menu_item_styles_to_nav_menus' );
