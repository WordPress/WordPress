<?php
function get_the_author($idmode = '') {
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
    
    return $id;
}
function the_author($idmode = '', $echo = true) {
	if ($echo) echo get_the_author($idmode);
	return get_the_author($idmode);
}

function get_the_author_description() {
    global $authordata;
    return $authordata->user_description;
}
function the_author_description() {
    echo get_the_author_description();
}

function get_the_author_login() {
    global $id,$authordata;    return $authordata->user_login;
}
function the_author_login() {
    echo get_the_author_login();
}

function get_the_author_firstname() {
    global $id,$authordata;    return $authordata->user_firstname;
}
function the_author_firstname() {
    echo get_the_author_firstname();
}

function get_the_author_lastname() {
    global $id,$authordata;    return $authordata->user_lastname;
}
function the_author_lastname() {
    echo get_the_author_lastname();
}

function get_the_author_nickname() {
    global $id,$authordata;    return $authordata->user_nickname;
}
function the_author_nickname() {
    echo get_the_author_nickname();
}

function get_the_author_ID() {
    global $id,$authordata;    return $authordata->ID;
}
function the_author_ID() {
    echo get_the_author_id();
}

function get_the_author_email() {
	global $authordata;
	return $authordata->user_email;
}

function the_author_email() {
	echo apply_filters('the_author_email', get_the_author_email() );
}

function get_the_author_url() {
    global $id,$authordata;    return $authordata->user_url;
}
function the_author_url() {
    echo get_the_author_url();
}

function get_the_author_icq() {
    global $id,$authordata;    return $authordata->user_icq;
}
function the_author_icq() {
    echo get_the_author_icq();
}

function get_the_author_aim() {
    global $id,$authordata;    return str_replace(' ', '+', $authordata->user_aim);
}
function the_author_aim() {
    echo get_the_author_aim();
}

function get_the_author_yim() {
    global $id,$authordata;    return $authordata->user_yim;
}
function the_author_yim() {
    echo get_the_author_yim();
}

function get_the_author_msn() {
    global $id,$authordata;    return $authordata->user_msn;
}
function the_author_msn() {
    echo get_the_author_msn();
}

function get_the_author_posts() {
    global $id,$post;    $posts=get_usernumposts($post->post_author);    return $posts;
}
function the_author_posts() {
    echo get_the_author_posts();
}

/* the_author_posts_link() requires no get_, use get_author_link() */
function the_author_posts_link($idmode='') {
    global $id, $authordata;

    echo '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) . '" title="' . sprintf(__("Posts by %s"), wp_specialchars(the_author($idmode, false))) . '">' . the_author($idmode, false) . '</a>';
}


function get_author_link($echo = false, $author_id, $author_nicename) {
	global $wpdb, $wp_rewrite, $post, $querystring_start, $querystring_equal, $cache_userdata;
    $auth_ID = $author_id;
    $link = $wp_rewrite->get_author_permastruct();
    
    if (empty($link)) {
        $file = get_settings('home') . '/';
        $link = $file.$querystring_start.'author'.$querystring_equal.$auth_ID;
    } else {
        if ('' == $author_nicename) $author_nicename = $cache_userdata[$author_id]->author_nicename;
				$link = str_replace('%author%', $author_nicename, $link);
				$link = get_settings('home') . trailingslashit($link);
    }

		$link = apply_filters('author_link', $link);
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
    global $wpdb;

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
            if ( !$hide_empty )
				$link = $name;
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