<?php
/*
Plugin Name: Staticize Reloaded
Version: 2.5
Plugin URI: http://www.cowpimp.com/archives/2004/06/08/staticize-plugin-for-wordpress/
Description: Automatic Generation of static files. Cuts down on php and mysql usage. Should automatically update when changes are made to site. Original by <a href="http://www.cowpimp.com">Bill Zeller</a>.
Author: Matt Mullenweg
Author URI: http://photomatt.net/
*/

$http = false; // This controls whether we send etag and last-modified headers or not
define(CACHE_PATH, trailingslashit( ABSPATH . 'wp-content/staticize-cache' ) );

update_option('gzipcompression', 0);

if ( !file_exists(CACHE_PATH) ) :
	if ( is_writable( dirname(CACHE_PATH) ) )
		$dir = mkdir( CACHE_PATH, 0777);
	else
		die("Your cache directory (<code>" . CACHE_PATH . "</code>) needs to be writable for this plugin to work. Double-check it. <a href='" . get_settings('siteurl') . "/wp-admin/plugins.php?action=deactivate&amp;plugin=staticize-reloaded.php'>Deactivate the Staticize Reloaded plugin</a>.");
endif;

// Array of files that have 'wp-' but should still be cached 
$acceptableFiles = array(
	'wp-atom.php', 'wp-comments-popup.php', 'wp-commentsrss2.php',
	'wp-links-opml.php', 'wp-locations.php', 'wp-rdf.php', 
	'wp-rss.php', 'wp-rss2.php'
);

$staticFileName = '';

function StaticizeInit() {
	global $staticFileName, $acceptableFiles, $timestart, $http;

	$key = $_SERVER['REQUEST_URI'] . join($_COOKIE, ',');
	$script = basename($_SERVER['SCRIPT_NAME']);

	if( strstr($_SERVER['SCRIPT_NAME'], 'wp-') && !in_array($script, $acceptableFiles) ) 
		return;

	$staticFileName = md5($key) . '.php';

	if( is_file(CACHE_PATH . $staticFileName) ) {

		if ($http) staticize_header();

		include(CACHE_PATH . $staticFileName);

		$timetotal = staticize_timer();

		echo "\n<!-- Static Page Served in $timetotal seconds -->";
		exit;
	}

	ob_start('StaticizeCallback'); 
	register_shutdown_function('StaticizeEnd');
}


function StaticizeCallback($buffer) {
	global $staticFileName, $timestart;

	$timetotal = staticize_timer();

	if ($timetotal < 0.05) : // Too fast to cache
		$buffer .= "\n<!-- Page served in $timetotal seconds, too fast to cache :) -->";
		return $buffer;
	endif;

	if ( strstr($buffer, 'wpdberror') )
		return $buffer;

	$fr = fopen(CACHE_PATH . $staticFileName, 'a');
	chmod(CACHE_PATH . $staticFileName, 0666);
	if (!$fr)
		$buffer = "Couldn't write to: " . CACHE_PATH . $staticFileName;

	$buffer .= "\n<!-- Dynamic Page Served (once) in $timetotal seconds -->";
	$return = $buffer;

	$buffer = preg_replace('|<!--mclude (.*?)-->(.*?)<!--/mclude-->|is', '<?php include_once("' . ABSPATH . '$1"); ?>', $buffer);
	$buffer = preg_replace('|<!--mfunc (.*?)-->(.*?)<!--/mfunc-->|is', '<?php $1 ;?>', $buffer);
	$buffer = str_replace('<?xml', '<?php echo "<?xml" ;?>', $buffer);
	fwrite($fr, $buffer);
	fclose($fr);      

	return $return;
}

function StaticizeClean() { // TODO: only touch files that have been changed using post ID
	if ( $handle = opendir( CACHE_PATH ) ) :
		while ( false !== ( $file = readdir($handle) ) ) :
		if ( '.' != $file && '..' != $file )
			unlink(CACHE_PATH . $file);
		endwhile;
	endif;
	closedir($handle);
}


function StaticizeEnd() {
	ob_end_clean();
}

function postChange($id) {
	StaticizeClean();
	return $id;
}

function staticize_timer() {
	global $timestart;
	$mtime = microtime(); 
	$mtime = explode(' ', $mtime);
	$timeend = $mtime[1] + $mtime[0];
	return number_format($timeend - $timestart, 3);
}

function staticize_header() {
	$file_touched = filemtime(CACHE_PATH . $staticFileName);
	$wp_last_modified = gmdate('D, d M Y H:i:s', $file_touched) . ' GMT';
	$wp_etag = '"'.md5($wp_last_modified).'"';
	header('Last-Modified: '.$wp_last_modified);
	header('ETag: '.$wp_etag);
	header ('X-Pingback: ' . get_settings('siteurl') . '/xmlrpc.php');
	header ('X-Staticize: ' . staticize_timer() );
	
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
			header('HTTP/1.x 304 Not Modified');
			exit;
		}
	}
}

if( function_exists('add_action') ) {
	StaticizeInit();

	add_action('publish_post', 'postChange', 0);
	add_action('edit_post', 'postChange', 0);
	add_action('delete_post', 'postChange', 0);
	add_action('comment_post', 'postChange', 0);
	add_action('trackback_post', 'postChange', 0);
	add_action('pingback_post', 'postChange', 0);
	add_action('edit_comment', 'postChange', 0);
	add_action('delete_comment', 'postChange', 0);
	add_action('template_save', 'postChange', 0);
	add_action('switch_theme', 'postChange', 0);
}

if ( isset($_GET['staticize-flush']) ) {
	StaticizeClean();
}

?>