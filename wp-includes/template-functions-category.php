<?php

function get_the_category() {
    global $post, $tablecategories, $tablepost2cat, $wpdb, $category_cache;
    if ($category_cache[$post->ID]) {
        return $category_cache[$post->ID];
    } else {
        $categories = $wpdb->get_results("
            SELECT category_id, cat_name, category_nicename, category_description, category_parent
            FROM  $tablecategories, $tablepost2cat
            WHERE $tablepost2cat.category_id = cat_ID AND $tablepost2cat.post_id = $post->ID
            ");
    
        return $categories;
    }
}

function get_category_link($echo = false, $category_id, $category_nicename) {
    global $wpdb, $tablecategories, $post, $querystring_start, $querystring_equal, $siteurl, $blogfilename, $cache_categories;
    $cat_ID = $category_id;
    $permalink_structure = get_settings('permalink_structure');
    
    if ('' == $permalink_structure) {
        $file = "$siteurl/$blogfilename";
        $link = $file.$querystring_start.'cat'.$querystring_equal.$cat_ID;
    } else {
        if ('' == $category_nicename) $category_nicename = $cache_categories[$category_id]->category_nicename;
        // Get any static stuff from the front
        $front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
        $link = $siteurl . $front . 'category/';
        if ($parent=$cache_categories[$category_id]->category_parent) $link .= get_category_parents($parent, FALSE, '/', TRUE);
        $link .= $category_nicename . '/';
    }

    if ($echo) echo $link;
    return $link;
}

function get_category_rss_link($echo = false, $category_id, $category_nicename) {
       global $querystring_start, $querystring_equal, $siteurl;
       $cat_ID = $category_id;
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
               $file = "$siteurl/wp-rss2.php";
        $link = $file . $querystring_start . 'cat' . $querystring_equal . $category_id;
       } else {
        $link = get_category_link(0, $category_id, $category_nicename);
               $link = $link . "/feed/";
       }

       if ($echo) echo $link;
       return $link;
}

function the_category($seperator = '', $parents='') {
    $categories = get_the_category();
    if ('' == $seperator) {
        echo '<ul class="post-categories">';
        foreach ($categories as $category) {
            $category->cat_name = stripslashes($category->cat_name);
            echo "\n\t<li>";
            switch(strtolower($parents)) {
                case 'multiple':
                    if ($category->category_parent)    echo get_category_parents($category->category_parent, TRUE);
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a></li>";
                    break;
                case 'single':
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>";
                    if ($category->category_parent)echo get_category_parents($category->category_parent, FALSE);
                    echo "$category->cat_name</a></li>";
                    break;
                case '':
                default:
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a></li>";
            }
        }
        echo '</ul>';
    } else {
        $i = 0;
        foreach ($categories as $category) {
            $category->cat_name = stripslashes($category->cat_name);
            if (0 < $i) echo $seperator . ' ';
            switch(strtolower($parents)) {
                case 'multiple':
                    if ($category->category_parent)    echo get_category_parents($category->category_parent, TRUE);
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a>";
                case 'single':
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>";
                    if ($category->category_parent)echo get_category_parents($category->category_parent, FALSE);
                    echo "$category->cat_name</a>";
                case '':
                default:
                    echo "<a href='" . get_category_link(0, $category->category_id, $category->category_nicename) . "' title='View all posts in $category->cat_name'>$category->cat_name</a>";
            }
            ++$i;
        }
    }
}

function the_category_rss($type = 'rss') {
    $categories = get_the_category();
    foreach ($categories as $category) {
        $category->cat_name = stripslashes(convert_chars($category->cat_name));
        if ('rdf' == $type) {
            echo "\n<dc:subject>$category->cat_name</dc:subject>";
        } else {
            echo "\n<category>$category->cat_name</category>";
        }
    }

}
function the_category_unicode() {
    $category = get_the_category();
    $category = apply_filters('the_category_unicode', $category);
    echo convert_chars($category, 'unicode');
}

function get_the_category_by_ID($cat_ID) {
    global $tablecategories, $cache_categories, $use_cache, $wpdb;
    if ((!$cache_categories[$cat_ID]) OR (!$use_cache)) {
        $cat_name = $wpdb->get_var("SELECT cat_name FROM $tablecategories WHERE cat_ID = '$cat_ID'");
        $cache_categories[$cat_ID]->cat_name = $cat_name;
    } else {
        $cat_name = $cache_categories[$cat_ID]->cat_name;
    }
    return(stripslashes($cat_name));
}

function get_category_parents($id, $link=FALSE, $separator=' / ', $nicename=FALSE){
    global $tablecategories, $cache_categories;
    $chain = "";
    $parent = $cache_categories[$id];
    if ($nicename) {
        $name = $parent->category_nicename;
    } else {
        $name = $parent->cat_name;
    }
    if ($parent->category_parent) $chain .= get_category_parents($parent->category_parent, $link, $separator, $nicename);
    if ($link) {
        $chain .= "<a href='" . get_category_link(0, $parent->cat_ID, $parent->category_nicename) . "' title='View all posts in $parent->cat_name'>$name</a>" . $separator;
    } else {
        $chain .= $name.$separator;
    }
    return $chain;
}

function get_category_children($id, $before=' / ', $after='') {
    global $tablecategories, $cache_categories;
    $c_cache=$cache_categories; // Can't do recursive foreach on a global, have to make a copy
    $chain = "";
    foreach ($c_cache as $category){
        if ($category->category_parent == $id){
            $chain .= $before.$category->cat_ID.$after;
            $chain .= get_category_children($category->cat_ID, $before, $after);
        }
    }
    return $chain;
}

function the_category_ID($echo=true) {
    global $post;
    if ($echo)
        echo $post->post_category;
    else
        return $post->post_category;
}

function the_category_head($before='', $after='') {
    global $post, $currentcat, $previouscat, $dateformat, $newday;
    $currentcat = $post->post_category;
    if ($currentcat != $previouscat) {
        echo $before;
        echo get_the_category_by_ID($currentcat);
        echo $after;
        $previouscat = $currentcat;
    }
}

function category_description($category = 0) {
    global $cat, $wpdb, $tablecategories, $cache_categories;
    if (!$category) $category = $cat;
    $category_description = $cache_categories[$category]->category_description;
    $category_description = apply_filters('category_description', $category_description);
    return $category_description;
}

// out of the WordPress loop
function dropdown_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc',
        $optiondates = 0, $optioncount = 0, $hide_empty = 1, $optionnone=FALSE,
        $selected=0, $hide=0) {
    global $tablecategories, $tableposts, $tablepost2cat, $wpdb;
    global $pagenow, $siteurl, $blogfilename;
    global $querystring_start, $querystring_equal, $querystring_separator;
    if (($file == 'blah') || ($file == '')) $file = "$siteurl/$blogfilename";
    if (!$selected) $selected=$cat;
    $sort_column = 'cat_'.$sort_column;

    $query = "
        SELECT cat_ID, cat_name, category_nicename,category_parent,
        COUNT($tablepost2cat.post_id) AS cat_count,
        DAYOFMONTH(MAX(post_date)) AS lastday, MONTH(MAX(post_date)) AS lastmonth
        FROM $tablecategories LEFT JOIN $tablepost2cat ON (cat_ID = category_id)
        LEFT JOIN $tableposts ON (ID = post_id)
        WHERE cat_ID > 0
        ";
    if ($hide) {
        $query .= " AND cat_ID != $hide";
        $query .= get_category_children($hide, " AND cat_ID != ");
    }
    $query .=" GROUP BY cat_ID";
    if (intval($hide_empty) == 1) $query .= " HAVING cat_count > 0";
    $query .= " ORDER BY $sort_column $sort_order, post_date DESC";

    $categories = $wpdb->get_results($query);
    echo "<select name='cat' class='postform'>\n";
    if (intval($optionall) == 1) {
        $all = apply_filters('list_cats', $all);
        echo "\t<option value='all'>$all</option>\n";
    }
    if (intval($optionnone) == 1) echo "\t<option value='0'>None</option>\n";
    if ($categories) {
        foreach ($categories as $category) {
            $cat_name = apply_filters('list_cats', $category->cat_name);
            echo "\t<option value=\"".$category->cat_ID."\"";
            if ($category->cat_ID == $selected)
                echo ' selected="selected"';
            echo '>';
            echo stripslashes($cat_name);
            if (intval($optioncount) == 1) echo '&nbsp;&nbsp;('.$category->cat_count.')';
            if (intval($optiondates) == 1) echo '&nbsp;&nbsp;'.$category->lastday.'/'.$category->lastmonth;
            echo "</option>\n";
        }
    }
    echo "</select>\n";
}

// out of the WordPress loop
function wp_list_cats($args = '') {
	parse_str($args, $r);
	if (!$r['optionall']) $r['optionall'] = 1;
	if (!$r['all']) $r['all'] = 'All';
	if (!$r['sort_column']) $r['sort_column'] = 'ID';
	if (!$r['file']) $r['file'] = '';
	if (!$r['list']) $r['list'] = true;
	if (!$r['optiondates']) $r['optiondates'] = 0;
	if (!$r['hide_empty']) $r['hide_empty'] = 1;
	if (!$r['use_desc_for_title']) $r['use_desc_for_title'] = 1;
	if (!$r['children']) $r['children'] = true;
	if (!$r['child_of']) $r['child_of'] = 0;
	if (!$r['categories']) $r['categories'] = 0;
	if (!$r['recurse']) $r['recurse'] = 0;
	list_cats($r['optionall'], $r['all'], $r['sort_column'], $r['sort_order'], $r['file'],
	$r['list'], $r['optiondates'], $r['optioncount'], $r['hide_empty'], $r['use_desc_for_title'],
	$r['children'], $r['child_of'], $r['categories'], $r['recurse']);
}

function list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0) {
    global $tablecategories, $tableposts, $tablepost2cat, $wpdb;
    global $pagenow, $siteurl, $blogfilename;
    global $querystring_start, $querystring_equal, $querystring_separator;
    // Optiondates now works
    if ('' == $file) {
        $file = "$siteurl/$blogfilename";
    }
    if (intval($categories)==0){
        $sort_column = 'cat_'.$sort_column;

        $query  = "
            SELECT cat_ID, cat_name, category_nicename, category_description, category_parent
            FROM $tablecategories
            WHERE cat_ID > 0
            ORDER BY $sort_column $sort_order";

        $categories = $wpdb->get_results($query);
    }
    if (intval($hide_empty) == 1 || intval($optioncount) == 1) {
        $cat_counts = $wpdb->get_results("    SELECT cat_ID,
        COUNT($tablepost2cat.post_id) AS cat_count
        FROM $tablecategories LEFT JOIN $tablepost2cat ON (cat_ID = category_id)
        LEFT JOIN $tableposts ON (ID = post_id)
        GROUP BY category_id");
        foreach ($cat_counts as $cat_count) {
            $category_posts["$cat_count->cat_ID"] = $cat_count->cat_count;
        }
    }
    
    if (intval($optiondates) == 1) {
        $cat_dates = $wpdb->get_results("    SELECT cat_ID,
        DAYOFMONTH(MAX(post_date)) AS lastday, MONTH(MAX(post_date)) AS lastmonth
        FROM $tablecategories LEFT JOIN $tablepost2cat ON (cat_ID = category_id)
        LEFT JOIN $tableposts ON (ID = post_id)
        GROUP BY category_id");
        foreach ($cat_dates as $cat_date) {
            $category_lastday["$cat_date->cat_ID"] = $cat_date->lastday;
            $category_lastmonth["$cat_date->cat_ID"] = $cat_date->lastmonth;
        }
    }
    
    if (intval($optionall) == 1 && !$child_of && $categories) {
        $all = apply_filters('list_cats', $all);
        $link = "<a href=\"".$file.$querystring_start.'cat'.$querystring_equal.'all">'.$all."</a>";
        if ($list) {
            echo "\n\t<li>$link</li>";
        } else {
            echo "\t$link<br />\n";
        }
    }
    
    $num_found=0;
    $thelist = "";
    
    foreach ($categories as $category) {
        if ((intval($hide_empty) == 0 || $category_posts["$category->cat_ID"] > 0) && (!$children || $category->category_parent == $child_of)) {
            $num_found++;
            $link = '<a href="'.get_category_link(0, $category->cat_ID, $category->category_nicename).'" ';
            if ($use_desc_for_title == 0 || empty($category->category_description)) {
                $link .= 'title="View all posts filed under ' . htmlspecialchars($category->cat_name) . '"';
            } else {
                $link .= 'title="' . htmlspecialchars($category->category_description) . '"';
            }
            $link .= '>';
            $link .= stripslashes($category->cat_name).'</a>';
            if (intval($optioncount) == 1) {
                $link .= ' ('.intval($category_posts["$category->cat_ID"]).')';
            }
            if (intval($optiondates) == 1) {
                $link .= ' '.$category_lastday["$category->cat_ID"].'/'.$category_lastmonth["$category->cat_ID"];
            }
            if ($list) {
                $thelist .= "\t<li>$link\n";
            } else {
                $thelist .= "\t$link<br />\n";
            }
            if ($children) $thelist .= list_cats($optionall, $all, $sort_column, $sort_order, $file, $list, $optiondates, $optioncount, $hide_empty, $use_desc_for_title, $children, $category->cat_ID, $categories, 1);
            if ($list) $thelist .= "</li>\n";
            }
    }
    if (!$num_found && !$child_of){
        if ($list) {
            $before = '<li>';
            $after = '</li>';
        }
        echo $before . "No categories" . $after . "\n";
        return;
    }
    if ($list && $child_of && $num_found && $recurse) {
        $pre = "\t\t<ul class='children'>";
        $post = "\t\t</ul>\n";
    }
    $thelist=$pre.$thelist.$post;
    if ($recurse) {
        return $thelist;
    }
    echo $thelist;
}
?>
