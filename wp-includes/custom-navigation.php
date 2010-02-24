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

function wp_custom_navigation_setup($override = false) {
	// Custom Navigation Menu Setup

	// Override for menu descriptions
	update_option('wp_settings_custom_nav_advanced_options', 'yes');

	$menus = wp_get_nav_menus();
 	if ( !empty( $menus ) ) {
		foreach ( $menus as $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}
	}

	wp_create_nav_menu( __('Main') );
}

function wp_delete_nav_menu( $menu_id ) {
	$menu_id = (int) $menu_id;
	if ( !$menu_id  )
		return false;

	$menu_objects = get_objects_in_term( $menu_id, 'nav_menu' );
	if ( !empty( $menu_objects ) ) {
		foreach ( $menu_objects as $item ) {
			wp_delete_post( $item );
		}
	}
	wp_delete_term( $menu_id, 'nav_menu' );
}

function wp_create_nav_menu( $menu_name ) {
	$menu_exists = get_term_by( 'name', $menu_name, 'nav_menu' );

	if ( $menu_exists )
		return WP_Error('menu_exists', sprintf( __('A menu named "%s" already exists; please try another name.'), $menu_exists->name ));

	return wp_insert_term( $menu_name, 'nav_menu' );
}

function wp_get_nav_menus() {
	return get_terms( 'nav_menu', array( 'hide_empty' => false ) );
}

function wp_get_nav_menu_items( $menu, $args = array() ) {
	$items = get_objects_in_term( (int) $menu, 'nav_menu' );

	if ( ! empty( $items ) ) {
		$defaults = array( 'orderby' => 'menu_order', 'post_type' => 'nav_menu_item', 'post_status' => 'publish', 'output' => ARRAY_A, 'output_key' => 'menu_order' );
		$args = wp_parse_args($args, $defaults);
		if ( count( $items ) > 1 )	
			$args['include'] = implode( ',', $items );
		else
			$args['include'] = $items[0];

		$items = get_posts( $args );

		if ( ARRAY_A == $args['output'] ) {
			$output = array();
			foreach ( $items as $item ) {
				$output[$item->$args['output_key']] = $item;
			}
			unset($items);
			ksort($output);
			return $output;
		}
	}
	return $items;
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
		$menu_item->ID = $menu_item->term_id;
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
			if ( empty($menu_item->guid) )
				$menu_item->link = get_category_link( $menu_item->object_id );
			else
				$menu_item->link = $menu_item->guid;

			if ( empty($menu_item->post_title) ) {
				$title_raw = get_category( $menu_item->object_id );
				$menu_item->title =  htmlentities($title_raw->cat_name);
			} else {
				$menu_item->title = htmlentities( $menu_item->post_title );
			}

			if ( empty($menu_item->post_content) )
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

	$menu_item->anchor_title = '';
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

function output_menu_item($menu_item, $context, $args = array() ) {
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
			$menu_item = setup_menu_item($menu_item);
			// List Items
			?><li id="menu-<?php echo $menu_item->ID; ?>" value="<?php echo $menu_item->ID; ?>" <?php echo $menu_item->li_class; ?>><?php
			output_menu_item($menu_item, $type, $args);
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
		if ( $post->post_parent == 0 ) {
			$post = setup_menu_item($post, 'page', $intCounter);
			if ( $type == 'menu' ) {
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
					<?php
						output_menu_item($post, 'menu', $intCounter);
						$parentli = $post->ID;
						$intCounter++;
						$intCounter = wp_custom_navigation_default_sub_items($post->ID, $intCounter, $parentli, 'pages', 'menu');
					?>
				</li>

				<?php
			} elseif ( $type == 'default' ) {
				// Sidebar Menu
				?>
				 <li>
					<?php
						output_menu_item($post, 'default');
						$parentli = $post->ID;
						$intCounter++;
						$intCounter = wp_custom_navigation_default_sub_items($post->ID, $intCounter, $parentli, 'pages', 'default');
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
		_e('Not Found');
		return $intCounter;
	}

	// Display Loop
	foreach ( $categories_array as $cat_item ) {
		if ( $cat_item->parent == 0 ) {
			$cat_item = setup_menu_item($cat_item, 'category', $intCounter);
			// Custom Menu
			if ( $type == 'menu' ) {
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
					<?php
						output_menu_item($cat_item, 'menu');
						$parentli = $cat_item->cat_ID;
						$intCounter++;
						$intCounter = wp_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories', 'menu');
					?>

				</li>

				<?php
			} elseif ( $type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						output_menu_item($cat_item, 'default');
						$parentli = $cat_item->cat_ID;
						$intCounter++;
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
		$item_type = 'category';
		$sub_array = get_categories($sub_args);
	} elseif ($type == 'pages') {
		// Get Sub Page Items
		$item_type = 'page';
		$sub_array = get_pages($sub_args);
	} else {
		$item_type = 'custom';
		$sub_array = array();
	}


	if ( $sub_array ) {
		?>
		<ul id="sub-custom-nav-<?php echo $type ?>">

		<?php
		// Display Loop
		foreach ( $sub_array as $sub_item ) {
			$sub_item = setup_menu_item($sub_item, $item_type, $counter);

			if ( $output_type == 'menu' ) {
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<?php
						output_menu_item($sub_item, 'menu');
						$counter++;
						$counter = wp_custom_navigation_default_sub_items($sub_item->ID, $counter, $sub_item->ID, $type, 'menu');
					?>

				</li>
				<?php
			} elseif ( $output_type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						output_menu_item($sub_item, 'default');
						//$counter++;
						$counter = wp_custom_navigation_default_sub_items($sub_item->ID, $counter, $sub_item->ID, $type, 'default');
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

?>
