<?php

if (! isset($wp_did_header)):
if ( !file_exists( dirname(__FILE__) . '/wp-config.php') )
    die("There doesn't seem to be a <code>wp-config.php</code> file. I need this before we can get started. Need more help? <a href='http://wordpress.org/docs/faq/#wp-config'>We got it</a>. You can <a href='wp-admin/setup-config.php'>create a <code>wp-config.php</code> file through a web interface</a>, but this doesn't work for all server setups. The safest way is to manually create the file.");

require_once( dirname(__FILE__) . '/wp-config.php');

require_once( dirname(__FILE__) . '/wp-includes/wp-l10n.php');

$query_vars = array();

// Process PATH_INFO and 404.
if ((isset($_GET['error']) && $_GET['error'] == '404') ||
    (! empty( $_SERVER['PATH_INFO']))) {

    // Fetch the rewrite rules.
    $rewrite = rewrite_rules('matches');

    if (! empty($rewrite)) {
        $pathinfo = $_SERVER['PATH_INFO'];
	$req_uri = $_SERVER['REQUEST_URI'];      
	$home_path = parse_url(get_settings('home'));
	$home_path = $home_path['path'];

	// Trim path info from the end and the leading home path from the
	// front.  For path info requests, this leaves us with the requesting
	// filename, if any.  For 404 requests, this leaves us with the
	// requested permalink.	
        $req_uri = str_replace($pathinfo, '', $req_uri);
	$req_uri = str_replace($home_path, '', $req_uri);
	$req_uri = trim($req_uri, '/');
	$pathinfo = trim($pathinfo, '/');

	// The requested permalink is in $pathinfo for path info requests and
	//  $req_uri for other requests.
	if (! empty($pathinfo)) {
	  $request = $pathinfo;
	} else {
	  $request = $req_uri;
	}

        // Look for matches.
	$request_match = $request;
        foreach ($rewrite as $match => $query) {
            // If the requesting file is the anchor of the match, prepend it
            // to the path info.
	    if ((! empty($req_uri)) && (strpos($match, $req_uri) === 0)) {
	      $request_match = $req_uri . '/' . $request;
	    }

            if (preg_match("!^$match!", $request_match, $matches)) {
                // Got a match.
                // Trim the query of everything up to the '?'.
                $query = preg_replace("!^.+\?!", '', $query);

                // Substitute the substring matches into the query.
                eval("\$query = \"$query\";");

                // Parse the query.
                parse_str($query, $query_vars);

		// If we're processing a 404 request, clear the error var
		// since we found something.
		if (isset($_GET['error'])) {
		    unset($_GET['error']);
		}

                break;
            }
        }
    }
}

$wpvarstoreset = array('m','p','posts','w', 'cat','withcomments','s','search','exact', 'sentence','poststart','postend','preview','debug', 'calendar','page','paged','more','tb', 'pb','author','order','orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'feed', 'author_name', 'static', 'pagename', 'error');

for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST[$wpvar])) {
			if (empty($_GET[$wpvar]) && empty($query_vars[$wpvar])) {
				$$wpvar = '';
			} elseif (!empty($_GET[$wpvar])) {
				$$wpvar = $_GET[$wpvar];
			} else {
				$$wpvar = $query_vars[$wpvar];
			}
		} else {
			$$wpvar = $_POST[$wpvar];
		}
	}
}

if ('' != $feed) {
    $doing_rss = true;
}

if (1 == $tb) {
    $doing_trackback = true;
}

// Sending HTTP headers

if ( !isset($doing_rss) || !$doing_rss ) {
	@header ('X-Pingback: '. get_settings('siteurl') . '/xmlrpc.php');
} else {
	// We're showing a feed, so WP is indeed the only thing that last changed
	$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
	$wp_etag = '"' . md5($wp_last_modified) . '"';
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
			exit;
		}
	}
}

// Getting settings from DB
if ( isset($doing_rss) && $doing_rss == 1 )
    $posts_per_page = get_settings('posts_per_rss');
if ( !isset($posts_per_page) || $posts_per_page == 0 )
    $posts_per_page = get_settings('posts_per_page');
if ( !isset($what_to_show) )
    $what_to_show = get_settings('what_to_show');
if ( isset($showposts) && $showposts ) {
    $showposts = (int) $showposts;
    $posts_per_page = $showposts;
}
if ( !isset($nopaging) ) {
  $nopaging = '';
}

$archive_mode = get_settings('archive_mode');
$use_gzipcompression = get_settings('gzipcompression');

$more_wpvars = array('posts_per_page', 'what_to_show', 'showposts', 'nopaging');

// Construct the query string.
$query_string = '';
foreach (array_merge($wpvarstoreset, $more_wpvars) as $wpvar) {
	if ($$wpvar != '') {
		$query_string .= (strlen($query_string) < 1) ? '' : '&';
		$query_string .= $wpvar . '=' . rawurlencode($$wpvar);
	}
}

$query_string = apply_filters('query_string', $query_string);

update_category_cache();

// Call query posts to do the work.
$posts = query_posts($query_string);

if (1 == count($posts)) {
	if (is_single()) {
		$more = 1;
		$single = 1;
	}
	if ( $s && empty($paged) && !strstr($_SERVER['PHP_SELF'], 'wp-admin/')) { // If they were doing a search and got one result
		header('Location: ' . get_permalink($posts[0]->ID));
	}
}

$wp_did_header = true;
endif;

// Template redirection
if (is_single() && (! isset($wp_did_single)) &&
    file_exists(ABSPATH . 'wp-content/single.php')) {
  $wp_did_single = true;
  include(ABSPATH . 'wp-content/single.php');
  exit;
} else if (is_page() && (! isset($wp_did_page)) &&
	    file_exists(ABSPATH . 'wp-content/page.php')) {
  $wp_did_page = true;
  include(ABSPATH . 'wp-content/page.php');
  exit;
} else if (is_category() && (! isset($wp_did_category)) &&
	   file_exists(ABSPATH . 'wp-content/category.php')) {
  $wp_did_category = true;
  include(ABSPATH . 'wp-content/category.php');
  exit;
} else if (is_author() && (! isset($wp_did_author)) &&
	   file_exists(ABSPATH . 'wp-content/author.php')) {
  $wp_did_author = true;
  include(ABSPATH . 'wp-content/author.php');
  exit;
} else if (is_date() && (! isset($wp_did_date)) &&
	   file_exists(ABSPATH . 'wp-content/date.php')) {
  $wp_did_date = true;
  include(ABSPATH . 'wp-content/date.php');
  exit;
} else if (is_archive() && (! isset($wp_did_archive)) &&
	   file_exists(ABSPATH . 'wp-content/archive.php')) {
  $wp_did_archive = true;
  include(ABSPATH . 'wp-content/archive.php');
  exit;
} else if (is_search() && (! isset($wp_did_search)) &&
	   file_exists(ABSPATH . 'wp-content/search.php')) {
  $wp_did_search = true;
  include(ABSPATH . 'wp-content/search.php');
  exit;
} else if (is_404() && (! isset($wp_did_404)) &&
	   file_exists(ABSPATH . 'wp-content/404.php')) {
  $wp_did_404 = true;
  include(ABSPATH . 'wp-content/404.php');
  exit;
} else if (is_feed() && $pagenow != 'wp-feed.php') {
  include(dirname(__FILE__) . '/wp-feed.php');
  exit;
} else if ($pagenow != 'wp-trackback.php' && $tb == 1) {
  include(dirname(__FILE__) . '/wp-trackback.php');
  exit;
}

if ($pagenow != 'post.php' && $pagenow != 'edit.php') {
	if ( get_settings('gzipcompression') ) 
		gzip_compression();
}

?>