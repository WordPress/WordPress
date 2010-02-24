<?php

/**
 * Outputs a navigation menu.
 *
 * @since 3.0.0
 *
 * @param array $args Arguments
 */
function wp_nav_menu( $args = array() ) {
	wp_print_nav_menu($args);
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

function wp_print_nav_menu_item($menu_item, $context, $args = array() ) {
	switch ( $context ) {
		case 'backend':
		case 'menu':
?>
						<dl>
							<dt>
								<span class="title"><?php echo esc_html($menu_item->title); ?></span>
								<span class="controls">
								<span class="type"><?php echo esc_html($menu_item->type); ?></span>
								<a id="edit<?php echo $menu_item->menu_order; ?>" onclick="edititem(<?php echo $menu_item->menu_order; ?>)" value="<?php echo $menu_item->menu_order; ?>"><img class="edit" alt="<?php esc_attr_e('Edit Menu Item'); ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" src="<?php echo admin_url('images/ico-edit.png'); ?>" /></a>
								<a id="remove<?php echo $menu_item->menu_order; ?>" onclick="removeitem(<?php echo $menu_item->menu_order; ?>)" value="<?php echo $menu_item->menu_order; ?>"><img class="remove" alt="<?php esc_attr_e('Remove from Custom Menu'); ?>" title="<?php esc_attr_e('Remove from Custom Menu'); ?>" src="<?php echo admin_url('images/ico-close.png'); ?>" /></a>
								<a id="view<?php echo $menu_item->menu_order; ?>" target="_blank" href="<?php echo $menu_item->link; ?>"><img alt="<?php esc_attr_e('View Page'); ?>" title="<?php esc_attr_e('View Page'); ?>" src="<?php echo admin_url('images/ico-viewpage.png'); ?>" /></a>
								</span>
							</dt>
						</dl>
						<?php if ( 'backend' == $context ) { ?>
						<a><span class=""></span></a>
						<?php } else { ?>
						<a class="hide" href="<?php echo $menu_item->link; ?>"><?php echo $menu_item->title; ?></a>
						<?php } ?>
						<input type="hidden" name="dbid<?php echo $menu_item->menu_order; ?>" id="dbid<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->ID; ?>" />
						<input type="hidden" name="postmenu<?php echo $menu_item->menu_order; ?>" id="postmenu<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->ID; ?>" />
						<input type="hidden" name="parent<?php echo $menu_item->menu_order; ?>" id="parent<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->parent_item; ?>" />
						<input type="hidden" name="title<?php echo $menu_item->menu_order; ?>" id="title<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->title; ?>" />
						<input type="hidden" name="linkurl<?php echo $menu_item->menu_order; ?>" id="linkurl<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->link; ?>" />
						<input type="hidden" name="description<?php echo $menu_item->menu_order; ?>" id="description<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->description; ?>" />
						<input type="hidden" name="icon<?php echo $menu_item->menu_order; ?>" id="icon<?php echo $menu_item->menu_order; ?>" value="0" />
						<input type="hidden" name="position<?php echo $menu_item->menu_order; ?>" id="position<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->menu_order; ?>" />
						<input type="hidden" name="linktype<?php echo $menu_item->menu_order; ?>" id="linktype<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->type; ?>" />
						<input type="hidden" name="anchortitle<?php echo $menu_item->menu_order; ?>" id="anchortitle<?php echo $menu_item->menu_order; ?>" value="<?php echo esc_html( $menu_item->post_excerpt ); ?>" />
						<input type="hidden" name="newwindow<?php echo $menu_item->menu_order; ?>" id="newwindow<?php echo $menu_item->menu_order; ?>" value="<?php echo ( '' == $menu_item->post_content_filtered ? '0' : '1' ); ?>" />
<?php
		break;

		case 'frontend':
			// Override for menu descriptions
			$advanced_option_descriptions = get_option('wp_settings_custom_nav_advanced_options');
			if ( $advanced_option_descriptions == 'no' )
				$args['desc'] = 2;
?>
			<a title="<?php echo $menu_item->anchor_title; ?>" href="<?php echo $menu_item->link; ?>" <?php echo $menu_item->target; ?>><?php echo $args['before_title'] . $menu_item->title . $args['after_title']; ?><?php

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
			$templatedir = get_bloginfo('url');
?>
					<dl>
					<dt>
					<span class="title"><?php echo $menu_item->title; ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','<?php echo $menu_item->append; ?>','<?php echo $menu_item->title; ?>','<?php echo $menu_item->link; ?>','<?php echo $menu_item->ID; ?>','<?php echo $menu_item->parent_item ?>','<?php echo $menu_item->description; ?>')" name="<?php echo $menu_item->title; ?>" value="<?php echo $menu_item->link; ?>"><img alt="<?php esc_attr_e('Add to Custom Menu'); ?>" title="<?php esc_attr_e('Add to Custom Menu'); ?>" src="<?php echo admin_url('images/ico-add.png'); ?>" /></a> </dt>
					</dl>
<?php
		break;
	}
}

?>