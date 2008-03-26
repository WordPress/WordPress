<?php

//
// Big Mess
//

// Dandy new recursive multiple category stuff.
function cat_rows( $parent = 0, $level = 0, $categories = 0 ) {
	if ( !$categories ) {
		$args = array('hide_empty' => 0);
		if ( !empty($_GET['s']) )
			$args['search'] = $_GET['s'];
		$categories = get_categories( $args );
	}

	$children = _get_term_hierarchy('category');

	if ( $categories ) {
		ob_start();
		foreach ( $categories as $category ) {
			if ( $category->parent == $parent) {
				echo "\t" . _cat_row( $category, $level );
				if ( isset($children[$category->term_id]) )
					cat_rows( $category->term_id, $level +1, $categories );
			}
		}
		$output = ob_get_contents();
		ob_end_clean();

		$output = apply_filters('cat_rows', $output);

		echo $output;
	} else {
		return false;
	}
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
		$output .= "<input type='checkbox' name='delete[]' value='$category->term_id' /></th>";
	} else {
		$output .= "&nbsp;";
	}
	$output .= "<td>$edit</td>
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
		$edit = "<a class='row-title' href='link-category.php?action=edit&amp;cat_ID=$category->term_id' title='" . attribute_escape(sprintf(__('Edit "%s"'), $category->name)) . "' class='edit'>$name</a>";
		$default_cat_id = (int) get_option( 'default_link_category' );
	} else {
		$edit = $name;
	}

	$class = " class='alternate'" == $class ? '' : " class='alternate'";

	$category->count = number_format_i18n( $category->count );
	$count = ( $category->count > 0 ) ? "<a href='link-manager.php?cat_id=$category->term_id'>$category->count</a>" : $category->count;
	$output = "<tr id='link-cat-$category->term_id'$class>" .
		'<th scope="row" class="check-column"> <input type="checkbox" name="delete[]" value="' . $category->term_id . '" /></th>' .
		"<td>$edit</td>
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
// Nasty Category Stuff
//

function sort_cats( $cat1, $cat2 ) {
	if ( $cat1['checked'] || $cat2['checked'] )
		return ( $cat1['checked'] && !$cat2['checked'] ) ? -1 : 1;
	else
		return strcasecmp( $cat1['cat_name'], $cat2['cat_name'] );
}

function wp_set_checked_post_categories( $default = 0 ) {
	global $post_ID, $checked_categories;

	if ( empty($checked_categories) ) {
		if ( $post_ID ) {
			$checked_categories = wp_get_post_categories($post_ID);

			if ( count( $checked_categories ) == 0 ) {
				// No selected categories, strange
			$checked_categories[] = $default;
			}
		} else {
			$checked_categories[] = $default;
		}
	}

}
function get_nested_categories( $default = 0, $parent = 0 ) {
	global $checked_categories;

	wp_set_checked_post_categories( $default = 0 );

	if ( is_object($parent) ) { // Hack: if passed a category object, will return nested cats with parent as root
		$root = array(
			'children' => get_nested_categories( $default, $parent->term_id ),
			'cat_ID' => $parent->term_id,
			'checked' => in_array( $parent->term_id, $checked_categories ),
			'cat_name' => get_the_category_by_ID( $parent->term_id )
		);
		$result = array( $parent->term_id => $root );
	} else {
		$parent = (int) $parent;

		$cats = get_categories("parent=$parent&hide_empty=0&fields=ids");

		$result = array();
		if ( is_array( $cats ) ) {
			foreach ( $cats as $cat ) {
				$result[$cat]['children'] = get_nested_categories( $default, $cat );
				$result[$cat]['cat_ID'] = $cat;
				$result[$cat]['checked'] = in_array( $cat, $checked_categories );
				$result[$cat]['cat_name'] = get_the_category_by_ID( $cat );
			}
		}
	}

	$result = apply_filters('get_nested_categories', $result);
	usort( $result, 'sort_cats' );

	return $result;
}

function write_nested_categories( $categories ) {
	foreach ( $categories as $category ) {
		echo "\n", '<li id="category-', $category['cat_ID'], '"><label for="in-category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'], '" type="checkbox" name="post_category[]" id="in-category-', $category['cat_ID'], '"', ($category['checked'] ? ' checked="checked"' : "" ), '/> ', wp_specialchars( apply_filters('the_category', $category['cat_name'] )), '</label>';

		if ( $category['children'] ) {
			echo "\n<ul>";
			write_nested_categories( $category['children'] );
			echo "\n</ul>";
		}
		echo '</li>';
	}
}

function dropdown_categories( $default = 0, $parent = 0 ) {
	write_nested_categories( get_nested_categories( $default, $parent ) );
}

function wp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10 ) {
	$categories = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number ) );

	foreach ( (array) $categories as $category ) {
		$id = "popular-category-$category->term_id";
		?>

		<li id="<?php echo $id; ?>" >
			<label class="selectit" for="in-<?php echo $id; ?>">
			<input id="in-<?php echo $id; ?>" type="checkbox" value="<?php echo (int) $category->term_id; ?>" />
				<?php echo wp_specialchars( apply_filters( 'the_category', $category->name ) ); ?>
			</label>
		</li>

		<?php
	}
}

function dropdown_link_categories( $default = 0 ) {
	global $link_id;

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
			$name . '</a></td>';

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
	$posts_columns['cb'] = '<input type="checkbox" onclick="checkAll(document.getElementById(\'posts-filter\'));" />';
	if ( 'draft' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Modified');
	elseif ( 'pending' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Submitted');
	else
		$posts_columns['date'] = __('Date');
	$posts_columns['title'] = __('Title');
	$posts_columns['author'] = __('Author');
	$posts_columns['categories'] = __('Categories');
	$posts_columns['tags'] = __('Tags');
	if ( !in_array($_GET['post_status'], array('pending', 'draft', 'future')) )
		$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div>';
	$posts_columns['status'] = __('Status');
	$posts_columns = apply_filters('manage_posts_columns', $posts_columns);

	return $posts_columns;
}

// define the columns to display, the syntax is 'internal name' => 'display name'
function wp_manage_media_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" onclick="checkAll(document.getElementById(\'posts-filter\'));" />';
	$posts_columns['icon'] = '';
	$posts_columns['media'] = _c('Media|media column header');
	$posts_columns['desc'] = _c('Description|media column header');
	$posts_columns['date'] = _c('Date Added|media column header');
	$posts_columns['parent'] = _c('Appears with|media column header');
	$posts_columns['comments'] = '<div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div>';
	$posts_columns['location'] = _c('Location|media column header');
	$posts_columns = apply_filters('manage_media_columns', $posts_columns);

	return $posts_columns;
}

function wp_manage_pages_columns() {
	$posts_columns = array();
	$posts_columns['cb'] = '<input type="checkbox" onclick="checkAll(document.getElementById(\'posts-filter\'));" />';
	if ( 'draft' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Modified');
	elseif ( 'pending' === $_GET['post_status'] )
		$posts_columns['modified'] = __('Submitted');
	else
		$posts_columns['date'] = __('Date');
	$posts_columns['title'] = __('Title');
	$posts_columns['author'] = __('Author');
	if ( !in_array($_GET['post_status'], array('pending', 'draft', 'future')) )
		$posts_columns['comments'] = '<div class="vers"><img alt="" src="images/comment-grey-bubble.png" /></div>';
	$posts_columns['status'] = __('Status');
	$posts_columns = apply_filters('manage_pages_columns', $posts_columns);

	return $posts_columns;
}

/*
 * display one row if the page doesn't have any children
 * otherwise, display the row and its children in subsequent rows
 */
function display_page_row( $page, &$children_pages, $level = 0 ) {
	global $post;
	static $class;

	$post = $page;
	setup_postdata($page);

	$page->post_title = wp_specialchars( $page->post_title );
	$pad = str_repeat( '&#8212; ', $level );
	$id = (int) $page->ID;
	$class = ('alternate' == $class ) ? '' : 'alternate';
	$posts_columns = wp_manage_pages_columns();
	$title = get_the_title();
	if ( empty($title) )
		$title = __('(no title)');
?>
  <tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'>


 <?php

foreach ($posts_columns as $column_name=>$column_display_name) {

	switch ($column_name) {

	case 'cb':
		?>
		<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php the_ID(); ?>" /></th>
		<?php
		break;
	case 'modified':
	case 'date':
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
		<td><abbr title="<?php echo $t_time ?>"><?php echo $h_time ?></abbr></td>
		<?php
		break;
	case 'title':
		?>
		<td><strong><a class="row-title" href="page.php?action=edit&amp;post=<?php the_ID(); ?>" title="<?php echo attribute_escape(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo $pad; echo $title ?></a></strong>
		<?php if ('private' == $page->post_status) _e(' &#8212; <strong>Private</strong>'); ?></td>
		<?php
		break;

	case 'comments':
		?>
		<td class="num"><div class="post-com-count-wrapper">
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
		<td><a href="edit-pages.php?author=<?php the_author_ID(); ?>"><?php the_author() ?></a></td>
		<?php
		break;

	case 'status':
		?>
		<td>
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

	if ( ! $children_pages )
		return true;

	for ( $i = 0; $i < count($children_pages); $i++ ) {

		$child = $children_pages[$i];

		if ( $child->post_parent == $id ) {
			array_splice($children_pages, $i, 1);
			display_page_row($child, $children_pages, $level+1);
			$i = -1; //as numeric keys in $children_pages are not preserved after splice
		}
	}
}

/*
 * displays pages in hierarchical order
 */
function page_rows( $pages ) {
	if ( ! $pages )
		$pages = get_pages( 'sort_column=menu_order' );

	if ( ! $pages )
		return false;

	// splice pages into two parts: those without parent and those with parent

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
			$children_pages[] = $page;
	}

	foreach ( $top_level_pages as $page )
		display_page_row($page, $children_pages, 0);

	/*
	 * display the remaining children_pages which are orphans
	 * having orphan requires parental attention
	 */
	 if ( count($children_pages) > 0 ) {
	 	$empty_array = array();
	 	foreach ( $children_pages as $orphan_page ) {
			clean_page_cache( $orphan_page->ID);
			display_page_row( $orphan_page, $empty_array, 0 );
		}
	 }
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
	$role_name = translate_with_context($wp_roles->role_names[$role]);
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
		$approved = "comment_approved != 'spam'";

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
	$class = ('unapproved' == $the_comment_status) ? 'unapproved' : '';

	if ( current_user_can( 'edit_post', $post->ID ) ) {
		$post_link = "<a href='" . get_comment_link() . "'>";

		$post_link .= get_the_title($comment->comment_post_ID) . '</a>';
			
		$edit_link_start = "<a class='row-title' href='comment.php?action=editcomment&amp;c={$comment->comment_ID}' title='" . __('Edit comment') . "'>";
		$edit_link_end = '</a>';
	} else {
		$post_link = get_the_title($comment->comment_post_ID);
		$edit_link_start = $edit_link_end ='';
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
  <tr id="comment-<?php echo $comment->comment_ID; ?>" class='<?php echo $class; ?>'>
<?php if ( $checkbox ) : ?>
    <td class="check-column"><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
<?php endif; ?>
    <td class="comment">
    <p class="comment-author"><strong><?php echo $edit_link_start; comment_author(); echo $edit_link_end; ?></strong><br />
    <?php if ( !empty($author_url) ) : ?>
    <a href="<?php echo $author_url ?>"><?php echo $author_url_display; ?></a> |
    <?php endif; ?>
    <?php if ( current_user_can( 'edit_post', $post->ID ) ) : ?>
    <?php if ( !empty($comment->comment_author_email) ): ?>
    <?php comment_author_email_link() ?> |
    <?php endif; ?>
    <a href="edit-comments.php?s=<?php comment_author_IP() ?>&amp;mode=detail"><?php comment_author_IP() ?></a>
	<?php endif; //current_user_can?>    
    </p>
   	<?php if ( 'detail' == $mode ) comment_text(); ?>
   	<p><?php printf(__('From %1$s, %2$s'), $post_link, $ptime) ?></p>
    </td>
    <td><?php comment_date(__('Y/m/d')); ?></td>
    <td class="action-links">
<?php

	$actions = array();

		$actions['approve']   = "<a href='$approve_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a> | ';
		$actions['unapprove'] = "<a href='$unapprove_url' class='dim:the-comment-list:comment-$comment->comment_ID:unapproved:e7e7d3:e7e7d3' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a> | ';

		// we're looking at list of only approved or only unapproved comments
		if ( 'moderated' == $comment_status ) {
			$actions['approve'] = "<a href='$approve_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment' title='" . __( 'Approve this comment' ) . "'>" . __( 'Approve' ) . '</a> | ';
			unset($actions['unapprove']);
		} elseif ( 'approved' == $comment_status ) {
			$actions['unapprove'] = "<a href='$unapprove_url' class='delete:the-comment-list:comment-$comment->comment_ID:e7e7d3:action=dim-comment' title='" . __( 'Unapprove this comment' ) . "'>" . __( 'Unapprove' ) . '</a> | ';
			unset($actions['approve']);
		}

	if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		$actions['spam']      = "<a href='$spam_url' class='delete:the-comment-list:comment-$comment->comment_ID::spam=1' title='" . __( 'Mark this comment as spam' ) . "'>" . __( 'Spam' ) . '</a> | ';
		$actions['delete']    = "<a href='$delete_url' class='delete:the-comment-list:comment-$comment->comment_ID delete'>" . __('Delete') . '</a>';
		foreach ( $actions as $action => $link )
			echo "<span class='$action'>$link</span>";
	}
	?>
	</td>
  </tr>
	<?php
}

function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	if (!$categories )
		$categories = get_categories( 'hide_empty=0' );

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
	$r .= "\n\t\t<td valign='top'><input name='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>";
	$r .= "\n\t\t<td><textarea name='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>";
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
<th colspan="2"><?php _e( 'Key' ) ?></th>
<th><?php _e( 'Value' ) ?></th>
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
</select> <?php _e( 'or' ); ?>
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="8"></textarea></td>
	</tr>
<tr class="submit"><td colspan="3">
	<?php wp_nonce_field( 'add-meta', '_ajax_nonce', false ); ?>
	<input type="submit" id="addmetasub" name="addmeta" class="add:the-list:newmeta::post_id=<?php echo $GLOBALS['post_ID'] ? $GLOBALS['post_ID'] : $GLOBALS['temp_ID']; ?>" tabindex="9" value="<?php _e( 'Add Custom Field' ) ?>" />
</td></tr>
</table>
<?php

}

function touch_time( $edit = 1, $for_post = 1, $tab_index = 0 ) {
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

	$month = "<select id=\"mm\" name=\"mm\"$tab_index_attribute>\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $wp_locale->get_month( $i ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" id="jj" name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	$year = '<input type="text" id="aa" name="aa" value="' . $aa . '" size="4" maxlength="5"' . $tab_index_attribute . ' autocomplete="off"  />';
	$hour = '<input type="text" id="hh" name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	$minute = '<input type="text" id="mn" name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off"  />';
	printf(_c('%1$s%2$s, %3$s <br />@ %4$s : %5$s|1: month input, 2: day input, 3: year input, 4: hour input, 5: minute input'), $month, $day, $year, $hour, $minute);
	echo "\n\n";
	foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit )
		echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
?>

<input type="hidden" id="ss" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" />
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
	$items = $wpdb->get_results( "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_type = 'page' ORDER BY menu_order" );

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

			echo "\n\t<option value='$item->ID'$current>$pad " . wp_specialchars($item->post_title) . "</option>";
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
 */
function add_meta_box($id, $title, $callback, $page, $context = 'advanced') {
	global $wp_meta_boxes;

	if  ( !isset($wp_meta_boxes) )
		$wp_meta_boxes = array();
	if ( !isset($wp_meta_boxes[$page]) )
		$wp_meta_boxes[$page] = array();
	if ( !isset($wp_meta_boxes[$page][$context]) )
		$wp_meta_boxes[$page][$context] = array();

	$wp_meta_boxes[$page][$context][] = array('id' => $id, 'title' => $title, 'callback' => $callback);
}

function do_meta_boxes($page, $context, $object) {
	global $wp_meta_boxes;

	if ( !isset($wp_meta_boxes) || !isset($wp_meta_boxes[$page]) || !isset($wp_meta_boxes[$page][$context]) )
		return;

	foreach ( (array) $wp_meta_boxes[$page][$context] as $box ) {
		echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes($box['id'], $page) . '">' . "\n";
		echo "<h3>{$box['title']}</h3>\n";
		echo '<div class="inside">' . "\n";
		call_user_func($box['callback'], $object, $box);
		echo "</div>\n";
		echo "</div>\n";
	}
}

?>
