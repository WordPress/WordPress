<?php


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

?>