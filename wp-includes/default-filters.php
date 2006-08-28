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
add_filter('comment_text', 'force_balance_tags', 25);
add_filter('comment_text', 'wpautop', 30);
add_filter('comment_text', 'convert_smilies', 20);

add_filter('comment_excerpt', 'convert_chars');

// Categories
add_filter('pre_category_name', 'strip_tags');
add_filter('pre_category_name', 'trim');
add_filter('pre_category_name', 'wp_filter_kses');
add_filter('pre_category_name', 'wp_specialchars', 30);
add_filter('pre_category_description', 'wp_filter_kses');

//Links
add_filter('pre_link_name', 'strip_tags');
add_filter('pre_link_name', 'trim');
add_filter('pre_link_name', 'wp_filter_kses');
add_filter('pre_link_name', 'wp_specialchars', 30);
add_filter('pre_link_description', 'wp_filter_kses');
add_filter('pre_link_notes', 'wp_filter_kses');
add_filter('pre_link_url', 'strip_tags');
add_filter('pre_link_url', 'trim');
add_filter('pre_link_url', 'clean_url');
add_filter('pre_link_image', 'strip_tags');
add_filter('pre_link_image', 'trim');
add_filter('pre_link_image', 'clean_url');
add_filter('pre_link_rss', 'strip_tags');
add_filter('pre_link_rss', 'trim');
add_filter('pre_link_rss', 'clean_url');
add_filter('pre_link_target', 'strip_tags');
add_filter('pre_link_target', 'trim');
add_filter('pre_link_target', 'wp_filter_kses');
add_filter('pre_link_target', 'wp_specialchars', 30);
add_filter('pre_link_rel', 'strip_tags');
add_filter('pre_link_rel', 'trim');
add_filter('pre_link_rel', 'wp_filter_kses');
add_filter('pre_link_rel', 'wp_specialchars', 30);

// Users
add_filter('pre_user_display_name', 'strip_tags');
add_filter('pre_user_display_name', 'trim');
add_filter('pre_user_display_name', 'wp_filter_kses');
add_filter('pre_user_display_name', 'wp_specialchars', 30);
add_filter('pre_user_first_name', 'strip_tags');
add_filter('pre_user_first_name', 'trim');
add_filter('pre_user_first_name', 'wp_filter_kses');
add_filter('pre_user_first_name', 'wp_specialchars', 30);
add_filter('pre_user_last_name', 'strip_tags');
add_filter('pre_user_last_name', 'trim');
add_filter('pre_user_last_name', 'wp_filter_kses');
add_filter('pre_user_last_name', 'wp_specialchars', 30);
add_filter('pre_user_nickname', 'strip_tags');
add_filter('pre_user_nickname', 'trim');
add_filter('pre_user_nickname', 'wp_filter_kses');
add_filter('pre_user_nickname', 'wp_specialchars', 30);
add_filter('pre_user_description', 'trim');
add_filter('pre_user_description', 'wp_filter_kses');
add_filter('pre_user_url', 'strip_tags');
add_filter('pre_user_url', 'trim');
add_filter('pre_user_url', 'clean_url');
add_filter('pre_user_email', 'trim');
add_filter('pre_user_email', 'sanitize_email');

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
add_filter('the_title_rss', 'wp_specialchars');
add_filter('the_content_rss', 'ent2ncr', 8);
add_filter('the_excerpt_rss', 'convert_chars');
add_filter('the_excerpt_rss', 'ent2ncr', 8);
add_filter('comment_author_rss', 'ent2ncr', 8);
add_filter('comment_text_rss', 'wp_specialchars');
add_filter('comment_text_rss', 'ent2ncr', 8);
add_filter('bloginfo_rss', 'ent2ncr', 8);
add_filter('the_author', 'ent2ncr', 8);

// Misc filters
add_filter('option_ping_sites', 'privacy_ping_filter');
add_filter('option_blog_charset', 'wp_specialchars');

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
add_action('sanitize_comment_cookies', 'sanitize_comment_cookies');
add_action('admin_print_scripts', 'wp_print_scripts', 20);
?>
