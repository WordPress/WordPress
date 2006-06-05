<?php

function get_bookmark($bookmark_id, $output = OBJECT) {
	global $wpdb;

	$link = $wpdb->get_row("SELECT * FROM $wpdb->links WHERE link_id = '$bookmark_id'");
	$link->link_category = wp_get_link_cats($bookmark_id);

	if ( $output == OBJECT ) {
		return $link;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($link);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($link));
	} else {
		return $link;
	}
}

// Deprecate
function get_link($bookmark_id, $output = OBJECT) {
	return get_bookmark($bookmark_id, $output);	
}

function get_bookmarks($args = '') {
	global $wpdb;

	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('orderby' => 'name', 'order' => 'ASC', 'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1, 'show_updated' => 0, 'include' => '', 'exclude' => '');
	$r = array_merge($defaults, $r);
	extract($r);

	$inclusions = '';
	if ( !empty($include) ) {
	$exclude = '';  //ignore exclude, category, and category_name params if using include
	$category = '';
	$category_name = '';
		$inclinks = preg_split('/[\s,]+/',$include);
		if ( count($inclinks) ) {
			foreach ( $inclinks as $inclink ) {
				if (empty($inclusions))
					$inclusions = ' AND ( link_id = ' . intval($inclink) . ' ';
				else
					$inclusions .= ' OR link_id = ' . intval($inclink) . ' ';
			}
		}
	}
	if (!empty($inclusions)) 
		$inclusions .= ')';

	$exclusions = '';
	if ( !empty($exclude) ) {
		$exlinks = preg_split('/[\s,]+/',$exclude);
		if ( count($exlinks) ) {
			foreach ( $exlinks as $exlink ) {
				if (empty($exclusions))
					$exclusions = ' AND ( link_id <> ' . intval($exlink) . ' ';
				else
					$exclusions .= ' AND link_id <> ' . intval($exlink) . ' ';
			}
		}
	}
	if (!empty($exclusions)) 
		$exclusions .= ')';
		
	if ( ! empty($category_name) ) {
		if ( $cat_id = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE cat_name='$category_name' LIMIT 1") )
			$category = $cat_id;
	}

	$category_query = '';
	$join = '';
	if ( !empty($category) ) {
		$incategories = preg_split('/[\s,]+/',$category);
		if ( count($incategories) ) {
			foreach ( $incategories as $incat ) {
				if (empty($category_query))
					$category_query = ' AND ( category_id = ' . intval($incat) . ' ';
				else
					$category_query .= ' OR category_id = ' . intval($incat) . ' ';
			}
		}
	}
	if (!empty($category_query)) {
		$category_query .= ')';	
		$join = " LEFT JOIN $wpdb->link2cat ON ($wpdb->links.link_id = $wpdb->link2cat.link_id) ";
	}

	if (get_settings('links_recently_updated_time')) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL " . get_settings('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
	} else {
		$recently_updated_test = '';
	}

	if ($show_updated) {
		$get_updated = ", UNIX_TIMESTAMP(link_updated) AS link_updated_f ";
	}

	$orderby = strtolower($orderby);
	$length = '';
	switch ($orderby) {
		case 'length':
			$length = ", CHAR_LENGTH(link_name) AS length";
			break;
		case 'rand':
			$orderby = 'rand()';
			break;
		default:
			$orderby = "link_" . $orderby;
	}

	if ( 'link_id' == $orderby )
		$orderby = "$wpdb->links.link_id";

	$visible = '';
	if ( $hide_invisible )
		$visible = "AND link_visible = 'Y'";

	$query = "SELECT * $length $recently_updated_test $get_updated FROM $wpdb->links $join WHERE 1=1 $visible $category_query";
	$query .= " $exclusions $inclusions";
	$query .= " ORDER BY $orderby $order";
	if ($limit != -1)
		$query .= " LIMIT $limit";

	$results = $wpdb->get_results($query);
	return apply_filters('get_bookmarks', $results, $r);
}

?>