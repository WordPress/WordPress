<?php
$HTTP_HOST = getenv('HTTP_HOST');  /* domain name */
$REMOTE_ADDR = getenv('REMOTE_ADDR'); /* visitor's IP */
$HTTP_USER_AGENT = getenv('HTTP_USER_AGENT'); /* visitor's browser */

// Fix for IIS, which doesn't set REQUEST_URI
$_SERVER['REQUEST_URI'] = ( isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')));

// Change to E_ALL for development/debugging
error_reporting(E_ALL ^ E_NOTICE);

// Table names
$tableposts               = $table_prefix . 'posts';
$tableusers               = $table_prefix . 'users';
$tablesettings            = $table_prefix . 'settings'; // only used during upgrade
$tablecategories          = $table_prefix . 'categories';
$tablepost2cat            = $table_prefix . 'post2cat';
$tablecomments            = $table_prefix . 'comments';
$tablelinks               = $table_prefix . 'links';
$tablelinkcategories      = $table_prefix . 'linkcategories';
$tableoptions             = $table_prefix . 'options';
$tableoptiontypes         = $table_prefix . 'optiontypes';
$tableoptionvalues        = $table_prefix . 'optionvalues';
$tableoptiongroups        = $table_prefix . 'optiongroups';
$tableoptiongroup_options = $table_prefix . 'optiongroup_options';
define('WPINC', 'wp-includes');
require_once (ABSPATH . WPINC . '/wp-db.php');

$wpdb->hide_errors();
if (!$wpdb->get_row("SELECT * FROM $tableusers LIMIT 1") && !strstr($_SERVER['REQUEST_URI'], 'install.php')) {
	die("It doesn't look like you've installed WP yet. Try running <a href='wp-admin/install.php'>install.php</a>.");
}
$wpdb->show_errors();

// This is the name of the include directory. No "/" allowed.

require_once (ABSPATH . WPINC . '/functions.php');
require_once (ABSPATH . 'wp-config-extra.php');
require_once (ABSPATH . WPINC . '/template-functions.php');
require_once (ABSPATH . WPINC . '/class-xmlrpc.php');
require_once (ABSPATH . WPINC . '/class-xmlrpcs.php');
require_once (ABSPATH . WPINC . '/links.php');
require_once (ABSPATH . WPINC . '/kses.php');

//setup the old globals from b2config.php
//
// We should eventually migrate to either calling
// get_settings() wherever these are needed OR
// accessing a single global $all_settings var
if (!strstr($_SERVER['REQUEST_URI'], 'install.php') && !strstr($_SERVER['REQUEST_URI'], 'wp-admin/import')) {
    $blogname = get_settings('blogname');
    $blogdescription = get_settings('blogdescription');
    $admin_email = get_settings('admin_email');
    $new_users_can_blog = get_settings('new_users_can_blog');
    $users_can_register = get_settings('users_can_register');
    $blog_charset = get_settings('blog_charset');
    $start_of_week = get_settings('start_of_week');
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
    $use_default_geourl = get_settings('use_default_geourl');
    $default_geourl_lat = get_settings('default_geourl_lat');
    $default_geourl_lon = get_settings('default_geourl_lon');

    $querystring_start = '?';
    $querystring_equal = '=';
    $querystring_separator = '&amp;';
    //}
    // Used to guarantee unique cookies
    $cookiehash = md5(get_settings('siteurl'));

} //end !$_wp_installing

require_once (ABSPATH . WPINC . '/vars.php');


// Check for hacks file if the option is enabled
if (get_settings('hack_file')) {
	if (file_exists(ABSPATH . '/my-hacks.php'))
		require(ABSPATH . '/my-hacks.php');
}



?>