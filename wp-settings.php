<?php
//setup the old globals from b2config.php
//
// We should eventually migrate to either calling
// get_settings() wherever these are needed OR
// accessing a single global $all_settings var
if (!$_wp_installing) {
    $siteurl = get_settings('siteurl');
    $blogfilename = get_settings('blogfilename');
    $blogname = get_settings('blogname');
    $blogdescription = get_settings('blogdescription');
    $admin_email = get_settings('admin_email');
    $new_users_can_blog = get_settings('new_users_can_blog');
    $users_can_register = get_settings('users_can_register');
    $start_of_week = get_settings('start_of_week');
    $use_preview = get_settings('use_preview');
    $use_bbcode = get_settings('use_bbcode');
    $use_gmcode = get_settings('use_gmcode');
    $use_quicktags = get_settings('use_quicktags');
    $use_htmltrans = get_settings('use_htmltrans');
    $use_balanceTags = get_settings('use_balanceTags');
    $use_fileupload = get_settings('use_fileupload');
    $fileupload_realpath = get_settings('fileupload_realpath');
    $fileupload_url = get_settings('fileupload_url');
    $fileupload_allowedtypes = get_settings('fileupload_allowedtypes');
    $fileupload_maxk = get_settings('fileupload_maxk');
    $fileupload_minlevel = get_settings('fileupload_minlevel');
    $fileupload_allowedusers = get_settings('fileupload_allowedusers');
    $posts_per_rss = get_settings('posts_per_rss');
    $rss_language = get_settings('rss_language');
    $rss_encoded_html = get_settings('rss_encoded_html');
    $rss_excerpt_length = get_settings('rss_excerpt_length');
    $rss_use_excerpt = get_settings('rss_use_excerpt');
    $use_weblogsping = get_settings('use_weblogsping');
    $use_blodotgsping = get_settings('use_blodotgsping');
    $blodotgsping_url = get_settings('blodotgsping_url');
    $use_trackback = get_settings('use_trackback');
    $use_pingback = get_settings('use_pingback');
    $require_name_email = get_settings('require_name_email');
    $comment_allowed_tags = get_settings('comment_allowed_tags');
    $comments_notify = get_settings('comments_notify');
    $use_smilies = get_settings('use_smilies');
    $smilies_directory = get_settings('smilies_directory');
    $mailserver_url = get_settings('mailserver_url');
    $mailserver_login = get_settings('mailserver_login');
    $mailserver_pass = get_settings('mailserver_pass');
    $mailserver_port = get_settings('mailserver_port');
    $default_category = get_settings('default_category');
    $subjectprefix = get_settings('subjectprefix');
    $bodyterminator = get_settings('bodyterminator');
    $emailtestonly = get_settings('emailtestonly');
    $use_phoneemail = get_settings('use_phoneemail');
    $phoneemail_separator = get_settings('phoneemail_separator');

    if (get_settings('search_engine_friendly_urls')) {
        $querystring_start = '/';
        $querystring_equal = '/';
        $querystring_separator = '/';
    } else {
        $querystring_start = '?';
        $querystring_equal = '=';
        $querystring_separator = '&amp;';
    }
} //end !$_wp_installing
?>