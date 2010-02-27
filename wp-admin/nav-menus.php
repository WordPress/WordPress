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

require_once('admin.php');

/*
TODO
	Add caps: edit_menus, delete_menus
*/
if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_admin_css( 'nav-menu' );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-droppable' );
wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_script( 'jquery-ui-dialog' );
wp_enqueue_script( 'nav-menu-dynamic-functions' );
wp_enqueue_script( 'nav-menu-default-items' );
wp_enqueue_script( 'jquery-autocomplete' );
wp_enqueue_script( 'nav-menu-php-functions' );
add_thickbox();

require_once( 'admin-header.php' );
require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );

function wp_reset_nav_menu() {
	wp_nav_menu_setup( true );
	return true;
}

$messages_div = '';
$menu_id_in_edit = 0;
$updated = false;
$advanced_option_descriptions = 'no';

// Get all menu link items
$available_links = new WP_Query( array( 'post_status' => 'any', 'post_type' => 'nav_menu_item', 'meta_key' => 'menu_type', 'meta_value' => 'custom' ) );

// Check which menu is selected and if menu is in edit already
if ( isset( $_GET['edit-menu'] ) ) {
	$menu_selected_id = (int) $_GET['edit-menu'];
	$updated = true;
} elseif ( isset( $_POST[ 'menu-id-in-edit' ] ) ) {
	$menu_selected_id = (int) $_POST[ 'menu-id-in-edit' ];
} else {
	$menu_selected_id = 0;
}

// Delete a menu
if ( isset($_GET['delete-menu']) && $_GET['delete-menu'] > 0 ) {
	// if ( ! current_user_can('delete_menus') )
	// 	wp_die( __( 'Cheatin&#8217; uh?' ));
	
	$menu_id = (int) $_GET['delete-menu'];
	check_admin_referer( 'delete_menu-' . $menu_id );
	
	wp_delete_nav_menu( $menu_id );
	$messages_div = '<div id="message" class="updated fade below-h2"><p>' . __('Menu successfully deleted.') . '</p></div>';
	$menu_selected_id = 0;
	$updated = true;
}

// Default Menu to show
$menus = wp_get_nav_menus();

if ( empty($menus) && empty($_POST) ) {
	wp_create_default_nav_menu();
	$menus = wp_get_nav_menus();
}

if ( ! $menu_selected_id && ! empty($menus) )
	$menu_selected_id = $menus[0]->term_id;

// Get the name of the current Menu 
$menu_title = '';
$valid_menu = false;
if ( $menu_selected_id > 0 ) {
	foreach ( $menus as $menu ) {
		if ( $menu->term_id == $menu_selected_id ) {
			$menu_title = $menu->name;
			$valid_menu = true;
			break;
		}
	}
}

if ( isset( $_POST['li-count'] ) )
	$post_counter = $_POST['li-count'];
else
	$post_counter = 0;

// Create a new menu. Menus are stored as terms in the 'menu' taxonomy.
if ( isset( $_POST['create-menu'] ) && ! $updated ) {
	$insert_menu_name = $_POST['create-menu-name'];

	if ( $insert_menu_name ) {
		$menu = wp_create_nav_menu( $insert_menu_name );
		if ( is_wp_error( $menu ) ) {
			$messages_div = '<div id="message" class="error fade below-h2"><p>' . $menu->get_error_message() . '</p></div>';
		} else {
			$menus[$menu->term_id] = $menu;
			$menu_selected_id = $menu->term_id;
			$menu_id_in_edit = $menu_selected_id;
			$menu_title = $menu->name;
			$messages_div = '<div id="message" class="updated fade below-h2"><p>' . sprintf( __('&#8220;%s&#8221; menu has been created.'), esc_html( $menu->name ) ) . '</p></div>';
			$post_counter = 0;
		}
	} else {
		$messages_div = '<div id="message" class="error fade below-h2"><p>' . __('Please enter a valid menu name.') . '</p></div>';
	}
	$updated = true;
}

if ( $post_counter > 0 && $menu_selected_id > 0 && ! $updated ) {
	$menu_items = wp_get_nav_menu_items( $menu_selected_id, array('orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID') );
	$parent_menu_ids = array();
	
	// Loop through all POST variables
	for ( $k = 1; $k <= $post_counter; $k++ ) {
		$db_id = isset( $_POST['dbid'.$k] )? $_POST['dbid'.$k] : 0;
		$object_id = isset( $_POST['postmenu'.$k] )? $_POST['postmenu'.$k] : 0;
		$parent_id = isset( $_POST['parent'.$k] )? $_POST['parent'.$k] : 0;
		$custom_title = isset( $_POST['item-title'.$k] )?  $_POST['item-title'.$k] : '';
		$custom_linkurl = ( isset( $_POST['item-url'.$k] ) && 'custom' == $_POST['linktype'.$k] ) ? $_POST['item-url'.$k] : '';
		$custom_description = isset( $_POST['item-description'.$k] )? $_POST['item-description'.$k] : '';
		// doesn't seem to be used by UI
		$icon = isset( $_POST['icon'.$k] )? $_POST['icon'.$k] : 0;
		$position = isset( $_POST['position'.$k] )? $_POST['position'.$k] : 0;
		$linktype = isset( $_POST['linktype'.$k] )? $_POST['linktype'.$k] : 'custom';
		$custom_anchor_title  = isset( $_POST['item-attr-title'.$k] )? $_POST['item-attr-title'.$k] : $custom_title;
		$new_window = isset( $_POST['item-target'.$k] )? $_POST['item-target'.$k] : 0;

		$post = array( 'post_status' => 'publish', 'post_type' => 'nav_menu_item', 'post_author' => $user_ID,
			'ping_status' => 0, 'post_parent' => 0, 'menu_order' => $position,
			'post_excerpt' => $custom_anchor_title, 'tax_input' => array( 'nav_menu' => $menu_title ),
			'post_content' => $custom_description, 'post_title' => $custom_title );

		if ( $parent_id > 0 && isset( $parent_menu_ids[$parent_id] ) )
			$post['post_parent'] = $parent_menu_ids[$parent_id];
		
		// New menu item
		if ( $db_id == 0 ) {
			$db_id = wp_insert_post( $post );
		} elseif ( isset( $menu_items[$db_id] ) ) {
			$post['ID'] = $db_id;
			wp_update_post( $post );
			unset( $menu_items[$db_id] );
		}
		$parent_menu_ids[ $k ] = $db_id;

		update_post_meta( $db_id, 'menu_type', $linktype );
		update_post_meta( $db_id, 'object_id', $object_id );
		if ( $new_window )
			update_post_meta( $db_id, 'menu_new_window', 1 );
		else
			update_post_meta( $db_id, 'menu_new_window', 0 );
		if ( $custom_linkurl )
			update_post_meta( $db_id, 'menu_link', esc_url_raw( $custom_linkurl ) );

	}
	if ( !empty( $menu_items ) ) {
		foreach ( array_keys( $menu_items ) as $menu_id ) {
			wp_delete_post( $menu_id );
		}
	}
	$messages_div = '<div id="message" class="updated fade below-h2"><p>' . __('The menu has been updated.') . '</p></div>';
}

?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php esc_html_e('Menus'); ?></h2>
	<?php echo $messages_div; ?>
	<div class="hide-if-js error"><p><?php _e('You do not have JavaScript enabled in your browser. Please enable it to access the Menus functionality.'); ?></p></div>
	
	<form onsubmit="wp_update_post_data();" action="<?php echo admin_url( 'nav-menus.php' ); ?>" method="post" enctype="multipart/form-data">
		<?php if ( !empty($menus) && count($menus) > 1 ) : ?>
		<ul class="subsubsub">
			<?php
				foreach ( $menus as $menu ) {
					$sep = end( $menus ) == $menu ? '' : ' | ';
					if ( ( $menu_id_in_edit == $menu->term_id ) || ( $menu_selected_id == $menu->term_id ) ) { ?>
						<li><a href='nav-menus.php?edit-menu=<?php echo esc_attr($menu->term_id); ?>' class="current"><?php echo esc_html( $menu->name ); ?></a><?php echo $sep; ?></li>
			<?php	} else { ?>
						<li><a href='nav-menus.php?edit-menu=<?php echo esc_attr($menu->term_id); ?>'><?php echo esc_html( $menu->name ); ?></a><?php echo $sep; ?></li>
			<?php	}
				}
			?>
		</ul>
		<?php endif; ?>
		
		<div id="menu-management" class="metabox-holder has-right-sidebar">
			<div id="post-body">
				<div id="post-body-content">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<?php if ( $valid_menu and ! empty( $menus ) ) : ?>
						<div id="menu-container" class="postbox">	
							<h3 class="hndle"><?php echo esc_html( $menu_title ); ?></h3>
							<div class="inside">
								<input type="hidden" name="li-count" id="li-count" value="0" />
								<input type="hidden" name="menu-id-in-edit" id="menu-id-in-edit" value="<?php echo esc_attr( $menu_selected_id ); ?>" />

								<ul id="menu">
								<?php
								if ( $menu_selected_id > 0 ) {
									wp_print_nav_menu( array( 'type' => 'backend', 'name' => $menu_title, 'id' => $menu_selected_id ) );
								}
								?>
								</ul><!-- /#menu-->
								
								<div id="queue" class="hide">
								</div><!--/#queue-->
							</div><!-- /.inside -->
						<!-- /#nav-menu-canvas .postbox-->
						</div>
						<p>
							<script type="text/javascript">
								wp_update_post_data();
							</script>
							<a class="submitdelete deletion" href="<?php echo wp_nonce_url( admin_url('nav-menus.php?delete-menu=' . $menu_selected_id), 'delete_menu-' . $menu_selected_id ); ?>"><?php _e('Delete Menu'); ?></a>
							<input class="button-primary save" name="save_menu" type="submit" value="<?php esc_attr_e('Save All Changes'); ?>" />
							<br class="clear" />
						</p>
					<?php endif; ?>
					</div><!-- /#normal-sortables-->
				</div><!-- /#post-body-content-->
			</div><!--- /#post-body -->
			<div id="menu-settings-column" class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					
					<div id="create-menu" class="postbox">
						<h3 class="hndle"><?php esc_html_e('Create Menu'); ?></h3>
						<div class="inside">
							<p>
								<input type="text" name="create-menu-name" id="create-menu-name" class="regular-text" value=""  />
								<input type="submit" name="create-menu" id="create-menu" class="button" value="<?php esc_attr_e('Create Menu'); ?>" />
							</p>
						</div><!-- /.inside-->
					</div><!--END #create-menu-->
					
					<div id="add-custom-link" class="postbox">
						<h3 class="hndle"><?php esc_html_e('Add a Custom Link'); ?></h3>
						<div class="inside">							
							<p id="menu-item-url-wrap">
								<label class="howto" for="menu-item-url">
									<span><?php _e('URL'); ?></span>
									<input id="menu-item-url" name="menu-item-url" type="text" class="code" value="http://" />
								</label>
							</p>
							<br class="clear" />
							<p id="menu-item-name-wrap">
								<label class="howto" for="custom-menu-item-name">
									<span><?php _e('Text'); ?></span>
									<input id="menu-item-name" type="text" class="regular-text" value="<?php echo esc_attr( __('Menu Item') ); ?>" />
								</label>
							</p>
							
					<?php if ( $available_links->posts ) : ?>
							<p class="button-controls">
								<a class="show-all button"><?php _e('View All'); ?></a>
								<a class="hide-all button"><?php _e('Hide All'); ?></a>
							</p>
							<div id="available-links" class="list-wrap">
								<div class="list-container">
									<ul class="list">
									<?php
									foreach ( $available_links->posts as $link ) :
									$url = get_post_meta( $link->ID, 'menu_link' );
									?>
										<li>
											<dl>
												<dt>
													<label class="item-title"><input type="checkbox" id="link-<?php echo esc_attr($link->ID); ?>" name="<?php echo esc_attr($link->post_title); ?>" value="<?php echo esc_attr($url[0]); ?>" /><?php echo esc_html($link->post_title); ?></label>
												</dt>
											</dl>
										</li>
									<?php
									endforeach;
									?>
									</ul>
								</div><!-- /.list-container-->
							</div><!-- /#available-links-->
					<?php endif; ?>
							<p class="add-to-menu">
								<a class="button"><?php _e('Add to Menu'); ?></a>
							</p>
							<br class="clear" />
						</div><!-- /.inside-->
					</div><!-- /#add-custom-link-->
					
					<div id="add-pages" class="postbox">
						<h3 class="hndle"><?php esc_html_e('Add an Existing Page'); ?></h3>
						<div class="inside">
							<?php
								$pages_args = array(
									'child_of' => 0, 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'hierarchical' => 1,
									'exclude' => '', 'include' => '', 'meta_key' => '', 'meta_value' => '', 'authors' => '',
									'parent' => -1, 'exclude_tree' => '', 'number' => '', 'offset' => 0
								);
								$page_name = '';
								$pages_array = get_pages( $pages_args );
								if ( $pages_array ) {
									foreach ( $pages_array as $post ) {
										$page_name .= $post->post_title . '|';
									}
								} else {
									$page_name = __('No pages available');
								}
							?>
							<script type="text/javascript" charset="<?php bloginfo('charset'); ?>">
								jQuery(document).ready(function(){
									var posts = "<?php echo esc_js( $page_name ); ?>".split('|');
									jQuery('#add-pages .quick-search').autocomplete(posts);
									
									
									jQuery('#add-pages .quick-search').result(function(event, data, formatted) {
										jQuery('#add-pages .list-wrap').css('display','block');
										jQuery("#add-pages .list-wrap dt:contains('" + data + "')").css('display','block');
										jQuery('#add-pages .show-all').hide();
										jQuery('#add-pages .hide-all').show();
									});
								});
							</script>
							<p>
								<input type="text" class="quick-search regular-text" value="" />
								<a class="quick-search-submit button"><?php _e('Search'); ?></a>
							</p>
							
							<p class="button-controls">
								<a class="show-all button"><?php _e('View All'); ?></a>
								<a class="hide-all button"><?php _e('Hide All'); ?></a>
							</p>
							
							<div id="existing-pages" class="list-wrap">
								<div class="list-container">
									<ul class="list">
									<?php $items_counter = wp_nav_menu_get_pages( 0, 'default' ); ?>
									</ul>
								</div><!-- /.list-container-->
							</div><!-- /#existing-pages-->
							<p class="add-to-menu enqueue">
								<a class="button"><?php _e('Add to Menu'); ?></a>
							</p>
							<br class="clear" />
						</div><!-- /.inside-->
					</div><!--END #add-pages-->
					
					<div id="add-categories" class="postbox">
						<h3 class="hndle"><?php esc_html_e('Add an Existing Category'); ?></h3>
						<div class="inside">
							<?php
								// Custom GET categories query
								// @todo Use API
								$categories = $wpdb->get_results("SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id ASC");
								$cat_name = '';
								if ( $categories ) {
									foreach ( $categories as $category ) {
										$cat_id = $category->term_id;
										$cat_args = array(
											'orderby' => 'name',
											'include' => $cat_id,
											'hierarchical' => 1,
											'order' => 'ASC',
										);
										$category_names = get_categories( $cat_args );
										if ( isset( $category_names[0]->name ) ) {
											$cat_name .= htmlentities( $category_names[0]->name ).'|';
										}
									}
								} else {
									$cat_name = __('No categories available');
								}
							?>
							<script type="text/javascript" charset="<?php bloginfo('charset'); ?>">
								jQuery(document).ready(function(){
									var categories = "<?php echo esc_js($cat_name); ?>".split('|');
									jQuery('#add-categories .quick-search').autocomplete(categories);
									jQuery('#add-categories .quick-search').result(function(event, data, formatted) {
										jQuery('#add-categories .list-wrap').css('display','block');
										jQuery("#add-categories .list-wrap dt:contains('" + data + "')").css('display','block');
										jQuery('#add-categories .show-all').hide();
										jQuery('#add-categories .hide-all').show();
									});
								});
							</script>
							<p>
								<input type="text" class="quick-search regular-text" value="" />
								<a class="quick-search-submit button"><?php _e('Search'); ?></a>
							</p>
							
							<p class="button-controls">
								<a class="show-all button"><?php _e('View All'); ?></a>
								<a class="hide-all button"><?php _e('Hide All'); ?></a>
							</p>
							
							<div id="existing-categories" class="list-wrap">
								<div class="list-container">
									<ul class="list">
										<?php $items_counter = wp_nav_menu_get_categories( $items_counter, 'default' ); ?>
									</ul>
								</div><!-- /.list-container-->
							</div><!-- /#existing-categories-->
							<p class="add-to-menu enqueue">
								<a class="button"><?php _e('Add to Menu'); ?></a>
							</p>
							<br class="clear" />
						</div><!-- /.inside-->
					</div><!--END #add-categories-->
				</div><!-- /#side-sortables-->
			</div><!-- /#menu-settings-column -->
			<br class="clear" />
		</div><!-- /.metabox-holder has-right-sidebar-->
	</form>
</div><!-- /.wrap-->

<div id="menu-item-settings">
	<p class="description">
		<label for="edit-item-title">
			<?php _e( 'Menu Title' ); ?><br />
			<input type="text" id="edit-item-title" class="widefat" name="edit-item-title" value="" tabindex="1" />
		</label>
	</p>
	<p class="description">
		<label for="edit-item-url">
			<?php _e( 'URL' ); ?><br />
			<input type="text" id="edit-item-url" class="widefat code" name="edit-item-url" value="" tabindex="2" />
		</label>
	</p>
	<p class="description">
		<label for="edit-item-attr-title">
			<?php _e( 'Attribute Title' ); ?><br />
			<input type="text" id="edit-item-attr-title" class="widefat" name="edit-item-attr-title" value="" tabindex="3" />
		</label>
	</p>
	<p class="description">
		<label for="edit-item-target">
			<?php _e( 'Open Link in a new window' ); ?><br />
			<select id="edit-item-target" class="widefat" name="edit-item-target">
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>
		</label>
	</p>
	<p class="description">
		<label for="edit-item-description">
			<?php _e( 'Description' ); ?><br />
			<textarea id="edit-item-description" class="widefat" rows="3" name="edit-item-description" tabindex="4" /></textarea>
		</label>
	</p>
	<p>
		<a id="cancel-save" class="submitdelete deletion"><?php _e('Cancel'); ?></a>
		<a id="update-menu-item" class="save button-primary" tabindex="5"><?php _e('Save Changes'); ?></a>
	</p>
	<input type="hidden" id="edit-item-id" name="edit-item-id" value="" />
</div><!-- /#menu-item-settings-->

<?php include( 'admin-footer.php' ); ?>