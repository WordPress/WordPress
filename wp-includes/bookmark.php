<?php
/**
 * Link/Bookmark API
 *
 * @package WordPress
 * @subpackage Bookmark
 */

/**
 * Retrieve Bookmark data based on ID
 *
 * @since 2.1.0
 * @uses $wpdb Database Object
 *
 * @param int $bookmark_id
 * @param string $output Optional. Either OBJECT, ARRAY_N, or ARRAY_A constant
 * @param string $filter Optional, default is 'raw'.
 * @return array|object Type returned depends on $output value.
 */
function get_bookmark($bookmark, $output = OBJECT, $filter = 'raw') {
	global $wpdb;

	if ( empty($bookmark) ) {
		if ( isset($GLOBALS['link']) )
			$_bookmark = & $GLOBALS['link'];
		else
			$_bookmark = null;
	} elseif ( is_object($bookmark) ) {
		wp_cache_add($bookmark->link_id, $bookmark, 'bookmark');
		$_bookmark = $bookmark;
	} else {
		if ( isset($GLOBALS['link']) && ($GLOBALS['link']->link_id == $bookmark) ) {
			$_bookmark = & $GLOBALS['link'];
		} elseif ( ! $_bookmark = wp_cache_get($bookmark, 'bookmark') ) {
			$_bookmark = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->links WHERE link_id = %d LIMIT 1", $bookmark));
			$_bookmark->link_category = array_unique( wp_get_object_terms($_bookmark->link_id, 'link_category', array('fields' => 'ids')) );
			wp_cache_add($_bookmark->link_id, $_bookmark, 'bookmark');
		}
	}

	$_bookmark = sanitize_bookmark($_bookmark, $filter);

	if ( $output == OBJECT ) {
		return $_bookmark;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($_bookmark);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($_bookmark));
	} else {
		return $_bookmark;
	}
}

/**
 * Retrieve single bookmark data item or field.
 *
 * @since 2.3.0
 * @uses get_bookmark() Gets bookmark object using $bookmark as ID
 * @uses sanitize_bookmark_field() Sanitizes Bookmark field based on $context.
 *
 * @param string $field The name of the data field to return
 * @param int $bookmark The bookmark ID to get field
 * @param string $context Optional. The context of how the field will be used.
 * @return string
 */
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

/**
 * Retrieves the list of bookmarks
 *
 * Attempts to retrieve from the cache first based on MD5 hash of arguments. If
 * that fails, then the query will be built from the arguments and executed. The
 * results will be stored to the cache.
 *
 * List of default arguments are as follows:
 * 'orderby' - Default is 'name' (string). How to order the links by. String is
 *		based off of the bookmark scheme.
 * 'order' - Default is 'ASC' (string). Either 'ASC' or 'DESC'. Orders in either
 *		ascending or descending order.
 * 'limit' - Default is -1 (integer) or show all. The amount of bookmarks to
 *		display.
 * 'category' - Default is empty string (string). Include the links in what
 *		category ID(s).
 * 'category_name' - Default is empty string (string). Get links by category
 *		name.
 * 'hide_invisible' - Default is 1 (integer). Whether to show (default) or hide
 *		links marked as 'invisible'.
 * 'show_updated' - Default is 0 (integer). Will show the time of when the
 *		bookmark was last updated.
 * 'include' - Default is empty string (string). Include other categories
 *		separated by commas.
 * 'exclude' - Default is empty string (string). Exclude other categories
 *		separated by commas.
 *
 * @since 2.1.0
 * @uses $wpdb Database Object
 * @link http://codex.wordpress.org/Template_Tags/get_bookmarks
 *
 * @param string|array $args List of arguments to overwrite the defaults
 * @return array List of bookmark row objects
 */
function get_bookmarks($args = '') {
	global $wpdb;

	$defaults = array(
		'orderby' => 'name', 'order' => 'ASC',
		'limit' => -1, 'category' => '',
		'category_name' => '', 'hide_invisible' => 1,
		'show_updated' => 0, 'include' => '',
		'exclude' => '', 'search' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$cache = array();
	$key = md5( serialize( $r ) );
	if ( $cache = wp_cache_get( 'get_bookmarks', 'bookmark' ) ) {
		if ( is_array($cache) && isset( $cache[ $key ] ) )
			return apply_filters('get_bookmarks', $cache[ $key ], $r );
	}

	if ( !is_array($cache) )
		$cache = array();

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

	if ( !empty($category_name) ) {
		if ( $category = get_term_by('name', $category_name, 'link_category') ) {
			$category = $category->term_id;
		} else {
			$cache[ $key ] = array();
			wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );
			return apply_filters( 'get_bookmarks', array(), $r );
		}
	}

	if ( ! empty($search) ) {
		$search = like_escape($search);
		$search = " AND ( (link_url LIKE '%$search%') OR (link_name LIKE '%$search%') OR (link_description LIKE '%$search%') ) ";
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

	if ( $show_updated && get_option('links_recently_updated_time') ) {
		$recently_updated_test = ", IF (DATE_ADD(link_updated, INTERVAL " . get_option('links_recently_updated_time') . " MINUTE) >= NOW(), 1,0) as recently_updated ";
	} else {
		$recently_updated_test = '';
	}

	$get_updated = ( $show_updated ) ? ', UNIX_TIMESTAMP(link_updated) AS link_updated_f ' : '';

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
			$orderparams = array();
			foreach ( explode(',', $orderby) as $ordparam )
				$orderparams[] = 'link_' . trim($ordparam);
			$orderby = implode(',', $orderparams);
	}

	if ( 'link_id' == $orderby )
		$orderby = "$wpdb->links.link_id";

	$visible = '';
	if ( $hide_invisible )
		$visible = "AND link_visible = 'Y'";

	$query = "SELECT * $length $recently_updated_test $get_updated FROM $wpdb->links $join WHERE 1=1 $visible $category_query";
	$query .= " $exclusions $inclusions $search";
	$query .= " ORDER BY $orderby $order";
	if ($limit != -1)
		$query .= " LIMIT $limit";

	$results = $wpdb->get_results($query);

	$cache[ $key ] = $results;
	wp_cache_set( 'get_bookmarks', $cache, 'bookmark' );

	return apply_filters('get_bookmarks', $results, $r);
}

/**
 * Sanitizes all bookmark fields
 *
 * @since 2.3.0
 *
 * @param object|array $bookmark Bookmark row
 * @param string $context Optional, default is 'display'. How to filter the
 *		fields
 * @return object|array Same type as $bookmark but with fields sanitized.
 */
function sanitize_bookmark($bookmark, $context = 'display') {
	$fields = array('link_id', 'link_url', 'link_name', 'link_image', 'link_target', 'link_category',
		'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_updated',
		'link_rel', 'link_notes', 'link_rss', );

	if ( is_object($bookmark) ) {
		$do_object = true;
		$link_id = $bookmark->link_id;
	} else {
		$do_object = false;
		$link_id = $bookmark['link_id'];
	}

	foreach ( $fields as $field ) {
		if ( $do_object ) {
			if ( isset($bookmark->$field) )
				$bookmark->$field = sanitize_bookmark_field($field, $bookmark->$field, $link_id, $context);
		} else {
			if ( isset($bookmark[$field]) )
				$bookmark[$field] = sanitize_bookmark_field($field, $bookmark[$field], $link_id, $context);
		}
	}

	return $bookmark;
}

/**
 * Sanitizes a bookmark field
 *
 * Sanitizes the bookmark fields based on what the field name is. If the field
 * has a strict value set, then it will be tested for that, else a more generic
 * filtering is applied. After the more strict filter is applied, if the
 * $context is 'raw' then the value is immediately return.
 *
 * Hooks exist for the more generic cases. With the 'edit' context, the
 * 'edit_$field' filter will be called and passed the $value and $bookmark_id
 * respectively. With the 'db' context, the 'pre_$field' filter is called and
 * passed the value. The 'display' context is the final context and has the
 * $field has the filter name and is passed the $value, $bookmark_id, and
 * $context respectively.
 *
 * @since 2.3.0
 *
 * @param string $field The bookmark field
 * @param mixed $value The bookmark field value
 * @param int $bookmark_id Bookmark ID
 * @param string $context How to filter the field value. Either 'raw', 'edit',
 *		'attribute', 'js', 'db', or 'display'
 * @return mixed The filtered value
 */
function sanitize_bookmark_field($field, $value, $bookmark_id, $context) {
	switch ( $field ) {
	case 'link_id' : // ints
	case 'link_rating' :
		$value = (int) $value;
		break;
	case 'link_category' : // array( ints )
		$value = array_map('absint', (array) $value);
		// We return here so that the categories aren't filtered.
		// The 'link_category' filter is for the name of a link category, not an array of a link's link categories
		return $value;
		break;
	case 'link_visible' : // bool stored as Y|N
		$value = preg_replace('/[^YNyn]/', '', $value);
		break;
	case 'link_target' : // "enum"
		$targets = array('_top', '_blank');
		if ( ! in_array($value, $targets) )
			$value = '';
		break;
	}

	if ( 'raw' == $context )
		return $value;

	if ( 'edit' == $context ) {
		$format_to_edit = array('link_notes');
		$value = apply_filters("edit_$field", $value, $bookmark_id);

		if ( in_array($field, $format_to_edit) ) {
			$value = format_to_edit($value);
		} else {
			$value = esc_attr($value);
		}
	} else if ( 'db' == $context ) {
		$value = apply_filters("pre_$field", $value);
	} else {
		// Use display filters by default.
		$value = apply_filters($field, $value, $bookmark_id, $context);

		if ( 'attribute' == $context )
			$value = esc_attr($value);
		else if ( 'js' == $context )
			$value = esc_js($value);
	}

	return $value;
}

/**
 * Deletes bookmark cache
 *
 * @since 2.7.0
 * @uses wp_cache_delete() Deletes the contents of 'get_bookmarks'
 */
function clean_bookmark_cache($bookmark_id) {
	wp_cache_delete( $bookmark_id, 'bookmark' );
	wp_cache_delete( 'get_bookmarks', 'bookmark' );
}

?>
