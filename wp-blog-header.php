<?php

if (!file_exists(dirname(__FILE__).'/' . 'wp-config.php'))
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='wp-admin/setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once(dirname(__FILE__).'/' . '/wp-config.php');

require_once(dirname(__FILE__).'/' . 'wp-includes/wp-l10n.php');

// Process PATH_INFO, if set.
$path_info = array();
if (! empty($_SERVER['PATH_INFO'])) {
    // Fetch the rewrite rules.
    $rewrite = rewrite_rules('matches');

    $pathinfo = $_SERVER['PATH_INFO'];
    // Trim leading '/'.
    $pathinfo = preg_replace("!^/!", '', $pathinfo);

    if (! empty($rewrite)) {
        // Get the name of the file requesting path info.
        $req_uri = $_SERVER['REQUEST_URI'];
        $req_uri = str_replace($pathinfo, '', $req_uri);
        $req_uri = preg_replace("!/+$!", '', $req_uri);
        $req_uri = explode('/', $req_uri);
        $req_uri = $req_uri[count($req_uri)-1];

        // Look for matches.
        $pathinfomatch = $pathinfo;
        foreach ($rewrite as $match => $query) {
            // If the request URI is the anchor of the match, prepend it
            // to the path info.
            if ((! empty($req_uri)) && (strpos($match, $req_uri) === 0)) {
                $pathinfomatch = $req_uri . '/' . $pathinfo;
            }

            if (preg_match("!^$match!", $pathinfomatch, $matches)) {
                // Got a match.
                // Trim the query of everything up to the '?'.
                $query = preg_replace("!^.+\?!", '', $query);

                // Substitute the substring matches into the query.
                eval("\$query = \"$query\";");

                // Parse the query.
                parse_str($query, $path_info);
                break;
            }
        }
    }    
}

$wpvarstoreset = array('m','p','posts','w', 'cat','withcomments','s','search','exact', 'sentence','poststart','postend','preview','debug', 'calendar','page','paged','more','tb', 'pb','author','order','orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'feed', 'author_name');

    for ($i=0; $i<count($wpvarstoreset); $i += 1) {
        $wpvar = $wpvarstoreset[$i];
        if (!isset($$wpvar)) {
            if (empty($_POST[$wpvar])) {
                if (empty($_GET[$wpvar]) && empty($path_info[$wpvar])) {
                    $$wpvar = '';
                } elseif (!empty($_GET[$wpvar])) {
                    $$wpvar = $_GET[$wpvar];
                } else {
                    $$wpvar = $path_info[$wpvar];
                }
            } else {
                $$wpvar = $_POST[$wpvar];
            }
        }
    }

if ($feed != '') {
    $doing_rss = 1;
}

if ($tb == 1) {
    $doing_trackback = 1;
}

// Sending HTTP headers

if (!isset($doing_rss) || !$doing_rss) {
	// It is presumptious to think that WP is the only thing that might change on the page.
	@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                 // Date in the past
	@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	@header("Cache-Control: no-store, no-cache, must-revalidate");     // HTTP/1.1
	@header("Cache-Control: post-check=0, pre-check=0", false);
	@header("Pragma: no-cache");                                     // HTTP/1.0
	@header ('X-Pingback: '. get_settings('siteurl') . '/xmlrpc.php');
} else {

	// We're showing a feed, so WP is indeed the only thing that last changed
	$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
	$wp_etag = '"'.md5($wp_last_modified).'"';
	@header('Last-Modified: '.$wp_last_modified);
	@header('ETag: '.$wp_etag);
	@header ('X-Pingback: ' . get_settings('siteurl') . '/xmlrpc.php');

	// Support for Conditional GET
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $client_last_modified = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
	else $client_last_modified = false;
	if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) $client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
	else $client_etag = false;

	if ( ($client_last_modified && $client_etag) ?
	    (($client_last_modified == $wp_last_modified) && ($client_etag == $wp_etag)) :
	    (($client_last_modified == $wp_last_modified) || ($client_etag == $wp_etag)) ) {
		if ( preg_match('/cgi/',php_sapi_name()) ) {
		    header('HTTP/1.1 304 Not Modified');
		    echo "\r\n\r\n";
		    exit;
		} else {
		    if (version_compare(phpversion(),'4.3.0','>=')) {
		        header('Not Modified', TRUE, 304);
		    } else {
		        header('HTTP/1.x 304 Not Modified');
		    }
		}
	}

}

// Getting settings from DB
if (isset($doing_rss) && $doing_rss == 1)
    $posts_per_page=get_settings('posts_per_rss');
if (!isset($posts_per_page) || $posts_per_page == 0)
    $posts_per_page = get_settings('posts_per_page');
if (!isset($what_to_show))
    $what_to_show = get_settings('what_to_show');
if (isset($showposts) && $showposts) {
    $showposts = (int)$showposts;
    $posts_per_page = $showposts;
}
$archive_mode = get_settings('archive_mode');
$use_gzipcompression = get_settings('gzipcompression');

$more_wpvars = array('posts_per_page', 'what_to_show', 'showposts');

// Construct the query string.
$query_string = '';
foreach (array_merge($wpvarstoreset, $more_wpvars) as $wpvar) {
    if ($$wpvar != '') {
        $query_string .= (strlen($query_string) < 1) ? '' : '&';
        $query_string .= $wpvar . '=' . rawurlencode($$wpvar);
    }
}

$query_string = apply_filters('query_string', $query_string);

$wp_query_state->parse_query($query_string);

// Update some caches.
update_category_cache();

// Call query posts to do the work.
$posts = query_posts($query_string);

if (1 == count($posts)) {
    if ($p || $name) {
        $more = 1;
        $single = 1;
    }
    if ($s && empty($paged)) { // If they were doing a search and got one result
        if (!strstr($_SERVER['PHP_SELF'], 'wp-admin')) // And not in admin section
            header('Location: ' . get_permalink($posts[0]->ID));
    }
}

if ($pagenow != 'wp-feed.php' && $feed != '') {
    require(dirname(__FILE__) . '/wp-feed.php');
    exit;
}

if ($pagenow != 'wp-trackback.php' && $tb == 1) {
    require(dirname(__FILE__) . '/wp-trackback.php');
    exit;
}

if ($pagenow != 'post.php' && $pagenow != 'edit.php') {
    if ( get_settings('gzipcompression') ) 
        gzip_compression();
}

?>