<?php

function the_permalink() {
	echo get_permalink();
}

function permalink_link() { // For backwards compatibility
	echo get_permalink();
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

function permalink_single_rss($file = '') {
    echo get_permalink();
}

function get_permalink($id=false) {
    global $post, $wpdb;
    global $querystring_start, $querystring_equal;

    $rewritecode = array(
        '%year%',
        '%monthnum%',
        '%day%',
		'%hour%',
		'%minute%',
		'%second%',
        '%postname%',
        '%post_id%'
    );
    if (!$id) {
        if ('' != get_settings('permalink_structure')) {
	    $unixtime = strtotime($post->post_date);
            $rewritereplace = array(
                date('Y', $unixtime),
                date('m', $unixtime),
                date('d', $unixtime),
				date('H', $unixtime),
				date('i', $unixtime),
				date('s', $unixtime),
                $post->post_name,
                $post->ID
            );
            return get_settings('home') . str_replace($rewritecode, $rewritereplace, get_settings('permalink_structure'));
        } else { // if they're not using the fancy permalink option
            return get_settings('home') . '/' . get_settings('blogfilename').$querystring_start.'p'.$querystring_equal.$post->ID;
        }
    } else { // if an ID is given
        $idpost = $wpdb->get_row("SELECT post_date, post_name FROM $wpdb->posts WHERE ID = $id");
        if ('' != get_settings('permalink_structure')) {
	    $unixtime = strtotime($idpost->post_date);
            $rewritereplace = array(
                date('Y', $unixtime),
                date('m', $unixtime),
                date('d', $unixtime),
				date('H', $unixtime),
				date('i', $unixtime),
				date('s', $unixtime),
                $idpost->post_name,
                $id
            );
            return get_settings('home') . str_replace($rewritecode, $rewritereplace, get_settings('permalink_structure'));
        } else {
            return get_settings('home') . '/' . get_settings('blogfilename').$querystring_start.'p'.$querystring_equal.$id;
        }
    }
}

function get_month_link($year, $month) {
    global $querystring_start, $querystring_equal;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
    if ('' != get_settings('permalink_structure')) {
        $permalink = get_settings('permalink_structure');

        // If the permalink structure does not contain year and month, make
        // one that does.
        if (! (strstr($permalink, '%year') && strstr($permalink, '%monthnum')) ) {
            $front = substr($permalink, 0, strpos($permalink, '%'));
            $permalink = $front . '%year%/%monthnum%/';
        }

        $off = strpos($permalink, '%monthnum%');
        $offset = $off + 11;
        $monthlink = substr($permalink, 0, $offset);
        if ('/' != substr($monthlink, -1)) $monthlink = substr($monthlink, 0, -1);
        $monthlink = str_replace('%year%', $year, $monthlink);
        $monthlink = str_replace('%monthnum%', zeroise(intval($month), 2), $monthlink);
        $monthlink = str_replace('%post_id%', '', $monthlink);
        return get_settings('home') . $monthlink;
    } else {
        return get_settings('home') .'/'. get_settings('blogfilename') .$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2);
    }
}

function get_day_link($year, $month, $day) {
    global $querystring_start, $querystring_equal;
    if (!$year) $year = gmdate('Y', time()+(get_settings('gmt_offset') * 3600));
    if (!$month) $month = gmdate('m', time()+(get_settings('gmt_offset') * 3600));
    if (!$day) $day = gmdate('j', time()+(get_settings('gmt_offset') * 3600));
    if ('' != get_settings('permalink_structure')) {
        $permalink = get_settings('permalink_structure');

        // If the permalink structure does not contain year, month, and day,
        // make one that does.
        if (! (strstr($permalink, '%year') && strstr($permalink, '%monthnum')) ) {
            $front = substr($permalink, 0, strpos($permalink, '%'));
            $permalink = $front . '%year%/%monthnum%/%day%/';
        }

        $off = strpos($permalink, '%day%');
        $offset = $off + 6;
        $daylink = substr($permalink, 0, $offset);
        if ('/' != substr($daylink, -1)) $daylink = substr($daylink, 0, -1);
        $daylink = str_replace('%year%', $year, $daylink);
        $daylink = str_replace('%monthnum%', zeroise(intval($month), 2), $daylink);
        $daylink = str_replace('%day%', zeroise(intval($day), 2), $daylink);
        $daylink = str_replace('%post_id%', '', $daylink);
        return get_settings('home') . $daylink;
    } else {
        return get_settings('home') .'/'. get_settings('blogfilename') .$querystring_start.'m'.$querystring_equal.$year.zeroise($month, 2).zeroise($day, 2);
    }
}

function get_feed_link($feed='rss2') {
    $do_perma = 0;
    $feed_url = get_settings('siteurl');
    $comment_feed_url = $feed_url;

    $permalink = get_settings('permalink_structure');
    if ('' != $permalink) {
        $do_perma = 1;
        $feed_url = get_settings('home');
        $index = get_settings('blogfilename');
        $prefix = '';
        if (preg_match('#^/*' . $index . '#', $permalink)) {
            $feed_url .= '/' . $index;
        }

        $comment_feed_url = $feed_url;
        $feed_url .= '/feed';
        $comment_feed_url .= '/comments/feed';
    }

    switch($feed) {
        case 'rdf':
            $output = $feed_url .'/wp-rdf.php';
            if ($do_perma) {
                $output = $feed_url . '/rdf/';
            }
            break;
        case 'rss':
            $output = $feed_url . '/wp-rss.php';
            if ($do_perma) {
                $output = $feed_url . '/rss/';
            }
            break;
        case 'atom':
            $output = $feed_url .'/wp-atom.php';
            if ($do_perma) {
                $output = $feed_url . '/atom/';
            }
            break;        
        case 'comments_rss2':
            $output = $feed_url .'/wp-commentsrss2.php';
            if ($do_perma) {
                $output = $comment_feed_url . '/rss2/';
            }
            break;
        case 'rss2':
        default:
            $output = $feed_url .'/wp-rss2.php';
            if ($do_perma) {
                $output = $feed_url . '/rss2/';
            }
            break;
    }

    return $output;
}

function edit_post_link($link = 'Edit This', $before = '', $after = '') {
    global $user_level, $post;

    get_currentuserinfo();

    if ($user_level > 0) {
        $authordata = get_userdata($post->post_author);
        if ($user_level < $authordata->user_level) {
            return;
        }
    } else {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=edit&amp;post=$post->ID";
    echo "$before <a href=\"$location\">$link</a> $after";
}

function edit_comment_link($link = 'Edit This', $before = '', $after = '') {
    global $user_level, $post, $comment;

    get_currentuserinfo();

    if ($user_level > 0) {
        $authordata = get_userdata($post->post_author);
        if ($user_level < $authordata->user_level) {
            return;
        }
    } else {
        return;
    }

    $location = get_settings('siteurl') . "/wp-admin/post.php?action=editcomment&amp;comment=$comment->comment_ID";
    echo "$before <a href='$location'>$link</a> $after";
}

?>