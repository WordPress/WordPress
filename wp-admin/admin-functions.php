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
 global $post, $tablecategories, $tablepost2cat, $mode, $wpdb;

 if ($post->ID) {
   $checked_categories = $wpdb->get_col("
     SELECT category_id
     FROM  $tablecategories, $tablepost2cat
     WHERE $tablepost2cat.category_id = cat_ID AND $tablepost2cat.post_id = '$post->ID'
     ");
 } else {
   $checked_categories[] = $default;
 }

 $categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY category_parent DESC");
 $result = array();
 foreach($categories as $category) {
   $array_category = get_object_vars($category);
   $me = 0 + $category->cat_ID;
   $parent = 0 + $category->category_parent;
   $array_category['children'] = $result[$me];
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
     write_nested_categories($category['children'], $count);
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
				echo "<tr style='background-color: $bgcolor'><td>$pad $category->cat_name</td>
				<td>$category->category_description</td>
				<td>$count</td>
				<td><a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>Edit</a></td><td><a href='categories.php?action=Delete&amp;cat_ID=$category->cat_ID' onclick=\"return confirm('You are about to delete the category \'". addslashes($category->cat_name) ."\' and all its posts will go to the default category.\\n  \'OK\' to delete, \'Cancel\' to stop.')\" class='delete'>Delete</a></td>
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
            $error = 'Filetype not supported. Thumbnail not created.';
        }elseif(!function_exists('imagejpeg') && $type[2] == 2) {
            $error = 'Filetype not supported. Thumbnail not created.';
        }elseif(!function_exists('imagepng') && $type[2] == 3) {
            $error = 'Filetype not supported. Thumbnail not created.';
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
                    $error = "Thumbnail path invalid";
                }
            } elseif($type[2] == 2) {
                if(!imagejpeg($thumbnail, $thumbpath)) {
                    $error = "Thumbnail path invalid";
                }
            } elseif($type[2] == 3) {
                if(!imagepng($thumbnail, $thumbpath)) {
                    $error = "Thumbnail path invalid";
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
		WHERE post_id = $postid
		ORDER BY meta_key,meta_id",ARRAY_A);

}

function list_meta($meta) {
	global $post_ID;	
	// Exit if no meta
	if (!$meta) return;

	
	print "
	<table id='meta-list'>
		<tr>
			<th>Key</th>
			<th>Value</th>
			<th>&nbsp</th>
		</tr>\n";
		
	foreach ($meta as $entry) {
		// TBD: Still need to add edit/del logic...
		print "
		<tr>
			<td>{$entry['meta_key']}</td>
			<td>{$entry['meta_value']}</td>
			<td><a href=\"?action=deletemeta&amp;meta_id={$entry['meta_id']}&amp;post={$entry['post_id']}\">Delete</a></td>
		</tr>\n";
	}
	print "
	</table>\n";
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
	$keys = get_meta_keys();
?>
<h4>Add new custom data to this post:</h4>
<div id="postcustomkeys">
<p>Select existing key or enter new key</p>
<?php
if ($keys) {
?>
<select id="metakeyselect" name="metakeyselect">
<option value="#NONE#">- Select -</option>
<?php
	foreach($keys as $key) {
		echo "<option value='$key'>$key</option>\n";
	}
?>
</select>
<?php
} // if ($keys)
?>
<input type="text" id="metakeyinput" name="metakeyinput" />
</div>
<div id="postcustomvals">
<p>Custom Value</p>

<textarea id="metavalue" name="metavalue" rows="3" cols="25"></textarea>
</div>
<br style="clear: both;" />
<div id="postcustomsubmit">
<input type="submit" id="save" name="save" value="Add Custom">
</div>
<?php
}

function add_meta($post_ID) {
	global $wpdb, $tablepostmeta;
	
	$metakeyselect = trim($_POST['metakeyselect']);
	$metakeyinput = trim($_POST['metakeyinput']);
	$metavalue = trim($_POST['metavalue']);

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

function del_meta($mid) {
	global $wpdb, $tablepostmeta;
	
	$result = $wpdb->query("DELETE FROM $tablepostmeta WHERE meta_id = '$mid'");
}

?>