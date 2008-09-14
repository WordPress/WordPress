<?php

//
// Big Mess
//

// Ugly recursive category stuff.
function cat_rows( $parent = 0, $level = 0, $categories = 0, $page = 1, $per_page = 20 ) {
	$count = 0;
	_cat_rows($categories, $count, $parent, $level, $page, $per_page);
}

function _cat_rows( $categories, &$count, $parent = 0, $level = 0, $page = 1, $per_page = 20 ) {
	if ( empty($categories) ) {
		$args = array('hide_empty' => 0);
		if ( !empty($_GET['s']) )
			$args['search'] = $_GET['s'];
		$categories = get_categories( $args );
	}

	if ( !$categories )
		return false;

	$children = _get_term_hierarchy('category');

	$start = ($page - 1) * $per_page;
	$end = $start + $per_page;
	$i = -1;
	ob_start();
	foreach ( $categories as $category ) {
		if ( $count >= $end )
			break;

		$i++;

		if ( $category->parent != $parent )
			continue;

		// If the page starts in a subtree, print the parents.
		if ( $count == $start && $category->parent > 0 ) {
			$my_parents = array();
			$my_parent = $category->parent;
			while ( $my_parent) {
				$my_parent = get_category($my_parent);
				$my_parents[] = $my_parent;
				if ( !$my_parent->parent )
					break;
				$my_parent = $my_parent->parent;
			}
			$num_parents = count($my_parents);
			while( $my_parent = array_pop($my_parents) ) {
				echo "\t" . _cat_row( $my_parent, $level - $num_parents );
				$num_parents--;
			}
		}

		if ( $count >= $start )
			echo "\t" . _cat_row( $category, $level );

		unset($categories[$i]); // Prune the working set
		$count++;

		if ( isset($children[$category->term_id]) )
			_cat_rows( $categories, $count, $category->term_id, $level + 1, $page, $per_page );

	}

	$output = ob_get_contents();
	ob_end_clean();

	$output = apply_filters('cat_rows', $output);

	echo $output;
}

function _cat_row( $category, $level, $name_override = false ) {
	global $class;

	$category = get_category( $category );

	$pad = str_repeat( '&#8212; ', $level );
	$name = ( $name_override ? $name_override : $pad . ' ' . $category->name );
	if ( current_user_can( 'manage_categories' ) ) {
		$edit = "<a class='row-title' href='categories.php?action=edit&amp;cat_ID=$category->term_id' title='" . attribute_escape(sprintf(__('Edit "%s"'), $category->name)) . "'>$name</a>";
	} else {
		$edit = $name;
	}

	$class = " class='alternate'" == $class ? '' : " class='alternate'";

	$category->count = number_format_i18n( $category->count );
	$posts_count = ( $category->count > 0 ) ? "<a href='edit.php?cat=$category->term_id'>$category->count</a>" : $category->count;
	$output = "<tr id='cat-$category->term_id'$class>
			   <th scope='row' class='check-column'>";
	if ( absint(get_option( 'default_category' ) ) != $category->term_id ) {
		$output .= "<input type='checkbox' name='delete[]' value='$category->term_id' />";
	} else {
		$output .= "&nbsp;";
	}
	$output .= "</th>
				<td>$edit</td>
				<td>$category->description</td>
				<td class='num'>$posts_count</td>\n\t</tr>\n";

	return apply_filters('cat_row', $output);
}

function link_cat_row( $category ) {
	global $class;

	if ( !$category = get_term( $category, 'link_category' ) )
		return false;
	if ( is_wp_error( $category ) )
		return $category;

	$name = ( $name_override ? $name_override : $category->name );
	if ( current_user_can( 'manage_categories' ) ) {
		$edit = "<a class='row-title' href='link-category.php?action=edit&amp;cat_ID=$category->term_id' title='" . attribute_escape(sprintf(__('Edit "%s"'), $category->name)) . "'>$name</a>";
		$default_cat_id = (int) get_option( 'default_link_category' );
	} else {
		$edit = $name;
	}

	$class = " class='alternate'" == $class ? '' : " class='alternate'";

	$category->count = number_format_i18n( $category->count );
	$count = ( $category->count > 0 ) ? "<a href='link-manager.php?cat_id=$category->term_id'>$category->count</a>" : $category->count;
	$output = "<tr id='link-cat-$category->term_id'$class>
			   <th scope='row' class='check-column'>";
	if ( absint( get_option( 'default_link_category' ) ) != $category->term_id ) {
		$output .= "<input type='checkbox' name='delete[]' value='$category->term_id' />";
	} else {
		$output .= "&nbsp;";
	}
	$output .= "</th>
				<td>$edit</td>
				<td>$category->description</td>
				<td class='num'>$count</td></tr>";

	return apply_filters( 'link_cat_row', $output );
}

function checked( $checked, $current) {
	if ( $checked == $current)
		echo ' checked="checked"';
}

function selected( $selected, $current) {
	if ( $selected == $current)
		echo ' selected="selected"';
}

//
// Category Checklists
//

// Deprecated. Use wp_link_category_checklist
function dropdown_categories( $default = 0, $parent = 0, $popular_ids = array() ) {
	global $post_ID;
	wp_category_checklist($post_ID);
}

class Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $category, $depth, $args) {
		extract($args);

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='category-$category->term_id'$class>" . '<label for="in-category-' . $category->term_id . '" class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="post_category[]" id="in-category-' . $category->term_id . '"' . (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "" ) . '/> ' . wp_specialchars( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
}

function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false ) {
	$walker = new Walker_Category_Checklist;
	$descendants_and_self = (int) $descendants_and_self;

	$args = array();

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id )
		$args['selected_cats'] = wp_get_post_categories($post_id);
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );

	if ( $descendants_and_self ) {
		$categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
		$self = get_category( $descendants_and_self );
		array_unshift( $categories, $self );
	} else {
		$categories = get_categories('get=all');
	}

	// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
	$checked_categories = array();
	for ( $i = 0; isset($categories[$i]); $i++ ) {
		if ( in_array($categories[$i]->term_id, $args['selected_cats']) ) {
			$checked_categories[] = $categories[$i];
			unset($categories[$i]);
		}
	}

	// Put checked cats on top
	echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	// Then the rest of them
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}

function wp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true ) {
	global $post_ID;
	if ( $post_ID )
		$checked_categories = wp_get_post_categories($post_ID);
	else
		$checked_categories = array();
	$categories = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number, 'hierarchical' => false ) );

	$popular_ids = array();
	foreach ( (array) $categories as $category ) {
		$popular_ids[] = $category->term_id;
		if ( !$echo ) // hack for AJAX use
			continue;
		$id = "popular-category-$category->term_id";
		?>

		<li id="<?php echo $id; ?>" class="popular-category">
			<label class="selectit" for="in-<?php echo $id; ?>">
			<input id="in-<?php echo $id; ?>" type="checkbox" value="<?php echo (int) $category->term_id; ?>" />
				<?php echo wp_specialchars( apply_filters( 'the_category', $category->name ) ); ?>
			</label>
		</li>

		<?php
	}
	return $popular_ids;
}

// Deprecated. Use wp_link_category_checklist
function dropdown_link_categories( $default = 0 ) {
	global $link_id;

	wp_link_category_checklist($link_id);
}

function wp_link_category_checklist( $link_id = 0 ) {
	$default = 1;

	if ( $link_id ) {
		$checked_categories = wp_get_link_cats($link_id);

		if ( count( $checked_categories ) == 0 ) {
			// No selected categories, strange
			$checked_categories[] = $default;
		}
	} else {
		$checked_categories[] = $default;
	}

	$categories = get_terms('link_category', 'orderby=count&hide_empty=0');

	if ( empty($categories) )
		return;

	foreach ( $categories as $category ) {
		$cat_id = $category->term_id;
		$name = wp_specialchars( apply_filters('the_category', $category->name));
		$checked = in_array( $cat_id, $checked_categories );
		echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', ($checked ? ' checked="checked"' : "" ), '/> ', $name, "</label></li>";
	}
}

// Tag stuff

// Returns a single tag row (see tag_rows below)
// Note: this is also used in admin-ajax.php!
function _tag_row( $tag, $class = '' ) {
		$count = number_format_i18n( $tag->count );
		$count = ( $count > 0 ) ? "<a href='edit.php?tag=$tag->slug'>$count</a>" : $count;

		$name = apply_filters( 'term_name', $tag->name );
		$out = '';
		$out .= '<tr id="tag-' . $tag->term_id . '"' . $class . '>';
		$out .= '<th scope="row" class="check-column"> <input type="checkbox" name="delete_tags[]" value="' . $tag->term_id . '" /></th>';
		$out .= '<td><strong><a class="row-title" href="edit-tags.php?action=edit&amp;tag_ID=' . $tag->term_id . '" title="' . attribute_escape(sprintf(__('Edit "%s"'), $name)) . '">' .
			$name . '</a></strong></td>';

		$out .= "<td class='num'>$count</td>";
		$out .= '</tr>';

		return $out;
}

// Outputs appropriate rows for the Nth page of the Tag Management screen,
// assuming M tags displayed at a time on the page
// Returns the number of tags displayed
function tag_rows( $page = 1, $pagesize = 20, $searchterms = '' ) {

	// Get a page worth of tags
	$start = ($page - 1) * $pagesize;

	$args = array('offset' => $start, 'number' => $pagesize, 'hide_empty' => 0);

	if ( !empty( $searchterms ) ) {
		$args['search'] = $searchterms;
	}

	$tags = get_terms( 'post_tag', $args );

	// convert it to table rows
	$out = '';
	$class = '';
	$count = 0;
	foreach( $tags as $tag )
		$out .= _tag_row( $tag, ++$count % 2 ? ' class="alternate"' : '' );

	// filter and send to screen
	$out = apply_filters('tag_rows', $out);
	echo $out;
	return $count;
}

// define the columns to display, the syntax is 'internal name' => 'display name'
function wp_manage_posts_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" />';
	$posts_columns['title'] = __('Title');
	if ( isset($_GET['post_status']) && 'draft' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Modified');
	elseif ( isset($_GET['post_status']) && 'pending' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Submitted');
	else
		$posts_columns['date'] = __('Date');
	$posts_columns['author'] = __('Author');
	$posts_columns['categories'] = __('Categories');
	$posts_columns['tags'] = __('Tags');
	if ( !isset($_GET['post_status']) || !in_array($_GET['post_status'], array('pending', 'draft', 'future')) )
		$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div>';
	$posts_columns['status'] = __('Status');
	$posts_columns = apply_filters('manage_posts_columns', $posts_columns);

	return $posts_columns;
}

// define the columns to display, the syntax is 'internal name' => 'display name'
function wp_manage_media_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" />';
	$posts_columns['icon'] = '';
	$posts_columns['media'] = _c('Media|media column header');
	$posts_columns['tags'] = _c('Tags|media column header');
//	$posts_columns['desc'] = _c('Description|media column header');
	$posts_columns['date'] = _c('Date Added|media column header');
	$posts_columns['parent'] = _c('Appears with|media column header');
	$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div>';
//	$posts_columns['actions'] = _c('Actions|media column header');
	$posts_columns = apply_filters('manage_media_columns', $posts_columns);

	return $posts_columns;
}

function wp_manage_pages_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" />';

	$posts_columns['title'] = __('Title');

	$post_status = isset( $_GET['post_status'] ) ? $_GET['post_status'] : '';

	switch( $post_status ) {
		case 'draft':
			$posts_columns['modified'] = __('Modified');
			break;
		case 'pending':
			$posts_columns['modified'] = __('Submitted');
			break;
		default:
			$posts_columns['date'] = __('Date');
	}

	$posts_columns['author'] = __('Author');
	if ( !in_array($post_status, array('pending', 'draft', 'future')) )
		$posts_columns['comments'] = '<div class="vers"><img alt="" src="images/comment-grey-bubble.png" /></div>';
	$posts_columns['status'] = __('Status');
	$posts_columns = apply_filters('manage_pages_columns', $posts_columns);

	return $posts_columns;
}

function wp_manage_links_columns() {
	$link_columns = array(
		'name'       => __('Name'),
		'url'       => __('URL'),
		'categories' => __('Categories'),
		'rel'      => __('rel'),
		'visible'   => __('Visible'),
	);

	return apply_filters('manage_link_columns', $link_columns);
}

function inline_edit_row( $type ) {
	global $current_user;

	if ( 'post' == $type ) 
		$post = get_default_post_to_edit(); 
	else 
		$post = get_default_page_to_edit();  

	echo '<tr id="inline-edit" style="display: none">';
	$columns = $type == 'post' ? wp_manage_posts_columns() : wp_manage_pages_columns();
	$hidden = (array) get_user_option( "manage-$type-columns-hidden" );
	foreach($columns as $column_name=>$column_display_name) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch($column_name) {

			case 'cb': ?>
			  <th class="check-column"></th>
			  <?php
			  break;

			case 'modified':
			case 'date':
				$attributes = 'class="date column-date"' . $style;
			?>
				<td class="date"<?php echo $style ?>>
					<?php touch_time(1, 1, 4, 1); ?>
				</td>
				<?php
				break;

			case 'title':
				$attributes = "class=\"$type-title column-title\"" . $style;
			?>
				<td <?php echo $attributes ?>>
					<div class="title">
						<input type="text" name="post_title" class="title" value="" /><br />
						<label><?php _e('Slug'); ?></label><input type="text" name="post_name" value="" class="slug" />
					</div>
					<?php if ($type == 'page'): ?>
					<div class="other">
						<label><?php _e('Parent'); ?></label>
						<select name="post_parent">
							<option value="0"><?php _e('Main Page (no parent)'); ?></option>
							<?php parent_dropdown(); ?>
						</select><br />
						<label><?php _e('Template'); ?></label>
						<select name="page_template">
							<option value='default'><?php _e('Default Template'); ?></option>
							<?php page_template_dropdown() ?>
						</select>
					</div>
					<div class="more">
						<label><?php _e('Order'); ?></label><input type="text" name="menu_order" value="<?php echo $post->menu_order ?>" />
						<label><?php _e('Password'); ?></label><input type="text" name="post_password" value="<?php echo $post->post_password ?>" />      
					</div>
					<?php endif; ?>
					<div class="clear"></div>
					<?php
					$actions = array();
					$actions['save'] = '<a href="#">' . __('Save') . '</a>';
					$actions['cancel'] = '<a href="#">' . __('Cancel') . '</a>';
					$action_count = count($actions);
					$i = 0;
					foreach ( $actions as $action => $link ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$link$sep</span>";
					}
					?>
				</td>
				<?php
				break;

			case 'categories': ?>
				<td <?php echo $attributes ?>>
					<ul class="categories">
						<?php wp_category_checklist() ?>
					</ul>
				</td>
				<?php
				break;

			case 'tags': ?>
				<td <?php echo $attributes ?>>
					<textarea name="tags_input"></textarea>
				</td>
				<?php
				break;

			case 'comments':
				$attributes = 'class="comments column-comments num"' . $style;
			 ?>
				<td <?php echo $attributes ?>>
					<input title="Allow Comments" type="checkbox" name="comment_status" value="open" /><br />
					<input title="Allow Pings" type="checkbox" name="ping_status" value="open" />
				</td>
				<?php
				break;

			case 'author': ?>
				<td <?php echo $attributes ?>>
					<?php
					$authors = get_editable_user_ids( $current_user->id ); // TODO: ROLE SYSTEM
					if ( $authors && count( $authors ) > 1 ) {
						wp_dropdown_users( array('include' => $authors, 'name' => 'post_author', 'class'=> 'author', 'selected' => $post->post_author) ); 
					} else {
						echo $current_user->user_nicename.'<input type="hidden" value="'.$post->post_author.'" class="author" />';
					}
					?>
				</td>
				<?php
				break;

			case 'status': ?>
				<td <?php echo $attributes ?>>
					<select name="post_status">
						<?php if ( current_user_can('publish_posts') ) : // Contributors only get "Unpublished" and "Pending Review" ?>
						<option value='publish'><?php _e('Published') ?></option>
						<option value='future'><?php _e('Scheduled') ?></option>
						<?php endif; ?>
						<option value='pending'><?php _e('Pending Review') ?></option>
						<option value='draft'><?php _e('Unpublished') ?></option>
					</select>
					<?php if($type == 'page'): ?>
					<br /><label><input type="checkbox" name="page_private" value="private" <?php checked($post->post_status, 'private'); ?> /> <?php _e('Private') ?></label></p>
					<?php else: ?>
					<?php if ( current_user_can( 'edit_others_posts' ) ) : ?>
					<br /><label><input type="checkbox" name="sticky" value="sticky" /> <?php _e('Sticky') ?></label></p>
					<?php endif; ?>
					<?php endif; ?>
				</td>
				<?php
				break;

			case 'control_view': ?>
				<td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
				<?php
				break;

			case 'control_edit': ?>
				<td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
				<?php
				break;

			case 'control_delete': ?>
				<td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete'>" . __('Delete') . "</a>"; } ?></td>
				<?php
				break;

			default: ?>
				<td><?php do_action('manage_posts_custom_column', $column_name, $post->ID); ?></td>
				<?php
				break;
		}
	}

	echo '</tr>';
}

function inline_save_row( $data ) {  
	// get the original post content
	$post = get_post( $data['post_ID'], ARRAY_A );
	$data['content'] = $post['post_content'];

	// statuses
	if ( 'page' == $data['post_type'] && 'private' == $data['page_private'] )
		$data['post_status'] = 'private';
	if ( empty($data['comment_status']) ) 
		$data['comment_status'] = 'closed';
	if ( empty($data['ping_status']) )
		$data['ping_status'] = 'closed';

	// rename
	$data['user_ID'] = $GLOBALS['user_ID'];
	$data['excerpt'] = $data['post_excerpt'];
	$data['trackback_url'] = $data['to_ping'];
	$data['parent_id'] = $data['post_parent'];

	// update the post
	$_POST = $data;
	edit_post();
}

// outputs XML of the post/page data ready for use in the inline editor
// accepts array of post IDs
function get_inline_data($posts) {
	global $post;

	header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
	echo "<?xml version='1.0' ?>\n";
	echo "<posts>\n";

	foreach ($posts as $ID) {
		$GLOBALS['post'] = get_post($ID);
		$GLOBALS['post_ID'] = $ID;

		if ( ($post->post_type == 'post' && !current_user_can('edit_post', $ID)) || 
				($post->post_type == 'page' && !current_user_can('edit_page', $ID)) || 
				($post->post_type != 'post' && $post->post_type != 'page'))
			continue;

		echo "  <post id='$ID'>\n";
		echo "    <post_title>" . wp_specialchars($post->post_title, 1) . "</post_title>\n";
		echo "    <post_name>$post->post_name</post_name>\n";
		echo "    <post_author>$post->post_author</post_author>\n";
		echo "    <comment_status>$post->comment_status</comment_status>\n";
		echo "    <ping_status>$post->ping_status</ping_status>\n";
		echo "    <post_status>$post->post_status</post_status>\n";
		echo "    <jj>" . mysql2date( 'd', $post->post_date ) . "</jj>\n";
		echo "    <mm>" . mysql2date( 'm', $post->post_date ) . "</mm>\n";
		echo "    <aa>" . mysql2date( 'Y', $post->post_date ) . "</aa>\n";
		echo "    <hh>" . mysql2date( 'H', $post->post_date ) . "</hh>\n";
		echo "    <mn>" . mysql2date( 'i', $post->post_date ) . "</mn>\n";
		if( $post->post_type == 'post' ) {
			echo '    <tags_input>' . wp_specialchars(get_tags_to_edit( $post->ID ), 1) . "</tags_input>\n";
			echo '    <post_category>' . implode( ',', wp_get_post_categories( $post->ID ) ) . "</post_category>\n";
			echo '    <sticky>' . (is_sticky($post->ID) ? 'sticky' : '') . "</sticky>\n";
		}
		if( $post->post_type == 'page' ) {
			echo "    <post_parent>$post->post_parent</post_parent>\n";
			echo '    <page_template>' . wp_specialchars(get_post_meta( $post->ID, '_wp_page_template', true ), 1) . "</page_template>\n";
			echo "    <post_password>" . wp_specialchars($post->post_password, 1) . "</post_password>\n";
			echo "    <menu_order>$post->menu_order</menu_order>\n";
		}
		echo "  </post>\n";
 	}

	echo '</posts>';
}

function post_rows( $posts = array() ) {
	global $wp_query, $post, $mode;

	add_filter('the_title','wp_specialchars');

	// Create array of post IDs.
	$post_ids = array();

	if ( empty($posts) )
		$posts = &$wp_query->posts;

	foreach ( $posts as $a_post )
		$post_ids[] = $a_post->ID;

	$comment_pending_count = get_pending_comments_num($post_ids);
	if ( empty($comment_pending_count) )
		$comment_pending_count = array();

	foreach ( $posts as $post ) {
		if ( empty($comment_pending_count[$post->ID]) )
			$comment_pending_count[$post->ID] = 0;

		_post_row($post, $comment_pending_count[$post->ID], $mode);
	}
}

function _post_row($a_post, $pending_comments, $mode) {
	global $post;
	static $class;

	$global_post = $post;
	$post = $a_post;
	setup_postdata($post);

	$class = 'alternate' == $class ? '' : 'alternate';
	global $current_user;
	$post_owner = ( $current_user->ID == $post->post_author ? 'self' : 'other' );
	$edit_link = get_edit_post_link( $post->ID );
	$title = get_the_title();
	if ( empty($title) )
		$title = __('(no title)');
?>
	<tr id='post-<?php echo $post->ID; ?>' class='<?php echo trim( $class . ' author-' . $post_owner . ' status-' . $post->post_status ); ?>' valign="top">
<?php
	$posts_columns = wp_manage_posts_columns();
	$hidden = (array) get_user_option( 'manage-post-columns-hidden' );
	foreach ( $posts_columns as $column_name=>$column_display_name ) {
		$class = "class=\"$column_name column-$column_name\"";

		$style = '';
		if ( in_array($column_name, $hidden) )
			$style = ' style="display:none;"';

		$attributes = "$class$style";

		switch ($column_name) {

		case 'cb':
		?>
		<th scope="row" class="check-column"><?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /><?php } ?></th>
		<?php
		break;

		case 'modified':
		case 'date':
			$attributes = 'class="date column-date"' . $style;
			if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
				$t_time = $h_time = __('Unpublished');
			} else {
				if ( 'modified' == $column_name ) {
					$t_time = get_the_modified_time(__('Y/m/d g:i:s A'));
					$m_time = $post->post_modified;
					$time = get_post_modified_time('G', true);
				} else {
					$t_time = get_the_time(__('Y/m/d g:i:s A'));
					$m_time = $post->post_date;
					$time = get_post_time('G', true);
				}
				if ( ( abs(time() - $time) ) < 86400 ) {
					if ( ( 'future' == $post->post_status) )
						$h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
					else
						$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
				} else {
					$h_time = mysql2date(__('Y/m/d'), $m_time);
				}
			}

			if ( 'excerpt' == $mode ) { ?>
		<td <?php echo $attributes ?>><?php echo apply_filters('post_date_column_time', $t_time, $post, $column_name, $mode) ?></td>
		<?php } else { ?>
		<td <?php echo $attributes ?>><abbr title="<?php echo $t_time ?>"><?php echo apply_filters('post_date_column_time', $h_time, $post, $column_name, $mode) ?></abbr></td>
		<?php }
		break;

		case 'title':
			$attributes = 'class="post-title column-title"' . $style;
		?>
		<td <?php echo $attributes ?>><strong><?php if ( current_user_can( 'edit_post', $post->ID ) ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo $title ?></a><?php } else { echo $title; } ?></strong>
		<?php
			if ( !empty($post->post_password) ) { _e(' &#8212; <strong>Protected</strong>'); } elseif ('private' == $post->post_status) { _e(' &#8212; <strong>Private</strong>'); }

			if ( 'excerpt' == $mode )
				the_excerpt();

			$actions = array();
			$actions['edit'] = '<a href="post.php?action=edit&amp;post=' . $post->ID . '">' . __('Edit') . '</a>';
			$actions['inline'] = '<a href="#" class="editinline">' . __('Quick Edit') . '</a>';
			$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("post.php?action=delete&amp;post=$post->ID", 'delete-post_' . $post->ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $post->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this post '%s'\n  'Cancel' to stop, 'OK' to delete."), $post->post_title )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
			$actions['view'] = '<a href="' . get_permalink($post->ID) . '" title="' . attribute_escape(sprintf(__('View "%s"'), $title)) . '" rel="permalink">' . __('View') . '</a>';
			$action_count = count($actions);
			$i = 0;
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
		?>
		</td>
		<?php
		break;

		case 'categories':
		?>
		<td <?php echo $attributes ?>><?php
			$categories = get_the_category();
			if ( !empty( $categories ) ) {
				$out = array();
				foreach ( $categories as $c )
					$out[] = "<a href='edit.php?category_name=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
					echo join( ', ', $out );
			} else {
				_e('Uncategorized');
			}
		?></td>
		<?php
		break;

		case 'tags':
		?>
		<td <?php echo $attributes ?>><?php
			$tags = get_the_tags();
			if ( !empty( $tags ) ) {
				$out = array();
				foreach ( $tags as $c )
					$out[] = "<a href='edit.php?tag=$c->slug'> " . wp_specialchars(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
				echo join( ', ', $out );
			} else {
				_e('No Tags');
			}
		?></td>
		<?php
		break;

		case 'comments':
			$attributes = 'class="comments column-comments num"' . $style;
		?>
		<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
		<?php
			$pending_phrase = sprintf( __('%s pending'), number_format( $pending_comments ) );
			if ( $pending_comments )
				echo '<strong>';
				comments_number("<a href='edit.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('0') . '</span></a>', "<a href='edit.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('1') . '</span></a>', "<a href='edit.php?p=$post->ID' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('%') . '</span></a>');
				if ( $pending_comments )
				echo '</strong>';
		?>
		</div></td>
		<?php
		break;

		case 'author':
		?>
		<td <?php echo $attributes ?>><a href="edit.php?author=<?php the_author_ID(); ?>"><?php the_author() ?></a></td>
		<?php
		break;

		case 'status':
		?>
		<td <?php echo $attributes ?>>
		<a href="<?php the_permalink(); ?>" title="<?php echo attribute_escape(sprintf(__('View "%s"'), $title)); ?>" rel="permalink">
		<?php
			switch ( $post->post_status ) {
				case 'publish' :
				case 'private' :
					_e('Published');
					break;
				case 'future' :
					_e('Scheduled');
					break;
				case 'pending' :
					_e('Pending Review');
					break;
				case 'draft' :
					_e('Unpublished');
					break;
			}
		?>
		</a>
		</td>
		<?php
		break;

		case 'control_view':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink" class="view"><?php _e('View'); ?></a></td>
		<?php
		break;

		case 'control_edit':
		?>
		<td><?php if ( current_user_can('edit_post', $post->ID) ) { echo "<a href='$edit_link' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

		case 'control_delete':
		?>
		<td><?php if ( current_user_can('delete_post', $post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete'>" . __('Delete') . "</a>"; } ?></td>
		<?php
		break;

		default:
		?>
		<td><?php do_action('manage_posts_custom_column', $column_name, $post->ID); ?></td>
		<?php
		break;
	}
}
?>
	</tr>
<?php
	$post = $global_post;
}

/*
 * display one row if the page doesn't have any children
 * otherwise, display the row and its children in subsequent rows
 */
function display_page_row( $page, $level = 0 ) {
	global $post;
	static $class;

	$post = $page;
	setup_postdata($page);

	$page->post_title = wp_specialchars( $page->post_title );
	$pad = str_repeat( '&#8212; ', $level );
	$id = (int) $page->ID;
	$class = ('alternate' == $class ) ? '' : 'alternate';
	$posts_columns = wp_manage_pages_columns();
	$hidden = (array) get_user_option( 'manage-page-columns-hidden' );
	$title = get_the_title();
	if ( empty($title) )
		$title = __('(no title)');
?>
  <tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'>


 <?php

foreach ($posts_columns as $column_name=>$column_display_name) {
	$class = "class=\"$column_name column-$column_name\"";

	$style = '';
	if ( in_array($column_name, $hidden) )
		$style = ' style="display:none;"';

	$attributes = "$class$style";

	switch ($column_name) {

	case 'cb':
		?>
		<th scope="row" class="check-column"><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /></th>
		<?php
		break;
	case 'modified':
	case 'date':
		$attributes = 'class="date column-date"' . $style;
		if ( '0000-00-00 00:00:00' == $page->post_date && 'date' == $column_name ) {
			$t_time = $h_time = __('Unpublished');
		} else {
			if ( 'modified' == $column_name ) {
				$t_time = get_the_modified_time(__('Y/m/d g:i:s A'));
				$m_time = $page->post_modified;
				$time = get_post_modified_time('G', true);
			} else {
				$t_time = get_the_time(__('Y/m/d g:i:s A'));
				$m_time = $page->post_date;
				$time = get_post_time('G', true);
			}
			if ( ( abs(time() - $time) ) < 86400 ) {
				if ( ( 'future' == $page->post_status) )
					$h_time = sprintf( __('%s from now'), human_time_diff( $time ) );
				else
					$h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date(__('Y/m/d'), $m_time);
			}
		}
		?>
		<td <?php echo $attributes ?>><abbr title="<?php echo $t_time ?>"><?php echo $h_time ?></abbr></td>
		<?php
		break;
	case 'title':
		$attributes = 'class="post-title page-title column-title"' . $style;
		$edit_link = get_edit_post_link( $page->ID );
		?>
		<td <?php echo $attributes ?>><strong><?php if ( current_user_can( 'edit_post', $page->ID ) ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; } ?></strong>
		<?php
		if ( !empty($post->post_password) ) { _e(' &#8212; <strong>Protected</strong>'); } elseif ('private' == $post->post_status) { _e(' &#8212; <strong>Private</strong>'); }

		$actions = array();
		$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
		$actions['inline'] = '<a href="#" class="editinline">' . __('Quick Edit') . '</a>';
		$actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url("page.php?action=delete&amp;post=$page->ID", 'delete-page_' . $page->ID) . "' onclick=\"if ( confirm('" . js_escape(sprintf( ('draft' == $page->post_status) ? __("You are about to delete this draft '%s'\n  'Cancel' to stop, 'OK' to delete.") : __("You are about to delete this page '%s'\n  'Cancel' to stop, 'OK' to delete."), $page->post_title )) . "') ) { return true;}return false;\">" . __('Delete') . "</a>";
		$actions['view'] = '<a href="' . get_permalink($page->ID) . '" title="' . attribute_escape(sprintf(__('View "%s"'), $title)) . '" rel="permalink">' . __('View') . '</a>';
		$action_count = count($actions);
		$i = 0;
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo "<span class='$action'>$link$sep</span>";
		}
		?>
		</td>
		<?php
		break;

	case 'comments':
		$attributes = 'class="comments column-comments num"' . $style;
		?>
		<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
		<?php
		$left = get_pending_comments_num( $page->ID );
		$pending_phrase = sprintf( __('%s pending'), number_format( $left ) );
		if ( $left )
			echo '<strong>';
		comments_number("<a href='edit-pages.php?page_id=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('0') . '</span></a>', "<a href='edit-pages.php?page_id=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('1') . '</span></a>', "<a href='edit-pages.php?page_id=$id' title='$pending_phrase' class='post-com-count'><span class='comment-count'>" . __('%') . '</span></a>');
		if ( $left )
			echo '</strong>';
		?>
		</div></td>
		<?php
		break;

	case 'author':
		?>
		<td <?php echo $attributes ?>><a href="edit-pages.php?author=<?php the_author_ID(); ?>"><?php the_author() ?></a></td>
		<?php
		break;

	case 'status':
		?>
		<td <?php echo $attributes ?>>
		<a href="<?php the_permalink(); ?>" title="<?php echo attribute_escape(sprintf(__('View "%s"'), $title)); ?>" rel="permalink">
		<?php
		switch ( $page->post_status ) {
			case 'publish' :
			case 'private' :
				_e('Published');
				break;
			case 'future' :
				_e('Scheduled');
				break;
			case 'pending' :
				_e('Pending Review');
				break;
			case 'draft' :
				_e('Unpublished');
				break;
		}
		?>
		</a>
		</td>
		<?php
		break;

	default:
		?>
		<td><?php do_action('manage_pages_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
 ?>

   </tr>

<?php
}

/*
 * displays pages in hierarchical order with paging support
 */
function page_rows($pages, $pagenum = 1, $per_page = 20) {
	$level = 0;

	if ( ! $pages ) {
		$pages = get_pages( array('sort_column' => 'menu_order') );

		if ( ! $pages )
			return false;
	}

	/*
	 * arrange pages into two parts: top level pages and children_pages
	 * children_pages is two dimensional array, eg.
	 * children_pages[10][] contains all sub-pages whose parent is 10.
	 * It only takes O(N) to arrange this and it takes O(1) for subsequent lookup operations
	 * If searching, ignore hierarchy and treat everything as top level
	 */
	if ( empty($_GET['s']) )  {

		$top_level_pages = array();
		$children_pages  = array();

		foreach ( $pages as $page ) {

			// catch and repair bad pages
			if ( $page->post_parent == $page->ID ) {
				$page->post_parent = 0;
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts SET post_parent = '0' WHERE ID = %d", $page->ID) );
				clean_page_cache( $page->ID );
			}

			if ( 0 == $page->post_parent )
				$top_level_pages[] = $page;
			else
				$children_pages[ $page->post_parent ][] = $page;
		}

		$pages = &$top_level_pages;
	}

	$count = 0;
	$start = ($pagenum - 1) * $per_page;
	$end = $start + $per_page;

	foreach ( $pages as $page ) {
		if ( $count >= $end )
			break;

		if ( $count >= $start )
			echo "\t" . display_page_row( $page, $level );

		$count++;

		if ( isset($children_pages) )
			_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
	}

	// if it is the last pagenum and there are orphaned pages, display them with paging as well
	if ( isset($children_pages) && $count < $end ){
		foreach( $children_pages as $orphans ){
			foreach ( $orphans as $op ) {
				if ( $count >= $end )
					break;
				if ( $count >= $start )
					echo "\t" . display_page_row( $op, 0 );
				$count++;
			}
		}
	}
}

/*
 * Given a top level page ID, display the nested hierarchy of sub-pages
 * together with paging support
 */
function _page_rows( &$children_pages, &$count, $parent, $level, $pagenum, $per_page ) {

	if ( ! isset( $children_pages[$parent] ) )
		return;

	$start = ($pagenum - 1) * $per_page;
	$end = $start + $per_page;

	foreach ( $children_pages[$parent] as $page ) {

		if ( $count >= $end )
			break;

		// If the page starts in a subtree, print the parents.
		if ( $count == $start && $page->post_parent > 0 ) {
			$my_parents = array();
			$my_parent = $page->post_parent;
			while ( $my_parent) {
				$my_parent = get_post($my_parent);
				$my_parents[] = $my_parent;
				if ( !$my_parent->post_parent )
					break;
				$my_parent = $my_parent->post_parent;
			}
			$num_parents = count($my_parents);
			while( $my_parent = array_pop($my_parents) ) {
				echo "\t" . display_page_row( $my_parent, $level - $num_parents );
				$num_parents--;
			}
		}

		if ( $count >= $start )
			echo "\t" . display_page_row( $page, $level );

		$count++;

		_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
	}

	unset( $children_pages[$parent] ); //required in order to keep track of orphans
}

function user_row( $user_object, $style = '', $role = '' ) {
	global $wp_roles;

	$current_user = wp_get_current_user();

	if ( !( is_object( $user_object) && is_a( $user_object, 'WP_User' ) ) )
		$user_object = new WP_User( (int) $user_object );
	$email = $user_object->user_email;
	$url = $user_object->user_url;
	$short_url = str_replace( 'http://', '', $url );
	$short_url = str_replace( 'www.', '', $short_url );
	if ('/' == substr( $short_url, -1 ))
		$short_url = substr( $short_url, 0, -1 );
	if ( strlen( $short_url ) > 35 )
		$short_url =  substr( $short_url, 0, 32 ).'...';
	$numposts = get_usernumposts( $user_object->ID );
	if ( current_user_can( 'edit_user', $user_object->ID ) ) {
		if ($current_user->ID == $user_object->ID) {
			$edit = 'profile.php';
		} else {
			$edit = clean_url( add_query_arg( 'wp_http_referer', urlencode( clean_url( stripslashes( $_SERVER['REQUEST_URI'] ) ) ), "user-edit.php?user_id=$user_object->ID" ) );
		}
		$edit = "<a href=\"$edit\">$user_object->user_login</a>";
	} else {
		$edit = $user_object->user_login;
	}
	$role_name = isset($wp_roles->role_names[$role]) ? translate_with_context($wp_roles->role_names[$role]) : __('None');
	$r = "<tr id='user-$user_object->ID'$style>
		<th scope='row' class='check-column'><input type='checkbox' name='users[]' id='user_{$user_object->ID}' class='$role' value='{$user_object->ID}' /></th>
		<td><strong>$edit</strong></td>
		<td>$user_object->first_name $user_object->last_name</td>
		<td><a href='mailto:$email' title='" . sprintf( __('e-mail: %s' ), $email ) . "'>$email</a></td>
		<td>$role_name</td>";
	$r .= "\n\t\t<td class='num'>";
	if ( $numposts > 0 ) {
		$r .= "<a href='edit.php?author=$user_object->ID' title='" . __( 'View posts by this author' ) . "' class='edit'>";
		$r .= $numposts;
		$r .= '</a>';
	} else {
		$r .= 0;
	}
	$r .= "</td>\n\t</tr>";
	return $r;
}

function _wp_get_comment_list( $status = '', $s = false, $start, $num ) {
	global $wpdb;

	$start = abs( (int) $start );
	$num = (int) $num;

	if ( 'moderated' == $status )
		$approved = "comment_approved = '0'";
	elseif ( 'approved' == $status )
		$approved = "comment_approved = '1'";
	elseif ( 'spam' == $status )
		$approved = "comment_approved = 'spam'";
	else
		$approved = "( comment_approved = '0' OR comment_approved = '1' )";

	if ( $s ) {
		$s = $wpdb->escape($s);
		$comments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments WHERE
			(comment_author LIKE '%$s%' OR
			comment_author_email LIKE '%$s%' OR
			comment_author_url LIKE ('%$s%') OR
			comment_author_IP LIKE ('%$s%') OR
			comment_content LIKE ('%$s%') ) AND
			$approved
			ORDER BY comment_date_gmt DESC LIMIT $start, $num");
	} else {
		$comments = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments USE INDEX (comment_date_gmt) WHERE $approved ORDER BY comment_date_gmt DESC LIMIT $start, $num" );
	}

	update_comment_cache($comments);

	$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );

	return array($comments, $total);
}

function _wp_comment_row( $comment_id, $mode, $comment_status, $checkbox = true ) {
	global $comment, $post;
	$comment = get_comment( $comment_id );
	$post = get_post($comment->comment_post_ID);
	$authordata = get_userdata($post->post_author);
	$the_comment_status = wp_get_comment_status($comment->comment_ID);

	if ( current_user_can( 'edit_post', $post->ID ) ) {
		$post_link = "<a href='" . get_comment_link() . "'>";
		$post_link .= get_the_title($comment->comment_post_ID) . '</a>';
	} else {
		$post_link = get_the_title($comment->comment_post_ID);
	}

	$author_url = get_comment_author_url();
	if ( 'http://' == $author_url )
		$author_url = '';
	$author_url_display = $author_url;
	if ( strlen($author_url_display) > 50 )
		$author_url_display = substr($author_url_display, 0, 49) . '...';

	$ptime = date('G', strtotime( $comment->comment_date ) );
	if ( ( abs(time() - $ptime) ) < 86400 )
		$ptime = sprintf( __('%s ago'), human_time_diff( $ptime ) );
	else
		$ptime = mysql2date(__('Y/m/d \a\t g:i A'), $comment->comment_date );

	$delete_url    = clean_url( wp_nonce_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
	$approve_url   = clean_url( wp_nonce_url( "comment.php?action=approvecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "approve-comment_$comment->comment_ID" ) );
	$unapprove_url = clean_url( wp_nonce_url( "comment.php?action=unapprovecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "unapprove-comment_$comment->comment_ID" ) );
	$spam_url      = clean_url( wp_nonce_url( "comment.php?action=deletecomment&dt=spam&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );

?>
  <tr id="comment-<?php echo $comment->comment_ID; ?>" class='<?php echo $the_comment_status; ?>'>
<?php if ( $checkbox ) : ?>
    <td class="check-column"><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
<?php endif; ?>
    <td class="comment-column">
    <?php if ( 'detail' == $mode || 'single' == $mode ) comment_text(); ?>
    
<?php
	$actions = array();

	if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		$actions['approve']   = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=approved vim-a' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
		$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3:new=unapproved vim-u' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
		if ( $comment_status ) { // not looking at all comments
			if ( 'approved' == $the_comment_status ) {
				$actions['unapprove'] = "<a href='$unapprove_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment vim-u vim-destructive' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a>';
				unset($actions['approve']);
			} else {
				$actions['approve'] = "<a href='$approve_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment vim-a vim-destructive' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a>';
				unset($actions['unapprove']);
			}
		}
		if ( 'spam' != $the_comment_status )
			$actions['spam']      = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1 vim-s vim-destructive' title='" . __( 'Mark this comment as spam' ) . "'>" . __( 'Spam' ) . '</a>';
		$actions['delete'] = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID delete vim-d vim-destructive'>" . __('Delete') . '</a>';
		$actions['edit'] = "<a href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . __('Edit comment') . "'>". __('Edit') . '</a>';
		if ( 'spam' != $the_comment_status )
			$actions['reply'] = '<a onclick="commentReply.open(\''.$comment->comment_ID.'\',\''.$post->ID.'\',this);return false;" class="vim-r" title="'.__('Reply to this comment').'" href="#">' . __('Reply') . '</a>';

		$actions = apply_filters( 'comment_row_actions', $actions, $comment );

		$action_count = count($actions);
		$i = 0;
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			// The action before reply shouldn't output a sep
			if ( 'edit' == $action )
				$sep = '';
			// Reply needs a hide-if-no-js span
			if ( 'reply' == $action )
				echo "<span class='$action'><span class='hide-if-no-js'> | $link</span>$sep</span>";
			else
				echo "<span class='$action'>$link$sep</span>";
		}
	}
	?>
    </td>
	<td class="author-column">
		<strong><?php comment_author(); ?></strong><br />
	    <?php if ( !empty($author_url) ) : ?>
	    <a href="<?php echo $author_url ?>"><?php echo $author_url_display; ?></a><br />
	    <?php endif; ?>
	    <?php if ( current_user_can( 'edit_post', $post->ID ) ) : ?>
	    <?php if ( !empty($comment->comment_author_email) ): ?>
	    <?php comment_author_email_link() ?><br />
	    <?php endif; ?>
	    <a href="edit-comments.php?s=<?php comment_author_IP() ?>&amp;mode=detail"><?php comment_author_IP() ?></a>
		<?php endif; //current_user_can?>    
	</td>
    <td class="date-column"><?php comment_date(__('Y/m/d \a\t g:ia')); ?></td>
<?php if ( 'single' !== $mode ) : ?>
    <td class="response-column">
    "<?php echo $post_link ?>" <?php echo sprintf('(%s comments)', $post->comment_count); ?><br />
    <?php echo get_the_time(__('Y/m/d \a\t g:ia')); ?>
    </td>
<?php endif; ?>
  </tr>
	<?php
}

function wp_comment_reply($position = '1', $checkbox = false, $mode = 'single') {
	global $current_user;

	// allow plugin to replace the popup content
	$content = apply_filters( 'wp_comment_reply', '', array('position'=>$position, 'checkbox'=>$checkbox, 'mode'=>$mode) );
	
	if ( ! empty($content) ) {
		echo $content;
		return;
	}
?>
	<div id="replyerror" style="display:none;">
	<img src="images/logo.gif" />
	<h3 class="info-box-title"><?php _e('Comment Reply Error'); ?></h3>
	<p id="replyerrtext"></p>
	<p class="submit"><button id="close-button" onclick="commentReply.close();" class="button"><?php _e('Close'); ?></button>
	<button id="back-button" onclick="commentReply.back();" class="button"><?php _e('Go back'); ?></button></p>
	</div>
	
	<div id="replydiv" style="display:none;">
	<p id="replyhandle"><?php _e('Reply'); ?></p>
	<form action="" method="post" id="replyform">
	<input type="hidden" name="user_ID" id="user_ID" value="<?php echo $current_user->ID; ?>" />
	<input type="hidden" name="action" value="replyto-comment" />
	<input type="hidden" name="comment_ID" id="comment_ID" value="" />
	<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
	<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
	<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
	<input type="hidden" name="mode" id="mode" value="<?php echo $mode; ?>" />
	<?php wp_nonce_field( 'replyto-comment', '_ajax_nonce', false ); ?>
	<?php wp_comment_form_unfiltered_html_nonce(); ?>

	<?php echo apply_filters( 'wp_comment_reply_content', '
	<div id="replycontainer"><textarea rows="5" cols="40" name="replycontent" tabindex="1000" id="replycontent"></textarea></div>
	'); ?>

	<p id="replysubmit"><input type="button" onclick="commentReply.close();" class="button" tabindex="1002" value="<?php _e('Cancel'); ?>" />
	<input type="button" onclick="commentReply.send();" class="button" tabindex="1001" value="<?php _e('Submit Reply'); ?>" /></p>
	</form>
	</div>
<?php
}

function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	if (!$categories )
		$categories = get_categories( array('hide_empty' => 0) );

	if ( $categories ) {
		foreach ( $categories as $category ) {
			if ( $currentcat != $category->term_id && $parent == $category->parent) {
				$pad = str_repeat( '&#8211; ', $level );
				$category->name = wp_specialchars( $category->name );
				echo "\n\t<option value='$category->term_id'";
				if ( $currentparent == $category->term_id )
					echo " selected='selected'";
				echo ">$pad$category->name</option>";
				wp_dropdown_cats( $currentcat, $currentparent, $category->term_id, $level +1, $categories );
			}
		}
	} else {
		return false;
	}
}

function list_meta( $meta ) {
	// Exit if no meta
	if (!$meta ) {
		echo '<tbody id="the-list" class="list:meta"><tr style="display: none;"><td>&nbsp;</td></tr></tbody>'; //TBODY needed for list-manipulation JS
		return;
	}
	$count = 0;
?>
	<thead>
	<tr>
		<th><?php _e( 'Key' ) ?></th>
		<th><?php _e( 'Value' ) ?></th>
		<th colspan='2'><?php _e( 'Action' ) ?></th>
	</tr>
	</thead>
	<tbody id='the-list' class='list:meta'>
<?php
	foreach ( $meta as $entry )
		echo _list_meta_row( $entry, $count );
	echo "\n\t</tbody>";
}

function _list_meta_row( $entry, &$count ) {
	static $update_nonce = false;
	if ( !$update_nonce )
		$update_nonce = wp_create_nonce( 'add-meta' );

	$r = '';
	++ $count;
	if ( $count % 2 )
		$style = 'alternate';
	else
		$style = '';
	if ('_' == $entry['meta_key'] { 0 } )
		$style .= ' hidden';

	if ( is_serialized( $entry['meta_value'] ) ) {
		if ( is_serialized_string( $entry['meta_value'] ) ) {
			// this is a serialized string, so we should display it
			$entry['meta_value'] = maybe_unserialize( $entry['meta_value'] );
		} else {
			// this is a serialized array/object so we should NOT display it
			--$count;
			return;
		}
	}

	$entry['meta_key']   = attribute_escape($entry['meta_key']);
	$entry['meta_value'] = htmlspecialchars($entry['meta_value']); // using a <textarea />
	$entry['meta_id'] = (int) $entry['meta_id'];

	$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

	$r .= "\n\t<tr id='meta-{$entry['meta_id']}' class='$style'>";
	$r .= "\n\t\t<td valign='top'><label class='hidden' for='meta[{$entry['meta_id']}][key]'>" . __( 'Key' ) . "</label><input name='meta[{$entry['meta_id']}][key]' id='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>";
	$r .= "\n\t\t<td><label class='hidden' for='meta[{$entry['meta_id']}][value]'>" . __( 'Value' ) . "</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>";
	$r .= "\n\t\t<td style='text-align: center;'><input name='updatemeta' type='submit' tabindex='6' value='".attribute_escape(__( 'Update' ))."' class='add:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$update_nonce updatemeta' /><br />";
	$r .= "\n\t\t<input name='deletemeta[{$entry['meta_id']}]' type='submit' ";
	$r .= "class='delete:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$delete_nonce deletemeta' tabindex='6' value='".attribute_escape(__( 'Delete' ))."' />";
	$r .= wp_nonce_field( 'change-meta', '_ajax_nonce', false, false );
	$r .= "</td>\n\t</tr>";
	return $r;
}

function meta_form() {
	global $wpdb;
	$limit = (int) apply_filters( 'postmeta_form_limit', 30 );
	$keys = $wpdb->get_col( "
		SELECT meta_key
		FROM $wpdb->postmeta
		WHERE meta_key NOT LIKE '\_%'
		GROUP BY meta_key
		ORDER BY meta_id DESC
		LIMIT $limit" );
	if ( $keys )
		natcasesort($keys);
?>
<p><strong><?php _e( 'Add a new custom field:' ) ?></strong></p>
<table id="newmeta" cellspacing="3" cellpadding="3">
	<tr>
<th colspan="2"><label <?php if ( $keys ) : ?> for="metakeyselect" <?php else : ?> for="metakeyinput" <?php endif; ?>><?php _e( 'Key' ) ?></label></th>
<th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
</tr>
	<tr valign="top">
		<td style="width: 18%;" class="textright">
<?php if ( $keys ) : ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#"><?php _e( '- Select -' ); ?></option>
<?php

	foreach ( $keys as $key ) {
		$key = attribute_escape( $key );
		echo "\n\t<option value='$key'>$key</option>";
	}
?>
</select> <label for="metakeyinput"><?php _e( 'or' ); ?></label>
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="8"></textarea></td>
	</tr>
<tr class="submit"><td colspan="3">
	<?php wp_nonce_field( 'add-meta', '_ajax_nonce', false ); ?>
	<input type="submit" id="addmetasub" name="addmeta" class="add:the-list:newmeta" tabindex="9" value="<?php _e( 'Add Custom Field' ) ?>" />
</td></tr>
</table>
<?php

}

function touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale, $post, $comment;

	if ( $for_post )
		$edit = ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date || '0000-00-00 00:00:00' == $post->post_date ) ) ? false : true;

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	// echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

	$time_adj = time() + (get_option( 'gmt_offset' ) * 3600 );
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date( 'd', $post_date ) : gmdate( 'd', $time_adj );
	$mm = ($edit) ? mysql2date( 'm', $post_date ) : gmdate( 'm', $time_adj );
	$aa = ($edit) ? mysql2date( 'Y', $post_date ) : gmdate( 'Y', $time_adj );
	$hh = ($edit) ? mysql2date( 'H', $post_date ) : gmdate( 'H', $time_adj );
	$mn = ($edit) ? mysql2date( 'i', $post_date ) : gmdate( 'i', $time_adj );
	$ss = ($edit) ? mysql2date( 's', $post_date ) : gmdate( 's', $time_adj );

	$month = "<select " . ( $multi ? '' : 'id="mm" ' ) . "name=\"mm\"$tab_index_attribute>\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $wp_locale->get_month( $i ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	$year = '<input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="5"' . $tab_index_attribute . ' autocomplete="off"  />';
	$hour = '<input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	$minute = '<input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	printf(_c('%1$s%2$s, %3$s @ %4$s : %5$s|1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input'), $month, $day, $year, $hour, $minute);

	if ( $multi ) return;
	
	echo "\n\n";
	foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit )
		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
?>

<input type="hidden" id="ss" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" />

<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><?php _e('Cancel'); ?></a>
<?php
}

function page_template_dropdown( $default = '' ) {
	$templates = get_page_templates();
	ksort( $templates );
	foreach (array_keys( $templates ) as $template )
		: if ( $default == $templates[$template] )
			$selected = " selected='selected'";
		else
			$selected = '';
	echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
	endforeach;
}

function parent_dropdown( $default = 0, $parent = 0, $level = 0 ) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' ORDER BY menu_order", $parent) );

	if ( $items ) {
		foreach ( $items as $item ) {
			// A page cannot be its own parent.
			if (!empty ( $post_ID ) ) {
				if ( $item->ID == $post_ID ) {
					continue;
				}
			}
			$pad = str_repeat( '&nbsp;', $level * 3 );
			if ( $item->ID == $default)
				$current = ' selected="selected"';
			else
				$current = '';

			echo "\n\t<option class='level-$level' value='$item->ID'$current>$pad " . wp_specialchars($item->post_title) . "</option>";
			parent_dropdown( $default, $item->ID, $level +1 );
		}
	} else {
		return false;
	}
}

function browse_happy() {
	$getit = __( 'WordPress recommends a better browser' );
	echo '
		<span id="bh" class="alignright"><a href="http://browsehappy.com/" title="'.$getit.'"><img src="images/browse-happy.gif" alt="Browse Happy" /></a></span>
		';
}

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
	add_action( 'in_admin_footer', 'browse_happy' );

function the_attachment_links( $id = false ) {
	$id = (int) $id;
	$post = & get_post( $id );

	if ( $post->post_type != 'attachment' )
		return false;

	$icon = get_attachment_icon( $post->ID );
	$attachment_data = wp_get_attachment_metadata( $id );
	$thumb = isset( $attachment_data['thumb'] );
?>
<form id="the-attachment-links">
<table>
	<col />
	<col class="widefat" />
	<tr>
		<th scope="row"><?php _e( 'URL' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><?php echo wp_get_attachment_url(); ?></textarea></td>
	</tr>
<?php if ( $icon ) : ?>
	<tr>
		<th scope="row"><?php $thumb ? _e( 'Thumbnail linked to file' ) : _e( 'Image linked to file' ); ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo wp_get_attachment_url(); ?>"><?php echo $icon ?></a></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php $thumb ? _e( 'Thumbnail linked to page' ) : _e( 'Image linked to page' ); ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link( $post->ID ) ?>" rel="attachment wp-att-<?php echo $post->ID; ?>"><?php echo $icon ?></a></textarea></td>
	</tr>
<?php else : ?>
	<tr>
		<th scope="row"><?php _e( 'Link to file' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo wp_get_attachment_url(); ?>" class="attachmentlink"><?php echo basename( wp_get_attachment_url() );  ?></a></textarea></td>
	</tr>
	<tr>
		<th scope="row"><?php _e( 'Link to page' ) ?></th>
		<td><textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link( $post->ID ) ?>" rel="attachment wp-att-<?php echo $post->ID ?>"><?php the_title(); ?></a></textarea></td>
	</tr>
<?php endif; ?>
</table>
</form>
<?php
}

function wp_dropdown_roles( $default = false ) {
	global $wp_roles;
	$p = '';
	$r = '';
	foreach( $wp_roles->role_names as $role => $name ) {
		$name = translate_with_context($name);
		if ( $default == $role ) // Make default first in list
			$p = "\n\t<option selected='selected' value='$role'>$name</option>";
		else
			$r .= "\n\t<option value='$role'>$name</option>";
	}
	echo $p . $r;
}

function wp_convert_hr_to_bytes( $size ) {
	$size = strtolower($size);
	$bytes = (int) $size;
	if ( strpos($size, 'k') !== false )
		$bytes = intval($size) * 1024;
	elseif ( strpos($size, 'm') !== false )
		$bytes = intval($size) * 1024 * 1024;
	elseif ( strpos($size, 'g') !== false )
		$bytes = intval($size) * 1024 * 1024 * 1024;
	return $bytes;
}

function wp_convert_bytes_to_hr( $bytes ) {
	$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
	$log = log( $bytes, 1024 );
	$power = (int) $log;
	$size = pow(1024, $log - $power);
	return $size . $units[$power];
}

function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
	$bytes = apply_filters( 'upload_size_limit', min($u_bytes, $p_bytes), $u_bytes, $p_bytes );
	return $bytes;
}

function wp_import_upload_form( $action ) {
	$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
	$size = wp_convert_bytes_to_hr( $bytes );
?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo attribute_escape($action) ?>">
<p>
<?php wp_nonce_field('import-upload'); ?>
<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __('Maximum size: %s' ), $size ); ?>)
<input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
</p>
<p class="submit">
<input type="submit" class="button" value="<?php _e( 'Upload file and import' ); ?>" />
</p>
</form>
<?php
}

function wp_remember_old_slug() {
	global $post;
	$name = attribute_escape($post->post_name); // just in case
	if ( strlen($name) )
		echo '<input type="hidden" id="wp-old-slug" name="wp-old-slug" value="' . $name . '" />';
}

/**
 * add_meta_box() - Add a meta box to an edit form
 *
 * @since 2.5
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $title Title of the meta box
 * @param string $callback Function that fills the box with the desired content.  The function should echo its output.
 * @param string $page The type of edit page on which to show the box (post, page, link)
 * @param string $context The context within the page where the boxes should show ('normal', 'advanced')
 * @param string $priority The priority within the context where the boxes should show ('high', 'low')
 */
function add_meta_box($id, $title, $callback, $page, $context = 'advanced', $priority = 'default') {
	global $wp_meta_boxes;

	if  ( !isset($wp_meta_boxes) )
		$wp_meta_boxes = array();
	if ( !isset($wp_meta_boxes[$page]) )
		$wp_meta_boxes[$page] = array();
	if ( !isset($wp_meta_boxes[$page][$context]) )
		$wp_meta_boxes[$page][$context] = array();

	foreach ( array_keys($wp_meta_boxes[$page]) as $a_context ) {
	foreach ( array('high', 'core', 'default', 'low') as $a_priority ) {
		if ( !isset($wp_meta_boxes[$page][$a_context][$a_priority][$id]) )
			continue;

		// If a core box was previously added or removed by a plugin, don't add.
		if ( 'core' == $priority ) {
			// If core box previously deleted, don't add
			if ( false === $wp_meta_boxes[$page][$a_context][$a_priority][$id] )
				return;
			// If box was added with default priority, give it core priority to maintain sort order
			if ( 'default' == $a_priority ) {
				$wp_meta_boxes[$page][$a_context]['core'][$id] = $wp_meta_boxes[$page][$a_context]['default'][$id];
				unset($wp_meta_boxes[$page][$a_context]['default'][$id]);
			}
			return;
		}
		// If no priority given and id already present, use existing priority
		if ( empty($priority) ) {
			$priority = $a_priority;
		// else if we're adding to the sorted priortiy, we don't know the title or callback.  Glab them from the previously added context/priority.
		} elseif ( 'sorted' == $priority ) {
			$title = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['title'];
			$callback = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['callback'];
		}
		// An id can be in only one priority and one context
		if ( $priority != $a_priority || $context != $a_context )
			unset($wp_meta_boxes[$page][$a_context][$a_priority][$id]);
	}
	}

	if ( empty($priority) )
		$priority = 'low';

	if ( !isset($wp_meta_boxes[$page][$context][$priority]) )
		$wp_meta_boxes[$page][$context][$priority] = array();

	$wp_meta_boxes[$page][$context][$priority][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback);
}

function do_meta_boxes($page, $context, $object) {
	global $wp_meta_boxes;
	static $already_sorted = false;

	do_action('do_meta_boxes', $page, $context, $object);

	$hidden = (array) get_user_option( "meta-box-hidden_$page" );

	echo "<div id='$context-sortables' class='meta-box-sortables'>\n";

	$i = 0;
	do { 
		// Grab the ones the user has manually sorted.  Pull them out of their previous context/priority and into the one the user chose
		if ( !$already_sorted && $sorted = get_user_option( "meta-box-order_$page" ) ) {
			foreach ( $sorted as $box_context => $ids )
				foreach ( explode(',', $ids) as $id )
					if ( $id )
						add_meta_box( $id, null, null, $page, $box_context, 'sorted' );
		}
		$already_sorted = true;

		if ( !isset($wp_meta_boxes) || !isset($wp_meta_boxes[$page]) || !isset($wp_meta_boxes[$page][$context]) )
			break;

		foreach ( array('high', 'sorted', 'core', 'default', 'low') as $priority ) {
			if ( isset($wp_meta_boxes[$page][$context][$priority]) ) {
				foreach ( (array) $wp_meta_boxes[$page][$context][$priority] as $box ) {
					if ( false == $box || ! $box['title'] )
						continue;
					$i++;
					$style = '';
					if ( in_array($box['id'], $hidden) )
						$style = 'style="display:none;"';
					echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes($box['id'], $page) . '" ' . $style . '>' . "\n";
					echo "<h3><span class='hndle'>{$box['title']}</span></h3>\n";
					echo '<div class="inside">' . "\n";
					call_user_func($box['callback'], $object, $box);
					echo "</div>\n";
					echo "</div>\n";
				}
			}
		}
	} while(0);

	echo "</div>";

	return $i;

}

/**
 * remove_meta_box() - Remove a meta box from an edit form
 *
 * @since 2.6
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $page The type of edit page on which to show the box (post, page, link)
 * @param string $context The context within the page where the boxes should show ('normal', 'advanced')
 */
function remove_meta_box($id, $page, $context) {
	global $wp_meta_boxes;

	if  ( !isset($wp_meta_boxes) )
		$wp_meta_boxes = array();
	if ( !isset($wp_meta_boxes[$page]) )
		$wp_meta_boxes[$page] = array();
	if ( !isset($wp_meta_boxes[$page][$context]) )
		$wp_meta_boxes[$page][$context] = array();

	foreach ( array('high', 'core', 'default', 'low') as $priority )
		$wp_meta_boxes[$page][$context][$priority][$id] = false;
}

function meta_box_prefs($page) {
	global $wp_meta_boxes;

	if ( empty($wp_meta_boxes[$page]) )
		return;

	$hidden = (array) get_user_option( "meta-box-hidden_$page" );

	foreach ( array_keys($wp_meta_boxes[$page]) as $context ) {
		foreach ( array_keys($wp_meta_boxes[$page][$context]) as $priority ) {
			foreach ( $wp_meta_boxes[$page][$context][$priority] as $box ) {
				if ( false == $box || ! $box['title'] )
					continue;
				// Submit box cannot be hidden
				if ( 'submitdiv' == $box['id'] )
					continue;
				$box_id = $box['id'];
				echo '<label for="' . $box_id . '-hide">';
				echo '<input class="hide-postbox-tog" name="' . $box_id . '-hide" type="checkbox" id="' . $box_id . '-hide" value="' . $box_id . '"' . (! in_array($box_id, $hidden) ? ' checked="checked"' : '') . ' />';
				echo "{$box['title']}</label>\n";
			}
		}
	}
}

/**
 * Add a new section to a settings page
 *
 * @since 2.7
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $title Title of the section
 * @param string $callback Function that fills the section with the desired content.  The function should echo its output.
 * @param string $page The type of settings page on which to show the section (general, reading, writing, ...)
 */
function add_settings_section($id, $title, $callback, $page) {
	global $wp_settings_sections;

	if  ( !isset($wp_settings_sections) )
		$wp_settings_sections = array();
	if ( !isset($wp_settings_sections[$page]) )
		$wp_settings_sections[$page] = array();
	if ( !isset($wp_settings_sections[$page][$id]) )
		$wp_settings_sections[$page][$id] = array();

	$wp_settings_sections[$page][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback);
}

/**
 * Add a new field to a settings page
 *
 * @since 2.7
 *
 * @param string $id String for use in the 'id' attribute of tags.
 * @param string $title Title of the field
 * @param string $callback Function that fills the field with the desired content.  The function should echo its output.
 * @param string $page The type of settings page on which to show the field (general, reading, writing, ...)
 * @param string $section The section of the settingss page in which to show the box (default, ...)
 * @param array $args Additional arguments
 */
function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
	global $wp_settings_fields;

	if  ( !isset($wp_settings_fields) )
		$wp_settings_fields = array();
	if ( !isset($wp_settings_fields[$page]) )
		$wp_settings_fields[$page] = array();
	if ( !isset($wp_settings_fields[$page][$section]) )
		$wp_settings_fields[$page][$section] = array();

	$wp_settings_fields[$page][$section][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $args);
}

function do_settings_sections($page) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		echo "<h3>{$section['title']}</h3>\n";
		call_user_func($section['callback'], $section);
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
			continue;
		echo '<table class="form-table">';
		do_settings_fields($page, $section['id']);
		echo '</table>';
	}
}

function do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		echo '<tr valign="top">';
		if ( !empty($field['args']['label_for']) )
			echo '<th scope="row"><label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label></th>';
		else
			echo '<th scope="row">' . $field['title'] . '</th>';
		echo '<td>';
		call_user_func($field['callback']);
		echo '</td>';
		echo '</tr>';
	}	
}

function manage_columns_prefs($page) {
	if ( 'post' == $page )
		$columns = wp_manage_posts_columns();
	elseif ( 'page' == $page )
		$columns = wp_manage_pages_columns();
	elseif ( 'link' == $page )
		$columns = wp_manage_links_columns();
	elseif ( 'media' == $page )
		$columns = wp_manage_media_columns();
	else return;

	$hidden = (array) get_user_option( "manage-$page-columns-hidden" );

	foreach ( $columns as $column => $title ) {
		// Can't hide these
		if ( 'cb' == $column || 'title' == $column || 'name' == $column )
			continue;
		if ( 'comments' == $column )
			$title = __('Comments');
		$id = "$column-hide";
		echo '<label for="' . $id . '">';
		echo '<input class="hide-column-tog" name="' . $id . '" type="checkbox" id="' . $id . '" value="' . $column . '"' . (! in_array($column, $hidden) ? ' checked="checked"' : '') . ' />';
		echo "$title</label>\n";
	}
}

?>
