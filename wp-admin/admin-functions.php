<?php

// Creates a new post from the "Write Post" form using $_POST information.
function write_post() {
	global $user_ID;

	if ( ! current_user_can('edit_posts') )
		die( __('You are not allowed to create posts or drafts on this blog.') );

	// Rename.
	$_POST['post_content']  = $_POST['content'];
	$_POST['post_excerpt']  = $_POST['excerpt'];
	$_POST['post_parent'] = $_POST['parent_id'];
	$_POST['to_ping'] = $_POST['trackback_url'];

	if (! empty($_POST['post_author_override'])) {
		$_POST['$post_author'] = (int) $_POST['post_author_override'];
	} else if (! empty($_POST['post_author'])) {
		$_POST['post_author'] = (int) $_POST['post_author'];
	} else {
		$_POST['post_author'] = (int) $_POST['user_ID'];
	}

	if ( ($_POST['post_author'] != $_POST['user_ID']) && ! current_user_can('edit_others_posts') )
		die( __('You cannot post as this user.') );
	
	// What to do based on which button they pressed
	if ('' != $_POST['saveasdraft']) $_POST['post_status'] = 'draft';
	if ('' != $_POST['saveasprivate']) $_POST['post_status'] = 'private';
	if ('' != $_POST['publish']) $_POST['post_status'] = 'publish';
	if ('' != $_POST['advanced']) $_POST['post_status'] = 'draft';
	if ('' != $_POST['savepage']) $_POST['post_status'] = 'static';

	if ( 'publish' == $_POST['post_status'] && ! current_user_can('publish_posts') )
		$_POST['post_status'] = 'draft';

	if ( !empty($_POST['edit_date']) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$_POST['post_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
		$_POST['post_date_gmt'] = get_gmt_from_date("$aa-$mm-$jj $hh:$mn:$ss");
	} 

	// Create the post.
	$post_ID = wp_insert_post($_POST);
	add_meta($post_ID);

	return $post_ID;
}

// Update an existing post with values provided in $_POST.
function edit_post() {
	global $user_ID;

	$post_ID = (int) $_POST['post_ID'];

	if ( ! current_user_can('edit_post', $post_ID) )
		die( __('You are not allowed to edit this post.') );

	// Rename.
	$_POST['ID'] = (int) $_POST['post_ID'];
	$_POST['post_content']  = $_POST['content'];
	$_POST['post_excerpt']  = $_POST['excerpt'];
	$_POST['post_parent'] = $_POST['parent_id'];
	$_POST['to_ping'] = $_POST['trackback_url'];

	if (! empty($_POST['post_author_override'])) {
		$_POST['$post_author'] = (int) $_POST['post_author_override'];
	} else if (! empty($_POST['post_author'])) {
		$_POST['post_author'] = (int) $_POST['post_author'];
	} else {
		$_POST['post_author'] = (int) $_POST['user_ID'];
	}

	if ( ($_POST['post_author'] != $_POST['user_ID']) && ! current_user_can('edit_others_posts') )
		die( __('You cannot post as this user.') );

	// What to do based on which button they pressed
	if ('' != $_POST['saveasdraft']) $_POST['post_status'] = 'draft';
	if ('' != $_POST['saveasprivate']) $_POST['post_status'] = 'private';
	if ('' != $_POST['publish']) $_POST['post_status'] = 'publish';
	if ('' != $_POST['advanced']) $_POST['post_status'] = 'draft';
	if ('' != $_POST['savepage']) $_POST['post_status'] = 'static';

	if ( 'publish' == $_POST['post_status'] && ! current_user_can('publish_posts') )
		$_POST['post_status'] = 'draft';
	
	if ( !isset($_POST['comment_status']) )
		$_POST['comment_status'] = 'closed';

	if ( !isset($_POST['ping_status']) )
		$_POST['ping_status'] = 'closed';
	
	if ( !empty($_POST['edit_date']) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$_POST['post_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
		$_POST['post_date_gmt'] = get_gmt_from_date("$aa-$mm-$jj $hh:$mn:$ss");
	} 

	wp_update_post($_POST);

	// Meta Stuff
	if ($_POST['meta']) :
		foreach ($_POST['meta'] as $key => $value) :
			update_meta($key, $value['key'], $value['value']);
		endforeach;
	endif;

	if ($_POST['deletemeta']) :
		foreach ($_POST['deletemeta'] as $key => $value) :
			delete_meta($key);
		endforeach;
	endif;

	add_meta($post_ID);
}

function edit_comment() {
	global $user_ID;

	$comment_ID = (int) $_POST['comment_ID'];
	$comment_post_ID = (int) $_POST['comment_post_ID'];

	if ( ! current_user_can('edit_post', $comment_post_ID) )
		die( __('You are not allowed to edit comments on this post, so you cannot edit this comment.') );

	$_POST['comment_author'] = $_POST['newcomment_author'];
	$_POST['comment_author_email']	= $_POST['newcomment_author_email'];
	$_POST['comment_author_url'] = $_POST['newcomment_author_url'];
	$_POST['comment_approved'] = $_POST['comment_status'];
	$_POST['comment_content'] = $_POST['content'];
	$_POST['comment_ID'] = (int) $_POST['comment_ID'];
 
	if ( !empty($_POST['edit_date']) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31) ? 31 : $jj;
		$hh = ($hh > 23) ? $hh - 24 : $hh;
		$mn = ($mn > 59) ? $mn - 60 : $mn;
		$ss = ($ss > 59) ? $ss - 60 : $ss;
		$_POST['comment_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
	}

	wp_update_comment($_POST);
}

// Get an existing post and format it for editing.
function get_post_to_edit($id) {
	$post = get_post($id);

	$post->post_content = format_to_edit($post->post_content);
	$post->post_content = apply_filters('content_edit_pre', $post->post_content);

	$post->post_excerpt = format_to_edit($post->post_excerpt);
	$post->post_excerpt = apply_filters('excerpt_edit_pre', $post->post_excerpt);

	$post->post_title = format_to_edit($post->post_title);
	$post->post_title = apply_filters('title_edit_pre', $post->post_title);

	if ($post->post_status == 'static')
		$post->page_template = get_post_meta($id, '_wp_page_template', true);	

	return $post;
}

// Default post information to use when populating the "Write Post" form.
function get_default_post_to_edit() {
	global $content, $excerpt, $edited_post_title;

	$post->post_status = 'draft';
	$post->comment_status = get_settings('default_comment_status');
	$post->ping_status = get_settings('default_ping_status');
	$post->post_pingback = get_settings('default_pingback_flag');
	$post->post_category = get_settings('default_category');
	$content = wp_specialchars($content);
	$post->post_content = apply_filters('default_content', $content);
	$post->post_title = apply_filters('default_title', $edited_post_title);
	$post->post_excerpt = apply_filters('default_excerpt', $excerpt);
	$post->page_template = 'default';
	$post->post_parent = 0;
	$post->menu_order = 0;

	return $post;
}

function get_comment_to_edit($id) {
	$comment = get_comment($id);

	$comment->comment_content = format_to_edit($comment->comment_content);
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

function wp_insert_category($catarr) {
	global $wpdb;

	extract($catarr);

	$cat_ID = (int) $cat_ID;

	// Are we updating or creating?
	if ( !empty($cat_ID) ) {
		$update = true;
	} else {
		$update = false;
		$id_result = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->categories'");
		$cat_ID = $id_result->Auto_increment;
	}

	$cat_name = wp_specialchars($cat_name);

	if ( empty($category_nicename) )
		$category_nicename = sanitize_title($cat_name, $cat_ID);
	else
		$category_nicename = sanitize_title($category_nicename, $cat_ID);

	if ( empty($category_description) )
		$category_description = '';

	if ( empty($category_parent) )
		$category_parent = 0;

	if ( !$update)
		$query = "INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_description, category_parent) VALUES ('0', '$cat_name', '$category_nicename', '$category_description', '$cat')";
	else
		$query = "UPDATE $wpdb->categories SET cat_name = '$cat_name', category_nicename = '$category_nicename', category_description = '$category_description', category_parent = '$category_parent' WHERE cat_ID = '$cat_ID'";

	$result = $wpdb->query($query);

	if ( $update ) {
		$rval = $wpdb->rows_affected;
		do_action('edit_category', $cat_ID);
	} else {
		$rval = $wpdb->insert_id;
		do_action('create_category', $cat_ID);
	}

	return $rval;
}

function wp_update_category($catarr) {
	global $wpdb;

	$cat_ID = (int) $catarr['cat_ID'];
	
	// First, get all of the original fields
	$category = get_category($cat_ID, ARRAY_A);	

	// Escape data pulled from DB.
	$category = add_magic_quotes($category);

	// Merge old and new fields with new fields overwriting old ones.
	$catarr = array_merge($category, $catarr);

	return wp_insert_category($catarr);
}

function wp_delete_category($cat_ID) {
	global $wpdb;

	$cat_ID = (int) $cat_ID;

	// Don't delete the default cat.
	if ( 1 == $cat_ID )
		return 0;

	$category = get_category($cat_ID);

	$parent = $category->category_parent;

	// Delete the category.
	$wpdb->query("DELETE FROM $wpdb->categories WHERE cat_ID = '$cat_ID'");

	// Update children to point to new parent.
	$wpdb->query("UPDATE $wpdb->categories SET category_parent = '$parent' WHERE category_parent = '$cat_ID'");

	// TODO: Only set categories to general if they're not in another category already
	$wpdb->query("UPDATE $wpdb->post2cat SET category_id='1' WHERE category_id='$cat_ID'");

	do_action('delete_category', $cat_ID);

	return 1;
}

function wp_delete_user($id, $reassign = 'novalue') {
	global $wpdb;

	$id = (int) $id;
	
	if($reassign == 'novalue') {
		$post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_author = $id");
	
		if ($post_ids) {
			$post_ids = implode(',', $post_ids);
			
			// Delete comments, *backs
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_post_ID IN ($post_ids)");
			// Clean cats
			$wpdb->query("DELETE FROM $wpdb->post2cat WHERE post_id IN ($post_ids)");
			// Clean post_meta
			$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id IN ($post_ids)");
			// Delete posts
			$wpdb->query("DELETE FROM $wpdb->posts WHERE post_author = $id");
		}
	
		// Clean links
		$wpdb->query("DELETE FROM $wpdb->links WHERE link_owner = $id");
	} else {
		$reassign = (int)$reassign;
		$wpdb->query("UPDATE $wpdb->posts SET post_author = {$reassign} WHERE post_author = {$id}");
		$wpdb->query("UPDATE $wpdb->links SET link_owner = {$reassign} WHERE link_owner = {$id}");
	}

	// FINALLY, delete user
	$wpdb->query("DELETE FROM $wpdb->users WHERE ID = $id");

	do_action('delete_user', $id);

	return true;
}

function url_shorten ($url) {
	$short_url = str_replace('http://', '', stripslashes($url));
	$short_url = str_replace('www.', '', $short_url);
	if ('/' == substr($short_url, -1))
		$short_url = substr($short_url, 0, -1);
	if (strlen($short_url) > 35)
		$short_url =  substr($short_url, 0, 32).'...';
	return $short_url;
}

function selected($selected, $current) {
	if ($selected == $current) echo ' selected="selected"';
}

function checked($checked, $current) {
	if ($checked == $current) echo ' checked="checked"';
}

function return_categories_list( $parent = 0 ) {
	global $wpdb;
	return $wpdb->get_col("SELECT cat_ID FROM $wpdb->categories WHERE category_parent = $parent ORDER BY category_count DESC");
}

function get_nested_categories($default = 0, $parent = 0) {
 global $post_ID, $mode, $wpdb;

 if ($post_ID) {
   $checked_categories = $wpdb->get_col("
     SELECT category_id
     FROM $wpdb->categories, $wpdb->post2cat
     WHERE $wpdb->post2cat.category_id = cat_ID AND $wpdb->post2cat.post_id = '$post_ID'
     ");

   if(count($checked_categories) == 0)
   {
     // No selected categories, strange
     $checked_categories[] = $default;
   }

 } else {
   $checked_categories[] = $default;
 }

 $cats = return_categories_list($parent);
 $result = array();

	if ( is_array( $cats ) ) {
		foreach($cats as $cat) {
			$result[$cat]['children'] = get_nested_categories($default, $cat);
			$result[$cat]['cat_ID'] = $cat;
			$result[$cat]['checked'] = in_array($cat, $checked_categories);
			$result[$cat]['cat_name'] = get_the_category_by_ID($cat);
		}
	}

	return $result;
}

function write_nested_categories($categories) {
 foreach($categories as $category) {
   echo '<label for="category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'],
     '" type="checkbox" name="post_category[]" id="category-', $category['cat_ID'], '"',
     ($category['checked'] ? ' checked="checked"' : ""), '/> ', wp_specialchars($category['cat_name']), "</label>\n";

   if(isset($category['children'])) {
     echo "\n<span class='cat-nest'>\n";
     write_nested_categories($category['children']);
     echo "</span>\n";
   }
 }
}

function dropdown_categories($default = 0) {
 write_nested_categories(get_nested_categories($default));
} 

// Dandy new recursive multiple category stuff.
function cat_rows($parent = 0, $level = 0, $categories = 0) {
	global $wpdb, $class;

	if ( !$categories )
		$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");

	if ($categories) {
		foreach ($categories as $category) {
			if ($category->category_parent == $parent) {
				$category->cat_name = wp_specialchars($category->cat_name);
				$count = $wpdb->get_var("SELECT COUNT(post_id) FROM $wpdb->post2cat WHERE category_id = $category->cat_ID");
				$pad = str_repeat('&#8212; ', $level);
				if ( current_user_can('manage_categories') )
					$edit = "<a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>" . __('Edit') . "</a></td><td><a href='categories.php?action=delete&amp;cat_ID=$category->cat_ID' onclick=\"return confirm('".  sprintf(__("You are about to delete the category \'%s\'.  All of its posts will go to the default category.\\n  \'OK\' to delete, \'Cancel\' to stop."), $wpdb->escape($category->cat_name)) . "')\" class='delete'>" .  __('Delete') . "</a>";
				else
					$edit = '';
				
				$class = ('alternate' == $class) ? '' : 'alternate';
				echo "<tr class='$class'><th scope='row'>$category->cat_ID</th><td>$pad $category->cat_name</td>
				<td>$category->category_description</td>
				<td>$count</td>
				<td>$edit</td>
				</tr>";
				cat_rows($category->cat_ID, $level + 1, $categories);
			}
		}
	} else {
		return false;
	}
}

function page_rows( $parent = 0, $level = 0, $pages = 0 ) {
	global $wpdb, $class, $post;
	if (!$pages)
		$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' ORDER BY menu_order");

	if ($pages) {
		foreach ($pages as $post) { start_wp();
			if ($post->post_parent == $parent) {
				$post->post_title = wp_specialchars($post->post_title);
				$pad = str_repeat('&#8212; ', $level);
				$id = $post->ID;
				$class = ('alternate' == $class) ? '' : 'alternate';
?>
  <tr class='<?php echo $class; ?>'> 
    <th scope="row"><?php echo $post->ID; ?></th> 
    <td>
      <?php echo $pad; ?><?php the_title() ?> 
    </td> 
    <td><?php the_author() ?></td>
    <td><?php echo mysql2date('Y-m-d g:i a', $post->post_modified); ?></td> 
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
    <td><?php if ( current_user_can('edit_pages') ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if ( current_user_can('edit_pages') ) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), the_title('','',0)) . "')\">" . __('Delete') . "</a>"; } ?></td> 
  </tr> 

<?php
				page_rows($id, $level + 1, $pages);
			}
		}
	} else {
		return false;
	}
}

function wp_dropdown_cats($currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {
	global $wpdb, $bgcolor;
	if (!$categories) {
		$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");
	}
	if ($categories) {
		foreach ($categories as $category) { if ($currentcat != $category->cat_ID && $parent == $category->category_parent) {
			$count = $wpdb->get_var("SELECT COUNT(post_id) FROM $wpdb->post2cat WHERE category_id = $category->cat_ID");
			$pad = str_repeat('&#8211; ', $level);
			$category->cat_name = wp_specialchars($category->cat_name);
			echo "\n\t<option value='$category->cat_ID'";
			if ($currentparent == $category->cat_ID)
				echo " selected='selected'";
			echo ">$pad$category->cat_name</option>";
			wp_dropdown_cats($currentcat, $currentparent, $category->cat_ID, $level + 1, $categories);
		} }
	} else {
		return false;
	}
}

function wp_create_thumbnail($file, $max_side, $effect = '') {

    // 1 = GIF, 2 = JPEG, 3 = PNG

    if(file_exists($file)) {
        $type = getimagesize($file);
        
        // if the associated function doesn't exist - then it's not
        // handle. duh. i hope.
        
        if(!function_exists('imagegif') && $type[2] == 1) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagejpeg') && $type[2] == 2) {
            $error = __('Filetype not supported. Thumbnail not created.');
        }elseif(!function_exists('imagepng') && $type[2] == 3) {
            $error = __('Filetype not supported. Thumbnail not created.');
        } else {
        
            // create the initial copy from the original file
            if($type[2] == 1) {
                $image = imagecreatefromgif($file);
            } elseif($type[2] == 2) {
                $image = imagecreatefromjpeg($file);
            } elseif($type[2] == 3) {
                $image = imagecreatefrompng($file);
            }
            
			if (function_exists('imageantialias'))
	            imageantialias($image, TRUE);
            
            $image_attr = getimagesize($file);
            
            // figure out the longest side
            
            if($image_attr[0] > $image_attr[1]) {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_width = $max_side;
                
                $image_ratio = $image_width/$image_new_width;
                $image_new_height = $image_height/$image_ratio;
                //width is > height
            } else {
                $image_width = $image_attr[0];
                $image_height = $image_attr[1];
                $image_new_height = $max_side;
                
                $image_ratio = $image_height/$image_new_height;
                $image_new_width = $image_width/$image_ratio;
                //height > width
            }
            
            $thumbnail = imagecreatetruecolor($image_new_width, $image_new_height);
            @imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
            
            // move the thumbnail to it's final destination
            
            $path = explode('/', $file);
            $thumbpath = substr($file, 0, strrpos($file, '/')) . '/thumb-' . $path[count($path)-1];
            
            if($type[2] == 1) {
                if(!imagegif($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 2) {
                if(!imagejpeg($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            } elseif($type[2] == 3) {
                if(!imagepng($thumbnail, $thumbpath)) {
                    $error = __("Thumbnail path invalid");
                }
            }
            
        }
    }
    
    if(!empty($error))
    {
        return $error;
    }
    else
    {
        return 1;
    }
}

// Some postmeta stuff
function has_meta($postid) {
	global $wpdb;

	return $wpdb->get_results("
		SELECT meta_key, meta_value, meta_id, post_id
		FROM $wpdb->postmeta
		WHERE post_id = '$postid'
		ORDER BY meta_key,meta_id",ARRAY_A);

}

function list_meta($meta) {
	global $post_ID;	
	// Exit if no meta
	if (!$meta) return;
	$count = 0;
?>
<table id='meta-list' cellpadding="3">
	<tr>
		<th><?php _e('Key') ?></th>
		<th><?php _e('Value') ?></th>
		<th colspan='2'><?php _e('Action') ?></th>
	</tr>
<?php
		
	foreach ($meta as $entry) {
		++$count;
		if ( $count % 2 ) $style = 'alternate';
		else $style = '';
		if ( '_' == $entry['meta_key']{0} ) $style .= ' hidden';
		echo "
	<tr class='$style'>
		<td valign='top'><input name='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='{$entry['meta_key']}' /></td>
		<td><textarea name='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>
		<td align='center' width='10%'><input name='updatemeta' type='submit' class='updatemeta' tabindex='6' value='" . __('Update') ."' /></td>
		<td align='center' width='10%'><input name='deletemeta[{$entry['meta_id']}]' type='submit' class='deletemeta' tabindex='6' value='" . __('Delete') ."' /></td>
	</tr>
";
	}
	echo "
	</table>
";
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
<table cellspacing="3" cellpadding="3">
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
	foreach($keys as $key) {
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
<p class="submit"><input type="submit" name="updatemeta" tabindex="9" value="<?php _e('Add Custom Field &raquo;') ?>" /></p>
<?php
}

function add_meta($post_ID) {
	global $wpdb;
	
	$metakeyselect = $wpdb->escape( stripslashes( trim($_POST['metakeyselect']) ) );
	$metakeyinput  = $wpdb->escape( stripslashes( trim($_POST['metakeyinput']) ) );
	$metavalue     = $wpdb->escape( stripslashes( trim($_POST['metavalue']) ) );

	if (!empty($metavalue) && ((('#NONE#' != $metakeyselect) && !empty($metakeyselect)) || !empty($metakeyinput))) {
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
	}
} // add_meta

function delete_meta($mid) {
	global $wpdb;

	$result = $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_id = '$mid'");
}

function update_meta($mid, $mkey, $mvalue) {
	global $wpdb;

	return $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '$mkey', meta_value = '$mvalue' WHERE meta_id = '$mid'");
}

function touch_time($edit = 1, $for_post = 1) {
	global $month, $post, $comment;
	if ( $for_post && ('draft' == $post->post_status) ) {
		$checked = 'checked="checked" ';
		$edit = false;
	} else {
		$checked = ' ';
	}

	echo '<fieldset><legend><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp" '.$checked.'/> <label for="timestamp">' . __('Edit timestamp') . '</label></legend>';
	
	$time_adj = time() + (get_settings('gmt_offset') * 3600);
	$post_date = ($for_post) ? $post->post_date : $comment->comment_date;
	$jj = ($edit) ? mysql2date('d', $post_date) : gmdate('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $post_date) : gmdate('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $post_date) : gmdate('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $post_date) : gmdate('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $post_date) : gmdate('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $post_date) : gmdate('s', $time_adj);

	echo "<select name=\"mm\">\n";
	for ($i=1; $i < 13; $i=$i+1) {
		echo "\t\t\t<option value=\"$i\"";
		if ($i == $mm)
		echo " selected='selected'";
		if ($i < 10) {
			$ii = "0".$i;
		} else {
			$ii = "$i";
		}
		echo ">".$month["$ii"]."</option>\n";
	} 

?>
</select>
<input type="text" name="jj" value="<?php echo $jj; ?>" size="2" maxlength="2" />
<input type="text" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" /> @ 
<input type="text" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" /> : 
<input type="text" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" /> 
<input type="hidden" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" /> 
<?php _e('Existing timestamp'); ?>: 
	<?php
		// We might need to readjust to display proper existing timestamp
		if ( $for_post && ('draft' == $post->post_status) ) {
			$jj = mysql2date('d', $post_date);
			$mm = mysql2date('m', $post_date);
			$aa = mysql2date('Y', $post_date);
			$hh = mysql2date('H', $post_date);
			$mn = mysql2date('i', $post_date);
			$ss = mysql2date('s', $post_date);
		}
		echo "{$month[$mm]} $jj, $aa @ $hh:$mn"; ?>
</fieldset>
	<?php
}

function check_admin_referer() {
	$adminurl = strtolower( get_settings('siteurl') ) . '/wp-admin';
	$referer = strtolower( $_SERVER['HTTP_REFERER'] );
	if ( !strstr($referer, $adminurl) )
		die(__('Sorry, you need to <a href="http://codex.wordpress.org/Enable_Sending_Referrers">enable sending referrers</a> for this feature to work.'));
	do_action('check_admin_referer');
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
			foreach($markerdata as $markerline) {
				if (strstr($markerline, "# BEGIN {$marker}\n")) $state = false;
				if ($state) fwrite($f, "{$markerline}\n");
				if (strstr($markerline, "# END {$marker}\n")) {
					fwrite($f, "# BEGIN {$marker}\n");
					if(is_array($insertion)) foreach($insertion as $insertline) fwrite($f, "{$insertline}\n");
					fwrite($f, "# END {$marker}\n");
					$state = true;
					$foundit = true;
				}
			}
		}
		if (!$foundit) {
			fwrite($f, "# BEGIN {$marker}\n");
			foreach($insertion as $insertline) fwrite($f, "{$insertline}\n");
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
	$result = array();

	if (!file_exists($filename)) {
		return $result;
	}

	if($markerdata = explode("\n", implode('', file($filename))));
	{
		$state = false;
		foreach($markerdata as $markerline) {
			if(strstr($markerline, "# END {$marker}"))	$state = false;
			if($state) $result[] = $markerline;
			if(strstr($markerline, "# BEGIN {$marker}")) $state = true;
		}
	}

	return $result;
}

function save_mod_rewrite_rules() {
	global $is_apache, $wp_rewrite;
	$home_path = get_home_path();

	if (! $wp_rewrite->using_mod_rewrite_permalinks())
		return;

	if ( ! ((!file_exists($home_path.'.htaccess') && is_writable($home_path)) || is_writable($home_path.'.htaccess')) )
		return;

	if (! $is_apache)
		return;

	$rules = explode("\n", $wp_rewrite->mod_rewrite_rules());
	insert_with_markers($home_path.'.htaccess', 'WordPress', $rules);
}

function the_quicktags () {
// Browser detection sucks, but until Safari supports the JS needed for this to work people just assume it's a bug in WP
if ( !strstr($_SERVER['HTTP_USER_AGENT'], 'Safari') ) :
	echo '
	<div id="quicktags">
	<script src="../wp-includes/js/quicktags.js" type="text/javascript"></script>
	<script type="text/javascript">edToolbar();</script>
';
	echo '</div>';
endif;
}

function validate_current_theme() {
	$theme_loc = 'wp-content/themes';
	$theme_root = ABSPATH . $theme_loc;

	$template = get_settings('template');
	$stylesheet = get_settings('stylesheet');

	if (($template != 'default') && (! file_exists("$theme_root/$template/index.php"))) {
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');
		return false;
	}

	if (($stylesheet != 'default') && (! file_exists("$theme_root/$stylesheet/style.css"))) {
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
	$page_templates = array();

	if( is_array( $templates ) ) {
		foreach ($templates as $template) {
			$template_data = implode('', file(ABSPATH . $template));
			preg_match("|Template Name:(.*)|i", $template_data, $name);
			preg_match("|Description:(.*)|i", $template_data, $description);

			$name = $name[1];
			$description = $description[1];

			if (! empty($name)) {
				$page_templates[trim($name)] = basename($template);
			}
		}
	}

	return $page_templates;
}

function page_template_dropdown($default = '') {
	$templates = get_page_templates();
	foreach (array_keys($templates) as $template) :
		if ($default == $templates[$template]) $selected = " selected='selected'";
		else $selected = '';
		echo "\n\t<option value='" . $templates[$template] . "' $selected>$template</option>";
		endforeach;
}

function parent_dropdown($default = 0, $parent = 0, $level = 0) {
	global $wpdb, $post_ID;
	$items = $wpdb->get_results("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_status = 'static' ORDER BY menu_order");

	if ($items) {
		foreach ($items as $item) {
			// A page cannot be it's own parent.
			if (!empty($post_ID)) {
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
			parent_dropdown($default, $item->ID, $level + 1);
		}
	} else {
		return false;
	}
}

function user_can_access_admin_page() {
	global $pagenow;
	global $menu;
	global $submenu;

	$parent = get_admin_page_parent();

	foreach ($menu as $menu_array) {
		//echo "parent array: " . $menu_array[2];
		if ($menu_array[2] == $parent) {
			if ( !current_user_can($menu_array[1]) ) {
				return false;
			} else {
				break;
			}
		}
	}

	if (isset($submenu[$parent])) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ($submenu_array[2] == $pagenow) {
				if ( !current_user_can($submenu_array[1]) ) {
					return false;
				} else {
					return true;
				}
			}
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

	if (isset($title) && ! empty($title)) {
		return $title;
	}

	$parent = get_admin_page_parent();
	if (empty($parent)) {
		foreach ($menu as $menu_array) {
			if (isset($menu_array[3])) {
				if ($menu_array[2] == $pagenow) {
					$title = $menu_array[3];
					return $menu_array[3];
				} else if (isset($plugin_page) && ($plugin_page == $menu_array[2])) {
					$title = $menu_array[3];
					return $menu_array[3];
				}
			}
		}
	} else {
		foreach (array_keys($submenu) as $parent) {
			foreach ($submenu[$parent] as $submenu_array) {
				if (isset($submenu_array[3])) {
					if ($submenu_array[2] == $pagenow) {
						$title = $submenu_array[3];
						return $submenu_array[3];
					} else if (isset($plugin_page) && ($plugin_page == $submenu_array[2])) {
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

	if (isset($parent_file) && ! empty($parent_file)) {
		return $parent_file;
	}

	if ($pagenow == 'admin.php' && isset($plugin_page)) {
		foreach ($menu as $parent_menu) {
			if ($parent_menu[2] == $plugin_page) {
				$parent_file = $plugin_page;
				return $plugin_page;
			}
		}
	}
		
	foreach (array_keys($submenu) as $parent) {
		foreach ($submenu[$parent] as $submenu_array) {
			if ($submenu_array[2] == $pagenow) {
				$parent_file = $parent;
				return $parent;
			} else if (isset($plugin_page) && ($plugin_page == $submenu_array[2])) {
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

	$menu[] = array($menu_title, $access_level, $file, $page_title);

	$admin_page_hooks[$file] = sanitize_title($menu_title);

	$hookname = get_plugin_page_hookname($file, '');
	if ( !empty($function) && !empty($hookname) )
		add_action($hookname, $function);

	return $hookname;
}

function add_submenu_page($parent, $page_title, $menu_title, $access_level, $file, $function = '') {
	global $submenu;
	global $menu;

	$parent = plugin_basename($parent);
	$file = plugin_basename($file);

	// If the parent doesn't already have a submenu, add a link to the parent
	// as the first item in the submenu.  If the submenu file is the same as the
	// parent file someone is trying to link back to the parent manually.  In
	// this case, don't automatically add a link back to avoid duplication.
	if (! isset($submenu[$parent]) && $file != $parent) {
		foreach ($menu as $parent_menu) {
			if ($parent_menu[2] == $parent) {
				$submenu[$parent][] = $parent_menu;
			}
		}
	}
	
	$submenu[$parent][] = array($menu_title, $access_level, $file, $page_title);

	$hookname = get_plugin_page_hookname($file, $parent);
	if ( !empty($function) && !empty($hookname) )
		add_action($hookname, $function);

	return $hookname;
}

function add_options_page($page_title, $menu_title, $access_level, $file, $function = '') {
	return add_submenu_page('options-personal.php', $page_title, $menu_title, $access_level, $file, $function);
}

function add_management_page($page_title, $menu_title, $access_level, $file, $function = '') {
	return add_submenu_page('edit.php', $page_title, $menu_title, $access_level, $file, $function);
}

function validate_file($file, $allowed_files = '') {
	if ( false !== strpos($file, './'))
		return 1;
	
	if (':' == substr($file,1,1))
		return 2;

	if ( !empty($allowed_files) && (! in_array($file, $allowed_files)) )
		return 3;

	return 0;
}

function validate_file_to_edit($file, $allowed_files = '') {
	$file = stripslashes($file);

	$code = validate_file($file, $allowed_files);

	if (! $code)
		return $file;

	switch ($code) {
	case 1:
		die (__('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.'));
	
	case 2:
		die (__('Sorry, can&#8217;t call files with their real path.'));

	case 3:
		die (__('Sorry, that file cannot be edited.'));
	}
}

function get_home_path() {
	$home = get_settings('home');
	if ( $home != '' && $home != get_settings('siteurl') ) {
		$home_path = parse_url($home);
		$home_path = $home_path['path'];
		$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"]);
		$home_path = trailingslashit($root . $home_path);
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

function get_real_file_to_edit($file) {
	if ('index.php' == $file ||
			 '.htaccess' == $file) {
		$real_file = get_home_path() . $file;
	} else {
		$real_file = ABSPATH . $file;
	}

	return $real_file;
}

$wp_file_descriptions = 
	array(
	'index.php' => __('Main Index Template'),
	'style.css' => __('Stylesheet'),
	'comments.php' => __('Comments Template'),
	'comments-popup.php' => __('Popup Comments Template'),
	'footer.php' => __('Footer Template'),
	'header.php' => __('Header Template'),
	'sidebar.php' => __('Sidebar Template'),
	'archive.php' => __('Archive Template'),
	'category.php' => __('Category Template'),
	'page.php' => __('Page Template'),
	'search.php' => __('Search Template'),
	'single.php' => __('Post Template'),
	'404.php' => __('404 Template'),
	'my-hacks.php' => __('my-hacks.php (legacy hacks support)'),
	'.htaccess' => __('.htaccess (for rewrite rules)'),
	// Deprecated files
	'wp-layout.css' => __('Stylesheet'),
	'wp-comments.php' => __('Comments Template'),
	'wp-comments-popup.php' => __('Popup Comments Template')
	);

function get_file_description($file) {
	global $wp_file_descriptions;

	if ( isset($wp_file_descriptions[basename($file)] ) ) {
		return $wp_file_descriptions[basename($file)];
	} elseif ( file_exists( ABSPATH . $file ) ) {
		$template_data = implode('', file(ABSPATH . $file));
		if ( preg_match("|Template Name:(.*)|i", $template_data, $name) )
			return $name[1];
	}

	return basename( $file );
}

function update_recently_edited($file) {
	$oldfiles = (array) get_option('recently_edited');
	if ($oldfiles) {
		$oldfiles = array_reverse($oldfiles);
		$oldfiles[] = $file;
		$oldfiles = array_reverse($oldfiles);
		$oldfiles = array_unique($oldfiles);
		if ( 5 < count($oldfiles) )
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
	if ( preg_match("|Version:(.*)|i", $plugin_data, $version) )
		$version = $version[1];
	else
		$version ='';

	$description = wptexturize($description[1]);

	$name = $plugin_name[1];
	$name = trim($name);
	$plugin = $name;
	if ('' != $plugin_uri[1] && '' != $name) {
		$plugin = '<a href="' . $plugin_uri[1] . '" title="' . __('Visit plugin homepage') . '">' . $plugin . '</a>';
	}

	if ('' == $author_uri[1]) {
		$author = $author_name[1];
	} else {
		$author = '<a href="' . $author_uri[1] . '" title="' . __('Visit author homepage') . '">' . $author_name[1] . '</a>';
	}

	return array('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'Template' => $template[1]);
}

function get_plugins() {
	global $wp_plugins;

	if (isset($wp_plugins)) {
		return $wp_plugins;
	}

	$wp_plugins = array();
	$plugin_loc = 'wp-content/plugins';
	$plugin_root = ABSPATH . $plugin_loc;

	// Files in wp-content/plugins directory
	$plugins_dir = @ dir($plugin_root);
	if ($plugins_dir) {
		while(($file = $plugins_dir->read()) !== false) {
			if ( preg_match('|^\.+$|', $file) )
				continue;
			if (is_dir($plugin_root . '/' . $file)) {
				$plugins_subdir = @ dir($plugin_root . '/' . $file);
				if ($plugins_subdir) {
					while(($subfile = $plugins_subdir->read()) !== false) {
						if ( preg_match('|^\.+$|', $subfile) )
							continue;
						if ( preg_match('|\.php$|', $subfile) )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( preg_match('|\.php$|', $file) ) 
					$plugin_files[] = $file;
			}
		}
	}

	if (!$plugins_dir || !$plugin_files) {
		return $wp_plugins;
	}

	sort($plugin_files);

	foreach($plugin_files as $plugin_file) {
		$plugin_data = get_plugin_data("$plugin_root/$plugin_file");
	  
		if (empty($plugin_data['Name'])) {
			continue;
		}

		$wp_plugins[plugin_basename($plugin_file)] = $plugin_data;
	}

	return $wp_plugins;
}

function get_plugin_page_hookname($plugin_page, $parent_page) {
	global $admin_page_hooks;

	$parent = get_admin_page_parent();

	if ( empty($parent_page) || 'admin.php' == $parent_page ) {
		if ( isset($admin_page_hooks[$plugin_page]) )
			$page_type = 'toplevel';
		else if ( isset($admin_page_hooks[$parent]) )
			$page_type = $admin_page_hooks[$parent];
	} else if ( isset($admin_page_hooks[$parent_page]) ) {
		$page_type = $admin_page_hooks[$parent_page];
	} else {
		$page_type = 'admin';
	}

	$plugin_name = preg_replace('!\.php!', '', $plugin_page);

	return $page_type . '_page_' . $plugin_name;
}

function get_plugin_page_hook($plugin_page, $parent_page) {
	global $wp_filter;
	
	$hook = get_plugin_page_hookname($plugin_page, $parent_page);
	if ( isset($wp_filter[$hook]) )
		return $hook;
	else
		return '';
}

function browse_happy() {
	$getit = __('WordPress recommends a better browser');
	echo '
	<p id="bh" style="text-align: center;"><a href="http://browsehappy.com/" title="' . $getit . '"><img src="images/browse-happy.gif" alt="Browse Happy" /></a></p>
	';
}
if ( strstr( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) )
	add_action('admin_footer', 'browse_happy');

function documentation_link( $for ) {
	return;
}

function register_importer($id, $name, $description, $callback) {
	global $wp_importers;
	
	$wp_importers[$id] = array($name, $description, $callback);
}

function get_importers() {
	global $wp_importers;

	return $wp_importers;
}

?>