<?php

	/***
	***	@add dynamic profile headers
	***/
	add_filter( 'wp_nav_menu_items', 'um_add_custom_message_to_menu', 10, 2 );
	function um_add_custom_message_to_menu( $items, $args ) {
		global $ultimatemember;
		
		// this feature required logged in user
		if ( !is_user_logged_in() )
			return $items;
		
		um_fetch_user( get_current_user_id() );
		$items = $ultimatemember->shortcodes->convert_user_tags( $items );
		um_reset_user();
		
		return $items;
	}

	/***
	***	@conditional menu items
	***/
	if ( ! is_admin() ) {
	
		add_filter( 'wp_get_nav_menu_items', 'um_conditional_nav_menu', 9999, 3 );
		function um_conditional_nav_menu( $items, $menu, $args ) {
			
			$hide_children_of = array();
			
			foreach($items as $key => $item){
			
				$mode = get_post_meta($item->ID, 'menu-item-um_nav_public', true);
				$roles = get_post_meta($item->ID, 'menu-item-um_nav_roles', true);
				
				$visible = true;
				
				// hide any item that is the child of a hidden item
				if( in_array( $item->menu_item_parent, $hide_children_of ) ){
					$visible = false;
					$hide_children_of[] = $item->ID; // for nested menus
				}
				
				if ( isset( $mode ) && $visible ){
				
					switch( $mode ) {
					
						case 2: 
							if ( is_user_logged_in() && isset($roles) && !empty($roles)) {
								if ( in_array( um_user('role'), (array)$roles) ) {
									$visible = true;
								} else {
									$visible = false;
								}
							} else {
								$visible = is_user_logged_in() ? true : false;
							}
							break;

						case 1:
							$visible = ! is_user_logged_in() ? true : false;
							break;
							
					}
					
				}
				
				// add filter to work with plugins that don't use traditional roles
				$visible = apply_filters( 'um_nav_menu_roles_item_visibility', $visible, $item );

				// unset non-visible item
				if ( ! $visible ) {
					$hide_children_of[] = $item->ID; // store ID of item 
					unset( $items[$key] ) ;
				}
				
			}
			
			return $items;
		}
	
	}