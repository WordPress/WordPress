<?php

if (!defined('ABSPATH')) {
    die();
}

define('W3TC', true);
define('W3TC_VERSION', '0.9.4.1');
define('W3TC_POWERED_BY', 'W3 Total Cache/' . W3TC_VERSION);
define('W3TC_EMAIL', 'w3tc@w3-edge.com');
define('W3TC_TEXT_DOMAIN', 'w3-total-cache');
define('W3TC_PAYPAL_URL', 'https://www.paypal.com/cgi-bin/webscr');
define('W3TC_PAYPAL_BUSINESS', 'w3tc-team@w3-edge.com');
define('W3TC_LINK_URL', 'http://www.w3-edge.com/wordpress-plugins/');
define('W3TC_LINK_NAME', 'W3 EDGE, Optimization Products for WordPress');
define('W3TC_FEED_URL', 'http://feeds.feedburner.com/W3TOTALCACHE');
define('W3TC_NEWS_FEED_URL', 'http://feeds.feedburner.com/W3EDGE');
define('W3TC_README_URL', 'http://plugins.svn.wordpress.org/w3-total-cache/trunk/readme.txt');
define('W3TC_SUPPORT_US_TIMEOUT', 2592000);
define('W3TC_SUPPORT_REQUEST_URL', 'https://www.w3-edge.com/w3tc/support/');
define('W3TC_TRACK_URL', 'https://www.w3-edge.com/w3tc/track/');
define('W3TC_MAILLINGLIST_SIGNUP_URL', 'https://www.w3-edge.com/w3tc/emailsignup/');
define('NEWRELIC_SIGNUP_URL', 'http://bit.ly/w3tc-partner-newrelic-signup');
define('MAXCDN_SIGNUP_URL', 'http://bit.ly/w3tc-cdn-maxcdn-create-account');
define('MAXCDN_AUTHORIZE_URL', 'http://bit.ly/w3tc-cdn-maxcdn-authorize');
define('NETDNA_AUTHORIZE_URL', 'https://cp.netdna.com/i/w3tc');
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
if (!defined('EDD_W3EDGE_STORE_URL')) define('EDD_W3EDGE_STORE_URL', 'https://www.w3-edge.com/' );
if (!defined('EDD_W3EDGE_STORE_URL_PLUGIN')) define('EDD_W3EDGE_STORE_URL_PLUGIN', 'https://www.w3-edge.com/?w3tc_buy_pro_plugin' );

// the name of your product. This should match the download name in EDD exactly
define('EDD_W3EDGE_W3TC_NAME', 'W3 Total Cache Pro: Annual Subscription');

define('W3TC_WIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

defined('W3TC_DIR') || define('W3TC_DIR', realpath(dirname(__FILE__) . '/..'));
define('W3TC_FILE', 'w3-total-cache/w3-total-cache.php');
define('W3TC_INC_DIR', W3TC_DIR . '/inc');
define('W3TC_INC_WIDGET_DIR', W3TC_INC_DIR. '/widget');
define('W3TC_INC_FUNCTIONS_DIR', W3TC_INC_DIR . '/functions');
define('W3TC_INC_OPTIONS_DIR', W3TC_INC_DIR . '/options');
define('W3TC_INC_LIGHTBOX_DIR', W3TC_INC_DIR . '/lightbox');
define('W3TC_INC_POPUP_DIR', W3TC_INC_DIR . '/popup');
define('W3TC_LIB_DIR', W3TC_DIR . '/lib');
define('W3TC_LIB_W3_DIR', W3TC_LIB_DIR . '/W3');
define('W3TC_LIB_MINIFY_DIR', W3TC_LIB_DIR . '/Minify');
define('W3TC_LIB_CF_DIR', W3TC_LIB_DIR . '/CF');
define('W3TC_LIB_CSSTIDY_DIR', W3TC_LIB_DIR . '/CSSTidy');
define('W3TC_LIB_MICROSOFT_DIR', W3TC_LIB_DIR . '/Microsoft');
define('W3TC_LIB_NUSOAP_DIR', W3TC_LIB_DIR . '/Nusoap');
define('W3TC_LIB_NETDNA_DIR', W3TC_LIB_DIR . '/NetDNA');
define('W3TC_LIB_OAUTH_DIR', W3TC_LIB_DIR . '/OAuth');
define('W3TC_LIB_NEWRELIC_DIR', W3TC_LIB_DIR . '/NewRelic');
define('W3TC_PLUGINS_DIR', W3TC_DIR . '/plugins');
define('W3TC_INSTALL_DIR', W3TC_DIR . '/wp-content');
define('W3TC_INSTALL_MINIFY_DIR', W3TC_INSTALL_DIR . '/w3tc/min');
define('W3TC_LANGUAGES_DIR', W3TC_DIR . '/languages');

define('W3TC_CACHE_DIR', WP_CONTENT_DIR . '/cache');
define('W3TC_CONFIG_DIR', WP_CONTENT_DIR . '/w3tc-config');
define('W3TC_CACHE_CONFIG_DIR', W3TC_CACHE_DIR  . '/config');
define('W3TC_CACHE_MINIFY_DIR', W3TC_CACHE_DIR  . '/minify');
define('W3TC_CACHE_PAGE_ENHANCED_DIR', W3TC_CACHE_DIR  . '/page_enhanced');
define('W3TC_CACHE_TMP_DIR', W3TC_CACHE_DIR . '/tmp');
define('W3TC_CACHE_BLOGMAP_FILENAME', W3TC_CACHE_DIR . '/blogs.php');

defined('WP_CONTENT_DIR') || define('WP_CONTENT_DIR', realpath(W3TC_DIR . '/../..'));

define('W3TC_CDN_COMMAND_UPLOAD', 1);
define('W3TC_CDN_COMMAND_DELETE', 2);
define('W3TC_CDN_COMMAND_PURGE', 3);
define('W3TC_CDN_TABLE_QUEUE', 'w3tc_cdn_queue');

define('W3TC_INSTALL_FILE_ADVANCED_CACHE', W3TC_INSTALL_DIR . '/advanced-cache.php');
define('W3TC_INSTALL_FILE_DB', W3TC_INSTALL_DIR . '/db.php');
define('W3TC_INSTALL_FILE_OBJECT_CACHE', W3TC_INSTALL_DIR . '/object-cache.php');

define('W3TC_ADDIN_FILE_ADVANCED_CACHE', WP_CONTENT_DIR . '/advanced-cache.php');
define('W3TC_ADDIN_FILE_DB', WP_CONTENT_DIR . '/db.php');
define('W3TC_FILE_DB_CLUSTER_CONFIG', WP_CONTENT_DIR . '/db-cluster-config.php');
define('W3TC_ADDIN_FILE_OBJECT_CACHE', WP_CONTENT_DIR . '/object-cache.php');


define('W3TC_WP_LOADER', (defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins') . '/w3tc-wp-loader.php');
if (!defined('W3TC_EXTENSION_DIR'))
    define('W3TC_EXTENSION_DIR', (defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins'));
define('W3TC_CORE_EXTENSION_DIR', W3TC_DIR . '/extensions');
w3_require_once(W3TC_INC_DIR . '/functions/compat.php');
w3_require_once(W3TC_INC_DIR . '/functions/plugin.php');

@ini_set('pcre.backtrack_limit', 4194304);
@ini_set('pcre.recursion_limit', 4194304);

global $w3_late_init;
$w3_late_init = false;
/**
 * Returns current microtime
 *
 * @return double
 */
function w3_microtime() {
    list ($usec, $sec) = explode(' ', microtime());

    return ((double) $usec + (double) $sec);
}

/**
 * Check if content is HTML or XML
 *
 * @param string $content
 * @return boolean
 */
function w3_is_xml($content) {
    if (strlen($content) > 1000) {
        $content = substr($content, 0, 1000);
    }

    if (strstr($content, '<!--') !== false) {
        $content = preg_replace('~<!--.*?-->~s', '', $content);
    }

    $content = ltrim($content, "\x00\x09\x0A\x0D\x20\xBB\xBF\xEF");

    return (stripos($content, '<?xml') === 0 || stripos($content, '<html') === 0 || stripos($content, '<!DOCTYPE') === 0);
}

/**
 * If content can handle HTML comments, can disable printout per request using filter 'w3tc_can_print_comment'
 * @param $buffer
 * @return bool
 */
function w3_can_print_comment(&$buffer) {
    if (function_exists('apply_filters'))
        return apply_filters('w3tc_can_print_comment', w3_is_xml($buffer) && !defined('DOING_AJAX'));
    return w3_is_xml($buffer) && !defined('DOING_AJAX');
}

/*
 * Returns URI from filename/dirname
 *
 * @return string
 */
function w3_filename_to_url($filename, $use_site_url = false) {
    // using wp-content instead of document_root as known dir since dirbased
    // multisite wp adds blogname to the path inside site_url
    if (substr($filename, 0, strlen(WP_CONTENT_DIR)) != WP_CONTENT_DIR)
        return '';
    $uri_from_wp_content = substr($filename, strlen(WP_CONTENT_DIR));

    if ($use_site_url)
        $site_url_ssl = w3_get_url_ssl(w3_get_site_url());
    else
        $site_url_ssl = w3_get_url_ssl(w3_get_home_url());

    $dir = '';
    if (substr(trailingslashit(WP_CONTENT_DIR), 0, strlen(trailingslashit(w3_get_site_root()))) == trailingslashit(w3_get_site_root())) {
        if ($use_site_url || w3_get_domain(w3_get_home_url()) == w3_get_domain(w3_get_site_url()))
            $dir = str_replace($site_url_ssl, '', w3_get_url_ssl(w3_get_site_url()));
        else
            $dir = str_replace($site_url_ssl, '', w3_get_url_ssl(w3_get_home_url()));
        $dir = trim($dir, '/\\');
        if ($dir)
            $dir = '/' . $dir;
        $content_path = trim(substr(trailingslashit(WP_CONTENT_DIR), strlen(trailingslashit(w3_get_site_root()))), '/\\');
    }
    else
        $content_path = trim(substr(trailingslashit(WP_CONTENT_DIR), strlen(trailingslashit(w3_get_document_root()))), '/\\');

    $url = $site_url_ssl . $dir . '/' . $content_path . $uri_from_wp_content;

    return $url;
}

/**
 * Returns true if database cluster is used
 *
 * @return boolean
 */
function w3_is_dbcluster() {
    return defined('W3TC_FILE_DB_CLUSTER_CONFIG') && @file_exists(W3TC_FILE_DB_CLUSTER_CONFIG)
        && defined('W3TC_ENTERPRISE') && W3TC_ENTERPRISE;
}

/**
 * Returns true if it's WPMU
 *
 * @return boolean
 */
function w3_is_wpmu() {
    static $wpmu = null;

    if ($wpmu === null) {
        $wpmu = file_exists(ABSPATH . 'wpmu-settings.php');
    }

    return $wpmu;
}

/**
 * Returns true if WPMU uses vhosts
 *
 * @return boolean
 */
function w3_is_subdomain_install() {
    return ((defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) || (defined('VHOST') && VHOST == 'yes'));
}

/**
 * Returns true if it's WP with enabled Network mode
 *
 * @return boolean
 */
function w3_is_multisite() {
    static $multisite = null;

    if ($multisite === null) {
        $multisite = ((defined('MULTISITE') && MULTISITE) || defined('SUNRISE') || w3_is_subdomain_install());
    }

    return $multisite;
}

/**
 * Returns if there is multisite mode
 *
 * @return boolean
 */
function w3_is_network() {
    return (w3_is_wpmu() || w3_is_multisite());
}

/**
 * Check if URL is valid
 *
 * @param string $url
 * @return boolean
 */
function w3_is_url($url) {
    return preg_match('~^(https?:)?//~', $url);
}

/**
 * Returns true if current connection is secure
 *
 * @return boolean
 */
function w3_is_https() {
    switch (true) {
        case (isset($_SERVER['HTTPS']) && w3_to_boolean($_SERVER['HTTPS'])):
        case (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] == 443):
            return true;
    }

    return false;
}

/**
 * Check if there was database error
 *
 * @param string $content
 * @return boolean
 */
function w3_is_database_error(&$content) {
    return (stristr($content, '<title>Database Error</title>') !== false);
}

/**
 * Retuns true if preview settings active
 *
 * @return boolean
 */
function w3_is_preview_mode() {
    return (isset($_COOKIE['w3tc_preview']) && $_COOKIE['w3tc_preview'] == true) || (isset($_REQUEST['w3tc_preview']) ||
        (isset($_SERVER['HTTP_REFERER']) &&
            strstr($_SERVER['HTTP_REFERER'], 'w3tc_preview') !== false));
}

/**
 * Returns a preview link with current state
 * @return string
 */
function w3tc_get_preview_link() {
    w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
    return w3_is_preview_mode() ? w3_button_link(__('Stop Previewing', 'w3-total-cache'), wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_default_stop_previewing'), 'w3tc'), false) : w3_button_link(__('Preview', 'w3-total-cache'), wp_nonce_url(w3_admin_url('admin.php?page=w3tc_dashboard&w3tc_default_previewing'), 'w3tc'), true);
}

/**
 * Returns true if server is Apache
 *
 * @return boolean
 */
function w3_is_apache() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false);
}

/**
 * Check whether server is LiteSpeed
 *
 * @return bool
 */
function w3_is_litespeed() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
}

/**
 * Returns true if server is nginx
 *
 * @return boolean
 */
function w3_is_nginx() {
    return (isset($_SERVER['SERVER_SOFTWARE']) && stristr($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
}

/**
 * Check whether $engine is correct CDN engine
 *
 * @param string $engine
 * @return boolean
 */
function w3_is_cdn_engine($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'mirror', 'netdna', 'maxcdn',
                                'cotendo', 'akamai', 'edgecast', 'att'));
}

/**
 * Returns true if CDN engine is mirror
 *
 * @param string $engine
 * @return bool
 */
function w3_is_cdn_mirror($engine) {
    return in_array($engine, array('mirror', 'netdna', 'maxcdn', 'cotendo', 'cf2', 'akamai', 'edgecast', 'att'));
}

/**
 * Returns true if CDN has purge all support
 * @param $engine
 * @return bool
 */
function w3_cdn_can_purge_all($engine) {
    return in_array($engine, array('cotendo', 'edgecast', 'att', 'netdna', 'maxcdn'));
}

/**
 * Returns domain from host
 *
 * @param string $host
 * @return string
 */
function w3_get_domain($host) {
    $host = strtolower($host);

    if (($pos = strpos($host, ':')) !== false) {
        $host = substr($host, $pos+3);
    }
    if (($pos = strpos($host, '/')) !== false) {
        $host = substr($host, 0, $pos);
    }

    $host = rtrim($host, '.');

    return $host;
}

/**
 * Returns array of all available blognames
 *
 * @return array
 */
function w3_get_blognames() {
    global $wpdb;

    $blognames = array();

    $sql = sprintf('SELECT domain, path FROM %s', $wpdb->blogs);
    $blogs = $wpdb->get_results($sql);

    if ($blogs) {
        $base_path = w3_get_base_path();

        foreach ($blogs as $blog) {
            $blogname = trim(str_replace($base_path, '', $blog->path), '/');

            if ($blogname) {
                $blognames[] = $blogname;
            }
        }
    }

    return $blognames;
}

/**
 * Returns current blog ID
 *
 * @return integer
 */
function w3_get_blog_id() {
    global $w3_current_blog_id;
    
    if (!is_null($w3_current_blog_id))
        return $w3_current_blog_id;
    
    if (!w3_is_network() || is_network_admin()) {
        $w3_current_blog_id = 0;
        return $w3_current_blog_id;
    }

    
    $blog_data = w3_blogmap_get_blog_data();
    if (!is_null($blog_data))
        $w3_current_blog_id = substr($blog_data, 1);
    else
        $w3_current_blog_id = 0;

    return $w3_current_blog_id;
}

/**
 * Returns blogmap filename by home url
 *
 * @param string $blog_home_url
 * @return string
 */
function w3_blogmap_filename($blog_home_url) {
    if (!defined('W3TC_BLOG_LEVELS'))
        return W3TC_CACHE_BLOGMAP_FILENAME;
    else {
        $filename = dirname(W3TC_CACHE_BLOGMAP_FILENAME) . '/' .
            basename(W3TC_CACHE_BLOGMAP_FILENAME, '.php') . '/';

        $s = md5($blog_home_url);
        for ($n = 0; $n < W3TC_BLOG_LEVELS; $n++)
            $filename .= substr($s, $n, 1) . '/';

        return $filename . basename(W3TC_CACHE_BLOGMAP_FILENAME);
    }
}

/**
 * Returns blog_id by home url
 * If database not initialized yet - returns 0
 *
 * @return integer
 */
function w3_blogmap_get_blog_data() {
    $host = w3_get_host();

    // subdomain
    if (w3_is_subdomain_install()) {
        $blog_data = w3_blogmap_try_get_blog_data($host);
        if (is_null($blog_data))
            $GLOBALS['w3tc_blogmap_register_new_item'] = $host;

        return $blog_data;
    } else {
        // try subdir blog
        $url = $host . $_SERVER['REQUEST_URI'];
        $pos = strpos($url, '?');
        if ($pos !== false)
            $url = substr($url, 0, $pos);

        $url = rtrim($url, '/');
        $start_url = $url;

        for (;;) {
            $blog_data = w3_blogmap_try_get_blog_data($url);
            if (!is_null($blog_data))
                return $blog_data;
            $pos = strrpos($url, '/');
            if ($pos === false)
                break;

            $url = rtrim(substr($url, 0, $pos), '/');
        }

        $GLOBALS['w3tc_blogmap_register_new_item'] = $start_url;
        return null;
    }
}



function w3_blogmap_try_get_blog_data($url) {
    $filename = w3_blogmap_filename($url);

    if (file_exists($filename)) {
        $data = file_get_contents($filename);
        $blog_data = @eval($data);

        if (is_array($blog_data) && isset($blog_data[$url]))
            return $blog_data[$url];
    }
    return null;
}

/**
 * @return bool
 */
function w3_force_master() {
    global $w3_force_master;
    if (!is_null($w3_force_master))
        return $w3_force_master;

    if (!w3_is_multisite())
        $w3_force_master = false;
    else {
        $blog_data = w3_blogmap_get_blog_data();
        if (is_null($blog_data) || 
            ($blog_data[0] != 'm' && $blog_data[0] != 'c'))
            $w3_force_master = true;
        else
            $w3_force_master = ($blog_data[0] == 'm');
    }

    return $w3_force_master;
}

/**
 * Returns path to section's cache dir
 *
 * @param string $section
 * @return string
 */
function w3_cache_dir($section) {
    return W3TC_CACHE_DIR . '/' . $section;
}

/**
 * Returns path to blog's cache dir
 *
 * @param string $section
 * @param null|int $blog_id
 * @return string
 */
function w3_cache_blog_dir($section, $blog_id = null) {
    if (is_null($blog_id))
        $blog_id = w3_get_blog_id();

    $postfix = sprintf('%06d', $blog_id);

    if (defined('W3TC_BLOG_LEVELS')) {
        for ($n = 0; $n < W3TC_BLOG_LEVELS; $n++)
            $postfix = substr($postfix, strlen($postfix) - 1 - $n, 1) . '/' .
                $postfix;
    }

    return w3_cache_dir($section) . '/' . $postfix;
}

/**
 * Return full path to log file for module
 * Path used in priority
 * 1) W3TC_DEBUG_DIR
 * 2) WP_DEBUG_LOG
 * 3) W3TC_CACHE_DIR
 *
 * @param $module
 * @param null $blog_id
 * @return string
 */
function w3_debug_log($module, $blog_id = null)  {
    if (is_null($blog_id))
        $blog_id = w3_get_blog_id();

    $postfix = sprintf('%06d', $blog_id);

    if (defined('W3TC_BLOG_LEVELS')) {
        for ($n = 0; $n < W3TC_BLOG_LEVELS; $n++)
            $postfix = substr($postfix, strlen($postfix) - 1 - $n, 1) . '/' .
                $postfix;
    }
    $from_dir = W3TC_CACHE_DIR;
    if (defined('W3TC_DEBUG_DIR') && W3TC_DEBUG_DIR) {
        $dir_path = W3TC_DEBUG_DIR;
        if (!is_dir(W3TC_DEBUG_DIR))
            $from_dir = dirname(W3TC_DEBUG_DIR);
    } else
        $dir_path = w3_cache_dir('log');
    $filename = $dir_path . '/' . $postfix . '/' . $module . '.log';
    if (!is_dir(dirname($filename))) {
        w3_require_once( W3TC_INC_DIR . '/functions/file.php');
        w3_mkdir_from(dirname($filename), $from_dir);
    }

    return $filename;
}

/**
 * Returns URL regexp from URL
 *
 * @param string $url
 * @return string
 */
function w3_get_url_regexp($url) {
    $url = preg_replace('~(https?:)?//~i', '', $url);
    $url = preg_replace('~^www\.~i', '', $url);

    $regexp = '(https?:)?//(www\.)?' . w3_preg_quote($url);

    return $regexp;
}

/**
 * Returns SSL URL if current connection is https
 * @param string $url
 * @return string
 */
function w3_get_url_ssl($url) {
    if (w3_is_https()) {
        $url = str_replace('http://', 'https://', $url);
    }

    return $url;
}

/**
 * Get domain URL
 *
 * @return string
 */

function w3_get_domain_url() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['scheme']) && isset($parse_url['host'])) {
        $scheme = $parse_url['scheme'];
        $host = $parse_url['host'];
        $port = (isset($parse_url['port']) && $parse_url['port'] != 80 ? ':' . (int) $parse_url['port'] : '');
        $domain_url = sprintf('%s://%s%s', $scheme, $host, $port);

        return $domain_url;
    }

    return false;
}

/**
 * Returns domain url regexp
 *
 * @return string
 */
function w3_get_domain_url_regexp() {
    $domain_url = w3_get_domain_url();
    $regexp = w3_get_url_regexp($domain_url);

    return $regexp;
}

/**
 * Returns home URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_url() {
    static $home_url = null;
    
    if ($home_url === null) {
        $config = w3_instance('W3_Config');
        if (w3_is_multisite() && $config->get_boolean('common.force_master')) {
            $home_url = get_home_url();
        } else {
            // get_option is unusable here, it can cause problem when objCache isn't yet initialized
            // Which is why we save the 'home' option in our ConfigCache
            // We don't just use $config->get_string, because we want the cache to rebuild
            // when 'wordpress.home' is not (yet) present
            $home_url = $config->get_cache_option('wordpress.home');
            $home_url = rtrim($home_url, '/');
        }
    }

    return $home_url;
}

/**
 * Returns SSL home url
 *
 * @return string
 */
function w3_get_home_url_ssl() {
    $home_url = w3_get_home_url();
    $ssl = w3_get_url_ssl($home_url);

    return $ssl;
}

/**
 * Returns home url regexp
 *
 * @return string
 */
function w3_get_home_url_regexp() {
    $home_url = w3_get_home_url();
    $regexp = w3_get_url_regexp($home_url);

    return $regexp;
}

/**
 * Network installs returns wrong wp site path
 * @return string
 */
function w3_get_wp_sitepath() {
    if (w3_is_network()) {
        global $current_site;
        return $current_site->path;
    } else {
        return w3_get_site_path();
    }
}

/**
 * Returns site URL
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_url() {
    static $site_url = null;

    if ($site_url === null) {
        $site_url = get_option('siteurl');
        $site_url = rtrim($site_url, '/');
    }

    return $site_url;
}

/**
 * Returns SSL site URL
 *
 * @return string
 */
function w3_get_site_url_ssl() {
    $site_url = w3_get_site_url();
    $ssl = w3_get_url_ssl($site_url);

    return $ssl;

}

/**
 * Returns absolute path to document root
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_document_root() {
    static $document_root = null;

    if ($document_root === null) {
        if (!empty($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == $_SERVER['PHP_SELF']) {
            $document_root = w3_get_site_root();
        } elseif (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $document_root = substr(w3_path($_SERVER['SCRIPT_FILENAME']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['PATH_TRANSLATED'])) {
            $document_root = substr(w3_path($_SERVER['PATH_TRANSLATED']), 0, -strlen(w3_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = w3_path($_SERVER['DOCUMENT_ROOT']);
        } else {
            $document_root = w3_get_site_root();
        }

        $document_root = realpath($document_root);
        $document_root = w3_path($document_root);
    }

    return $document_root;
}

/**
 * Returns absolute path to home directory
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * Install dir=/var/www/vhosts/domain.com/site/blog
 * home=http://domain.com/site
 * siteurl=http://domain.com/site/blog
 * return /var/www/vhosts/domain.com/site
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_home_root() {
    if (w3_is_network()) {
        $path = w3_get_base_path();
    } else {
        $path = w3_get_home_path();
    }

    $home_root = w3_get_document_root() . $path;
    $home_root = realpath($home_root);
    $home_root = w3_path($home_root);

    return $home_root;
}

/**
 * Returns absolute path to blog install dir
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com
 * install dir=/var/www/vhosts/domain.com/site/blog
 * return /var/www/vhosts/domain.com/site/blog
 *
 * No trailing slash!
 *
 * @return string
 */
function w3_get_site_root() {
    $site_root = ABSPATH;
    $site_root = realpath($site_root);
    $site_root = w3_path($site_root);

    return $site_root;
}

/**
 * Returns blog path
 *
 * Example:
 *
 * siteurl=http://domain.com/site/blog
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_site_path() {
    $site_url = w3_get_site_url();
    $parse_url = @parse_url($site_url);

    if ($parse_url && isset($parse_url['path'])) {
        $site_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $site_path = '/';
    }

    if (substr($site_path, -1) != '/') {
        $site_path .= '/';
    }

    return $site_path;
}

/**
 * Returns home domain
 *
 * @return string
 */
function w3_get_home_domain() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['host'])) {
        return $parse_url['host'];
    }

    return w3_get_host();
}

/**
 * Returns home path
 *
 * Example:
 *
 * home=http://domain.com/site/
 * siteurl=http://domain.com/site/blog
 * return /site/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_home_path() {
    $home_url = w3_get_home_url();
    $parse_url = @parse_url($home_url);

    if ($parse_url && isset($parse_url['path'])) {
        $home_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $home_path = '/';
    }

    if (substr($home_path, -1) != '/') {
        $home_path .= '/';
    }

    return $home_path;
}

/**
 * Returns path to WP directory relative to document root
 *
 * Example:
 *
 * DOCUMENT_ROOT=/var/www/vhosts/domain.com/
 * Install dir=/var/www/vhosts/domain.com/site/blog/
 * return /site/blog/
 *
 * With trailing slash!
 *
 * @return string
 */
function w3_get_base_path() {
    $document_root = w3_get_document_root();
    $site_root = w3_get_site_root();

    $base_path = str_replace($document_root, '', $site_root);
    $base_path = '/' . ltrim($base_path, '/');

    if (substr($base_path, -1) != '/') {
        $base_path .= '/';
    }

    return $base_path;
}

/**
 * Returns server hostname
 *
 * @return string
 */
function w3_get_host() {
    static $host = null;

    if ($host === null) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            // HTTP_HOST sometimes is not set causing warning
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = '';
        }
    }

    return $host;
}

/**
 * Returns host ID
 *
 * @return string
 */
function w3_get_host_id() {
    static $host_id = null;

    if ($host_id === null) {
        $host = w3_get_host();
        $blog_id = w3_get_blog_id();

        $host_id = sprintf('%s_%d', $host, $blog_id);
    }

    return $host_id;
}

/**
 * Returns WP config file path
 *
 * @return string
 */
function w3_get_wp_config_path() {
    $search = array(
        ABSPATH . 'wp-config.php',
        dirname(ABSPATH) . '/wp-config.php'
    );

    foreach ($search as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    return false;
}

/**
 * Returns theme key
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key($theme_root, $template, $stylesheet) {
    $site_root = w3_get_site_root();
    $theme_path = ltrim(str_replace($site_root, '', w3_path($theme_root)), '/');

    return substr(md5($theme_path . $template . $stylesheet), 0, 5);
}

/**
 * Returns theme key (legacy support)
 *
 * @param string $theme_root
 * @param string $template
 * @param string $stylesheet
 * @return string
 */
function w3_get_theme_key_legacy($theme_root, $template, $stylesheet) {
    return substr(md5($theme_root . $template . $stylesheet), 0, 6);
}

/**
 * Returns true if we can check rules
 *
 * @return bool
 */
function w3_can_check_rules() {
    return (w3_is_apache() || w3_is_litespeed() || w3_is_nginx());
}

/**
 * Returns true if CDN engine is supporting purge
 *
 * @param string $engine
 * @return bool
 */
function w3_can_cdn_purge($engine) {
    return in_array($engine, array('ftp', 's3', 'cf', 'cf2', 'rscf', 'azure', 'netdna', 'maxcdn', 'cotendo',
                                   'edgecast', 'akamai', 'att'));
}

/**
 * Returns true if CDN supports realtime purge. That is purging on post changes, comments etc.
 * @param $engine
 * @return bool
 */
function w3tc_cdn_supports_realtime_purge($engine) {
    return !in_array($engine, array('cf2'));
}

/**
 * Parses path
 *
 * @param string $path
 * @return mixed
 */
function w3_parse_path($path) {
    $path = str_replace(array(
        '%BLOG_ID%',
        '%POST_ID%',
        '%BLOG_ID%',
        '%HOST%',
        '%DOMAIN%',
        '%BASE_PATH%'
    ), array(
        (isset($GLOBALS['blog_id']) ? (int) $GLOBALS['blog_id'] : 0),
        (isset($GLOBALS['post_id']) ? (int) $GLOBALS['post_id'] : 0),
        w3_get_blog_id(),
        w3_get_host(),
        w3_get_domain(w3_get_host()),
        trim(w3_get_base_path(), '/')
    ), $path);

    return $path;
}

/**
 * Normalizes file name
 *
 * Relative to site root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $home_url_regexp = '~' . w3_get_home_url_regexp() . '~i';
            $file = preg_replace($home_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_site_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify($file) {
    if (w3_is_url($file)) {
        if (strstr($file, '?') === false) {
            $domain_url_regexp = '~' . w3_get_domain_url_regexp() . '~i';
            $file = preg_replace($domain_url_regexp, '', $file);
        }
    }

    if (!w3_is_url($file)) {
        $file = w3_path($file);
        $file = str_replace(w3_get_document_root(), '', $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Normalizes file name for minify
 *
 * Relative to document root!
 *
 * @param string $file
 * @return string
 */
function w3_normalize_file_minify2($file) {
    $file = w3_remove_query($file);
    $file = w3_normalize_file_minify($file);
    $file = w3_translate_file($file);

    return $file;
}

/**
 * Translates remote file to local file
 *
 * @param string $file
 * @return string
 */
function w3_translate_file($file) {
    if (!w3_is_url($file)) {
        $file = '/' . ltrim($file, '/');
        $regexp = '~^' . w3_preg_quote(w3_get_site_path()) . '~';
        $file = preg_replace($regexp, w3_get_base_path(), $file);
        $file = ltrim($file, '/');
    }

    return $file;
}

/**
 * Remove WP query string from URL
 *
 * @param string $url
 * @return string
 */
function w3_remove_query($url) {
    $url = preg_replace('~[&\?]+(ver=([a-z0-9-_\.]+|[0-9-]+))~i', '', $url);

    return $url;
}

/**
 * Converts win path to unix
 *
 * @param string $path
 * @return string
 */
function w3_path($path) {
    $path = preg_replace('~[/\\\]+~', '/', $path);
    $path = rtrim($path, '/');

    return $path;
}

/**
 * Returns real path of given path
 *
 * @param string $path
 * @return string
 */
function w3_realpath($path) {
    $path = w3_path($path);
    $parts = explode('/', $path);
    $absolutes = array();

    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }

    return implode('/', $absolutes);
}

/**
 * Returns GMT date
 * @param integer $time
 * @return string
 */
function w3_http_date($time) {
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
}

/**
 * Redirects to URL
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function w3_redirect($url = '', $params = array()) {
    w3_require_once(W3TC_INC_DIR . '/functions/url.php');

    $url = w3_url_format($url, $params);
    if (function_exists('do_action'))
        do_action('w3_redirect');

    @header('Location: ' . $url);
    exit();
}

/**
 * Redirects to URL
 *
 * @param string $url
 * @param array  $params
 *
 * @return string
 */
function w3_redirect_temp( $url = '', $params = array() ) {
	w3_require_once( W3TC_INC_DIR . '/functions/url.php' );

	$url = w3_url_format( $url, $params );
    if (function_exists('do_action'))
        do_action( 'w3_redirect' );

	$status_code = 301;

	$protocol = $_SERVER["SERVER_PROTOCOL"];
	if ( 'HTTP/1.1' === $protocol ) {
		$status_code = 307;
	}

	$text = get_status_header_desc( $status_code );
	if ( !empty( $text ) ) {
		$status_header = "$protocol $status_code $text";
		@header( $status_header, true, $status_code );
	}
	@header( 'Location: ' . $url, true, $status_code );
	exit();
}

/**
 * Returns caching engine name
 *
 * @param $engine
 * @param $module
 *
 * @return string
 */
function w3_get_engine_name($engine, $module = '') {
    switch ($engine) {
        case 'memcached':
            $engine_name = 'memcached';
            break;

        case 'apc':
            $engine_name = 'apc';
            break;

        case 'eaccelerator':
            $engine_name = 'eaccelerator';
            break;

        case 'xcache':
            $engine_name = 'xcache';
            break;

        case 'wincache':
            $engine_name = 'wincache';
            break;

        case 'file':
            if ($module == 'pgcache')
                $engine_name = 'disk: basic';
            else
                $engine_name = 'disk';
            break;

        case 'file_generic':
            $engine_name = 'disk: enhanced';
            break;

        case 'ftp':
            $engine_name = 'self-hosted / file transfer protocol upload';
            break;

        case 's3':
            $engine_name = 'amazon simple storage service (s3)';
            break;

        case 'cf':
            $engine_name = 'amazon cloudfront';
            break;

        case 'cf2':
            $engine_name = 'amazon cloudfront';
            break;

        case 'rscf':
            $engine_name = 'rackspace cloud files';
            break;

        case 'azure':
            $engine_name = 'microsoft azure storage';
            break;

        case 'mirror':
            $engine_name = 'mirror';
            break;

        case 'netdna':
            $engine_name = 'netdna';
            break;

        case 'maxcdn':
            $engine_name = 'maxcdn';
            break;

        case 'cotendo':
            $engine_name = 'cotendo';
            break;

        case 'akamai':
            $engine_name = 'akamai';
            break;

        case 'edgecast':
            $engine_name = 'media template procdn / edgecast';
            break;

        case 'att':
            $engine_name = 'at&amp;t';
            break;

        default:
            $engine_name = 'n/a';
            break;
    }

    return $engine_name;
}

/**
 * Converts value to boolean
 *
 * @param mixed $value
 * @return boolean
 */
function w3_to_boolean($value) {
    if (is_string($value)) {
        switch (strtolower($value)) {
            case '+':
            case '1':
            case 'y':
            case 'on':
            case 'yes':
            case 'true':
            case 'enabled':
                return true;

            case '-':
            case '0':
            case 'n':
            case 'no':
            case 'off':
            case 'false':
            case 'disabled':
                return false;
        }
    }

    return (boolean) $value;
}

/**
 * Quotes regular expression string
 *
 * @param string $string
 * @param string $delimiter
 * @return string
 */
function w3_preg_quote($string, $delimiter = null) {
    $string = preg_quote($string, $delimiter);
    $string = strtr($string, array(
        ' ' => '\ '
    ));

    return $string;
}

/**
 * Returns true if zlib output compression is enabled otherwise false
 *
 * @return boolean
 */
function w3_zlib_output_compression() {
    return w3_to_boolean(ini_get('zlib.output_compression'));
}

/**
 * Recursive strips slahes from the var
 *
 * @param mixed $var
 * @return mixed
 */
function w3_stripslashes($var) {
    if (is_string($var)) {
        return stripslashes($var);
    } elseif (is_array($var)) {
        $var = array_map('w3_stripslashes', $var);
    }

    return $var;
}

/**
 * Escapes HTML comment
 *
 * @param string $comment
 * @return mixed
 */
function w3_escape_comment($comment) {
    while (strstr($comment, '--') !== false) {
        $comment = str_replace('--', '- -', $comment);
    }

    return $comment;
}

/**
 * Returns instance of singleton class
 *
 * @param string $class
 * @return object
 */
function w3_instance($class) {
    static $instances = array();

    if (!isset($instances[$class])) {
        w3_require_once( W3TC_LIB_W3_DIR . '/' .
                str_replace('_', '/', substr($class, 3)) . '.php');
        $instances[$class] = new $class();
    }

    $v = $instances[$class];   // Don't return reference
    return $v;
}

/**
 * Requires and keeps track of which files has already been loaded.
 *
 * @param string $path Absolute path to the file
 */
function w3_require_once($path) {
    static $files = array();

    if (!isset($files[$path])) {
        $files[$path] = 1;
        require_once $path;
    }
}

/**
 * Detects post ID
 *
 * @return integer
 */
function w3_detect_post_id() {
    global $posts, $comment_post_ID, $post_ID;

    if ($post_ID) {
        return $post_ID;
    } elseif ($comment_post_ID) {
        return $comment_post_ID;
    } elseif (is_single() || is_page() && count($posts)) {
        return $posts[0]->ID;
    } elseif (isset($_REQUEST['p'])) {
        return (integer) $_REQUEST['p'];
    }

    return 0;
}

function w3_get_instance_id() {
    static $instance_id;

    if(!isset($instance_id)) {
        $config = w3_instance('W3_Config');
        $instance_id = $config->get_integer('common.instance_id', 0);
    }
    return $instance_id;
}


/**
 * Checks if post should be flushed or not. Returns true if it should not be flushed
 * @param $post
 * @param string $module which cache module to check against (pgcache, varnish, cdncache, dbcache or objectcache)
 * @param W3_Config $config
 * @return bool
 */
function w3_is_flushable_post($post, $module, $config) {
    if (is_numeric($post))
        $post = get_post($post);
    $post_status = array('publish');
    // dont flush when we have post "attachment"
    // its child of the post and is flushed always when post is published, while not changed in fact
    $post_type = array('revision', 'attachment');
    switch($module) {
        case 'pgcache':
        case 'varnish':
        case 'cdncache':
            if (!$config->get_boolean('pgcache.reject.logged'))
                $post_status[] = 'private';
        break;
        case 'dbcache':
            if (!$config->get_boolean('dbcache.reject.logged'))
                $post_status[] = 'private';
            break;
    }
    $flushable = !in_array($post->post_type, $post_type) && in_array($post->post_status, $post_status);
    return apply_filters('w3tc_flushable_post', $flushable, $post, $module);
}

/**
 * Takes seconds and converts to array('Nh ','Nm ', 'Ns ', 'Nms ') or "Nh Nm Ns Nms"
 * @param $input
 * @param bool $string
 * @return array|string
 */
function w3_convert_secs_to_time($input, $string = true) {
    $input = (double)$input;
    $time = array();
    $msecs = floor($input*1000 % 1000);
    $seconds = $input % 60;
    $input = floor($input / 60);
    $minutes = $input % 60;
    $input = floor($input / 60);
    $hours = $input % 60;
    if ($hours)
        $time[] = sprintf(__('%dh', 'w3-total-cache'), $hours);
    if ($minutes)
        $time[] = sprintf(__('%dm', 'w3-total-cache'), $minutes);
    if ($seconds)
        $time[] = sprintf(__('%ds', 'w3-total-cache'), $seconds);
    if ($msecs)
        $time[] = sprintf(__('%dms', 'w3-total-cache'), $msecs);

    if(empty($time))
        $time[] = sprintf(__('%dms', 'w3-total-cache'), 0);
    if ($string)
        return implode(' ', $time);
    return $time;
}

/**
 * @var W3_Config $config
 * @return string
 */
function w3_w3tc_release_version($config = null) {
    if (w3_is_enterprise($config))
        return 'enterprise';
    if (w3_is_pro($config) &&  w3tc_is_pro_dev_mode())
        return 'pro development';
    if (w3_is_pro($config))
        return 'pro';
    return 'community';
}

/**
 * @param W3_Config $config
 * @return bool
 */
function w3_is_pro($config = null) {
    $result = false;
    if ($config)
        $result = $config->get_string('plugin.type') == 'pro' ||
            ($config->get_string('plugin.type') == 'pro_dev' );
    return $result || (defined('W3TC_PRO') && W3TC_PRO);
}

/**
 * Enable Pro Dev mode support
 * @return bool
 */
function w3tc_is_pro_dev_mode() {
    return defined('W3TC_PRO_DEV_MODE') && W3TC_PRO_DEV_MODE;
}

/**
 * @param W3_Config $config
 * @return bool
 */
function w3_is_enterprise($config = null) {
    $result = false;
    if ($config)
        $result = $config->get_string('plugin.type') == 'enterprise';
    return $result || (defined('W3TC_ENTERPRISE') && W3TC_ENTERPRISE);
}

/**
 * Checks if site is using edge mode.
 * @return bool
 */
function w3tc_edge_mode() {
    return defined('W3TC_EDGE_MODE') && W3TC_EDGE_MODE;
}
