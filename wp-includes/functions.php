<?php

function mysql2date($dateformatstring, $mysqlstring, $translate = true) {
	global $wp_locale;
	$m = $mysqlstring;
	if ( empty($m) ) {
		return false;
	}
	$i = mktime(
		(int) substr( $m, 11, 2 ), (int) substr( $m, 14, 2 ), (int) substr( $m, 17, 2 ),
		(int) substr( $m, 5, 2 ), (int) substr( $m, 8, 2 ), (int) substr( $m, 0, 4 )
	);

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

function number_format_i18n($number, $decimals = null) {
	global $wp_locale;
	// let the user override the precision only
	$decimals = is_null($decimals)? $wp_locale->number_format['decimals'] : intval($decimals);

	return number_format($number, $decimals, $wp_locale->number_format['decimal_point'], $wp_locale->number_format['thousands_sep']);
}

function size_format($bytes, $decimals = null) {
	// technically the correct unit names for powers of 1024 are KiB, MiB etc
	// see http://en.wikipedia.org/wiki/Byte
	$quant = array(
		'TB' => pow(1024, 4),
		'GB' => pow(1024, 3),
		'MB' => pow(1024, 2),
		'kB' => pow(1024, 1),
		'B'  => pow(1024, 0),
	);

	foreach ($quant as $unit => $mag)
		if ( intval($bytes) >= $mag )
			return number_format_i18n($bytes / $mag, $decimals) . ' ' . $unit;
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

function maybe_unserialize($original) {
	if ( is_serialized($original) ) // don't attempt to unserialize data that wasn't serialized going in
		if ( false !== $gm = @ unserialize($original) )
			return $gm;
	return $original;
}

function is_serialized($data) {
	// if it isn't a string, it isn't serialized
	if ( !is_string($data) )
		return false;
	$data = trim($data);
	if ( 'N;' == $data )
		return true;
	if ( !preg_match('/^([adObis]):/', $data, $badions) )
		return false;
	switch ( $badions[1] ) :
	case 'a' :
	case 'O' :
	case 's' :
		if ( preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data) )
			return true;
		break;
	case 'b' :
	case 'i' :
	case 'd' :
		if ( preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data) )
			return true;
		break;
	endswitch;
	return false;
}

function is_serialized_string($data) {
	// if it isn't a string, it isn't a serialized string
	if ( !is_string($data) )
		return false;
	$data = trim($data);
	if ( preg_match('/^s:[0-9]+:.*;$/s',$data) ) // this should fetch all serialized strings
		return true;
	return false;
}

/* Options functions */

// expects $setting to already be SQL-escaped
function get_option($setting) {
	global $wpdb;

	// Allow plugins to short-circuit options.
	$pre = apply_filters( 'pre_option_' . $setting, false );
	if ( false !== $pre )
		return $pre;

	// prevent non-existent options from triggering multiple queries
	$notoptions = wp_cache_get('notoptions', 'options');
	if ( isset($notoptions[$setting]) )
		return false;

	$alloptions = wp_load_alloptions();

	if ( isset($alloptions[$setting]) ) {
		$value = $alloptions[$setting];
	} else {
		$value = wp_cache_get($setting, 'options');

		if ( false === $value ) {
			if ( defined('WP_INSTALLING') )
				$wpdb->hide_errors();
			$row = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = '$setting' LIMIT 1");
			if ( defined('WP_INSTALLING') )
				$wpdb->show_errors();

			if( is_object( $row) ) { // Has to be get_row instead of get_var because of funkiness with 0, false, null values
				$value = $row->option_value;
				wp_cache_add($setting, $value, 'options');
			} else { // option does not exist, so we must cache its non-existence
				$notoptions[$setting] = true;
				wp_cache_set('notoptions', $notoptions, 'options');
				return false;
			}
		}
	}

	// If home is not set use siteurl.
	if ( 'home' == $setting && '' == $value )
		return get_option('siteurl');

	if ( in_array($setting, array('siteurl', 'home', 'category_base', 'tag_base')) )
		$value = untrailingslashit($value);

	return apply_filters( 'option_' . $setting, maybe_unserialize($value) );
}

function wp_protect_special_option($option) {
	$protected = array('alloptions', 'notoptions');
	if ( in_array($option, $protected) )
		die(sprintf(__('%s is a protected WP option and may not be modified'), wp_specialchars($option)));
}

function form_option($option) {
	echo attribute_escape(get_option($option));
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

function wp_load_alloptions() {
	global $wpdb;

	$alloptions = wp_cache_get('alloptions', 'options');

	if ( !$alloptions ) {
		$wpdb->hide_errors();
		if ( !$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'") )
			$alloptions_db = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options");
		$wpdb->show_errors();
		$alloptions = array();
		foreach ( (array) $alloptions_db as $o )
			$alloptions[$o->option_name] = $o->option_value;
		wp_cache_add('alloptions', $alloptions, 'options');
	}
	return $alloptions;
}

// expects $option_name to NOT be SQL-escaped
function update_option($option_name, $newvalue) {
	global $wpdb;

	wp_protect_special_option($option_name);

	$safe_option_name = $wpdb->escape($option_name);
	$newvalue = sanitize_option($option_name, $newvalue);

	if ( is_string($newvalue) )
		$newvalue = trim($newvalue);

	// If the new and old values are the same, no need to update.
	$oldvalue = get_option($safe_option_name);
	if ( $newvalue === $oldvalue ) {
		return false;
	}

	if ( false === $oldvalue ) {
		add_option($option_name, $newvalue);
		return true;
	}

	$notoptions = wp_cache_get('notoptions', 'options');
	if ( is_array($notoptions) && isset($notoptions[$option_name]) ) {
		unset($notoptions[$option_name]);
		wp_cache_set('notoptions', $notoptions, 'options');
	}

	$_newvalue = $newvalue;
	$newvalue = maybe_serialize($newvalue);

	$alloptions = wp_load_alloptions();
	if ( isset($alloptions[$option_name]) ) {
		$alloptions[$option_name] = $newvalue;
		wp_cache_set('alloptions', $alloptions, 'options');
	} else {
		wp_cache_set($option_name, $newvalue, 'options');
	}

	$newvalue = $wpdb->escape($newvalue);
	$option_name = $wpdb->escape($option_name);
	$wpdb->query("UPDATE $wpdb->options SET option_value = '$newvalue' WHERE option_name = '$option_name'");
	if ( $wpdb->rows_affected == 1 ) {
		do_action("update_option_{$option_name}", $oldvalue, $_newvalue);
		return true;
	}
	return false;
}

// thx Alex Stapleton, http://alex.vort-x.net/blog/
// expects $name to NOT be SQL-escaped
function add_option($name, $value = '', $deprecated = '', $autoload = 'yes') {
	global $wpdb;

	wp_protect_special_option($name);
	$safe_name = $wpdb->escape($name);

	// Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
	$notoptions = wp_cache_get('notoptions', 'options');
	if ( !is_array($notoptions) || !isset($notoptions[$name]) )
		if ( false !== get_option($safe_name) )
			return;

	$value = maybe_serialize($value);
	$autoload = ( 'no' === $autoload ) ? 'no' : 'yes';

	if ( 'yes' == $autoload ) {
		$alloptions = wp_load_alloptions();
		$alloptions[$name] = $value;
		wp_cache_set('alloptions', $alloptions, 'options');
	} else {
		wp_cache_set($name, $value, 'options');
	}

	// This option exists now
	$notoptions = wp_cache_get('notoptions', 'options'); // yes, again... we need it to be fresh
	if ( is_array($notoptions) && isset($notoptions[$name]) ) {
		unset($notoptions[$name]);
		wp_cache_set('notoptions', $notoptions, 'options');
	}

	$name = $wpdb->escape($name);
	$value = $wpdb->escape($value);
	$wpdb->query("INSERT INTO $wpdb->options (option_name, option_value, autoload) VALUES ('$name', '$value', '$autoload')");

	return;
}

function delete_option($name) {
	global $wpdb;

	wp_protect_special_option($name);

	// Get the ID, if no ID then return
	$option = $wpdb->get_row("SELECT option_id, autoload FROM $wpdb->options WHERE option_name = '$name'");
	if ( !$option->option_id ) return false;
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = '$name'");
	if ( 'yes' == $option->autoload ) {
		$alloptions = wp_load_alloptions();
		if ( isset($alloptions[$name]) ) {
			unset($alloptions[$name]);
			wp_cache_set('alloptions', $alloptions, 'options');
		}
	} else {
		wp_cache_delete($name, 'options');
	}
	return true;
}

function maybe_serialize($data) {
	if ( is_string($data) )
		$data = trim($data);
	elseif ( is_array($data) || is_object($data) )
		return serialize($data);
	if ( is_serialized($data) )
		return serialize($data);
	return $data;
}

function gzip_compression() {
	if ( !get_option( 'gzipcompression' ) ) {
		return false;
	}

	if ( ( ini_get( 'zlib.output_compression' ) == 'On' || ini_get( 'zlib.output_compression_level' ) > 0 ) || ini_get( 'output_handler' ) == 'ob_gzhandler' ) {
		return false;
	}

	if ( extension_loaded( 'zlib' ) ) {
		ob_start( 'ob_gzhandler' );
	}
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

	$log = debug_fopen(ABSPATH . 'enclosures.log', 'a');
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

function build_query($data) {
	return _http_build_query($data, NULL, '&', '', false);
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
		if ( @func_num_args() < 2 || false === @func_get_arg(1) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg(1);
	} else {
		if ( @func_num_args() < 3 || false === @func_get_arg(2) )
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

	if (strpos($uri, '?') !== false) {
		$parts = explode('?', $uri, 2);
		if ( 1 == count($parts) ) {
			$base = '?';
			$query = $parts[0];
		} else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	} elseif (!empty($protocol) || strpos($uri, '=') === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	wp_parse_str($query, $qs);
	$qs = urlencode_deep($qs); // this re-URL-encodes things that were already in the query string
	if ( is_array(func_get_arg(0)) ) {
		$kayvees = func_get_arg(0);
		$qs = array_merge($qs, $kayvees);
	} else {
		$qs[func_get_arg(0)] = func_get_arg(1);
	}

	foreach ( $qs as $k => $v ) {
		if ( $v === false )
			unset($qs[$k]);
	}

	$ret = build_query($qs);
	$ret = trim($ret, '?');
	$ret = preg_replace('#=(&|$)#', '$1', $ret);
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim($ret, '?');
	return $ret;
}

/*
remove_query_arg: Returns a modified querystring by removing
a single key or an array of keys.
Omitting oldquery_or_uri uses the $_SERVER value.

Parameters:
remove_query_arg(removekey, [oldquery_or_uri]) or
remove_query_arg(removekeyarray, [oldquery_or_uri])
*/

function remove_query_arg($key, $query=FALSE) {
	if ( is_array($key) ) { // removing multiple keys
		foreach ( (array) $key as $k )
			$query = add_query_arg($k, FALSE, $query);
		return $query;
	}
	return add_query_arg($key, FALSE, $query);
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
	$timeout = 10;
	$parsed_url = @parse_url($uri);

	if ( !$parsed_url || !is_array($parsed_url) )
		return false;

	if ( !isset($parsed_url['scheme']) || !in_array($parsed_url['scheme'], array('http','https')) )
		$uri = 'http://' . $uri;

	if ( ini_get('allow_url_fopen') ) {
		$fp = @fopen( $uri, 'r' );
		if ( !$fp )
			return false;

		//stream_set_timeout($fp, $timeout); // Requires php 4.3
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
		curl_setopt ($handle, CURLOPT_TIMEOUT, $timeout);
		$buffer = curl_exec($handle);
		curl_close($handle);
		return $buffer;
	} else {
		return false;
	}
}

function wp($query_vars = '') {
	global $wp, $wp_query, $wp_the_query;

	$wp->main($query_vars);

	if( !isset($wp_the_query) )
		$wp_the_query = $wp_query;
}

function get_status_header_desc( $code ) {
	global $wp_header_to_desc;

	$code = (int) $code;

	if ( !isset($wp_header_to_desc) ) {
		$wp_header_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
	}

	if ( isset( $wp_header_to_desc[$code] ) ) {
		return $wp_header_to_desc[$code];
	} else {
		return '';
	}
}

function status_header( $header ) {
	$text = get_status_header_desc( $header );

	if ( empty( $text ) )
		return false;

	$protocol = $_SERVER["SERVER_PROTOCOL"];
	if ( ('HTTP/1.1' != $protocol) && ('HTTP/1.0' != $protocol) )
		$protocol = 'HTTP/1.0';
	$status_header = "$protocol $header $text";
	if ( function_exists('apply_filters') )
		$status_header = apply_filters('status_header', $status_header, $header, $text, $protocol);

	if ( version_compare( phpversion(), '4.3.0', '>=' ) ) {
		return @header( $status_header, true, $header );
	} else {
		return @header( $status_header );
	}
}

function nocache_headers() {
	@ header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	@ header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	@ header('Cache-Control: no-cache, must-revalidate, max-age=0');
	@ header('Pragma: no-cache');
}

function cache_javascript_headers() {
	$expiresOffset = 864000; // 10 days
	header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));
	header("Vary: Accept-Encoding"); // Handle proxies
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");
}

function get_num_queries() {
	global $wpdb;
	return $wpdb->num_queries;
}

function bool_from_yn( $yn ) {
	return ( strtolower( $yn ) == 'y' );
}

function do_feed() {
	global $wp_query;

	$feed = get_query_var('feed');

	// Remove the pad, if present.
	$feed = preg_replace('/^_+/', '', $feed);

	if ( $feed == '' || $feed == 'feed' )
		$feed = 'rss2';

	$hook = 'do_feed_' . $feed;
	do_action($hook, $wp_query->is_comment_feed);
}

function do_feed_rdf() {
	load_template(ABSPATH . WPINC . '/feed-rdf.php');
}

function do_feed_rss() {
	load_template(ABSPATH . WPINC . '/feed-rss.php');
}

function do_feed_rss2($for_comments) {
	if ( $for_comments ) {
		load_template(ABSPATH . WPINC . '/feed-rss2-comments.php');
	} else {
		load_template(ABSPATH . WPINC . '/feed-rss2.php');
	}
}

function do_feed_atom($for_comments) {
	if ($for_comments) {
		load_template(ABSPATH . WPINC . '/feed-atom-comments.php');
	} else {
		load_template(ABSPATH . WPINC . '/feed-atom.php');
	}
}

function do_robots() {
	header('Content-Type: text/plain; charset=utf-8');

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

	$install_status = !empty( $installed ) ? TRUE : FALSE;
	return $install_status;
}

function wp_nonce_url($actionurl, $action = -1) {
	$actionurl = str_replace('&amp;', '&', $actionurl);
	return wp_specialchars(add_query_arg('_wpnonce', wp_create_nonce($action), $actionurl));
}

function wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true) {
	$name = attribute_escape($name);
	echo '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';
	if ( $referer )
		wp_referer_field();
}

function wp_referer_field() {
	$ref = attribute_escape($_SERVER['REQUEST_URI']);
	echo '<input type="hidden" name="_wp_http_referer" value="'. $ref . '" />';
	if ( wp_get_original_referer() ) {
		$original_ref = attribute_escape(stripslashes(wp_get_original_referer()));
		echo '<input type="hidden" name="_wp_original_http_referer" value="'. $original_ref . '" />';
	}
}

function wp_original_referer_field() {
	echo '<input type="hidden" name="_wp_original_http_referer" value="' . attribute_escape(stripslashes($_SERVER['REQUEST_URI'])) . '" />';
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
		return array('error' => sprintf(__('Could not write file %s'), $new_file));

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
		'exe' => 'application/x-msdownload',
		// openoffice formats
		'odt' => 'application/vnd.oasis.opendocument.text',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odf' => 'application/vnd.oasis.opendocument.formula',

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

		$trans['add']['bookmark'] = array(__('Are you sure you want to add this link?'), false);
		$trans['delete']['bookmark'] = array(__('Are you sure you want to delete this link: &quot;%s&quot;?'), 'use_id');
		$trans['update']['bookmark'] = array(__('Are you sure you want to edit this link: &quot;%s&quot;?'), 'use_id');
		$trans['bulk']['bookmarks'] = array(__('Are you sure you want to bulk modify links?'), false);

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

	return apply_filters( 'explain_nonce_' . $verb . '-' . $noun, __('Are you sure you want to do this?'), $matches[4] );
}

function wp_nonce_ays($action) {
	global $pagenow, $menu, $submenu, $parent_file, $submenu_file;

	$adminurl = get_option('siteurl') . '/wp-admin';
	if ( wp_get_referer() )
		$adminurl = clean_url(wp_get_referer());

	$title = __('WordPress Confirmation');
	// Remove extra layer of slashes.
	$_POST   = stripslashes_deep($_POST  );
	if ( $_POST ) {
		$q = http_build_query($_POST);
		$q = explode( ini_get('arg_separator.output'), $q);
		$html .= "\t<form method='post' action='" . attribute_escape($pagenow) . "'>\n";
		foreach ( (array) $q as $a ) {
			$v = substr(strstr($a, '='), 1);
			$k = substr($a, 0, -(strlen($v)+1));
			$html .= "\t\t<input type='hidden' name='" . attribute_escape(urldecode($k)) . "' value='" . attribute_escape(urldecode($v)) . "' />\n";
		}
		$html .= "\t\t<input type='hidden' name='_wpnonce' value='" . wp_create_nonce($action) . "' />\n";
		$html .= "\t\t<div id='message' class='confirm fade'>\n\t\t<p>" . wp_specialchars(wp_explain_nonce($action)) . "</p>\n\t\t<p><a href='$adminurl'>" . __('No') . "</a> <input type='submit' value='" . __('Yes') . "' /></p>\n\t\t</div>\n\t</form>\n";
	} else {
		$html .= "\t<div id='message' class='confirm fade'>\n\t<p>" . wp_specialchars(wp_explain_nonce($action)) . "</p>\n\t<p><a href='$adminurl'>" . __('No') . "</a> <a href='" . clean_url(add_query_arg( '_wpnonce', wp_create_nonce($action), $_SERVER['REQUEST_URI'] )) . "'>" . __('Yes') . "</a></p>\n\t</div>\n";
	}
	$html .= "</body>\n</html>";
	wp_die($html, $title);
}

function wp_die( $message, $title = '' ) {
	global $wp_locale;

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( empty($title) ) {
			$error_data = $message->get_error_data();
			if ( is_array($error_data) && isset($error_data['title']) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count($errors) ) :
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
			break;
		endswitch;
	} elseif ( is_string($message) ) {
		$message = "<p>$message</p>";
	}

	if ( defined('WP_SITEURL') && '' != WP_SITEURL ) 
		$admin_dir = WP_SITEURL.'/wp-admin/'; 
	elseif (function_exists('get_bloginfo') && '' != get_bloginfo('wpurl'))
		$admin_dir = get_bloginfo('wpurl').'/wp-admin/'; 
	elseif (strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false)
		$admin_dir = '';
	else
		$admin_dir = 'wp-admin/';

	if ( !function_exists('did_action') || !did_action('admin_head') ) :
	if( !headers_sent() ){
		status_header(500);
		nocache_headers();
		header('Content-Type: text/html; charset=utf-8');
	}

	if ( empty($title) ){
		if( function_exists('__') )
			$title = __('WordPress &rsaquo; Error');
		else
			$title = 'WordPress &rsaquo; Error';
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists('language_attributes') ) language_attributes(); ?>>
<head>
	<title><?php echo $title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="<?php echo $admin_dir; ?>css/install.css" type="text/css" />
<?php
if ( ( $wp_locale ) && ('rtl' == $wp_locale->text_direction) ) : ?>
	<link rel="stylesheet" href="<?php echo $admin_dir; ?>css/install-rtl.css" type="text/css" />
<?php endif; ?>
</head>
<body>
<?php endif; ?>
	<h1 id="logo"><img alt="WordPress" src="<?php echo $admin_dir; ?>images/wordpress-logo.png" /></h1>
	<?php echo $message; ?>

</body>
</html>
<?php
	die();
}

function _config_wp_home($url = '') {
	if ( defined( 'WP_HOME' ) )
		return WP_HOME;
	else return $url;
}

function _config_wp_siteurl($url = '') {
	if ( defined( 'WP_SITEURL' ) )
		return WP_SITEURL;
	else return $url;
}

function _mce_set_direction() {
	global $wp_locale;

	if ('rtl' == $wp_locale->text_direction) {
		echo 'directionality : "rtl" ,';
		echo 'theme_advanced_toolbar_align : "right" ,';
	}
}

function _mce_load_rtl_plugin($input) {
	global $wp_locale;

	if ('rtl' == $wp_locale->text_direction)
		$input[] = 'directionality';

	return $input;
}

function _mce_add_direction_buttons($input) {
	global $wp_locale;

	if ('rtl' == $wp_locale->text_direction) {
		$new_buttons = array('separator', 'ltr', 'rtl');
		$input = array_merge($input, $new_buttons);
	}

	return $input;
}

function smilies_init() {
	global $wpsmiliestrans, $wp_smiliessearch, $wp_smiliesreplace;

	// don't bother setting up smilies if they are disabled
	if ( !get_option('use_smilies') )
		return;

	if (!isset($wpsmiliestrans)) {
		$wpsmiliestrans = array(
		':mrgreen:' => 'icon_mrgreen.gif',
		':neutral:' => 'icon_neutral.gif',
		':twisted:' => 'icon_twisted.gif',
		  ':arrow:' => 'icon_arrow.gif',
		  ':shock:' => 'icon_eek.gif',
		  ':smile:' => 'icon_smile.gif',
		    ':???:' => 'icon_confused.gif',
		   ':cool:' => 'icon_cool.gif',
		   ':evil:' => 'icon_evil.gif',
		   ':grin:' => 'icon_biggrin.gif',
		   ':idea:' => 'icon_idea.gif',
		   ':oops:' => 'icon_redface.gif',
		   ':razz:' => 'icon_razz.gif',
		   ':roll:' => 'icon_rolleyes.gif',
		   ':wink:' => 'icon_wink.gif',
		    ':cry:' => 'icon_cry.gif',
		    ':eek:' => 'icon_surprised.gif',
		    ':lol:' => 'icon_lol.gif',
		    ':mad:' => 'icon_mad.gif',
		    ':sad:' => 'icon_sad.gif',
		      '8-)' => 'icon_cool.gif',
		      '8-O' => 'icon_eek.gif',
		      ':-(' => 'icon_sad.gif',
		      ':-)' => 'icon_smile.gif',
		      ':-?' => 'icon_confused.gif',
		      ':-D' => 'icon_biggrin.gif',
		      ':-P' => 'icon_razz.gif',
		      ':-o' => 'icon_surprised.gif',
		      ':-x' => 'icon_mad.gif',
		      ':-|' => 'icon_neutral.gif',
		      ';-)' => 'icon_wink.gif',
		       '8)' => 'icon_cool.gif',
		       '8O' => 'icon_eek.gif',
		       ':(' => 'icon_sad.gif',
		       ':)' => 'icon_smile.gif',
		       ':?' => 'icon_confused.gif',
		       ':D' => 'icon_biggrin.gif',
		       ':P' => 'icon_razz.gif',
		       ':o' => 'icon_surprised.gif',
		       ':x' => 'icon_mad.gif',
		       ':|' => 'icon_neutral.gif',
		       ';)' => 'icon_wink.gif',
		      ':!:' => 'icon_exclaim.gif',
		      ':?:' => 'icon_question.gif',
		);
	}

	$siteurl = get_option('siteurl');
	foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
		$wp_smiliessearch[] = '/(\s|^)'.preg_quote($smiley, '/').'(\s|$)/';
		$smiley_masked = htmlspecialchars(trim($smiley), ENT_QUOTES);
		$wp_smiliesreplace[] = " <img src='$siteurl/wp-includes/images/smilies/$img' alt='$smiley_masked' class='wp-smiley' /> ";
	}
}

function wp_parse_args( $args, $defaults = '' ) {
	if ( is_object($args) )
		$r = get_object_vars($args);
	else if ( is_array( $args ) )
		$r =& $args;
	else
		wp_parse_str( $args, $r );

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );
	else
		return $r;
}

function wp_maybe_load_widgets() {
	if ( !function_exists( 'dynamic_sidebar' ) ) {
		require_once ABSPATH . WPINC . '/widgets.php';
		add_action( '_admin_menu', 'wp_widgets_add_menu' );
	}
}

function wp_widgets_add_menu() {
	global $submenu;
	$submenu['themes.php'][7] = array( __( 'Widgets' ), 'switch_themes', 'widgets.php' );
	ksort($submenu['themes.php'], SORT_NUMERIC);
}

// For PHP 5.2, make sure all output buffers are flushed
// before our singletons our destroyed.
function wp_ob_end_flush_all()
{
	while ( @ob_end_flush() );
}

?>
