<?php

function the_author() {
    global $id, $authordata;
    $i = $authordata->user_idmode;
    if ($i == 'nickname')    echo $authordata->user_nickname;
    if ($i == 'login')    echo $authordata->user_login;
    if ($i == 'firstname')    echo $authordata->user_firstname;
    if ($i == 'lastname')    echo $authordata->user_lastname;
    if ($i == 'namefl')    echo $authordata->user_firstname.' '.$authordata->user_lastname;
    if ($i == 'namelf')    echo $authordata->user_lastname.' '.$authordata->user_firstname;
    if (!$i) echo $authordata->user_nickname;
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
    global $id,$postdata;    $posts=get_usernumposts($post->post_author);    echo $posts;
}

function get_author_link($echo = false, $author_id, $author_nicename) {
    global $wpdb, $tableusers, $post, $querystring_start, $querystring_equal, $cache_authors;
    $auth_ID = $author_id;
    $permalink_structure = get_settings('permalink_structure');
    
    if ('' == $permalink_structure) {
        $file = get_settings('siteurl') . '/' . get_settings('blogfilename');
        $link = $file.$querystring_start.'author'.$querystring_equal.$auth_ID;
    } else {
        if ('' == $author_nicename) $author_nicename = $cache_authors[$author_id]->author_nicename;
        // Get any static stuff from the front
        $front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
        $link = get_settings('siteurl') . $front . 'author/';
        $link .= $author_nicename . '/';
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

	list_authors($r['optioncount'], $r['exclude_admin'], $r['show_fullname'], $r[hide_empty], $r['feed'], $r['feed_image']);
}

function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = '', $feed_image = '') {
    global $tableusers, $wpdb, $blogfilename;

    $query = "SELECT ID, user_nickname, user_firstname, user_lastname, user_nicename from $tableusers " . ($exclude_admin ? "WHERE user_nickname <> 'admin' " : '') . "ORDER BY user_nickname";
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
            $link = '<a href="' . get_author_link(0, $author->ID, $author->user_nicename) . '" title="' . sprintf(__("Posts by %s"), htmlspecialchars($author->user_nickname)) . '">' . stripslashes($name) . '</a>';

            if ( (! empty($feed_image)) || (! empty($feed)) ) {
                
                $link .= ' ';

                if (empty($feed_image)) {
                    $link .= '(';
                }

                $link .= '<a href="' . get_author_rss_link(0, $author->ID, $author->user_nicename)  . '"';

                if (! empty($feed)) {
                    $title =  ' title="' . stripslashes($feed) . '"';
                    $alt = ' alt="' . stripslashes($feed) . '"';
                    $name = stripslashes($feed);
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