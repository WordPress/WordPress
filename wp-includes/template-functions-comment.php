<?php

// Default filters for these functions
add_filter('comment_author', 'remove_slashes', 5);
add_filter('comment_author', 'wptexturize');
add_filter('comment_author', 'convert_chars');

add_filter('comment_email', 'remove_slashes', 5);
add_filter('comment_email', 'antispambot');

add_filter('comment_url', 'clean_url');

add_filter('comment_text', 'remove_slashes', 5);
add_filter('comment_text', 'convert_chars');
add_filter('comment_text', 'make_clickable');
add_filter('comment_text', 'wpautop', 30);
add_filter('comment_text', 'balanceTags');
add_filter('comment_text', 'convert_smilies', 20);

add_filter('comment_excerpt', 'remove_slashes', 5);
add_filter('comment_excerpt', 'convert_chars');

function clean_url($url) {
	$url = str_replace('http://url', '', $url);
	$url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $url);
	$url = str_replace(';//', '://', $url);
	$url = (!strstr($url, '://')) ? 'http://'.$url : $url;
	$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
	return $url;
}

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
    $javascript = "<script type=\"text/javascript\">\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
    echo $javascript;
}

function comments_popup_link($zero='No Comments', $one='1 Comment', $more='% Comments', $CSSclass='', $none='Comments Off') {
    global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post, $wpdb, $tablecomments, $HTTP_COOKIE_VARS, $cookiehash;
    global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;
    global $comment_count_cache, $single;
	if (!$single) {
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
                echo('Enter your password to view comments');
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
}

function comment_ID() {
	global $comment;
	echo $comment->comment_ID;
}

function comment_author() {
	global $comment;
	$author = apply_filters('comment_author', $comment->comment_author);
	if (empty($author)) {
		echo 'Anonymous';
	} else {
		echo $author;
	}
}

function comment_author_email() {
	global $comment;
	echo apply_filters('author_email', $comment->comment_author_email);
}

function comment_author_link() {
	global $comment;
	$url = apply_filters('comment_url', $comment->comment_author_url);
	$email = apply_filters('comment_email', $comment->comment_author_email);
	$author = apply_filters('comment_author', $comment->comment_author);

	if (empty($url) && empty($email)) {
		echo 'Anonymous';
		return;
	}

	echo '<a href="';
	if ($url) {
		echo $url;
	} else {
		echo "mailto:$email";
	}
	echo '" rel="external">' . $author . '</a>';
}

function comment_type($commenttxt = 'Comment', $trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
	global $comment;
	if (preg_match('|<trackback />|', $comment->comment_content))
		echo $trackbacktxt;
	elseif (preg_match('|<pingback />|', $comment->comment_content))
		echo $pingbacktxt;
	else
		echo $commenttxt;
}

function comment_author_url() {
	global $comment;
	echo apply_filters('comment_url', $comment->comment_author_url);
}

function comment_author_email_link($linktext='', $before='', $after='') {
	global $comment;
	$email = apply_filters('comment_email', $comment->comment_author_email);
	if ((!empty($email)) && ($email != '@')) {
	$display = ($linktext != '') ? $linktext : stripslashes($email);
		echo $before;
		echo "<a href='mailto:$email'>$display</a>";
		echo $after;
	}
}

function comment_author_url_link($linktext='', $before='', $after='') {
	global $comment;
	$url = apply_filters('comment_url', $comment->comment_author_url);

	if ((!empty($url)) && ($url != 'http://') && ($url != 'http://url')) {
	$display = ($linktext != '') ? $linktext : stripslashes($url);
		echo "$before<a href='$url' rel='external'>$display</a>$after";
	}
}

function comment_author_IP() {
	global $comment;
	echo $comment->comment_author_IP;
}

function comment_text() {
	global $comment;
	$comment_text = str_replace('<trackback />', '', $comment->comment_content);
	$comment_text = str_replace('<pingback />', '', $comment_text);
	echo apply_filters('comment_text', $comment_text);
}

function comment_excerpt() {
	global $comment;
	$comment_text = str_replace('<trackback />', '', $comment->comment_content);
	$comment_text = str_replace('<pingback />', '', $comment_text);
	$comment_text = strip_tags($comment_text);
	$blah = explode(' ', $comment_text);
	if (count($blah) > 20) {
		$k = 20;
		$use_dotdotdot = 1;
	} else {
		$k = count($blah);
		$use_dotdotdot = 0;
	}
	$excerpt = '';
	for ($i=0; $i<$k; $i++) {
		$excerpt .= $blah[$i] . ' ';
	}
	$excerpt .= ($use_dotdotdot) ? '...' : '';
	echo apply_filters('comment_excerpt', $excerpt);
}

function comment_date($d='') {
	global $comment;
	if ('' == $d) {
		echo mysql2date(get_settings('date_format'), get_date_from_gmt($comment->comment_date));
	} else {
		echo mysql2date($d, get_date_from_gmt($comment->comment_date));
	}
}

function comment_time($d='') {
	global $comment;
	if ($d == '') {
		echo mysql2date(get_settings('time_format'), get_date_from_gmt($comment->comment_date));
	} else {
		echo mysql2date($d, get_date_from_gmt($comment->comment_date));
	}
}

function comments_rss_link($link_text='Comments RSS', $commentsrssfilename = 'wp-commentsrss2.php') {
	global $id;
	global $querystring_start, $querystring_equal, $querystring_separator, $siteurl;

	if ('' != get_settings('permalink_structure')) {
		$url = trailingslashit(get_permalink()) . 'rss2/';
	} else {
		$url = $siteurl.'/'.$commentsrssfilename.$querystring_start.'p'.$querystring_equal.$id;
	}

	echo "<a href='$url'>$link_text</a>";
}

function comment_author_rss() {
	global $comment;
	if (empty($comment->comment_author)) {
		echo 'Anonymous';
	} else {
		echo htmlspecialchars(apply_filters('comment_author', $comment->comment_author));
	}
}

function comment_text_rss() {
	global $comment;
	$comment_text = str_replace('<trackback />', '', $comment->comment_content);
	$comment_text = str_replace('<pingback />', '', $comment_text);
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
	global $id;
	$tb_url = get_settings('siteurl') . '/wp-trackback.php/' . $id;
	
	if ('' != get_settings('permalink_structure')) {
		$tb_url = trailingslashit(get_permalink()) . 'trackback/';
	}
	
	if ($display) {
		echo $tb_url;
	} else {
		return $tb_url;
	}
}


function trackback_rdf($timezone = 0) {
	global $siteurl, $id, $HTTP_SERVER_VARS;
	if (!stristr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], 'W3C_Validator')) {
	echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
	    xmlns:dc="http://purl.org/dc/elements/1.1/"
	    xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
		<rdf:Description rdf:about="';
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