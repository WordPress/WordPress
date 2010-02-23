<?php
/**
 * WordPress Administration Custom Navigation
 * General Functions
 *
 * @author Jeffikus <pearce.jp@gmail.com>
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

function wp_custom_navigation_get_menu_items( $menu_objects, $key = 'ID' ) {
	$menu_items = array();
	if ( !empty( $menu_objects ) && !empty( $key ) ) {
		$args = array( 'orderby' => 'menu_order', 'post_type' => 'nav_menu_item', 'post_status' => 'publish' );
		if ( count( $menu_objects ) > 1 )
			$args['include'] = implode( ',', $menu_objects );
		else
			$args['include'] = $menu_objects[0];
		$posts = get_posts( $args );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$menu_items[ $post->$key ] = $post;
			}
		}
		unset( $posts );
		ksort( $menu_items );
	}
	return $menu_items;
}

function wp_custom_navigation_setup($override = false) {
	// Custom Navigation Menu Setup

	// Override for menu descriptions
	update_option('wp_settings_custom_nav_advanced_options', 'yes');

	$custom_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
 	if ( !empty( $custom_menus ) ) {
		foreach ( $custom_menus as $menu ) {
			wp_custom_navigation_delete_menu( $menu->term_id );
		}
	}
}

function wp_custom_navigation_delete_menu( $menu_term_id ) {
	$term_id = (int) $menu_term_id;
	if ( $term_id > 0 ) {
		$menu_objects = get_objects_in_term( $term_id, 'nav_menu' );
		if ( !empty( $menu_objects ) ) {
			foreach ( $menu_objects as $item ) {
				wp_delete_post( $item );
			}
		}
		wp_delete_term( $term_id, 'nav_menu' );
	}
}

function setup_menu_item($menu_item, $type = 'item', $position = 0) {
	global $parent_menu_order;

	if ( 'item' == $type ) {
		$menu_item->type = get_post_meta($menu_item->ID, 'menu_type', true);
		$menu_item->object_id = get_post_meta($menu_item->ID, 'object_id', true);
		if ( isset( $parent_menu_order[ $menu_item->post_parent ] ) )
			$menu_item->parent_item = $parent_menu_order[ $menu_item->post_parent ];
		else
			$menu_item->parent_item = 0;
	} elseif ( 'category' == $type ) {
		$menu_item->type = $type;
		$menu_item->object_id = $menu_item->term_id;
		$menu_item->parent_item = $menu_item->parent;
		$menu_item->menu_order = $position;
	} elseif ( 'page' == $type ) {
		$menu_item->type = $type;
		$menu_item->object_id = $menu_item->ID;
		$menu_item->parent_item = $menu_item->post_parent;
		$menu_item->menu_order = $position;
	}

	switch ( $menu_item->type ) {
		// Page Menu Item
		case 'page':
			if ( $menu_item->guid == '' )
				$menu_item->link = get_permalink( $menu_item->object_id );
			else
				$menu_item->link = $menu_item->guid;

			if ( $menu_item->post_title == '' )
				$menu_item->title = htmlentities( get_the_title( $menu_item->object_id ) );
			else
				$menu_item->title = htmlentities( $menu_item->post_title );

			if ( $menu_item->post_content == '' )
				$menu_item->description = htmlentities( get_post_meta( $menu_item->ID, 'page-description', true ) );
			else
				$menu_item->description = htmlentities( $menu_item->post_content );
			$menu_item->target = '';
			$menu_item->append = 'Page';
		break;
		// Category Menu Item
		case 'category':
			if ( $menu_item->guid == '' )
				$menu_item->link = get_category_link( $menu_item->object_id );
			else
				$menu_item->link = $menu_item->guid;

			if ( $menu_item->post_title == '' ) {
				$title_raw = get_categories( array('include' => $menu_item->object_id) );
				$menu_item->title =  htmlentities($title_raw[0]->cat_name);
			} else {
				$menu_item->title = htmlentities( $menu_item->post_title );
			}

			if ( $menu_item->post_content == '' )
				$menu_item->description = htmlentities( strip_tags( category_description( $menu_item->object_id ) ) );
			else
				$menu_item->description = htmlentities( $menu_item->post_content );
			$menu_item->target = '';
			$menu_item->append = 'Category';
		break;
		default:
			// Custom Menu Item
			$menu_item->link = $menu_item->guid;
			$menu_item->title =  htmlentities( $menu_item->post_title );
			$menu_item->description = htmlentities( $menu_item->post_content );
			$menu_item->target = 'target="_blank"';
			$menu_item->append = 'Custom';
		break;
	}

	$menu_item->li_class = '';
	global $wp_query;
	if ( $menu_item->ID == $wp_query->get_queried_object_id() )
		$menu_item->li_class = 'class="current_page_item"';

/* @todo: update to use tax/post data

			//SET anchor title
			if (isset($wp_custom_nav_menu_items->custom_anchor_title)) {
				$anchor_title = htmlentities($wp_custom_nav_menu_items->custom_anchor_title);
			}
			else {
				$anchor_title = $title;
			}

			if ($queried_id == $wp_custom_nav_menu_items->post_id) {
				$li_class = 'class="current_page_item"';
			}

			if (isset($wp_custom_nav_menu_items->new_window)) {
				if ($wp_custom_nav_menu_items->new_window > 0) {
					$target = 'target="_blank"';
				}
				else {
					$target = '';
				}
			}
*/

	return $menu_item;
}

function output_menu_item($menu_item, $context) {
	switch( $context ) {
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
						<php } else { ?>
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
	}
}

/*-----------------------------------------------------------------------------------*/
/* Custom Navigation Functions */
/* wp_custom_navigation_output() displays the menu in the back/frontend
/* wp_custom_nav_get_pages()
/* wp_custom_nav_get_categories()
/* wp_custom_navigation_default_sub_items() is a recursive sub menu item function
/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Main Output Function
/* args list
/* type - frontend or backend
/* name - name of your menu
/* id - id of menu in db
/* desc - 1 = show descriptions, 2 = dont show descriptions
/* before_title - html before title is outputted in <a> tag
/* after_title - html after title is outputted in <a> tag
/*-----------------------------------------------------------------------------------*/
function wp_custom_navigation_output( $args = array() ) {
		// Defaults
		$defaults = array( 'type' => 'frontend', 'name' => 'Menu 1', 'id' => 0, 'desc' => 2, 'before_title' => '', 'after_title' => '');

		$args = wp_parse_args($args, $defaults);
		extract($args, EXTR_SKIP);

		$menu_objects = get_objects_in_term( $id, 'nav_menu' );
		$menu_items = wp_custom_navigation_get_menu_items( $menu_objects, 'menu_order' );
		// Override for menu descriptions
		$advanced_option_descriptions = get_option('wp_settings_custom_nav_advanced_options');
		if ( $advanced_option_descriptions == 'no' )
			$desc = 2;

		$parent_stack = array();
		$current_parent = 0;
		$parent_menu_order = array();
		// Setup parentage
		foreach ( $menu_items as $key => $menu_item ) {
			$parent_menu_order[ $menu_item->ID ] = $menu_item->menu_order;
		}

	    // Display Loop
		foreach ( $menu_items as $key => $menu_item ) {
			$menu_item = setup_menu_item($menu_item);
			// List Items
			?><li id="menu-<?php echo $menu_item->ID; ?>" value="<?php echo $menu_item->ID; ?>" <?php echo $menu_item->li_class; ?>><?php
					//@todo: update front end to use post data
					//FRONTEND Link
					if ( $type == 'frontend' ) {
						?><a title="<?php echo $menu_item->anchor_title; ?>" href="<?php echo $menu_item->link; ?>" <?php echo $menu_item->target; ?>><?php echo $before_title.$menu_item->title.$after_title; ?><?php

							if ( $advanced_option_descriptions == 'no' ) {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ( ($desc == 1) || ($desc == 0) ) {
									?><span class="nav-description"><?php echo $menu_item->description; ?></span><?php
								}
							} else {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ( $desc == 1 ) {
									?><span class="nav-description"><?php echo $menu_item->description; ?></span><?php
								}
							}

						?></a><?php
					} elseif ( $type == 'backend' ) {
						output_menu_item($menu_item, 'backend');
					}
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
<?php			}
	}		
}

// Outputs All Pages and Sub Items
function wp_custom_nav_get_pages($counter, $type) {

	$pages_args = array(
		    'child_of' => 0,
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0 );

	//GET all pages
	$pages_array = get_pages($pages_args);

	$intCounter = $counter;
	$parentli = $intCounter;

	if ( !$pages_array ) {
		echo 'Not Found';
		return $intCounter;
	}

	// Display Loop
	foreach ( $pages_array as $post ) {
		if ($post->post_parent == 0) {
			// Custom Menu
			if ( $type == 'menu' ) {
				$description = get_post_meta($post->ID, 'page-description', true);
				$post = setup_menu_item($post, 'page', $intCounter);
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">

					<?php output_menu_item($post, 'menu', $intCounter); ?>

					<?php $parentli = $post->ID; ?>
					<?php $intCounter++; ?>
					<?php

						//Recursive function
						$intCounter = wp_custom_navigation_default_sub_items($post->ID, $intCounter, $parentli, 'pages', 'menu');

					?>

				</li>

				<?php

			} elseif ( $type == 'default' ) {
				// Sidebar Menu
				?>

				 <li>
					<dl>
					<dt>
					<?php
						$post_text = htmlentities($post->post_title);
						$post_url = get_permalink($post->ID);
						$post_id = $post->ID;
						$post_parent_id = $post->post_parent;

						$description = htmlentities(get_post_meta($post_id, 'page-description', true));

					?>
					<?php $templatedir = get_bloginfo('url'); ?>

					<span class="title"><?php echo $post->post_title; ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','Page','<?php echo $post_text; ?>','<?php echo $post_url; ?>','<?php echo $post_id; ?>','<?php echo $post_parent_id ?>','<?php echo $description; ?>')" name="<?php echo $post_text; ?>" value="<?php echo get_permalink($post->ID); ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-add.png" /></a></dt>
					</dl>
					<?php $parentli = $post->ID; ?>
					<?php $intCounter++; ?>
					<?php

						//Recursive function
						$intCounter = wp_custom_navigation_default_sub_items($post_id, $intCounter, $parentli, 'pages', 'default');

					 ?>

				</li>

				<?php

			}
		}
	}

	return $intCounter;
}

// Outputs All Categories and Sub Items
function wp_custom_nav_get_categories($counter, $type) {

	$category_args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => false,
			'include_last_update_time' => false,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'pad_counts'               => false );

	$intCounter = $counter;

	// Get all categories
	$categories_array = get_categories($category_args);

	if ( !$categories_array ) {
		echo 'Not Found';
		return $intCounter;
	}

	// Display Loop
	foreach ( $categories_array as $cat_item ) {
		if ( $cat_item->parent == 0 ) {
			// Custom Menu
			if ( $type == 'menu' ) {
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
					<dl>
					<dt>
						<span class="title"><?php echo esc_html($cat_item->cat_name); ?></span>
						<span class="controls">
						<span class="type">category</span>
						<a id="edit<?php echo $intCounter; ?>" onclick="edititem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>"><img class="edit" alt="<?php esc_attr_e('Edit Menu Item'); ?>" title="="<?php esc_attr_e('Edit Menu Item'); ?>" src="<?php echo admin_url('images/ico-edit.png'); ?>" /></a>
						<a id="remove<?php echo $intCounter; ?>" onclick="removeitem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>">
							<img class="remove" alt="="<?php esc_attr_e('Remove from Custom Menu'); ?>" title="="<?php esc_attr_e('Remove from Custom Menu'); ?>" src="<?php echo admin_url('images/ico-close.png'); ?>" />
						</a>
						<a target="_blank" href="<?php echo get_category_link($cat_item->cat_ID); ?>">
							<img alt="="<?php esc_attr_e('View Page'); ?>" title="="<?php esc_attr_e('View Page'); ?>" src="<?php echo admin_url('images/ico-viewpage.png'); ?>" />
						</a>
						</span>

					</dt>
					</dl>
					<a class="hide" href="<?php echo get_category_link($cat_item->cat_ID); ?>"><span class="title"><?php echo $cat_item->cat_name; ?></span>
					<?php
					$use_cats_raw = get_option('wp_settings_custom_nav_descriptions');
					$use_cats = strtolower($use_cats_raw);
					if ( $use_cats == 'yes' ) { ?>
					<br/> <span><?php echo $cat_item->category_description; ?></span>
					<?php } ?>
								</a>
					<input type="hidden" name="postmenu<?php echo $intCounter; ?>" id="postmenu<?php echo $intCounter; ?>" value="<?php echo $cat_item->cat_ID; ?>" />
					<input type="hidden" name="parent<?php echo $intCounter; ?>" id="parent<?php echo $intCounter; ?>" value="0" />
					<input type="hidden" name="title<?php echo $intCounter; ?>" id="title<?php echo $intCounter; ?>" value="<?php echo esc_attr($cat_item->cat_name); ?>" />
					<input type="hidden" name="linkurl<?php echo $intCounter; ?>" id="linkurl<?php echo $intCounter; ?>" value="<?php echo esc_attr(get_category_link($cat_item->cat_ID)); ?>" />
					<input type="hidden" name="description<?php echo $intCounter; ?>" id="description<?php echo $intCounter; ?>" value="<?php echo esc_attr($cat_item->category_description); ?>" />
					<input type="hidden" name="icon<?php echo $intCounter; ?>" id="icon<?php echo $intCounter; ?>" value="0" />
					<input type="hidden" name="position<?php echo $intCounter; ?>" id="position<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>" />
					<input type="hidden" name="linktype<?php echo $intCounter; ?>" id="linktype<?php echo $intCounter; ?>" value="category" />
					<input type="hidden" name="anchortitle<?php echo $intCounter; ?>" id="anchortitle<?php echo $intCounter; ?>" value="<?php echo esc_attr($cat_item->cat_name); ?>" />
					<input type="hidden" name="newwindow<?php echo $intCounter; ?>" id="newwindow<?php echo $intCounter; ?>" value="0" />

					<?php $parentli = $cat_item->cat_ID; ?>
					<?php $intCounter++; ?>
					<?php

						// Recursive function
						$intCounter = wp_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories','menu');

					?>

				</li>

				<?php
			} elseif ( $type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<dl>
					<dt>
					<?php
					$post_text = htmlentities($cat_item->cat_name);
					$post_url = get_category_link($cat_item->cat_ID);
					$post_id = $cat_item->cat_ID;
					$post_parent_id = $cat_item->parent;
					$description = htmlentities(strip_tags($cat_item->description));
					?>
					<?php $templatedir = get_bloginfo('url'); ?>
					<span class="title"><?php echo esc_html($cat_item->cat_name); ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','Category','<?php echo $post_text; ?>','<?php echo $post_url; ?>','<?php echo $post_id; ?>','<?php echo $post_parent_id ?>','<?php echo $description; ?>')" name="<?php echo $post_text; ?>" value="<?php echo $post_url;  ?>"><img alt="="<?php esc_attr_e('Add to Custom Menu'); ?>" title="="<?php esc_attr_e('Add to Custom Menu'); ?>"  src="<?php echo admin_url('images/ico-add.png'); ?>" /></a> </dt>
					</dl>
					<?php $parentli = $cat_item->cat_ID; ?>
					<?php $intCounter++; ?>
					<?php
						// Recursive function
						$intCounter = wp_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories', 'default');
					?>

				</li>

				<?php
			}
		}
	}

	return $intCounter;
}

//RECURSIVE Sub Menu Items of default categories and pages
function wp_custom_navigation_default_sub_items($childof, $intCounter, $parentli, $type, $output_type) {

	$counter = $intCounter;

	// Custom Menu
	if ( $output_type == 'menu' ) {
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	} elseif ( $output_type == 'default' ) {
		// Sidebar Menu
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	}

	if ( $type == 'categories' ) {
		// Get Sub Category Items
		$sub_array = get_categories($sub_args);
	} elseif ($type == 'pages') {
		// Get Sub Page Items
		$sub_array = get_pages($sub_args);
	} else {
		$sub_array = array();
	}


	if ( $sub_array ) {
		?>
		<ul id="sub-custom-nav-<?php echo $type ?>">

		<?php
		// Display Loop
		foreach ( $sub_array as $sub_item ) {
			// Prepare Menu Data
			if ( $type == 'categories' ) {
				// Category Menu Item
				$link = get_category_link($sub_item->cat_ID);
				$title = htmlentities($sub_item->cat_name);
				$parent_id = $sub_item->cat_ID;
				$itemid = $sub_item->cat_ID;
				$linktype = 'category';
				$appendtype = 'Category';
				$description = htmlentities(strip_tags($sub_item->description));
			} elseif ( $type == 'pages' ) {
				//Page Menu Item
				$link = get_permalink($sub_item->ID);
				$title = htmlentities($sub_item->post_title);
				$parent_id = $sub_item->ID;
				$linktype = 'page';
				$itemid = $sub_item->ID;
				$appendtype = 'Page';
				$description = htmlentities(get_post_meta($itemid, 'page-description', true));
			} else {
				// Custom Menu Item
				$title = '';
				$linktype = 'custom';
				$appendtype= 'Custom';
			}

			// Custom Menu
			if ( $output_type == 'menu' ) {
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<dl>
					<dt>
						<span class="title"><?php echo $title; ?></span>
							<span class="controls">
							<span class="type"><?php echo $linktype; ?></span>
							<a id="edit<?php echo $counter; ?>" onclick="edititem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="edit" alt="<?php esc_attr_e('Edit Menu Item'); ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" src="<?php echo admin_url('images/ico-edit.png'); ?>" /></a>
								<a id="remove<?php echo $counter; ?>" onclick="removeitem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>">
									<img class="remove" alt="<?php esc_attr_e('Remove from Custom Menu'); ?>" title="<?php esc_attr_e('Remove from Custom Menu'); ?>" src="<?php echo admin_url('images/ico-close.png'); ?>" />
								</a>
								<a target="_blank" href="<?php echo $link; ?>">
									<img alt="<?php esc_attr_e('View Page'); ?>" title="<?php esc_attr_e('View Page'); ?>" src="<?php echo admin_url('images/ico-viewpage.png'); ?>" />
								</a>
						</span>

					</dt>
					</dl>
					<a class="hide" href="<?php echo $link; ?>"><?php echo $title; ?></a>
					<input type="hidden" name="dbid<?php echo $counter; ?>" id="dbid<?php echo $counter; ?>" value="<?php echo $sub_item->id; ?>" />
					<input type="hidden" name="postmenu<?php echo $counter; ?>" id="postmenu<?php echo $counter; ?>" value="<?php echo $parent_id; ?>" />
					<input type="hidden" name="parent<?php echo $counter; ?>" id="parent<?php echo $counter; ?>" value="<?php echo $parentli; ?>" />
					<input type="hidden" name="title<?php echo $counter; ?>" id="title<?php echo $counter; ?>" value="<?php echo $title; ?>" />
					<input type="hidden" name="linkurl<?php echo $counter; ?>" id="linkurl<?php echo $counter; ?>" value="<?php echo $link; ?>" />
					<input type="hidden" name="description<?php echo $counter; ?>" id="description<?php echo $counter; ?>" value="<?php echo $description; ?>" />
					<input type="hidden" name="icon<?php echo $counter; ?>" id="icon<?php echo $counter; ?>" value="0" />
					<input type="hidden" name="position<?php echo $counter; ?>" id="position<?php echo $counter; ?>" value="<?php echo $counter; ?>" />
					<input type="hidden" name="linktype<?php echo $counter; ?>" id="linktype<?php echo $counter; ?>" value="<?php echo $linktype; ?>" />
					<input type="hidden" name="anchortitle<?php echo $counter; ?>" id="anchortitle<?php echo $counter; ?>" value="<?php echo $title; ?>" />
					<input type="hidden" name="newwindow<?php echo $counter; ?>" id="newwindow<?php echo $counter; ?>" value="0" />

					<?php $counter++; ?>
					<?php

						// Do recursion
						$counter = wp_custom_navigation_default_sub_items($parent_id, $counter, $parent_id, $type, 'menu');

					?>

				</li>
				<?php
			} elseif ($output_type == 'default') {
				// Sidebar Menu
				?>
				<li>
					<dl>
					<dt>

					<?php $templatedir = get_bloginfo('url'); ?>
					<span class="title"><?php echo $title; ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','<?php echo $appendtype; ?>','<?php echo $title; ?>','<?php echo $link; ?>','<?php echo $itemid; ?>','<?php echo $parent_id ?>','<?php echo $description; ?>')" name="<?php echo $title; ?>" value="<?php echo $link; ?>"><img alt="<?php esc_attr_e('Add to Custom Menu'); ?>" title="<?php esc_attr_e('Add to Custom Menu'); ?>" src="<?php echo admin_url('images/ico-add.png'); ?>" /></a> </dt>
					</dl>
					<?php

						// Do recursion
						$counter = wp_custom_navigation_default_sub_items($itemid, $counter, $parent_id, $type, 'default');

					?>
				</li>

				<?php
			}

		}
		?>

		</ul>

	<?php
	}

	return $counter;
}

/*-----------------------------------------------------------------------------------*/
/* Recursive get children */
/*-----------------------------------------------------------------------------------*/

function get_children_menu_elements($childof, $intCounter, $parentli, $type, $menu_id, $table_name) {
	global $wpdb;

	$counter = $intCounter;

	if ( $type == 'categories' ) {
		// Get Sub Category Items
		$sub_args = array(
			'child_of' => $childof,
			'hide_empty'  => false,
			'parent' => $childof);
		$sub_array = get_categories($sub_args);
	} elseif ($type == 'pages') {
		// Get Sub Page Items
		$sub_args = array(
			'child_of' => $childof,
			'parent' => $childof);

		$sub_array = get_pages($sub_args);

	} else {
		$sub_array = array();
	}

	if ( $sub_array ) {
		// DISPLAY Loop
		foreach ( $sub_array as $sub_item ) {
			if ( isset($sub_item->parent) )
				$sub_item_parent = $sub_item->parent;
			elseif (isset($sub_item->post_parent))
				$sub_item_parent = $sub_item->post_parent;

			// Is child
			if ( $sub_item_parent == $childof ) {
				// Prepare Menu Data
				// Category Menu Item
				if ( $type == 'categories' ) {
					$link = get_category_link($sub_item->cat_ID);
					$title = htmlentities($sub_item->cat_name);
					$parent_id = $sub_item->category_parent;
					$itemid = $sub_item->cat_ID;
					$linktype = 'category';
					$appendtype= 'Category';
				}
				// Page Menu Item
				elseif ( $type == 'pages' ) {
					$link = get_permalink($sub_item->ID);
					$title = htmlentities($sub_item->post_title);
					$parent_id = $sub_item->post_parent;
					$linktype = 'page';
					$itemid = $sub_item->ID;
					$appendtype= 'Page';
				}
				// Custom Menu Item
				else {
					$title = '';
					$linktype = 'custom';
					$appendtype= 'Custom';
				}

				// CHECK for existing parent records
				// echo $parent_id;
				$wp_result = $wpdb->get_results("SELECT id FROM ".$table_name." WHERE post_id='".$parent_id."' AND link_type='".$linktype."' AND menu_id='".$menu_id."'");
				if ( $wp_result > 0 && isset($wp_result[0]->id) )
					$parent_id = $wp_result[0]->id;

				//INSERT item
				$insert = "INSERT INTO ".$table_name." (position,post_id,parent_id,custom_title,custom_link,custom_description,menu_icon,link_type,menu_id,custom_anchor_title) "."VALUES ('".$counter."','".$itemid."','".$parent_id."','".$title."','".$link."','','','".$linktype."','".$menu_id."','".$title."')";
	  			$results = $wpdb->query( $insert );

	  			$counter++;
	  			$counter = get_children_menu_elements($itemid, $counter, $parent_id, $type, $menu_id, $table_name);
			}
		}
	}
	return $counter;
}

?>
