<?php

function wp_admin_head() {
	do_action('wp_head', '');
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

function get_nested_categories($default = 0) {
 global $post_ID, $tablecategories, $tablepost2cat, $mode, $wpdb;

 if ($post_ID) {
   $checked_categories = $wpdb->get_col("
     SELECT category_id
     FROM  $tablecategories, $tablepost2cat
     WHERE $tablepost2cat.category_id = cat_ID AND $tablepost2cat.post_id = '$post_ID'
     ");
 } else {
   $checked_categories[] = $default;
 }

 $categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY category_parent DESC, cat_name ASC");
 $result = array();
 foreach($categories as $category) {
   $array_category = get_object_vars($category);
   $me = 0 + $category->cat_ID;
   $parent = 0 + $category->category_parent;
	if (isset($result[$me]))   $array_category['children'] = $result[$me];
   $array_category['checked'] = in_array($category->cat_ID, $checked_categories);
   $array_category['cat_name'] = stripslashes($category->cat_name);
   $result[$parent][] = $array_category;
 }

 return $result[0];
}

function write_nested_categories($categories) {
 foreach($categories as $category) {
   echo '<label for="category-', $category['cat_ID'], '" class="selectit"><input value="', $category['cat_ID'],
     '" type="checkbox" name="post_category[]" id="category-', $category['cat_ID'], '"',
     ($category['checked'] ? ' checked="checked"' : ""), '/> ', $category['cat_name'], "</label>\n";

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
	global $wpdb, $tablecategories, $tablepost2cat, $bgcolor;
	if (!$categories) {
		$categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY cat_name");
	}
	if ($categories) {
		foreach ($categories as $category) {
			if ($category->category_parent == $parent) {
				$count = $wpdb->get_var("SELECT COUNT(post_id) FROM $tablepost2cat WHERE category_id = $category->cat_ID");
				$pad = str_repeat('&#8212; ', $level);

				$bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
				echo "<tr style='background-color: $bgcolor'><th scope='row'>$category->cat_ID</th><td>$pad $category->cat_name</td>
				<td>$category->category_description</td>
				<td>$count</td>
				<td><a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>" . __('Edit') . "</a></td><td><a href='categories.php?action=Delete&amp;cat_ID=$category->cat_ID' onclick=\"return confirm('".  sprintf(__("You are about to delete the category \'%s\'.  All of its posts will go to the default category.\\n  \'OK\' to delete, \'Cancel\' to stop."), addslashes($category->cat_name)) . "')\" class='delete'>" .  __('Delete') . "</a></td>
				</tr>";
				cat_rows($category->cat_ID, $level + 1);
			}
		}
	} else {
		return false;
	}
}

function wp_dropdown_cats($currentcat, $currentparent = 0, $parent = 0, $level = 0, $categories = 0) {
	global $wpdb, $tablecategories, $tablepost2cat, $bgcolor;
	if (!$categories) {
		$categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY cat_name");
	}
	if ($categories) {
		foreach ($categories as $category) { if ($currentcat != $category->cat_ID && $parent == $category->category_parent) {
			$count = $wpdb->get_var("SELECT COUNT(post_id) FROM $tablepost2cat WHERE category_id = $category->cat_ID");
			$pad = str_repeat('&#8211; ', $level);
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
            @imagecopyresized($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]);
            
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
	global $wpdb, $tablepostmeta;

	return $wpdb->get_results("
		SELECT meta_key, meta_value, meta_id, post_id
		FROM $tablepostmeta
		WHERE post_id = '$postid'
		ORDER BY meta_key,meta_id",ARRAY_A);

}

function list_meta($meta) {
	global $post_ID;	
	// Exit if no meta
	if (!$meta) return;	
?>
<table id='meta-list' cellpadding="3">
	<tr>
		<th><?php _e('Key') ?></th>
		<th><?php _e('Value') ?></th>
		<th colspan='2'><?php _e('Action') ?></th>
	</tr>
<?php
		
	foreach ($meta as $entry) {
		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';
		echo "
	<tr $style>
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
	global $wpdb, $tablepostmeta;
	
	$keys = $wpdb->get_col("
		SELECT meta_key
		FROM $tablepostmeta
		GROUP BY meta_key
		ORDER BY meta_key");
	
	return $keys;
}

function meta_form() {
	global $wpdb, $tablepostmeta;
	$keys = $wpdb->get_col("
		SELECT meta_key
		FROM $tablepostmeta
		GROUP BY meta_key
		ORDER BY meta_id DESC
		LIMIT 10");
?>
<h3><?php _e('Add a new custom field to this post:') ?></h3>
<table cellspacing="3" cellpadding="3">
	<tr>
<th colspan="2"><?php _e('Key') ?></th>
<th><?php _e('Value') ?></th>
</tr>
	<tr valign="top">
		<td align="right" width="18%">
<?php if ($keys) : ?>
<select id="metakeyselect" name="metakeyselect" tabindex="7">
<option value="#NONE#">- Select -</option>
<?php
	foreach($keys as $key) {
		echo "\n\t<option value='$key'>$key</option>";
	}
?>
</select> or 
<?php endif; ?>
</td>
<td><input type="text" id="metakeyinput" name="metakeyinput" tabindex="7" /></td>
		<td><textarea id="metavalue" name="metavalue" rows="3" cols="25" tabindex="7"></textarea></td>
	</tr>

</table>
<p class="submit"><input type="submit" name="updatemeta" tabindex="7" value="<?php _e('Add Custom Field &raquo;') ?>"></p>
<?php
}

function add_meta($post_ID) {
	global $wpdb, $tablepostmeta;
	
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
				INSERT INTO $tablepostmeta 
				(post_id,meta_key,meta_value) 
				VALUES ('$post_ID','$metakey','$metavalue')
			");
	}
} // add_meta

function delete_meta($mid) {
	global $wpdb, $tablepostmeta;

	$result = $wpdb->query("DELETE FROM $tablepostmeta WHERE meta_id = '$mid'");
}

function update_meta($mid, $mkey, $mvalue) {
	global $wpdb, $tablepostmeta;

	return $wpdb->query("UPDATE $tablepostmeta SET meta_key = '$mkey', meta_value = '$mvalue' WHERE meta_id = '$mid'");
}

function touch_time($edit = 1) {
	global $month, $postdata;
	// echo $postdata['Date'];
	if ('draft' == $postdata->post_status) {
		$checked = 'checked="checked" ';
		$edit = false;
	} else {
		$checked = ' ';
	}

	echo '<p><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp" '.$checked.'/> <label for="timestamp">' . __('Edit timestamp') . '</label> <a href="http://wordpress.org/docs/reference/post/#edit_timestamp" title="' . __('Help on changing the timestamp') . '">?</a><br />';
	
	$time_adj = time() + (get_settings('gmt_offset') * 3600);
	$post_date = $postdata->post_date;
	$jj = ($edit) ? mysql2date('d', $post_date) : gmdate('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $post_date) : gmdate('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $post_date) : gmdate('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $post_date) : gmdate('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $post_date) : gmdate('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $post_date) : gmdate('s', $time_adj);

	echo '<input type="text" name="jj" value="'.$jj.'" size="2" maxlength="2" />'."\n";
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
	} ?>
</select>
<input type="text" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" /> @ 
<input type="text" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" /> : 
<input type="text" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" /> : 
<input type="text" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" /> </p>
	<?php
}

function check_admin_referer() {
  $adminurl = strtolower(get_settings('siteurl')).'/wp-admin';
  $referer = strtolower($_SERVER['HTTP_REFERER']);
  if ( !strstr($referer, $adminurl) ) {
    die('Sorry, you need to enable sending referrers, for this feature to work.');
  }
}

?>