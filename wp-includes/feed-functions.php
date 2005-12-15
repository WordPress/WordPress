<?php

function get_bloginfo_rss($show = '') {
	$info = strip_tags(get_bloginfo($show));
	return convert_chars($info);
}

function bloginfo_rss($show = '') {
	echo get_bloginfo_rss($show);
}

function get_the_title_rss() {
	$title = get_the_title();
	$title = apply_filters('the_title', $title);
	$title = apply_filters('the_title_rss', $title);
	return $title;
}

function the_title_rss() {
	echo get_the_title_rss();
}

function the_content_rss($more_link_text='(more...)', $stripteaser=0, $more_file='', $cut = 0, $encode_html = 0) {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content_rss', $content);
	if ($cut && !$encode_html) {
		$encode_html = 2;
	}
	if ($encode_html == 1) {
		$content = wp_specialchars($content);
		$cut = 0;
	} elseif ($encode_html == 0) {
		$content = make_url_footnote($content);
	} elseif ($encode_html == 2) {
		$content = strip_tags($content);
	}
	if ($cut) {
		$blah = explode(' ', $content);
		if (count($blah) > $cut) {
			$k = $cut;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i].' ';
		}
		$excerpt .= ($use_dotdotdot) ? '...' : '';
		$content = $excerpt;
	}
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

function the_excerpt_rss() {
	$output = get_the_excerpt(true);
	echo apply_filters('the_excerpt_rss', $output);
}

function permalink_single_rss($file = '') {
    echo get_permalink();
}

function comment_link() {
	echo get_comment_link();
}

function get_comment_author_rss() {
	return apply_filters('comment_author_rss', get_comment_author() );
}
function comment_author_rss() {
	echo get_comment_author_rss();
}

function comment_text_rss() {
	$comment_text = get_comment_text();
	$comment_text = apply_filters('comment_text_rss', $comment_text);
	echo $comment_text;
}

function comments_rss_link($link_text = 'Comments RSS', $commentsrssfilename = '') {
	$url = comments_rss($commentsrssfilename);
	echo "<a href='$url'>$link_text</a>";
}

function comments_rss($commentsrssfilename = '') {
	global $id;

	if ('' != get_settings('permalink_structure'))
		$url = trailingslashit( get_permalink() ) . 'feed/';
	else
		$url = get_settings('home') . "/$commentsrssfilename?feed=rss2&amp;p=$id";

	return apply_filters('post_comments_feed_link', $url);
}

function get_author_rss_link($echo = false, $author_id, $author_nicename) {
       $auth_ID = $author_id;
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
				 $link = get_settings('home') . '?feed=rss2&amp;author=' . $author_id;
       } else {
				 $link = get_author_link(0, $author_id, $author_nicename);
				 $link = $link . "feed/";
       }
			 
			 $link = apply_filters('author_feed_link', $link);

       if ($echo) echo $link;
       return $link;
}

function get_category_rss_link($echo = false, $cat_ID, $category_nicename) {
       $permalink_structure = get_settings('permalink_structure');

       if ('' == $permalink_structure) {
				 $link = get_settings('home') . '?feed=rss2&amp;cat=' . $cat_ID;
       } else {
				 $link = get_category_link($cat_ID);
				 $link = $link . "feed/";
       }

			 $link = apply_filters('category_feed_link', $link);

       if ($echo) echo $link;
       return $link;
}

function the_category_rss($type = 'rss') {
    $categories = get_the_category();
    $the_list = '';
    foreach ($categories as $category) {
        $category->cat_name = convert_chars($category->cat_name);
        if ('rdf' == $type) {
            $the_list .= "\n\t<dc:subject>$category->cat_name</dc:subject>";
        } else {
            $the_list .= "\n\t<category>$category->cat_name</category>";
        }
    }
    echo apply_filters('the_category_rss', $the_list, $type);
}

function rss_enclosure() {
	global $id, $post;
	if (!empty($post->post_password) && ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password)) return;

	$custom_fields = get_post_custom();
	if( is_array( $custom_fields ) ) {
		while( list( $key, $val ) = each( $custom_fields ) ) { 
			if( $key == 'enclosure' ) {
				if (is_array($val)) {
					foreach($val as $enc) {
						$enclosure = split( "\n", $enc );
						print "<enclosure url='".trim( htmlspecialchars($enclosure[ 0 ]) )."' length='".trim( $enclosure[ 1 ] )."' type='".trim( $enclosure[ 2 ] )."'/>\n";
					}
				}
			}
		}
	}
}

?>