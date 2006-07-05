<?php

// Creates a new post from the "Write Post" form using $_POST information.
function write_post() {
	global $user_ID;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can('edit_pages') )
			wp_die(__('You are not allowed to create pages on this blog.'));
	} else {
		if ( !current_user_can('edit_posts') )
			wp_die(__('You are not allowed to create posts or drafts on this blog.'));
	}

	// Rename.
	$_POST['post_content'] = $_POST['content'];
	$_POST['post_excerpt'] = $_POST['excerpt'];
	$_POST['post_parent'] = $_POST['parent_id'];
	$_POST['to_ping'] = $_POST['trackback_url'];

	if (!empty ($_POST['post_author_override'])) {
		$_POST['post_author'] = (int) $_POST['post_author_override'];
	} else {
		if (!empty ($_POST['post_author'])) {
			$_POST['post_author'] = (int) $_POST['post_author'];
		} else {
			$_POST['post_author'] = (int) $_POST['user_ID'];
		}

	}

	if ($_POST['post_author'] != $_POST['user_ID']) {
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can('edit_others_pages') )
				wp_die(__('You cannot create pages as this user.'));
		} else {
			if ( !current_user_can('edit_others_posts') )
				wp_die(__('You cannot post as this user.'));

		}
	}

	// What to do based on which button they pressed
	if ('' != $_POST['saveasdraft'])
		$_POST['post_status'] = 'draft';
	if ('' != $_POST['saveasprivate'])
		$_POST['post_status'] = 'private';
	if ('' != $_POST['publish'])
		$_POST['post_status'] = 'publish';
	if ('' != $_POST['advanced'])
		$_POST['post_status'] = 'draft';

	if ( 'page' == $_POST['post_type'] ) {
		if ('publish' == $_POST['post_status'] && !current_user_can('publish_pages'))
			$_POST['post_status'] = 'draft';
	} else {
		if ('publish' == $_POST['post_status'] && !current_user_can('publish_posts'))
			$_POST['post_status'] = 'draft';
	}

	if (!isset ($_POST['comment_status']))
		$_POST['comment_status'] = 'closed';

	if (!isset ($_POST['ping_status']))
		$_POST['ping_status'] = 'closed';

	if (!empty ($_POST['edit_date'])) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh -24 : $hh;
		$mn = ($mn > 59) ? $mn -60 : $mn;
		$ss = ($ss > 59) ? $ss -60 : $ss;
		$_POST['post_date'] = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss);
		$_POST['post_date_gmt'] = get_gmt_from_date($_POST['post_date']);
	}

	// Create the post.
	$post_ID = wp_insert_post($_POST);
	add_meta($post_ID);

	// Reunite any orphaned attachments with their parent
	if ( $_POST['temp_ID'] )
		relocate_children($_POST['temp_ID'], $post_ID);

	// Now that we have an ID we can fix any attachment anchor hrefs
	fix_attachment_links($post_ID);

	return $post_ID;
}

// Move child posts to a new parent
function relocate_children($old_ID, $new_ID) {
	global $wpdb;
	$old_ID = (int) $old_ID;
	$new_ID = (int) $new_ID;
	return $wpdb->query("UPDATE $wpdb->posts SET post_parent = $new_ID WHERE post_parent = $old_ID");
}

// Replace hrefs of attachment anchors with up-to-date permalinks.
function fix_attachment_links($post_ID) {
	global $wp_rewrite;

	$post = & get_post($post_ID, ARRAY_A);

	$search = "#<a[^>]+rel=('|\")[^'\"]*attachment[^>]*>#ie";

	// See if we have any rel="attachment" links
	if ( 0 == preg_match_all($search, $post['post_content'], $anchor_matches, PREG_PATTERN_ORDER) )
		return;

	$i = 0;
	$search = "# id=(\"|')p(\d+)\\1#i";
	foreach ( $anchor_matches[0] as $anchor ) {
		if ( 0 == preg_match($search, $anchor, $id_matches) )
			continue;

		$id = $id_matches[2];

		// While we have the attachment ID, let's adopt any orphans.
		$attachment = & get_post($id, ARRAY_A);
		if ( ! empty($attachment) && ! is_object(get_post($attachment['post_parent'])) ) {
			$attachment['post_parent'] = $post_ID;
			// Escape data pulled from DB.
			$attachment = add_magic_quotes($attachment);
			wp_update_post($attachment);
		}

		$post_search[$i] = $anchor;
		$post_replace[$i] = preg_replace("#href=(\"|')[^'\"]*\\1#e", "stripslashes('href=\\1').get_attachment_link($id).stripslashes('\\1')", $anchor);
		++$i;
	}

	$post['post_content'] = str_replace($post_search, $post_replace, $post['post_content']);

	// Escape data pulled from DB.
	$post = add_magic_quotes($post);

	return wp_update_post($post);
}

// Update an existing post with values provided in $_POST.
function edit_post() {
	global $user_ID;

	$post_ID = (int) $_POST['post_ID'];

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can('edit_page', $post_ID) )
			wp_die(__('You are not allowed to edit this page.'));
	} else {
		if ( !current_user_can('edit_post', $post_ID) )
			wp_die(__('You are not allowed to edit this post.'));
	}

	// Rename.
	$_POST['ID'] = (int) $_POST['post_ID'];
	$_POST['post_content'] = $_POST['content'];
	$_POST['post_excerpt'] = $_POST['excerpt'];
	$_POST['post_parent'] = $_POST['parent_id'];
	$_POST['to_ping'] = $_POST['trackback_url'];

	if (!empty ($_POST['post_author_override'])) {
		$_POST['post_author'] = (int) $_POST['post_author_override'];
	} else
		if (!empty ($_POST['post_author'])) {
			$_POST['post_author'] = (int) $_POST['post_author'];
		} else {
			$_POST['post_author'] = (int) $_POST['user_ID'];
		}

	if ($_POST['post_author'] != $_POST['user_ID']) {
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can('edit_others_pages') )
				wp_die(__('You cannot edit pages as this user.'));
		} else {
			if ( !current_user_can('edit_others_posts') )
				wp_die(__('You cannot edit posts as this user.'));

		}
	}

	// What to do based on which button they pressed
	if ('' != $_POST['saveasdraft'])
		$_POST['post_status'] = 'draft';
	if ('' != $_POST['saveasprivate'])
		$_POST['post_status'] = 'private';
	if ('' != $_POST['publish'])
		$_POST['post_status'] = 'publish';
	if ('' != $_POST['advanced'])
		$_POST['post_status'] = 'draft';

	if ( 'page' == $_POST['post_type'] ) {
		if ('publish' == $_POST['post_status'] && !current_user_can('edit_published_pages'))
			$_POST['post_status'] = 'draft';
	} else {
		if ('publish' == $_POST['post_status'] && !current_user_can('edit_published_posts'))
			$_POST['post_status'] = 'draft';
	}

	if (!isset ($_POST['comment_status']))
		$_POST['comment_status'] = 'closed';

	if (!isset ($_POST['ping_status']))
		$_POST['ping_status'] = 'closed';

	if (!empty ($_POST['edit_date'])) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh -24 : $hh;
		$mn = ($mn > 59) ? $mn -60 : $mn;
		$ss = ($ss > 59) ? $ss -60 : $ss;
		$_POST['post_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
		$_POST['post_date_gmt'] = get_gmt_from_date("$aa-$mm-$jj $hh:$mn:$ss");
	}

	// Meta Stuff
	if ($_POST['meta']) {
		foreach ($_POST['meta'] as $key => $value)
			update_meta($key, $value['key'], $value['value']);
	}

	if ($_POST['deletemeta']) {
		foreach ($_POST['deletemeta'] as $key => $value)
			delete_meta($key);
	}

	add_meta($post_ID);

	wp_update_post($_POST);

	// Now that we have an ID we can fix any attachment anchor hrefs
	fix_attachment_links($post_ID);

	return $post_ID;
}

function edit_comment() {
	global $user_ID;

	$comment_ID = (int) $_POST['comment_ID'];
	$comment_post_ID = (int) $_POST['comment_post_ID'];

	if (!current_user_can('edit_post', $comment_post_ID))
		wp_die(__('You are not allowed to edit comments on this post, so you cannot edit this comment.'));

	$_POST['comment_author'] = $_POST['newcomment_author'];
	$_POST['comment_author_email'] = $_POST['newcomment_author_email'];
	$_POST['comment_author_url'] = $_POST['newcomment_author_url'];
	$_POST['comment_approved'] = $_POST['comment_status'];
	$_POST['comment_content'] = $_POST['content'];
	$_POST['comment_ID'] = (int) $_POST['comment_ID'];

	if (!empty ($_POST['edit_date'])) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh -24 : $hh;
		$mn = ($mn > 59) ? $mn -60 : $mn;
		$ss = ($ss > 59) ? $ss -60 : $ss;
		$_POST['comment_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
	}

	wp_update_comment($_POST);
}

// Get an existing post and format it for editing.
function get_post_to_edit($id) {
	global $richedit;
	$richedit = ( 'true' == get_user_option('rich_editing') ) ? true : false;

	$post = get_post($id);

	$post->post_content = format_to_edit($post->post_content, $richedit);
	$post->post_content = apply_filters('content_edit_pre', $post->post_content);

	$post->post_excerpt = format_to_edit($post->post_excerpt);
	$post->post_excerpt = apply_filters('excerpt_edit_pre', $post->post_excerpt);

	$post->post_title = format_to_edit($post->post_title);
	$post->post_title = apply_filters('title_edit_pre', $post->post_title);

	if ($post->post_type == 'page')
		$post->page_template = get_post_meta($id, '_wp_page_template', true);

	return $post;
}

// Default post information to use when populating the "Write Post" form.
function get_default_post_to_edit() {
	if ( !empty($_REQUEST['post_title']) )
		$post_title = wp_specialchars(stripslashes($_REQUEST['post_title']));
	else if ( !empty($_REQUEST['popuptitle']) ) {
		$post_title = wp_specialchars(stripslashes($_REQUEST['popuptitle']));
		$post_title = funky_javascript_fix($post_title);
	} else {
		$post_title = '';
	}

	if ( !empty($_REQUEST['content']) )
		$post_content = wp_specialchars(stripslashes($_REQUEST['content']));
	else if ( !empty($post_title) ) {
		$text       = wp_specialchars(stripslashes(urldecode($_REQUEST['text'])));
		$text       = funky_javascript_fix($text);
		$popupurl   = wp_specialchars($_REQUEST['popupurl']);
        $post_content = '<a href="'.$popupurl.'">'.$post_title.'</a>'."\n$text";
    }

	if ( !empty($_REQUEST['excerpt']) )
		$post_excerpt = wp_specialchars(stripslashes($_REQUEST['excerpt']));
	else
		$post_excerpt = '';

	$post->post_status = 'draft';
	$post->comment_status = get_settings('default_comment_status');
	$post->ping_status = get_settings('default_ping_status');
	$post->post_pingback = get_settings('default_pingback_flag');
	$post->post_category = get_settings('default_category');
	$post->post_content = apply_filters('default_content', $post_content);
	$post->post_title = apply_filters('default_title', $post_title);
	$post->post_excerpt = apply_filters('default_excerpt', $post_excerpt);
	$post->page_template = 'default';
	$post->post_parent = 0;
	$post->menu_order = 0;

	return $post;
}

function get_comment_to_edit($id) {
	global $richedit;
	$richedit = ( 'true' == get_user_option('rich_editing') ) ? true : false;

	$comment = get_comment($id);

	$comment->comment_content = format_to_edit($comment->comment_content, $richedit);
	$comment->comment_content = apply_filters('comment_edit_pre', $comment->comment_content);

	$comment->comment_author = format_to_edit($comment->comment_author);
	$comment->comment_author_email = format_to_edit($comment->comment_author_email);
	$comment->comment_author_url = format_to_edit($comment->comment_author_url);

	return $comment;
}

function get_category_to_edit($id) {
	$category = get_category($id);

	return $category;
}

function wp_dropdown_roles( $default = false ) {
	global $wp_roles;
	$r = '';
	foreach($wp_roles->role_names as $role => $name)
		if ( $default == $role ) // Make default first in list
			$p = "\n\t<option selected='selected' value='$role'>$name</option>";
		else
			$r .= "\n\t<option value='$role'>$name</option>";
	echo $p . $r;
}


// Creates a new user from the "Users" form using $_POST information.

function add_user() {
	if ( func_num_args() ) { // The hackiest hack that ever did hack
		global $current_user, $wp_roles;
		$user_id = func_get_arg(0);
		if (isset ($_POST['role'])) {
			if($user_id != $current_user->id || $wp_roles->role_objects[$_POST['role']]->has_cap('edit_users')) {
				$user = new WP_User($user_id);
				$user->set_role($_POST['role']);
			}
		}
	} else {
		add_action('user_register', 'add_user'); // See above
		return edit_user();
	}
}

function edit_user($user_id = 0) {
	global $current_user, $wp_roles, $wpdb;
	if ($user_id != 0) {
		$update = true;
		$user->ID = $user_id;
		$userdata = get_userdata($user_id);
		$user->user_login = $wpdb->escape($userdata->user_login);
	} else {
		$update = false;
		$user = '';
	}

	if (isset ($_POST['user_login']))
		$user->user_login = wp_specialchars(trim($_POST['user_login']));

	$pass1 = $pass2 = '';
	if (isset ($_POST['pass1']))
		$pass1 = $_POST['pass1'];
	if (isset ($_POST['pass2']))
		$pass2 = $_POST['pass2'];

	if (isset ($_POST['role'])) {
		if($user_id != $current_user->id || $wp_roles->role_objects[$_POST['role']]->has_cap('edit_users'))
			$user->role = $_POST['role'];
	}

	if (isset ($_POST['email']))
		$user->user_email = wp_specialchars(trim($_POST['email']));
	if (isset ($_POST['url'])) {
		$user->user_url = wp_specialchars(trim($_POST['url']));
		$user->user_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $user->user_url) ? $user->user_url : 'http://'.$user->user_url;
	}
	if (isset ($_POST['first_name']))
		$user->first_name = wp_specialchars(trim($_POST['first_name']));
	if (isset ($_POST['last_name']))
		$user->last_name = wp_specialchars(trim($_POST['last_name']));
	if (isset ($_POST['nickname']))
		$user->nickname = wp_specialchars(trim($_POST['nickname']));
	if (isset ($_POST['display_name']))
		$user->display_name = wp_specialchars(trim($_POST['display_name']));
	if (isset ($_POST['description']))
		$user->description = wp_specialchars(trim($_POST['description']));
	if (isset ($_POST['jabber']))
		$user->jabber = wp_specialchars(trim($_POST['jabber']));
	if (isset ($_POST['aim']))
		$user->aim = wp_specialchars(trim($_POST['aim']));
	if (isset ($_POST['yim']))
		$user->yim = wp_specialchars(trim($_POST['yim']));

	$errors = new WP_Error();

	/* checking that username has been typed */
	if ($user->user_login == '')
		$errors->add('user_login', __('<strong>ERROR</strong>: Please enter a username.'));

	/* checking the password has been typed twice */
	do_action('check_passwords', array ($user->user_login, & $pass1, & $pass2));

	if (!$update) {
		if ($pass1 == '' || $pass2 == '')
			$errors->add('pass', __('<strong>ERROR</strong>: Please enter your password twice.'));
	} else {
		if ((empty ($pass1) && !empty ($pass2)) || (empty ($pass2) && !empty ($pass1)))
			$errors->add('pass', __("<strong>ERROR</strong>: you typed your new password only once."));
	}

	/* Check for "\" in password */
	if( strpos( " ".$pass1, "\\" ) )
		$errors->add('pass', __('<strong>ERROR</strong>: Passwords may not contain the character "\\".'));

	/* checking the password has been typed twice the same */
	if ($pass1 != $pass2)
		$errors->add('pass', __('<strong>ERROR</strong>: Please type the same password in the two password fields.'));

	if (!empty ($pass1))
		$user->user_pass = $pass1;

	if ( !validate_username($user->user_login) )
		$errors->add('user_login', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.'));

	if (!$update && username_exists($user->user_login))
		$errors->add('user_login', __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));

	/* checking e-mail address */
	if (empty ($user->user_email)) {
		$errors->add('user_email', __("<strong>ERROR</strong>: please type an e-mail address"));
	} else
		if (!is_email($user->user_email)) {
			$errors->add('user_email', __("<strong>ERROR</strong>: the email address isn't correct"));
		}

	if ( $errors->get_error_codes() )
		return $errors;

	if ($update) {
		$user_id = wp_update_user(get_object_vars($user));
	} else {
		$user_id = wp_insert_user(get_object_vars($user));
		wp_new_user_notification($user_id);
	}
	return $user_id;
}


function get_link_to_edit($link_id) {
	$link = get_link($link_id);

	$link->link_url = wp_specialchars($link->link_url, 1);
	$link->link_name = wp_specialchars($link->link_name, 1);
	$link->link_description = wp_specialchars($link->link_description);
	$link->link_notes = wp_specialchars($link->link_notes);
	$link->link_rss = wp_specialchars($link->link_rss);
	$link->post_category = $link->link_category;

	return $link;
}

function get_default_link_to_edit() {
	if ( isset($_GET['linkurl']) )
		$link->link_url = wp_specialchars($_GET['linkurl'], 1);
	else
		$link->link_url = '';

	if ( isset($_GET['name']) )
		$link->link_name = wp_specialchars($_GET['name'], 1);
	else
		$link->link_name = '';

	$link->link_visible = 'Y';

	return $link;
}

function add_link() {
	return edit_link();
}

function edit_link($link_id = '') {
	if (!current_user_can('manage_links'))
		wp_die(__("Cheatin' uh ?"));

	$_POST['link_url'] = wp_specialchars($_POST['link_url']);
	$_POST['link_url'] = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $_POST['link_url']) ? $_POST['link_url'] : 'http://' . $_POST['link_url'];
	$_POST['link_name'] = wp_specialchars($_POST['link_name']);
	$_POST['link_image'] = wp_specialchars($_POST['link_image']);
	$_POST['link_rss'] = wp_specialchars($_POST['link_rss']);
	$_POST['link_category'] = $_POST['post_category'];

	if ( !empty($link_id) ) {
		$_POST['link_id'] = $link_id;
		return wp_update_link($_POST);
	} else {
		return wp_insert_link($_POST);
	}
}

function url_shorten($url) {
	$short_url = str_replace('http://', '', stripslashes($url));
	$short_url = str_replace('www.', '', $short_url);
	if ('/' == substr($short_url, -1))
		$short_url = substr($short_url, 0, -1);
	if (strlen($short_url) > 35)
		$short_url = substr($short_url, 0, 32).'...';
	return $short_url;
}

function selected($selected, $current) {
	if ($selected == $current)
		echo ' selected="selected"';
}

function checked($checked, $current) {
	if ($checked == $current)
		echo ' checked="checked"';
}

function return_categories_list($parent = 0) {
	global $wpdb;
	return $wpdb->get_col("SELECT cat_ID FROM $wpdb->categories WHERE category_parent = $parent ORDER BY category_count DESC");
}

function sort_cats($cat1, $cat2) {
	return strcasecmp($cat1['cat_name'], $cat2['cat_name']);
}

function get_nested_categories($default = 0, $parent = 0) {
	global $post_ID, $link_id, $mode, $wpdb;

	if ($post_ID) {
		$checked_categories = $wpdb->get_col("
		     SELECT category_id
		     FROM $wpdb->categories, $wpdb->post2cat
		     WHERE $wpdb->post2cat.category_id = cat_ID AND $wpdb->post2cat.post_id = '$post_ID'
		     ");

		if (count($checked_categories) == 0) {
			// No selected categories, strange
			$checked_categories[] = $default;
		}
	} else if ($link_id) {
		$checked_categories = $wpdb->get_col("
		     SELECT category_id
		     FROM $wpdb->categories, $wpdb->link2cat
		     WHERE $wpdb->link2cat.category_id = cat_ID AND $wpdb->link2cat.link_id = '$link_id'
		     ");

		if (count($checked_categories) == 0) {
			// No selected categories, strange
			$checked_categories[] = $default;
		}	
	} else {
		$checked_categories[] = $default;
	}

	$cats = return_categories_list($parent);
	$result = array ();

	if (is_array($cats)) {
		foreach ($cats as $cat) {
			$result[$cat]['children'] = get_nested_categories($default, $cat);
			$result[$cat]['cat_ID'] = $cat;
			$result[$cat]['checked'] = in_array($cat, $checked_categories);
			$result[$cat]['cat_name'] = get_the_category_by_ID($cat);
		}
	}

	usort($result, 'sort_cats');

	return $result;
}

function write_nested_categories($categories) {
	foreach ($categories as $category) {
		echo '<li id="category-', $category['cat_ID'], '"><label for="in-category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'], '" type="checkbox" name="post_category[]" id="in-category-', $category['cat_ID'], '"', ($category['checked'] ? ' checked="checked"' : ""), '/> ', wp_specialchars($category['cat_name']), "</label></li>\n";

		if ( $category['children'] ) {
			echo "<ul>\n";
			write_nested_categories($category['children']);
			echo "</ul>\n";
		}
	}
}

function dropdown_categories($default = 0) {
	write_nested_categories(get_nested_categories($default));
}

// Dandy new recursive multiple category stuff.
function cat_rows($parent = 0, $level = 0, $categories = 0) {
	global $wpdb, $class;

	if (!$categories)
		$categories = get_categories('hide_empty=0');

	if ($categories) {
		foreach ($categories as $category) {
			if ($category->category_parent == $parent) {
				$category->cat_name = wp_specialchars($category->cat_name,'double');
				$pad = str_repeat('&#8212; ', $level);
				if ( current_user_can('manage_categories') ) {
					$edit = "<a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>".__('Edit')."</a></td>";
					$default_cat_id = get_option('default_category');
					$default_link_cat_id = get_option('default_link_category');

					if ( ($category->cat_ID != $default_cat_id) && ($category->cat_ID != $default_link_cat_id) )
						$edit .= "<td><a href='" . wp_nonce_url("categories.php?action=delete&amp;cat_ID=$category->cat_ID", 'delete-category_' . $category->cat_ID ) . "' onclick=\"return deleteSomething( 'cat', $category->cat_ID, '" . sprintf(__("You are about to delete the category &quot;%s&quot;.\\nAll of its posts will go into the default category of &quot;%s&quot;\\nAll of its bookmarks will go into the default category of &quot;%s&quot;.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), js_escape($category->cat_name), js_escape(get_catname($default_cat_id)), js_escape(get_catname($default_link_cat_id))) . "' );\" class='delete'>".__('Delete')."</a>";
					else
						$edit .= "<td style='text-align:center'>".__("Default");
				}
				else
					$edit = '';

				$class = ('alternate' == $class) ? '' : 'alternate';
				
				$category->category_count = number_format( $category->category_count );
				$category->link_count = number_format( $category->link_count );
				echo "<tr id='cat-$category->cat_ID' class='$class'><th scope='row'>$category->cat_ID</th><td>$pad $category->cat_name</td>
								<td>$category->category_description</td>
								<td align='center'>$category->category_count</td>
								<td align='center'>$category->link_count</td>
								<td>$edit</td>
								</tr>";
				cat_rows($category->cat_ID, $level +1, $categories);
			}
		}
	} else {
		return false;
	}
}

function page_rows($parent = 0, $level = 0, $pages = 0, $hierarchy = true) {
	global $wpdb, $class, $post;

	if (!$pages)
		$pages = get_pages('sort_column=menu_order');

	if (! $pages)
		return false;

	foreach ($pages as $post) {
		setup_postdata($post);
		if ( $hierarchy && ($post->post_parent != $parent) )
			continue;

		$post->post_title = wp_specialchars($post->post_title);
		$pad = str_repeat('&#8212; ', $level);
		$id = $post->ID;
		$class = ('alternate' == $class) ? '' : 'alternate';
?>
  <tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'> 
    <th scope="row"><?php echo $post->ID; ?></th> 
    <td>
      <?php echo $pad; ?><?php the_title() ?>
      <?php if ('private' == $post->post_status) _e(' - <strong>Private</strong>'); ?>
    </td> 
    <td><?php the_author() ?></td>
    <td><?php echo mysql2date('Y-m-d g:i a', $post->post_modified); ?></td> 
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
    <td><?php if ( current_user_can('edit_page', $id) ) { echo "<a href='page.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if ( current_user_can('edit_page', $id) ) { echo "<a href='" . wp_nonce_url("page.php?action=delete&amp;post=$id", 'delete-page_' . $id) .  "' class='delete' onclick=\"return deleteSomething( 'page', " . $id . ", '" . sprintf(__("You are about to delete the &quot;%s&quot; page.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), js_escape(get_the_title()) ) . "' );\">" . __('Delete') . "</a>"; } ?></td> 
  </tr> 

<?php
		if ( $hierarchy) page_rows($id, $level + 1, $pages);
	}
}

function user_row( $user_object, $style = '' ) {
	if ( !(is_object($user_object) && is_a($user_object, 'WP_User')) )
		$user_object = new WP_User( (int) $user_object );
	$email = $user_object->user_email;
	$url = $user_object->user_url;
	$short_url = str_replace('http://', '', $url);
	$short_url = str_replace('www.', '', $short_url);
	if ('/' == substr($short_url, -1))
		$short_url = substr($short_url, 0, -1);
	if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
	$numposts = get_usernumposts($user_object->ID);
	$r = "<tr id='user-$user_object->ID'$style>
		<td><input type='checkbox' name='users[]' id='user_{$user_object->ID}' value='{$user_object->ID}' /> <label for='user_{$user_object->ID}'>{$user_object->ID}</label></td>
		<td><label for='user_{$user_object->ID}'><strong>$user_object->user_login</strong></label></td>
		<td><label for='user_{$user_object->ID}'>$user_object->first_name $user_object->last_name</label></td>
		<td><a href='mailto:$email' title='" . sprintf(__('e-mail: %s'), $email) . "'>$email</a></td>
		<td><a href='$url' title='website: $url'>$short_url</a></td>";
	$r .= "\n\t\t<td align='center'>";
	if ($numposts > 0) {
		$r .= "<a href='edit.php?author=$user_object->ID' title='" . __('View posts by this author') . "' class='edit'>";
		$r .= sprintf(__('View %1$s %2$s'), $numposts, __ngettext('post', 'posts', $numposts));
	}
	$r .= "</td>\n\t\t<td>";
	$edit_link = add_query_arg('wp_http_referer', wp_specialchars(urlencode(stripslashes($_SERVER['REQUEST_URI']))), "user-edit.php?user_id=$user_object->ID");
	if ( current_user_can('edit_user', $user_object->ID) )
		$r .= "<a href='$edit_link' class='edit'>".__('Edit')."</a>";
	$r .= "</td>\n\t</tr>";
	return $r;
}

function wp_dropdown_cats($currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {
	global $wpdb;
	if (!$categories)
		$categories = get_categories('hide_empty=0');

	if ($categories) {
		foreach ($categories as $category) {
			if ($currentcat != $category->cat_ID && $parent == $category->category_parent) {
				$pad = str_repeat('&#8211; ', $level);
				$category->cat_name = wp_specialchars($category->cat_name);
				echo "\n\t<option value='$category->cat_ID'";
				if ($currentparent == $category->cat_ID)
					echo " selected='selected'";
				echo ">$pad$category->cat_name</option>";
				wp_dropdown_cats($currentcat, $currentparent, $category->cat_ID, $level +1, $categories);
			}
		}
	} else {
		return false;
	}
}

function return_link_categories_list($parent = 0) {
	global $wpdb;
	return $wpdb->get_col("SELECT cat_ID FROM $wpdb->categories WHERE category_parent = $parent ORDER BY link_count DESC");
}

function wp_create_thumbnail($file, $max_side, $effect = '') {

		// 1 = GIF, 2 = JPEG, 3 = PNG

	if (file_exists($file)) {
		$type = getimagesize($file);

		// if the associated function doesn't exist - then it's not
		// handle. duh. i hope.

		if (!function_exists('imagegif') && $type[2] == 1) {
			$error = __('Filetype not supported. Thumbnail not created.');
		}
		elseif (!function_exists('imagejpeg') && $type[2] == 2) {
			$error = __('Filetype not supported. Thumbnail not created.');
		}
		elseif (!function_exists('imagepng') && $type[2] == 3) {
			$error = __('Filetype not supported. Thumbnail not created.');
		} else {

			// create the initial copy from the original file
			if ($type[2] == 1) {
				$image = imagecreatefromgif($file);
			}
			elseif ($type[2] == 2) {
				$image = imagecreatefromjpeg($file);
			}
			elseif ($type[2] == 3) {
				$image = imagecreatefrompng($file);
			}

			if (function_exists('imageantialias'))
				imageantialias($image, TRUE);

			$image_attr = getimagesize($file);

			// figure out the longest side

			if ($image_attr[0] > $image_attr[1]) {
				$image_width = $image_attr[0];
				$image_height = $image_attr[1];
				$image_new_width = $max_side;

				$image_ratio = $image_width / $image_new_width;
				$image_new_height = $image_height / $image_ratio;
				//width is > height
			} else {
				$image_width = $image_attr[0];
				$image_height = $image_attr[1];
				$image_new_height = $max_side;

				$image_ratio = $image_height / $image_new_height;
				$image_new_width = $image_width / $image_ratio;
				//height > width
			}

			$thumbnail = imagecreatetruecolor($image_new_width, $image_new_height);
			@ imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);

			// If no filters change the filename, we'll do a default transformation.
			if ( basename($file) == $thumb = apply_filters('thumbnail_filename', basename($file)) )
				$thumb = preg_replace('!(\.[^.]+)?$!', __('.thumbnail').'$1', basename($file), 1);

			$thumbpath = str_replace(basename($file), $thumb, $file);

			// move the thumbnail to it's final destination
			if ($type[2] == 1) {
				if (!imagegif($thumbnail, $thumbpath)) {
					$error = __("Thumbnail path invalid");
				}
			}
			elseif ($type[2] == 2) {
				if (!imagejpeg($thumbnail, $thumbpath)) {
					$error = __("Thumbnail path invalid");
				}
			}
			elseif ($type[2] == 3) {
				if (!imagepng($thumbnail, $thumbpath)) {
					$error = __("Thumbnail path invalid");
				}
			}

		}
	} else {
		$error = __('File not found');
	}

	if (!empty ($error)) {
		return $error;
	} else {
		return $thumbpath;
	}
}

// Some postmeta stuff
function has_meta($postid) {
	global $wpdb;

	return $wpdb->get_results("
			SELECT meta_key, meta_value, meta_id, post_id
			FROM $wpdb->postmeta
			WHERE post_id = '$postid'
			ORDER BY meta_key,meta_id", ARRAY_A);

}

function list_meta($meta) {
	global $post_ID;
	// Exit if no meta
	if (!$meta) {
		echo '<tbody id="the-list"></tbody>'; //TBODY needed for list-manipulation JS
		return;
	}
	$count = 0;
?>
	<thead>
	<tr>
		<th><?php _e('Key') ?></th>
		<th><?php _e('Value') ?></th>
		<th colspan='2'><?php _e('Action') ?></th>
	</tr>
	</thead>
<?php
	$r ="\n\t<tbody id='the-list'>";
	foreach ($meta as $entry) {
		++ $count;
		if ($count % 2)
			$style = 'alternate';
		else
			$style = '';
		if ('_' == $entry['meta_key'] { 0 })
			$style .= ' hidden';
		$key_js = addslashes(wp_specialchars( $entry['meta_key'], 'double' ));
		$entry['meta_key'] = wp_specialchars( $entry['meta_key'], true );
		$entry['meta_value'] = wp_specialchars( $entry['meta_value'], true );
		$r .= "\n\t<tr id='meta-{$entry['meta_id']}' class='$style'>";
		$r .= "\n\t\t<td valign='top'><input name='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>";
		$r .= "\n\t\t<td><textarea name='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>";
		$r .= "\n\t\t<td align='center'><input name='updatemeta' type='submit' class='updatemeta' tabindex='6' value='".__('Update')."' /><br />";
		$r .= "\n\t\t<input name='deletemeta[{$entry['meta_id']}]' type='submit' onclick=\"return deleteSomething( 'meta', {$entry['meta_id']}, '";
		$r .= sprintf(__("You are about to delete the &quot;%s&quot; custom field on this post.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), $key_js);
		$r .= "' );\" class='deletemeta' tabindex='6' value='".__('Delete')."' /></td>";
		$r .= "\n\t</tr>";
	}
	echo $r;
	echo "\n\t</tbody>";
}

// Get a list of previously defined keys
function get_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col("
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			ORDER BY meta_key");

	return $keys;
}

function meta_form() {
	global $wpdb;
	$keys = $wpdb->get_col("
			SELECT meta_key
			FROM $wpdb->postmeta
			GROUP BY meta_key
			ORDER BY meta_id DESC
			LIMIT 10");
?>
<h3><?php _e('Add a new custom field:') ?></h3>
<table id="newmeta" cellspacing="3" cellpadding="3">
	<tr>
<th colspan="2"><?php _e('Key') ?></th>
<th><?php _e('Value') ?></th>
</tr>
	<tr valign="top">
		<td align="right" width="18%">
<?php if ($keys) : ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#"><?php _e('- Select -'); ?></option>
<?php

	foreach ($keys as $key) {
		echo "\n\t<option value='$key'>$key</option>";
	}
?>
</select> <?php _e('or'); ?>
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="8"></textarea></td>
	</tr>

</table>
<p class="submit"><input type="submit" id="updatemetasub" name="updatemeta" tabindex="9" value="<?php _e('Add Custom Field &raquo;') ?>" /></p>
<?php

}

function add_meta($post_ID) {
	global $wpdb;
	$post_ID = (int) $post_ID;

	$metakeyselect = $wpdb->escape(stripslashes(trim($_POST['metakeyselect'])));
	$metakeyinput = $wpdb->escape(stripslashes(trim($_POST['metakeyinput'])));
	$metavalue = $wpdb->escape(stripslashes(trim($_POST['metavalue'])));

	if ( ('0' === $metavalue || !empty ($metavalue)) && ((('#NONE#' != $metakeyselect) && !empty ($metakeyselect)) || !empty ($metakeyinput)) ) {
		// We have a key/value pair. If both the select and the 
		// input for the key have data, the input takes precedence:

 		if ('#NONE#' != $metakeyselect)
			$metakey = $metakeyselect;

		if ($metakeyinput)
			$metakey = $metakeyinput; // default

		$result = $wpdb->query("
						INSERT INTO $wpdb->postmeta 
						(post_id,meta_key,meta_value) 
						VALUES ('$post_ID','$metakey','$metavalue')
					");
		return $wpdb->insert_id;
	}
	return false;
} // add_meta

function delete_meta($mid) {
	global $wpdb;
	$mid = (int) $mid;

	return $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_id = '$mid'");
}

function update_meta($mid, $mkey, $mvalue) {
	global $wpdb;
	$mid = (int) $mid;

	return $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '$mkey', meta_value = '$mvalue' WHERE meta_id = '$mid'");
}

function get_post_meta_by_id($mid) {
	global $wpdb;
	$mid = (int) $mid;

	return $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_id = '$mid'");
}

function touch_time($edit = 1, $for_post = 1) {
	global $wp_locale, $post, $comment;

	if ( $for_post )
		$edit = ( ('draft' == $post->post_status) && (!$post->post_date || '0000-00-00 00:00:00' == $post->post_date) ) ? false : true;
 
	echo '<fieldset><legend><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp" /> <label for="timestamp">'.__('Edit timestamp').'</label></legend>';

	$time_adj = time() + (get_settings('gmt_offset') * 3600);
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date('d', $post_date) : gmdate('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $post_date) : gmdate('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $post_date) : gmdate('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $post_date) : gmdate('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $post_date) : gmdate('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $post_date) : gmdate('s', $time_adj);

	echo "<select name=\"mm\" onchange=\"edit_date.checked=true\">\n";
	for ($i = 1; $i < 13; $i = $i +1) {
		echo "\t\t\t<option value=\"$i\"";
		if ($i == $mm)
			echo ' selected="selected"';
		echo '>' . $wp_locale->get_month($i) . "</option>\n";
	}
?>
</select>
<input type="text" id="jj" name="jj" value="<?php echo $jj; ?>" size="2" maxlength="2" onchange="edit_date.checked=true"/>
<input type="text" id="aa" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" onchange="edit_date.checked=true" /> @ 
<input type="text" id="hh" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" onchange="edit_date.checked=true" /> : 
<input type="text" id="mn" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" onchange="edit_date.checked=true" /> 
<input type="hidden" id="ss" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" onchange="edit_date.checked=true" /> 
<?php
	if ( $edit ) {
		_e('Existing timestamp');
		//echo ': ' . $wp_locale->get_month($mm) . "$jj, $aa @ $hh:$mn";
		echo sprintf(__(': %1$s %2$s, %3$s @ %4$s:%5$s'), $wp_locale->get_month($mm), $jj, $aa, $hh, $mn);
	}
?>
</fieldset>
	<?php

}

// insert_with_markers: Owen Winkler, fixed by Eric Anderson
// Inserts an array of strings into a file (.htaccess), placing it between
// BEGIN and END markers.  Replaces existing marked info.  Retains surrounding
// data.  Creates file if none exists.
// Returns true on write success, false on failure.
function insert_with_markers($filename, $marker, $insertion) {
	if (!file_exists($filename) || is_writeable($filename)) {
		if (!file_exists($filename)) {
			$markerdata = '';
		} else {
			$markerdata = explode("\n", implode('', file($filename)));
		}

		$f = fopen($filename, 'w');
		$foundit = false;
		if ($markerdata) {
			$state = true;
			foreach ($markerdata as $n => $markerline) {
				if (strstr($markerline, "# BEGIN {$marker}"))
					$state = false;
				if ($state) {
					if ( $n + 1 < count($markerdata) )
						fwrite($f, "{$markerline}\n");
					else
						fwrite($f, "{$markerline}");
				}
				if (strstr($markerline, "# END {$marker}")) {
					fwrite($f, "# BEGIN {$marker}\n");
					if (is_array($insertion))
						foreach ($insertion as $insertline)
							fwrite($f, "{$insertline}\n");
					fwrite($f, "# END {$marker}\n");
					$state = true;
					$foundit = true;
				}
			}
		}
		if (!$foundit) {
			fwrite($f, "# BEGIN {$marker}\n");
			foreach ($insertion as $insertline)
				fwrite($f, "{$insertline}\n");
			fwrite($f, "# END {$marker}\n");
		}
		fclose($f);
		return true;
	} else {
		return false;
	}
}

// extract_from_markers: Owen Winkler
// Returns an array of strings from a file (.htaccess) from between BEGIN
// and END markers.
function extract_from_markers($filename, $marker) {
	$result = array ();

	if (!file_exists($filename)) {
		return $result;
	}

	if ($markerdata = explode("\n", implode('', file($filename))));
	{
		$state = false;
		foreach ($markerdata as $markerline) {
			if (strstr($markerline, "# END {$marker}"))
				$state = false;
			if ($state)
				$result[] = $markerline;
			if (strstr($markerline, "# BEGIN {$marker}"))
				$state = true;
		}
	}

	return $result;
}

function got_mod_rewrite() {
	global $is_apache;

	// take 3 educated guesses as to whether or not mod_rewrite is available
	if ( !$is_apache )
		return false;

	if ( function_exists('apache_get_modules') ) {
		if ( !in_array('mod_rewrite', apache_get_modules()) )
			return false;
	}

	return true;
}

function save_mod_rewrite_rules() {
	global $is_apache, $wp_rewrite;
	$home_path = get_home_path();

	if (!$wp_rewrite->using_mod_rewrite_permalinks())
		return;

	if (!((!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess')))
		return;

	if (! got_mod_rewrite())
		return;

	$rules = explode("\n", $wp_rewrite->mod_rewrite_rules());
	insert_with_markers($home_path.'.htaccess', 'WordPress', $rules);
}

function the_quicktags() {
	// Browser detection sucks, but until Safari supports the JS needed for this to work people just assume it's a bug in WP
	if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
		echo '
		<div id="quicktags">
			';
		wp_print_scripts( 'quicktags' );
		echo '			<script type="text/javascript">if ( typeof tinyMCE == "undefined" || tinyMCE.configs.length < 1 ) edToolbar();</script>
		</div>
';
	} else echo '
<script type="text/javascript">
function edInsertContent(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == "0") {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		              + myValue 
                      + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}
</script>
';
}

function validate_current_theme() {
	$theme_loc = 'wp-content/themes';
	$theme_root = ABSPATH.$theme_loc;

	$template = get_settings('template');
	$stylesheet = get_settings('stylesheet');

	if (($template != 'default') && (!file_exists("$theme_root/$template/index.php"))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	if (($stylesheet != 'default') && (!file_exists("$theme_root/$stylesheet/style.css"))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	return true;
}

function get_broken_themes() {
	global $wp_broken_themes;

	get_themes();
	return $wp_broken_themes;
}

function get_page_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$page_templates = array ();

	if (is_array($templates)) {
		foreach ($templates as $template) {
			$template_data = implode('', file(ABSPATH.$template));
			preg_match("|Template Name:(.*)|i", $template_data, $name);
			preg_match("|Description:(.*)|i", $template_data, $description);

			$name = $name[1];
			$description = $description[1];

			if (!empty ($name)) {
				$page_templates[trim($name)] = basename($template);
			}
		}
	}

	return $page_templates;
}

function page_template_dropdown($default = '') {
	$templates = get_page_templates();
	foreach (array_keys($templates) as $template)
		: if ($default == $templates[$template])
			$selected = " selected='selected'";
		else
			$selected = '';
	echo "\n\t<option value='".$templates[$template]."' $selected>$template</option>";
	endforeach;
}

function parent_dropdown($default = 0, $parent = 0, $level = 0) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_type = 'page' ORDER BY menu_order");

	if ($items) {
		foreach ($items as $item) {
			// A page cannot be it's own parent.
			if (!empty ($post_ID)) {
				if ($item->ID == $post_ID) {
					continue;
				}
			}
			$pad = str_repeat('&nbsp;', $level * 3);
			if ($item->ID == $default)
				$current = ' selected="selected"';
			else
				$current = '';

			echo "\n\t<option value='$item->ID'$current>$pad $item->post_title</option>";
			parent_dropdown($default, $item->ID, $level +1);
		}
	} else {
		return false;
	}
}

function user_can_access_admin_page() {
	global $pagenow;
	global $menu;
	global $submenu;
	global $menu_nopriv;

	$parent = get_admin_page_parent();
	
	if ( isset($menu_nopriv[$pagenow]) )
		return false;

	if ( empty($parent) )
		return true;

	if (isset ($submenu[$parent])) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ($submenu_array[2] == $pagenow) {
				if (current_user_can($submenu_array[1]))
					return true;
				else
					return false;
			}
		}
	}

	foreach ($menu as $menu_array) {
		//echo "parent array: " . $menu_array[2];
		if ($menu_array[2] == $parent) {
			if (current_user_can($menu_array[1]))
				return true;
			else
				return false;
		}
	}
	
	return true;
}

function get_admin_page_title() {
	global $title;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;

	if (isset ($title) && !empty ($title)) {
		return $title;
	}

	$hook = get_plugin_page_hook($plugin_page, $pagenow);

	$parent = $parent1 = get_admin_page_parent();
	if (empty ($parent)) {
		foreach ($menu as $menu_array) {
			if (isset ($menu_array[3])) {
				if ($menu_array[2] == $pagenow) {
					$title = $menu_array[3];
					return $menu_array[3];
				} else
					if (isset ($plugin_page) && ($plugin_page == $menu_array[2]) && ($hook == $menu_array[3])) {
						$title = $menu_array[3];
						return $menu_array[3];
					}
			}
		}
	} else {
		foreach (array_keys($submenu) as $parent) {
			foreach ($submenu[$parent] as $submenu_array) {
				if (isset ($submenu_array[3])) {
					if ($submenu_array[2] == $pagenow) {
						$title = $submenu_array[3];
						return $submenu_array[3];
					} else
						if (isset ($plugin_page) && ($plugin_page == $submenu_array[2]) && (($parent == $pagenow) || ($parent == $plugin_page) || ($plugin_page == $hook) || (($pagenow == 'admin.php') && ($parent1 != $submenu_array[2])))) {
							$title = $submenu_array[3];
							return $submenu_array[3];
						}
				}
			}
		}
	}

	return '';
}

function get_admin_page_parent() {
	global $parent_file;
	global $menu;
	global $submenu;
	global $pagenow;
	global $plugin_page;
	global $real_parent_file;

	if ( !empty ($parent_file) ) {
		if ( isset($real_parent_file[$parent_file]) )
			$parent_file = $real_parent_file[$parent_file];

		return $parent_file;
	}

	if ($pagenow == 'admin.php' && isset ($plugin_page)) {
		foreach ($menu as $parent_menu) {
			if ($parent_menu[2] == $plugin_page) {
				$parent_file = $plugin_page;
				if ( isset($real_parent_file[$parent_file]) )
					$parent_file = $real_parent_file[$parent_file];
					
				return $parent_file;
			}
		}
	}

	foreach (array_keys($submenu) as $parent) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ( isset($real_parent_file[$parent]) )
				$parent = $real_parent_file[$parent];
			if ($submenu_array[2] == $pagenow) {
				$parent_file = $parent;
				return $parent;
			} else
				if (isset ($plugin_page) && ($plugin_page == $submenu_array[2])) {
					$parent_file = $parent;
					return $parent;
				}
		}
	}

	$parent_file = '';
	return '';
}

function add_menu_page($page_title, $menu_title, $access_level, $file, $function = '') {
	global $menu, $admin_page_hooks;

	$file = plugin_basename($file);

	$menu[] = array ($menu_title, $access_level, $file, $page_title);

	$admin_page_hooks[$file] = sanitize_title($menu_title);

	$hookname = get_plugin_page_hookname($file, '');
	if (!empty ($function) && !empty ($hookname))
		add_action($hookname, $function);

	return $hookname;
}

function add_submenu_page($parent, $page_title, $menu_title, $access_level, $file, $function = '') {
	global $submenu;
	global $menu;
	global $real_parent_file;

	$parent = plugin_basename($parent);
	if ( isset($real_parent_file[$parent]) )
		$parent = $real_parent_file[$parent];

	$file = plugin_basename($file);

	// If the parent doesn't already have a submenu, add a link to the parent
	// as the first item in the submenu.  If the submenu file is the same as the
	// parent file someone is trying to link back to the parent manually.  In
	// this case, don't automatically add a link back to avoid duplication.
	if (!isset ($submenu[$parent]) && $file != $parent) {
		foreach ($menu as $parent_menu) {
			if ($parent_menu[2] == $parent) {
				$submenu[$parent][] = $parent_menu;
			}
		}
	}

	$submenu[$parent][] = array ($menu_title, $access_level, $file, $page_title);

	$hookname = get_plugin_page_hookname($file, $parent);
	if (!empty ($function) && !empty ($hookname))
		add_action($hookname, $function);

	return $hookname;
}

function add_options_page($page_title, $menu_title, $access_level, $file, $function = '') {
	return add_submenu_page('options-general.php', $page_title, $menu_title, $access_level, $file, $function);
}

function add_management_page($page_title, $menu_title, $access_level, $file, $function = '') {
	return add_submenu_page('edit.php', $page_title, $menu_title, $access_level, $file, $function);
}

function add_theme_page($page_title, $menu_title, $access_level, $file, $function = '') {
	return add_submenu_page('themes.php', $page_title, $menu_title, $access_level, $file, $function);
}

function validate_file($file, $allowed_files = '') {
	if (false !== strpos($file, './'))
		return 1;

	if (':' == substr($file, 1, 1))
		return 2;

	if (!empty ($allowed_files) && (!in_array($file, $allowed_files)))
		return 3;

	return 0;
}

function validate_file_to_edit($file, $allowed_files = '') {
	$file = stripslashes($file);

	$code = validate_file($file, $allowed_files);

	if (!$code)
		return $file;

	switch ($code) {
		case 1 :
			wp_die(__('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.'));

		case 2 :
			wp_die(__('Sorry, can&#8217;t call files with their real path.'));

		case 3 :
			wp_die(__('Sorry, that file cannot be edited.'));
	}
}

function get_home_path() {
	$home = get_settings('home');
	if ($home != '' && $home != get_settings('siteurl')) {
		$home_path = parse_url($home);
		$home_path = $home_path['path'];
		$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"]);
		$home_path = trailingslashit($root.$home_path);
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

function get_real_file_to_edit($file) {
	if ('index.php' == $file || '.htaccess' == $file) {
		$real_file = get_home_path().$file;
	} else {
		$real_file = ABSPATH.$file;
	}

	return $real_file;
}

$wp_file_descriptions = array ('index.php' => __('Main Index Template'), 'style.css' => __('Stylesheet'), 'comments.php' => __('Comments'), 'comments-popup.php' => __('Popup Comments'), 'footer.php' => __('Footer'), 'header.php' => __('Header'), 'sidebar.php' => __('Sidebar'), 'archive.php' => __('Archives'), 'category.php' => __('Category Template'), 'page.php' => __('Page Template'), 'search.php' => __('Search Results'), 'single.php' => __('Single Post'), '404.php' => __('404 Template'), 'my-hacks.php' => __('my-hacks.php (legacy hacks support)'), '.htaccess' => __('.htaccess (for rewrite rules)'),
	// Deprecated files
	'wp-layout.css' => __('Stylesheet'), 'wp-comments.php' => __('Comments Template'), 'wp-comments-popup.php' => __('Popup Comments Template'));

function get_file_description($file) {
	global $wp_file_descriptions;

	if (isset ($wp_file_descriptions[basename($file)])) {
		return $wp_file_descriptions[basename($file)];
	}
	elseif ( file_exists( ABSPATH . $file ) && is_file( ABSPATH . $file ) ) {
		$template_data = implode('', file( ABSPATH . $file ));
		if (preg_match("|Template Name:(.*)|i", $template_data, $name))
			return $name[1];
	}

	return basename($file);
}

function update_recently_edited($file) {
	$oldfiles = (array) get_option('recently_edited');
	if ($oldfiles) {
		$oldfiles = array_reverse($oldfiles);
		$oldfiles[] = $file;
		$oldfiles = array_reverse($oldfiles);
		$oldfiles = array_unique($oldfiles);
		if (5 < count($oldfiles))
			array_pop($oldfiles);
	} else {
		$oldfiles[] = $file;
	}
	update_option('recently_edited', $oldfiles);
}

function get_plugin_data($plugin_file) {
	$plugin_data = implode('', file($plugin_file));
	preg_match("|Plugin Name:(.*)|i", $plugin_data, $plugin_name);
	preg_match("|Plugin URI:(.*)|i", $plugin_data, $plugin_uri);
	preg_match("|Description:(.*)|i", $plugin_data, $description);
	preg_match("|Author:(.*)|i", $plugin_data, $author_name);
	preg_match("|Author URI:(.*)|i", $plugin_data, $author_uri);
	if (preg_match("|Version:(.*)|i", $plugin_data, $version))
		$version = $version[1];
	else
		$version = '';

	$description = wptexturize($description[1]);

	$name = $plugin_name[1];
	$name = trim($name);
	$plugin = $name;
	if ('' != $plugin_uri[1] && '' != $name) {
		$plugin = '<a href="'.$plugin_uri[1].'" title="'.__('Visit plugin homepage').'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1]) {
		$author = $author_name[1];
	} else {
		$author = '<a href="'.$author_uri[1].'" title="'.__('Visit author homepage').'">'.$author_name[1].'</a>';
	}

	return array ('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1]);
}

function get_plugins() {
	global $wp_plugins;

	if (isset ($wp_plugins)) {
		return $wp_plugins;
	}

	$wp_plugins = array ();
	$plugin_loc = 'wp-content/plugins';
	$plugin_root = ABSPATH.$plugin_loc;

	// Files in wp-content/plugins directory
	$plugins_dir = @ dir($plugin_root);
	if ($plugins_dir) {
		while (($file = $plugins_dir->read()) !== false) {
			if (preg_match('|^\.+$|', $file))
				continue;
			if (is_dir($plugin_root.'/'.$file)) {
				$plugins_subdir = @ dir($plugin_root.'/'.$file);
				if ($plugins_subdir) {
					while (($subfile = $plugins_subdir->read()) !== false) {
						if (preg_match('|^\.+$|', $subfile))
							continue;
						if (preg_match('|\.php$|', $subfile))
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if (preg_match('|\.php$|', $file))
					$plugin_files[] = $file;
			}
		}
	}

	if (!$plugins_dir || !$plugin_files) {
		return $wp_plugins;
	}

	sort($plugin_files);

	foreach ($plugin_files as $plugin_file) {
		if ( !is_readable("$plugin_root/$plugin_file"))
			continue;

		$plugin_data = get_plugin_data("$plugin_root/$plugin_file");

		if (empty ($plugin_data['Name'])) {
			continue;
		}

		$wp_plugins[plugin_basename($plugin_file)] = $plugin_data;
	}

	return $wp_plugins;
}

function get_plugin_page_hookname($plugin_page, $parent_page) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent();

	if (empty ($parent_page) || 'admin.php' == $parent_page) {
		if (isset ($admin_page_hooks[$plugin_page]))
			$page_type = 'toplevel';
		else
			if (isset ($admin_page_hooks[$parent]))
				$page_type = $admin_page_hooks[$parent];
	} else
		if (isset ($admin_page_hooks[$parent_page])) {
			$page_type = $admin_page_hooks[$parent_page];
		} else {
			$page_type = 'admin';
		}

	$plugin_name = preg_replace('!\.php!', '', $plugin_page);

	return $page_type.'_page_'.$plugin_name;
}

function get_plugin_page_hook($plugin_page, $parent_page) {
	global $wp_filter;

	$hook = get_plugin_page_hookname($plugin_page, $parent_page);
	if (isset ($wp_filter[$hook]))
		return $hook;
	else
		return '';
}

function browse_happy() {
	$getit = __('WordPress recommends a better browser');
	echo '
		<p id="bh" style="text-align: center;"><a href="http://browsehappy.com/" title="'.$getit.'"><img src="images/browse-happy.gif" alt="Browse Happy" /></a></p>
		';
}
if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
	add_action('admin_footer', 'browse_happy');

function documentation_link($for) {
	return;
}

function register_importer($id, $name, $description, $callback) {
	global $wp_importers;

	$wp_importers[$id] = array ($name, $description, $callback);
}

function get_importers() {
	global $wp_importers;

	return $wp_importers;
}

function current_theme_info() {
	$themes = get_themes();
	$current_theme = get_current_theme();
	$ct->name = $current_theme;
	$ct->title = $themes[$current_theme]['Title'];
	$ct->version = $themes[$current_theme]['Version'];
	$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
	$ct->template_dir = $themes[$current_theme]['Template Dir'];
	$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
	$ct->template = $themes[$current_theme]['Template'];
	$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
	$ct->screenshot = $themes[$current_theme]['Screenshot'];
	$ct->description = $themes[$current_theme]['Description'];
	$ct->author = $themes[$current_theme]['Author'];
	return $ct;
}


// array wp_handle_upload ( array &file [, array overrides] )
// file: reference to a single element of $_FILES. Call the function once for each uploaded file.
// overrides: an associative array of names=>values to override default variables with extract($overrides, EXTR_OVERWRITE).
// On success, returns an associative array of file attributes.
// On failure, returns $overrides['upload_error_handler'](&$file, $message) or array('error'=>$message).
function wp_handle_upload(&$file, $overrides = false) {
	// The default error handler.
	if (! function_exists('wp_handle_upload_error') ) {
		function wp_handle_upload_error(&$file, $message) {
			return array('error'=>$message);
		}
	}

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_upload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array(false,
		__("The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>."),
		__("The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form."),
		__("The uploaded file was only partially uploaded."),
		__("No file was uploaded."),
		__("Missing a temporary folder."),
		__("Failed to write file to disk."));

	// All tests are on by default. Most can be turned off by $override[{test_name}] = false;
	$test_form = true;
	$test_size = true;

	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;

	// Install user overrides. Did we mention that this voids your warranty?
	if ( is_array($overrides) )
		extract($overrides, EXTR_OVERWRITE);

	// A correct form post will pass this test.
	if ( $test_form && (!isset($_POST['action']) || ($_POST['action'] != $action)) )
		return $upload_error_handler($file, __('Invalid form submission.'));

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
		return $upload_error_handler($file, $upload_error_strings[$file['error']]);

	// A non-empty file will pass this test.
	if ( $test_size && !($file['size'] > 0) )
		return $upload_error_handler($file, __('File is empty. Please upload something more substantial.'));

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file($file['tmp_name']) )
		return $upload_error_handler($file, __('Specified file failed upload test.'));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype($file['name'], $mimes);

		extract($wp_filetype);

		if ( !$type || !$ext )
			return $upload_error_handler($file, __('File type does not meet security guidelines. Try another.'));
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) )
		return $upload_error_handler($file, $uploads['error']);

	// Increment the file number until we have a unique file to save in $dir. Use $override['unique_filename_callback'] if supplied.
	if ( isset($unique_filename_callback) && function_exists($unique_filename_callback) ) {
		$filename = $unique_filename_callback($uploads['path'], $file['name']);
	} else {
		$number = '';
		$filename = str_replace('#', '_', $file['name']);
		$filename = str_replace(array('\\', "'"), '', $filename);
		if ( empty($ext) )
			$ext = '';
		else
			$ext = ".$ext";
		while ( file_exists($uploads['path'] . "/$filename") ) {
			if ( '' == "$number$ext" )
				$filename = $filename . ++$number . $ext;
			else
				$filename = str_replace("$number$ext", ++$number . $ext, $filename);
		}
		$filename = str_replace($ext, '', $filename);
		$filename = sanitize_title_with_dashes($filename) . $ext;
	}

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @ move_uploaded_file($file['tmp_name'], $new_file) )
		wp_die(printf(__('The uploaded file could not be moved to %s.'), $file['path']));

	// Set correct file permissions
	$stat = stat(dirname($new_file));
	$perms = $stat['mode'] & 0000666;
	@ chmod($new_file, $perms);

	// Compute the URL
	$url = $uploads['url'] . "/$filename";
	
	$return = apply_filters( 'wp_handle_upload', array('file' => $new_file, 'url' => $url, 'type' => $type) );

	return $return;
}

function wp_shrink_dimensions($width, $height, $wmax = 128, $hmax = 96) {
	if ( $height <= $hmax && $width <= $wmax )
		return array($width, $height);
	elseif ( $width / $height > $wmax / $hmax )
		return array($wmax, (int) ($height / $width * $wmax));
	else
		return array((int) ($width / $height * $hmax), $hmax);
}

function wp_import_cleanup($id) {
	wp_delete_attachment($id);
}

function wp_import_upload_form($action) {
?>
<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo $action ?>">
<p>
<label for="upload"><?php _e('Choose a file from your computer:'); ?></label> <input type="file" id="upload" name="import" size="25" />
<input type="hidden" name="action" value="save" />
</p>
<p class="submit">
<input type="submit" value="<?php _e('Upload file and import'); ?> &raquo;" />
</p>
</form>
<?php
}

function wp_import_handle_upload() {
	$overrides = array('test_form' => false, 'test_type' => false);
	$file = wp_handle_upload($_FILES['import'], $overrides);

	if ( isset($file['error']) )
		return $file;

	$url = $file['url'];
	$file = addslashes( $file['file'] );
	$filename = basename($file);

	// Construct the object array
	$object = array(
		'post_title' => $filename,
		'post_content' => $url,
		'post_mime_type' => 'import',
		'guid' => $url
	);

	// Save the data
	$id = wp_insert_attachment($object, $file);

	return array('file' => $file, 'id' => $id);
}

function user_can_richedit() {
	if ( 'true' != get_user_option('rich_editing') )
		return false;

	if ( preg_match('!opera[ /][2-8]|konqueror|safari!i', $_SERVER['HTTP_USER_AGENT']) )
		return false;

	return true; // Best guess
}

function the_attachment_links($id = false) {
	$id = (int) $id;
	$post = & get_post($id);

	if ( $post->post_type != 'attachment' )
		return false;

	$icon = get_attachment_icon($post->ID);

?>
<p><?php _e('Text linked to file') ?><br />
<textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo $post->guid ?>" class="attachmentlink"><?php echo basename($post->guid) ?></a></textarea></p>
<p><?php _e('Text linked to subpost') ?><br />
<textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link($post->ID) ?>" rel="attachment" id="<?php echo $post->ID ?>"><?php echo $post->post_title ?></a></textarea></p>
<?php if ( $icon ) : ?>
<p><?php _e('Thumbnail linked to file') ?><br />
<textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo $post->guid ?>" class="attachmentlink"><?php echo $icon ?></a></textarea></p>
<p><?php _e('Thumbnail linked to subpost') ?><br />
<textarea rows="1" cols="40" type="text" class="attachmentlinks" readonly="readonly"><a href="<?php echo get_attachment_link($post->ID) ?>" rel="attachment" id="<?php echo $post->ID ?>"><?php echo $icon ?></a></textarea></p>
<?php endif; ?>
<?php
}

function get_udims($width, $height) {
	if ( $height <= 96 && $width <= 128 )
		return array($width, $height);
	elseif ( $width / $height > 4 / 3 )
		return array(128, (int) ($height / $width * 128));
	else
		return array((int) ($width / $height * 96), 96);
}

function wp_reset_vars($vars) {
	for ($i=0; $i<count($vars); $i += 1) {
		$var = $vars[$i];
		global $$var;

		if (!isset($$var)) {
			if (empty($_POST["$var"])) {
				if (empty($_GET["$var"]))
					$$var = '';
				else
					$$var = $_GET["$var"];
			} else {
				$$var = $_POST["$var"];
			}
		}
	}
}

?>
