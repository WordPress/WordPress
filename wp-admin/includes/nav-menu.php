<?php

/**
 * Displays a list of links and thier sub items.
 *
 * @since 3.0.0
 *
 * @param string $counter 
 * @param string $context 
 */
function wp_nav_menu_get_custom_links( $counter, $context ) {
	$available_links = new WP_Query(  );
	
	$args = array( 'post_status' => 'any', 'post_type' => 'nav_menu_item', 'meta_value' => 'custom' );
	$link_objects = new WP_Query( $args );
	
	$items_counter = $counter;

	if ( !$link_objects->posts ) {
		_e('Not Found');
		return $items_counter;
	}
	
	// Display Loop
	foreach ( $link_objects->posts as $item ) {
		if ( 0 == $item->parent ) {
			$item = wp_setup_nav_menu_item( $item, 'item', $items_counter );
			
			switch ( $context ) {
				case 'menu':
					?>
					<li id="menu-<?php echo $items_counter; ?>" value="<?php echo $items_counter; ?>">
						<?php
							echo wp_get_nav_menu_item( $item, 'menu' );
							$parentli = $item->ID;
							$items_counter++;
							$items_counter = wp_nav_menu_sub_items( $item->ID, $items_counter, $parentli, 'categories', 'menu' );
						?>
					</li>
					<?php
					break;
				
				case 'default':
					?>
					<li>
						<?php
							echo wp_get_nav_menu_item( $item, 'default' );
							$parentli = $item->ID;
							$items_counter++;
							$items_counter = wp_nav_menu_sub_items( $item->ID, $items_counter, $parentli, 'categories', 'default' );
						?>
					</li>
					<?php
					break;
			}
		}
	}
	return $items_counter;
}

/**
 * Displays a list of pages and thier sub items.
 *
 * @since 3.0.0
 *
 * @param string $counter 
 * @param string $context 
 */
function wp_nav_menu_get_pages( $counter, $context ) {
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

	// Get all pages
	$pages_array = get_pages( $pages_args );

	$items_counter = $counter;
	$parentli = $items_counter;

	if ( !$pages_array ) {
		echo __('Not Found');
		return $items_counter;
	}

	// Display Loop
	foreach ( $pages_array as $post ) {
		if ( $post->post_parent == 0 ) {
			$post = wp_setup_nav_menu_item( $post, 'page', $items_counter );
			if ( $context == 'menu' ) {
				?>
				<li id="menu-<?php echo $items_counter; ?>" value="<?php echo $items_counter; ?>">
					<?php
						echo wp_get_nav_menu_item( $post, 'menu', $items_counter );
						$parentli = $post->ID;
						$items_counter++;
						$items_counter = wp_nav_menu_sub_items( $post->ID, $items_counter, $parentli, 'pages', 'menu' );
					?>
				</li>
				<?php
			} elseif ( $context == 'default' ) {
				// Sidebar Menu
				?>
				 <li>
					<?php
						echo wp_get_nav_menu_item( $post, 'default' );
						$parentli = $post->ID;
						$items_counter++;
						$items_counter = wp_nav_menu_sub_items( $post->ID, $items_counter, $parentli, 'pages', 'default' );
					 ?>
				</li>
				<?php
			}
		}
	}
	return $items_counter;
}

/**
 * Displays a list of categories and thier sub items.
 *
 * @since 3.0.0
 *
 * @param string $counter 
 * @param string $context 
 */
function wp_nav_menu_get_categories( $counter, $context ) {
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

	$items_counter = $counter;

	// Get all categories
	$categories_array = get_categories( $category_args );

	if ( !$categories_array ) {
		_e('Not Found');
		return $items_counter;
	}

	// Display Loop
	foreach ( $categories_array as $cat_item ) {
		if ( $cat_item->parent == 0 ) {
			$cat_item = wp_setup_nav_menu_item( $cat_item, 'category', $items_counter );
			// Custom Menu
			if ( $context == 'menu' ) {
				?>
				<li id="menu-<?php echo $items_counter; ?>" value="<?php echo $items_counter; ?>">
					<?php
						echo wp_get_nav_menu_item($cat_item, 'menu');
						$parentli = $cat_item->cat_ID;
						$items_counter++;
						$items_counter = wp_nav_menu_sub_items( $cat_item->cat_ID, $items_counter, $parentli, 'categories', 'menu' );
					?>
				</li>
				<?php
			} elseif ( $context == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						echo wp_get_nav_menu_item( $cat_item, 'default' );
						$parentli = $cat_item->cat_ID;
						$items_counter++;
						$items_counter = wp_nav_menu_sub_items( $cat_item->cat_ID, $items_counter, $parentli, 'categories', 'default' );
					?>
				</li>
				<?php
			}
		}
	}
	return $items_counter;
}

/**
 * Recursive function that gets sub menu items.
 *
 * @since 3.0.0
 *
 * @param string $childof 
 * @param string $items_counter 
 * @param string $parentli 
 * @param string $context 
 * @param string $output_type 
 */
function wp_nav_menu_sub_items( $childof, $items_counter, $parentli, $context, $output_type ) {
	$counter = $items_counter;

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

	if ( $context == 'categories' ) {
		// Get Sub Category Items
		$item_type = 'category';
		$sub_array = get_categories($sub_args);
	} elseif ($context == 'pages') {
		// Get Sub Page Items
		$item_type = 'page';
		$sub_array = get_pages($sub_args);
	} else {
		$item_type = 'custom';
		$sub_array = array();
	}

	if ( $sub_array ) {
		?>
		<ul id="sub-menu-<?php echo $context ?>">
		<?php
		// Display Loop
		foreach ( $sub_array as $sub_item ) {
			$sub_item = wp_setup_nav_menu_item( $sub_item, $item_type, $counter );
			if ( $output_type == 'menu' ) {
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<?php
						echo wp_get_nav_menu_item( $sub_item, 'menu' );
						$counter++;
						$counter = wp_nav_menu_sub_items( $sub_item->ID, $counter, $sub_item->ID, $context, 'menu' );
					?>
				</li>
				<?php
			} elseif ( $output_type == 'default' ) {
				// Sidebar Menu
				?>
				<li>
					<?php
						echo wp_get_nav_menu_item( $sub_item, 'default' );
						//$counter++;
						$counter = wp_nav_menu_sub_items( $sub_item->ID, $counter, $sub_item->ID, $context, 'default' );
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