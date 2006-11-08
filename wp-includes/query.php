<?php

/*
 * The Big Query.
 */
 
function get_query_var($var) {
	global $wp_query;

	return $wp_query->get($var);
}

function &query_posts($query) {
	global $wp_query;
	return $wp_query->query($query);
}

/*
 * Query type checks.
 */

function is_admin () {
	global $wp_query;

	return ( $wp_query->is_admin || strstr($_SERVER['REQUEST_URI'], 'wp-admin/') );
}

function is_archive () {
	global $wp_query;

	return $wp_query->is_archive;
}

function is_attachment () {
	global $wp_query;

	return $wp_query->is_attachment;
}

function is_author ($author = '') {
	global $wp_query;

	if ( !$wp_query->is_author )
		return false;

	if ( empty($author) )
		return true;

	$author_obj = $wp_query->get_queried_object();

	if ( $author == $author_obj->ID )
		return true;
	elseif ( $author == $author_obj->nickname )
		return true;
	elseif ( $author == $author_obj->user_nicename )
		return true;

	return false;
}

function is_category ($category = '') {
	global $wp_query;

	if ( !$wp_query->is_category )
		return false;

	if ( empty($category) )
		return true;

	$cat_obj = $wp_query->get_queried_object();

	if ( $category == $cat_obj->cat_ID )
		return true;
	else if ( $category == $cat_obj->cat_name )
		return true;
	elseif ( $category == $cat_obj->category_nicename )
		return true;

	return false;
}

function is_comments_popup () {
	global $wp_query;

	return $wp_query->is_comments_popup;
}

function is_date () {
	global $wp_query;

	return $wp_query->is_date;
}

function is_day () {
	global $wp_query;

	return $wp_query->is_day;
}

function is_feed () {
	global $wp_query;

	return $wp_query->is_feed;
}

function is_home () {
	global $wp_query;

	return $wp_query->is_home;
}

function is_month () {
	global $wp_query;

	return $wp_query->is_month;
}

function is_page ($page = '') {
	global $wp_query;

	if ( !$wp_query->is_page )
		return false;

	if ( empty($page) )
		return true;

	$page_obj = $wp_query->get_queried_object();

	if ( $page == $page_obj->ID )
		return true;
	elseif ( $page == $page_obj->post_title )
		return true;
	else if ( $page == $page_obj->post_name )
		return true;

	return false;
}

function is_paged () {
	global $wp_query;

	return $wp_query->is_paged;
}

function is_plugin_page() {
	global $plugin_page;

	if ( isset($plugin_page) )
		return true;

	return false;
}

function is_preview() {
	global $wp_query;

	return $wp_query->is_preview;
}

function is_robots() {
	global $wp_query;

	return $wp_query->is_robots;
}

function is_search () {
	global $wp_query;

	return $wp_query->is_search;
}

function is_single ($post = '') {
	global $wp_query;

	if ( !$wp_query->is_single )
		return false;

	if ( empty( $post) )
		return true;

	$post_obj = $wp_query->get_queried_object();

	if ( $post == $post_obj->ID )
		return true;
	elseif ( $post == $post_obj->post_title )
		return true;
	elseif ( $post == $post_obj->post_name )
		return true;

	return false;
}

function is_singular() {
	global $wp_query;

	return $wp_query->is_singular;	
}

function is_time () {
	global $wp_query;

	return $wp_query->is_time;
}

function is_trackback () {
	global $wp_query;

	return $wp_query->is_trackback;
}

function is_year () {
	global $wp_query;

	return $wp_query->is_year;
}

function is_404 () {
	global $wp_query;

	return $wp_query->is_404;
}

/*
 * The Loop.  Post loop control.
 */
 
function have_posts() {
	global $wp_query;

	return $wp_query->have_posts();
}

function in_the_loop() {
	global $wp_query;

	return $wp_query->in_the_loop;
}

function rewind_posts() {
	global $wp_query;

	return $wp_query->rewind_posts();
}

function the_post() {
	global $wp_query;

	$wp_query->the_post();
}

/*
 * WP_Query
 */

class WP_Query {
	var $query;
	var $query_vars = array();
	var $queried_object;
	var $queried_object_id;
	var $request;

	var $posts;
	var $post_count = 0;
	var $current_post = -1;
	var $in_the_loop = false;
	var $post;

	var $is_single = false;
	var $is_preview = false;
	var $is_page = false;
	var $is_archive = false;
	var $is_date = false;
	var $is_year = false;
	var $is_month = false;
	var $is_day = false;
	var $is_time = false;
	var $is_author = false;
	var $is_category = false;
	var $is_search = false;
	var $is_feed = false;
	var $is_trackback = false;
	var $is_home = false;
	var $is_404 = false;
	var $is_comments_popup = false;
	var $is_admin = false;
	var $is_attachment = false;
	var $is_singular = false;
	var $is_robots = false;
	var $is_posts_page = false;

	function init_query_flags() {
		$this->is_single = false;
		$this->is_page = false;
		$this->is_archive = false;
		$this->is_date = false;
		$this->is_year = false;
		$this->is_month = false;
		$this->is_day = false;
		$this->is_time = false;
		$this->is_author = false;
		$this->is_category = false;
		$this->is_search = false;
		$this->is_feed = false;
		$this->is_trackback = false;
		$this->is_home = false;
		$this->is_404 = false;
		$this->is_paged = false;
		$this->is_admin = false;
		$this->is_attachment = false;
		$this->is_singular = false;
		$this->is_robots = false;
		$this->is_posts_page = false;
	}

	function init () {
		unset($this->posts);
		unset($this->query);
		$this->query_vars = array();
		unset($this->queried_object);
		unset($this->queried_object_id);
		$this->post_count = 0;
		$this->current_post = -1;
		$this->in_the_loop = false;

		$this->init_query_flags();
	}

	// Reparse the query vars.
	function parse_query_vars() {
		$this->parse_query('');
	}
	
	function fill_query_vars($array) {
		$keys = array(
			'error'
			, 'm'
			, 'p'
			, 'subpost'
			, 'subpost_id'
			, 'attachment'
			, 'attachment_id'
			, 'name'
			, 'hour'
			, 'static'
			, 'pagename'
			, 'page_id'
			, 'second'
			, 'minute'
			, 'hour'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 'category_name'
			, 'author_name'
			, 'feed'
			, 'tb'
			, 'paged'
			, 'comments_popup'
			, 'preview'
		);

		foreach ($keys as $key) {
			if ( !isset($array[$key]))
				$array[$key] = '';
		}
		
		return $array;
	}

	// Parse a query string and set query type booleans.
	function parse_query ($query) {
		if ( !empty($query) || !isset($this->query) ) {
			$this->init();
			if ( is_array($query) )
				$qv = & $query;
			else
				parse_str($query, $qv);
			$this->query = $query;
			$this->query_vars = $qv;
		}
		
		$qv = $this->fill_query_vars($qv);
		
		if ( ! empty($qv['robots']) ) {
			$this->is_robots = true;
			return;
		}

		if ('404' == $qv['error']) {
			$this->is_404 = true;
			if ( !empty($query) ) {
				do_action_ref_array('parse_query', array(&$this));
			}
			return;
		}

		$qv['m'] =  (int) $qv['m'];
		$qv['p'] =  (int) $qv['p'];

		// Compat.  Map subpost to attachment.
		if ( '' != $qv['subpost'] )
			$qv['attachment'] = $qv['subpost'];
		if ( '' != $qv['subpost_id'] )
			$qv['attachment_id'] = $qv['subpost_id'];

		if ( ('' != $qv['attachment']) || (int) $qv['attachment_id'] ) {
			$this->is_single = true;
			$this->is_attachment = true;
		} elseif ('' != $qv['name']) {
			$this->is_single = true;
		} elseif ( $qv['p'] ) {
			$this->is_single = true;
		} elseif (('' != $qv['hour']) && ('' != $qv['minute']) &&('' != $qv['second']) && ('' != $qv['year']) && ('' != $qv['monthnum']) && ('' != $qv['day'])) {
			// If year, month, day, hour, minute, and second are set, a single 
			// post is being queried.        
			$this->is_single = true;
		} elseif ('' != $qv['static'] || '' != $qv['pagename'] || (int) $qv['page_id']) {
			$this->is_page = true;
			$this->is_single = false;
		} elseif (!empty($qv['s'])) {
			$this->is_search = true;
		} else {
		// Look for archive queries.  Dates, categories, authors.

			if ( (int) $qv['second']) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( (int) $qv['minute']) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( (int) $qv['hour']) {
				$this->is_time = true;
				$this->is_date = true;
			}

			if ( (int) $qv['day']) {
				if (! $this->is_date) {
					$this->is_day = true;
					$this->is_date = true;
				}
			}

			if ( (int)  $qv['monthnum']) {
				if (! $this->is_date) {
					$this->is_month = true;
					$this->is_date = true;
				}
			}

			if ( (int)  $qv['year']) {
				if (! $this->is_date) {
					$this->is_year = true;
					$this->is_date = true;
				}
			}

			if ( (int)  $qv['m']) {
				$this->is_date = true;
				if (strlen($qv['m']) > 9) {
					$this->is_time = true;
				} else if (strlen($qv['m']) > 7) {
					$this->is_day = true;
				} else if (strlen($qv['m']) > 5) {
					$this->is_month = true;
				} else {
					$this->is_year = true;
				}
			}

			if ('' != $qv['w']) {
				$this->is_date = true;
			}

			if (empty($qv['cat']) || ($qv['cat'] == '0')) {
				$this->is_category = false;
			} else {
				if (stristr($qv['cat'],'-')) {
					$this->is_category = false;
				} else {
					$this->is_category = true;
				}
			}

			if ('' != $qv['category_name']) {
				$this->is_category = true;
			}
            
			if ((empty($qv['author'])) || ($qv['author'] == '0')) {
				$this->is_author = false;
			} else {
				$this->is_author = true;
			}

			if ('' != $qv['author_name']) {
				$this->is_author = true;
			}

			if ( ($this->is_date || $this->is_author || $this->is_category)) {
				$this->is_archive = true;
			}
		}

		if ('' != $qv['feed']) {
			$this->is_feed = true;
		}

		if ('' != $qv['tb']) {
			$this->is_trackback = true;
		}

		if ('' != $qv['paged']) {
			$this->is_paged = true;
		}

		if ('' != $qv['comments_popup']) {
			$this->is_comments_popup = true;
		}

		//if we're previewing inside the write screen
		if ('' != $qv['preview']) {
			$this->is_preview = true;
		}

		if (strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
			$this->is_admin = true;
		}

		if ( $this->is_single || $this->is_page || $this->is_attachment )
			$this->is_singular = true;

		if ( ! ($this->is_singular || $this->is_archive || $this->is_search || $this->is_feed || $this->is_trackback || $this->is_404 || $this->is_admin || $this->is_comments_popup)) {
			$this->is_home = true;
		}

		if ( !empty($query) ) {
			do_action_ref_array('parse_query', array(&$this));
		}
	}

	function set_404() {
		$is_feed = $this->is_feed;

		$this->init_query_flags();
		$this->is_404 = true;

		$this->is_feed = $is_feed;
	}

	function get($query_var) {
		if (isset($this->query_vars[$query_var])) {
			return $this->query_vars[$query_var];
		}

		return '';
	}

	function set($query_var, $value) {
		$this->query_vars[$query_var] = $value;
	}

	function &get_posts() {
		global $wpdb, $pagenow, $user_ID;

		do_action_ref_array('pre_get_posts', array(&$this));

		// Shorthand.
		$q = &$this->query_vars;
		
		$q = $this->fill_query_vars($q);

		// First let's clear some variables
		$distinct = '';
		$whichcat = '';
		$whichauthor = '';
		$whichpage = '';
		$result = '';
		$where = '';
		$limits = '';
		$join = '';
		$search = '';
		$groupby = '';

		if ( !isset($q['post_type']) )
			$q['post_type'] = 'post';
		$post_type = $q['post_type'];
		if ( !isset($q['posts_per_page']) || $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = get_option('posts_per_page');
		if ( !isset($q['what_to_show']) )
			$q['what_to_show'] = get_option('what_to_show');
		if ( isset($q['showposts']) && $q['showposts'] ) {
			$q['showposts'] = (int) $q['showposts'];
			$q['posts_per_page'] = $q['showposts'];
		}
		if ( (isset($q['posts_per_archive_page']) && $q['posts_per_archive_page'] != 0) && ($this->is_archive || $this->is_search) )
			$q['posts_per_page'] = $q['posts_per_archive_page'];
		if ( !isset($q['nopaging']) ) {
			if ($q['posts_per_page'] == -1) {
				$q['nopaging'] = true;
			} else {
				$q['nopaging'] = false;
			}
		}
		if ( $this->is_feed ) {
			$q['posts_per_page'] = get_option('posts_per_rss');
			$q['what_to_show'] = 'posts';
		}
		$q['posts_per_page'] = (int) $q['posts_per_page'];
		if ( $q['posts_per_page'] < -1 )
			$q['posts_per_page'] = abs($q['posts_per_page']);
		else if ( $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = 1;

		if ( $this->is_home && (empty($this->query) || $q['preview'] == 'true') && ( 'page' == get_option('show_on_front') ) && get_option('page_on_front') ) {
			$this->is_page = true;
			$this->is_home = false;
			$q['page_id'] = get_option('page_on_front');
		}

		if (isset($q['page'])) {
			$q['page'] = trim($q['page'], '/');
			$q['page'] = (int) $q['page'];
			$q['page'] = abs($q['page']);
		}

		$add_hours = intval(get_option('gmt_offset'));
		$add_minutes = intval(60 * (get_option('gmt_offset') - $add_hours));
		$wp_posts_post_date_field = "post_date"; // "DATE_ADD(post_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)";

		// If a month is specified in the querystring, load that month
		if ( (int) $q['m'] ) {
			$q['m'] = '' . preg_replace('|[^0-9]|', '', $q['m']);
			$where .= ' AND YEAR(post_date)=' . substr($q['m'], 0, 4);
			if (strlen($q['m'])>5)
				$where .= ' AND MONTH(post_date)=' . substr($q['m'], 4, 2);
			if (strlen($q['m'])>7)
				$where .= ' AND DAYOFMONTH(post_date)=' . substr($q['m'], 6, 2);
			if (strlen($q['m'])>9)
				$where .= ' AND HOUR(post_date)=' . substr($q['m'], 8, 2);
			if (strlen($q['m'])>11)
				$where .= ' AND MINUTE(post_date)=' . substr($q['m'], 10, 2);
			if (strlen($q['m'])>13)
				$where .= ' AND SECOND(post_date)=' . substr($q['m'], 12, 2);
		}

		if ( (int) $q['hour'] ) {
			$q['hour'] = '' . intval($q['hour']);
			$where .= " AND HOUR(post_date)='" . $q['hour'] . "'";
		}

		if ( (int) $q['minute'] ) {
			$q['minute'] = '' . intval($q['minute']);
			$where .= " AND MINUTE(post_date)='" . $q['minute'] . "'";
		}

		if ( (int) $q['second'] ) {
			$q['second'] = '' . intval($q['second']);
			$where .= " AND SECOND(post_date)='" . $q['second'] . "'";
		}

		if ( (int) $q['year'] ) {
			$q['year'] = '' . intval($q['year']);
			$where .= " AND YEAR(post_date)='" . $q['year'] . "'";
		}

		if ( (int) $q['monthnum'] ) {
			$q['monthnum'] = '' . intval($q['monthnum']);
			$where .= " AND MONTH(post_date)='" . $q['monthnum'] . "'";
		}

		if ( (int) $q['day'] ) {
			$q['day'] = '' . intval($q['day']);
			$where .= " AND DAYOFMONTH(post_date)='" . $q['day'] . "'";
		}

		// Compat.  Map subpost to attachment.
		if ( '' != $q['subpost'] )
			$q['attachment'] = $q['subpost'];
		if ( '' != $q['subpost_id'] )
			$q['attachment_id'] = $q['subpost_id'];

		if ('' != $q['name']) {
			$q['name'] = sanitize_title($q['name']);
			$where .= " AND post_name = '" . $q['name'] . "'";
		} else if ('' != $q['pagename']) {
			$reqpage = get_page_by_path($q['pagename']);
			if ( !empty($reqpage) )
				$reqpage = $reqpage->ID;
			else
				$reqpage = 0;

			if  ( ('page' == get_option('show_on_front') ) && ( $reqpage == get_option('page_for_posts') ) ) {
				$this->is_page = false;
				$this->is_home = true;
				$this->is_posts_page = true;
			} else {
				$q['pagename'] = str_replace('%2F', '/', urlencode(urldecode($q['pagename'])));
				$page_paths = '/' . trim($q['pagename'], '/');
				$q['pagename'] = sanitize_title(basename($page_paths));
				$q['name'] = $q['pagename'];
				$where .= " AND (ID = '$reqpage')";
			}
		} elseif ('' != $q['attachment']) {
			$q['attachment'] = str_replace('%2F', '/', urlencode(urldecode($q['attachment'])));
			$attach_paths = '/' . trim($q['attachment'], '/');
			$q['attachment'] = sanitize_title(basename($attach_paths));
			$q['name'] = $q['attachment'];
			$where .= " AND post_name = '" . $q['attachment'] . "'";
		}

		if ( (int) $q['w'] ) {
			$q['w'] = ''.intval($q['w']);
			$where .= " AND WEEK(post_date, 1)='" . $q['w'] . "'";
		}

		if ( intval($q['comments_popup']) )
			$q['p'] = intval($q['comments_popup']);

		// If a attachment is requested by number, let it supercede any post number.
		if ( ($q['attachment_id'] != '') && (intval($q['attachment_id']) != 0) )
			$q['p'] = (int) $q['attachment_id'];

		// If a post number is specified, load that post
		if (($q['p'] != '') && intval($q['p']) != 0) {
			$q['p'] =  (int) $q['p'];
			$where = ' AND ID = ' . $q['p'];
		}

		if (($q['page_id'] != '') && (intval($q['page_id']) != 0)) {
			$q['page_id'] = intval($q['page_id']);
			if  ( ('page' == get_option('show_on_front') ) && ( $q['page_id'] == get_option('page_for_posts') ) ) {
				$this->is_page = false;
				$this->is_home = true;
				$this->is_posts_page = true;
			} else {
				$q['p'] = $q['page_id'];
				$where = ' AND ID = '.$q['page_id'];
			}
		}

		// If a search pattern is specified, load the posts that match
		if (!empty($q['s'])) {
			// added slashes screw with quote grouping when done early, so done later
			$q['s'] = stripslashes($q['s']);
			if ($q['sentence']) {
				$q['search_terms'] = array($q['s']);
			}
			else {
				preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $q[s], $matches);
				$q['search_terms'] = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);
			}
			$n = ($q['exact']) ? '' : '%';
			$searchand = '';
			foreach((array)$q['search_terms'] as $term) {
				$term = addslashes_gpc($term);
				$search .= "{$searchand}((post_title LIKE '{$n}{$term}{$n}') OR (post_content LIKE '{$n}{$term}{$n}'))";
				$searchand = ' AND ';
			}
			$term = addslashes_gpc($q['s']); 
			if (!$q['sentence'] && count($q['search_terms']) > 1 && $q['search_terms'][0] != $q['s'] ) $search .= " OR (post_title LIKE '{$n}{$term}{$n}') OR (post_content LIKE '{$n}{$term}{$n}')";
			
			$search = " AND ({$search}) ";
		}

		// Category stuff

		if ((empty($q['cat'])) || ($q['cat'] == '0') || 
				// Bypass cat checks if fetching specific posts
				( $this->is_single || $this->is_page )) {
			$whichcat='';
		} else {
			$q['cat'] = ''.urldecode($q['cat']).'';
			$q['cat'] = addslashes_gpc($q['cat']);
			$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) ";
			$cat_array = preg_split('/[,\s]+/', $q['cat']);
			$in_cats = $out_cats = $out_posts = '';
			foreach ( $cat_array as $cat ) {
				$cat = intval($cat);
				$in = strstr($cat, '-') ? false : true;
				$cat = trim($cat, '-');
				if ( $in )
					$in_cats .= "$cat, " . get_category_children($cat, '', ', ');
				else
					$out_cats .= "$cat, " . get_category_children($cat, '', ', ');				
			}
			$in_cats = substr($in_cats, 0, -2);
			$out_cats = substr($out_cats, 0, -2);
			if ( strlen($in_cats) > 0 )
				$in_cats = " AND category_id IN ($in_cats)";
			if ( strlen($out_cats) > 0 ) {
				$ids = $wpdb->get_col("SELECT post_id FROM $wpdb->post2cat WHERE category_id IN ($out_cats)");
				if ( is_array($ids) && count($ids > 0) ) {
					foreach ( $ids as $id )
						$out_posts .= "$id, ";
					$out_posts = substr($out_posts, 0, -2);
				}
				if ( strlen($out_posts) > 0 )
					$out_cats = " AND ID NOT IN ($out_posts)";
			}
			$whichcat = $in_cats . $out_cats;
			$groupby = "{$wpdb->posts}.ID";
		}

		// Category stuff for nice URLs
		if ('' != $q['category_name']) {
			$reqcat = get_category_by_path($q['category_name']);
			$q['category_name'] = str_replace('%2F', '/', urlencode(urldecode($q['category_name'])));
			$cat_paths = '/' . trim($q['category_name'], '/');
			$q['category_name'] = sanitize_title(basename($cat_paths));

			$cat_paths = '/' . trim(urldecode($q['category_name']), '/');
			$q['category_name'] = sanitize_title(basename($cat_paths));
			$cat_paths = explode('/', $cat_paths);
			foreach($cat_paths as $pathdir)
				$cat_path .= ($pathdir!=''?'/':'') . sanitize_title($pathdir);

			//if we don't match the entire hierarchy fallback on just matching the nicename
			if ( empty($reqcat) )
				$reqcat = get_category_by_path($q['category_name'], false);

			if ( !empty($reqcat) )
				$reqcat = $reqcat->cat_ID;
			else
				$reqcat = 0;

			$q['cat'] = $reqcat;
				
			$tables = ", $wpdb->post2cat, $wpdb->categories";
			$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) LEFT JOIN $wpdb->categories ON ($wpdb->post2cat.category_id = $wpdb->categories.cat_ID) ";
			$whichcat = " AND category_id IN ({$q['cat']}, ";
			$whichcat .= get_category_children($q['cat'], '', ', ');
			$whichcat = substr($whichcat, 0, -2);
			$whichcat .= ")";
			$groupby = "{$wpdb->posts}.ID";
		}

		// Author/user stuff

		if ((empty($q['author'])) || ($q['author'] == '0')) {
			$whichauthor='';
		} else {
			$q['author'] = ''.urldecode($q['author']).'';
			$q['author'] = addslashes_gpc($q['author']);
			if (stristr($q['author'], '-')) {
				$eq = '!=';
				$andor = 'AND';
				$q['author'] = explode('-', $q['author']);
				$q['author'] = ''.intval($q['author'][1]);
			} else {
				$eq = '=';
				$andor = 'OR';
			}
			$author_array = preg_split('/[,\s]+/', $q['author']);
			$whichauthor .= ' AND (post_author '.$eq.' '.intval($author_array[0]);
			for ($i = 1; $i < (count($author_array)); $i = $i + 1) {
				$whichauthor .= ' '.$andor.' post_author '.$eq.' '.intval($author_array[$i]);
			}
			$whichauthor .= ')';
		}

		// Author stuff for nice URLs

		if ('' != $q['author_name']) {
			if (stristr($q['author_name'],'/')) {
				$q['author_name'] = explode('/',$q['author_name']);
				if ($q['author_name'][count($q['author_name'])-1]) {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-1];#no trailing slash
				} else {
					$q['author_name'] = $q['author_name'][count($q['author_name'])-2];#there was a trailling slash
				}
			}
			$q['author_name'] = sanitize_title($q['author_name']);
			$q['author'] = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_nicename='".$q['author_name']."'");
			$whichauthor .= ' AND (post_author = '.intval($q['author']).')';
		}

		$where .= $search.$whichcat.$whichauthor;

		if ((empty($q['order'])) || ((strtoupper($q['order']) != 'ASC') && (strtoupper($q['order']) != 'DESC'))) {
			$q['order']='DESC';
		}

		// Order by
		if (empty($q['orderby'])) {
			$q['orderby'] = 'post_date '.$q['order'];
		} else {
			// Used to filter values
			$allowed_keys = array('author', 'date', 'category', 'title', 'modified', 'menu_order');
			$q['orderby'] = urldecode($q['orderby']);
			$q['orderby'] = addslashes_gpc($q['orderby']);
			$orderby_array = explode(' ',$q['orderby']);
			if ( empty($orderby_array) )
				$orderby_array[] = $q['orderby'];
			$q['orderby'] = '';
			for ($i = 0; $i < count($orderby_array); $i++) {
				// Only allow certain values for safety
				$orderby = $orderby_array[$i];
				if ( 'menu_order' != $orderby )
					$orderby = 'post_' . $orderby;
				if ( in_array($orderby_array[$i], $allowed_keys) )
					$q['orderby'] .= (($i == 0) ? '' : ',') . "$orderby {$q['order']}";
			}
			if ( empty($q['orderby']) )
				$q['orderby'] = 'post_date '.$q['order'];
		}

		if ( $this->is_attachment ) {
			$where .= " AND (post_type = 'attachment')";
		} elseif ($this->is_page) {
			$where .= " AND (post_type = 'page')";
		} elseif ($this->is_single) {
			$where .= " AND (post_type = 'post')";
		} else {
			$where .= " AND (post_type = '$post_type' AND (post_status = 'publish'";

			if ( is_admin() )
				$where .= " OR post_status = 'future' OR post_status = 'draft'";
	
			if ( is_user_logged_in() ) {
				if ( 'post' == $post_type )
					$cap = 'edit_private_posts';
				else
					$cap = 'edit_private_pages';

				if ( current_user_can($cap) )
					$where .= " OR post_status = 'private'";
				else
				$where .= " OR post_author = $user_ID AND post_status = 'private'";
			}

			$where .= '))';
		}

		// Apply filters on where and join prior to paging so that any
		// manipulations to them are reflected in the paging by day queries.
		$where = apply_filters('posts_where', $where);
		$join = apply_filters('posts_join', $join);

		// Paging
		if (empty($q['nopaging']) && ! $this->is_single && ! $this->is_page) {
			$page = abs(intval($q['paged']));
			if (empty($page)) {
				$page = 1;
			}

			if (($q['what_to_show'] == 'posts')) {
				if ( empty($q['offset']) ) {
					$pgstrt = '';
					$pgstrt = (intval($page) -1) * $q['posts_per_page'] . ', ';
					$limits = 'LIMIT '.$pgstrt.$q['posts_per_page'];
				} else { // we're ignoring $page and using 'offset'
					$q['offset'] = abs(intval($q['offset']));
					$pgstrt = $q['offset'] . ', ';
					$limits = 'LIMIT ' . $pgstrt . $q['posts_per_page'];
				}
			} elseif ($q['what_to_show'] == 'days') {
				$startrow = $q['posts_per_page'] * (intval($page)-1);
				$start_date = $wpdb->get_var("SELECT max(post_date) FROM $wpdb->posts $join WHERE (1=1) $where GROUP BY year(post_date), month(post_date), dayofmonth(post_date) ORDER BY post_date DESC LIMIT $startrow,1");
				$endrow = $startrow + $q['posts_per_page'] - 1;
				$end_date = $wpdb->get_var("SELECT min(post_date) FROM $wpdb->posts $join WHERE (1=1) $where GROUP BY year(post_date), month(post_date), dayofmonth(post_date) ORDER BY post_date DESC LIMIT $endrow,1");

				if ($page > 1) {
					$where .= " AND post_date >= '$end_date' AND post_date <= '$start_date'";
				} else {
					$where .= " AND post_date >= '$end_date'";
				}
			}
		}

		// Apply post-paging filters on where and join.  Only plugins that
		// manipulate paging queries should use these hooks.
		$where = apply_filters('posts_where_paged', $where);
		$groupby = apply_filters('posts_groupby', $groupby);
		if ( ! empty($groupby) )
			$groupby = 'GROUP BY ' . $groupby;
		$join = apply_filters('posts_join_paged', $join);
		$orderby = apply_filters('posts_orderby', $q['orderby']);
		$distinct = apply_filters('posts_distinct', $distinct);
		$fields = apply_filters('posts_fields', "$wpdb->posts.*");
		$found_rows = '';
		if ( !empty($limits) )
			$found_rows = 'SQL_CALC_FOUND_ROWS';
		$request = " SELECT $found_rows $distinct $fields FROM $wpdb->posts $join WHERE 1=1 $where $groupby ORDER BY $orderby $limits";
		$this->request = apply_filters('posts_request', $request);

		$this->posts = $wpdb->get_results($this->request);
		if ( !empty($limits) ) {
			$num_rows = $wpdb->get_var('SELECT FOUND_ROWS()');
			global $max_num_pages;
			$max_num_pages = $num_rows / $q['posts_per_page'];
		}
		// Check post status to determine if post should be displayed.
		if ( !empty($this->posts) && ($this->is_single || $this->is_page) ) {
			$status = get_post_status($this->posts[0]);
			//$type = get_post_type($this->posts[0]);
			if ( ('publish' != $status) ) {
				if ( ! is_user_logged_in() ) {
					// User must be logged in to view unpublished posts.
					$this->posts = array();
				} else {
					if ('draft' == $status) {
						// User must have edit permissions on the draft to preview.
						if (! current_user_can('edit_post', $this->posts[0]->ID)) {
							$this->posts = array();
						} else {
							$this->is_preview = true;
							$this->posts[0]->post_date = current_time('mysql');
						}
					}  else if ('future' == $status) {
						$this->is_preview = true;
						if (!current_user_can('edit_post', $this->posts[0]->ID)) {
							$this->posts = array ( );
						}
					} else {
						if (! current_user_can('read_post', $this->posts[0]->ID))
							$this->posts = array();
					}
				}
			}
		}

		update_post_caches($this->posts);

		$this->posts = apply_filters('the_posts', $this->posts);
		$this->post_count = count($this->posts);
		if ($this->post_count > 0) {
			$this->post = $this->posts[0];
		}

		return $this->posts;
	}

	function next_post() {
        
		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}

	function the_post() {
		global $post;
		$this->in_the_loop = true;
		$post = $this->next_post();
		setup_postdata($post);

		if ( $this->current_post == 0 ) // loop has just started
			do_action('loop_start');
	}

	function have_posts() {
		if ($this->current_post + 1 < $this->post_count) {
			return true;
		} elseif ($this->current_post + 1 == $this->post_count) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

		$this->in_the_loop = false;
		return false;
	}

	function rewind_posts() {
		$this->current_post = -1;
		if ($this->post_count > 0) {
			$this->post = $this->posts[0];
		}
	}
    
	function &query($query) {
		$this->parse_query($query);
		return $this->get_posts();
	}

	function get_queried_object() {
		if (isset($this->queried_object)) {
			return $this->queried_object;
		}

		$this->queried_object = NULL;
		$this->queried_object_id = 0;

		if ($this->is_category) {
			$cat = $this->get('cat');
			$category = &get_category($cat);
			$this->queried_object = &$category;
			$this->queried_object_id = $cat;
		} else if ($this->is_posts_page) {
			$this->queried_object = & get_page(get_option('page_for_posts'));
			$this->queried_object_id = $this->queried_object->ID;
		} else if ($this->is_single) {
			$this->queried_object = $this->post;
			$this->queried_object_id = $this->post->ID;
		} else if ($this->is_page) {
			$this->queried_object = $this->post;
			$this->queried_object_id = $this->post->ID;
		} else if ($this->is_author) {
			$author_id = $this->get('author');
			$author = get_userdata($author_id);
			$this->queried_object = $author;
			$this->queried_object_id = $author_id;
		}

		return $this->queried_object;
	}

	function get_queried_object_id() {
		$this->get_queried_object();

		if (isset($this->queried_object_id)) {
			return $this->queried_object_id;
		}

		return 0;
	}

	function WP_Query ($query = '') {
		if (! empty($query)) {
			$this->query($query);
		}
	}
}

//
// Private helper functions
//

// Setup global post data.
function setup_postdata($post) {
	global $id, $postdata, $authordata, $day, $page, $pages, $multipage, $more, $numpages, $wp_query;
	global $pagenow;

	$id = $post->ID;

	$authordata = get_userdata($post->post_author);

	$day = mysql2date('d.m.y', $post->post_date);
	$currentmonth = mysql2date('m', $post->post_date);
	$numpages = 1;
	$page = get_query_var('page');
	if ( !$page )
		$page = 1;
	if ( is_single() || is_page() )
		$more = 1;
	$content = $post->post_content;
	if ( preg_match('/<!--nextpage-->/', $content) ) {
		if ( $page > 1 )
			$more = 1;
		$multipage = 1;
		$content = str_replace("\n<!--nextpage-->\n", '<!--nextpage-->', $content);
		$content = str_replace("\n<!--nextpage-->", '<!--nextpage-->', $content);
		$content = str_replace("<!--nextpage-->\n", '<!--nextpage-->', $content);
		$pages = explode('<!--nextpage-->', $content);
		$numpages = count($pages);
	} else {
		$pages[0] = $post->post_content;
		$multipage = 0;
	}
	return true;
}

?>
