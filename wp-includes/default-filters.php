<?php

// Some default filters
add_filter('bloginfo','wp_specialchars');
add_filter('category_description', 'wptexturize');
add_filter('list_cats', 'wptexturize');
add_filter('comment_author', 'wptexturize');
add_filter('comment_text', 'wptexturize');
add_filter('single_post_title', 'wptexturize');
add_filter('the_title', 'wptexturize');
add_filter('the_content', 'wptexturize');
add_filter('the_excerpt', 'wptexturize');
add_filter('bloginfo', 'wptexturize');

// Comments, trackbacks, pingbacks
add_filter('pre_comment_author_name', 'strip_tags');
add_filter('pre_comment_author_name', 'trim');
add_filter('pre_comment_author_name', 'wp_specialchars', 30);

add_filter('pre_comment_author_email', 'trim');
add_filter('pre_comment_author_email', 'sanitize_email');

add_filter('pre_comment_author_url', 'strip_tags');
add_filter('pre_comment_author_url', 'trim');
add_filter('pre_comment_author_url', 'clean_url');

add_filter('pre_comment_content', 'wp_rel_nofollow', 15);
add_filter('pre_comment_content', 'balanceTags', 30);

add_filter('pre_comment_author_name', 'wp_filter_kses');
add_filter('pre_comment_author_email', 'wp_filter_kses');
add_filter('pre_comment_author_url', 'wp_filter_kses');

// Default filters for these functions
add_filter('comment_author', 'wptexturize');
add_filter('comment_author', 'convert_chars');
add_filter('comment_author', 'wp_specialchars');

add_filter('comment_email', 'antispambot');

add_filter('comment_url', 'clean_url');

add_filter('comment_text', 'convert_chars');
add_filter('comment_text', 'make_clickable');
add_filter('comment_text', 'wpautop', 30);
add_filter('comment_text', 'convert_smilies', 20);

add_filter('comment_excerpt', 'convert_chars');

// Places to balance tags on input
add_filter('content_save_pre', 'balanceTags', 50);
add_filter('excerpt_save_pre', 'balanceTags', 50);
add_filter('comment_save_pre', 'balanceTags', 50);

// Misc. title, content, and excerpt filters
add_filter('the_title', 'convert_chars');
add_filter('the_title', 'trim');

add_filter('the_content', 'convert_smilies');
add_filter('the_content', 'convert_chars');
add_filter('the_content', 'wpautop');

add_filter('the_excerpt', 'convert_smilies');
add_filter('the_excerpt', 'convert_chars');
add_filter('the_excerpt', 'wpautop');
add_filter('get_the_excerpt', 'wp_trim_excerpt');

add_filter('sanitize_title', 'sanitize_title_with_dashes');

// RSS filters
add_filter('the_title_rss', 'strip_tags');
add_filter('the_title_rss', 'ent2ncr', 8);
add_filter('the_content_rss', 'ent2ncr', 8);
add_filter('the_excerpt_rss', 'convert_chars');
add_filter('the_excerpt_rss', 'ent2ncr', 8);
add_filter('comment_author_rss', 'ent2ncr', 8);
add_filter('comment_text_rss', 'htmlspecialchars');
add_filter('comment_text_rss', 'ent2ncr', 8);
add_filter('bloginfo_rss', 'ent2ncr', 8);
add_filter('the_author', 'ent2ncr', 8);

// Misc filters
add_filter('option_ping_sites', 'privacy_ping_filter');

// Actions
add_action('wp_head', 'rsd_link');
add_action('publish_future_post', 'wp_publish_post', 10, 1);
add_action('wp_head', 'noindex', 1);
add_action('wp_head', 'wp_print_scripts');
if(!defined('DOING_CRON'))
	add_action('init', 'wp_cron');
add_action('do_feed_rdf', 'do_feed_rdf', 10, 1);
add_action('do_feed_rss', 'do_feed_rss', 10, 1);
add_action('do_feed_rss2', 'do_feed_rss2', 10, 1);
add_action('do_feed_atom', 'do_feed_atom', 10, 1);
add_action('do_pings', 'do_all_pings', 10, 1);
add_action('do_robots', 'do_robots');
?>
