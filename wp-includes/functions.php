<?php

require_once(dirname(__FILE__).'/compat.php');

function mysql2date($dateformatstring, $mysqlstring, $translate = true) {
	global $wp_locale;
	$m = $mysqlstring;
	if ( empty($m) ) {
		return false;
	}
	$i = mktime(substr($m,11,2),substr($m,14,2),substr($m,17,2),substr($m,5,2),substr($m,8,2),substr($m,0,4));

	if( 'U' == $dateformatstring )
		return $i;
	
	if ( -1 == $i || false == $i )
		$i = 0;

	if ( !empty($wp_locale->month) && !empty($wp_locale->weekday) && $translate ) {
		$datemonth = $wp_locale->get_month(date('m', $i));
		$datemonth_abbrev = $wp_locale->get_month_abbrev($datemonth);
		$dateweekday = $wp_locale->get_weekday(date('w', $i));
		$dateweekday_abbrev = $wp_locale->get_weekday_abbrev($dateweekday);
		$datemeridiem = $wp_locale->get_meridiem(date('a', $i));
		$datemeridiem_capital = $wp_locale->get_meridiem(date('A', $i));
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit($dateweekday_abbrev), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit($datemonth_abbrev), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])a/", "\\1".backslashit($datemeridiem), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])A/", "\\1".backslashit($datemeridiem_capital), $dateformatstring);

		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $i);
	if ( !$j ) {
	// for debug purposes
	//	echo $i." ".$mysqlstring;
	}
	return $j;
}

function current_time($type, $gmt = 0) {
	switch ($type) {
		case 'mysql':
			if ( $gmt ) $d = gmdate('Y-m-d H:i:s');
			else $d = gmdate('Y-m-d H:i:s', (time() + (get_option('gmt_offset') * 3600)));
			return $d;
			break;
		case 'timestamp':
			if ( $gmt ) $d = time();
			else $d = time() + (get_option('gmt_offset') * 3600);
			return $d;
			break;
	}
}

function date_i18n($dateformatstring, $unixtimestamp) {
	global $wp_locale;
	$i = $unixtimestamp;
	if ( (!empty($wp_locale->month)) && (!empty($wp_locale->weekday)) ) {
		$datemonth = $wp_locale->get_month(date('m', $i));
		$datemonth_abbrev = $wp_locale->get_month_abbrev($datemonth);
		$dateweekday = $wp_locale->get_weekday(date('w', $i));
		$dateweekday_abbrev = $wp_locale->get_weekday_abbrev($dateweekday);
		$datemeridiem = $wp_locale->get_meridiem(date('a', $i));
		$datemeridiem_capital = $wp_locale->get_meridiem(date('A', $i));
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit($dateweekday_abbrev), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit($datemonth_abbrev), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])a/", "\\1".backslashit($datemeridiem), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])A/", "\\1".backslashit($datemeridiem_capital), $dateformatstring);

		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $i);
	return $j;
}

function get_weekstartend($mysqlstring, $start_of_week) {
	$my = substr($mysqlstring,0,4);
	$mm = substr($mysqlstring,8,2);
	$md = substr($mysqlstring,5,2);
	$day = mktime(0,0,0, $md, $mm, $my);
	$weekday = date('w',$day);
	$i = 86400;

	if ( $weekday < get_option('start_of_week') )
		$weekday = 7 - (get_option('start_of_week') - $weekday);

	while ($weekday > get_option('start_of_week')) {
		$weekday = date('w',$day);
		if ( $weekday < get_option('start_of_week') )
			$weekday = 7 - (get_option('start_of_week') - $weekday);

		$day = $day - 86400;
		$i = 0;
	}
	$week['start'] = $day + 86400 - $i;
	// $week['end'] = $day - $i + 691199;
	$week['end'] = $week['start'] + 604799;
	return $week;
}

function get_lastpostdate($timezone = 'server') {
	global $cache_lastpostdate, $pagenow, $wpdb;
	$add_seconds_blog = get_option('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	if ( !isset($cache_lastpostdate[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostdate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostdate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostdate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
		}
		$cache_lastpostdate[$timezone] = $lastpostdate;
	} else {
		$lastpostdate = $cache_lastpostdate[$timezone];
	}
	return $lastpostdate;
}

function get_lastpostmodified($timezone = 'server') {
	global $cache_lastpostmodified, $pagenow, $wpdb;
	$add_seconds_blog = get_option('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	if ( !isset($cache_lastpostmodified[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostmodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
		}
		$lastpostdate = get_lastpostdate($timezone);
		if ( $lastpostdate > $lastpostmodified ) {
			$lastpostmodified = $lastpostdate;
		}
		$cache_lastpostmodified[$timezone] = $lastpostmodified;
	} else {
		$lastpostmodified = $cache_lastpostmodified[$timezone];
	}
	return $lastpostmodified;
}

function maybe_unserialize($original) {
	if ( false !== $gm = @ unserialize($original) )
		return $gm;
	else
		return $original;
}

/* Options functions */

function get_option($setting) {
	global $wpdb;

	$value = wp_cache_get($setting, 'options');

	if ( false === $value ) {
		if ( defined('WP_INSTALLING') )
			$wpdb->hide_errors();
		$row = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = '$setting' LIMIT 1");
		if ( defined('WP_INSTALLING') )
			$wpdb->show_errors();

		if( is_object( $row) ) { // Has to be get_row instead of get_var because of funkiness with 0, false, null values
			$value = $row->option_value;
			wp_cache_set($setting, $value, 'options');
		} else {
			return false;
		}
	}

	// If home is not set use siteurl.
	if ( 'home' == $setting && '' == $value )
		return get_option('siteurl');

	if ( 'siteurl' == $setting || 'home' == $setting || 'category_base' == $setting )
		$value = preg_replace('|/+$|', '', $value);

	return apply_filters( 'option_' . $setting, maybe_unserialize($value) );
}

function form_option($option) {
	echo wp_specialchars( get_option($option), 1 );
}

function get_alloptions() {
	global $wpdb, $wp_queries;
	$wpdb->hide_errors();
	if ( !$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'") ) {
		$options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options");
	}
	$wpdb->show_errors();

	foreach ($options as $option) {
		// "When trying to design a foolproof system,
		//  never underestimate the ingenuity of the fools :)" -- Dougal
		if ( 'siteurl' == $option->option_name )
			$option->option_value = preg_replace('|/+$|', '', $option->option_value);
		if ( 'home' == $option->option_name )
			$option->option_value = preg_replace('|/+$|', '', $option->option_value);
		if ( 'category_base' == $option->option_name )
			$option->option_value = preg_replace('|/+$|', '', $option->option_value);
		$value = maybe_unserialize($option->option_value);
		$all_options->{$option->option_name} = apply_filters('pre_option_' . $option->option_name, $value);
	}
	return apply_filters('all_options', $all_options);
}

function update_option($option_name, $newvalue) {
	global $wpdb;

	if ( is_string($newvalue) )
		$newvalue = trim($newvalue);

	// If the new and old values are the same, no need to update.
	$oldvalue = get_option($option_name);
	if ( $newvalue == $oldvalue ) {
		return false;
	}

	if ( false === $oldvalue ) {
		add_option($option_name, $newvalue);
		return true;
	}

	$_newvalue = $newvalue;
	if ( is_array($newvalue) || is_object($newvalue) )
		$newvalue = serialize($newvalue);

	wp_cache_set($option_name, $newvalue, 'options');

	$newvalue = $wpdb->escape($newvalue);
	$option_name = $wpdb->escape($option_name);
	$wpdb->query("UPDATE $wpdb->options SET option_value = '$newvalue' WHERE option_name = '$option_name'");
	if ( $wpdb->rows_affected == 1 ) {
		do_action("update_option_{$option_name}", array('old'=>$oldvalue, 'new'=>$_newvalue));
		return true;
	}
	return false;
}

// thx Alex Stapleton, http://alex.vort-x.net/blog/
function add_option($name, $value = '', $description = '', $autoload = 'yes') {
	global $wpdb;

	// Make sure the option doesn't already exist
	if ( false !== get_option($name) )
		return;

	if ( is_array($value) || is_object($value) )
		$value = serialize($value);

	wp_cache_set($name, $value, 'options');

	$name = $wpdb->escape($name);
	$value = $wpdb->escape($value);
	$description = $wpdb->escape($description);
	$wpdb->query("INSERT INTO $wpdb->options (option_name, option_value, option_description, autoload) VALUES ('$name', '$value', '$description', '$autoload')");

	return;
}

function delete_option($name) {
	global $wpdb;
	// Get the ID, if no ID then return
	$option_id = $wpdb->get_var("SELECT option_id FROM $wpdb->options WHERE option_name = '$name'");
	if ( !$option_id ) return false;
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = '$name'");
	wp_cache_delete($name, 'options');
	return true;
}

function gzip_compression() {
	if ( !get_option('gzipcompression') ) return false;

	if ( extension_loaded('zlib') ) {
		ob_start('ob_gzhandler');
	}
}


// functions to count the page generation time (from phpBB2)
// ( or just any time between timer_start() and timer_stop() )

function timer_stop($display = 0, $precision = 3) { //if called like timer_stop(1), will echo $timetotal
	global $timestart, $timeend;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timeend = $mtime;
	$timetotal = $timeend-$timestart;
	if ( $display )
		echo number_format($timetotal,$precision);
	return $timetotal;
}

function make_url_footnote($content) {
	preg_match_all('/<a(.+?)href=\"(.+?)\"(.*?)>(.+?)<\/a>/', $content, $matches);
	$j = 0;
	for ($i=0; $i<count($matches[0]); $i++) {
		$links_summary = (!$j) ? "\n" : $links_summary;
		$j++;
		$link_match = $matches[0][$i];
		$link_number = '['.($i+1).']';
		$link_url = $matches[2][$i];
		$link_text = $matches[4][$i];
		$content = str_replace($link_match, $link_text.' '.$link_number, $content);
		$link_url = ((strtolower(substr($link_url,0,7)) != 'http://') && (strtolower(substr($link_url,0,8)) != 'https://')) ? get_option('home') . $link_url : $link_url;
		$links_summary .= "\n".$link_number.' '.$link_url;
	}
	$content = strip_tags($content);
	$content .= $links_summary;
	return $content;
}


function xmlrpc_getposttitle($content) {
	global $post_default_title;
	if ( preg_match('/<title>(.+?)<\/title>/is', $content, $matchtitle) ) {
		$post_title = $matchtitle[0];
		$post_title = preg_replace('/<title>/si', '', $post_title);
		$post_title = preg_replace('/<\/title>/si', '', $post_title);
	} else {
		$post_title = $post_default_title;
	}
	return $post_title;
}

function xmlrpc_getpostcategory($content) {
	global $post_default_category;
	if ( preg_match('/<category>(.+?)<\/category>/is', $content, $matchcat) ) {
		$post_category = trim($matchcat[1], ',');
		$post_category = explode(',', $post_category);
	} else {
		$post_category = $post_default_category;
	}
	return $post_category;
}

function xmlrpc_removepostdata($content) {
	$content = preg_replace('/<title>(.+?)<\/title>/si', '', $content);
	$content = preg_replace('/<category>(.+?)<\/category>/si', '', $content);
	$content = trim($content);
	return $content;
}

function debug_fopen($filename, $mode) {
	global $debug;
	if ( $debug == 1 ) {
		$fp = fopen($filename, $mode);
		return $fp;
	} else {
		return false;
	}
}

function debug_fwrite($fp, $string) {
	global $debug;
	if ( $debug == 1 ) {
		fwrite($fp, $string);
	}
}

function debug_fclose($fp) {
	global $debug;
	if ( $debug == 1 ) {
		fclose($fp);
	}
}

function do_enclose( $content, $post_ID ) {
	global $wp_version, $wpdb;
	include_once (ABSPATH . WPINC . '/class-IXR.php');

	$log = debug_fopen(ABSPATH . '/enclosures.log', 'a');
	$post_links = array();
	debug_fwrite($log, 'BEGIN '.date('YmdHis', time())."\n");

	$pung = get_enclosed( $post_ID );

	$ltrs = '\w';
	$gunk = '/#~:.?+=&%@!\-';
	$punc = '.:?\-';
	$any = $ltrs . $gunk . $punc;

	preg_match_all("{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp);

	debug_fwrite($log, 'Post contents:');
	debug_fwrite($log, $content."\n");

	foreach($post_links_temp[0] as $link_test) :
		if ( !in_array($link_test, $pung) ) : // If we haven't pung it already
			$test = parse_url($link_test);
			if ( isset($test['query']) )
				$post_links[] = $link_test;
			elseif (($test['path'] != '/') && ($test['path'] != ''))
				$post_links[] = $link_test;
		endif;
	endforeach;

	foreach ($post_links as $url) :
		if ( $url != '' && !$wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE post_id = '$post_ID' AND meta_key = 'enclosure' AND meta_value LIKE ('$url%')") ) {
			if ( $headers = wp_get_http_headers( $url) ) {
				$len = (int) $headers['content-length'];
				$type = $wpdb->escape( $headers['content-type'] );
				$allowed_types = array( 'video', 'audio' );
				if ( in_array( substr( $type, 0, strpos( $type, "/" ) ), $allowed_types ) ) {
					$meta_value = "$url\n$len\n$type\n";
					$wpdb->query( "INSERT INTO `$wpdb->postmeta` ( `post_id` , `meta_key` , `meta_value` )
					VALUES ( '$post_ID', 'enclosure' , '$meta_value')" );
				}
			}
		}
	endforeach;
}

function wp_get_http_headers( $url, $red = 1 ) {
	global $wp_version;
	@set_time_limit( 60 );

	if ( $red > 5 )
	   return false;

	$parts = parse_url( $url );
	$file = $parts['path'] . ($parts['query'] ? '?'.$parts['query'] : '');
	$host = $parts['host'];
	if ( !isset( $parts['port'] ) )
		$parts['port'] = 80;

	$head = "HEAD $file HTTP/1.1\r\nHOST: $host\r\nUser-Agent: WordPress/" . $wp_version . "\r\n\r\n";

	$fp = @fsockopen($host, $parts['port'], $err_num, $err_msg, 3);
	if ( !$fp )
		return false;

	$response = '';
	fputs( $fp, $head );
	while ( !feof( $fp ) && strpos( $response, "\r\n\r\n" ) == false )
		$response .= fgets( $fp, 2048 );
	fclose( $fp );
	preg_match_all('/(.*?): (.*)\r/', $response, $matches);
	$count = count($matches[1]);
	for ( $i = 0; $i < $count; $i++) {
		$key = strtolower($matches[1][$i]);
		$headers["$key"] = $matches[2][$i];
	}

	preg_match('/.*([0-9]{3}).*/', $response, $return);
	$headers['response'] = $return[1]; // HTTP response code eg 204, 200, 404

    $code = $headers['response'];
    if ( ('302' == $code || '301' == $code) && isset($headers['location']) )
        return wp_get_http_headers( $headers['location'], ++$red );

	return $headers;
}

function is_new_day() {
	global $day, $previousday;
	if ( $day != $previousday ) {
		return(1);
	} else {
		return(0);
	}
}

function update_post_cache(&$posts) {
	global $post_cache;

	if ( !$posts )
		return;

	for ($i = 0; $i < count($posts); $i++) {
		$post_cache[$posts[$i]->ID] = &$posts[$i];
	}
}

function clean_post_cache($id) {
	global $post_cache;

	if ( isset( $post_cache[$id] ) )
		unset( $post_cache[$id] );
}

function update_page_cache(&$pages) {
	global $page_cache;

	if ( !$pages )
		return;

	for ($i = 0; $i < count($pages); $i++) {
		$page_cache[$pages[$i]->ID] = &$pages[$i];
		wp_cache_add($pages[$i]->ID, $pages[$i], 'pages');
	}
}


function clean_page_cache($id) {
	global $page_cache;

	if ( isset( $page_cache[$id] ) )
		unset( $page_cache[$id] );
}

function update_post_category_cache($post_ids) {
	global $wpdb, $category_cache;

	if ( empty($post_ids) )
		return;

	if ( is_array($post_ids) )
		$post_ids = implode(',', $post_ids);

	$dogs = $wpdb->get_results("SELECT post_id, category_id FROM $wpdb->post2cat WHERE post_id IN ($post_ids)");

	if ( empty($dogs) )
		return;

	foreach ($dogs as $catt)
		$category_cache[$catt->post_id][$catt->category_id] = &get_category($catt->category_id);
}

function update_post_caches(&$posts) {
	global $post_cache, $category_cache, $post_meta_cache;
	global $wpdb;

	// No point in doing all this work if we didn't match any posts.
	if ( !$posts )
		return;

	// Get the categories for all the posts
	for ($i = 0; $i < count($posts); $i++) {
		$post_id_array[] = $posts[$i]->ID;
		$post_cache[$posts[$i]->ID] = &$posts[$i];
	}

	$post_id_list = implode(',', $post_id_array);

	update_post_category_cache($post_id_list);

	// Get post-meta info
	if ( $meta_list = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id IN($post_id_list) ORDER BY post_id, meta_key", ARRAY_A) ) {
		// Change from flat structure to hierarchical:
		$post_meta_cache = array();
		foreach ($meta_list as $metarow) {
			$mpid = $metarow['post_id'];
			$mkey = $metarow['meta_key'];
			$mval = $metarow['meta_value'];

			// Force subkeys to be array type:
			if ( !isset($post_meta_cache[$mpid]) || !is_array($post_meta_cache[$mpid]) )
				$post_meta_cache[$mpid] = array();
			if ( !isset($post_meta_cache[$mpid]["$mkey"]) || !is_array($post_meta_cache[$mpid]["$mkey"]) )
				$post_meta_cache[$mpid]["$mkey"] = array();

			// Add a value to the current pid/key:
			$post_meta_cache[$mpid][$mkey][] = $mval;
		}
	}
}

function update_category_cache() {
	return true;
}

/*
add_query_arg: Returns a modified querystring by adding
a single key & value or an associative array.
Setting a key value to emptystring removes the key.
Omitting oldquery_or_uri uses the $_SERVER value.

Parameters:
add_query_arg(newkey, newvalue, oldquery_or_uri) or
add_query_arg(associative_array, oldquery_or_uri)
*/
function add_query_arg() {
	$ret = '';
	if ( is_array(func_get_arg(0)) ) {
		if ( @func_num_args() < 2 || '' == @func_get_arg(1) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg(1);
	} else {
		if ( @func_num_args() < 3 || '' == @func_get_arg(2) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg(2);
	}

	if ( $frag = strstr($uri, '#') )
		$uri = substr($uri, 0, -strlen($frag));
	else
		$frag = '';

	if ( preg_match('|^https?://|i', $uri, $matches) ) {
		$protocol = $matches[0];
		$uri = substr($uri, strlen($protocol));
	} else {
		$protocol = '';
	}

	if ( strstr($uri, '?') ) {
		$parts = explode('?', $uri, 2);
		if ( 1 == count($parts) ) {
			$base = '?';
			$query = $parts[0];
		} else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	} else if ( !empty($protocol) || strstr($uri, '/') ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	parse_str($query, $qs);
	if ( is_array(func_get_arg(0)) ) {
		$kayvees = func_get_arg(0);
		$qs = array_merge($qs, $kayvees);
	} else {
		$qs[func_get_arg(0)] = func_get_arg(1);
	}

	foreach($qs as $k => $v) {
		if ( $v != '' ) {
			if ( $ret != '' )
				$ret .= '&';
			$ret .= "$k=$v";
		}
	}
	$ret = $protocol . $base . $ret . $frag;
	if ( get_magic_quotes_gpc() )
		$ret = stripslashes($ret); // parse_str() adds slashes if magicquotes is on.  See: http://php.net/parse_str
	return trim($ret, '?');
}

/*
remove_query_arg: Returns a modified querystring by removing
a single key or an array of keys.
Omitting oldquery_or_uri uses the $_SERVER value.

Parameters:
remove_query_arg(removekey, [oldquery_or_uri]) or
remove_query_arg(removekeyarray, [oldquery_or_uri])
*/

function remove_query_arg($key, $query='') {
	if ( is_array($key) ) { // removing multiple keys
		foreach ( (array) $key as $k )
			$query = add_query_arg($k, '', $query);
		return $query;
	}
	return add_query_arg($key, '', $query);
}

function add_magic_quotes($array) {
	global $wpdb;

	foreach ($array as $k => $v) {
		if ( is_array($v) ) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = $wpdb->escape($v);
		}
	}
	return $array;
}

function wp_remote_fopen( $uri ) {
	if ( ini_get('allow_url_fopen') ) {
		$fp = fopen( $uri, 'r' );
		if ( !$fp )
			return false;
		$linea = '';
		while( $remote_read = fread($fp, 4096) )
			$linea .= $remote_read;
		fclose($fp);
		return $linea;
	} else if ( function_exists('curl_init') ) {
		$handle = curl_init();
		curl_setopt ($handle, CURLOPT_URL, $uri);
		curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt ($handle, CURLOPT_RETURNTRANSFER, 1);
		$buffer = curl_exec($handle);
		curl_close($handle);
		return $buffer;
	} else {
		return false;
	}
}

function wp($query_vars = '') {
	global $wp;

	$wp->main($query_vars);
}

function status_header( $header ) {
	if ( 200 == $header )
		$text = 'OK';
	elseif ( 301 == $header )
		$text = 'Moved Permanently';
	elseif ( 302 == $header )
		$text = 'Moved Temporarily';
	elseif ( 304 == $header )
		$text = 'Not Modified';
	elseif ( 404 == $header )
		$text = 'Not Found';
	elseif ( 410 == $header )
		$text = 'Gone';

	@header("HTTP/1.1 $header $text");
	@header("Status: $header $text");
}

function nocache_headers() {
	@ header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	@ header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	@ header('Cache-Control: no-cache, must-revalidate, max-age=0');
	@ header('Pragma: no-cache');
}

function cache_javascript_headers() {
	$expiresOffset = 864000; // 10 days
	header("Content-type: text/javascript; charset=" . get_bloginfo('charset'));
	header("Vary: Accept-Encoding"); // Handle proxies
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
}

function get_num_queries() {
	global $wpdb;
	return $wpdb->num_queries;
}

function bool_from_yn($yn) {
    if ($yn == 'Y') return 1;
    return 0;
}

function do_feed() {
	$feed = get_query_var('feed');

	// Remove the pad, if present.
	$feed = preg_replace('/^_+/', '', $feed);

	if ($feed == '' || $feed == 'feed')
    	$feed = 'rss2';

	$for_comments = false;
	if ( is_singular() || get_query_var('withcomments') == 1 || $feed == 'comments-rss2' ) {
		$feed = 'rss2';
		$for_comments = true;	
	}

	$hook = 'do_feed_' . $feed;
	do_action($hook, $for_comments);
}

function do_feed_rdf() {
	load_template(ABSPATH . 'wp-rdf.php');
}

function do_feed_rss() {
	load_template(ABSPATH . 'wp-rss.php');
}

function do_feed_rss2($for_comments) {
	if ( $for_comments ) {
		load_template(ABSPATH . 'wp-commentsrss2.php');
	} else {
		load_template(ABSPATH . 'wp-rss2.php');
	}
}

function do_feed_atom() {
	load_template(ABSPATH . 'wp-atom.php');
}

function do_robots() {
	do_action('do_robotstxt');
	if ( '0' == get_option('blog_public') ) {
		echo "User-agent: *\n";
		echo "Disallow: /\n";
	} else {
		echo "User-agent: *\n";
		echo "Disallow:\n";
	}
}

function is_blog_installed() {
	global $wpdb;
	$wpdb->hide_errors();
	$installed = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'siteurl'");
	$wpdb->show_errors();
	return $installed;
}

function wp_nonce_url($actionurl, $action = -1) {
	return wp_specialchars(add_query_arg('_wpnonce', wp_create_nonce($action), $actionurl));
}

function wp_nonce_field($action = -1) {
	echo '<input type="hidden" name="_wpnonce" value="' . wp_create_nonce($action) . '" />';
	wp_referer_field();
}

function wp_referer_field() {
	$ref = wp_specialchars($_SERVER['REQUEST_URI']);
	echo '<input type="hidden" name="_wp_http_referer" value="'. $ref . '" />';
	if ( wp_get_original_referer() ) {
		$original_ref = wp_specialchars(stripslashes(wp_get_original_referer()));
		echo '<input type="hidden" name="_wp_original_http_referer" value="'. $original_ref . '" />';
	}
}

function wp_original_referer_field() {
	echo '<input type="hidden" name="_wp_original_http_referer" value="' . wp_specialchars(stripslashes($_SERVER['REQUEST_URI'])) . '" />';
}

function wp_get_referer() {
	foreach ( array($_REQUEST['_wp_http_referer'], $_SERVER['HTTP_REFERER']) as $ref )
		if ( !empty($ref) )
			return $ref;
	return false;
}

function wp_get_original_referer() {
	if ( !empty($_REQUEST['_wp_original_http_referer']) )
		return $_REQUEST['_wp_original_http_referer'];
	return false;
}

function wp_mkdir_p($target) {
	// from php.net/mkdir user contributed notes
	if (file_exists($target)) {
		if (! @ is_dir($target))
			return false;
		else
			return true;
	}

	// Attempting to create the directory may clutter up our display.
	if (@ mkdir($target)) {
		$stat = @ stat(dirname($target));
		$dir_perms = $stat['mode'] & 0007777;  // Get the permission bits.
		@ chmod($target, $dir_perms);
		return true;
	} else {
		if ( is_dir(dirname($target)) )
			return false;
	}

	// If the above failed, attempt to create the parent node, then try again.
	if (wp_mkdir_p(dirname($target)))
		return wp_mkdir_p($target);

	return false;
}

// Returns an array containing the current upload directory's path and url, or an error message.
function wp_upload_dir() {
	$siteurl = get_option('siteurl');
	//prepend ABSPATH to $dir and $siteurl to $url if they're not already there
	$path = str_replace(ABSPATH, '', trim(get_option('upload_path')));
	$dir = ABSPATH . $path;
	$url = trailingslashit($siteurl) . $path;

	if ( $dir == ABSPATH ) { //the option was empty
		$dir = ABSPATH . 'wp-content/uploads';
	}

	if ( defined('UPLOADS') ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit($siteurl) . UPLOADS;
	}

	if ( get_option('uploads_use_yearmonth_folders')) {
		// Generate the yearly and monthly dirs
		$time = current_time( 'mysql' );
		$y = substr( $time, 0, 4 );
		$m = substr( $time, 5, 2 );
		$dir = $dir . "/$y/$m";
		$url = $url . "/$y/$m";
	}

	// Make sure we have an uploads dir
	if ( ! wp_mkdir_p( $dir ) ) {
		$message = sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), $dir);
		return array('error' => $message);
	}

    $uploads = array('path' => $dir, 'url' => $url, 'error' => false);
	return apply_filters('upload_dir', $uploads);
}

function wp_upload_bits($name, $type, $bits) {
	if ( empty($name) )
		return array('error' => __("Empty filename"));

	$wp_filetype = wp_check_filetype($name);
	if ( !$wp_filetype['ext'] )
		return array('error' => __("Invalid file type"));

	$upload = wp_upload_dir();

	if ( $upload['error'] !== false )
		return $upload;

	$number = '';
	$filename = $name;
	$path_parts = pathinfo($filename);
	$ext = $path_parts['extension'];
	if ( empty($ext) )
		$ext = '';
	else
		$ext = ".$ext";
	while ( file_exists($upload['path'] . "/$filename") ) {
		if ( '' == "$number$ext" )
			$filename = $filename . ++$number . $ext;
		else
			$filename = str_replace("$number$ext", ++$number . $ext, $filename);
	}

	$new_file = $upload['path'] . "/$filename";
	if ( ! wp_mkdir_p( dirname($new_file) ) ) {
		$message = sprintf(__('Unable to create directory %s. Is its parent directory writable by the server?'), dirname($new_file));
		return array('error' => $message);
	}

	$ifp = @ fopen($new_file, 'wb');
	if ( ! $ifp )
		return array('error' => "Could not write file $new_file.");

	$success = @ fwrite($ifp, $bits);
	fclose($ifp);
	// Set correct file permissions
	$stat = @ stat(dirname($new_file));
	$perms = $stat['mode'] & 0007777;
	$perms = $perms & 0000666;
	@ chmod($new_file, $perms);

	// Compute the URL
	$url = $upload['url'] . "/$filename";

	return array('file' => $new_file, 'url' => $url, 'error' => false);
}

function wp_check_filetype($filename, $mimes = null) {
	// Accepted MIME types are set here as PCRE unless provided.
	$mimes = is_array($mimes) ? $mimes : apply_filters('upload_mimes', array (
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'bmp' => 'image/bmp',
		'tif|tiff' => 'image/tiff',
		'ico' => 'image/x-icon',
		'asf|asx|wax|wmv|wmx' => 'video/asf',
		'avi' => 'video/avi',
		'mov|qt' => 'video/quicktime',
		'mpeg|mpg|mpe' => 'video/mpeg',
		'txt|c|cc|h' => 'text/plain',
		'rtx' => 'text/richtext',
		'css' => 'text/css',
		'htm|html' => 'text/html',
		'mp3|mp4' => 'audio/mpeg',
		'ra|ram' => 'audio/x-realaudio',
		'wav' => 'audio/wav',
		'ogg' => 'audio/ogg',
		'mid|midi' => 'audio/midi',
		'wma' => 'audio/wma',
		'rtf' => 'application/rtf',
		'js' => 'application/javascript',
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
		'wri' => 'application/vnd.ms-write',
		'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
		'mdb' => 'application/vnd.ms-access',
		'mpp' => 'application/vnd.ms-project',
		'swf' => 'application/x-shockwave-flash',
		'class' => 'application/java',
		'tar' => 'application/x-tar',
		'zip' => 'application/zip',
		'gz|gzip' => 'application/x-gzip',
		'exe' => 'application/x-msdownload'
	));

	$type = false;
	$ext = false;

	foreach ($mimes as $ext_preg => $mime_match) {
		$ext_preg = '!\.(' . $ext_preg . ')$!i';
		if ( preg_match($ext_preg, $filename, $ext_matches) ) {
			$type = $mime_match;
			$ext = $ext_matches[1];
			break;
		}
	}

	return compact('ext', 'type');
}

function wp_proxy_check($ipnum) {
	if ( get_option('open_proxy_check') && isset($ipnum) ) {
		$ipnum = preg_replace( '/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}).*/', '$1', $ipnum );
		$rev_ip = implode( '.', array_reverse( explode( '.', $ipnum ) ) );
		$lookup = $rev_ip . '.sbl-xbl.spamhaus.org.';
		if ( $lookup != gethostbyname( $lookup ) )
			return true;
	}

	return false;
}

function wp_explain_nonce($action) {
	if ( $action !== -1 && preg_match('/([a-z]+)-([a-z]+)(_(.+))?/', $action, $matches) ) {
		$verb = $matches[1];
		$noun = $matches[2];

		$trans = array();
		$trans['update']['attachment'] = array(__('Are you sure you want to edit this attachment: &quot;%s&quot;?'), 'get_the_title');

		$trans['add']['category'] = array(__('Are you sure you want to add this category?'), false);
		$trans['delete']['category'] = array(__('Are you sure you want to delete this category: &quot;%s&quot;?'), 'get_catname');
		$trans['update']['category'] = array(__('Are you sure you want to edit this category: &quot;%s&quot;?'), 'get_catname');

		$trans['delete']['comment'] = array(__('Are you sure you want to delete this comment: &quot;%s&quot;?'), 'use_id');
		$trans['unapprove']['comment'] = array(__('Are you sure you want to unapprove this comment: &quot;%s&quot;?'), 'use_id');
		$trans['approve']['comment'] = array(__('Are you sure you want to approve this comment: &quot;%s&quot;?'), 'use_id');
		$trans['update']['comment'] = array(__('Are you sure you want to edit this comment: &quot;%s&quot;?'), 'use_id');
		$trans['bulk']['comments'] = array(__('Are you sure you want to bulk modify comments?'), false);
		$trans['moderate']['comments'] = array(__('Are you sure you want to moderate comments?'), false);

		$trans['add']['bookmark'] = array(__('Are you sure you want to add this bookmark?'), false);
		$trans['delete']['bookmark'] = array(__('Are you sure you want to delete this bookmark: &quot;%s&quot;?'), 'use_id');
		$trans['update']['bookmark'] = array(__('Are you sure you want to edit this bookmark: &quot;%s&quot;?'), 'use_id');
		$trans['bulk']['bookmarks'] = array(__('Are you sure you want to bulk modify bookmarks?'), false);

		$trans['add']['page'] = array(__('Are you sure you want to add this page?'), false);
		$trans['delete']['page'] = array(__('Are you sure you want to delete this page: &quot;%s&quot;?'), 'get_the_title');
		$trans['update']['page'] = array(__('Are you sure you want to edit this page: &quot;%s&quot;?'), 'get_the_title');

		$trans['edit']['plugin'] = array(__('Are you sure you want to edit this plugin file: &quot;%s&quot;?'), 'use_id');
		$trans['activate']['plugin'] = array(__('Are you sure you want to activate this plugin: &quot;%s&quot;?'), 'use_id');
		$trans['deactivate']['plugin'] = array(__('Are you sure you want to deactivate this plugin: &quot;%s&quot;?'), 'use_id');

		$trans['add']['post'] = array(__('Are you sure you want to add this post?'), false);
		$trans['delete']['post'] = array(__('Are you sure you want to delete this post: &quot;%s&quot;?'), 'get_the_title');
		$trans['update']['post'] = array(__('Are you sure you want to edit this post: &quot;%s&quot;?'), 'get_the_title');

		$trans['add']['user'] = array(__('Are you sure you want to add this user?'), false);
		$trans['delete']['users'] = array(__('Are you sure you want to delete users?'), false);
		$trans['bulk']['users'] = array(__('Are you sure you want to bulk modify users?'), false);
		$trans['update']['user'] = array(__('Are you sure you want to edit this user: &quot;%s&quot;?'), 'get_author_name');
		$trans['update']['profile'] = array(__('Are you sure you want to modify the profile for: &quot;%s&quot;?'), 'get_author_name');

		$trans['update']['options'] = array(__('Are you sure you want to edit your settings?'), false);
		$trans['update']['permalink'] = array(__('Are you sure you want to change your permalink structure to: %s?'), 'use_id');
		$trans['edit']['file'] = array(__('Are you sure you want to edit this file: &quot;%s&quot;?'), 'use_id');
		$trans['edit']['theme'] = array(__('Are you sure you want to edit this theme file: &quot;%s&quot;?'), 'use_id');
		$trans['switch']['theme'] = array(__('Are you sure you want to switch to this theme: &quot;%s&quot;?'), 'use_id');

		if ( isset($trans[$verb][$noun]) ) {
			if ( !empty($trans[$verb][$noun][1]) ) {
				$lookup = $trans[$verb][$noun][1];
				$object = $matches[4];
				if ( 'use_id' != $lookup )
					$object = call_user_func($lookup, $object);
				return sprintf($trans[$verb][$noun][0], $object);
			} else {
				return $trans[$verb][$noun][0];
			}
		}
	}

	return __('Are you sure you want to do this');
}

function wp_nonce_ays($action) {
	global $pagenow, $menu, $submenu, $parent_file, $submenu_file;

	$adminurl = get_option('siteurl') . '/wp-admin';
	if ( wp_get_referer() )
		$adminurl = wp_get_referer();

	$title = __('WordPress Confirmation');
	// Remove extra layer of slashes.
	$_POST   = stripslashes_deep($_POST  );
	if ( $_POST ) {
		$q = http_build_query($_POST);
		$q = explode( ini_get('arg_separator.output'), $q);
		$html .= "\t<form method='post' action='$pagenow'>\n";
		foreach ( (array) $q as $a ) {
			$v = substr(strstr($a, '='), 1);
			$k = substr($a, 0, -(strlen($v)+1));
			$html .= "\t\t<input type='hidden' name='" . wp_specialchars( urldecode($k), 1 ) . "' value='" . wp_specialchars( urldecode($v), 1 ) . "' />\n";
		}
		$html .= "\t\t<input type='hidden' name='_wpnonce' value='" . wp_create_nonce($action) . "' />\n";
		$html .= "\t\t<div id='message' class='confirm fade'>\n\t\t<p>" . wp_explain_nonce($action) . "</p>\n\t\t<p><a href='$adminurl'>" . __('No') . "</a> <input type='submit' value='" . __('Yes') . "' /></p>\n\t\t</div>\n\t</form>\n";
	} else {
		$html .= "\t<div id='message' class='confirm fade'>\n\t<p>" . wp_explain_nonce($action) . "</p>\n\t<p><a href='$adminurl'>" . __('No') . "</a> <a href='" . add_query_arg( '_wpnonce', wp_create_nonce($action), $_SERVER['REQUEST_URI'] ) . "'>" . __('Yes') . "</a></p>\n\t</div>\n";
	}
	$html .= "</body>\n</html>";
	wp_die($html, $title);
}

function wp_die($message, $title = '') {
	header('Content-Type: text/html; charset=utf-8');

	if ( empty($title) )
		$title = __('WordPress &rsaquo; Error');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style media="screen" type="text/css">
	<!--
	html {
		background: #eee;
	}
	body {
		background: #fff;
		color: #000;
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 25%;
		margin-right: 25%;
		padding: .2em 2em;
	}

	h1 {
		color: #006;
		font-size: 18px;
		font-weight: lighter;
	}

	h2 {
		font-size: 16px;
	}

	p, li, dt {
		line-height: 140%;
		padding-bottom: 2px;
	}

	ul, ol {
		padding: 5px 5px 5px 20px;
	}
	#logo {
		margin-bottom: 2em;
	}
	-->
	</style>
</head>
<body>
	<h1 id="logo"><img alt="WordPress" src="<?php echo get_option('siteurl'); ?>/wp-admin/images/wordpress-logo.png" /></h1>
	<p><?php echo $message; ?></p>
</body>
</html>
<?php

	die();
}

?>
