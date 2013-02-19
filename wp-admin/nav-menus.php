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
require_once( './admin.php' );

// Load all the nav menu interface functions
require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

if ( ! current_theme_supports( 'menus' ) && ! current_theme_supports( 'widgets' ) )
	wp_die( __( 'Your theme does not support navigation menus or widgets.' ) );

// Permissions Check
if ( ! current_user_can('edit_theme_options') )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

wp_enqueue_script( 'nav-menu' );

if ( wp_is_mobile() )
	wp_enqueue_script( 'jquery-touch-punch' );

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

				// set up the data we need in one pass through the array of menu items
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
							wp_update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );

						}

					// make menu item a child of its next sibling
					} else {
						$next_item_data['menu_order'] = $next_item_data['menu_order'] - 1;
						$menu_item_data['menu_order'] = $menu_item_data['menu_order'] + 1;

						$menu_item_data['menu_item_parent'] = $next_item_data['ID'];
						wp_update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );

						wp_update_post($menu_item_data);
						wp_update_post($next_item_data);
					}

				// the item is last but still has a parent, so bubble up
				} elseif (
					! empty( $menu_item_data['menu_item_parent'] ) &&
					in_array( $menu_item_data['menu_item_parent'], $orders_to_dbids )
				) {
					$menu_item_data['menu_item_parent'] = (int) get_post_meta( $menu_item_data['menu_item_parent'], '_menu_item_menu_item_parent', true);
					wp_update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
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

				// set up the data we need in one pass through the array of menu items
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
							wp_update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
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
						wp_update_post_meta( $menu_item_data['ID'], '_menu_item_menu_item_parent', (int) $menu_item_data['menu_item_parent'] );
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
			$deletion = _wp_delete_nav_menu( $nav_menu_selected_id );
		} else {
			// Reset the selected menu
			$nav_menu_selected_id = 0;
			unset( $_REQUEST['menu'] );
		}

		if ( ! isset( $deletion ) )
			break;

		if ( is_wp_error( $deletion ) )
			$messages[] = '<div id="message" class="error"><p>' . $deletion->get_error_message() . '</p></div>';
		else
			$messages[] = '<div id="message" class="updated"><p>' . __( 'The menu has been successfully deleted.' ) . '</p></div>';
		break;

	case 'delete_menus':
		check_admin_referer( 'nav_menus_bulk_actions' );
		foreach ( $_REQUEST['delete_menus'] as $menu_id_to_delete ) {
			if ( ! is_nav_menu( $menu_id_to_delete ) )
				continue;

			$deletion = _wp_delete_nav_menu( $menu_id_to_delete );
			if ( is_wp_error( $deletion ) ) {
				$messages[] = '<div id="message" class="error"><p>' . $deletion->get_error_message() . '</p></div>';
				$deletion_error = true;
			}
		}

		if ( empty( $deletion_error ) )
			$messages[] = '<div id="message" class="updated"><p>' . __( 'Selected menus have been successfully deleted.' ) . '</p></div>';
		break;

	case 'update':
		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Get existing menu locations assignments
		$locations = get_registered_nav_menus();
		$menu_locations = get_nav_menu_locations();
		if ( empty( $menu_locations ) || ! is_array( $menu_locations ) )
			$menu_locations = array();

		// Remove menu locations that have been unchecked
		foreach ( $locations as $location => $description ) {
			if ( ( empty( $_POST['menu-locations'] ) || empty( $_POST['menu-locations'][ $location ] ) ) && isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] == $nav_menu_selected_id )
				unset( $menu_locations[ $location ] );
		}

		// Merge new and existing menu locations if any new ones are set
		if ( isset( $_POST['menu-locations'] ) ) {
			$new_menu_locations = array_map( 'absint', $_POST['menu-locations'] );
			$menu_locations = array_merge( $menu_locations, $new_menu_locations );
		}

		// Set menu locations
		set_theme_mod( 'nav_menu_locations', $menu_locations );

		// Add Menu
		if ( 0 == $nav_menu_selected_id ) {
			$new_menu_title = trim( esc_html( $_POST['menu-name'] ) );

			if ( $new_menu_title ) {
				$_nav_menu_selected_id = wp_update_nav_menu_object( 0, array('menu-name' => $new_menu_title) );

				if ( is_wp_error( $_nav_menu_selected_id ) ) {
					$messages[] = '<div id="message" class="error"><p>' . $_nav_menu_selected_id->get_error_message() . '</p></div>';
				} else {
					$_menu_object = wp_get_nav_menu_object( $_nav_menu_selected_id );
					$nav_menu_selected_id = $_nav_menu_selected_id;
					$nav_menu_selected_title = $_menu_object->name;
					if ( isset( $_REQUEST['menu-item'] ) )
						wp_save_nav_menu_items( $nav_menu_selected_id, absint( $_REQUEST['menu-item'] ) );
					if ( isset( $_REQUEST['zero-menu-state'] ) ) {
						// If there are menu items, add them
						wp_nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title );
						// Auto-save nav_menu_locations
						$locations = get_theme_mod( 'nav_menu_locations' );
						foreach ( (array) $locations as $location => $menu_id ) {
								$locations[ $location ] = $nav_menu_selected_id;
								break; // There should only be 1
						}
						set_theme_mod( 'nav_menu_locations', $locations );
					}
					$messages[] = '<div id="message" class="updated"><p>' . sprintf( __( '<strong>%s</strong> has been created.' ), $nav_menu_selected_title ) . '</p></div>';
				}
			} else {
				$messages[] = '<div id="message" class="error"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
			}

		// Update existing menu
		} else {

			$_menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

			$menu_title = trim( esc_html( $_POST['menu-name'] ) );
			if ( ! $menu_title ) {
				$messages[] = '<div id="message" class="error"><p>' . __( 'Please enter a valid menu name.' ) . '</p></div>';
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
				$messages = array_merge( $messages, wp_nav_menu_update_menu_items( $nav_menu_selected_id, $nav_menu_selected_title ) );
			}
		}
		break;
}

// Get all nav menus
$nav_menus = wp_get_nav_menus( array('orderby' => 'name') );
$menu_count = count( $nav_menus );

// Are we on the add new screen?
$add_new_screen = ( isset( $_GET['menu'] ) && 0 == $_GET['menu'] ) ? true : false;

// If we have one theme location, and zero menus, we take them right into editing their first menu
$page_count = wp_count_posts( 'page' );
$one_theme_location_no_menus = ( 1 == count( get_registered_nav_menus() ) && ! $add_new_screen && empty( $nav_menus ) && ! empty( $page_count->publish ) ) ? true : false;

// Redirect to add screen if there are no menus and this users has either zero, or more than 1 theme locations
if ( 0 == $menu_count && ! $add_new_screen && ! $one_theme_location_no_menus )
	wp_redirect( admin_url( 'nav-menus.php?action=edit&menu=0' ) );

// Get recently edited nav menu
$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) )
	$recently_edited = $nav_menu_selected_id;

// Use $recently_edited if none are selected
if ( empty( $nav_menu_selected_id ) && ! isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) )
	$nav_menu_selected_id = $recently_edited;

// On deletion of menu, if another menu exists, show it
if ( ! $add_new_screen && 0 < $menu_count && isset( $_GET['action'] ) && 'delete' == $_GET['action'] )
	$nav_menu_selected_id = $nav_menus[0]->term_id;

// Set $nav_menu_selected_id to 0 if no menus
if ( $one_theme_location_no_menus ) {
	$nav_menu_selected_id = 0;
} elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
	// if we have no selection yet, and we have menus, set to the first one in the list
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

// Retrieve menu locations
if ( current_theme_supports( 'menus' ) ) {
	$locations = get_registered_nav_menus();
	$menu_locations = get_nav_menu_locations();
}

// Ensure the user will be able to scroll horizontally
// by adding a class for the max menu depth.
global $_wp_nav_menu_max_depth;
$_wp_nav_menu_max_depth = 0;

// Calling wp_get_nav_menu_to_edit generates $_wp_nav_menu_max_depth
if ( is_nav_menu( $nav_menu_selected_id ) ) {
	$menu_items = wp_get_nav_menu_items( $nav_menu_selected_id, array( 'post_status' => 'any' ) );
	$edit_markup = wp_get_nav_menu_to_edit( $nav_menu_selected_id );
}

function wp_nav_menu_max_depth($classes) {
	global $_wp_nav_menu_max_depth;
	return "$classes menu-max-depth-$_wp_nav_menu_max_depth";
}

add_filter('admin_body_class', 'wp_nav_menu_max_depth');

wp_nav_menu_setup();
wp_initial_nav_menu_meta_boxes();

if ( ! current_theme_supports( 'menus' ) && ! wp_get_nav_menus() )
	$messages[] = '<div id="message" class="updated"><p>' . __('The current theme does not natively support menus, but you can use the &#8220;Custom Menu&#8221; widget to add any menus you create here to the theme&#8217;s sidebar.') . '</p></div>';

get_current_screen()->add_help_tab( array(
'id'		=> 'overview',
'title'		=> __('Overview'),
'content'	=>
	'<p>' . __('This feature allows you to use a custom menu in place of your theme&#8217;s default menus.') . '</p>' .
	'<p>' . __('Custom menus may contain links to pages, categories, custom links or other content types (use the Screen Options tab to decide which ones to show on the screen). You can specify a different navigation label for a menu item as well as other attributes. You can create multiple menus. If your theme includes more than one menu location, you can choose which custom menu to associate with each. You can also use custom menus in conjunction with the Custom Menus widget.') . '</p>' .
	'<p>' . sprintf( __('If your theme does not support the custom menus feature (the default themes, %1$s and %2$s, do), you can learn about adding this support by following the Documentation link to the side.'), 'Twenty Twelve', 'Twenty Eleven' ) . '</p>'
) );
get_current_screen()->add_help_tab( array(
'id'		=> 'create-menus',
'title'		=> __('Create Menus'),
'content'	=>
	'<p>' . __('To create a new custom menu, click on the + tab, give the menu a name, and click Create Menu. Next, add menu items from the appropriate boxes. You&#8217;ll be able to edit the information for each menu item, and can drag and drop to change their order. You can also drag a menu item a little to the right to make it a submenu. Don&#8217;t forget to click Save Menu when you&#8217;re finished.') . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Appearance_Menus_Screen" target="_blank">Documentation on Menus</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

// Get the admin header
require_once( './admin-header.php' );
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Menus' ); ?> <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit', 'menu' => 0, ), admin_url( 'nav-menus.php' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New' ); ?></a></h2>
	<?php
	foreach( $messages as $message ) :
		echo $message . "\n";
	endforeach;
	?>
	<?php if ( 1 < $menu_count ) : ?>
	<form method="post" action="<?php echo admin_url( 'nav-menus.php' ); ?>">
		<input type="hidden" name="action" value="edit" />
		<div class="manage-menus">
			<label for="menu" class="selected-menu"><?php _e('Select menu to edit'); ?></label>
			<select name="menu" id="menu">
				<?php if ( $add_new_screen ) : ?>
					<option value="0" selected="selected"><?php _e( '-- Select --' ); ?></option>
				<?php endif; ?>
				<?php foreach( (array) $nav_menus as $_nav_menu ) : ?>
					<option value="<?php echo esc_attr( $_nav_menu->term_id ); ?>" <?php selected( $_nav_menu->term_id, $nav_menu_selected_id ); ?>>
						<?php
						echo esc_html( $_nav_menu->truncated_name ) ;

						if ( ! empty( $menu_locations ) && in_array( $_nav_menu->term_id, $menu_locations ) ) {
							$locations_assigned_to_this_menu = array();
							foreach ( array_keys( $menu_locations, $_nav_menu->term_id ) as $menu_location_key ) {
								 $locations_assigned_to_this_menu[] = $locations[ $menu_location_key ];
							}
							$assigned_locations = array_slice( $locations_assigned_to_this_menu, 0, absint( apply_filters( 'wp_nav_locations_listed_per_menu', 3 ) ) );

							// Adds ellipses following the number of locations defined in $assigned_locations
							printf( ' (%1$s%2$s)',
								implode( ', ', $assigned_locations ),
								count( $locations_assigned_to_this_menu ) > count( $assigned_locations ) ? ' &hellip;' : ''
							);
						}
						?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="submit-btn"><input type="submit" class="button-secondary" value="<?php _e( 'Select' ); ?>"></span>
		</div>
	</form>
	<?php endif; ?>
	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder<?php if ( isset( $_GET['menu'] ) && '0' == $_GET['menu'] ) { echo ' metabox-holder-disabled'; } ?>">

		<div class="clear"></div>

		<form id="nav-menu-meta" action="<?php echo admin_url( 'nav-menus.php' ); ?>" class="nav-menu-meta" method="post" enctype="multipart/form-data">
			<input type="hidden" name="menu" id="nav-menu-meta-object-id" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
			<input type="hidden" name="action" value="add-menu-item" />
			<?php wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); ?>
			<?php do_meta_boxes( 'nav-menus', 'side', null ); ?>
		</form>

	</div><!-- /#menu-settings-column -->
	<div id="menu-management-liquid">
		<div id="menu-management">
			<form id="update-nav-menu" action="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" method="post" enctype="multipart/form-data">
				<div class="menu-edit <?php if ( $add_new_screen ) echo 'blank-slate'; ?>">
					<?php
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
					wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );

					if ( $one_theme_location_no_menus ) { ?>
						<input type="hidden" name="zero-menu-state" value="true" />
					<?php } ?>
 					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="menu" id="menu" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" />
					<div id="nav-menu-header">
						<div class="major-publishing-actions">
							<label class="menu-name-label howto open-label" for="menu-name">
								<span><?php _e( 'Menu Name' ); ?></span>
								<input name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e( 'Enter menu name here' ); ?>" value="<?php if ( $one_theme_location_no_menus ) _e( 'Menu 1' ); else echo esc_attr( $nav_menu_selected_title ); ?>" />
							</label>
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( 'Create Menu' ) : __( 'Save Menu' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- END .nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content">
							<?php if ( ! $add_new_screen ) : ?>
							<?php $starter_copy = ( $one_theme_location_no_menus ) ? __( 'Edit your default menu by adding or removing items. Drag each item into the order you prefer. Click Create Menu to save your changes.' ) : __( 'Drag each item into the order you prefer. Click an item to reveal additional configuration options.' ); ?>
							<div class="drag-instructions post-body-plain" <?php if ( isset( $menu_items ) && 0 == count( $menu_items ) ) { ?>style="display: none;"<?php } ?>>
								<p><?php echo $starter_copy; ?></p>
							</div>
							<?php
							if ( isset( $edit_markup ) && ! is_wp_error( $edit_markup ) ) {
								echo $edit_markup;
							} else {
							?>
							<ul class="menu" id="menu-to-edit"></ul>
							<?php } ?>
							<?php endif; ?>
							<?php if ( $add_new_screen ) : ?>
								<p class="post-body-plain"><?php _e( 'Give your menu a name above, then click Create Menu.' ); ?></p>
							<?php endif; ?>
							<div class="menu-settings" <?php if ( $one_theme_location_no_menus ) { ?>style="display: none;"<?php } ?>>
								<?php
								if ( ! isset( $auto_add ) ) {
									$auto_add = get_option( 'nav_menu_options' );
									if ( ! isset( $auto_add['auto_add'] ) )
										$auto_add = false;
									elseif ( false !== array_search( $nav_menu_selected_id, $auto_add['auto_add'] ) )
										$auto_add = true;
									else
										$auto_add = false;
								} ?>

								<dl class="auto-add-pages">
									<dt class="howto"><?php _e( 'Auto add pages' ); ?></dt>
									<dd class="checkbox-input"><input type="checkbox"<?php checked( $auto_add ); ?> name="auto-add-pages" id="auto-add-pages" value="1" /> <label for="auto-add-pages"><?php printf( __('Automatically add new top-level pages to this menu' ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ); ?></label></dd>
								</dl>

								<?php if ( current_theme_supports( 'menus' ) ) : ?>

									<dl class="menu-theme-locations">
										<dt class="howto"><?php _e( 'Theme locations' ); ?></dt>
										<?php foreach ( $locations as $location => $description ) : ?>
										<dd class="checkbox-input">
											<input type="checkbox"<?php checked( isset( $menu_locations[ $location ] ) && $menu_locations[ $location ] == $nav_menu_selected_id ); ?> name="menu-locations[<?php echo esc_attr( $location ); ?>]" id="locations-<?php echo esc_attr( $location ); ?>" value="<?php echo esc_attr( $nav_menu_selected_id ); ?>" /> <label for="locations-<?php echo esc_attr( $location ); ?>"><?php echo $description; ?></label>
											<?php if ( ! empty( $menu_locations[ $location ] ) && $menu_locations[ $location ] != $nav_menu_selected_id ) : ?>
											<span class="theme-location-set"> <?php printf( __( "(Currently set to: %s)" ), wp_get_nav_menu_object( $menu_locations[ $location ] )->name ); ?> </span>
											<?php endif; ?>
										</dd>
										<?php endforeach; ?>
									</dl>

								<?php endif; ?>

							</div>
						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer">
						<div class="major-publishing-actions">
							<?php if ( 0 != $menu_count && ! $add_new_screen ) : ?>
							<span class="delete-action">
								<a class="submitdelete deletion menu-delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'menu' => $nav_menu_selected_id, admin_url() ) ), 'delete-nav_menu-' . $nav_menu_selected_id) ); ?>"><?php _e('Delete Menu'); ?></a>
							</span><!-- END .delete-action -->
							<?php endif; ?>
							<div class="publishing-action">
								<?php submit_button( empty( $nav_menu_selected_id ) ? __( 'Create Menu' ) : __( 'Save Menu' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#nav-menu-footer -->
				</div><!-- /.menu-edit -->
			</form><!-- /#update-nav-menu -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
</div><!-- /.wrap-->
<script type="text/javascript">var oneThemeLocationNoMenus = <?php if ( $one_theme_location_no_menus ) echo 'true'; else echo 'false'; ?>;</script>
<?php include( './admin-footer.php' ); ?>
