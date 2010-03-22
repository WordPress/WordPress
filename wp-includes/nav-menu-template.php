<?php
/**
 * Displays a navigation menu.
 *
 * Optional $args contents:
 *
 * id - The menu id. Defaults to blank.
 * slug - The menu slug. Defaults to blank.
 * menu_class - CSS class to use for the div container of the menu list. Defaults to 'menu'.
 * format - Whether to format the ul. Defaults to 'div'.
 * fallback_cb - If the menu doesn't exists, a callback function will fire. Defaults to 'wp_page_menu'.
 * before - Text before the link text.
 * after - Text after the link text.
 * link_before - Text before the link.
 * link_after - Text after the link.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 * show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'menu_class' => 'menu', 'echo' => true,
	'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '',
	'depth' => 0, 'walker' => '' );

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_nav_menu_args', $args );
	$args = (object) $args;

	// Get the nav menu
	$menu = wp_get_nav_menu_object( $args->menu );

	// If we couldn't find a menu based off the name, id or slug,
	// get the first menu that has items.
	if ( !$menu ) {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			if ( wp_get_nav_menu_items($menu_maybe->term_id) ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}

	if ( $menu && ! is_wp_error( $menu ) )
		$args->menu = $menu->term_id;
	$nav_menu = '';

	if ( 'div' == $args->container ) {
		$class = $args->container_class ? ' class="' . esc_attr($args->container_class) . '"' : '';
		
		if ( is_nav_menu($menu) ) {
			$nav_menu .= '<div id="menu-' . $menu->slug . '"'. $class .'>';
		} else {
			$nav_menu .= '<div'. $class .'>';
		}
	}

	$nav_menu .= wp_get_nav_menu( $args );

	if ( 'div' == $args->container )
		$nav_menu .= '</div>';
	
	$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

	if ( $args->echo )
		echo $nav_menu;
	else
		return $nav_menu;
}

/**
 * Returns a Navigation Menu.
 *
 * See wp_nav_menu() for args.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 * @return mixed $output False if menu doesn't exists, else, returns the menu.
 **/
function wp_get_nav_menu( $args = array() ) {
	$defaults = array( 'menu' => '', 'menu_class' => 'menu', 'context' => 'frontend', 'depth' => 0,
	'fallback_cb' => '', 'walker' => '', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', );
	
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_get_nav_menu_args', $args );
	$args = (object) $args;
	
	// Variable setup
	$nav_menu = '';
	$items = '';
	
	// Get the menu object
	$menu = wp_get_nav_menu_object( $args->menu );
	
	// If the menu exists, get it's items.
	if ( $menu && !is_wp_error($menu) )
		$menu_items = wp_get_nav_menu_items( $menu->term_id, $args->context );
	
	// If no menu was found or if the menu has no items, call the fallback_cb
	if ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) ) ) {
		if ( function_exists($args->fallback_cb) || is_callable( $args->fallback_cb ) ) {
			$_args = array_merge( (array) $args, array('echo' => false) );
			return call_user_func( $args->fallback_cb, $_args );
		}
	}
		
	// Set up the $menu_item variables
	foreach ( (array) $menu_items as $key => $menu_item )
		$menu_items[$menu_item->menu_order] = wp_setup_nav_menu_item( $menu_item, 'frontend' );
	
	$items .= walk_nav_menu_tree( $menu_items, $args->depth, $args );
	
	// CSS class
	$ul_class = $args->menu_class ? ' class="'. $args->menu_class .'"' : '';
	$nav_menu .= '<ul'. $ul_class .'>';
	
	// Allow plugins to hook into the menu to add their own <li>'s
	if ( 'frontend' == $args->context ) {
		$items = apply_filters( 'wp_nav_menu_items', $items, $args );
		$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );
		$nav_menu .= $items;
	} else {
		$nav_menu .= $items;
	}
	
	$nav_menu .= '</ul>';
	
	return apply_filters( 'wp_get_nav_menu', $nav_menu );
}

/**
 * Returns the menu item formatted based on it's context.
 *
 * @since 3.0.0
 *
 * @param string $menu_item The menu item to format.
 * @param string $context The context to which the menu item will be formatted to.
 * @param string $args Optional. Args used for the 'template' context.
 * @return string $output The menu formatted menu item.
 */
function wp_get_nav_menu_item( $menu_item, $context = 'frontend', $args = array() ) {
	$output = '';
	switch ( $context ) {
		case 'frontend':
			$attributes  = ( isset($menu_item->attr_title) && '' != $menu_item->attr_title ) ? ' title="'. esc_attr($menu_item->attr_title) .'"' : '';
			$attributes .= ( isset($menu_item->target) && '' != $menu_item->target ) ? ' target="'. esc_attr($menu_item->target) .'"' : '';
			$attributes .= ( isset($menu_item->xfn) && '' != $menu_item->xfn ) ? ' rel="'. esc_attr($menu_item->xfn) .'"' : '';
			$attributes .= ( isset($menu_item->url) && '' != $menu_item->url ) ? ' href="'. esc_attr($menu_item->url) .'"' : '';
			
			$output .= esc_html( $args->before );
			$output .= '<a'. $attributes .'>';
			$output .= esc_html( $args->link_before . apply_filters('the_title', $menu_item->title) . $args->link_after );
			$output .= '</a>';
			$output .= esc_html( $args->after );
			
			break;
		
		case 'backend':
			$output .= '<dl><dt>';
			$output .= '<span class="item-title">'. esc_html( $menu_item->title ) .'</span>';
			$output .= '<span class="item-controls">';
			$output .= '<span class="item-type">'. esc_html( $menu_item->append ) .'</span>';
			
			// Actions
			$output .= '<a class="item-edit thickbox" id="edit-'. esc_attr( $menu_item->ID ) .'" value="'. esc_attr( $menu_item->ID ) .'" title="'. __('Edit Menu Item') .'" href="#TB_inline?height=540&width=300&inlineId=menu-item-settings">'. __('Edit') .'</a> | ';
			$output .= '<a class="item-delete" id="delete-'. esc_attr( $menu_item->ID ) .'" value="'. esc_attr( $menu_item->ID ) .'">'. __('Delete') .'</a>';
			
			$output .= '</span></dt></dl>';
			
			// Menu Item Settings
			$output .= '<input type="hidden" name="menu-item-db-id[]" value="'. esc_attr( $menu_item->ID ) .'" />';
			$output .= '<input type="hidden" name="menu-item-object-id[]" value="'. esc_attr( $menu_item->object_id ) .'" />';
			$output .= '<input type="hidden" name="menu-item-object[]" value="'. esc_attr( $menu_item->object ) .'" />';
			$output .= '<input type="hidden" name="menu-item-parent-id[]" value="'. esc_attr( $menu_item->post_parent ) .'" />';
			$output .= '<input type="hidden" name="menu-item-position[]" value="'. esc_attr( $menu_item->menu_order ) .'" />';
			$output .= '<input type="hidden" name="menu-item-type[]" value="'. esc_attr( $menu_item->type ) .'" />';
			$output .= '<input type="hidden" name="menu-item-title[]" value="'. esc_attr( $menu_item->title ) .'" />';
			$output .= '<input type="hidden" name="menu-item-url[]" value="'. esc_attr( $menu_item->url ) .'" />';
			$output .= '<input type="hidden" name="menu-item-description[]" value="'. esc_attr( $menu_item->description ) .'" />';
			$output .= '<input type="hidden" name="menu-item-classes[]" value="'. esc_attr( $menu_item->classes ) .'" />';
			$output .= '<input type="hidden" name="menu-item-xfn[]" value="'. esc_attr( $menu_item->xfn ) .'" />';
			$output .= '<input type="hidden" name="menu-item-attr-title[]" value="'.esc_attr( $menu_item->post_excerpt )  .'" />';
			$output .= '<input type="hidden" name="menu-item-target[]" value="'. esc_attr( $menu_item->target ) .'" />';
			break;
		
		case 'custom':
		case 'taxonomy':
		case 'post_type':
			$output .= '<label class="menu-item-title"><input type="checkbox" id="'. esc_attr( 'menu-item-' . $menu_item->object_id ) .'" value="'. esc_attr( $menu_item->url ) .'" />'. $menu_item->title .'</label>';
			
			// Menu item hidden fields
			$output .= '<input type="hidden" class="menu-item-db-id" value="0" />';
			$output .= '<input type="hidden" class="menu-item-object-id" value="'. esc_attr( $menu_item->object_id ) .'" />';
			$output .= '<input type="hidden" class="menu-item-object" value="'. esc_attr( $menu_item->object ) .'" />';
			$output .= '<input type="hidden" class="menu-item-parent-id" value="'. esc_attr( $menu_item->post_parent ) .'" />';
			$output .= '<input type="hidden" class="menu-item-type" value="'. esc_attr( $menu_item->type ) .'" />';
			$output .= '<input type="hidden" class="menu-item-append" value="'. esc_attr( $menu_item->append ) .'" />';
			$output .= '<input type="hidden" class="menu-item-title" value="'. esc_attr( $menu_item->title ) .'" />';
			$output .= '<input type="hidden" class="menu-item-url" value="'. esc_attr( $menu_item->url ) .'" />';
			$output .= '<input type="hidden" class="menu-item-append" value="'. esc_attr( $menu_item->append ) .'" />';
			$output .= '<input type="hidden" class="menu-item-target" value="'. esc_attr( $menu_item->target ) .'" />';
			$output .= '<input type="hidden" class="menu-item-attr_title" value="'. esc_attr( $menu_item->attr_title ) .'" />';
			$output .= '<input type="hidden" class="menu-item-description" value="'. esc_attr( $menu_item->description ) .'" />';
			$output .= '<input type="hidden" class="menu-item-classes" value="'. esc_attr( $menu_item->classes ) .'" />';
			$output .= '<input type="hidden" class="menu-item-xfn" value="'. esc_attr( $menu_item->xfn ) .'" />';
			break;
	}
	
	return $output;
}
?>