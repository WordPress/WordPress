<?php

class WP_Query {
	var $query;
	var $query_vars;
	var $queried_object;
	var $queried_object_id;

	var $posts;
	var $post_count = 0;
	var $current_post = -1;
	var $post;

	var $is_single = false;
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
	var $is_admin = false;

	function init () {
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

		unset($this->posts);
		unset($this->query);
		unset($this->query_vars);
		unset($this->queried_object);
		unset($this->queried_object_id);
		$this->post_count = 0;
		$this->current_post = -1;
	}

	function parse_query ($query) {
		$this->init();
		parse_str($query, $qv);
		$this->query = $query;
		$this->query_vars = $qv;
		$qv['m'] =  (int) $qv['m'];

		if ('' != $qv['name']) {
			$this->is_single = true;
		} else 	if (($qv['p'] != '') && ($qv['p'] != 'all')) {
			$this->is_single = true;            
		}	else if (('' != $qv['hour']) && ('' != $qv['minute']) &&('' != $qv['second']) && ('' != $qv['year']) && ('' != $qv['monthnum']) && ('' != $qv['day'])) {
			// If year, month, day, hour, minute, and second are set, a single 
		  // post is being queried.        
			$this->is_single = true;
		} else if ('' != $qv['static'] || '' != $qv['pagename'] || '' != $qv['page_id']) {
			$this->is_page = true;
			$this->is_single = false;
		} else if (!empty($qv['s'])) {
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

			if (empty($qv['cat']) || ($qv['cat'] == 'all') || ($qv['cat'] == '0')) {
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
            
			if ((empty($qv['author'])) || ($qv['author'] == 'all') || ($qv['author'] == '0')) {
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

		if ('404' == $qv['error']) {
			$this->is_404 = true;
		}

		if ('' != $qv['paged']) {
			$this->is_paged = true;
		}

		if (strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
			$this->is_admin = true;
		}

		if ( ! ($this->is_archive || $this->is_single || $this->is_page || $this->is_search || $this->is_feed || $this->is_trackback || $this->is_404 || $this->is_admin)) {
			$this->is_home = true;
		}
	}

	function get($query_var) {
		if (isset($this->query_vars[$query_var])) {
			return $this->query_vars[$query_var];
		}

		return '';
	}

	function get_posts() {
		global $wpdb, $pagenow, $request, $user_ID;

		// Shorthand.
		$q = $this->query_vars;	

		// First let's clear some variables
		$whichcat = '';
		$whichauthor = '';
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
		if ( (isset($q['posts_per_archive_page']) && $q['posts_per_archive_page'] != 0) && (is_archive() || is_search()) )
			$q['posts_per_page'] = $q['posts_per_archive_page'];
		if ( !isset($q['nopaging']) ) {
			if ($q['posts_per_page'] == -1) {
				$q['nopaging'] = true;
			} else {
				$q['nopaging'] = false;
			}
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

		if ('' != $q['name']) {
			$q['name'] = sanitize_title($q['name']);
			$where .= " AND post_name = '" . $q['name'] . "'";
		} else if ('' != $q['pagename']) {
			$q['pagename'] = sanitize_title($q['pagename']);
			$q['name'] = $q['pagename'];
			$where .= " AND post_name = '" . $q['pagename'] . "'";
		}


		if ( (int) $q['w'] ) {
			$q['w'] = ''.intval($q['w']);
			$where .= " AND WEEK(post_date, 1)='" . $q['w'] . "'";
		}

		// If a post number is specified, load that post
		if (($q['p'] != '') && ($q['p'] != 'all')) {
			$q['p'] =  (int) $q['p'];
			$where = ' AND ID = ' . $q['p'];
		}

		if (($q['page_id'] != '') && ($q['page_id'] != 'all')) {
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

		if ((empty($q['cat'])) || ($q['cat'] == 'all') || ($q['cat'] == '0') || 
				// Bypass cat checks if fetching specific posts
				(
				 intval($q['year']) || intval($q['monthnum']) || intval($q['day']) || intval($q['w']) ||
				 intval($q['p']) || !empty($q['name']) || !empty($q['s'])
				 )
				) {
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
			$cat_array = explode(' ',$q['cat']);
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

		if ('' != $q['category_name']) {
			if (stristr($q['category_name'],'/')) {
				$q['category_name'] = explode('/',$q['category_name']);
				if ($q['category_name'][count($q['category_name'])-1]) {
					$q['category_name'] = $q['category_name'][count($q['category_name'])-1]; // no trailing slash
				} else {
					$q['category_name'] = $q['category_name'][count($q['category_name'])-2]; // there was a trailling slash
				}
			}
			$q['category_name'] = sanitize_title($q['category_name']);
			$tables = ", $wpdb->post2cat, $wpdb->categories";
			$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) LEFT JOIN $wpdb->categories ON ($wpdb->post2cat.category_id = $wpdb->categories.cat_ID) ";
			$whichcat = " AND (category_nicename = '" . $q['category_name'] . "'";
			$q['cat'] = $wpdb->get_var("SELECT cat_ID FROM $wpdb->categories WHERE category_nicename = '" . $q['category_name'] . "'");
			$whichcat .= get_category_children($q['cat'], " OR category_id = ");
			$whichcat .= ")";
		}

		// Author/user stuff

		if ((empty($q['author'])) || ($q['author'] == 'all') || ($q['author'] == '0')) {
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
			$author_array = explode(' ', $q['author']);
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
			$allowed_keys = array('author','date','category','title');
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

		if ($q['p'] == 'all') {
			$where = '';
		}

		$now = gmdate('Y-m-d H:i:59');

		if ($pagenow != 'post.php' && $pagenow != 'edit.php') {
			if ((empty($q['poststart'])) || (empty($q['postend'])) || !($q['postend'] > $q['poststart'])) {
				$where .= " AND post_date_gmt <= '$now'";
			}

			$distinct = 'DISTINCT';
		}

		if (is_page()) {
			$where .= ' AND (post_status = "static"';
		} else {
			$where .= ' AND (post_status = "publish"';
		}

		// Get private posts
		if (isset($user_ID) && ('' != intval($user_ID)))
			$where .= " OR post_author = $user_ID AND post_status != 'draft' AND post_status != 'static')";
		else
			$where .= ')';

		// Apply filters on where and join prior to paging so that any
		// manipulations to them are reflected in the paging by day queries.
		$where = apply_filters('posts_where', $where);
		$join = apply_filters('posts_join', $join);

		// Paging
		if ( !empty($q['postend']) && ($q['postend'] > $q['poststart']) ) {
			if ($q['what_to_show'] == 'posts') {
				$q['poststart'] = intval($q['poststart']);
				$q['postend'] = intval($q['postend']);
				$limposts = $q['postend'] - $q['poststart'];
				$limits = ' LIMIT '.$q['poststart'].','.$limposts;
			} elseif ($q['what_to_show'] == 'days') {
				$q['poststart'] = intval($q['poststart']);
				$q['postend'] = intval($q['postend']);
				$limposts = $q['postend'] - $q['poststart'];
				$lastpostdate = get_lastpostdate();
				$lastpostdate = mysql2date('Y-m-d 00:00:00',$lastpostdate);
				$lastpostdate = mysql2date('U',$lastpostdate);
				$startdate = date('Y-m-d H:i:s', ($lastpostdate - (($q['poststart'] -1) * 86400)));
				$otherdate = date('Y-m-d H:i:s', ($lastpostdate - (($q['postend'] -1) * 86400)));
				$where .= " AND post_date > '$otherdate' AND post_date < '$startdate'";
			}
		} else if (empty($q['nopaging']) && ! is_single()) {
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
		$where .= " GROUP BY $wpdb->posts.ID";
		$join = apply_filters('posts_join_paged', $join);
		$request = " SELECT $distinct * FROM $wpdb->posts $join WHERE 1=1".$where." ORDER BY post_" . $q['orderby'] . " $limits";

		if ($q['preview']) {
			$request = 'SELECT 1-1'; // dummy mysql query for the preview
			// little funky fix for IEwin, rawk on that code
			$is_winIE = ((preg_match('/MSIE/',$HTTP_USER_AGENT)) && (preg_match('/Win/',$HTTP_USER_AGENT)));
			if (($is_winIE) && (!isset($IEWin_bookmarklet_fix))) {
				$preview_content =  preg_replace('/\%u([0-9A-F]{4,4})/e',  "'&#'.base_convert('\\1',16,10).';'", $preview_content);
			}
		}

		$this->posts = $wpdb->get_results($request);
		$this->posts = apply_filters('the_posts', $this->posts);
		$this->post_count = count($this->posts);
		if ($this->post_count > 0) {
			$this->post = $this->posts[0];
		}

		update_post_caches($this->posts);
		
		// Save any changes made to the query vars.
		$this->query_vars = $q;
		return $this->posts;
	}

	function next_post() {
        
		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}

	function have_posts() {
		if ($this->current_post + 1 < $this->post_count) {
			return true;
		}

		return false;
	}
    
	function query($query) {
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
			global $cache_categories;
			if (isset($cache_categories[$this->get('cat')])) {
				$this->queried_object = $cache_categories[$this->get('cat')];
				$this->queried_object_id = $this->get('cat');
			}
		} else if ($this->is_single) {
			$this->queried_object = $this->post;
			$this->queried_object_id = $this->post->ID;
		} else if ($this->is_page) {
			$this->queried_object = $this->post;
			$this->queried_object_id = $this->post->ID;
		} else if ($this->is_author) {
			global $cache_userdata;
			if (isset($cache_userdata[$this->get('author')])) {
				$this->queried_object = $cache_userdata[$this->get('author')];
				$this->queried_object_id = $this->get('author');
			}
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

// Make a global instance.
if (! isset($wp_query)) {
    $wp_query = new WP_Query();
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
					$fulltext = strtolower($comment->email.' '.$comment->url.' '.$comment->ip.' '.$comment->text);
					if( strpos( $fulltext, strtolower(trim($word)) ) != FALSE ) {
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
	var $author_structure;
	var $date_structure;
	var $front;
	var $root = '';
	var $index = 'index.php';
	var $matches = '';
	var $rules;
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

	function using_index_permalinks() {
    if (empty($this->permalink_structure)) {
			return false;
    }

    // If the index is not in the permalink, we're using mod_rewrite.
    if (preg_match('#^/*index.php#', $this->permalink_structure)) {
      return true;
    }
    
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

		$rewrite_rules = array();
		if( is_array( $uris ) )
			{
				foreach ($uris as $uri => $pagename) {
					$rewrite_rules += array($uri . '/?$' => "index.php?pagename=" . urldecode($pagename));
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

		foreach ($endians as $endian) {
			if (false !== strpos($this->permalink_structure, $endian)) {
				$this->date_structure = $this->front . $endian;
				break;
			}
		} 

		if (empty($this->date_structure)) {
			$this->date_structure = $this->front . '%year%/%monthnum%/%day%';
		}

		return $this->date_structure;
	}

	function get_year_permastruct() {
		$structure = $this->get_date_permastruct($permalink_structure);

		if (empty($structure)) {
			return false;
		}

		$structure = str_replace('%monthnum%', '', $structure);
		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	function get_month_permastruct() {
		$structure = $this->get_date_permastruct($permalink_structure);

		if (empty($structure)) {
			return false;
		}

		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	function get_day_permastruct() {
		return $this->get_date_permastruct($permalink_structure);
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

		$this->author_structure = $this->front . 'author/%author%';

		return $this->author_structure;
	}

	function add_rewrite_tag($tag, $pattern, $query) {
		$this->rewritecode[] = $tag;
		$this->rewritereplace[] = $pattern;
		$this->queryreplace[] = $query;
	}

	function generate_rewrite_rules($permalink_structure, $page = true, $feed = true, $forcomments = false, $walk_dirs = true) {
		$feedregex2 = '(feed|rdf|rss|rss2|atom)/?$';
		$feedregex = 'feed/' . $feedregex2;

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
				$post = 0;
				if (strstr($struct, '%postname%') || strstr($struct, '%post_id%')
						|| (strstr($struct, '%year%') &&  strstr($struct, '%monthnum%') && strstr($struct, '%day%') && strstr($struct, '%hour%') && strstr($struct, '%minute') && strstr($struct, '%second%'))) {
					$post = 1;
					$trackbackmatch = $match . $trackbackregex;
					$trackbackquery = $trackbackindex . '?' . $query . '&tb=1';
					$match = $match . '?([0-9]+)?/?$';
					$query = $index . '?' . $query . '&page=' . $this->preg_index($num_toks + 1);
				} else {
					$match .= '?$';
					$query = $index . '?' . $query;
				}
				        
				$rewrite = $rewrite + array($match => $query);

				if ($post) {
					$rewrite = array($trackbackmatch => $trackbackquery) + $rewrite;
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

		// Date
		$date_rewrite = $this->generate_rewrite_rules($this->get_date_permastruct());
		
		// Root
		$root_rewrite = $this->generate_rewrite_rules($this->root . '/');

		// Comments
		$comments_rewrite = $this->generate_rewrite_rules($this->root . 'comments',true, true, true);

		// Search
		$search_structure = $this->root . "search/%search%";
		$search_rewrite = $this->generate_rewrite_rules($search_structure);

		// Categories
		$category_rewrite = $this->generate_rewrite_rules($this->get_category_permastruct());

		// Authors
		$author_rewrite = $this->generate_rewrite_rules($this->get_author_permastruct());

		// Pages
		$page_rewrite = $this->page_rewrite_rules();

		// Deprecated style static pages
		$page_structure = $this->root . 'site/%pagename%';
		$old_page_rewrite = $this->generate_rewrite_rules($page_structure);

		// Put them together.
		$this->rules = $page_rewrite + $root_rewrite + $comments_rewrite + $old_page_rewrite + $search_rewrite + $category_rewrite + $author_rewrite + $date_rewrite + $post_rewrite;

		do_action('generate_rewrite_rules', '');
		$this->rules = apply_filters('rewrite_rules_array', $this->rules);
		return $this->rules;
	}

	function wp_rewrite_rules() {
		$this->matches = 'matches';
		return $this->rewrite_rules();
	}

	function mod_rewrite_rules () {
		$site_root = str_replace('http://', '', trim(get_settings('siteurl')));
		$site_root = preg_replace('|([^/]*)(.*)|i', '$2', $site_root);
		if ('/' != substr($site_root, -1)) $site_root = $site_root . '/';
    
		$home_root = str_replace('http://', '', trim(get_settings('home')));
		$home_root = preg_replace('|([^/]*)(.*)|i', '$2', $home_root);
		if ('/' != substr($home_root, -1)) $home_root = $home_root . '/';

		$rules = "<IfModule mod_rewrite.c>\n";
		$rules .= "RewriteEngine On\n";
		$rules .= "RewriteBase $home_root\n";
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

			if (strstr($query, 'index.php')) {
				$rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA,L]\n";
			} else {
				$rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA,L]\n";
			}
		}
		$rules .= "</IfModule>\n";

		$rules = apply_filters('rewrite_rules', $rules);

		return $rules;
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

// Make a global instance.
if (! isset($wp_rewrite)) {
    $wp_rewrite = new WP_Rewrite();
}

?>