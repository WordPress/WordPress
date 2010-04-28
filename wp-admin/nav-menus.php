<?php
/**
 * WordPress Administration for Navigation Menus
 * Interface functions
 *
 * @version 2.0.0
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Administration Bootstrap */
require_once( 'admin.php' );

// Load all the nav menu interface functions
require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

// Permissions Check
if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

// Nav Menu CSS
wp_admin_css( 'nav-menu' );

// jQuery
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-droppable' );
wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_script( 'jquery-autocomplete' );

// Nav Menu functions
wp_enqueue_script( 'nav-menu' );

// Metaboxes
wp_enqueue_script( 'common' );
wp_enqueue_script( 'wp-lists' );
wp_enqueue_script( 'postbox' );

// Container for any messages displayed to the user
$messages_div = '';

// Container that stores the name of the active menu
$nav_menu_selected_title = '';

// The menu id of the current menu being edited
$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

// Allowed actions: add, update, delete
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

switch ( $action ) {
	case 'add-menu-item':
		if ( current_user_can( 'switch_themes' ) ) {
			check_admin_referer( 'add-menu_item', 'menu-settings-column-nonce' );
			if ( isset( $_REQUEST['menu-item'] ) ) {
				wp_save_nav_menu_item( $nav_menu_selected_id, $_REQUEST['menu-item'] );
			}
		}
		break;
	case 'move-down-menu-item' :
		// moving down a menu item is the same as moving up the next in order
		check_admin_referer( 'move-menu_item' );
		$menu_item_id = (int) $_REQUEST['menu-item'];
		$next_item_id = 0;
		if ( is_nav_menu_item( $menu_item_id ) ) {
			$menus = isset( $_REQUEST['menu'] ) ? array( (int) $_REQUEST['menu'] ) : wp_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $menus ) ) {
				foreach( (array) $menus as $menu_id ) {
					$move_down_ordered_menu_items = (array) wp_get_nav_menu_items( $menu_id );
					while ( $next = array_shift( $move_down_ordered_menu_items ) ) {
						if ( isset( $next->ID ) && $next->ID == $menu_item_id ) {
							break;
						}
					}

					if ( $following = array_shift( $move_down_ordered_menu_items ) ) {
						$next_item_id = (int) $following->ID;
					}
				}
			}
		}
		// fall through to next case
	case 'move-up-menu-item' :
		check_admin_referer( 'move-menu_item' );
		$menu_item_id = empty( $next_item_id ) ? (int) $_REQUEST['menu-item'] : $next_item_id;
		if ( is_nav_menu_item( $menu_item_id ) ) {
			$menus = isset( $_REQUEST['menu'] ) ? array( (int) $_REQUEST['menu'] ) : wp_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $menus ) ) {
				foreach( (array) $menus as $menu_id ) {
					$ordered_menu_items = wp_get_nav_menu_items( $menu_id );
					$menu_item_data = get_post( $menu_item_id , ARRAY_A );

					// setup the data we need in one pass through the array of menu items
					$dbids_to_orders = array();
					$orders_to_dbids = array();
					$objectids_to_dbids = array();
					$dbids_to_objectids = array();
					foreach( (array) $ordered_menu_items as $ordered_menu_item_object ) {
						if ( isset( $ordered_menu_item_object->ID ) ) {
							if ( isset( $ordered_menu_item_object->menu_order ) ) {
								$dbids_to_orders[$ordered_menu_item_object->ID] = $ordered_menu_item_object->menu_order;
								$orders_to_dbids[$ordered_menu_item_object->menu_order] = $ordered_menu_item_object->ID;
							}

							$possible_object_id = (int) get_post_meta( $ordered_menu_item_object->ID, '_menu_item_object_id', true );
							if ( ! empty( $possible_object_id ) ) {
								$dbids_to_objectids[$ordered_menu_item_object->ID] = $possible_object_id;
								$objectids_to_dbids[$possible_object_id] = $ordered_menu_item_object->ID;
							} 
						}
					}


					// if this menu item is not first
					if ( ! empty( $dbids_to_orders[$menu_item_id] ) && ! empty( $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] ) ) {
						
						// if this menu item is a child of the previous
						if ( 
							! empty( $menu_item_data['post_parent'] ) && 
							isset( $objectids_to_dbids[$menu_item_data['post_parent']] ) &&
							isset( $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] ) &&
							( $objectids_to_dbids[$menu_item_data['post_parent']] == $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] )
						) {

							$parent_db_id = $objectids_to_dbids[$menu_item_data['post_parent']];
							$parent_data = get_post( $parent_db_id, ARRAY_A );

							if ( ! is_wp_error( $parent_data ) ) {
								
								// if there is something before the parent, make menu item a child of the parent's parent
								if ( ! empty( $dbids_to_orders[$parent_db_id] ) && ! empty( $orders_to_dbids[$dbids_to_orders[$parent_db_id] - 1] ) ) {
									$menu_item_data['post_parent'] = $parent_data['post_parent'];

								// else there isn't something before the parent
								} else {
									$menu_item_data['post_parent'] = 0;
								}
								
								// set former parent's [menu_order] to that of menu-item's
								$parent_data['menu_order'] = $parent_data['menu_order'] + 1;

								// set menu-item's [menu_order] to that of former parent
								$menu_item_data['menu_order'] = $menu_item_data['menu_order'] - 1;
								
								// save changes
								wp_update_post($menu_item_data);
								wp_update_post($parent_data);
							}

						// else this menu item is not a child of the previous
						} elseif ( isset($dbids_to_objectids[$orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1]] ) ) {
							// just make it a child of the previous; keep the order
							$menu_item_data['post_parent'] = (int) $dbids_to_objectids[$orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1]];
							wp_update_post($menu_item_data);
						}
					}
				}
			}
		}
		break;

	case 'delete-menu-item':
		$menu_item_id = (int) $_REQUEST['menu-item'];

		check_admin_referer( 'delete-menu_item_' . $menu_item_id );


		if ( is_nav_menu_item( $menu_item_id ) ) {
			if ( wp_delete_post( $menu_item_id, true ) ) {
				
				$messages_div = '<div id="message" class="updated"><p>' . __('The menu item has been successfully deleted.') . '</p></div>';
			}
		}
		break;
	case 'delete':
		check_admin_referer( 'delete-nav_menu-' . $nav_menu_selected_id );

		if ( is_nav_menu( $nav_menu_selected_id ) ) {
			$delete_nav_menu = wp_delete_nav_menu( $nav_menu_selected_id );

			if ( is_wp_error($delete_nav_menu) ) {
				$messages_div = '<div id="message" class="error"><p>' . $delete_nav_menu->get_error_message() . '</p></div>';
			} else {
				$messages_div = '<div id="message" class="updated"><p>' . __('The menu has been successfully deleted.') . '</p></div>';
				$nav_menu_selected_id = 0; // Reset the selected menu
			}
			unset( $delete_nav_menu );
		}
		break;

	case 'update':
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Add Menu
		if ( 0 == $nav_menu_selected_id ) {
			if ( current_theme_supports('nav-menus') || current_theme_supports('widgets') ) {
				$new_menu_title = esc_html( $_POST['menu-name'] );

				if ( $new_menu_title ) {
					$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

					if ( is_wp_error( $_nav_menu_selected_id ) ) {
						$messages_div = '<div id="message" class="error"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
					} else {
						$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
						$nav_menu_selected_id = $_nav_menu_selected_id;
						$nav_menu_selected_title = $_menu_object->name;
						$messages_div = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> menu has been successfully created.'), $nav_menu_selected_title ) . '</p></div>';
					}
				} else {
					$messages_div = '<div id="message" class="error"><p>' . __('Please enter a valid menu name.') . '</p></div>';
				}
			}

		// update existing menu
		} else {

			$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

			if ( ! is_wp_error( $_menu_object ) ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $_POST['menu-name'] ) );
				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$_menu_object = $_nav_menu_selected_id;
					$messages_div = '<div id="message" class="error"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items

			if ( ! is_wp_error( $_menu_object ) ) {
				$menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array('orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID') );

				// Loop through all the menu items' POST variables
				if ( ! empty( $_POST['menu-item-db-id'] ) ) {
					foreach( (array) $_POST['menu-item-db-id'] as $_key => $k ) {

						// Menu item title can't be blank
						if ( '' == $_POST['menu-item-title'][$_key] )
							continue;
		
						$args = array(
							'menu-item-db-id' => $_POST['menu-item-db-id'][$_key],
							'menu-item-object-id' => $_POST['menu-item-object-id'][$_key],
							'menu-item-object' => $_POST['menu-item-object'][$_key],
							'menu-item-parent-id' => $_POST['menu-item-parent-id'][$_key],
							'menu-item-position' => $_POST['menu-item-position'][$_key],
							'menu-item-type' => $_POST['menu-item-type'][$_key],
							'menu-item-append' => $_POST['menu-item-append'][$_key],
							'menu-item-title' => $_POST['menu-item-title'][$_key],
							'menu-item-url' => $_POST['menu-item-url'][$_key],
							'menu-item-description' => $_POST['menu-item-description'][$_key],
							'menu-item-attr-title' => $_POST['menu-item-attr-title'][$_key],
							'menu-item-target' => $_POST['menu-item-target'][$_key],
							'menu-item-classes' => $_POST['menu-item-classes'][$_key],
							'menu-item-xfn' => $_POST['menu-item-xfn'][$_key],
						);

						$menu_item_db_id = wp_update_nav_menu_item( $nav_menu_selected_id, ( $_POST['menu-item-db-id'][$_key] != $_key ? 0 : $_key ), $args );

						if ( ! is_wp_error( $menu_item_db_id ) && isset( $menu_items[$menu_item_db_id] ) ) {
							unset( $menu_items[$menu_item_db_id] );
						}
					}
				}

				// Remove menu items from the menu that weren't in $_POST
				if ( ! empty( $menu_items ) ) {
					foreach ( array_keys( $menu_items ) as $menu_item_id ) {
						if ( is_nav_menu_item( $menu_item_id ) ) {
							wp_delete_post( $menu_item_id );
						}
					}
				}

				do_action( 'wp_update_nav_menu', $nav_menu_selected_id );

				$messages_div = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> menu has been updated.'), $nav_menu_selected_title ) . '</p></div>';
				unset( $menu_items );
			}
		}
		break;
}

// Get all nav menus
$nav_menus = wp_get_nav_menus();

// Get recently edited nav menu
$recently_edited = (int) get_user_option( 'nav_menu_recently_edited' );

// If there was no recently edited menu, and $nav_menu_selected_id is a nav menu, update recently edited menu.
if ( !$recently_edited && is_nav_menu( $nav_menu_selected_id ) ) {
	$recently_edited = $nav_menu_selected_id;

// Else if $nav_menu_selected_id is not a menu and not requesting that we create a new menu, but $recently_edited is a menu, grab that one.
} elseif ( 0 == $nav_menu_selected_id && ! isset( $_REQUEST['menu'] ) && is_nav_menu( $recently_edited ) ) {
	$nav_menu_selected_id = $recently_edited;

// Else try to grab the first menu from the menus list
} elseif ( 0 == $nav_menu_selected_id && ! isset( $_REQUEST['menu'] ) && ! empty($nav_menus) ) {
	$nav_menu_selected_id = $nav_menus[0]->term_id;
}

// Update the user's setting
if ( $nav_menu_selected_id != $recently_edited && is_nav_menu( $nav_menu_selected_id ) )
	update_user_meta( $current_user->ID, 'nav_menu_recently_edited', $nav_menu_selected_id );

// If there's a menu, get its name.
if ( ! $nav_menu_selected_title && is_nav_menu( $nav_menu_selected_id ) ) {
	$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );
	$nav_menu_selected_title = ! is_wp_error( $_menu_object ) ? $_menu_object->name : '';
}

// The theme supports menus
if ( current_theme_supports('nav-menus') ) {
	// Register nav menu metaboxes
	wp_nav_menu_meta_boxes_setup();

// The theme does not support menus but supports widgets
} elseif ( current_theme_supports('widgets') ) {
	// Register nav menu metaboxes
	wp_nav_menu_meta_boxes_setup();
	$messages_div = '<div id="message" class="error"><p>' . __('The current theme does not natively support menus, but you can use the &#8220;Navigation Menu&#8221; widget to add any menus you create here to the theme&#8217;s sidebar.') . '</p></div>';

// The theme supports neither menus nor widgets.
} else {
	remove_meta_box( 'create-menu', 'nav-menus', 'side' );
	$messages_div = '<div id="message" class="error"><p>' . __('The current theme does not support menus.') . '</p></div>';
}

// Get the admin header
require_once( 'admin-header.php' );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e('Menus'); ?></h2>
	<?php echo $messages_div; ?>
	
	<?php if ( current_theme_supports('nav-menus') || current_theme_supports('widgets') ) : ?>
	<div id="menu-settings-column" class="metabox-holder">

		<form id="nav-menu-meta" action="<?php echo admin_url( 'nav-menus.php' ); ?>" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<input type="hidden" name="action" value="add-menu-item" />
			<?php wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); ?>
			<?php do_meta_boxes( 'nav-menus', 'side', null ); ?>
		</form>

	</div><!-- /#menu-settings-column -->
	
	<div id="menu-management-liquid">
		<div id="menu-management" class="">
			<h2>
				<?php 
				foreach( (array) $nav_menus as $_nav_menu ) :
				
					?>
					<a href="<?php 
						echo add_query_arg(
							array(
								'action' => 'edit',
								'menu' => $_nav_menu->term_id,
							),
							admin_url( 'nav-menus.php' )
						);
					?>" class="menu-tabs<?php 
						if ( $nav_menu_selected_id != $_nav_menu->term_id ) 
							echo ' menu-tab-inactive';
					?>"><?php echo esc_html( $_nav_menu->name ); ?></a>

					<?php
				endforeach;
				?>
				<a href="<?php 
					echo add_query_arg(
						array(
							'action' => 'edit',
							'menu' => 0,
						),
						admin_url( 'nav-menus.php' )
					);
				?>" class="menu-tabs menu-add-new<?php 
					if ( 0 != $nav_menu_selected_id ) 
						echo ' menu-tab-inactive';
				?>"><?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add menu' ) ); ?></a>
			</h2>
			<div class="menu-edit">
				<form id="update-nav-menu" action="<?php echo admin_url( 'nav-menus.php' ); ?>" method="post" enctype="multipart/form-data">
					<div id="submitpost" class="submitbox">
						<div id="minor-publishing">
							<div class="misc-pub-section misc-pub-section-last">
								<label class="howto" for="menu-name">
									<span><?php _e('Name'); ?></span>
									<input id="menu-name" name="menu-name" type="text" class="regular-text menu-item-textbox" value="<?php echo esc_attr( $nav_menu_selected_title  ); ?>" />
									<br class="clear" />
								</label>
							</div><!--END .misc-pub-section misc-pub-section-last-->
							<br class="clear" />
						</div><!--END #misc-publishing-actions-->
						<div id="major-publishing-actions">

							<?php if ( ! empty( $nav_menu_selected_id ) ) : ?>
							<div id="delete-action">
								<a class="submitdelete deletion menu-delete" href="<?php echo wp_nonce_url( admin_url('nav-menus.php?action=delete&amp;menu=' . $nav_menu_selected_id), 'delete-nav_menu-' . $nav_menu_selected_id ); ?>"><?php _e('Delete Menu'); ?></a>
							</div><!--END #delete-action-->
							<?php endif; ?>

							<div id="publishing-action">
								<input class="button-primary" name="save_menu" type="submit" value="<?php esc_attr_e('Save Menu'); ?>" />
							</div><!--END #publishing-action-->
							<br class="clear" />
						</div><!--END #major-publishing-actions-->
					</div><!--END #submitpost .submitbox-->
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
					<?php wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' ); ?>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="menu" id="menu" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
					<input type="hidden" id="hidden-metaboxes" value="<?php echo wp_initial_nav_menu_meta_boxes(); ?>" />
					<div id="post-body">
						<div id="post-body-content">
							<?php if ( is_nav_menu( $nav_menu_selected_id ) && ( current_theme_supports('nav-menus') || current_theme_supports('widgets') ) ) : ?>
								<ul class="menu" id="menu-to-edit">
								<?php 
								$edit_markup = wp_get_nav_menu_to_edit( $nav_menu_selected_id  ); 
								if ( ! is_wp_error( $edit_markup ) ) {
									echo $edit_markup;
								}
								?>
								</ul>
							<?php elseif ( empty($nav_menu_selected_id) ):
								echo '<p>' . __('To create your first custom menu, give it a name above, then choose items like pages, categories or custom links from the left column to add to this menu.') . '</p>';
								echo '<p>' . __('After you have added your items, drag and drop to put them in the order you want, and click each item to reveal additional configuration options.') . '</p>';
								echo '<p>' . __('When you are finished building your custom menu, make sure you click the Save Menu button above.') . '</p>';
								echo '<p>' . __('You can create multiple menus. You can also display custom menus using the new "Custom Menu" widget.') . '</p>';
								echo '<p>' . __('For more information on this feature, see the <a href="codex link">Custom Menus</a> article in the Codex.') . '</p>';
							endif; ?>
							<br class="clear" />
						</div><!-- /#post-body-content-->
					</div><!--- /#post-body -->
				</form><!--/#update-nav-menu-->
			</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	<?php endif; // if menus supported in current theme ?>
</div><!-- /.wrap-->


<?php include( 'admin-footer.php' ); ?>
