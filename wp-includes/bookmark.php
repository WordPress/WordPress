<?php

function get_bookmark($bookmark_id, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	$bookmark_id = (int) $bookmark_id;
	$link = $wpdb->get_row("SELECT * FROM $wpdb->links WHERE link_id = '$bookmark_id' LIMIT 1");
	$link->link_category = array_unique( wp_get_object_terms($link_id, 'link_category', 'fields=ids') );

	$link = sanitize_bookmark($link, $filter);

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

function get_bookmark_field( $field, $bookmark, $context = 'display' ) {
	$bookmark = (int) $bookmark;
	$bookmark = get_bookmark( $bookmark );

	if ( is_wp_error($bookmark) )
		return $bookmark;

	if ( !is_object($bookmark) )
		return '';

	if ( !isset($bookmark->$field) )
		return '';

	return sanitize_bookmark_field($field, $bookmark->$field, $bookmark->link_id, $context);
}

// Deprecate
function get_link($bookmark_id, $output = OBJECT) {
	return get_bookmark($bookmark_id, $output);
}

function get_bookmarks($args = '') {
	global $wpdb;

	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'include' => '',
		'exclude' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_bookmarks', 'bookmark' ) )
		if ( isset( $cache[ $key ] ) )
			return apply_filters('get_bookmarks', $cache[ $key ], $r );

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
		if ( $category = get_term_by('name', $category_name, 'link_category') )
			$category = $category->term_id;
	}

	$category_query = '';
	$join = '';
	if ( !empty($category) ) {
		$incategories = preg_split('/[\s,]+/',$category);
		if ( count($incategories) ) {
			foreach ( $incategories as $incat ) {
				if (empty($category_query))
					$category_query = ' AND ( tt.term_id = ' . intval($incat) . ' ';
				else
					$category_query .= ' OR tt.term_id = ' . intval($incat) . ' ';
			}
		}
	}
	if (!empty($category_query)) {
		$category_query .= ") AND taxonomy = 'link_category'";
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON ($wpdb->links.link_id = tr.object_id) INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
	}

	if (get_option('links_recently_updated_time')) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
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

	$cache[ $key ] = $results;
	wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );

	return apply_filters('get_bookmarks', $results, $r);
}

function sanitize_bookmark($bookmark, $context = 'display') {
	$fields = array('link_id', 'link_url', 'link_name', 'link_image', 'link_target', 'link_category',
		'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_updated',
		'link_rel', 'link_notes', 'link_rss', );

	$do_object = false;
	if ( is_object($bookmark) )
		$do_object = true;

	foreach ( $fields as $field ) {
		if ( $do_object )
			$bookmark->$field = sanitize_bookmark_field($field, $bookmark->$field, $bookmark->link_id, $context);
		else
			$bookmark[$field] = sanitize_bookmark_field($field, $bookmark[$field], $bookmark['link_id'], $context);
	}

	return $bookmark;
}

function sanitize_bookmark_field($field, $value, $bookmark_id, $context) {
	$int_fields = array('link_id', 'link_rating');
	if ( in_array($field, $int_fields) )
		$value = (int) $value;

	$yesno = array('link_visible');
	if ( in_array($field, $yesno) )
		$value = preg_replace('/[^YNyn]/', '', $value);

	if ( 'link_target' == $field ) {
		$targets = array('_top', '_blank');
		if ( ! in_array($value, $targets) )
			$value = '';
	}

	if ( 'raw' == $context )
		return $value;

	if ( 'edit' == $context ) {
		$format_to_edit = array('link_notes');
		$value = apply_filters("edit_$field", $value, $bookmark_id);

		if ( in_array($field, $format_to_edit) ) {
			$value = format_to_edit($value);
		} else {
			$value = attribute_escape($value);
		}
	} else if ( 'db' == $context ) {
		$value = apply_filters("pre_$field", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters($field, $value, $bookmark_id, $context);
	}

	if ( 'attribute' == $context )
		$value = attribute_escape($value);
	else if ( 'js' == $context )
		$value = js_escape($value);

	return $value;
}

function delete_get_bookmark_cache() {
	wp_cache_delete( 'get_bookmarks', 'bookmark' );
}
add_action( 'add_link', 'delete_get_bookmark_cache' );
add_action( 'edit_link', 'delete_get_bookmark_cache' );
add_action( 'delete_link', 'delete_get_bookmark_cache' );

?>
