<?php

class WP_Query {
    var $query;
    var $query_vars;

    var $posts;
    var $post_count = 0;
    var $current_post = -1;
    var $post;

    var $is_single = false;
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
    var $is_home = false;

    function init () {
        $this->is_single = false;
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
        $this->is_home = false;

        unset($this->posts);
        unset($this->query);
        unset($this->query_vars);
        $this->post_count = 0;
        $this->current_post = -1;
    }

    function parse_query ($query) {
        $this->init();
        parse_str($query, $qv);
        $this->query = $query;
        $this->query_vars = $qv;

        if ('' != $qv['name']) {
            $this->is_single = true;
        }

        if (($qv['p'] != '') && ($qv['p'] != 'all')) {
            $this->is_single = true;            
        }

        if ('' != $qv['second']) {
            $this->is_time = true;
            $this->is_date = true;
        }

        if ('' != $qv['minute']) {
            $this->is_time = true;
            $this->is_date = true;
        }

        if ('' != $qv['hour']) {
  	    $this->is_time = true;
	    $this->is_date = true;
        }

        if ('' != $qv['day']) {
	  if (! $this->is_date) {
	    $this->is_day = true;
            $this->is_date = true;
	  }
        }

        if ('' != $qv['monthnum']) {
	  if (! $this->is_date) {
	    $this->is_month = true;
            $this->is_date = true;
	  }
        }

        if ('' != $qv['year']) {
	  if (! $this->is_date) {
	    $this->is_year = true;
            $this->is_date = true;
	  }
        }

        if ('' != $qv['m']) {
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

        // If year, month, day, hour, minute, and second are set, a single 
        // post is being queried.        
        if (('' != $qv['hour']) && ('' != $qv['minute']) &&('' != $qv['second']) && ('' != $qv['year']) && ('' != $qv['monthnum']) && ('' != $qv['day'])) {
            $this->is_single = true;
        }

        if (!empty($qv['s'])) {
            $this->is_search = true;
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
            
        // single, date, and search override category.
        if ($this->is_single || $this->is_date || $this->is_search) {
            $this->is_category = false;                
        }

        if ((empty($qv['author'])) || ($qv['author'] == 'all') || ($qv['author'] == '0')) {
            $this->is_author = false;
        } else {
            $this->is_author = true;
        }

        if ('' != $qv['author_name']) {
            $this->is_author = true;
        }

        if ('' != $qv['feed']) {
            $this->is_feed = true;
            $this->feed = $qv['feed'];
        }

        if ( ($this->is_date || $this->is_author || $this->is_category)
             && (! $this->is_single)) {
            $this->is_archive = true;
        }

        if ( ! ($this->is_archive || $this->is_single || $this->is_search || $this->is_feed)) {
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

        $add_hours = intval(get_settings('gmt_offset'));
        $add_minutes = intval(60 * (get_settings('gmt_offset') - $add_hours));
        $wp_posts_post_date_field = "post_date"; // "DATE_ADD(post_date, INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE)";

        // If a month is specified in the querystring, load that month
        if ('' != $q['m']) {
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

        if ('' != $q['hour']) {
            $q['hour'] = '' . intval($q['hour']);
            $where .= " AND HOUR(post_date)='" . $q['hour'] . "'";
        }

        if ('' != $q['minute']) {
            $q['minute'] = '' . intval($q['minute']);
            $where .= " AND MINUTE(post_date)='" . $q['minute'] . "'";
        }

        if ('' != $q['second']) {
            $q['second'] = '' . intval($q['second']);
            $where .= " AND SECOND(post_date)='" . $q['second'] . "'";
        }

        if ('' != $q['year']) {
            $q['year'] = '' . intval($q['year']);
            $where .= " AND YEAR(post_date)='" . $q['year'] . "'";
        }

        if ('' != $q['monthnum']) {
            $q['monthnum'] = '' . intval($q['monthnum']);
            $where .= " AND MONTH(post_date)='" . $q['monthnum'] . "'";
        }

        if ('' != $q['day']) {
            $q['day'] = '' . intval($q['day']);
            $where .= " AND DAYOFMONTH(post_date)='" . $q['day'] . "'";
        }

        if ('' != $q['name']) {
  	    $q['name'] = sanitize_title($q['name']);
            $where .= " AND post_name = '" . $q['name'] . "'";
        } else if ('' != $q['pagename']) {
	    // If pagename is set, set static to true and set name to pagename.
	    $q['pagename'] = sanitize_title($q['pagename']);
	    $q['name'] = $q['pagename'];
	    $q['static'] = true;
            $where .= " AND post_name = '" . $q['pagename'] . "'";
        }


        if ('' != $q['w']) {
            $q['w'] = ''.intval($q['w']);
            $where .= " AND WEEK(post_date, 1)='" . $q['w'] . "'";
        }

        // If a post number is specified, load that post
        if (($q['p'] != '') && ($q['p'] != 'all')) {
            $q['p'] = intval($q['p']);
            $where = ' AND ID = '.$q['p'];
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
	    $lastpostdate = get_lastpostdate();
	    $lastpostdate = mysql2date('Y-m-d 00:00:00',$lastpostdate);
	    $lastpostdate = mysql2date('U',$lastpostdate);
	    $startdate = date('Y-m-d H:i:s', ($lastpostdate - ((intval($page) -1) * ($q['posts_per_page']-1) * 86400)));
	    $enddate = date('Y-m-d H:i:s', ($lastpostdate - (intval($page) * ($q['posts_per_page']-1) * 86400)));
	    if ($page > 1) {
	      $where .= " AND post_date > '$enddate' AND post_date < '$startdate'";
	    } else {
	      $where .= " AND post_date > '$enddate'";
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

	if ('' != $q['static']) {
	  $where .= ' AND (post_status = "static"';
	} else {
	  $where .= ' AND (post_status = "publish"';
	}

        // Get private posts
        if (isset($user_ID) && ('' != intval($user_ID)))
            $where .= " OR post_author = $user_ID AND post_status != 'draft' AND post_status != 'static')";
        else
            $where .= ')';

        $where = apply_filters('posts_where', $where);
        $where .= " GROUP BY $wpdb->posts.ID";
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
            $this->post = $posts[0];
        }

        update_post_caches($this->posts);
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

?>
