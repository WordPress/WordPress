<?php

function the_permalink() {
	echo apply_filters('the_permalink', get_permalink());
}

function permalink_link() { // For backwards compatibility
	echo apply_filters('the_permalink', get_permalink());
}

function permalink_anchor($mode = 'id') {
    global $id, $post;
    switch(strtolower($mode)) {
        case 'title':
            $title = sanitize_title($post->post_title) . '-' . $id;
            echo '<a id="'.$title.'"></a>';
            break;
        case 'id':
        default:
            echo '<a id="post-'.$id.'"></a>';
            break;
    }
}

function get_permalink($id = false) {
	global $post, $wpdb;

	$rewritecode = array(
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
		'%pagename%'
	);

	if ($id) {
		$idpost = $wpdb->get_row("SELECT ID, post_date, post_name, post_status, post_author FROM $wpdb->posts WHERE ID = $id");
	} else {
		$idpost = $post;
	}

	if ($idpost->post_status == 'static') {
		return get_page_link($idpost->ID);
	}

	$permalink = get_settings('permalink_structure');

	if ('' != $permalink) {
		$unixtime = strtotime($idpost->post_date);

		$category = '';
		if (strstr($permalink, '%category%')) {
			$cats = get_the_category($idpost->ID);
			$category = $cats[0]->category_nicename;
			if ($parent=$cats[0]->category_parent) $category = get_category_parents($parent, FALSE, '/', TRUE) . $category;
		}

		$authordata = get_userdata($idpost->post_author);
		$author = $authordata->user_nicename;
		$rewritereplace = 
		array(
			date('Y', $unixtime),
			date('m', $unixtime),
			date('d', $unixtime),
			date('H', $unixtime),
			date('i', $unixtime),
			date('s', $unixtime),
			$idpost->post_name,
			$idpost->ID,
			$category,
			$author,
			$idpost->post_name,
		);
		return apply_filters('post_link', get_settings('home') . str_replace($rewritecode, $rewritereplace, $permalink), $idpost);
	} else { // if they're not using the fancy permalink option
		$permalink = get_settings('home') . '/?p=' . $idpost->ID;
		return apply_filters('post_link', $permalink, $idpost);
	}
}

function get_page_link($id = false) {
	global $post, $wp_rewrite;

	if (! $id) {
		$id = $post->ID;
	}

	$pagestruct = $wp_rewrite->get_page_permastruct();

	if ('' != $pagestruct) {
		$link = get_page_uri($id);
		$link = str_replace('%pagename%', $link, $pagestruct);
		$link = get_settings('home') . "/$link/";
	} else {
		$link = get_settings('home') . "/?page_id=$id";
	}

	return apply_filters('page_link', $link, $id);
}

function get_year_link($year) {
	global $wp_rewrite;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
		$yearlink = $wp_rewrite->get_year_permastruct();
    if (!empty($yearlink)) {
        $yearlink = str_replace('%year%', $year, $yearlink);
        return apply_filters('year_link', get_settings('home') . trailingslashit($yearlink), $year);
    } else {
        return apply_filters('year_link', get_settings('home') . '/?m=' . $year, $year);
    }
}

function get_month_link($year, $month) {
    global $wp_rewrite;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
		$monthlink = $wp_rewrite->get_month_permastruct();
    if (!empty($monthlink)) {
        $monthlink = str_replace('%year%', $year, $monthlink);
        $monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
        return apply_filters('month_link', get_settings('home') . trailingslashit($monthlink), $year, $month);
    } else {
        return apply_filters('month_link', get_settings('home') . '/?m=' . $year . zeroise($month, 2), $year, $month);
    }
}

function get_day_link($year, $month, $day) {
    global $wp_rewrite;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
    if (!$day) $day = gmdate('j', time()+(get_settings('gmt_offset') * 3600));

		$daylink = $wp_rewrite->get_day_permastruct();
    if (!empty($daylink)) {
        $daylink = str_replace('%year%', $year, $daylink);
        $daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
        $daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
        return apply_filters('day_link', get_settings('home') . trailingslashit($daylink), $year, $month, $day);
    } else {
        return apply_filters('day_link', get_settings('home') . '/?m=' . $year . zeroise($month, 2) . zeroise($day, 2), $year, $month, $day);
    }
}

function get_feed_link($feed='rss2') {
	global $wp_rewrite;
	$do_perma = 0;
	$feed_url = get_settings('siteurl');
	$comment_feed_url = $feed_url;

	$permalink = $wp_rewrite->get_feed_permastruct();
	if ('' != $permalink) {
		if ( false !== strpos($feed, 'comments_') ) {
			$feed = str_replace('comments_', '', $feed);
			$permalink = $wp_rewrite->get_comment_feed_permastruct();
		}

		if ( 'rss2' == $feed )
			$feed = '';

		$permalink = str_replace('%feed%', $feed, $permalink);
		$permalink = preg_replace('#/+#', '/', "/$permalink/");
		$output =  get_settings('home') . $permalink;
	} else {
		if ( false !== strpos($feed, 'comments_') )
			$feed = str_replace('comments_', 'comments-', $feed);

		$output = get_settings('home') . "/?feed={$feed}";
	}

	return apply_filters('feed_link', $output, $feed);
}

function edit_post_link($link = 'Edit This', $before = '', $after = '') {
    global $user_ID, $post;

    get_currentuserinfo();

	if (!user_can_edit_post($user_ID, $post->ID)) {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=edit&amp;post=$post->ID";
    echo "$before <a href=\"$location\">$link</a> $after";
}

function edit_comment_link($link = 'Edit This', $before = '', $after = '') {
    global $user_ID, $post, $comment;

    get_currentuserinfo();

	if (!user_can_edit_post_comments($user_ID, $post->ID)) {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=editcomment&amp;comment=$comment->comment_ID";
    echo "$before <a href='$location'>$link</a> $after";
}

// Navigation links

function get_previous_post($in_same_cat = false, $excluded_categories = '') {
    global $post, $wpdb;

    if(! is_single()) {
      return null;
    }
    
    $current_post_date = $post->post_date;
    $current_category = $post->post_category;
    
    $sqlcat = '';
    if ($in_same_cat) {
      $sqlcat = " AND post_category = '$current_category' ";
    }

    $sql_exclude_cats = '';
    if (!empty($excluded_categories)) {
      $blah = explode('and', $excluded_categories);
      foreach($blah as $category) {
	$category = intval($category);
	$sql_exclude_cats .= " AND post_category != $category";
      }
    }

    return @$wpdb->get_row("SELECT ID, post_title FROM $wpdb->posts WHERE post_date < '$current_post_date' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT 1");
}

function get_next_post($in_same_cat = false, $excluded_categories = '') {
    global $post, $wpdb;

    if(! is_single()) {
      return null;
    }

    $current_post_date = $post->post_date;
    $current_category = $post->post_category;
    
    $sqlcat = '';
    if ($in_same_cat) {
      $sqlcat = " AND post_category = '$current_category' ";
    }

    $sql_exclude_cats = '';
    if (!empty($excluded_categories)) {
      $blah = explode('and', $excluded_categories);
      foreach($blah as $category) {
	$category = intval($category);
	$sql_exclude_cats .= " AND post_category != $category";
      }
    }

    $now = current_time('mysql');
    
    return @$wpdb->get_row("SELECT ID,post_title FROM $wpdb->posts WHERE post_date > '$current_post_date' AND post_date < '$now' AND post_status = 'publish' $sqlcat $sql_exclude_cats AND ID != $post->ID ORDER BY post_date ASC LIMIT 1");
}

function previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
  $post = get_previous_post($in_same_cat, $excluded_categories);

  if(! $post) {
    return;
  }

  $title = apply_filters('the_title', $post->post_title, $post);

  $string = '<a href="'.get_permalink($post->ID).'">';

  $link = str_replace('%title', $title, $link);

  $link = $string . $link . '</a>';

  $format = str_replace('%link', $link, $format);

  echo $format;	    
}

function next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
  $post = get_next_post($in_same_cat, $excluded_categories);

  if(! $post) {
    return;
  }

  $title = apply_filters('the_title', $post->post_title, $post);

  $string = '<a href="'.get_permalink($post->ID).'">';

  $link = str_replace('%title', $title, $link);

  $link = $string . $link . '</a>';

  $format = str_replace('%link', $link, $format);

  echo $format;	    
}

function previous_post($format='%', $previous='previous post: ', $title='yes', $in_same_cat='no', $limitprev=1, $excluded_categories='') {
    global $id, $post, $wpdb;
    global $posts, $posts_per_page, $s;

    if(($posts_per_page == 1) || is_single()) {

        $current_post_date = $post->post_date;
        $current_category = $post->post_category;

        $sqlcat = '';
        if ($in_same_cat != 'no') {
            $sqlcat = " AND post_category = '$current_category' ";
        }

        $sql_exclude_cats = '';
        if (!empty($excluded_categories)) {
            $blah = explode('and', $excluded_categories);
            foreach($blah as $category) {
                $category = intval($category);
                $sql_exclude_cats .= " AND post_category != $category";
            }
        }

        $limitprev--;
        $lastpost = @$wpdb->get_row("SELECT ID, post_title FROM $wpdb->posts WHERE post_date < '$current_post_date' AND post_status = 'publish' $sqlcat $sql_exclude_cats ORDER BY post_date DESC LIMIT $limitprev, 1");
        if ($lastpost) {
            $string = '<a href="'.get_permalink($lastpost->ID).'">'.$previous;
            if ($title == 'yes') {
                $string .= wptexturize($lastpost->post_title);
            }
            $string .= '</a>';
            $format = str_replace('%', $string, $format);
            echo $format;
        }
    }
}

function next_post($format='%', $next='next post: ', $title='yes', $in_same_cat='no', $limitnext=1, $excluded_categories='') {
    global $posts_per_page, $post, $wpdb;
    if(1 == $posts_per_page || is_single()) {

        $current_post_date = $post->post_date;
        $current_category = $post->post_category;

        $sqlcat = '';
        if ($in_same_cat != 'no') {
            $sqlcat = " AND post_category='$current_category' ";
        }

        $sql_exclude_cats = '';
        if (!empty($excluded_categories)) {
            $blah = explode('and', $excluded_categories);
            foreach($blah as $category) {
                $category = intval($category);
                $sql_exclude_cats .= " AND post_category != $category";
            }
        }

        $now = current_time('mysql', 1);

        $limitnext--;

        $nextpost = @$wpdb->get_row("SELECT ID,post_title FROM $wpdb->posts WHERE post_date > '$current_post_date' AND post_date_gmt < '$now' AND post_status = 'publish' $sqlcat $sql_exclude_cats AND ID != $post->ID ORDER BY post_date ASC LIMIT $limitnext,1");
        if ($nextpost) {
            $string = '<a href="'.get_permalink($nextpost->ID).'">'.$next;
            if ($title=='yes') {
                $string .= wptexturize($nextpost->post_title);
            }
            $string .= '</a>';
            $format = str_replace('%', $string, $format);
            echo $format;
        }
    }
}

function get_pagenum_link($pagenum = 1){
	global $wp_rewrite;

   $qstr = $_SERVER['REQUEST_URI'];

   $page_querystring = "paged"; 
   $page_modstring = "page/";
   $page_modregex = "page/?";
   $permalink = 0;
	 $index = 'index.php';

	 $home_root = parse_url(get_settings('home'));
	 $home_root = $home_root['path'];
   $home_root = trailingslashit($home_root);
   $qstr = preg_replace('|^'. $home_root . '|', '', $qstr);
   $qstr = preg_replace('|^/+|', '', $qstr);

   // if we already have a QUERY style page string
   if( stristr( $qstr, $page_querystring ) ) {
       $replacement = "$page_querystring=$pagenum";
      $qstr = preg_replace("/".$page_querystring."[^\d]+\d+/", $replacement, $qstr);
   // if we already have a mod_rewrite style page string
   } elseif ( preg_match( '|'.$page_modregex.'\d+|', $qstr ) ){
      $permalink = 1;
      $qstr = preg_replace('|'.$page_modregex.'\d+|',"$page_modstring$pagenum",$qstr);

   // if we don't have a page string at all ...
   // lets see what sort of URL we have...
   } else {
      // we need to know the way queries are being written
      // if there's a querystring_start (a "?" usually), it's deffinitely not mod_rewritten
      if ( stristr( $qstr, '?' ) ){
         // so append the query string (using &, since we already have ?)
         $qstr .=  '&amp;' . $page_querystring . '=' . $pagenum;
         // otherwise, it could be rewritten, OR just the default index ...
      } elseif( '' != get_settings('permalink_structure')) {
         $permalink = 1;

	 // If it's not a path info permalink structure, trim the index.
	 if (! $wp_rewrite->using_index_permalinks()) {
	   $qstr = preg_replace("#/*" . $index . "/*#", '/', $qstr);
	 } else {
	   // If using path info style permalinks, make sure the index is in
	   // the URI.
	   if (strpos($qstr, $index) === false) {
	     $qstr = '/' . $index . $qstr;
	   }
	 }

	 $qstr =  trailingslashit($qstr) . $page_modstring . $pagenum;
      } else {
         $qstr = $index . '?' . $page_querystring . '=' . $pagenum;
      }
   }

   $qstr = preg_replace('|^/+|', '', $qstr);
   if ($permalink) $qstr = trailingslashit($qstr);
   return preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', trailingslashit( get_settings('home') ) . $qstr );
}

function next_posts($max_page = 0) { // original by cfactor at cooltux.org
    global $paged, $pagenow;

     if (! is_single()) {
         if (!$paged) $paged = 1;
         $nextpage = intval($paged) + 1;
         if (!$max_page || $max_page >= $nextpage) {
             echo get_pagenum_link($nextpage);
         }         
     }
}

function next_posts_link($label='Next Page &raquo;', $max_page=0) {
    global $paged, $result, $request, $posts_per_page, $wpdb;
    if (!$max_page) {
        preg_match('#FROM (.*) GROUP BY#', $request, $matches);
        $fromwhere = $matches[1];
        $numposts = $wpdb->get_var("SELECT COUNT(ID) FROM $fromwhere");
        $max_page = ceil($numposts / $posts_per_page);
    }
    if (!$paged)
        $paged = 1;
    $nextpage = intval($paged) + 1;
    if ((! is_single()) && (empty($paged) || $nextpage <= $max_page)) {
        echo '<a href="';
        next_posts($max_page);
        echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
    }
}


function previous_posts() { // original by cfactor at cooltux.org
    global $_SERVER, $paged, $pagenow;

     if (! is_single()) {
         $nextpage = intval($paged) - 1;
         if ($nextpage < 1) $nextpage = 1;
         echo get_pagenum_link($nextpage);
     }
}

function previous_posts_link($label='&laquo; Previous Page') {
    global $paged;
    if ((! is_single())  && ($paged > 1) ) {
        echo '<a href="';
        previous_posts();
        echo '">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
    }
}

function posts_nav_link($sep=' &#8212; ', $prelabel='&laquo; Previous Page', $nxtlabel='Next Page &raquo;') {
	global $request, $posts_per_page, $wpdb;
	if (! is_single()) {

		if (get_query_var('what_to_show') == 'posts') {
			preg_match('#FROM (.*) GROUP BY#', $request, $matches);
			$fromwhere = $matches[1];
			$numposts = $wpdb->get_var("SELECT COUNT(ID) FROM $fromwhere");
			$max_page = ceil($numposts / $posts_per_page);
		} else {
			$max_page = 999999;
		}

        if ($max_page > 1) {
            previous_posts_link($prelabel);
            echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
            next_posts_link($nxtlabel, $max_page);
        }
    }
}

?>