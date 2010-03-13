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
 * before_link - Output text before the link.
 * after_link - Output text after the link.
 * before_title - Output text before the link text.
 * before_title - Output text after the link text.
 * echo - Whether to echo the menu or return it. Defaults to echo.
 *
 * TODO:
 * show_home - If you set this argument, then it will display the link to the home page. The show_home argument really just needs to be set to the value of the text of the link.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	$defaults = array( 'menu' => '', 'menu_class' => 'menu', 'format' => 'div', 'echo' => true,
	'fallback_cb' => 'wp_page_menu', 'link_before' => '', 'link_after' => '', 'before_link' => '', 'after_link' => '', );
	
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

	if ( $menu )
		$args->menu = $menu->term_id;
	$nav_menu = '';

	if ( 'div' == $args->format ) {
		if ( $menu )
			$nav_menu .= '<div id="menu-' . $menu->slug . '" class="' . esc_attr($args->menu_class) . '">';
		else
			$nav_menu .= '<div id="menu-default">';
	}

	$nav_menu .= wp_get_nav_menu( $args );

	if ( 'div' == $args->format )
		$nav_menu .= '</div>';

	$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu );

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
	$defaults = array( 'menu' => '', 'menu_class' => 'menu', 'ul_class' => '', 'format' => 'div', 'type' => 'frontend',
	'fallback_cb' => '', 'link_before' => '', 'link_after' => '', 'before_link' => '', 'after_link' => '', );
	
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_get_nav_menu_args', $args );
	$args = (object) $args;
	
	$menu = wp_get_nav_menu_object( $args->menu );
	
	// If no menu was found, call the fallback_cb
	if ( !$menu || is_wp_error($menu) ) {
		if ( function_exists($args->fallback_cb) ) {
			$_args = array_merge( (array)$args, array('echo' => false) );
			return call_user_func( $args->fallback_cb, $_args );
		}
	}
	
	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	$nav_menu = '';
	$parent_stack = array();
	$current_parent = 0;
	$parent_menu_order = array();
	
	// Setup parentage
	foreach ( $menu_items as $menu_item )
		$parent_menu_order[ $menu_item->ID ] = $menu_item->menu_order;
	
	$ul_class = isset($args->ul_class) ? ' class="'. $args->ul_class .'"' : '';
	$nav_menu .= '<ul'. $ul_class .'>';
	
	// Display Loop
	foreach ( $menu_items as $key => $menu_item ) :
		// Setup the $menu_item variables
		$menu_item = wp_setup_nav_menu_item( $menu_item );

		$maybe_value = 'frontend' == $args->type ? '' : ' value="'. $menu_item->ID .'"';
		$classes = 'frontend' == $args->type ? ' class="menu-item-'. $menu_item->type . $menu_item->li_class .'"' : '';

		$nav_menu .= '<li id="menu-item-'. $menu_item->ID .'"'. $maybe_value . $classes .'>';
		$nav_menu .= wp_get_nav_menu_item( $menu_item, $args->type, $args );
		
		// Indent children
		$last_item = ( count( $menu_items ) == $menu_item->menu_order );
		if ( $last_item || $current_parent != $menu_items[ $key + 1 ]->post_parent ) {
			if ( $last_item || in_array( $menu_items[ $key + 1 ]->post_parent, $parent_stack ) ) {
				$nav_menu .= '</li>';
				while ( !empty( $parent_stack ) && ($last_item || $menu_items[ $key + 1 ]->post_parent != $current_parent ) ) {
					$nav_menu .= '</ul></li>';
					$current_parent = array_pop( $parent_stack );
				}
			} else {
				array_push( $parent_stack, $current_parent );
				$current_parent = $menu_item->ID;
				$nav_menu .= '<ul>';
			}
		} else {
			$nav_menu .= '</li>';
		}

	endforeach;
	
	$nav_menu .= '</ul>';
	
	return apply_filters( 'wp_get_nav_menu', $nav_menu );
}

/**
 * Returns a menu item.
 *
 * @since 3.0.0
 *
 * @param object $menu_item The menu item
 * @param string $context frontend|backend|default
 * @param array $args See wp_get_nav_menu().
 **/
function wp_get_nav_menu_item( $menu_item, $context, $args = array() ) {
	$item = '';
	switch ( $context ) {
		case 'frontend':
			$attr_title = ( isset($menu_item->anchor_title) && '' != $menu_item->anchor_title ) ? ' title="'. esc_attr($menu_item->anchor_title) .'"' : '';
			$href = isset($menu_item->link) ? ' href="'. esc_url($menu_item->link) .'"' : '';
			
			$item .= '<a'. $attr_title . $href . $menu_item->target .'>';
			$item .= $args->before_link . esc_html( $menu_item->title ) . $args->after_link;
			$item .= '</a>';
			
			break;
		
		case 'backend':
			$item .= '<dl><dt>';
			$item .= '<span class="item-title">'. esc_html($menu_item->title) .'</span>';
			$item .= '<span class="item-controls">';
			$item .= '<span class="item-type">'. esc_html($menu_item->type) .'</span>';
			
			// Actions
			$item .= '<a class="item-edit thickbox" id="edit'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->menu_order ) .'" title="'. __('Edit Menu Item') .'" href="#TB_inline?height=380&width=300&inlineId=menu-item-settings">'. __('Edit') .'</a> | ';
			$item .= '<a class="item-delete" id="delete'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->menu_order ) .'">'. __('Delete') .'</a>';
			
			$item .= '</dt></dl>';
			
			// Menu Item Settings
			$item .= '<input type="hidden" id="item-dbid'. esc_attr( $menu_item->menu_order ) .'" name="item-dbid'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->ID ) .'" />';
			$item .= '<input type="hidden" id="item-postmenu'. esc_attr( $menu_item->menu_order ) .'" name="item-postmenu'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( get_post_meta( $menu_item->ID, 'object_id', true ) ) .'" />';
			$item .= '<input type="hidden" id="item-parent'. esc_attr( $menu_item->menu_order ) .'" name="item-parent'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->parent_item ) .'" />';
			$item .= '<input type="hidden" id="item-position'. esc_attr( $menu_item->menu_order ) .'" name="item-position'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->menu_order ) .'" />';
			$item .= '<input type="hidden" id="item-type'. esc_attr( $menu_item->menu_order ) .'" name="item-type'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( get_post_meta( $menu_item->ID, 'menu_type', true ) ) .'" />';
			$item .= '<input type="hidden" id="item-title'. esc_attr( $menu_item->menu_order ) .'" name="item-title'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->title ) .'" />';
			$item .= '<input type="hidden" id="item-url'. esc_attr( $menu_item->menu_order ) .'" name="item-url'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->link ) .'" />';
			$item .= '<input type="hidden" id="item-description'. esc_attr( $menu_item->menu_order ) .'" name="item-description'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( $menu_item->description ) .'" />';
			$item .= '<input type="hidden" id="item-attr-title'. esc_attr( $menu_item->menu_order ) .'" name="item-attr-title'. esc_attr( $menu_item->menu_order ) .'" value="'.esc_attr( $menu_item->post_excerpt )  .'" />';
			$item .= '<input type="hidden" id="item-target'. esc_attr( $menu_item->menu_order ) .'" name="item-target'. esc_attr( $menu_item->menu_order ) .'" value="'. esc_attr( get_post_meta( $menu_item->ID, 'menu_new_window', true ) ? '1' : '0' ) .'" />';
			break;
			
		case 'default':
			$menu_id = 'menu-item-' . $menu_item->ID;
			$item .= '<label class="item-title"><input type="checkbox" id="'. esc_attr($menu_id) .'" name="'. esc_attr( $menu_item->title ) .'" value="'. esc_attr( $menu_item->link ) .'" />'. $menu_item->title .'</label>';
			
			// Menu Item Settings
			$item .= '<input type="hidden" class="item-type" value="'. esc_attr( $menu_item->type ) .'" />';
			$item .= '<input type="hidden" class="item-title" value="'. esc_attr( $menu_item->title ) .'" />';
			$item .= '<input type="hidden" class="item-url" value="'. esc_attr( $menu_item->link ) .'" />';
			$item .= '<input type="hidden" class="item-dbid" value="'. esc_attr( $menu_item->ID ) .'" />';
			$item .= '<input type="hidden" class="item-parent" value="'. esc_attr( $menu_item->parent_item ) .'" />';
			$item .= '<input type="hidden" class="item-description" value="'. esc_attr( $menu_item->description ) .'" />';
			break;
	}
	return apply_filters( 'wp_get_nav_menu_item', $item );
}
?>