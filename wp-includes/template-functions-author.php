<?php

function the_author($idmode = '', $echo = true) {
    global $authordata;
    if (empty($idmode)) {
        $idmode = $authordata->user_idmode;
    }

    if ($idmode == 'nickname')    $id = $authordata->user_nickname;
    if ($idmode == 'login')    $id = $authordata->user_login;
    if ($idmode == 'firstname')    $id = $authordata->user_firstname;
    if ($idmode == 'lastname')    $id = $authordata->user_lastname;
    if ($idmode == 'namefl')    $id = $authordata->user_firstname.' '.$authordata->user_lastname;
    if ($idmode == 'namelf')    $id = $authordata->user_lastname.' '.$authordata->user_firstname;
    if (!$idmode) $id = $authordata->user_nickname;

    if ($echo) echo $id;
    return $id;
}
function the_author_description() {
    global $authordata;
    echo $authordata->user_description;
}
function the_author_login() {
    global $id,$authordata;    echo $authordata->user_login;
}

function the_author_firstname() {
    global $id,$authordata;    echo $authordata->user_firstname;
}

function the_author_lastname() {
    global $id,$authordata;    echo $authordata->user_lastname;
}

function the_author_nickname() {
    global $id,$authordata;    echo $authordata->user_nickname;
}

function the_author_ID() {
    global $id,$authordata;    echo $authordata->ID;
}

function the_author_email() {
    global $id,$authordata;    echo antispambot($authordata->user_email);
}

function the_author_url() {
    global $id,$authordata;    echo $authordata->user_url;
}

function the_author_icq() {
    global $id,$authordata;    echo $authordata->user_icq;
}

function the_author_aim() {
    global $id,$authordata;    echo str_replace(' ', '+', $authordata->user_aim);
}

function the_author_yim() {
    global $id,$authordata;    echo $authordata->user_yim;
}

function the_author_msn() {
    global $id,$authordata;    echo $authordata->user_msn;
}

function the_author_posts() {
    global $id,$post;    $posts=get_usernumposts($post->post_author);    echo $posts;
}

function the_author_posts_link($idmode='') {
    global $id, $authordata;

    echo '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) . '" title="' . sprintf(__("Posts by %s"), wp_specialchars(the_author($idmode, false))) . '">' . the_author($idmode, false) . '</a>';
}


function get_author_link($echo = false, $author_id, $author_nicename) {
	global $wpdb, $wp_rewrite, $post, $querystring_start, $querystring_equal, $cache_userdata;
    $auth_ID = $author_id;
    $link = $wp_rewrite->get_author_permastruct();
    
    if (empty($link)) {
        $file = get_settings('home') . '/' . get_settings('blogfilename');
        $link = $file.$querystring_start.'author'.$querystring_equal.$auth_ID;
    } else {
        if ('' == $author_nicename) $author_nicename = $cache_userdata[$author_id]->author_nicename;
				$link = str_replace('%author%', $author_nicename, $link);
				$link = get_settings('home') . trailingslashit($link);
    }

    if ($echo) echo $link;
    return $link;
}

function get_author_rss_link($echo = false, $author_id, $author_nicename) {
       global $querystring_start, $querystring_equal;
       $auth_ID = $author_id;
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
           $file = get_settings('siteurl') . '/wp-rss2.php';
           $link = $file . $querystring_start . 'author' . $querystring_equal . $author_id;
       } else {
           $link = get_author_link(0, $author_id, $author_nicename);
           $link = $link . "feed/";
       }

       if ($echo) echo $link;
       return $link;
}

function wp_list_authors($args = '') {
	parse_str($args, $r);
	if (!isset($r['optioncount'])) $r['optioncount'] = false;
    if (!isset($r['exclude_admin'])) $r['exclude_admin'] = true;
    if (!isset($r['show_fullname'])) $r['show_fullname'] = false;
	if (!isset($r['hide_empty'])) $r['hide_empty'] = true;
    if (!isset($r['feed'])) $r['feed'] = '';
    if (!isset($r['feed_image'])) $r['feed_image'] = '';

	list_authors($r['optioncount'], $r['exclude_admin'], $r['show_fullname'], $r['hide_empty'], $r['feed'], $r['feed_image']);
}

function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = '', $feed_image = '') {
    global $wpdb, $blogfilename;

    $query = "SELECT ID, user_nickname, user_firstname, user_lastname, user_nicename from $wpdb->users " . ($exclude_admin ? "WHERE user_nickname <> 'admin' " : '') . "ORDER BY user_nickname";
    $authors = $wpdb->get_results($query);

    foreach($authors as $author) {
        $posts = get_usernumposts($author->ID);
        $name = $author->user_nickname;

        if ($show_fullname && ($author->user_firstname != '' && $author->user_lastname != '')) {
            $name = "$author->user_firstname $author->user_lastname";
        }
        
        if (! ($posts == 0 && $hide_empty)) echo "<li>";
        if ($posts == 0) {
            if (! $hide_empty) echo $name;
        } else {
            $link = '<a href="' . get_author_link(0, $author->ID, $author->user_nicename) . '" title="' . sprintf(__("Posts by %s"), wp_specialchars($author->user_nickname)) . '">' . $name . '</a>';

            if ( (! empty($feed_image)) || (! empty($feed)) ) {
                
                $link .= ' ';

                if (empty($feed_image)) {
                    $link .= '(';
                }

                $link .= '<a href="' . get_author_rss_link(0, $author->ID, $author->user_nicename)  . '"';

                if (! empty($feed)) {
                    $title =  ' title="' . $feed . '"';
                    $alt = ' alt="' . $feed . '"';
                    $name = $feed;
                    $link .= $title;
                }

                $link .= '>';

                if (! empty($feed_image)) {
                    $link .= "<img src=\"$feed_image\" border=\"0\"$alt$title" . ' />';
                } else {
                    $link .= $name;
                }
                
                $link .= '</a>';

                if (empty($feed_image)) {
                    $link .= ')';
                }
            }

            if ($optioncount) {
                $link .= ' ('. $posts . ')';
            }
        }

        if (! ($posts == 0 && $hide_empty)) echo "$link</li>";
    }
}

?>