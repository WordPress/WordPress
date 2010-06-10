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

if ( ! current_theme_supports( 'menus' ) && ! current_theme_supports( 'widgets' ) )
	wp_die( __( 'Your theme does not support navigation menus or widgets.' ) );

// Permissions Check
if ( ! current_user_can('edit_theme_options') )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

// Nav Menu CSS
wp_admin_css( 'nav-menu' );

// jQuery
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-droppable' );
wp_enqueue_script( 'jquery-ui-sortable' );

// Nav Menu functions
wp_enqueue_script( 'nav-menu' );

// Metaboxes
wp_enqueue_script( 'common' );
wp_enqueue_script( 'wp-lists' );
wp_enqueue_script( 'postbox' );

// Container for any messages displayed to the user
$messages = array();

// Container that stores the name of the active menu
$nav_menu_selected_title = '';

// The menu id of the current menu being edited
$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;

// Allowed actions: add, update, delete
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'edit';

switch ( $action ) {
	case 'add-menu-item':
		check_admin_referer( 'add-menu_item', 'menu-settings-column-nonce' );
		if ( isset( $_REQUEST['nav-menu-locations'] ) )
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_REQUEST['menu-locations'] ) );
		elseif ( isset( $_REQUEST['menu-item'] ) )
			wp_save_nav_menu_items( $nav_menu_selected_id, $_REQUEST['menu-item'] );
		break;
	case 'move-down-menu-item' :
		// moving down a menu item is the same as moving up the next in order
		check_admin_referer( 'move-menu_item' );
		$menu_item_id = isset( $_REQUEST['menu-item'] ) ? (int) $_REQUEST['menu-item'] : 0;
		if ( is_nav_menu_item( $menu_item_id ) ) {
			$menus = isset( $_REQUEST['menu'] ) ? array( (int) $_REQUEST['menu'] ) : wp_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $menus ) && ! empty( $menus[0] ) ) {
				$menu_id = (int) $menus[0];
				$ordered_menu_items = wp_get_nav_menu_items( $menu_id );
				$menu_item_data = (array) wp_setup_nav_menu_item( get_post( $menu_item_id ) );

				// setup the data we need in one pass through the array of menu items
				$dbids_to_orders = array();
				$orders_to_dbids = array();
				foreach( (array) $ordered_menu_items as $ordered_menu_item_object ) {
					if ( isset( $ordered_menu_item_object->ID ) ) {
						if ( isset( $ordered_menu_item_object->menu_order ) ) {
							$dbids_to_orders[$ordered_menu_item_object->ID] = $ordered_menu_item_object->menu_order;
							$orders_to_dbids[$ordered_menu_item_object->menu_order] = $ordered_menu_item_object->ID;
						}
					}
				}

				// get next in order
				if (
					isset( $orders_to_dbids[$dbids_to_orders[$menu_item_id] + 1] )
				) {
					$next_item_id = $orders_to_dbids[$dbids_to_orders[$menu_item_id] + 1];
					$next_item_data = (array) wp_setup_nav_menu_item( get_post( $next_item_id ) );

					// if not siblings of same parent, bubble menu item up but keep order
					if (
						! empty( $menu_item_data['menu_item_parent'] ) &&
						(
							empty( $next_item_data['menu_item_parent'] ) ||
							$next_item_data['menu_item_parent'] != $menu_item_data['menu_item_parent']
						)
					) {

						$parent_db_id = in_array( $menu_item_data['menu_item_parent'], $orders_to_dbids ) ? (int) $menu_item_data['menu_item_parent'] : 0;

						$parent_object = wp_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_wp_error( $parent_object ) ) {
							$parent_data = (array) $parent_object;
							$menu_item_data['menu_item_parent'] = $parent_data['menu_item_parent'];
							update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );

						}

					// make menu item a child of its next sibling
					} else {
						$next_item_data['menu_order'] = $next_item_data['menu_order'] - 1;
						$menu_item_data['menu_order'] = $menu_item_data['menu_order'] + 1;

						$menu_item_data['menu_item_parent'] = $next_item_data['ID'];
						update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );

						wp_update_post($menu_item_data);
						wp_update_post($next_item_data);
					}


				// the item is last but still has a parent, so bubble up
				} elseif (
					! empty( $menu_item_data['menu_item_parent'] ) &&
					in_array( $menu_item_data['menu_item_parent'], $orders_to_dbids )
				) {
					$menu_item_data['menu_item_parent'] = (int) get_post_meta( $menu_item_data['menu_item_parent'], '_menu_item_menu_item_parent', true);
					update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
				}
			}
		}

		break;
	case 'move-up-menu-item' :
		check_admin_referer( 'move-menu_item' );
		$menu_item_id = isset( $_REQUEST['menu-item'] ) ? (int) $_REQUEST['menu-item'] : 0;
		if ( is_nav_menu_item( $menu_item_id ) ) {
			$menus = isset( $_REQUEST['menu'] ) ? array( (int) $_REQUEST['menu'] ) : wp_get_object_terms( $menu_item_id, 'nav_menu', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $menus ) && ! empty( $menus[0] ) ) {
				$menu_id = (int) $menus[0];
				$ordered_menu_items = wp_get_nav_menu_items( $menu_id );
				$menu_item_data = (array) wp_setup_nav_menu_item( get_post( $menu_item_id ) );

				// setup the data we need in one pass through the array of menu items
				$dbids_to_orders = array();
				$orders_to_dbids = array();
				foreach( (array) $ordered_menu_items as $ordered_menu_item_object ) {
					if ( isset( $ordered_menu_item_object->ID ) ) {
						if ( isset( $ordered_menu_item_object->menu_order ) ) {
							$dbids_to_orders[$ordered_menu_item_object->ID] = $ordered_menu_item_object->menu_order;
							$orders_to_dbids[$ordered_menu_item_object->menu_order] = $ordered_menu_item_object->ID;
						}
					}
				}


				// if this menu item is not first
				if ( ! empty( $dbids_to_orders[$menu_item_id] ) && ! empty( $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] ) ) {

					// if this menu item is a child of the previous
					if (
						! empty( $menu_item_data['menu_item_parent'] ) &&
						in_array( $menu_item_data['menu_item_parent'], array_keys( $dbids_to_orders ) ) &&
						isset( $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] ) &&
						( $menu_item_data['menu_item_parent'] == $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] )
					) {
						$parent_db_id = in_array( $menu_item_data['menu_item_parent'], $orders_to_dbids ) ? (int) $menu_item_data['menu_item_parent'] : 0;
						$parent_object = wp_setup_nav_menu_item( get_post( $parent_db_id ) );

						if ( ! is_wp_error( $parent_object ) ) {
							$parent_data = (array) $parent_object;

							// if there is something before the parent and parent a child of it, make menu item a child also of it
							if (
								! empty( $dbids_to_orders[$parent_db_id] ) &&
								! empty( $orders_to_dbids[$dbids_to_orders[$parent_db_id] - 1] ) &&
								! empty( $parent_data['menu_item_parent'] )
							) {
								$menu_item_data['menu_item_parent'] = $parent_data['menu_item_parent'];

							// else if there is something before parent and parent not a child of it, make menu item a child of that something's parent
							} elseif (
								! empty( $dbids_to_orders[$parent_db_id] ) &&
								! empty( $orders_to_dbids[$dbids_to_orders[$parent_db_id] - 1] )
							) {
								$_possible_parent_id = (int) get_post_meta( $orders_to_dbids[$dbids_to_orders[$parent_db_id] - 1], '_menu_item_menu_item_parent', true);
								if ( in_array( $_possible_parent_id, array_keys( $dbids_to_orders ) ) )
									$menu_item_data['menu_item_parent'] = $_possible_parent_id;
								else
									$menu_item_data['menu_item_parent'] = 0;

							// else there isn't something before the parent
							} else {
								$menu_item_data['menu_item_parent'] = 0;
							}

							// set former parent's [menu_order] to that of menu-item's
							$parent_data['menu_order'] = $parent_data['menu_order'] + 1;

							// set menu-item's [menu_order] to that of former parent
							$menu_item_data['menu_order'] = $menu_item_data['menu_order'] - 1;

							// save changes
							update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
							wp_update_post($menu_item_data);
							wp_update_post($parent_data);
						}

					// else this menu item is not a child of the previous
					} elseif (
						empty( $menu_item_data['menu_order'] ) ||
						empty( $menu_item_data['menu_item_parent'] ) ||
						! in_array( $menu_item_data['menu_item_parent'], array_keys( $dbids_to_orders ) ) ||
						empty( $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] ) ||
						$orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1] != $menu_item_data['menu_item_parent']
					) {
						// just make it a child of the previous; keep the order
						$menu_item_data['menu_item_parent'] = (int) $orders_to_dbids[$dbids_to_orders[$menu_item_id] - 1];
						update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
						wp_update_post($menu_item_data);
					}
				}
			}
		}
		break;

	case 'delete-menu-item':
		$menu_item_id = (int) $_REQUEST['menu-item'];

		check_admin_referer( 'delete-menu_item_' . $menu_item_id );


		if ( is_nav_menu_item( $menu_item_id ) && wp_delete_post( $menu_item_id, true ) )
			$messages[] = '<div id="message" class="updated"><p>' . __('The menu item has been successfully deleted.') . '</p></div>';
		break;
	case 'delete':
		check_admin_referer( 'delete-nav_menu-' . $nav_menu_selected_id );

		if ( is_nav_menu( $nav_menu_selected_id ) ) {
			$deleted_nav_menu = wp_get_nav_menu_object( $nav_menu_selected_id );
			$delete_nav_menu = wp_delete_nav_menu( $nav_menu_selected_id );

			if ( is_wp_error($delete_nav_menu) ) {
				$messages[] = '<div id="message" class="error"><p>' . $delete_nav_menu->get_error_message() . '</p></div>';
			} else {
				// Remove this menu from any locations.
				$locations = get_theme_mod( 'nav_menu_locations' );
				foreach ( (array) $locations as $location => $menu_id ) {
					if ( $menu_id == $nav_menu_selected_id )
						$locations[ $location ] = 0;
				}
				set_theme_mod( 'nav_menu_locations', $locations );
				$messages[] = '<div id="message" class="updated"><p>' . __('The menu has been successfully deleted.') . '</p></div>';
				// Select the next available menu
				$nav_menu_selected_id = 0;
				$_nav_menus = wp_get_nav_menus( array('orderby' => 'name') );
				foreach( $_nav_menus as $index => $_nav_menu ) {
					if ( strcmp( $_nav_menu->name, $deleted_nav_menu->name ) >= 0
					 || $index == count( $_nav_menus ) - 1 ) {
						$nav_menu_selected_id = $_nav_menu->term_id;
						break;
					}
				}
			}
			unset( $delete_nav_menu, $deleted_nav_menu, $_nav_menus );
		} else {
			// Reset the selected menu
			$nav_menu_selected_id = 0;
			unset( $_REQUEST['menu'] );
		}
		break;

	case 'update':
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Update menu theme locations
		if ( isset( $_POST['menu-locations'] ) )
			set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_POST['menu-locations'] ) );

		// Add Menu
		if ( 0 == $nav_menu_selected_id ) {
			$new_menu_title = trim( esc_html( $_POST['menu-name'] ) );

			if ( $new_menu_title ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$messages[] = '<div id="message" class="error"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					if ( ( $_menu_locations = get_registered_nav_menus() ) && 1 == count( wp_get_nav_menus() ) )
						set_theme_mod( 'nav_menu_locations', array( key( $_menu_locations ) => $_nav_menu_selected_id ) );
					unset( $_menu_locations );
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;
					$messages[] = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> menu has been successfully created.'), $nav_menu_selected_title ) . '</p></div>';
				}
			} else {
				$messages[] = '<div id="message" class="error"><p>' . __('Please enter a valid menu name.') . '</p></div>';
			}

		// update existing menu
		} else {

			$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

			$menu_title = trim( esc_html( $_POST['menu-name'] ) );
			if ( ! $menu_title ) {
				$messages[] = '<div id="message" class="error"><p>' . __('Please enter a valid menu name.') . '</p></div>';
				$menu_title = $_menu_object->name;
			}

			if ( ! is_wp_error( $_menu_object ) ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( $nav_menu_selected_id, array( 'menu-name' => $menu_title ) );
				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$_menu_object = $_nav_menu_selected_id;
					$messages[] = '<div id="message" class="error"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_title = $_menu_object->name;
				}
			}

			// Update menu items

			if ( ! is_wp_error( $_menu_object ) ) {
				$unsorted_menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array('orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID', 'post_status' => 'draft,publish') );
				$menu_items = array();
				// Index menu items by db ID
				foreach( $unsorted_menu_items as $_item )
					$menu_items[$_item->db_id] = $_item;

				$post_fields = array( 'menu-item-db-id', 'menu-item-object-id', 'menu-item-object', 'menu-item-parent-id', 'menu-item-position', 'menu-item-type', 'menu-item-title', 'menu-item-url', 'menu-item-description', 'menu-item-attr-title', 'menu-item-target', 'menu-item-classes', 'menu-item-xfn' );
				wp_defer_term_counting(true);
				// Loop through all the menu items' POST variables
				if ( ! empty( $_POST['menu-item-db-id'] ) ) {
					foreach( (array) $_POST['menu-item-db-id'] as $_key => $k ) {

						// Menu item title can't be blank
						if ( empty( $_POST['menu-item-title'][$_key] ) )
							continue;

						$args = array();
						foreach ( $post_fields as $field )
							$args[$field] = isset( $_POST[$field][$_key] ) ? $_POST[$field][$_key] : '';

						$menu_item_db_id = wp_update_nav_menu_item( $nav_menu_selected_id, ( $_POST['menu-item-db-id'][$_key] != $_key ? 0 : $_key ), $args );

						if ( is_wp_error( $menu_item_db_id ) )
							$messages[] = '<div id="message" class="error"><p>' . $menu_item_db_id->get_error_message() . '</p></div>';
						elseif ( isset( $menu_items[$menu_item_db_id] ) )
							unset( $menu_items[$menu_item_db_id] );
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

				// Store 'auto-add' pages.
				$auto_add = ! empty( $_POST['auto-add-pages'] );
				$nav_menu_option = (array) get_option( 'nav_menu_options' );
				if ( ! isset( $nav_menu_option['auto_add'] ) )
					$nav_menu_option['auto_add'] = array();
				if ( $auto_add ) {
					if ( ! in_array( $nav_menu_selected_id, $nav_menu_option['auto_add'] ) )
						$nav_menu_option['auto_add'][] = $nav_menu_selected_id;
				} else {
					if ( false !== ( $key = array_search( $nav_menu_selected_id, $nav_menu_option['auto_add'] ) ) )
						unset( $nav_menu_option['auto_add'][$key] );
				}
				// Remove nonexistent/deleted menus
				$nav_menu_option['auto_add'] = array_intersect( $nav_menu_option['auto_add'], wp_get_nav_menus( array( 'fields' => 'ids' ) ) );
				update_option( 'nav_menu_options', $nav_menu_option );

				wp_defer_term_counting(false);

				do_action( 'wp_update_nav_menu', $nav_menu_selected_id );

				$messages[] = '<div id="message" class="updated"><p>' . sprintf( __('The <strong>%s</strong> menu has been updated.'), $nav_menu_selected_title ) . '</p></div>';
				unset( $menu_items, $unsorted_menu_items );
			}
		}
		break;
}

// Get all nav menus
$nav_menus = wp_get_nav_menus( array('orderby' => 'name') );

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

// Generate truncated menu names
foreach( (array) $nav_menus as $key => $_nav_menu ) {
	$_nav_menu->truncated_name = trim( wp_html_excerpt( $_nav_menu->name, 40 ) );
	if ( $_nav_menu->truncated_name != $_nav_menu->name )
		$_nav_menu->truncated_name .= '&hellip;';

	$nav_menus[$key]->truncated_name = $_nav_menu->truncated_name;
}

wp_nav_menu_setup();
wp_initial_nav_menu_meta_boxes();

if ( ! current_theme_supports( 'menus' ) && ! wp_get_nav_menus() )
	echo '<div id="message" class="updated"><p>' . __('The current theme does not natively support menus, but you can use the &#8220;Custom Menu&#8221; widget to add any menus you create here to the theme&#8217;s sidebar.') . '</p></div>';

$help =  '<p>' . __('This feature is new in version 3.0; to use a custom menu in place of your theme&#8217;s default menus, support for this feature must be registered in the theme&#8217;s functions.php file. If your theme does not support the custom menus feature yet (the new default theme, Twenty Ten, does), you can learn about adding support yourself by following the below link.') . '</p>';
$help .= '<p>' . __('You can create custom menus for your site. These menus may contain links to pages, categories, custom links or other content types (use the Screen Options tab to decide which ones to show on the screen). You can specify a different navigation label for a menu item as well as other attributes. You can create multiple menus. If your theme includes more than one menu, you can choose which custom menu to associate with each. You can also use custom menus in conjunction with the Custom Menus widget.') . '</p>';
$help .= '<p>' . __('To create a new custom menu, click on the + tab, give the menu a name, and click Create Menu. Next, add menu items from the appropriate boxes. You&#8217;ll be able to edit the information for each menu item, and can drag and drop to put them in order. You can also drag a menu item a little to the right to make it a submenu, to create menus with hierarchy. You&#8217;ll see when the position of the drop target shifts over to create the nested placement. Don&#8217;t forget to click Save when you&#8217;re finished.') . '</p>';
$help .= '<p><strong>' . __('For more information:') . '</strong></p>';
$help .= '<p>' . __('<a href="http://codex.wordpress.org/Appearance_Menus_SubPanel" target="_blank">Menus Documentation</a>') . '</p>';
$help .= '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>';

add_contextual_help($current_screen, $help);

// Get the admin header
require_once( 'admin-header.php' );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e('Menus'); ?></h2>
	<?php
	foreach( $messages as $message ) :
		echo $message . "\n";
	endforeach;
	?>
	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder<?php if ( !$nav_menu_selected_id ) { echo ' metabox-holder-disabled'; } ?>">

		<form id="nav-menu-meta" action="<?php echo admin_url( 'nav-menus.php' ); ?>" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<input type="hidden" name="action" value="add-menu-item" />
			<?php wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); ?>
			<?php do_meta_boxes( 'nav-menus', 'side', null ); ?>
		</form>

	</div><!-- /#menu-settings-column -->
	<div id="menu-management-liquid">
		<div id="menu-management">
			<div id="select-nav-menu-container" class="hide-if-js">
				<form id="select-nav-menu" action="">
					<strong><label for="select-nav-menu"><?php esc_html_e( 'Select Menu:' ); ?></label></strong>
					<select class="select-nav-menu" name="menu">
						<?php foreach( (array) $nav_menus as $_nav_menu ) : ?>
							<option value="<?php echo esc_attr($_nav_menu->term_id) ?>" <?php selected($nav_menu_selected_id, $_nav_menu->term_id); ?>>
								<?php echo esc_html( $_nav_menu->truncated_name ); ?>
							</option>
						<?php endforeach; ?>
						<option value="0"><?php esc_html_e('Add New Menu'); ?></option>
					</select>
					<input type="hidden" name="action" value="edit" />
					<input class="button-secondary" name="select_menu" type="submit" value="<?php esc_attr_e('Select'); ?>" />
				</form>
			</div>
			<div class="nav-tabs-wrapper">
			<div class="nav-tabs">
				<?php
				foreach( (array) $nav_menus as $_nav_menu ) :
					if ( $nav_menu_selected_id == $_nav_menu->term_id ) : ?><span class="nav-tab nav-tab-active">
							<?php echo esc_html( $_nav_menu->truncated_name ); ?>
						</span><?php else : ?><a href="<?php
							echo esc_url(add_query_arg(
								array(
									'action' => 'edit',
									'menu' => $_nav_menu->term_id,
								),
								admin_url( 'nav-menus.php' )
							));
						?>" class="nav-tab hide-if-no-js">
							<?php echo esc_html( $_nav_menu->truncated_name ); ?>
						</a><?php endif;
				endforeach;
				if ( 0 == $nav_menu_selected_id ) : ?><span class="nav-tab menu-add-new nav-tab-active">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add menu' ) ); ?>
				</span><?php else : ?><a href="<?php
					echo esc_url(add_query_arg(
						array(
							'action' => 'edit',
							'menu' => 0,
						),
						admin_url( 'nav-menus.php' )
					));
				?>" class="nav-tab menu-add-new">
					<?php printf( '<abbr title="%s">+</abbr>', esc_html__( 'Add menu' ) ); ?>
				</a><?php endif; ?>
			</div>
			</div>
			<div class="menu-edit">
				<form id="update-nav-menu" action="<?php echo admin_url( 'nav-menus.php' ); ?>" method="post" enctype="multipart/form-data">
					<div id="nav-menu-header">
						<div id="submitpost" class="submitbox">
							<div class="major-publishing-actions">
								<label class="menu-name-label howto open-label" for="menu-name">
									<span><?php _e('Menu Name'); ?></span>
									<input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Enter menu name here'); ?>" value="<?php echo esc_attr( $nav_menu_selected_title  ); ?>" />
								</label>
								<?php if ( !empty( $nav_menu_selected_id ) ) :
									if ( ! isset( $auto_add ) ) {
										$auto_add = get_option( 'nav_menu_options' );
										if ( ! isset( $auto_add['auto_add'] ) )
											$auto_add = false;
										elseif ( false !== array_search( $nav_menu_selected_id, $auto_add['auto_add'] ) )
											$auto_add = true;
										else
											$auto_add = false;
									}
								?>
								<div class="auto-add-pages">
									<label class="howto"><input type="checkbox"<?php checked( $auto_add ); ?> name="auto-add-pages" value="1" /> <?php printf( __('Automatically add new top-level pages' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></label>
								</div>
								<?php endif; ?>
								<br class="clear" />
								<div class="publishing-action">
									<input class="button-primary menu-save" name="save_menu" type="submit" value="<?php empty($nav_menu_selected_id) ? esc_attr_e('Create Menu') : esc_attr_e('Save Menu'); ?>" />
								</div><!-- END .publishing-action -->

								<?php if ( ! empty( $nav_menu_selected_id ) ) : ?>
								<div class="delete-action">
									<a class="submitdelete deletion menu-delete" href="<?php echo esc_url( wp_nonce_url( admin_url('nav-menus.php?action=delete&amp;menu=' . $nav_menu_selected_id), 'delete-nav_menu-' . $nav_menu_selected_id ) ); ?>"><?php _e('Delete Menu'); ?></a>
								</div><!-- END .delete-action -->
								<?php endif; ?>
							</div><!-- END .major-publishing-actions -->
						</div><!-- END #submitpost .submitbox -->
						<?php
						wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
						wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
						wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );
						?>
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="menu" id="menu" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
					</div><!-- END #nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content">
							<?php
							if ( is_nav_menu( $nav_menu_selected_id ) ) :
								$edit_markup = wp_get_nav_menu_to_edit( $nav_menu_selected_id  );
								if ( ! is_wp_error( $edit_markup ) ) :
									echo $edit_markup;
								endif;
							elseif ( empty( $nav_menu_selected_id ) ) :
								echo '<div class="post-body-plain">';
								echo '<p>' . __('To create a custom menu, give it a name above and click Create Menu. Then choose items like pages, categories or custom links from the left column to add to this menu.') . '</p>';
								echo '<p>' . __('After you have added your items, drag and drop to put them in the order you want. You can also click each item to reveal additional configuration options.') . '</p>';
								echo '<p>' . __('When you have finished building your custom menu, make sure you click the Save Menu button.') . '</p>';
								echo '</div>';
							endif;
							?>
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
				</form><!-- /#update-nav-menu -->
			</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
</div><!-- /.wrap-->


<?php include( 'admin-footer.php' ); ?>
