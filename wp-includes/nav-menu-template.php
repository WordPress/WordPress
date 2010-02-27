<?php

/**
 * Outputs a navigation menu.
 *
 * Optional $args contents:
 *
 * id - The menu id. Defaults to blank.
 * slug - The menu slug. Defaults to blank.
 * menu_class - CSS class to use for the div container of the menu list. Defaults to 'menu'.
 * format - Whether to format the ul. Defaults to 'div'.
 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
 *
 * TODO:
 * show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
 * link_before - Text before show_home argument text.
 * link_after - Text after show_home argument text.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	$defaults = array( 'id' => '', 'slug' => '', 'menu_class' => 'menu', 'format' => 'div', 'fallback_cb' => 'wp_page_menu', 'echo' => true, 'link_before' => '', 'link_after' => '' );
	$args = wp_parse_args( $args, $defaults );
	$args = (object) $args;
	
	// Get the menu
	$menu = null;
	if ( !empty($args->id) ) {
		$menu = wp_get_nav_menu( $args->id );
	} elseif ( !empty($args->slug) ) {
		$menu = get_term_by( 'slug', $args->slug, 'nav_menu' );
	} else {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			if ( wp_get_nav_menu_items($menu_maybe->term_id) ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}
	
	// If the menu doesn't exists, call the fallback_cb
	if ( !$menu || is_wp_error($menu) )
		return call_user_func($args->fallback_cb, $args );

	if ( 'div' == $args->format )
		echo '<div class="' . esc_attr($args->menu_class) . '"><ul>';

	$args->id = $menu->term_id;

	wp_print_nav_menu($args);
		
	if ( 'div' == $args->format )
		echo '</ul></div>';
}

function wp_print_nav_menu( $args = array() ) {
		// Defaults
		$defaults = array( 'type' => 'frontend', 'name' => 'Menu 1', 'id' => 0, 'desc' => 2, 'before_title' => '', 'after_title' => '');

		$args = wp_parse_args($args, $defaults);
		extract($args, EXTR_SKIP);

		$menu_items = wp_get_nav_menu_items( $id );

		$parent_stack = array();
		$current_parent = 0;
		$parent_menu_order = array();
		// Setup parentage
		foreach ( $menu_items as $menu_item ) {
			$parent_menu_order[ $menu_item->ID ] = $menu_item->menu_order;
		}

	    // Display Loop
		foreach ( $menu_items as $key => $menu_item ) {
			$menu_item = wp_setup_nav_menu_item($menu_item);
			// List Items
			?><li id="menu-<?php echo $menu_item->ID; ?>" value="<?php echo $menu_item->ID; ?>" <?php echo $menu_item->li_class; ?>><?php
			wp_print_nav_menu_item($menu_item, $type, $args);
			// Indent children
			$last_item = ( count( $menu_items ) == $menu_item->menu_order );
			if ( $last_item || $current_parent != $menu_items[ $key + 1 ]->post_parent ) {
				if ( $last_item || in_array( $menu_items[ $key + 1 ]->post_parent, $parent_stack ) ) { ?>
		</li>
<?php					while ( !empty( $parent_stack ) && ($last_item || $menu_items[ $key + 1 ]->post_parent != $current_parent ) ) { ?>
			</ul>
		</li>
<?php					$current_parent = array_pop( $parent_stack );
					} ?>
<?php				} else {
					array_push( $parent_stack, $current_parent );
					$current_parent = $menu_item->ID; ?>
			<ul>
<?php				}
			} else { ?>
		</li>
<?php		}
	}
}

function wp_print_nav_menu_item( $menu_item, $context, $args = array() ) {
	switch ( $context ) {
		case 'backend':
		case 'menu':
?>
						<dl>
							<dt>
								<span class="item-title"><?php echo esc_html($menu_item->title); ?></span>
								<span class="item-controls">
									<span class="item-type"><?php echo esc_html($menu_item->type); ?></span>
									<a class="item-edit thickbox" id="edit<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->menu_order ); ?>" title="<?php _e('Edit Menu Item'); ?>" href="#TB_inline?height=380&width=300&inlineId=menu-item-settings"><?php _e('Edit'); ?></a> |
									<a class="item-delete" id="delete<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->menu_order ); ?>"><?php _e('Delete'); ?></a>
								</span>
							</dt>
						</dl>
						<?php if ( 'backend' == $context ) { ?>
						<a><span class=""></span></a>
						<?php } else { ?>
						<a class="hide" href="<?php echo $menu_item->link; ?>"><?php echo esc_html( $menu_item->title ); ?></a>
						<?php } ?>
						<input type="hidden" name="dbid<?php echo esc_attr( $menu_item->menu_order ); ?>" id="dbid<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->ID ); ?>" />
						<input type="hidden" name="postmenu<?php echo esc_attr( $menu_item->menu_order ); ?>" id="postmenu<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( get_post_meta( $menu_item->ID, 'object_id', true ) ); ?>" />
						<input type="hidden" name="parent<?php echo esc_attr( $menu_item->menu_order ); ?>" id="parent<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->parent_item ); ?>" />
						<input type="hidden" name="icon<?php echo esc_attr( $menu_item->menu_order ); ?>" id="icon<?php echo esc_attr( $menu_item->menu_order ); ?>" value="0" />
						<input type="hidden" name="position<?php echo esc_attr( $menu_item->menu_order ); ?>" id="position<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->menu_order ); ?>" />
						<input type="hidden" name="linktype<?php echo esc_attr( $menu_item->menu_order ); ?>" id="linktype<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( get_post_meta( $menu_item->ID, 'menu_type', true ) ); ?>" />
						<input type="hidden" name="item-title<?php echo esc_attr( $menu_item->menu_order ); ?>" id="item-title<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->title ); ?>" />
						<input type="hidden" name="item-url<?php echo esc_attr( $menu_item->menu_order ); ?>" id="item-url<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->link ); ?>" />
						<input type="hidden" name="item-description<?php echo esc_attr( $menu_item->menu_order ); ?>" id="item-description<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->description ); ?>" />
						<input type="hidden" name="item-attr-title<?php echo esc_attr( $menu_item->menu_order ); ?>" id="item-attr-title<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo esc_attr( $menu_item->post_excerpt ); ?>" />
						<input type="hidden" name="item-target<?php echo esc_attr( $menu_item->menu_order ); ?>" id="item-target<?php echo esc_attr( $menu_item->menu_order ); ?>" value="<?php echo ( get_post_meta( $menu_item->ID, 'menu_new_window', true ) ? '1' : '0' ); ?>" />
<?php
		break;

		case 'frontend':
			// Override for menu descriptions
			$advanced_option_descriptions = get_option('wp_settings_nav_menu_advanced_options');
			if ( $advanced_option_descriptions == 'no' )
				$args['desc'] = 2;
?>
			<a title="<?php echo esc_attr( $menu_item->anchor_title ); ?>" href="<?php echo esc_url( $menu_item->link ); ?>" <?php echo $menu_item->target; ?>><?php echo $args['before_title'] . esc_html( $menu_item->title ) . $args['after_title']; ?><?php

							if ( $advanced_option_descriptions == 'no' ) {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ( ($args['desc'] == 1) || ($args['desc'] == 0) ) {
									?><span class="nav-description"><?php echo $menu_item->description; ?></span><?php
								}
							} else {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ( $args['desc'] == 1 ) {
									?><span class="nav-description"><?php echo $menu_item->description; ?></span><?php
								}
							}
						?></a>
<?php
		break;

		case 'default':
			$menu_id = 'menu-item-' . $menu_item->ID;
?>
					<dl>
						<dt>
							<label class="item-title"><input type="checkbox" id="<?php echo esc_attr($menu_id); ?>" onclick="wp_update_queue('<?php echo esc_js( $menu_item->append ); ?>','<?php echo esc_js( $menu_item->title ); ?>','<?php echo esc_js( $menu_item->link ); ?>','<?php echo esc_js( $menu_item->ID ); ?>','<?php echo esc_js( $menu_item->parent_item ); ?>','<?php echo esc_js( $menu_item->description ); ?>')" name="<?php echo esc_attr( $menu_item->title ); ?>" value="<?php echo esc_attr( $menu_item->link ); ?>" /><?php echo $menu_item->title; ?></label>
						</dt>
					</dl>
<?php
		break;
	}
}

?>