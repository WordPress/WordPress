<?php

/*-----------------------------------------------------------------------------------*/
/* Custom Navigation Functions */
/* wp_custom_navigation_output() displays the menu in the back/frontend
/* wp_custom_nav_get_pages()
/* wp_custom_nav_get_categories()
/* wp_custom_navigation_default_sub_items() is a recursive sub menu item function
/*-----------------------------------------------------------------------------------*/

// Outputs All Pages and Sub Items
function wp_nav_menu_get_pages($counter, $type) {

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
			$post = wp_setup_nav_menu_item($post, 'page', $intCounter);
			if ( $type == 'menu' ) {
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
					<?php
						wp_print_nav_menu_item($post, 'menu', $intCounter);
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
						wp_print_nav_menu_item($post, 'default');
						$parentli = $post->ID;
						$intCounter++;
						$intCounter = wp_nav_menu_sub_items($post->ID, $intCounter, $parentli, 'pages', 'default');
					 ?>
				</li>

				<?php
			}
		}
	}

	return $intCounter;
}

// Outputs All Categories and Sub Items
function wp_nav_menu_get_categories($counter, $type) {

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
			$cat_item = wp_setup_nav_menu_item($cat_item, 'category', $intCounter);
			// Custom Menu
			if ( $type == 'menu' ) {
				?>

				<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
					<?php
						wp_print_nav_menu_item($cat_item, 'menu');
						$parentli = $cat_item->cat_ID;
						$intCounter++;
						$intCounter = wp_nav_menu_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories', 'menu');
					?>

				</li>

				<?php
			} elseif ( $type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						wp_print_nav_menu_item($cat_item, 'default');
						$parentli = $cat_item->cat_ID;
						$intCounter++;
						$intCounter = wp_nav_menu_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories', 'default');
					?>

				</li>

				<?php
			}
		}
	}

	return $intCounter;
}

//RECURSIVE Sub Menu Items of default categories and pages
function wp_nav_menu_sub_items($childof, $intCounter, $parentli, $type, $output_type) {

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
			$sub_item = wp_setup_nav_menu_item($sub_item, $item_type, $counter);

			if ( $output_type == 'menu' ) {
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<?php
						wp_print_nav_menu_item($sub_item, 'menu');
						$counter++;
						$counter = wp_nav_menu_sub_items($sub_item->ID, $counter, $sub_item->ID, $type, 'menu');
					?>

				</li>
				<?php
			} elseif ( $output_type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						wp_print_nav_menu_item($sub_item, 'default');
						//$counter++;
						$counter = wp_nav_menu_sub_items($sub_item->ID, $counter, $sub_item->ID, $type, 'default');
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

function wp_nav_menu_setup($override = false) {
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

?>