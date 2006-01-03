<?php

class WP_Query {
	var $query;
	var $query_vars;
	var $queried_object;
	var $queried_object_id;

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
	}
	
	function init () {
		unset($this->posts);
		unset($this->query);
		unset($this->query_vars);
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

	// Parse a query string and set query type booleans.
	function parse_query ($query) {
		if ( !empty($query) || !isset($this->query) ) {
			$this->init();
			parse_str($query, $qv);
			$this->query = $query;
			$this->query_vars = $qv;
		}

		if ('404' == $qv['error']) {
			$this->is_404 = true;
			if ( !empty($query) ) {
				do_action('parse_query', array(&$this));
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
		} elseif ('' != $qv['static'] || '' != $qv['pagename'] || '' != $qv['page_id']) {
			$this->is_page = true;
			$this->is_single = false;
		} elseif (!empty($qv['s'])) {
			$this->is_search = true;
			switch ($qv['show_post_type']) {
			case 'page' :
				$this->is_page = true;
				break;
			case 'attachment' :
				$this->is_attachment = true;
				break;
			}
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

			if ( 'attachment' == $qv['show_post_type'] ) {
				$this->is_attachment = true;
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

		if (strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
			$this->is_admin = true;
		}

		if ( ! ($this->is_attachment || $this->is_archive || $this->is_single || $this->is_page || $this->is_search || $this->is_feed || $this->is_trackback || $this->is_404 || $this->is_admin || $this->is_comments_popup)) {
			$this->is_home = true;
		}

		if ( !empty($query) ) {
			do_action('parse_query', array(&$this));
		}
	}

	function set_404() {
		$this->init_query_flags();
		$this->is_404 = true;	
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
		global $wpdb, $pagenow, $request, $user_ID;

		do_action('pre_get_posts', array(&$this));

		// Shorthand.
		$q = $this->query_vars;	

		// First let's clear some variables
		$whichcat = '';
		$whichauthor = '';
		$whichpage = '';
		$result = '';
		$where = '';
		$limits = '';
		$distinct = '';
		$join = '';

		if ( !isset($q['posts_per_page']) || $q['posts_per_page'] == 0 )
			$q['posts_per_page'] = get_settings('posts_per_page');
		if ( !isset($q['what_to_show']) )
			$q['what_to_show'] = get_settings('what_to_show');
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
			$q['posts_per_page'] = get_settings('posts_per_rss');
			$q['what_to_show'] = 'posts';
		}

		if (isset($q['page'])) {
			$q['page'] = trim($q['page'], '/');
			$q['page'] = (int) $q['page'];
		}
	
		$add_hours = intval(get_settings('gmt_offset'));
		$add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));
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
			$q['pagename'] = str_replace('%2F', '/', urlencode(urldecode($q['pagename'])));
			$page_paths = '/' . trim($q['pagename'], '/');
			$q['pagename'] = sanitize_title(basename($page_paths));
			$q['name'] = $q['pagename'];
			$page_paths = explode('/', $page_paths);
			foreach($page_paths as $pathdir)
				$page_path .= ($pathdir!=''?'/':'') . sanitize_title($pathdir);
				
			$all_page_ids = get_all_page_ids();
			$reqpage = 0;
			foreach ( $all_page_ids as $page_id ) {
				$page = get_page($page_id);
				if ( $page->fullpath == $page_path ) {
					$reqpage = $page_id;
					break;
				}
			}
			
			$where .= " AND (ID = '$reqpage')";
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
			$q['p'] = $q['page_id'];
			$where = ' AND ID = '.$q['page_id'];
		}

		// If a search pattern is specified, load the posts that match
		if (!empty($q['s'])) {
			$q['s'] = addslashes_gpc($q['s']);
			$search = ' AND (';
			$q['s'] = preg_replace('/, +/', ' ', $q['s']);
			$q['s'] = str_replace(',', ' ', $q['s']);
			$q['s'] = str_replace('"', ' ', $q['s']);
			$q['s'] = trim($q['s']);
			if ($q['exact']) {
				$n = '';
			} else {
				$n = '%';
			}
			if (!$q['sentence']) {
				$s_array = explode(' ',$q['s']);
				$q['search_terms'] = $s_array;
				$search .= '((post_title LIKE \''.$n.$s_array[0].$n.'\') OR (post_content LIKE \''.$n.$s_array[0].$n.'\'))';
				for ( $i = 1; $i < count($s_array); $i = $i + 1) {
					$search .= ' AND ((post_title LIKE \''.$n.$s_array[$i].$n.'\') OR (post_content LIKE \''.$n.$s_array[$i].$n.'\'))';
				}
				$search .= ' OR (post_title LIKE \''.$n.$q['s'].$n.'\') OR (post_content LIKE \''.$n.$q['s'].$n.'\')';
				$search .= ')';
			} else {
				$search = ' AND ((post_title LIKE \''.$n.$q['s'].$n.'\') OR (post_content LIKE \''.$n.$q['s'].$n.'\'))';
			}
		}

		// Category stuff

		if ((empty($q['cat'])) || ($q['cat'] == '0') || 
				// Bypass cat checks if fetching specific posts
				( $this->is_single || $this->is_page )) {
			$whichcat='';
		} else {
			$q['cat'] = ''.urldecode($q['cat']).'';
			$q['cat'] = addslashes_gpc($q['cat']);
			if (stristr($q['cat'],'-')) {
				// Note: if we have a negative, we ignore all the positives. It must
				// always mean 'everything /except/ this one'. We should be able to do
				// multiple negatives but we don't :-(
				$eq = '!=';
				$andor = 'AND';
				$q['cat'] = explode('-',$q['cat']);
				$q['cat'] = intval($q['cat'][1]);
			} else {
				$eq = '=';
				$andor = 'OR';
			}
			$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) ";
			$cat_array = preg_split('/[,\s]+/', $q['cat']);
			$whichcat .= ' AND (category_id '.$eq.' '.intval($cat_array[0]);
			$whichcat .= get_category_children($cat_array[0], ' '.$andor.' category_id '.$eq.' ');
			for ($i = 1; $i < (count($cat_array)); $i = $i + 1) {
				$whichcat .= ' '.$andor.' category_id '.$eq.' '.intval($cat_array[$i]);
				$whichcat .= get_category_children($cat_array[$i], ' '.$andor.' category_id '.$eq.' ');
			}
			$whichcat .= ')';
			if ($eq == '!=') {
				$q['cat'] = '-'.$q['cat']; // Put back the knowledge that we are excluding a category.
			}
		}

		// Category stuff for nice URIs

		global $cache_categories;
		if ('' != $q['category_name']) {
			$cat_paths = '/' . trim(urldecode($q['category_name']), '/');
			$q['category_name'] = sanitize_title(basename($cat_paths));
			$cat_paths = explode('/', $cat_paths);
			foreach($cat_paths as $pathdir)
				$cat_path .= ($pathdir!=''?'/':'') . sanitize_title($pathdir);

			$all_cat_ids = get_all_category_ids();
			$q['cat'] = 0; $partial_match = 0;
			foreach ( $all_cat_ids as $cat_id ) {
				$cat = get_category($cat_id);
				if ( $cat->fullpath == $cat_path ) {
					$q['cat'] = $cat_id;
					break;
				} elseif ( $cat->category_nicename == $q['category_name'] ) {
					$partial_match = $cat_id;
				}
			}
			
			//if we don't match the entire hierarchy fallback on just matching the nicename
			if (!$q['cat'] && $partial_match) {
				$q['cat'] = $partial_match;
			}			

			$tables = ", $wpdb->post2cat, $wpdb->categories";
			$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) LEFT JOIN $wpdb->categories ON ($wpdb->post2cat.category_id = $wpdb->categories.cat_ID) ";
			$whichcat = " AND (category_id = '" . $q['cat'] . "'";
			$whichcat .= get_category_children($q['cat'], " OR category_id = ");
			$whichcat .= ")";
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

		// Author stuff for nice URIs

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
			$q['orderby']='date '.$q['order'];
		} else {
			// Used to filter values
			$allowed_keys = array('author', 'date', 'category', 'title', 'modified');
			$q['orderby'] = urldecode($q['orderby']);
			$q['orderby'] = addslashes_gpc($q['orderby']);
			$orderby_array = explode(' ',$q['orderby']);
			if (!in_array($orderby_array[0],$allowed_keys)) {
				$orderby_array[0] = 'date';
			}
			$q['orderby'] = $orderby_array[0].' '.$q['order'];
			if (count($orderby_array)>1) {
				for ($i = 1; $i < (count($orderby_array)); $i = $i + 1) {
					// Only allow certain values for safety
					if (in_array($orderby_array[$i],$allowed_keys)) {
						$q['orderby'] .= ',post_'.$orderby_array[$i].' '.$q['order'];
					}
				}
			}
		}

		$now = gmdate('Y-m-d H:i:59');
		
		//only select past-dated posts, except if a logged in user is viewing a single: then, if they
		//can edit the post, we let them through
		if ($pagenow != 'post.php' && $pagenow != 'edit.php' && !($this->is_single && $user_ID)) {
			$where .= " AND post_date_gmt <= '$now'";
			$distinct = 'DISTINCT';
		}

		if ( $this->is_attachment ) {
			$where .= ' AND (post_status = "attachment")';
		} elseif ($this->is_page) {
			$where .= ' AND (post_status = "static")';
		} elseif ($this->is_single) {
			$where .= ' AND (post_status != "static")';
		} else {
			$where .= ' AND (post_status = "publish"';

			if (isset($user_ID) && ('' != intval($user_ID)))
				$where .= " OR post_author = $user_ID AND post_status != 'draft' AND post_status != 'static')";
			else
				$where .= ')';				
		}

		if (! $this->is_attachment )
			$where .= ' AND post_status != "attachment"';

		// Apply filters on where and join prior to paging so that any
		// manipulations to them are reflected in the paging by day queries.
		$where = apply_filters('posts_where', $where);
		$join = apply_filters('posts_join', $join);

		// Paging
		if (empty($q['nopaging']) && ! $this->is_single) {
			$page = $q['paged'];
			if (empty($page)) {
				$page = 1;
			}

			if (($q['what_to_show'] == 'posts')) {
				$pgstrt = '';
				$pgstrt = (intval($page) -1) * $q['posts_per_page'] . ', ';
				$limits = 'LIMIT '.$pgstrt.$q['posts_per_page'];
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
		$groupby = " $wpdb->posts.ID ";
		$groupby = apply_filters('posts_groupby', $groupby);
		$join = apply_filters('posts_join_paged', $join);
		$orderby = "post_" . $q['orderby'];
		$orderby = apply_filters('posts_orderby', $orderby); 
		$request = " SELECT $distinct * FROM $wpdb->posts $join WHERE 1=1" . $where . " GROUP BY " . $groupby . " ORDER BY " . $orderby . " $limits";
		$request = apply_filters('posts_request', $request);

		$this->posts = $wpdb->get_results($request);

		// Check post status to determine if post should be displayed.
		if ($this->is_single) {
			$status = get_post_status($this->posts[0]);
			if ( ('publish' != $status) && ('static' != $status) ) {
				if ( ! (isset($user_ID) && ('' != intval($user_ID))) ) {
					// User must be logged in to view unpublished posts.
					$this->posts = array();
				} else {
					if ('draft' == $status) {
						// User must have edit permissions on the draft to preview.
						if (! current_user_can('edit_post', $this->posts[0]->ID)) {
							$this->posts = array();
						} else {
							$this->is_preview = true;
						}
					} else {
						if (! current_user_can('read_post', $this->posts[0]->ID))
							$this->posts = array();
					}
				}
			} else {
				if (mysql2date('U', $this->posts[0]->post_date_gmt) > mysql2date('U', $now)) { //it's future dated
					$this->is_preview = true;
					if (!current_user_can('edit_post', $this->posts[0]->ID)) {
						$this->posts = array ( );
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
		
		// Save any changes made to the query vars.
		$this->query_vars = $q;
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

class retrospam_mgr {
	var $spam_words;
	var $comments_list;
	var $found_comments;

	function retrospam_mgr() {
		global $wpdb;

		$list = explode("\n", get_settings('moderation_keys') );
		$list = array_unique( $list );
		$this->spam_words = $list;

		$this->comment_list = $wpdb->get_results("SELECT comment_ID AS ID, comment_content AS text, comment_approved AS approved, comment_author_url AS url, comment_author_ip AS ip, comment_author_email AS email FROM $wpdb->comments ORDER BY comment_ID ASC");
	}	// End of class constructor

	function move_spam( $id_list ) {
		global $wpdb;
		$cnt = 0;
		$id_list = explode( ',', $id_list );

		foreach ( $id_list as $comment ) {
			if ( $wpdb->query("update $wpdb->comments set comment_approved = '0' where comment_ID = '$comment'") ) {
				$cnt++;
			}
		}
		echo "<div class='updated'><p>$cnt comment";
		if ($cnt != 1 ) echo "s";
		echo " moved to the moderation queue.</p></div>\n";
	}	// End function move_spam

	function find_spam() {
		$in_queue = 0;

		foreach( $this->comment_list as $comment ) {
			if( $comment->approved == 1 ) {
				foreach( $this->spam_words as $word ) {
					$word = trim($word);
					if ( empty( $word ) )
						continue;
					$fulltext = strtolower($comment->email.' '.$comment->url.' '.$comment->ip.' '.$comment->text);
					if( strpos( $fulltext, strtolower($word) ) != FALSE ) {
						$this->found_comments[] = $comment->ID;
						break;
					}
				}
			} else {
				$in_queue++;
			}
		}
		return array( 'found' => $this->found_comments, 'in_queue' => $in_queue );
	}	// End function find_spam

	function display_edit_form( $counters ) {
		$numfound = count($counters[found]);
		$numqueue = $counters[in_queue];

		$body = '<p>' . sprintf(__('Suspected spam comments: <strong>%s</strong>'), $numfound) . '</p>';

		if ( count($counters[found]) > 0 ) {
			$id_list = implode( ',', $counters[found] );
			$body .= '<p><a href="options-discussion.php?action=retrospam&amp;move=true&amp;ids='.$id_list.'">'. __('Move suspect comments to moderation queue &raquo;') . '</a></p>';

		}
		$head = '<div class="wrap"><h2>' . __('Check Comments Results:') . '</h2>';

		$foot .= '<p><a href="options-discussion.php">' . __('&laquo; Return to Discussion Options page.') . '</a></p></div>';
		
		return $head . $body . $foot;
	} 	// End function display_edit_form

}

class WP_Rewrite {
	var $permalink_structure;
	var $category_base;
	var $category_structure;
	var $author_base = 'author';
	var $author_structure;
	var $date_structure;
	var $page_structure;
	var $search_base = 'search';
	var $search_structure;
	var $comments_base = 'comments';
	var $feed_base = 'feed';
	var $comments_feed_structure;
	var $feed_structure;
	var $front;
	var $root = '';
	var $index = 'index.php';
	var $matches = '';
	var $rules;
	var $use_verbose_rules = false;
	var $rewritecode = 
		array(
					'%year%',
					'%monthnum%',
					'%day%',
					'%hour%',
					'%minute%',
					'%second%',
					'%postname%',
					'%post_id%',
					'%category%',
					'%author%',
					'%pagename%',
					'%search%'
					);

	var $rewritereplace = 
		array(
					'([0-9]{4})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([^/]+)',
					'([0-9]+)',
					'(.+?)',
					'([^/]+)',
					'([^/]+)',
					'(.+)'
					);

	var $queryreplace = 
		array (
					'year=',
					'monthnum=',
					'day=',
					'hour=',
					'minute=',
					'second=',
					'name=',
					'p=',
					'category_name=',
					'author_name=',
					'pagename=',
					's='
					);

	var $feeds = array ('feed', 'rdf', 'rss', 'rss2', 'atom');

	function using_permalinks() {
		if (empty($this->permalink_structure))
			return false;
		else
			return true;
	}					

	function using_index_permalinks() {
		if (empty($this->permalink_structure)) {
			return false;
		}

		// If the index is not in the permalink, we're using mod_rewrite.
		if (preg_match('#^/*' . $this->index . '#', $this->permalink_structure)) {
			return true;
		}
    
		return false;
	}

	function using_mod_rewrite_permalinks() {
		if ( $this->using_permalinks() && ! $this->using_index_permalinks())
			return true;
		else
			return false;
	}					

	function preg_index($number) {
		$match_prefix = '$';
		$match_suffix = '';

		if (! empty($this->matches)) {
			$match_prefix = '$' . $this->matches . '['; 
			$match_suffix = ']';
		}        

		return "$match_prefix$number$match_suffix";        
	}

	function page_rewrite_rules() {
		$uris = get_settings('page_uris');
		$attachment_uris = get_settings('page_attachment_uris');

		$rewrite_rules = array();
		$page_structure = $this->get_page_permastruct();
		if( is_array( $attachment_uris ) ) {
			foreach ($attachment_uris as $uri => $pagename) {
				$this->add_rewrite_tag('%pagename%', "($uri)", 'attachment=');
				$rewrite_rules += $this->generate_rewrite_rules($page_structure);
			}
		}
		if( is_array( $uris ) ) {
			foreach ($uris as $uri => $pagename) {
				$this->add_rewrite_tag('%pagename%', "($uri)", 'pagename=');
				$rewrite_rules += $this->generate_rewrite_rules($page_structure);
			}
		}

		return $rewrite_rules;
	}

	function get_date_permastruct() {
		if (isset($this->date_structure)) {
			return $this->date_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->date_structure = '';
			return false;
		}
		
		// The date permalink must have year, month, and day separated by slashes.
		$endians = array('%year%/%monthnum%/%day%', '%day%/%monthnum%/%year%', '%monthnum%/%day%/%year%');

		$this->date_structure = '';
		$date_endian = '';

		foreach ($endians as $endian) {
			if (false !== strpos($this->permalink_structure, $endian)) {
				$date_endian= $endian;
				break;
			}
		} 

		if ( empty($date_endian) )
			$date_endian = '%year%/%monthnum%/%day%';

		// Do not allow the date tags and %post_id% to overlap in the permalink
		// structure. If they do, move the date tags to $front/date/.  
		$front = $this->front;
		preg_match_all('/%.+?%/', $this->permalink_structure, $tokens);
		$tok_index = 1;
		foreach ($tokens[0] as $token) {
			if ( ($token == '%post_id%') && ($tok_index <= 3) ) {
				$front = $front . 'date/';
				break;
			}
		}

		$this->date_structure = $front . $date_endian;

		return $this->date_structure;
	}

	function get_year_permastruct() {
		$structure = $this->get_date_permastruct($this->permalink_structure);

		if (empty($structure)) {
			return false;
		}

		$structure = str_replace('%monthnum%', '', $structure);
		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	function get_month_permastruct() {
		$structure = $this->get_date_permastruct($this->permalink_structure);

		if (empty($structure)) {
			return false;
		}

		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	function get_day_permastruct() {
		return $this->get_date_permastruct($this->permalink_structure);
	}

	function get_category_permastruct() {
		if (isset($this->category_structure)) {
			return $this->category_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->category_structure = '';
			return false;
		}

		if (empty($this->category_base))
			$this->category_structure = $this->front . 'category/';
		else
			$this->category_structure = $this->category_base . '/';

		$this->category_structure .= '%category%';
		
		return $this->category_structure;
	}

	function get_author_permastruct() {
		if (isset($this->author_structure)) {
			return $this->author_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->author_structure = '';
			return false;
		}

		$this->author_structure = $this->front . $this->author_base . '/%author%';

		return $this->author_structure;
	}

	function get_search_permastruct() {
		if (isset($this->search_structure)) {
			return $this->search_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->search_structure = '';
			return false;
		}

		$this->search_structure = $this->root . $this->search_base . '/%search%';

		return $this->search_structure;
	}

	function get_page_permastruct() {
		if (isset($this->page_structure)) {
			return $this->page_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->page_structure = '';
			return false;
		}

		$this->page_structure = $this->root . '%pagename%';

		return $this->page_structure;
	}

	function get_feed_permastruct() {
		if (isset($this->feed_structure)) {
			return $this->feed_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->feed_structure = '';
			return false;
		}

		$this->feed_structure = $this->root . $this->feed_base . '/%feed%';

		return $this->feed_structure;
	}

	function get_comment_feed_permastruct() {
		if (isset($this->comment_feed_structure)) {
			return $this->comment_feed_structure;
		}

		if (empty($this->permalink_structure)) {
			$this->comment_feed_structure = '';
			return false;
		}

		$this->comment_feed_structure = $this->root . $this->comments_base . '/' . $this->feed_base . '/%feed%';

		return $this->comment_feed_structure;
	}

	function add_rewrite_tag($tag, $pattern, $query) {
		// If the tag already exists, replace the existing pattern and query for
		// that tag, otherwise add the new tag, pattern, and query to the end of
		// the arrays.
		$position = array_search($tag, $this->rewritecode);		
		if (FALSE !== $position && NULL !== $position) {
			$this->rewritereplace[$position] = $pattern;
			$this->queryreplace[$position] = $query;			
		} else {
			$this->rewritecode[] = $tag;
			$this->rewritereplace[] = $pattern;
			$this->queryreplace[] = $query;
		}
	}

	function generate_rewrite_rules($permalink_structure, $page = true, $feed = true, $forcomments = false, $walk_dirs = true) {
		$feedregex2 = '';
		foreach ($this->feeds as $feed_name) {
			$feedregex2 .= $feed_name . '|';
		}
		$feedregex2 = '(' . trim($feedregex2, '|') .  ')/?$';
		$feedregex = $this->feed_base  . '/' . $feedregex2;

		$trackbackregex = 'trackback/?$';
		$pageregex = 'page/?([0-9]{1,})/?$';
		
		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		preg_match_all('/%.+?%/', $permalink_structure, $tokens);

		$num_tokens = count($tokens[0]);

		$index = $this->index;
		$feedindex = $index;
		$trackbackindex = $index;
		for ($i = 0; $i < $num_tokens; ++$i) {
			if (0 < $i) {
				$queries[$i] = $queries[$i - 1] . '&';
			}
             
			$query_token = str_replace($this->rewritecode, $this->queryreplace, $tokens[0][$i]) . $this->preg_index($i+1);
			$queries[$i] .= $query_token;
		}

		$structure = $permalink_structure;
		if ($front != '/') {
			$structure = str_replace($front, '', $structure);
		}
		$structure = trim($structure, '/');
		if ($walk_dirs) {
			$dirs = explode('/', $structure);
		} else {
			$dirs[] = $structure;
		}
		$num_dirs = count($dirs);

		$front = preg_replace('|^/+|', '', $front);

		$post_rewrite = array();
		$struct = $front;
		for ($j = 0; $j < $num_dirs; ++$j) {
			$struct .= $dirs[$j] . '/';
			$struct = ltrim($struct, '/');
			$match = str_replace($this->rewritecode, $this->rewritereplace, $struct);
			$num_toks = preg_match_all('/%.+?%/', $struct, $toks);
			$query = $queries[$num_toks - 1];

			$pagematch = $match . $pageregex;
			$pagequery = $index . '?' . $query . '&paged=' . $this->preg_index($num_toks + 1);

			$feedmatch = $match . $feedregex;
			$feedquery = $feedindex . '?' . $query . '&feed=' . $this->preg_index($num_toks + 1);

			$feedmatch2 = $match . $feedregex2;
			$feedquery2 = $feedindex . '?' . $query . '&feed=' . $this->preg_index($num_toks + 1);

			if ($forcomments) {
				$feedquery .= '&withcomments=1';
				$feedquery2 .= '&withcomments=1';
			}

			$rewrite = array();
			if ($feed) 
				$rewrite = array($feedmatch => $feedquery, $feedmatch2 => $feedquery2);
			if ($page)
				$rewrite = $rewrite + array($pagematch => $pagequery);

			if ($num_toks) {
				$post = false;
				$page = false;
				if (strstr($struct, '%postname%') || strstr($struct, '%post_id%')
						|| strstr($struct, '%pagename%')
						|| (strstr($struct, '%year%') &&  strstr($struct, '%monthnum%') && strstr($struct, '%day%') && strstr($struct, '%hour%') && strstr($struct, '%minute') && strstr($struct, '%second%'))) {
					$post = true;
					if  ( strstr($struct, '%pagename%') )
						$page = true;
					$trackbackmatch = $match . $trackbackregex;
					$trackbackquery = $trackbackindex . '?' . $query . '&tb=1';
					$match = rtrim($match, '/');
					$submatchbase = str_replace(array('(',')'),'',$match);
					$sub1 = $submatchbase . '/([^/]+)/';
					$sub1tb = $sub1 . $trackbackregex;
					$sub1feed = $sub1 . $feedregex;
					$sub1feed2 = $sub1 . $feedregex2;
					$sub1 .= '?$';
					$sub2 = $submatchbase . '/attachment/([^/]+)/';
					$sub2tb = $sub2 . $trackbackregex;
					$sub2feed = $sub2 . $feedregex;
					$sub2feed2 = $sub2 . $feedregex2;
					$sub2 .= '?$';
					$subquery = $index . '?attachment=' . $this->preg_index(1);
					$subtbquery = $subquery . '&tb=1';
					$subfeedquery = $subquery . '&feed=' . $this->preg_index(2);
					$match = $match . '(/[0-9]+)?/?$';
					$query = $index . '?' . $query . '&page=' . $this->preg_index($num_toks + 1);
				} else {
					$match .= '?$';
					$query = $index . '?' . $query;
				}
				        
				$rewrite = $rewrite + array($match => $query);

				if ($post) {
					$rewrite = array($trackbackmatch => $trackbackquery) + $rewrite;
					if ( ! $page )
						$rewrite = $rewrite + array($sub1 => $subquery, $sub1tb => $subtbquery, $sub1feed => $subfeedquery, $sub1feed2 => $subfeedquery);
					$rewrite = $rewrite + array($sub2 => $subquery, $sub2tb => $subtbquery, $sub2feed => $subfeedquery, $sub2feed2 => $subfeedquery);
				}
			}

			$post_rewrite = $rewrite + $post_rewrite;
		}

		return $post_rewrite;
	}

	function generate_rewrite_rule($permalink_structure, $walk_dirs = false) {
		return $this->generate_rewrite_rules($permalink_structure, false, false, false, $walk_dirs);
	}

	/* rewrite_rules
	 * Construct rewrite matches and queries from permalink structure.
	 * Returns an associate array of matches and queries.
	 */
	function rewrite_rules() {
		$rewrite = array();

		if (empty($this->permalink_structure)) {
			return $rewrite;
		}

		// Post
		$post_rewrite = $this->generate_rewrite_rules($this->permalink_structure);
		$post_rewrite = apply_filters('post_rewrite_rules', $post_rewrite);

		// Date
		$date_rewrite = $this->generate_rewrite_rules($this->get_date_permastruct());
		$date_rewrite = apply_filters('date_rewrite_rules', $date_rewrite);
		
		// Root
		$root_rewrite = $this->generate_rewrite_rules($this->root . '/');
		$root_rewrite = apply_filters('root_rewrite_rules', $root_rewrite);

		// Comments
		$comments_rewrite = $this->generate_rewrite_rules($this->root . $this->comments_base, true, true, true);
		$comments_rewrite = apply_filters('comments_rewrite_rules', $comments_rewrite);

		// Search
		$search_structure = $this->get_search_permastruct();
		$search_rewrite = $this->generate_rewrite_rules($search_structure);
		$search_rewrite = apply_filters('search_rewrite_rules', $search_rewrite);

		// Categories
		$category_rewrite = $this->generate_rewrite_rules($this->get_category_permastruct());
		$category_rewrite = apply_filters('category_rewrite_rules', $category_rewrite);

		// Authors
		$author_rewrite = $this->generate_rewrite_rules($this->get_author_permastruct());
		$author_rewrite = apply_filters('author_rewrite_rules', $author_rewrite);

		// Pages
		$page_rewrite = $this->page_rewrite_rules();
		$page_rewrite = apply_filters('page_rewrite_rules', $page_rewrite);

		// Put them together.
		$this->rules = $page_rewrite + $root_rewrite + $comments_rewrite + $search_rewrite + $category_rewrite + $author_rewrite + $date_rewrite + $post_rewrite;

		do_action('generate_rewrite_rules', array(&$this));
		$this->rules = apply_filters('rewrite_rules_array', $this->rules);

		return $this->rules;
	}

	function wp_rewrite_rules() {
		$this->rules = get_option('rewrite_rules');
		if ( empty($this->rules) ) {
			$this->matches = 'matches';
			$this->rewrite_rules();
			update_option('rewrite_rules', $this->rules);
		}

		return $this->rules;
	}

	function mod_rewrite_rules() {
		if ( ! $this->using_permalinks()) {
			return '';
		}

		$site_root = parse_url(get_settings('siteurl'));
		$site_root = trailingslashit($site_root['path']);

		$home_root = parse_url(get_settings('home'));
		$home_root = trailingslashit($home_root['path']);
    
		$rules = "<IfModule mod_rewrite.c>\n";
		$rules .= "RewriteEngine On\n";
		$rules .= "RewriteBase $home_root\n";

		if ($this->use_verbose_rules) {
			$this->matches = '';
			$rewrite = $this->rewrite_rules();
			$num_rules = count($rewrite);
			$rules .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n" .
				"RewriteCond %{REQUEST_FILENAME} -d\n" .
				"RewriteRule ^.*$ - [S=$num_rules]\n";
		
			foreach ($rewrite as $match => $query) {
				// Apache 1.3 does not support the reluctant (non-greedy) modifier.
				$match = str_replace('.+?', '.+', $match);

				// If the match is unanchored and greedy, prepend rewrite conditions
				// to avoid infinite redirects and eclipsing of real files.
				if ($match == '(.+)/?$' || $match == '([^/]+)/?$' ) {
					//nada.
				}
			
				if (strstr($query, $this->index)) {
					$rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA,L]\n";
				} else {
					$rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA,L]\n";
				}
			}
		} else {
			$rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n" .
				"RewriteCond %{REQUEST_FILENAME} !-d\n" .
				"RewriteRule . {$home_root}{$this->index}\n";
		}

		$rules .= "</IfModule>\n";

		$rules = apply_filters('mod_rewrite_rules', $rules);
		$rules = apply_filters('rewrite_rules', $rules);  // Deprecated

		return $rules;
	}

	function flush_rules() {
		generate_page_rewrite_rules();
		delete_option('rewrite_rules');
		$this->wp_rewrite_rules();
		if ( function_exists('save_mod_rewrite_rules') )
			save_mod_rewrite_rules();
	}

	function init() {
		$this->permalink_structure = get_settings('permalink_structure');
		$this->front = substr($this->permalink_structure, 0, strpos($this->permalink_structure, '%'));		
		$this->root = '';
		if ($this->using_index_permalinks()) {
			$this->root = $this->index . '/';
		}
		$this->category_base = get_settings('category_base');
		unset($this->category_structure);
		unset($this->author_structure);
		unset($this->date_structure);
		unset($this->page_structure);
		unset($this->search_structure);
		unset($this->feed_structure);
		unset($this->comment_feed_structure);
	}

	function set_permalink_structure($permalink_structure) {
		if ($permalink_structure != $this->permalink_structure) {
			update_option('permalink_structure', $permalink_structure);
			$this->init();
		}
	}

	function set_category_base($category_base) {
		if ($category_base != $this->category_base) {
			update_option('category_base', $category_base);
			$this->init();
		}
	}

	function WP_Rewrite() {
		$this->init();
	}
}

class WP {
	var $public_query_vars = array('m', 'p', 'posts', 'w', 'cat', 'withcomments', 's', 'search', 'exact', 'sentence', 'debug', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error', 'comments_popup', 'attachment', 'attachment_id', 'subpost', 'subpost_id');

	var $private_query_vars = array('posts_per_page', 'posts_per_archive_page', 'what_to_show', 'showposts', 'nopaging', 'show_post_type');

	var $query_vars;
	var $query_string;
	var $request;
	var $matched_rule;
	var $matched_query;
	var $did_permalink = false;

	function parse_request($extra_query_vars = '') {
		global $wp_rewrite;

		$this->query_vars = array();

		if (! empty($extra_query_vars))
			parse_str($extra_query_vars, $extra_query_vars);
		else
			$extra_query_vars = array();

		// Process PATH_INFO, REQUEST_URI, and 404 for permalinks.

		// Fetch the rewrite rules.
		$rewrite = $wp_rewrite->wp_rewrite_rules();

		if (! empty($rewrite)) {
			// If we match a rewrite rule, this will be cleared.
			$error = '404';
			$this->did_permalink = true;

			$pathinfo = $_SERVER['PATH_INFO'];
			$pathinfo_array = explode('?', $pathinfo);
			$pathinfo = $pathinfo_array[0];
			$req_uri = $_SERVER['REQUEST_URI'];
			$req_uri_array = explode('?', $req_uri);
			$req_uri = $req_uri_array[0];
			$self = $_SERVER['PHP_SELF'];
			$home_path = parse_url(get_settings('home'));
			$home_path = $home_path['path'];
			$home_path = trim($home_path, '/');

			// Trim path info from the end and the leading home path from the
			// front.  For path info requests, this leaves us with the requesting
			// filename, if any.  For 404 requests, this leaves us with the
			// requested permalink.	
			$req_uri = str_replace($pathinfo, '', $req_uri);
			$req_uri = trim($req_uri, '/');
			$req_uri = preg_replace("|^$home_path|", '', $req_uri);
			$req_uri = trim($req_uri, '/');
			$pathinfo = trim($pathinfo, '/');
			$pathinfo = preg_replace("|^$home_path|", '', $pathinfo);
			$pathinfo = trim($pathinfo, '/');
			$self = trim($self, '/');
			$self = preg_replace("|^$home_path|", '', $self);
			$self = str_replace($home_path, '', $self);
			$self = trim($self, '/');

			// The requested permalink is in $pathinfo for path info requests and
			//  $req_uri for other requests.
			if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
				$request = $pathinfo;
			} else {
				// If the request uri is the index, blank it out so that we don't try to match it against a rule.
				if ( $req_uri == $wp_rewrite->index )
					$req_uri = '';
				$request = $req_uri;
			}

			$this->request = $request;

			// Look for matches.
			$request_match = $request;
			foreach ($rewrite as $match => $query) {
				// If the requesting file is the anchor of the match, prepend it
				// to the path info.
				if ((! empty($req_uri)) && (strpos($match, $req_uri) === 0) && ($req_uri != $request)) {
					$request_match = $req_uri . '/' . $request;
				}

				if (preg_match("!^$match!", $request_match, $matches) ||
					preg_match("!^$match!", urldecode($request_match), $matches)) {
					// Got a match.
					$this->matched_rule = $match;

					// Trim the query of everything up to the '?'.
					$query = preg_replace("!^.+\?!", '', $query);

					// Substitute the substring matches into the query.
					eval("\$query = \"$query\";");
					$this->matched_query = $query;

					// Parse the query.
					parse_str($query, $query_vars);

					// If we're processing a 404 request, clear the error var
					// since we found something.
					if (isset($_GET['error']))
						unset($_GET['error']);

					if (isset($error))
						unset($error);

					break;
				}
			}

			// If req_uri is empty or if it is a request for ourself, unset error.
			if ( empty($request) || $req_uri == $self || strstr($_SERVER['PHP_SELF'], 'wp-admin/') ) {
				if (isset($_GET['error']))
					unset($_GET['error']);

				if (isset($error))
					unset($error);
					
				if ( isset($query_vars) && strstr($_SERVER['PHP_SELF'], 'wp-admin/') )
					unset($query_vars);
					
				$this->did_permalink = false;
			}
		}

		$this->public_query_vars = apply_filters('query_vars', $this->public_query_vars);

		for ($i=0; $i<count($this->public_query_vars); $i += 1) {
			$wpvar = $this->public_query_vars[$i];
			if (isset($extra_query_vars[$wpvar]))
				$this->query_vars[$wpvar] = $extra_query_vars[$wpvar];
			elseif (isset($GLOBALS[$wpvar]))
				$this->query_vars[$wpvar] = $GLOBALS[$wpvar];
			elseif (!empty($_POST[$wpvar]))
				$this->query_vars[$wpvar] = $_POST[$wpvar];
			elseif (!empty($_GET[$wpvar]))
				$this->query_vars[$wpvar] = $_GET[$wpvar];
			elseif (!empty($query_vars[$wpvar]))
				$this->query_vars[$wpvar] = $query_vars[$wpvar];
			else
				$this->query_vars[$wpvar] = '';
		}

		if ( isset($error) )
			$this->query_vars['error'] = $error;
	}

	function send_headers() {
		global $current_user;
		@header('X-Pingback: '. get_bloginfo('pingback_url'));
		if ( is_user_logged_in() )
			nocache_headers();
		if ( !empty($this->query_vars['error']) && '404' == $this->query_vars['error'] ) {
			status_header( 404 );
		} else if ( empty($this->query_vars['feed']) ) {
			@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
		} else {
			// We're showing a feed, so WP is indeed the only thing that last changed
			if ( $this->query_vars['withcomments'] )
				$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastcommentmodified('GMT'), 0).' GMT';
			else 
				$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
			$wp_etag = '"' . md5($wp_last_modified) . '"';
			@header("Last-Modified: $wp_last_modified");
			@header("ETag: $wp_etag");

			// Support for Conditional GET
			if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
			else $client_etag = false;

			$client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE']);
			// If string is empty, return 0. If not, attempt to parse into a timestamp
			$client_modified_timestamp = $client_last_modified ? strtotime($client_last_modified) : 0;

			// Make a timestamp for our most recent modification...	
			$wp_modified_timestamp = strtotime($wp_last_modified);

			if ( ($client_last_modified && $client_etag) ?
					 (($client_modified_timestamp >= $wp_modified_timestamp) && ($client_etag == $wp_etag)) :
					 (($client_modified_timestamp >= $wp_modified_timestamp) || ($client_etag == $wp_etag)) ) {
				status_header( 304 );
				exit;
			}
		}
	}

	function build_query_string() {
		$this->query_string = '';

		foreach ($this->public_query_vars as $wpvar) {
			if (isset($this->query_vars[$wpvar]) && '' != $this->query_vars[$wpvar]) {
				$this->query_string .= (strlen($this->query_string) < 1) ? '' : '&';
				$this->query_string .= $wpvar . '=' . rawurlencode($this->query_vars[$wpvar]);
			}
		}

		foreach ($this->private_query_vars as $wpvar) {
			if (isset($GLOBALS[$wpvar]) && '' != $GLOBALS[$wpvar]) {
				$this->query_string .= (strlen($this->query_string) < 1) ? '' : '&';
				$this->query_string .= $wpvar . '=' . rawurlencode($GLOBALS[$wpvar]);
			}
		}

		$this->query_string = apply_filters('query_string', $this->query_string);
	}

	function register_globals() {
		global $wp_query;
		// Extract updated query vars back into global namespace.
		foreach ($wp_query->query_vars as $key => $value) {
			$GLOBALS[$key] = $value;
		}

		$GLOBALS['query_string'] = & $this->query_string;
		$GLOBALS['posts'] = & $wp_query->posts;
		$GLOBALS['post'] = & $wp_query->post;

		if ( is_single() || is_page() ) {
			$GLOBALS['more'] = 1;
			$GLOBALS['single'] = 1;
		}
	}

	function init() {
		get_currentuserinfo();
	}

	function query_posts() {
		$this->build_query_string();
		query_posts($this->query_string);
 	}

	function handle_404() {
		global $wp_query;
		// Issue a 404 if a permalink request doesn't match any posts.  Don't
		// issue a 404 if one was already issued, if the request was a search,
		// or if the request was a regular query string request rather than a
		// permalink request.
		if ( (0 == count($wp_query->posts)) && !is_404() && !is_category() && !is_search() && ( $this->did_permalink || (!empty($_SERVER['QUERY_STRING']) && (false === strpos($_SERVER['REQUEST_URI'], '?'))) ) ) {
			$wp_query->set_404();
			status_header( 404 );
		}	elseif( is_404() != true ) {
			status_header( 200 );
		}
	}

	function main($query_args = '') {
		$this->init();
		$this->parse_request($query_args);
		$this->send_headers();
		$this->query_posts();
		$this->handle_404();
		$this->register_globals();
	}

	function WP() {
		// Empty.
	}
}

?>
