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

	$nav_version = '1.1.0';
	//Custom Navigation Menu Setup

	//Check for Upgrades
	if (get_option('wp_settings_custom_nav_version') <> '') {
		$nav_version_in_db = get_option('wp_settings_custom_nav_version');
	}
	else {
		$nav_version_in_db = '0';
	}

	//Override for menu descriptions
	update_option('wp_settings_custom_nav_advanced_options','yes');

	if(($nav_version_in_db <> $nav_version) || ($override))
		update_option('wp_settings_custom_nav_version',$nav_version);

	$custom_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
 	if ( !empty( $custom_menus ) ) {
		foreach( $custom_menus as $menu ) {
			$menu_objects = get_objects_in_term( $menu->term_id, 'nav_menu' );
			if ( !empty( $menu_objects ) ) {
				foreach( $menu_objects as $item )
					wp_delete_post( $item );
			}
			wp_delete_term( $menu->term_id, 'nav_menu' );
		}
	}

}

/*-----------------------------------------------------------------------------------*/
/* Custom Navigation Functions */
/* wp_custom_navigation_output() displays the menu in the back/frontend
/* wp_custom_navigation_sub_items() is a recursive sub menu item function
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

function wp_custom_navigation_output($args = array()) {

		//DEFAULT ARGS
		$type = 'frontend';
		$name = 'Menu 1';
		$id = 0;
		$desc = 2;
		$before_title = '';
		$after_title = '';

		if (isset($args)) {

			if ( !is_array($args) )
			parse_str( $args, $args );

			extract($args);
		}

		$menu_objects = get_objects_in_term( $id, 'nav_menu' );
		$menu_items = wp_custom_navigation_get_menu_items( $menu_objects, 'menu_order' );
		//Override for menu descriptions
		$advanced_option_descriptions = get_option('wp_settings_custom_nav_advanced_options');
		if ( $advanced_option_descriptions == 'no' )
			$desc = 2;

		$queried_id = 0;
		global $wp_query;
		if ( is_page() )
			$queried_id = $wp_query->get_queried_object_id();
		elseif ( is_category() )
			$queried_id = $wp_query->get_queried_object_id();

		$parent_stack = array();
		$current_parent = 0;
		$parent_menu_order = array();
	    // Display Loop
		foreach ( $menu_items as $key => $menu_item ) {
			$menu_type = get_post_meta($menu_item->ID, 'menu_type', true);
			$object_id = get_post_meta($menu_item->ID, 'object_id', true);
			$parent_menu_order[ $menu_item->ID ] = $menu_item->menu_order;
			if ( isset( $parent_menu_order[ $menu_item->post_parent ] ) )
				$parent_item = $parent_menu_order[ $menu_item->post_parent ];
			else
				$parent_item = 0;

			switch ( $menu_type ) {
				// Page Menu Item
				case 'page':
					if ( $menu_item->guid == '' )
						$link = get_permalink( $object_id );
					else
						$link = $menu_item->guid;

					if ( $menu_item->post_title == '' )
						$title = htmlentities( get_the_title( $object_id ) );
					else
						$title = htmlentities( $menu_item->post_title );

					if ( $menu_item->post_content == '' )
						$description = htmlentities( get_post_meta( $menu_item->ID, 'page-description', true ) );
					else
						$description = htmlentities( $menu_item->post_content );
					$target = '';
				break;
				// Category Menu Item
				case 'category':
					if ( $menu_item->guid == '' )
						$link = get_category_link( $object_id );
					else
						$link = $menu_item->guid;

					if ( $menu_item->post_title == '' ) {
						$title_raw = get_categories( array('include' => $object_id) );
						$title =  htmlentities($title_raw[0]->cat_name);
					} else {
						$title = htmlentities( $menu_item->post_title );
					}

					if ( $menu_item->post_content == '' )
						$description = htmlentities( strip_tags( category_description( $object_id ) ) );
					else
						$description = htmlentities( $menu_item->post_content );
					$target = '';
				break;
				default:
					// Custom Menu Item
					$link = $menu_item->guid;
					$title =  htmlentities( $menu_item->post_title );
					$description = htmlentities( $menu_item->post_content );
					$target = 'target="_blank"';
				break;
			}

			$li_class = '';
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
			// List Items
			?><li id="menu-<?php echo $menu_item->ID; ?>" value="<?php echo $menu_item->ID; ?>" <?php echo $li_class; ?>><?php
					//@todo: update front end to use post data
					//FRONTEND Link
					if ( $type == 'frontend' ) {
						?><a title="<?php echo $anchor_title; ?>" href="<?php echo $link; ?>" <?php echo $target; ?>><?php echo $before_title.$title.$after_title; ?><?php

							if ( $advanced_option_descriptions == 'no' ) {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if (($desc == 1) || ($desc == 0) )
								{
									?><span class="nav-description"><?php echo $description; ?></span><?php
								}
								elseif ($desc == 2)
								{ }
								else
								{ }
							} else {
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ( $desc == 1 ) {
									?><span class="nav-description"><?php echo $description; ?></span><?php
								}
								elseif (($desc == 2) || ($desc == 0))
								{ }
								else
								{ }
							}

						?></a><?php
					} elseif ( $type == 'backend' ) {
						//BACKEND draggable and droppable elements
						$link_type = $menu_type;
						?>

						<dl>
							<dt>
								<span class="title"><?php echo $title; ?></span>
								<span class="controls">
								<span class="type"><?php echo $link_type; ?></span>
								<a id="edit<?php echo $menu_item->menu_order; ?>" onclick="edititem(<?php echo $menu_item->menu_order; ?>)" value="<?php echo $menu_item->menu_order; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-edit.png" /></a>
								<a id="remove<?php echo $menu_item->menu_order; ?>" onclick="removeitem(<?php echo $menu_item->menu_order; ?>)" value="<?php echo $menu_item->menu_order; ?>"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-close.png" /></a>
								<a id="view<?php echo $menu_item->menu_order; ?>" target="_blank" href="<?php echo $link; ?>"><img alt="View Page" title="View Page" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-viewpage.png" /></a>
								</span>
							</dt>
						</dl>

						<a><span class=""></span></a>
						<input type="hidden" name="dbid<?php echo $menu_item->menu_order; ?>" id="dbid<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->ID; ?>" />
						<input type="hidden" name="postmenu<?php echo $menu_item->menu_order; ?>" id="postmenu<?php echo $menu_item->menu_order; ?>" value="<?php echo $id; ?>" />
						<input type="hidden" name="parent<?php echo $menu_item->menu_order; ?>" id="parent<?php echo $menu_item->menu_order; ?>" value="<?php echo $parent_item; ?>" />
						<input type="hidden" name="title<?php echo $menu_item->menu_order; ?>" id="title<?php echo $menu_item->menu_order; ?>" value="<?php echo $title; ?>" />
						<input type="hidden" name="linkurl<?php echo $menu_item->menu_order; ?>" id="linkurl<?php echo $menu_item->menu_order; ?>" value="<?php echo $link; ?>" />
						<input type="hidden" name="description<?php echo $menu_item->menu_order; ?>" id="description<?php echo $menu_item->menu_order; ?>" value="<?php echo $description; ?>" />
						<input type="hidden" name="icon<?php echo $menu_item->menu_order; ?>" id="icon<?php echo $menu_item->menu_order; ?>" value="0" />
						<input type="hidden" name="position<?php echo $menu_item->menu_order; ?>" id="position<?php echo $menu_item->menu_order; ?>" value="<?php echo $menu_item->menu_order; ?>" />
						<input type="hidden" name="linktype<?php echo $menu_item->menu_order; ?>" id="linktype<?php echo $menu_item->menu_order; ?>" value="<?php echo $link_type; ?>" />
						<input type="hidden" name="anchortitle<?php echo $menu_item->menu_order; ?>" id="anchortitle<?php echo $menu_item->menu_order; ?>" value="<?php echo esc_html( $menu_item->post_excerpt ); ?>" />
						<input type="hidden" name="newwindow<?php echo $menu_item->menu_order; ?>" id="newwindow<?php echo $menu_item->menu_order; ?>" value="<?php echo ( '' == $menu_item->post_content_filtered ? '0' : '1' ); ?>" />

						<?php
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
//@todo: implement menu heirarchy
//RECURSIVE Sub Menu Items
function wp_custom_navigation_sub_items($post_id,$type,$table_name,$output_type,$menu_id = 0) {

	$parent_id = 0;
	global $wpdb;

	//GET sub menu items
	$wp_custom_nav_menu = $wpdb->get_results("SELECT id,post_id,parent_id,position,custom_title,custom_link,custom_description,menu_icon,link_type,custom_anchor_title,new_window FROM ".$table_name." WHERE parent_id = '".$post_id."' AND menu_id='".$menu_id."' ORDER BY position ASC");

	if (empty($wp_custom_nav_menu))
	{

	}
	else
	{
		?><ul id="sub-custom-nav">
		<?php
    	$queried_id = 0;
		global $wp_query;
        if (is_page()) {
	    	$queried_id = $wp_query->post->ID;
	    }
	    elseif (is_category()) {
	    	$queried_id = $wp_query->query_vars['cat'];
	    }
	    else {

	    }
	    //DISPLAY Loop
		foreach ($wp_custom_nav_menu as $sub_item)
		{
			//Figure out where the menu item sits
			$counter=$sub_item->position;

			//Prepare Menu Data
			//Category Menu Item
			if ($sub_item->link_type == 'category')
			{

				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;

				if ($sub_item->custom_link == '') {
					$link = get_category_link($sub_item->post_id);
				}
				else {
					$link = $sub_item->custom_link;
				}

				if ($sub_item->custom_title == '') {
					$title_raw = get_categories('include='.$sub_item->post_id);
					$title =  htmlentities($title_raw[0]->cat_name);
				}
				else {
					$title = htmlentities($sub_item->custom_title);
				}

				if ($sub_item->custom_description == '') {
					$description = strip_tags(category_description($sub_item->post_id));
				}
				else {
					$description = $sub_item->custom_description;
				}
				$target = '';
			}
			//Page Menu Item
			elseif ($sub_item->link_type == 'page')
			{

				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;

				if ($sub_item->custom_link == '') {
					$link = get_permalink($sub_item->post_id);
				}
				else {
					$link = $sub_item->custom_link;
				}

				if ($sub_item->custom_title == '') {
					$title = htmlentities(get_the_title($sub_item->post_id));
				}
				else {
					$title = htmlentities($sub_item->custom_title);
				}

				if ($sub_item->custom_description == '') {
					$description = get_post_meta($sub_item->post_id, 'page-description', true);
				}
				else {
					$description = $sub_item->custom_description;
				}
				$target = '';

			}
			//Custom Menu Item
			else
			{
				$link = $sub_item->custom_link;
				$title = htmlentities($sub_item->custom_title);
				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;
				$description = $sub_item->custom_description;
				$target = 'target="_blank"';
			}
			if ($queried_id == $sub_item->post_id) {
				$li_class = 'class="current_page_item"';
			}
			else {
				$li_class = '';
			}

			//SET anchor title
			if (isset($sub_item->custom_anchor_title)) {
				$anchor_title = htmlentities($sub_item->custom_anchor_title);
			}
			else {
				$anchor_title = $title;
			}

			if (isset($sub_item->new_window)) {
				if ($sub_item->new_window > 0) {
					$target = 'target="_blank"';
				}
			}

			//List Items
			?><li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>" <?php echo $li_class; ?>><?php
						//FRONTEND
						if ($output_type == "frontend")
						{
							?><a title="<?php echo $anchor_title; ?>" href="<?php echo $link; ?>" <?php echo $target; ?>><?php echo $title; ?></a><?php
						}
						//BACKEND
						elseif ($output_type == "backend")
						{
							?>
							<dl>
							<dt>
								<span class="title"><?php echo $title; ?></span>
								<span class="controls">
								<span class="type"><?php echo $sub_item->link_type; ?></span>
								<a id="edit<?php echo $counter; ?>" onclick="edititem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-edit.png" /></a>
								<a id="remove<?php echo $counter; ?>" onclick="removeitem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-close.png" /></a>
								<a id="view<?php echo $counter; ?>" target="_blank" href="<?php echo $link; ?>"><img alt="View Page" title="View Page" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-viewpage.png" /></a>
								</span>
							</dt>
							</dl>
							<a class="hide" href="<?php echo $link; ?>"><?php echo $title; ?></a>
							<input type="hidden" name="dbid<?php echo $counter; ?>" id="dbid<?php echo $counter; ?>" value="<?php echo $sub_item->id; ?>" />
							<input type="hidden" name="postmenu<?php echo $counter; ?>" id="postmenu<?php echo $counter; ?>" value="<?php echo $post_id; ?>" />
							<input type="hidden" name="parent<?php echo $counter; ?>" id="parent<?php echo $counter; ?>" value="<?php echo $parent_id; ?>" />
							<input type="hidden" name="title<?php echo $counter; ?>" id="title<?php echo $counter; ?>" value="<?php echo $title; ?>" />
							<input type="hidden" name="linkurl<?php echo $counter; ?>" id="linkurl<?php echo $counter; ?>" value="<?php echo $link; ?>" />
							<input type="hidden" name="description<?php echo $counter; ?>" id="description<?php echo $counter; ?>" value="<?php echo $description; ?>" />
							<input type="hidden" name="icon<?php echo $counter; ?>" id="icon<?php echo $counter; ?>" value="0" />
							<input type="hidden" name="position<?php echo $counter; ?>" id="position<?php echo $counter; ?>" value="<?php echo $counter; ?>" />
							<input type="hidden" name="linktype<?php echo $counter; ?>" id="linktype<?php echo $counter; ?>" value="<?php echo $sub_item->link_type; ?>" />
							<input type="hidden" name="anchortitle<?php echo $counter; ?>" id="anchortitle<?php echo $counter; ?>" value="<?php echo $anchor_title; ?>" />
							<input type="hidden" name="newwindow<?php echo $counter; ?>" id="newwindow<?php echo $counter; ?>" value="<?php echo $sub_item->new_window; ?>" />
							<?php
						}

						//Do recursion
						wp_custom_navigation_sub_items($sub_item->id,$sub_item->link_type,$table_name,$output_type,$menu_id);
			?></li>
			<?php

		}

	?></ul>
	<?php

	}

	return $parent_id;

}


//Outputs All Pages and Sub Items
function wp_custom_nav_get_pages($counter,$type) {

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

	if ($pages_array)
	{
		//DISPLAY Loop
		foreach ($pages_array as $post)
		{

			if ($post->post_parent == 0)
			{
				//Custom Menu
				if ($type == 'menu')
				{
					$description = get_post_meta($post->ID, 'page-description', true);
					?>

					<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">

						<dl>
						<dt>
						<span class="title"><?php echo $post->post_title; ?></span>
						<span class="controls">
							<span class="type">page</span>
							<a id="edit<?php echo $intCounter; ?>" onclick="edititem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-edit.png" /></a>
							<a id="remove<?php echo $intCounter; ?>" onclick="removeitem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>">
								<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-close.png" />
							</a>
							<a target="_blank" href="<?php echo get_permalink($post->ID); ?>">
								<img alt="View Page" title="View Page" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-viewpage.png" />
							</a>
						</span>

						</dt>
						</dl>
						<a class="hide" href="<?php echo get_permalink($post->ID); ?>"><span class="title"><?php echo $post->post_title; ?></span>
		    	    	</a>
		    	    	<input type="hidden" name="postmenu<?php echo $intCounter; ?>" id="postmenu<?php echo $intCounter; ?>" value="<?php echo $post->ID; ?>" />
						<input type="hidden" name="parent<?php echo $intCounter; ?>" id="parent<?php echo $intCounter; ?>" value="0" />
						<input type="hidden" name="title<?php echo $intCounter; ?>" id="title<?php echo $intCounter; ?>" value="<?php echo htmlentities($post->post_title); ?>" />
						<input type="hidden" name="linkurl<?php echo $intCounter; ?>" id="linkurl<?php echo $intCounter; ?>" value="<?php echo get_permalink($post->ID); ?>" />
						<input type="hidden" name="description<?php echo $intCounter; ?>" id="description<?php echo $intCounter; ?>" value="<?php echo $description; ?>" />
						<input type="hidden" name="icon<?php echo $intCounter; ?>" id="icon<?php echo $intCounter; ?>" value="0" />
						<input type="hidden" name="position<?php echo $intCounter; ?>" id="position<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>" />
						<input type="hidden" name="linktype<?php echo $intCounter; ?>" id="linktype<?php echo $intCounter; ?>" value="page" />
						<input type="hidden" name="anchortitle<?php echo $intCounter; ?>" id="anchortitle<?php echo $intCounter; ?>" value="<?php echo htmlentities($post->post_title); ?>" />
						<input type="hidden" name="newwindow<?php echo $intCounter; ?>" id="newwindow<?php echo $intCounter; ?>" value="0" />

						<?php $parentli = $post->ID; ?>
						<?php $intCounter++; ?>
						<?php

							//Recursive function
							$intCounter = wp_custom_navigation_default_sub_items($post->ID, $intCounter, $parentli, 'pages', 'menu');

						?>

					</li>

					<?php

				}
				//Sidebar Menu
				elseif ($type == 'default')
				{
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
				else
				{

				}
			}
		}
	}
	else
	{
		echo 'Not Found';
	}

	return $intCounter;
}

//Outputs All Categories and Sub Items
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

	//GET all categories
	$categories_array = get_categories($category_args);

	if ($categories_array)
	{
		//DISPLAY Loop
		foreach ($categories_array as $cat_item)
		{

			if ($cat_item->parent == 0)
			{
				//Custom Menu
				if ($type == 'menu')
				{
					?>

			    	<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
			    		<dl>
			            <dt>
			            	<span class="title"><?php echo $cat_item->cat_name; ?></span>
							<span class="controls">
							<span class="type">category</span>
							<a id="edit<?php echo $intCounter; ?>" onclick="edititem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-edit.png" /></a>
							<a id="remove<?php echo $intCounter; ?>" onclick="removeitem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>">
								<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-close.png" />
							</a>
							<a target="_blank" href="<?php echo get_category_link($cat_item->cat_ID); ?>">
								<img alt="View Page" title="View Page" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-viewpage.png" />
							</a>
							</span>

			            </dt>
			            </dl>
			            <a class="hide" href="<?php echo get_category_link($cat_item->cat_ID); ?>"><span class="title"><?php echo $cat_item->cat_name; ?></span>
			            <?php
			            $use_cats_raw = get_option('wp_settings_custom_nav_descriptions');
			   			$use_cats = strtolower($use_cats_raw);
			   			if ($use_cats == 'yes') { ?>
			            <br/> <span><?php echo $cat_item->category_description; ?></span>
			            <?php } ?>
			                    	</a>
			            <input type="hidden" name="postmenu<?php echo $intCounter; ?>" id="postmenu<?php echo $intCounter; ?>" value="<?php echo $cat_item->cat_ID; ?>" />
			            <input type="hidden" name="parent<?php echo $intCounter; ?>" id="parent<?php echo $intCounter; ?>" value="0" />
			            <input type="hidden" name="title<?php echo $intCounter; ?>" id="title<?php echo $intCounter; ?>" value="<?php echo htmlentities($cat_item->cat_name); ?>" />
						<input type="hidden" name="linkurl<?php echo $intCounter; ?>" id="linkurl<?php echo $intCounter; ?>" value="<?php echo get_category_link($cat_item->cat_ID); ?>" />
						<input type="hidden" name="description<?php echo $intCounter; ?>" id="description<?php echo $intCounter; ?>" value="<?php echo htmlentities($cat_item->category_description); ?>" />
						<input type="hidden" name="icon<?php echo $intCounter; ?>" id="icon<?php echo $intCounter; ?>" value="0" />
						<input type="hidden" name="position<?php echo $intCounter; ?>" id="position<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>" />
						<input type="hidden" name="linktype<?php echo $intCounter; ?>" id="linktype<?php echo $intCounter; ?>" value="category" />
						<input type="hidden" name="anchortitle<?php echo $intCounter; ?>" id="anchortitle<?php echo $intCounter; ?>" value="<?php echo htmlentities($cat_item->cat_name); ?>" />
						<input type="hidden" name="newwindow<?php echo $intCounter; ?>" id="newwindow<?php echo $intCounter; ?>" value="0" />

			            <?php $parentli = $cat_item->cat_ID; ?>
			            <?php $intCounter++; ?>
			           	<?php

							//Recursive function
							$intCounter = wp_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories','menu');

						?>

			    	</li>

			    	<?php
			    }
			    //Sidebar Menu
			    elseif ($type == 'default')
			    {
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
						<span class="title"><?php echo $cat_item->cat_name; ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','Category','<?php echo $post_text; ?>','<?php echo $post_url; ?>','<?php echo $post_id; ?>','<?php echo $post_parent_id ?>','<?php echo $description; ?>')" name="<?php echo $post_text; ?>" value="<?php echo $post_url;  ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu"  src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-add.png" /></a> </dt>
						</dl>
						<?php $parentli = $cat_item->cat_ID; ?>
			            <?php $intCounter++; ?>
						<?php
							//Recursive function
							$intCounter = wp_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories','default');
						?>

					</li>

					<?php
			    }
			}
		}
	}
	else
	{
		echo 'Not Found';
	}

	return $intCounter;
}

//RECURSIVE Sub Menu Items of default categories and pages
function wp_custom_navigation_default_sub_items($childof, $intCounter, $parentli, $type, $output_type) {

	$counter = $intCounter;

	//Custom Menu
	if ($output_type == 'menu')
	{
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	}
	//Sidebar Menu
	elseif ($output_type == 'default')
	{
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	}
	else
	{

	}

	//Get Sub Category Items
	if ($type == 'categories')
	{
		$sub_array = get_categories($sub_args);
	}
	//Get Sub Page Items
	elseif ($type == 'pages')
	{
		$sub_array = get_pages($sub_args);
	}


	if ($sub_array)
	{
		?>

		<ul id="sub-custom-nav-<?php echo $type ?>">

		<?php
		//DISPLAY Loop
		foreach ($sub_array as $sub_item)
		{
			//Prepare Menu Data
			//Category Menu Item
			if ($type == 'categories')
			{
				$link = get_category_link($sub_item->cat_ID);
				$title = htmlentities($sub_item->cat_name);
				$parent_id = $sub_item->cat_ID;
				$itemid = $sub_item->cat_ID;
				$linktype = 'category';
				$appendtype = 'Category';
				$description = htmlentities(strip_tags($sub_item->description));
			}
			//Page Menu Item
			elseif ($type == 'pages')
			{
				$link = get_permalink($sub_item->ID);
				$title = htmlentities($sub_item->post_title);
				$parent_id = $sub_item->ID;
				$linktype = 'page';
				$itemid = $sub_item->ID;
				$appendtype = 'Page';
				$description = htmlentities(get_post_meta($itemid, 'page-description', true));
			}
			//Custom Menu Item
			else
			{
				$title = '';
				$linktype = 'custom';
				$appendtype= 'Custom';
			}

			//Custom Menu
			if ($output_type == 'menu')
			{
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<dl>
					<dt>
						<span class="title"><?php echo $title; ?></span>
							<span class="controls">
							<span class="type"><?php echo $linktype; ?></span>
							<a id="edit<?php echo $counter; ?>" onclick="edititem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-edit.png" /></a>
								<a id="remove<?php echo $counter; ?>" onclick="removeitem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>">
									<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-close.png" />
								</a>
								<a target="_blank" href="<?php echo $link; ?>">
									<img alt="View Page" title="View Page" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-viewpage.png" />
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

						//Do recursion
						$counter = wp_custom_navigation_default_sub_items($parent_id, $counter, $parent_id, $type, 'menu');

					?>

				</li>
				<?php
			}
			//Sidebar Menu
			elseif ($output_type == 'default')
			{

				?>
				<li>
					<dl>
					<dt>

					<?php $templatedir = get_bloginfo('url'); ?>
					<span class="title"><?php echo $title; ?></span> <a onclick="appendToList('<?php echo $templatedir; ?>','<?php echo $appendtype; ?>','<?php echo $title; ?>','<?php echo $link; ?>','<?php echo $itemid; ?>','<?php echo $parent_id ?>','<?php echo $description; ?>')" name="<?php echo $title; ?>" value="<?php echo $link; ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu" src="<?php echo get_bloginfo('url'); ?>/wp-admin/images/ico-add.png" /></a> </dt>
					</dl>
					<?php

						//Do recursion
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

	$counter = $intCounter;

	global $wpdb;



	//Get Sub Category Items
	if ($type == 'categories')
	{
		$sub_args = array(
			'child_of' => $childof,
			'hide_empty'  => false,
			'parent' => $childof);
		$sub_array = get_categories($sub_args);
	}
	//Get Sub Page Items
	elseif ($type == 'pages')
	{
		$sub_args = array(
			'child_of' => $childof,
			'parent' => $childof);

		$sub_array = get_pages($sub_args);

	}
	else {

	}

	if ($sub_array)
	{
		//DISPLAY Loop
		foreach ($sub_array as $sub_item)
		{
			if (isset($sub_item->parent)) {
				$sub_item_parent = $sub_item->parent;
			}
			elseif (isset($sub_item->post_parent)) {
				$sub_item_parent = $sub_item->post_parent;
			}
			else {
			}
			//Is child
			if ($sub_item_parent == $childof)
			{
				//Prepare Menu Data
				//Category Menu Item
				if ($type == 'categories')
				{
					$link = get_category_link($sub_item->cat_ID);
					$title = htmlentities($sub_item->cat_name);
					$parent_id = $sub_item->category_parent;
					$itemid = $sub_item->cat_ID;
					$linktype = 'category';
					$appendtype= 'Category';
				}
				//Page Menu Item
				elseif ($type == 'pages')
				{
					$link = get_permalink($sub_item->ID);
					$title = htmlentities($sub_item->post_title);
					$parent_id = $sub_item->post_parent;
					$linktype = 'page';
					$itemid = $sub_item->ID;
					$appendtype= 'Page';
				}
				//Custom Menu Item
				else
				{
					$title = '';
					$linktype = 'custom';
					$appendtype= 'Custom';
				}

				//CHECK for existing parent records
				//echo $parent_id;
				$wp_result = $wpdb->get_results("SELECT id FROM ".$table_name." WHERE post_id='".$parent_id."' AND link_type='".$linktype."' AND menu_id='".$menu_id."'");
				if ($wp_result > 0 && isset($wp_result[0]->id)) {
					$parent_id = $wp_result[0]->id;
				}
				else {
					//$parent_id = 0;
				}

				//INSERT item
				$insert = "INSERT INTO ".$table_name." (position,post_id,parent_id,custom_title,custom_link,custom_description,menu_icon,link_type,menu_id,custom_anchor_title) "."VALUES ('".$counter."','".$itemid."','".$parent_id."','".$title."','".$link."','','','".$linktype."','".$menu_id."','".$title."')";
	  			$results = $wpdb->query( $insert );

	  			$counter++;
	  			$counter = get_children_menu_elements($itemid, $counter, $parent_id, $type, $menu_id, $table_name);
			}
			//Do nothing
			else {

			}
		}
	}
	return $counter;
}


?>
