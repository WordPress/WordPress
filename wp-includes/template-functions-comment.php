<?php

function comments_number($zero='No Comments', $one='1 Comment', $more='% Comments', $number='') {
    global $id, $comment, $tablecomments, $wpdb;
    if ('' == $number) $number = $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_post_ID = $id AND comment_approved = '1'");
    if ($number == 0) {
        $blah = $zero;
    } elseif ($number == 1) {
        $blah = $one;
    } elseif ($number  > 1) {
        $blah = str_replace('%', $number, $more);
    }
    echo $blah;
}

function comments_link($file='', $echo=true) {
    global $id, $pagenow;
    if ($file == '')    $file = $pagenow;
    if ($file == '/')    $file = '';
    if (!$echo) return get_permalink() . '#comments';
    else echo get_permalink() . '#comments';
}

function comments_popup_script($width=400, $height=400, $file='wp-comments-popup.php') {
    global $wpcommentspopupfile, $wptrackbackpopupfile, $wppingbackpopupfile, $wpcommentsjavascript;
    $wpcommentspopupfile = $file;
    $wpcommentsjavascript = 1;
    $javascript = "<script type='text/javascript'>\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
    echo $javascript;
}

function comments_popup_link($zero='No Comments', $one='1 Comment', $more='% Comments', $CSSclass='', $none='Comments Off') {
    global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post, $wpdb, $tablecomments, $HTTP_COOKIE_VARS, $cookiehash;
    global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
    global $comment_count_cache;
    if ('' == $comment_count_cache["$id"]) {
        $number = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $tablecomments WHERE comment_post_ID = $id AND comment_approved = '1';");
    } else {
        $number = $comment_count_cache["$id"];
    }
    if (0 == $number && 'closed' == $post->comment_status && 'closed' == $post->ping_status) {
        echo $none;
        return;
    } else {
        if (!empty($post->post_password)) { // if there's a password
            if ($HTTP_COOKIE_VARS['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
                echo("Enter your password to view comments");
                return;
            }
        }
        echo '<a href="';
        if ($wpcommentsjavascript) {
            echo $siteurl.'/'.$wpcommentspopupfile.$querystring_start.'p'.$querystring_equal.$id.$querystring_separator.'c'.$querystring_equal.'1';
            //echo get_permalink();
            echo '" onclick="wpopen(this.href); return false"';
        } else {
            // if comments_popup_script() is not in the template, display simple comment link
            comments_link();
            echo '"';
        }
        if (!empty($CSSclass)) {
            echo ' class="'.$CSSclass.'"';
        }
        echo '>';
        comments_number($zero, $one, $more, $number);
        echo '</a>';
    }
}

function comment_ID() {
    global $comment;
    echo $comment->comment_ID;
}

function comment_author() {
    global $comment;
    $author = stripslashes(stripslashes($comment->comment_author));
    $author = apply_filters('comment_auther', $author);
    $author = convert_chars($author);
    if (!empty($author)) {
        echo $comment->comment_author;
    }
    else {
        echo "Anonymous";
    }
}

function comment_author_email() {
    global $comment;
    $email = stripslashes(stripslashes($comment->comment_author_email));
    
    echo antispambot(stripslashes($comment->comment_author_email));
}

function comment_author_link() {
    global $comment;
    $url = trim(stripslashes($comment->comment_author_url));
    $email = stripslashes($comment->comment_author_email);
    $author = stripslashes($comment->comment_author);
    $author = convert_chars($author);
    $author = wptexturize($author);
    if (empty($author)) {
        $author = "Anonymous";
    }

    $url = str_replace('http://url', '', $url);
    $url = preg_replace('|[^a-z0-9-_.?#=&;,/:]|i', '', $url);
    if (empty($url) && empty($email)) {
        echo $author;
        return;
        }
    echo '<a href="';
    if ($url) {
        $url = str_replace(';//', '://', $url);
        $url = (!strstr($url, '://')) ? 'http://'.$url : $url;
        $url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
        echo $url;
    } else {
        echo 'mailto:'.antispambot($email);
    }
    echo '" rel="external">' . $author . '</a>';
}

function comment_type($commenttxt = 'Comment', $trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
    global $comment;
    if (preg_match('|<trackback />|', $comment->comment_content)) echo $trackbacktxt;
    elseif (preg_match('|<pingback />|', $comment->comment_content)) echo $pingbacktxt;
    else echo $commenttxt;
}

function comment_author_url() {
    global $comment;
    $url = trim(stripslashes($comment->comment_author_url));
    $url = str_replace(';//', '://', $url);
    $url = (!strstr($url, '://')) ? 'http://'.$url : $url;
    // convert & into &amp;
    $url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
    $url = preg_replace('|[^a-z0-9-_.,/:]|i', '', $url);
    if ($url != 'http://url') {
        echo $url;
    }
}

function comment_author_email_link($linktext='', $before='', $after='') {
    global $comment;
    $email = $comment->comment_author_email;
    if ((!empty($email)) && ($email != '@')) {
        $display = ($linktext != '') ? $linktext : antispambot(stripslashes($email));
        echo $before;
        echo '<a href="mailto:'.antispambot(stripslashes($email)).'">'.$display.'</a>';
        echo $after;
    }
}

function comment_author_url_link($linktext='', $before='', $after='') {
    global $comment;
    $url = trim(stripslashes($comment->comment_author_url));
    $url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
    $url = (!stristr($url, '://')) ? 'http://'.$url : $url;
    $url = preg_replace('|[^a-z0-9-_.,/:]|i', '', $url);
    if ((!empty($url)) && ($url != 'http://') && ($url != 'http://url')) {
        $display = ($linktext != '') ? $linktext : stripslashes($url);
        echo $before;
        echo '<a href="'.stripslashes($url).'" rel="external">'.$display.'</a>';
        echo $after;
    }
}

function comment_author_IP() {
    global $comment;
    echo stripslashes($comment->comment_author_IP);
}

function comment_text() {
    global $comment;
    $comment_text = stripslashes($comment->comment_content);
    $comment_text = str_replace('<trackback />', '', $comment_text);
    $comment_text = str_replace('<pingback />', '', $comment_text);
    $comment_text = convert_chars($comment_text);
    $comment_text = convert_bbcode($comment_text);
    $comment_text = convert_gmcode($comment_text);
    $comment_text = make_clickable($comment_text);
    $comment_text = balanceTags($comment_text,1);
    $comment_text = apply_filters('comment_text', $comment_text);
    $comment_text = convert_smilies($comment_text);
    echo $comment_text;
}

function comment_date($d='') {
    global $comment, $dateformat;
    if ($d == '') {
        echo mysql2date($dateformat, $comment->comment_date);
    } else {
        echo mysql2date($d, $comment->comment_date);
    }
}

function comment_time($d='') {
    global $comment, $timeformat;
    if ($d == '') {
        echo mysql2date($timeformat, $comment->comment_date);
    } else {
        echo mysql2date($d, $comment->comment_date);
    }
}

function comments_rss_link($link_text='Comments RSS', $commentsrssfilename = 'wp-commentsrss2.php') {
    global $id;
    global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
    $url = $siteurl.'/'.$commentsrssfilename.$querystring_start.'p'.$querystring_equal.$id;
    $url = '<a href="'.$url.'">'.$link_text.'</a>';
    echo $url;
}

function comment_author_rss() {
    global $comment;
    if (!empty($comment->comment_author)) {
        echo htmlspecialchars(strip_tags(stripslashes($comment->comment_author)));
    }
    else {
        echo "Anonymous";
    }
}

function comment_text_rss() {
    global $comment;
    $comment_text = stripslashes($comment->comment_content);
    $comment_text = str_replace('<trackback />', '', $comment_text);
    $comment_text = str_replace('<pingback />', '', $comment_text);
    $comment_text = convert_chars($comment_text);
    $comment_text = convert_bbcode($comment_text);
    $comment_text = convert_gmcode($comment_text);
    $comment_text = convert_smilies($comment_text);
    $comment_text = apply_filters('comment_text', $comment_text);
    $comment_text = strip_tags($comment_text);
    $comment_text = htmlspecialchars($comment_text);
    echo $comment_text;
}

function comment_link_rss() {
    global $comment;
    echo get_permalink($comment->comment_post_ID).'#comments';
}

function permalink_comments_rss() {
    global $comment;
    echo get_permalink($comment->comment_post_ID);
}

function trackback_url($display = true) {
    global $siteurl, $id;
    $tb_url = $siteurl.'/wp-trackback.php/'.$id;
    if ($display) {
        echo $tb_url;
    } else {
        return $tb_url;
    }
}


function trackback_rdf($timezone = 0) {
    global $siteurl, $id, $HTTP_SERVER_VARS;
    if (!stristr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], 'W3C_Validator')) {
        echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" '."\n";
        echo '    xmlns:dc="http://purl.org/dc/elements/1.1/"'."\n";
        echo '    xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">'."\n";
        echo '<rdf:Description'."\n";
        echo '    rdf:about="';
        permalink_single();
        echo '"'."\n";
        echo '    dc:identifier="';
        permalink_single();
        echo '"'."\n";
        echo '    dc:title="'.str_replace('--', '&#x2d;&#x2d;', addslashes(strip_tags(get_the_title()))).'"'."\n";
        echo '    trackback:ping="'.trackback_url(0).'"'." />\n";
        echo '</rdf:RDF>';
    }
}

?>