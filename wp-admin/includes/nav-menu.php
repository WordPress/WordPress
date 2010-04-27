<?php

/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit extends Walker_Nav_Menu  {

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);
		?>
		<li id="menu-item-<?php echo $item_id; ?>">
			<dl>
				<dt>
					<span class="item-title"><?php echo esc_html( $item->title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->append ); ?></span>
						<span class="item-order">
							<a href="<?php
								echo wp_nonce_url( 
									add_query_arg(
										array(
											'action' => 'move-up-menu-item', 
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) 
									), 
									'move-item' 
								); 
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url( 
									add_query_arg(
										array(
											'action' => 'move-down-menu-item', 
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) 
									), 
									'move-item'
								); 
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
							|
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php _e('Edit Menu Item'); ?>" href="<?php 
							echo add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) ); 
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Edit'); ?></a> |
						<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php 
						echo wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'delete-menu-item',
									'menu-item' => $item_id,
								),
								remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) 
							),
							'delete-menu_item_' . $item_id
						); ?>"><?php _e('Delete'); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings <?php 
				if ( isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item'] ) :
					echo ' menu-item-edit-active';
				else :
					echo ' menu-item-edit-inactive';
				endif;
			?>" id="menu-item-settings-<?php echo $item_id; ?>">
				<p class="description">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Menu Title' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e( 'URL' ); ?><br />
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<?php _e( 'Link Target' ); ?><br />
						<select id="edit-menu-item-target-<?php echo $item_id; ?>" class="widefat edit-menu-item-target" name="menu-item-target[<?php echo $item_id; ?>]">
							<option value="" <?php selected( $item->target, ''); ?>><?php _e('Same window or tab'); ?></option>
							<option value="_blank" <?php selected( $item->target, '_blank'); ?>><?php _e('New window or tab'); ?></option>
						</select>
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->classes ); ?>" />
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN) (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="description">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description (optional)' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
					</label>
				</p>
				
				<input type="hidden" name="menu-item-append[<?php echo $item_id; ?>]" value="<?php echo $item->append; ?>" />
				<input type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_parent ); ?>" />
				<input type="hidden" class="menu-item-position" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
		<?php
		$output .= ob_get_clean();
	}
}

/**
 * Prints the appropriate response to a menu quick search.
 *
 * @since 3.0.0
 * 
 * @param array $request The unsanitized request values.
 */
function _wp_ajax_menu_quick_search( $request = array() ) {
	$args = array();
	$type = isset( $request['type'] ) ? $request['type'] : '';
	$object_type = isset( $request['object_type'] ) ? $request['object_type'] : '';
	$query = isset( $request['q'] ) ? $request['q'] : '';
	$response_format = isset( $request['response-format'] ) && in_array( $request['response-format'], array( 'json', 'markup' ) ) ? $request['response-format'] : 'json';

	if ( 'markup' == $response_format ) {
		$args['walker'] = new Walker_Nav_Menu_Checklist;
	}

	if ( 'get-post-item' == $type ) {
		if ( get_post_type_object( $object_type ) ) {
			if ( isset( $request['ID'] ) ) {
				$object_id = (int) $request['ID'];
				if ( 'markup' == $response_format ) {
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_post( $object_id ) ) ), 0, (object) $args );
				} elseif ( 'json' == $response_format ) {
					$post_obj = get_post( $object_id );
					echo json_encode(
						array(
							'ID' => $object_id,
							'post_title' => get_the_title( $object_id ),
							'post_type' => get_post_type( $object_id ),
						)
					);
					echo "\n";
				}
			}
		} elseif ( is_taxonomy( $object_type ) ) {
			if ( isset( $request['ID'] ) ) {
				$object_id = (int) $request['ID'];
				if ( 'markup' == $response_format ) {
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_term( $object_id, $object_type ) ) ), 0, (object) $args );
				} elseif ( 'json' == $response_format ) {
					$post_obj = get_term( $object_id, $object_type );
					echo json_encode(
						array(
							'ID' => $object_id,
							'post_title' => $post_obj->name,
							'post_type' => $object_type,
						)
					);
					echo "\n";
				}
			}

		}


	} elseif ( preg_match('/quick-search-(posttype|taxonomy)-([a-zA-Z_-]*\b)/', $type, $matches) ) {
		if ( 'posttype' == $matches[1] && get_post_type_object( $matches[2] ) ) {
			query_posts(array(
				'posts_per_page' => 10,
				'post_type' => $matches[2],
				's' => $query,
			));
			while ( have_posts() ) {
				the_post();
				if ( 'markup' == $response_format ) {
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( get_post( get_the_ID() ) ) ), 0, (object) $args );
				} elseif ( 'json' == $response_format ) {
					echo json_encode(
						array(
							'ID' => get_the_ID(),
							'post_title' => get_the_title(),
							'post_type' => get_post_type(),
						)
					);
					echo "\n";
				}
			}
		} elseif ( 'taxonomy' == $matches[1] ) {
			$terms = get_terms( $matches[2], array(
				'name__like' => $query,
				'number' => 10,
			));
			foreach( (array) $terms as $term ) {
				if ( 'markup' == $response_format ) {
					echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', array( $term ) ), 0, (object) $args );
				} elseif ( 'json' == $response_format ) {
					echo json_encode(
						array(
							'ID' => $term->term_id,
							'post_title' => $term->name,
							'post_type' => $matches[2],
						)
					);
					echo "\n";
				}
			}
		}
	}
}

/**
 * Register nav menu metaboxes
 *
 * @since 3.0.0
 **/
function wp_nav_menu_meta_boxes_setup() {
	add_meta_box( 'add-custom-links', __('Add Custom Links'), 'wp_nav_menu_item_link_meta_box', 'nav-menus', 'side', 'default' );
	wp_nav_menu_post_type_meta_boxes();
	wp_nav_menu_taxonomy_meta_boxes();
}

/**
 * Limit the amount of meta boxes to just links, pages and cats for first time users.
 *
 * @since 3.0.0
 **/
function wp_initial_nav_menu_meta_boxes() {
	global $wp_meta_boxes;

	if ( !get_user_option( 'meta-box-hidden_nav-menus' ) && is_array($wp_meta_boxes) ) {

		$initial_meta_boxes = array( 'manage-menu', 'create-menu', 'add-custom-links', 'add-page', 'add-category' );
		$hidden_meta_boxes = array();

		foreach ( array_keys($wp_meta_boxes['nav-menus']) as $context ) {
			foreach ( array_keys($wp_meta_boxes['nav-menus'][$context]) as $priority ) {
				foreach ( $wp_meta_boxes['nav-menus'][$context][$priority] as $box ) {
					if ( in_array( $box['id'], $initial_meta_boxes ) ) {
						unset( $box['id'] );
					} else {
						$hidden_meta_boxes[] = $box['id'];
					}
				}
			}
		}
		$user = wp_get_current_user();
		update_user_meta( $user->ID, 'meta-box-hidden_nav-menus', $hidden_meta_boxes );

		// returns all the hidden metaboxes to the js function: wpNavMenu.initial_meta_boxes()
		return join( ',', $hidden_meta_boxes );
	}
}

/**
 * Creates metaboxes for any post type menu item.
 *
 * @since 3.0.0
 */
function wp_nav_menu_post_type_meta_boxes() {
	$post_types = get_post_types( array( 'public' => true ), 'object' );

	if ( !$post_types )
		return;

	foreach ( $post_types as $post_type ) {
		$id = $post_type->name;
		add_meta_box( "add-{$id}", sprintf( __('Add %s'), $post_type->label ), 'wp_nav_menu_item_post_type_meta_box', 'nav-menus', 'side', 'default', $post_type );
	}
}

/**
 * Creates metaboxes for any taxonomy menu item.
 *
 * @since 3.0.0
 */
function wp_nav_menu_taxonomy_meta_boxes() {
	$taxonomies = get_taxonomies( array( 'show_ui' => true ), 'object' );

	if ( !$taxonomies )
		return;

	foreach ( $taxonomies as $tax ) {
		$id = $tax->name;
		add_meta_box( "add-{$id}", sprintf( __('Add %s'), $tax->label ), 'wp_nav_menu_item_taxonomy_meta_box', 'nav-menus', 'side', 'default', $tax );
	}
}

/**
 * Displays a metabox for the custom links menu item.
 *
 * @since 3.0.0
 */
function wp_nav_menu_item_link_meta_box() {
	static $_placeholder;
	$_placeholder = 0 > $_placeholder ? $_placeholder - 1 : -1;

	// @note: hacky query, see #12660
	$args = array( 'post_type' => 'nav_menu_item', 'post_status' => 'any', 'meta_key' => '_menu_item_type', 'numberposts' => -1, 'orderby' => 'title', );

	// @todo transient caching of these results with proper invalidation on updating links
	$links = get_posts( $args );

	$current_tab = 'create';
	if ( isset( $_REQUEST['customlink-tab'] ) && in_array( $_REQUEST['customlink-tab'], array('create', 'all') ) ) {
		$current_tab = $_REQUEST['customlink-tab'];
	}

	$removed_args = array(
		'action', 
		'customlink-tab',
		'edit-menu-item',
		'menu-item',
		'page-tab',
		'_wpnonce',
	);

	?>
	<div class="customlinkdiv">
		<ul id="customlink-tabs" class="customlink-tabs add-menu-item-tabs">
			<li <?php echo ( 'create' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg('customlink-tab', 'create', remove_query_arg($removed_args)); ?>#tabs-panel-create-custom"><?php _e('Create New'); ?></a></li>
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg('customlink-tab', 'all', remove_query_arg($removed_args)); ?>#tabs-panel-all-custom"><?php _e('View All'); ?></a></li>
		</ul>

		<div class="tabs-panel <?php 
			echo ( 'create' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>" id="tabs-panel-create-custom">
			<input type="hidden" value="custom" name="menu-item[<?php echo $_placeholder; ?>][menu-item-type]" />
			<p id="menu-item-url-wrap">
				<label class="howto" for="custom-menu-item-url">
					<span><?php _e('URL'); ?></span>
					<input id="custom-menu-item-url" name="menu-item[<?php echo $_placeholder; ?>][menu-item-url]" type="text" class="code menu-item-textbox" value="http://" />
				</label>
			</p>

			<p id="menu-item-name-wrap">
				<label class="howto" for="custom-menu-item-name">
					<span><?php _e('Text'); ?></span>
					<input id="custom-menu-item-name" name="menu-item[<?php echo $_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" value="<?php echo esc_attr( __('Menu Item') ); ?>" />
				</label>
			</p>
		</div><!-- /.tabs-panel -->

		<div class="tabs-panel <?php 
			echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>" id="tabs-panel-all-custom">
			<ul id="customlinkchecklist" class="list:customlink customlinkchecklist form-no-clear">
				<?php
				$args['walker'] = new Walker_Nav_Menu_Checklist;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $links), 0, (object) $args );
				?>
			</ul>
		</div><!-- /.tabs-panel -->

		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit" class="button-secondary" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" />
			</span>
		</p>

		<div class="clear"></div>
	</div><!-- /.customlinkdiv -->
	<?php
}

/**
 * Displays a metabox for a post type menu item.
 *
 * @since 3.0.0
 *
 * @param string $object Not used.
 * @param string $post_type The post type object.
 */
function wp_nav_menu_item_post_type_meta_box( $object, $post_type ) {
	$post_type_name = $post_type['args']->name;

	// paginate browsing for large numbers of post objects
	$per_page = 50;
	$pagenum = isset( $_REQUEST[$post_type_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;

	$args = array( 
		'offset' => $offset, 
		'order' => 'ASC',
		'orderby' => 'title', 
		'posts_per_page' => $per_page, 
		'post_type' => $post_type_name, 
		'suppress_filters' => true, 
	);

	// @todo transient caching of these results with proper invalidation on updating of a post of this type
	$get_posts = new WP_Query;
	$posts = $get_posts->query( $args );

	$post_type_object = get_post_type_object($post_type_name);

	$num_pages = $get_posts->max_num_pages;

	$count_posts = (int) @count( $posts );

	if ( isset( $get_posts->found_posts ) && ( $get_posts->found_posts > $count_posts ) ) {
		// somewhat like display_page_row(), let's make sure ancestors show up on paged display
		$parent_ids = array();
		$child_ids = array();
		foreach( (array) $posts as $post ) {
			$parent_ids[] = (int) $post->post_parent;
			$child_ids[] = (int) $post->ID;
		}
		$parent_ids = array_unique($parent_ids);
		$child_ids = array_unique($child_ids);
		
		$missing_parents = array();
		do {
			foreach( (array) $missing_parents as $missing_parent_id ) {
				$missing_parent = get_post($missing_parent_id);
				$posts[] = $missing_parent;
				$child_ids[] = $missing_parent_id;
				$parent_ids[] = $missing_parent->post_parent;
			}
			
			$missing_parents = array_filter( array_diff( array_unique( $parent_ids ), array_unique( $child_ids ) ) );

		} while( 0 < count( $missing_parents ) );
		
	}

	$page_links = paginate_links( array(
		'base' => add_query_arg( 
			array(
				$post_type_name . '-tab' => 'all',
				'paged' => '%#%',
			)
		),
		'format' => '', 
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $num_pages,
		'current' => $pagenum
	));
	
	if ( !$posts )
		$error = '<li id="error">'. sprintf( __( 'No %s exists' ), $post_type['args']->label ) .'</li>';

	$current_tab = 'search';
	if ( isset( $_REQUEST[$post_type_name . '-tab'] ) && in_array( $_REQUEST[$post_type_name . '-tab'], array('all', 'search') ) ) {
		$current_tab = $_REQUEST[$post_type_name . '-tab'];
	}

	if ( ! empty( $_REQUEST['quick-search-posttype-' . $post_type_name] ) ) {
		$current_tab = 'search';
	}

	$removed_args = array(
		'action', 
		'customlink-tab',
		'edit-menu-item',
		'menu-item',
		'page-tab',
		'_wpnonce',
	);

	?>
	<div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv">
		<ul id="posttype-<?php echo $post_type_name; ?>-tabs" class="posttype-tabs add-menu-item-tabs">
			<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg($post_type_name . '-tab', 'search', remove_query_arg($removed_args)); ?>#tabs-panel-posttype-<?php echo $post_type_name; ?>-search"><?php _e('Search'); ?></a></li>
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg($post_type_name . '-tab', 'all', remove_query_arg($removed_args)); ?>#<?php echo $post_type_name; ?>-all"><?php _e('View All'); ?></a></li>
		</ul>

		<div class="tabs-panel <?php 
			echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>" id="tabs-panel-posttype-<?php echo $post_type_name; ?>-search">
			<?php 
			if ( isset( $_REQUEST['quick-search-posttype-' . $post_type_name] ) ) {
				$searched = esc_attr( $_REQUEST['quick-search-posttype-' . $post_type_name] );
				$search_results = get_posts( array( 's' => $searched, 'post_type' => $post_type_name, 'fields' => 'all', 'order' => 'DESC', ) );
			} else {
				$searched = '';
				$search_results = array();
			}
			?>
			<p class="quick-search-wrap">
				<input type="text" class="quick-search regular-text" value="<?php echo $searched; ?>" name="quick-search-posttype-<?php echo $post_type_name; ?>" />
				<input type="submit" class="quick-search-submit button-secondary" value="<?php esc_attr_e('Search'); ?>" />
			</p>

			<ul id="<?php echo $post_type_name; ?>-search-checklist" class="list:<?php echo $post_type_name?> categorychecklist form-no-clear">
			<?php if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ) : ?>
				<?php
				$args['walker'] = new Walker_Nav_Menu_Checklist;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $search_results), 0, (object) $args );
				?>
			<?php endif; ?>
			</ul>
		</div><!-- /.tabs-panel -->


		<div id="<?php echo $post_type_name; ?>-all" class="tabs-panel <?php
			echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>">
			<div class="add-menu-item-pagelinks">
				<?php echo $page_links; ?>
			</div>
			<ul id="<?php echo $post_type_name; ?>checklist" class="list:<?php echo $post_type_name?> categorychecklist form-no-clear">
				<?php
				$args['walker'] = new Walker_Nav_Menu_Checklist;
				$checkbox_items = walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $posts), 0, (object) $args );

				if ( 'all' == $current_tab && ! empty( $_REQUEST['selectall'] ) ) {
					$checkbox_items = preg_replace('/(type=(.)checkbox(\2))/', '$1 checked=$2checked$2', $checkbox_items);
					
				}
				echo $checkbox_items;
				?>
			</ul>
			<div class="add-menu-item-pagelinks">
				<?php echo $page_links; ?>
			</div>
		</div><!-- /.tabs-panel -->


		<p class="button-controls">
			<span class="lists-controls">
				<a href="<?php 
					echo add_query_arg(
						array(
							$post_type_name . '-tab' => 'all',
							'selectall' => 1,
						),
						remove_query_arg($removed_args)
					);
				?>#posttype-<?php echo $post_type_name; ?>" class="select-all"><?php _e('Select All'); ?></a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-post-type-menu-item" />
			</span>
		</p>

		<br class="clear" />
	</div><!-- /.posttypediv -->
	<?php
}

/**
 * Displays a metabox for a taxonomy menu item.
 *
 * @since 3.0.0
 *
 * @param string $object Not used.
 * @param string $taxonomy The taxonomy object.
 */
function wp_nav_menu_item_taxonomy_meta_box( $object, $taxonomy ) {
	$taxonomy_name = $taxonomy['args']->name;
	// paginate browsing for large numbers of objects
	$per_page = 50;
	$pagenum = isset( $_REQUEST[$taxonomy_name . '-tab'] ) && isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
	$offset = 0 < $pagenum ? $per_page * ( $pagenum - 1 ) : 0;
	
	$args = array(
		'child_of' => 0, 
		'exclude' => '',
		'hide_empty' => false, 
		'hierarchical' => 1, 
		'include' => '', 
		'include_last_update_time' => false, 
		'number' => $per_page, 
		'offset' => $offset,
		'order' => 'ASC',
		'orderby' => 'name', 
		'pad_counts' => false,
	);

	$num_pages = ceil( wp_count_terms($taxonomy_name) / $per_page );

	$page_links = paginate_links( array(
		'base' => add_query_arg( 
			array(
				$taxonomy_name . '-tab' => 'all',
				'paged' => '%#%',
			)
		),
		'format' => '', 
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $num_pages,
		'current' => $pagenum
	));
	
	$walker = new Walker_Nav_Menu_Checklist;
	// @todo transient caching of these results with proper invalidation on updating of a tax of this type
	$terms = get_terms( $taxonomy_name, $args );

	if ( ! $terms || is_wp_error($terms) )
		$error = '<li id="error">'. sprintf( __( 'No %s exists' ), $taxonomy['args']->label ) .'</li>';

	$current_tab = 'most-used';
	if ( isset( $_REQUEST[$taxonomy_name . '-tab'] ) && in_array( $_REQUEST[$taxonomy_name . '-tab'], array('all', 'most-used', 'search') ) ) {
		$current_tab = $_REQUEST[$taxonomy_name . '-tab'];
	}

	if ( ! empty( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
		$current_tab = 'search';
	}

	$removed_args = array(
		'action', 
		'customlink-tab',
		'edit-menu-item',
		'menu-item',
		'page-tab',
		'_wpnonce',
	);

	?>
	<div id="taxonomy-<?php echo $taxonomy_name; ?>" class="taxonomydiv">
		<ul id="taxonomy-<?php echo $taxonomy_name; ?>-tabs" class="taxonomy-tabs add-menu-item-tabs">
			<li <?php echo ( 'most-used' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg($taxonomy_name . '-tab', 'most-used', remove_query_arg($removed_args)); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-pop"><?php _e('Most Used'); ?></a></li>
			<li <?php echo ( 'search' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg($taxonomy_name . '-tab', 'search', remove_query_arg($removed_args)); ?>#tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>"><?php _e('Search'); ?></a></li>
			<li <?php echo ( 'all' == $current_tab ? ' class="tabs"' : '' ); ?>><a class="menu-tab-link" href="<?php echo add_query_arg($taxonomy_name . '-tab', 'all', remove_query_arg($removed_args)); ?>#tabs-panel-<?php echo $taxonomy_name; ?>-all"><?php _e('View All'); ?></a></li>
		</ul>

		<div id="tabs-panel-<?php echo $taxonomy_name; ?>-pop" class="tabs-panel <?php
			echo ( 'most-used' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>">
			<ul id="<?php echo $taxonomy_name; ?>checklist-pop" class="categorychecklist form-no-clear" >
				<?php
				$popular_terms = get_terms( $taxonomy_name, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
				$args['walker'] = $walker;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $popular_terms), 0, (object) $args );
				?>
				<?php 
				?>
			</ul>
		</div><!-- /.tabs-panel -->

		<div class="tabs-panel <?php 
			echo ( 'search' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>" id="tabs-panel-search-taxonomy-<?php echo $taxonomy_name; ?>">
			<?php 
			if ( isset( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] ) ) {
				$searched = esc_attr( $_REQUEST['quick-search-taxonomy-' . $taxonomy_name] );
				$search_results = get_terms( $taxonomy_name, array( 'name__like' => $searched, 'fields' => 'all', 'orderby' => 'count', 'order' => 'DESC', 'hierarchical' => false ) );
			} else {
				$searched = '';
				$search_results = array();
			}
			?>
			<p class="quick-search-wrap">
				<input type="text" class="quick-search regular-text" value="<?php echo $searched; ?>" name="quick-search-taxonomy-<?php echo $taxonomy_name; ?>" />
				<input type="submit" class="quick-search-submit button-secondary" value="<?php esc_attr_e('Search'); ?>" />
			</p>
		
			<ul id="<?php echo $taxonomy_name; ?>-search-checklist" class="list:<?php echo $taxonomy_name?> categorychecklist form-no-clear">
			<?php if ( ! empty( $search_results ) && ! is_wp_error( $search_results ) ) : ?>
				<?php
				$args['walker'] = $walker;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $search_results), 0, (object) $args );
				?>
			<?php endif; ?>
			</ul>
		</div><!-- /.tabs-panel -->

		<div id="tabs-panel-<?php echo $taxonomy_name; ?>-all" class="tabs-panel <?php
			echo ( 'all' == $current_tab ? 'tabs-panel-active' : 'tabs-panel-inactive' );
		?>">
			<div class="add-menu-item-pagelinks">
				<?php echo $page_links; ?>
			</div>
			<ul id="<?php echo $taxonomy_name; ?>checklist" class="list:<?php echo $taxonomy_name?> categorychecklist form-no-clear">
				<?php
				$args['walker'] = $walker;
				echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args );
				?>
			</ul>
			<div class="add-menu-item-pagelinks">
				<?php echo $page_links; ?>
			</div>
		</div><!-- /.tabs-panel -->

		<p class="button-controls">
			<span class="lists-controls">
				<a href="<?php 
					echo add_query_arg(
						array(
							$taxonomy_name . '-tab' => 'all',
							'selectall' => 1,
						),
						remove_query_arg($removed_args)
					);
				?>#taxonomy-<?php echo $taxonomy_name; ?>" class="select-all"><?php _e('Select All'); ?></a>
			</span>

			<span class="add-to-menu">
				<input type="submit" class="button-secondary" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-taxonomy-menu-item" />
			</span>
		</p>

		<br class="clear" />
	</div><!-- /.taxonomydiv -->
	<?php
}

/**
 * Save posted nav menu item data.
 *
 * @since 3.0.0
 *
 * @param int $menu_id The menu ID for which to save this item.
 * @param array $menu_data The unsanitized posted menu item data.
 * @return array The database IDs of the items saved.
 */
function wp_save_nav_menu_item( $menu_id = 0, $menu_data = array() ) {
	$menu_id = (int) $menu_id;
	$items_saved = array();

	if ( is_nav_menu( $menu_id ) ) {

		// Loop through all the menu items' POST values
		foreach( (array) $menu_data as $_possible_db_id => $_item_object_data ) {
			if ( 
				empty( $_item_object_data['menu-item-object-id'] ) && // checkbox is not checked
				( 
					! isset( $_item_object_data['menu-item-type'] ) || // and item type either isn't set 
					in_array( $_item_object_data['menu-item-url'], array( 'http://', '' ) ) || // or URL is the default
					'custom' != $_item_object_data['menu-item-type'] ||  // or it's not a custom menu item	
					! empty( $_item_object_data['menu-item-db-id'] ) // or it *is* a custom menu item that already exists
				)
			) {
				continue; // then this potential menu item is not getting added to this menu
			}

			// if this possible menu item doesn't actually have a menu database ID yet
			if ( 
				empty( $_item_object_data['menu-item-db-id'] ) ||
				( 0 > $_possible_db_id ) ||
				$_possible_db_id != $_item_object_data['menu-item-db-id']
			) {
				$_actual_db_id = 0;
			} else {
				$_actual_db_id = (int) $_item_object_data['menu-item-db-id'];
			}
			
			$args = array(
				'menu-item-db-id' => ( isset( $_item_object_data['menu-item-db-id'] ) ? $_item_object_data['menu-item-db-id'] : '' ),
				'menu-item-object-id' => ( isset( $_item_object_data['menu-item-object-id'] ) ? $_item_object_data['menu-item-object-id'] : '' ),
				'menu-item-object' => ( isset( $_item_object_data['menu-item-object'] ) ? $_item_object_data['menu-item-object'] : '' ),
				'menu-item-parent-id' => ( isset( $_item_object_data['menu-item-parent-id'] ) ? $_item_object_data['menu-item-parent-id'] : '' ),
				'menu-item-position' => ( isset( $_item_object_data['menu-item-position'] ) ? $_item_object_data['menu-item-position'] : '' ),
				'menu-item-type' => ( isset( $_item_object_data['menu-item-type'] ) ? $_item_object_data['menu-item-type'] : '' ),
				'menu-item-append' => ( isset( $_item_object_data['menu-item-append'] ) ? $_item_object_data['menu-item-append'] : '' ),
				'menu-item-title' => ( isset( $_item_object_data['menu-item-title'] ) ? $_item_object_data['menu-item-title'] : '' ),
				'menu-item-url' => ( isset( $_item_object_data['menu-item-url'] ) ? $_item_object_data['menu-item-url'] : '' ),
				'menu-item-description' => ( isset( $_item_object_data['menu-item-description'] ) ? $_item_object_data['menu-item-description'] : '' ),
				'menu-item-attr-title' => ( isset( $_item_object_data['menu-item-attr-title'] ) ? $_item_object_data['menu-item-attr-title'] : '' ),
				'menu-item-target' => ( isset( $_item_object_data['menu-item-target'] ) ? $_item_object_data['menu-item-target'] : '' ),
				'menu-item-classes' => ( isset( $_item_object_data['menu-item-classes'] ) ? $_item_object_data['menu-item-classes'] : '' ),
				'menu-item-xfn' => ( isset( $_item_object_data['menu-item-xfn'] ) ? $_item_object_data['menu-item-xfn'] : '' ),
			);

			$items_saved[] = wp_update_nav_menu_item( $menu_id, $_actual_db_id, $args );

		}
	}
	return $items_saved;
}

/**
 * Returns the menu item formatted to edit.
 *
 * @since 3.0.0
 *
 * @param string $menu_item_id The ID of the menu item to format.
 * @return string|WP_Error $output The menu formatted to edit or error object on failure.
 */
function wp_get_nav_menu_to_edit( $menu_item_id = 0 ) {
	static $_placeholder;
	
	$menu = wp_get_nav_menu_object( $menu_item_id );
	
	// If the menu exists, get its items.
	if ( is_nav_menu( $menu ) ) {
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

		$walker = new Walker_Nav_Menu_Edit; 

		return walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $menu_items), 0, (object) array('walker' => $walker ) );
	} elseif ( is_wp_error( $menu ) ) {
		return $menu;	
	}


}

?>
