<?php

if (!function_exists('_')) {
	function _($string) {
		return $string;
	}
}

if (!function_exists('floatval')) {
	function floatval($string) {
		return ((float) $string);
	}
}

function get_profile($field, $user = false) {
	global $wpdb;
	if (!$user)
		$user = $wpdb->escape($_COOKIE['wordpressuser_' . COOKIEHASH]);
	return $wpdb->get_var("SELECT $field FROM $wpdb->users WHERE user_login = '$user'");
}

function mysql2date($dateformatstring, $mysqlstring, $use_b2configmonthsdays = 1) {
	global $month, $weekday;
	$m = $mysqlstring;
	if (empty($m)) {
		return false;
	}
	$i = mktime(substr($m,11,2),substr($m,14,2),substr($m,17,2),substr($m,5,2),substr($m,8,2),substr($m,0,4)); 
	if (!empty($month) && !empty($weekday) && $use_b2configmonthsdays) {
		$datemonth = $month[date('m', $i)];
		$dateweekday = $weekday[date('w', $i)];
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit(substr($dateweekday, 0, 3)), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit(substr($datemonth, 0, 3)), $dateformatstring);
		$dateformatstring = substr($dateformatstring, 1, strlen($dateformatstring)-1);
	}
	$j = @date($dateformatstring, $i);
	if (!$j) {
	// for debug purposes
	//	echo $i." ".$mysqlstring;
	}
	return $j;
}

function current_time($type, $gmt = 0) {
	switch ($type) {
		case 'mysql':
			if ($gmt) $d = gmdate('Y-m-d H:i:s');
			else $d = gmdate('Y-m-d H:i:s', (time() + (get_settings('gmt_offset') * 3600)));
			return $d;
			break;
		case 'timestamp':
			if ($gmt) $d = time();
			else $d = time() + (get_settings('gmt_offset') * 3600);
			return $d;
			break;
	}
}

function date_i18n($dateformatstring, $unixtimestamp) {
	global $month, $weekday;
	$i = $unixtimestamp; 
	if ((!empty($month)) && (!empty($weekday))) {
		$datemonth = $month[date('m', $i)];
		$dateweekday = $weekday[date('w', $i)];
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace("/([^\\\])D/", "\\1".backslashit(substr($dateweekday, 0, 3)), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])F/", "\\1".backslashit($datemonth), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])l/", "\\1".backslashit($dateweekday), $dateformatstring);
		$dateformatstring = preg_replace("/([^\\\])M/", "\\1".backslashit(substr($datemonth, 0, 3)), $dateformatstring);
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
	while ($weekday > get_settings('start_of_week')) {
		$weekday = date('w',$day);
		$day = $day - 86400;
		$i = 0;
	}
	$week['start'] = $day + 86400 - $i;
	$week['end']   = $day + 691199;
	return $week;
}

function get_lastpostdate($timezone = 'server') {
	global $cache_lastpostdate, $pagenow, $wpdb;
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastpostdate[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostdate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostdate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostdate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_date_gmt <= '$now' AND post_status = 'publish' ORDER BY post_date_gmt DESC LIMIT 1");
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
	$add_seconds_blog = get_settings('gmt_offset') * 3600;
	$add_seconds_server = date('Z');
	$now = current_time('mysql', 1);
	if ( !isset($cache_lastpostmodified[$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpostmodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpostmodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_modified_gmt <= '$now' AND post_status = 'publish' ORDER BY post_modified_gmt DESC LIMIT 1");
				break;
		}
		$lastpostdate = get_lastpostdate($timezone);
		if ($lastpostdate > $lastpostmodified) {
			$lastpostmodified = $lastpostdate;
		}
		$cache_lastpostmodified[$timezone] = $lastpostmodified;
	} else {
		$lastpostmodified = $cache_lastpostmodified[$timezone];
	}
	return $lastpostmodified;
}

function user_pass_ok($user_login,$user_pass) {
	global $cache_userdata;
	if ( empty($cache_userdata[$user_login]) ) {
		$userdata = get_userdatabylogin($user_login);
	} else {
		$userdata = $cache_userdata[$user_login];
	}
	return (md5($user_pass) == $userdata->user_pass);
}

function get_currentuserinfo() { // a bit like get_userdata(), on steroids
	global $user_login, $userdata, $user_level, $user_ID, $user_nickname, $user_email, $user_url, $user_pass_md5, $cookiehash;
	// *** retrieving user's data from cookies and db - no spoofing

	if (isset($_COOKIE['wordpressuser_' . $cookiehash])) 
		$user_login = $_COOKIE['wordpressuser_' . $cookiehash];
	$userdata = get_userdatabylogin($user_login);
	$user_level = $userdata->user_level;
	$user_ID = $userdata->ID;
	$user_nickname = $userdata->user_nickname;
	$user_email = $userdata->user_email;
	$user_url = $userdata->user_url;
	$user_pass_md5 = md5($userdata->user_pass);
}

function get_userdata($userid) {
	global $wpdb, $cache_userdata;
	$userid = (int) $userid;
	if ( empty($cache_userdata[$userid]) ) {
        $cache_userdata[$userid] = 
            $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = '$userid'");
	} 

    return $cache_userdata[$userid];
}

function get_userdatabylogin($user_login) {
	global $cache_userdata, $wpdb;
	if ( !empty($user_login) && empty($cache_userdata["$user_login"]) ) {
		$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_login = '$user_login'");
		$cache_userdata["$user_login"] = $user;
	} else {
		$user = $cache_userdata["$user_login"];
	}
	return $user;
}

function get_userid($user_login) {
	global $cache_userdata, $wpdb;
	if ( !empty($user_login) && empty($cache_userdata["$user_login"]) ) {
		$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$user_login'");

		$cache_userdata["$user_login"] = $user_id;
	} else {
		$user_id = $cache_userdata["$user_login"];
	}
	return $user_id;
}

function get_usernumposts($userid) {
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '$userid'");
}

// examine a url (supposedly from this blog) and try to
// determine the post ID it represents.
function url_to_postid($url = '') {
	global $wpdb;

	$siteurl = get_settings('home');
	// Take a link like 'http://example.com/blog/something'
	// and extract just the '/something':
	$uri = preg_replace("#$siteurl#i", '', $url);

	// on failure, preg_replace just returns the subject string
	// so if $uri and $siteurl are the same, they didn't match:
	if ($uri == $siteurl) 
		return 0;
		
	// First, check to see if there is a 'p=N' to match against:
	preg_match('#[?&]p=(\d+)#', $uri, $values);
	$p = intval($values[1]);
	if ($p) return $p;
	
	// Match $uri against our permalink structure
	$permalink_structure = get_settings('permalink_structure');
	
	// Matt's tokenizer code
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%postname%',
		'%post_id%'
	);
	$rewritereplace = array(
		'([0-9]{4})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([0-9]{1,2})?',
		'([_0-9a-z-]+)?',
		'([0-9]+)?'
	);

	// Turn the structure into a regular expression
	$matchre = str_replace('/', '/?', $permalink_structure);
	$matchre = str_replace($rewritecode, $rewritereplace, $matchre);

	// Extract the key values from the uri:
	preg_match("#$matchre#",$uri,$values);

	// Extract the token names from the structure:
	preg_match_all("#%(.+?)%#", $permalink_structure, $tokens);

	for($i = 0; $i < count($tokens[1]); $i++) {
		$name = $tokens[1][$i];
		$value = $values[$i+1];

		// Create a variable named $year, $monthnum, $day, $postname, or $post_id:
		$$name = $value;
	}
	
	// If using %post_id%, we're done:
	if (intval($post_id)) return intval($post_id);

	// Otherwise, build a WHERE clause, making the values safe along the way:
	if ($year) $where .= " AND YEAR(post_date) = '" . intval($year) . "'";
	if ($monthnum) $where .= " AND MONTH(post_date) = '" . intval($monthnum) . "'";
	if ($day) $where .= " AND DAYOFMONTH(post_date) = '" . intval($day) . "'";
	if ($hour) $where .= " AND HOUR(post_date) = '" . intval($hour) . "'";
	if ($minute) $where .= " AND MINUTE(post_date) = '" . intval($minute) . "'";
	if ($second) $where .= " AND SECOND(post_date) = '" . intval($second) . "'";
	if ($postname) $where .= " AND post_name = '" . $wpdb->escape($postname) . "' ";

	// Run the query to get the post ID:
	$id = intval($wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE 1 = 1 " . $where));

	return $id;
}


/* Options functions */

function get_settings($setting) {
	global $wpdb, $cache_settings;
	if ( strstr($_SERVER['REQUEST_URI'], 'wp-admin/install.php') || strstr($_SERVER['REQUEST_URI'], 'wp-admin/upgrade.php') )
		return false;

	if ( empty($cache_settings) )
		$cache_settings = get_alloptions();

	if ('home' == $setting && '' == $cache_settings->home)
		return $cache_settings->siteurl;

	if ( isset($cache_settings->$setting) ) :
		return $cache_settings->$setting;
	else :
		$option = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '$setting'");
		if (@ $kellogs =  unserialize($option) ) return $kellogs;
		else return $option;
	endif;
}

function get_alloptions() {
	global $wpdb;
	if ($options = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'")) {
		foreach ($options as $option) {
			// "When trying to design a foolproof system, 
			//  never underestimate the ingenuity of the fools :)" -- Dougal
			if ('siteurl' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('home' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
			if ('category_base' == $option->option_name) $option->option_value = preg_replace('|/+$|', '', $option->option_value);
		if (@ $value =  unserialize($option->option_value) )
			$all_options->{$option->option_name} = $value;
		else $value = $option->option_value;
			$all_options->{$option->option_name} = $value;
		}
	}
	return $all_options;
}

function update_option($option_name, $newvalue) {
	global $wpdb, $cache_settings;
	if ( is_array($newvalue) || is_object($value) )
		$newvalue = serialize($newvalue);

	$newvalue = trim($newvalue); // I can't think of any situation we wouldn't want to trim

    // If the new and old values are the same, no need to update.
    if ($newvalue == get_settings($option_name)) {
        return true;
    }

	$newvalue = $wpdb->escape($newvalue);
	$wpdb->query("UPDATE $wpdb->options SET option_value = '$newvalue' WHERE option_name = '$option_name'");
	$cache_settings = get_alloptions(); // Re cache settings
	return true;
}


// thx Alex Stapleton, http://alex.vort-x.net/blog/
function add_option($name, $value = '') {
	// Adds an option if it doesn't already exist
	global $wpdb;
	if ( is_array($value) || is_object($value) )
		$value = serialize($value);

	if(!get_settings($name)) {
		$name = $wpdb->escape($name);
		$value = $wpdb->escape($value);
		$wpdb->query("INSERT INTO $wpdb->options (option_name, option_value) VALUES ('$name', '$value')");

		if($wpdb->insert_id) {
			global $cache_settings;
			$cache_settings->{$name} = $value;
		}
	}
	return;
}

function delete_option($name) {
	global $wpdb;
	// Get the ID, if no ID then return
	$option_id = $wpdb->get_var("SELECT option_id FROM $wpdb->options WHERE option_name = '$name'");
	if (!$option_id) return false;
	$wpdb->query("DELETE FROM $wpdb->optiongroup_options WHERE option_id = '$option_id'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = '$name'");
	return true;
}

function get_postdata($postid) {
	global $post, $wpdb;

	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$postid'");
	
	$postdata = array (
		'ID' => $post->ID, 
		'Author_ID' => $post->post_author, 
		'Date' => $post->post_date, 
		'Content' => $post->post_content, 
		'Excerpt' => $post->post_excerpt, 
		'Title' => $post->post_title, 
		'Category' => $post->post_category,
		'Lat' => $post->post_lat,
		'Lon' => $post->post_lon,
		'post_status' => $post->post_status,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_password' => $post->post_password,
		'to_ping' => $post->to_ping,
		'pinged' => $post->pinged,
		'post_name' => $post->post_name
	);
	return $postdata;
}

function get_commentdata($comment_ID,$no_cache=0,$include_unapproved=false) { // less flexible, but saves DB queries
	global $postc,$id,$commentdata, $wpdb;
	if ($no_cache) {
		$query = "SELECT * FROM $wpdb->comments WHERE comment_ID = '$comment_ID'";
		if (false == $include_unapproved) {
		    $query .= " AND comment_approved = '1'";
		}
    		$myrow = $wpdb->get_row($query, ARRAY_A);
	} else {
		$myrow['comment_ID']=$postc->comment_ID;
		$myrow['comment_post_ID']=$postc->comment_post_ID;
		$myrow['comment_author']=$postc->comment_author;
		$myrow['comment_author_email']=$postc->comment_author_email;
		$myrow['comment_author_url']=$postc->comment_author_url;
		$myrow['comment_author_IP']=$postc->comment_author_IP;
		$myrow['comment_date']=$postc->comment_date;
		$myrow['comment_content']=$postc->comment_content;
		$myrow['comment_karma']=$postc->comment_karma;
        $myrow['comment_approved']=$postc->comment_approved;
		if (strstr($myrow['comment_content'], '<trackback />')) {
			$myrow['comment_type'] = 'trackback';
		} elseif (strstr($myrow['comment_content'], '<pingback />')) {
			$myrow['comment_type'] = 'pingback';
		} else {
			$myrow['comment_type'] = 'comment';
		}
	}
	return $myrow;
}

function get_catname($cat_ID) {
	global $cache_catnames, $wpdb;
	if ( !$cache_catnames ) {
        $results = $wpdb->get_results("SELECT * FROM $wpdb->categories") or die('Oops, couldn\'t query the db for categories.');
		foreach ($results as $post) {
			$cache_catnames[$post->cat_ID] = $post->cat_name;
		}
	}
	$cat_name = $cache_catnames[$cat_ID];
	return $cat_name;
}

function gzip_compression() {
	if ( strstr($_SERVER['PHP_SELF'], 'wp-admin') ) return false;
	if ( !get_settings('gzipcompression') ) return false;

	if( extension_loaded('zlib') ) {
		ob_start('ob_gzhandler');
	}
}


// functions to count the page generation time (from phpBB2)
// ( or just any time between timer_start() and timer_stop() )

function timer_start() {
	global $timestart;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timestart = $mtime;
	return true;
}

function timer_stop($display = 0, $precision = 3) { //if called like timer_stop(1), will echo $timetotal
	global $timestart, $timeend;
	$mtime = microtime();
	$mtime = explode(' ',$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$timeend = $mtime;
	$timetotal = $timeend-$timestart;
	if ($display)
		echo number_format($timetotal,$precision);
	return $timetotal;
}

function weblog_ping($server = '', $path = '') {
	$debug = false;
	include_once (ABSPATH . WPINC . '/class-xmlrpc.php');
	include_once (ABSPATH . WPINC . '/class-xmlrpcs.php');

	$f = new xmlrpcmsg('weblogUpdates.ping',
		array(new xmlrpcval(get_settings('blogname'), 'string'),
			new xmlrpcval(get_settings('home') ,'string')));
	$c = new xmlrpc_client($path, $server, 80);
	$r = $c->send($f);

	if ('0' != $r) {	
		if ($debug) {
			echo "<h3>Response Object Dump:</h3>
				<pre>\n";
			print_r($r);
			echo "</pre>\n";
		}

		$v = @phpxmlrpc_decode($r->value());
		if (!$r->faultCode()) {
			$result['message'] =  "<p class=\"rpcmsg\">";
			$result['message'] = $result['message'] .  $v["message"] . "<br />\n";
			$result['message'] = $result['message'] . "</p>";
		} else {
			$result['err'] = $r->faultCode();
			$result['message'] =  "<!--\n";
			$result['message'] = $result['message'] . "Fault: ";
			$result['message'] = $result['message'] . "Code: " . $r->faultCode();
			$result['message'] = $result['message'] . " Reason '" .$r->faultString()."'<BR>";
			$result['message'] = $result['message'] . "-->\n";
		}

		if ($debug) print '<blockquote>' . $result['message'] . '</blockquote>';
	}
}

function generic_ping($post_id = 0) {
	$services = get_settings('ping_sites');
	$services = preg_replace("|(\s)+|", '$1', $services); // Kill dupe lines
	$services = trim($services);
	if ('' != $services) {
		$services = explode("\n", $services);
		foreach ($services as $service) {
			$uri = parse_url($service);
			weblog_ping($uri['host'], $uri['path']);
		}
	}
}

add_action('publish_post', 'generic_ping');

// Send a Trackback
function trackback($trackback_url, $title, $excerpt, $ID) {
	global $wpdb;
	$title = urlencode($title);
	$excerpt = urlencode($excerpt);
	$blog_name = urlencode(get_settings('blogname'));
	$tb_url = $trackback_url;
	$url = urlencode(get_permalink($ID));
	$query_string = "title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";
	$trackback_url = parse_url($trackback_url);
	$http_request  = 'POST ' . $trackback_url['path'] . ($trackback_url['query'] ? '?'.$trackback_url['query'] : '') . " HTTP/1.0\r\n";
	$http_request .= 'Host: '.$trackback_url['host']."\r\n";
	$http_request .= 'Content-Type: application/x-www-form-urlencoded; charset='.get_settings('blog_charset')."\r\n";
	$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
	$http_request .= "\r\n";
	$http_request .= $query_string;
	$fs = @fsockopen($trackback_url['host'], 80);
	@fputs($fs, $http_request);
/*
	$debug_file = 'trackback.log';
	$fp = fopen($debug_file, 'a');
	fwrite($fp, "\n*****\nRequest:\n\n$http_request\n\nResponse:\n\n");
	while(!@feof($fs)) {
		fwrite($fp, @fgets($fs, 4096));
	}
	fwrite($fp, "\n\n");
	fclose($fp);
*/
	@fclose($fs);

	$wpdb->query("UPDATE $wpdb->posts SET pinged = CONCAT(pinged, '\n', '$tb_url') WHERE ID = '$ID'");
	$wpdb->query("UPDATE $wpdb->posts SET to_ping = REPLACE(to_ping, '$tb_url', '') WHERE ID = '$ID'");
	return $result;
}

// trackback - reply
function trackback_response($error = 0, $error_message = '') {
	if ($error) {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
	} else {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
	die();
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
		$link_url = (strtolower(substr($link_url,0,7)) != 'http://') ? get_settings('home') . $link_url : $link_url;
		$links_summary .= "\n".$link_number.' '.$link_url;
	}
	$content = strip_tags($content);
	$content .= $links_summary;
	return $content;
}


function xmlrpc_getposttitle($content) {
	global $post_default_title;
	if (preg_match('/<title>(.+?)<\/title>/is', $content, $matchtitle)) {
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
	if (preg_match('/<category>(.+?)<\/category>/is', $content, $matchcat)) {
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
	if ($debug == 1) {
		$fp = fopen($filename, $mode);
		return $fp;
	} else {
		return false;
	}
}

function debug_fwrite($fp, $string) {
	global $debug;
	if ($debug == 1) {
		fwrite($fp, $string);
	}
}

function debug_fclose($fp) {
	global $debug;
	if ($debug == 1) {
		fclose($fp);
	}
}

function pingback($content, $post_ID) {
include_once (ABSPATH . WPINC . '/class-xmlrpc.php');
include_once (ABSPATH . WPINC . '/class-xmlrpcs.php');
	// original code by Mort (http://mort.mine.nu:8080)
	global $wp_version;
	$log = debug_fopen('./pingback.log', 'a');
	$post_links = array();
	debug_fwrite($log, 'BEGIN '.time()."\n");

	// Variables
	$ltrs = '\w';
	$gunk = '/#~:.?+=&%@!\-';
	$punc = '.:?\-';
	$any = $ltrs.$gunk.$punc;
	$pingback_str_dquote = 'rel="pingback"';
	$pingback_str_squote = 'rel=\'pingback\'';
	$x_pingback_str = 'x-pingback: ';
	$pingback_href_original_pos = 27;

	// Step 1
	// Parsing the post, external links (if any) are stored in the $post_links array
	// This regexp comes straight from phpfreaks.com
	// http://www.phpfreaks.com/quickcode/Extract_All_URLs_on_a_Page/15.php
	preg_match_all("{\b http : [$any] +? (?= [$punc] * [^$any] | $)}x", $content, $post_links_temp);

	// Debug
	debug_fwrite($log, 'Post contents:');
	debug_fwrite($log, $content."\n");
	
	// Step 2.
	// Walking thru the links array
	// first we get rid of links pointing to sites, not to specific files
	// Example:
	// http://dummy-weblog.org
	// http://dummy-weblog.org/
	// http://dummy-weblog.org/post.php
	// We don't wanna ping first and second types, even if they have a valid <link/>

	foreach($post_links_temp[0] as $link_test){
		$test = parse_url($link_test);
		if (isset($test['query'])) {
			$post_links[] = $link_test;
		} elseif(($test['path'] != '/') && ($test['path'] != '')) {
			$post_links[] = $link_test;
		}
	}

	foreach ($post_links as $pagelinkedto){
		debug_fwrite($log, 'Processing -- '.$pagelinkedto."\n\n");

		$bits = parse_url($pagelinkedto);
		if (!isset($bits['host'])) {
			debug_fwrite($log, 'Couldn\'t find a hostname for '.$pagelinkedto."\n\n");
			continue;
		}
		$host = $bits['host'];
		$path = isset($bits['path']) ? $bits['path'] : '';
		if (isset($bits['query'])) {
			$path .= '?'.$bits['query'];
		}
		if (!$path) {
			$path = '/';
		}
		$port = isset($bits['port']) ? $bits['port'] : 80;

		// Try to connect to the server at $host
		$fp = fsockopen($host, $port, $errno, $errstr, 30);
		if (!$fp) {
			debug_fwrite($log, 'Couldn\'t open a connection to '.$host."\n\n");
			continue;
		}

		// Send the GET request
		$request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: WordPress/$wp_version PHP/" . phpversion() . "\r\n\r\n";
		ob_end_flush();
		fputs($fp, $request);

		// Start receiving headers and content
		$contents = '';
		$headers = '';
		$gettingHeaders = true;
		$found_pingback_server = 0;
		while (!feof($fp)) {
			$line = fgets($fp, 4096);
			if (trim($line) == '') {
				$gettingHeaders = false;
			}
			if (!$gettingHeaders) {
				$contents .= trim($line)."\n";
				$pingback_link_offset_dquote = strpos($contents, $pingback_str_dquote);
				$pingback_link_offset_squote = strpos($contents, $pingback_str_squote);
			} else {
				$headers .= trim($line)."\n";
				$x_pingback_header_offset = strpos(strtolower($headers), $x_pingback_str);
			}
			if ($x_pingback_header_offset) {
				preg_match('#x-pingback: (.+)#is', $headers, $matches);
				$pingback_server_url = trim($matches[1]);
				debug_fwrite($log, "Pingback server found from X-Pingback header @ $pingback_server_url\n");
				$found_pingback_server = 1;
				break;
			}
			if ($pingback_link_offset_dquote || $pingback_link_offset_squote) {
				$quote = ($pingback_link_offset_dquote) ? '"' : '\'';
				$pingback_link_offset = ($quote=='"') ? $pingback_link_offset_dquote : $pingback_link_offset_squote;
				$pingback_href_pos = @strpos($contents, 'href=', $pingback_link_offset);
				$pingback_href_start = $pingback_href_pos+6;
				$pingback_href_end = @strpos($contents, $quote, $pingback_href_start);
				$pingback_server_url_len = $pingback_href_end-$pingback_href_start;
				$pingback_server_url = substr($contents, $pingback_href_start, $pingback_server_url_len);
				debug_fwrite($log, "Pingback server found from Pingback <link /> tag @ $pingback_server_url\n");
				$found_pingback_server = 1;
				break;
			}
		}

		if (!$found_pingback_server) {
			debug_fwrite($log, "Pingback server not found\n\n*************************\n\n");
			@fclose($fp);
		} else {
			debug_fwrite($log,"\n\nPingback server data\n");

			// Assuming there's a "http://" bit, let's get rid of it
			$host_clear = substr($pingback_server_url, 7);

			//  the trailing slash marks the end of the server name
			$host_end = strpos($host_clear, '/');

			// Another clear cut
			$host_len = $host_end-$host_start;
			$host = substr($host_clear, 0, $host_len);
			debug_fwrite($log, 'host: '.$host."\n");

			// If we got the server name right, the rest of the string is the server path
			$path = substr($host_clear,$host_end);
			debug_fwrite($log, 'path: '.$path."\n\n");

			 // Now, the RPC call
			$method = 'pingback.ping';
			debug_fwrite($log, 'Page Linked To: '.$pagelinkedto."\n");
			debug_fwrite($log, 'Page Linked From: ');
			$pagelinkedfrom = get_permalink($post_ID);
			debug_fwrite($log, $pagelinkedfrom."\n");

			$client = new xmlrpc_client($path, $host, 80);
			$message = new xmlrpcmsg($method, array(new xmlrpcval($pagelinkedfrom), new xmlrpcval($pagelinkedto)));
			$result = $client->send($message);
			if ($result){
				if (!$result->value()){
					debug_fwrite($log, $result->faultCode().' -- '.$result->faultString());
				} else {
					$value = phpxmlrpc_decode($result->value());
					if (is_array($value)) {
						$value_arr = '';
						foreach($value as $blah) {
							$value_arr .= $blah.' |||| ';
						}
						debug_fwrite($log, $value_arr);
					} else {
						debug_fwrite($log, $value);
					}
				}
			}
			@fclose($fp);
		}
	}

	debug_fwrite($log, "\nEND: ".time()."\n****************************\n\r");
	debug_fclose($log);
}

function doGeoUrlHeader($post_list = '') {
    global $posts;

  if (get_settings('use_geo_positions')) {
		if ($posts && 1 === count($posts) && ! empty($posts[0]->post_lat)) {
			// there's only one result  see if it has a geo code
			$row = $posts[0];
			$lat = $row->post_lat;
			$lon = $row->post_lon;
			$title = $row->post_title;
			if(($lon != null) && ($lat != null) ) {
				echo "<meta name=\"ICBM\" content=\"".$lat.", ".$lon."\" />\n";
				echo "<meta name=\"DC.title\" content=\"".convert_chars(strip_tags(htmlspecialchars(get_bloginfo("name"))))." - ".$title."\" />\n";
				echo "<meta name=\"geo.position\" content=\"".$lat.";".$lon."\" />\n";
				return;
			}
		} else {
			if(get_settings('use_default_geourl')) {
				// send the default here 
				echo "<meta name='ICBM' content=\"". get_settings('default_geourl_lat') .", ". get_settings('default_geourl_lon') ."\" />\n";
				echo "<meta name='DC.title' content=\"".convert_chars(strip_tags(htmlspecialchars(get_bloginfo("name"))))."\" />\n";
				echo "<meta name='geo.position' content=\"". get_settings('default_geourl_lat') .";". get_settings('default_geourl_lon') ."\" />\n";
			}
		}
	}
}

function getRemoteFile($host,$path) {
    $fp = fsockopen($host, 80, $errno, $errstr);
    if ($fp) {
        fputs($fp,"GET $path HTTP/1.0\r\nHost: $host\r\n\r\n");
        while ($line = fgets($fp, 4096)) {
            $lines[] = $line;
        }
        fclose($fp);
        return $lines;
    } else {
        return false;
    }
}

function pingGeoURL($blog_ID) {

    $ourUrl = get_settings('home') ."/index.php?p=".$blog_ID;
    $host="geourl.org";
    $path="/ping/?p=".$ourUrl;
    getRemoteFile($host,$path); 
}

/* wp_set_comment_status:
   part of otaku42's comment moderation hack
   changes the status of a comment according to $comment_status.
   allowed values:
   hold   : set comment_approve field to 0
   approve: set comment_approve field to 1
   delete : remove comment out of database
   
   returns true if change could be applied
   returns false on database error or invalid value for $comment_status
 */
function wp_set_comment_status($comment_id, $comment_status) {
    global $wpdb;

    switch($comment_status) {
		case 'hold':
			$query = "UPDATE $wpdb->comments SET comment_approved='0' WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		case 'approve':
			$query = "UPDATE $wpdb->comments SET comment_approved='1' WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		case 'delete':
			$query = "DELETE FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1";
		break;
		default:
			return false;
    }
    
    if ($wpdb->query($query)) {
		do_action('wp_set_comment_status', $comment_id);
		return true;
    } else {
		return false;
    }
}


/* wp_get_comment_status
   part of otaku42's comment moderation hack
   gets the current status of a comment

   returned values:
   "approved"  : comment has been approved
   "unapproved": comment has not been approved
   "deleted   ": comment not found in database

   a (boolean) false signals an error
 */
function wp_get_comment_status($comment_id) {
    global $wpdb;
    
    $result = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    if ($result == NULL) {
        return "deleted";
    } else if ($result == "1") {
        return "approved";
    } else if ($result == "0") {
        return "unapproved";
    } else {
        return false;
    }
}

function wp_notify_postauthor($comment_id, $comment_type='comment') {
    global $wpdb;
    global $querystring_start, $querystring_equal, $querystring_separator;
    
    $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
    $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

    if ('' == $user->user_email) return false; // If there's no email to send the comment to

	$comment_author_domain = gethostbyaddr($comment->comment_author_IP);

	$blogname = get_settings('blogname');
	
	if ('comment' == $comment_type) {
		$notify_message  = "New comment on your post #$comment->comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Author : $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
		$notify_message .= "E-mail : $comment->comment_author_email\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=$comment->comment_author_IP\r\n";
		$notify_message .= "Comment:\r\n".$comment->comment_content."\r\n\r\n";
		$notify_message .= "You can see all comments on this post here: \r\n";
		$subject = '[' . $blogname . '] Comment: "' .$post->post_title.'"';
	} elseif ('trackback' == $comment_type) {
		$notify_message  = "New trackback on your post #$comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Website: $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Excerpt: \n".$comment->comment_content."\r\n\r\n";
		$notify_message .= "You can see all trackbacks on this post here: \r\n";
		$subject = '[' . $blogname . '] Trackback: "' .$post->post_title.'"';
	} elseif ('pingback' == $comment_type) {
		$notify_message  = "New pingback on your post #$comment_post_ID \"".$post->post_title."\"\r\n\r\n";
		$notify_message .= "Website: $comment->comment_author\r\n";
		$notify_message .= "URI    : $comment->comment_author_url\r\n";
		$notify_message .= "Excerpt: \n[...] $original_context [...]\r\n\r\n";
		$notify_message .= "You can see all pingbacks on this post here: \r\n";
		$subject = '[' . $blogname . '] Pingback: "' .$post->post_title.'"';
	}
	$notify_message .= get_permalink($comment->comment_post_ID) . '#comments';

	if ('' == $comment->comment_author_email || '' == $comment->comment_author) {
		$from = "From: \"$blogname\" <wordpress@" . $_SERVER['SERVER_NAME'] . '>';
	} else {
		$from = 'From: "' . $comment->comment_author . "\" <$comment->comment_author_email>";
	}

	$message_headers = "MIME-Version: 1.0\r\n"
		. "$from\r\n"
		. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";

	@mail($user->user_email, $subject, $notify_message, $message_headers);
   
    return true;
}

/* wp_notify_moderator
   notifies the moderator of the blog (usually the admin)
   about a new comment that waits for approval
   always returns true
 */
function wp_notify_moderator($comment_id) {
    global $wpdb;
    global $querystring_start, $querystring_equal, $querystring_separator;
    
    $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
    $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");
    $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID='$post->post_author' LIMIT 1");

    $comment_author_domain = gethostbyaddr($comment->comment_author_IP);
    $comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

    $notify_message  = "A new comment on the post #$comment->comment_post_ID \"".$post->post_title."\" is waiting for your approval\r\n\r\n";
    $notify_message .= "Author : $comment->comment_author (IP: $comment->comment_author_IP , $comment_author_domain)\r\n";
    $notify_message .= "E-mail : $comment->comment_author_email\r\n";
    $notify_message .= "URL    : $comment->comment_author_url\r\n";
    $notify_message .= "Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=$comment->comment_author_IP\r\n";
    $notify_message .= "Comment:\r\n".$comment->comment_content."\r\n\r\n";
    $notify_message .= "To approve this comment, visit: " . get_settings('siteurl') . "/wp-admin/post.php?action=mailapprovecomment&p=".$comment->comment_post_ID."&comment=$comment_id\r\n";
    $notify_message .= "To delete this comment, visit: " . get_settings('siteurl') . "/wp-admin/post.php?action=confirmdeletecomment&p=".$comment->comment_post_ID."&comment=$comment_id\r\n";
    $notify_message .= "Currently $comments_waiting comments are waiting for approval. Please visit the moderation panel:\r\n";
    $notify_message .= get_settings('siteurl') . "/wp-admin/moderation.php\r\n";

    $subject = '[' . get_settings('blogname') . '] Please approve: "' .$post->post_title.'"';
    $admin_email = get_settings("admin_email");
    $from  = "From: $admin_email";

    $message_headers = "MIME-Version: 1.0\r\n"
    	. "$from\r\n"
    	. "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";

    @mail($admin_email, $subject, $notify_message, $message_headers);
    
    return true;
}


function start_wp($use_wp_query = false) {
  global $post, $id, $postdata, $authordata, $day, $preview, $page, $pages, $multipage, $more, $numpages, $wp_query;
	global $pagenow;

	if ($use_wp_query) {
	  $post = $wp_query->next_post();
	} else {
	  $wp_query->next_post();
	}

	if (!$preview) {
		$id = $post->ID;
	} else {
		$id = 0;
		$postdata = array (
			'ID' => 0,
			'Author_ID' => $_GET['preview_userid'],
			'Date' => $_GET['preview_date'],
			'Content' => $_GET['preview_content'],
			'Excerpt' => $_GET['preview_excerpt'],
			'Title' => $_GET['preview_title'],
			'Category' => $_GET['preview_category'],
			'Notify' => 1
			);
	}
	$authordata = get_userdata($post->post_author);

	$day = mysql2date('d.m.y', $post->post_date);
	$currentmonth = mysql2date('m', $post->post_date);
	$numpages = 1;
	if (!$page)
		$page = 1;
	if (isset($p))
		$more = 1;
	$content = $post->post_content;
	if (preg_match('/<!--nextpage-->/', $post->post_content)) {
		if ($page > 1)
			$more = 1;
		$multipage = 1;
		$content = $post->post_content;
		$content = str_replace("\n<!--nextpage-->\n", '<!--nextpage-->', $content);
		$content = str_replace("\n<!--nextpage-->", '<!--nextpage-->', $content);
		$content = str_replace("<!--nextpage-->\n", '<!--nextpage-->', $content);
		$pages = explode('<!--nextpage-->', $content);
		$numpages = count($pages);
	} else {
		$pages[0] = $post->post_content;
		$multipage = 0;
	}
	return true;
}

function is_new_day() {
	global $day, $previousday;
	if ($day != $previousday) {
		return(1);
	} else {
		return(0);
	}
}

// Filters: these are the core of WP's plugin architecture

function apply_filters($tag, $string) {
	global $wp_filter;
	if (isset($wp_filter['all'])) {
		foreach ($wp_filter['all'] as $priority => $functions) {
			if (isset($wp_filter[$tag][$priority]))
				$wp_filter[$tag][$priority] = array_merge($wp_filter['all'][$priority], $wp_filter[$tag][$priority]);
			else
				$wp_filter[$tag][$priority] = array_merge($wp_filter['all'][$priority], array());
			$wp_filter[$tag][$priority] = array_unique($wp_filter[$tag][$priority]);
		}

	}
	
	if (isset($wp_filter[$tag])) {
		ksort($wp_filter[$tag]);
		foreach ($wp_filter[$tag] as $priority => $functions) {
			if (!is_null($functions)) {
                foreach($functions as $function) {
					$string = $function($string);
                }
            }
		}
	}
	return $string;
}

function add_filter($tag, $function_to_add, $priority = 10) {
	global $wp_filter;
	// So the format is wp_filter['tag']['array of priorities']['array of functions']
	if (!@in_array($function_to_add, $wp_filter[$tag]["$priority"])) {
		$wp_filter[$tag]["$priority"][] = $function_to_add;
	}
	return true;
}

function remove_filter($tag, $function_to_remove, $priority = 10) {
	global $wp_filter;
	if (@in_array($function_to_remove, $wp_filter[$tag]["$priority"])) {
		foreach ($wp_filter[$tag]["$priority"] as $function) {
			if ($function_to_remove != $function) {
				$new_function_list[] = $function;
			}
		}
		$wp_filter[$tag]["$priority"] = $new_function_list;
	}
	//die(var_dump($wp_filter));
	return true;
}

// The *_action functions are just aliases for the *_filter functions, they take special strings instead of generic content

function do_action($tag, $string) {
	return apply_filters($tag, $string);
}

function add_action($tag, $function_to_add, $priority = 10) {
	add_filter($tag, $function_to_add, $priority);
}

function remove_action($tag, $function_to_remove, $priority = 10) {
	remove_filter($tag, $function_to_remove, $priority);
}

function using_mod_rewrite($permalink_structure = '') {
    if (empty($permalink_structure)) {
        $permalink_structure = get_settings('permalink_structure');
	
        if (empty($permalink_structure)) {
            return false;
        }
    }

    // If the index is not in the permalink, we're using mod_rewrite.
    if (! preg_match('#^/*' . get_settings('blogfilename') . '#', $permalink_structure)) {
      return true;
    }
    
    return false;
}

function preg_index($number, $matches = '') {
    $match_prefix = '$';
    $match_suffix = '';
    
    if (! empty($matches)) {
        $match_prefix = '$' . $matches . '['; 
        $match_suffix = ']';
    }        
    
    return "$match_prefix$number$match_suffix";        
}


function page_permastruct() {
    $permalink_structure = get_settings('permalink_structure');
        
    if (empty($permalink_structure)) {
        return '';
    }

    $front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));    
    $index = get_settings('blogfilename');
    $prefix = '';
    if (preg_match('#^/*' . $index . '#', $front)) {
        $prefix = $index . '/';
    }

    return '/' . $prefix . 'site/%pagename%';    
}

function generate_rewrite_rules($permalink_structure = '', $matches = '') {
    $rewritecode = 
	array(
	'%year%',
	'%monthnum%',
	'%day%',
	'%hour%',
	'%minute%',
	'%second%',
	'%postname%',
	'%post_id%',
	'%category%',
	'%author%',
	'%pagename%',
	'%search%'
	);

    $rewritereplace = 
	array(
	'([0-9]{4})',
	'([0-9]{1,2})',
	'([0-9]{1,2})',
	'([0-9]{1,2})',
	'([0-9]{1,2})',
	'([0-9]{1,2})',
	'([_0-9a-z-]+)',
	'([0-9]+)',
	'([/_0-9a-z-]+)',
	'([_0-9a-z-]+)',
	'([_0-9a-z-]+)',
	'(.+)'
	);

    $queryreplace = 
	array (
	'year=',
	'monthnum=',
	'day=',
	'hour=',
	'minute=',
	'second=',
	'name=',
	'p=',
	'category_name=',
	'author_name=',
	'pagename=',
	's='
	);

    $feedregex = '(feed|rdf|rss|rss2|atom)/?$';
    $trackbackregex = 'trackback/?$';
    $pageregex = 'page/?([0-9]{1,})/?$';

    $front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));    
    preg_match_all('/%.+?%/', $permalink_structure, $tokens);

    $num_tokens = count($tokens[0]);

    $index = get_settings('blogfilename');;
    $feedindex = $index;
    $trackbackindex = $index;
    for ($i = 0; $i < $num_tokens; ++$i) {
             if (0 < $i) {
                 $queries[$i] = $queries[$i - 1] . '&';
             }
             
             $query_token = str_replace($rewritecode, $queryreplace, $tokens[0][$i]) . preg_index($i+1, $matches);
             $queries[$i] .= $query_token;
             }

    $structure = $permalink_structure;
    if ($front != '/') {
        $structure = str_replace($front, '', $structure);
    }
    $structure = trim($structure, '/');
    $dirs = explode('/', $structure);
    $num_dirs = count($dirs);

    $front = preg_replace('|^/+|', '', $front);

    $post_rewrite = array();
    $struct = $front;
    for ($j = 0; $j < $num_dirs; ++$j) {
        $struct .= $dirs[$j] . '/';
        $match = str_replace($rewritecode, $rewritereplace, $struct);
        $num_toks = preg_match_all('/%.+?%/', $struct, $toks);
        $query = $queries[$num_toks - 1];

        $pagematch = $match . $pageregex;
        $pagequery = $index . '?' . $query . '&paged=' . preg_index($num_toks + 1, $matches);

        $feedmatch = $match . $feedregex;
        $feedquery = $feedindex . '?' . $query . '&feed=' . preg_index($num_toks + 1, $matches);

        $post = 0;
        if (strstr($struct, '%postname%') || strstr($struct, '%post_id%')
            || (strstr($struct, '%year%') &&  strstr($struct, '%monthnum%') && strstr($struct, '%day%') && strstr($struct, '%hour%') && strstr($struct, '%minute') && strstr($struct, '%second%'))) {
                $post = 1;
                $trackbackmatch = $match . $trackbackregex;
                $trackbackquery = $trackbackindex . '?' . $query . '&tb=1';
                $match = $match . '?([0-9]+)?/?$';
                $query = $index . '?' . $query . '&page=' . preg_index($num_toks + 1, $matches);
        } else {
            $match .= '?$';
            $query = $index . '?' . $query;
        }
        
        $post_rewrite = array($feedmatch => $feedquery, $pagematch => $pagequery, $match => $query) + $post_rewrite;

        if ($post) {
            $post_rewrite = array($trackbackmatch => $trackbackquery) + $post_rewrite;
        }
    }

    return $post_rewrite;
}

/* rewrite_rules
 * Construct rewrite matches and queries from permalink structure.
 * matches - The name of the match array to use in the query strings.
 *           If empty, $1, $2, $3, etc. are used.
 * Returns an associate array of matches and queries.
 */
function rewrite_rules($matches = '', $permalink_structure = '') {
    $rewrite = array();

    if (empty($permalink_structure)) {
        $permalink_structure = get_settings('permalink_structure');
        
        if (empty($permalink_structure)) {
            return $rewrite;
        }
    }

    $post_rewrite = generate_rewrite_rules($permalink_structure, $matches);

    $feedregex = '(feed|rdf|rss|rss2|atom)/?$';
    $pageregex = 'page/?([0-9]{1,})/?$';
    $front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));    
    $index = get_settings('blogfilename');
    $prefix = '';
    if (! using_mod_rewrite($permalink_structure)) {
        $prefix = $index . '/';
    }

    // If the permalink does not have year, month, and day, we need to create a
    // separate archive rule.
    $doarchive = false;
    if (! (strstr($permalink_structure, '%year%') && strstr($permalink_structure, '%monthnum%') && strstr($permalink_structure, '%day%')) ||
        preg_match('/%category%.*(%year%|%monthnum%|%day%)/', $permalink_structure)) {
        $doarchive = true;
        $archive_structure = $front . '%year%/%monthnum%/%day%/';
        $archive_rewrite =  generate_rewrite_rules($archive_structure, $matches);
    }

    // Site feed
    $sitefeedmatch = $prefix . 'feed/?([_0-9a-z-]+)?/?$';
    $sitefeedquery = 'index.php?feed=_' . preg_index(1, $matches);

    // Site comment feed
    $sitecommentfeedmatch = $prefix . 'comments/feed/?([_0-9a-z-]+)?/?$';
    $sitecommentfeedquery = 'index.php?feed=_' . preg_index(1, $matches) . '&withcomments=1';

    // Site page
    $sitepagematch = $prefix . $pageregex;
    $sitepagequery = 'index.php?paged=' . preg_index(1, $matches);

    $site_rewrite = array(
                     $sitefeedmatch => $sitefeedquery,
                     $sitecommentfeedmatch => $sitecommentfeedquery,
                     $sitepagematch => $sitepagequery,
                     );

    // Search
    $search_structure = $prefix . "search/%search%";
    $search_rewrite = generate_rewrite_rules($search_structure, $matches);

    // Categories
	if ( '' == get_settings('category_base') )
		$category_structure = $front . 'category/';
	else
	    $category_structure = get_settings('category_base') . '/';

    $category_structure = $category_structure . '%category%';
    $category_rewrite = generate_rewrite_rules($category_structure, $matches);

    // Authors
    $author_structure = $front . 'author/%author%';
    $author_rewrite = generate_rewrite_rules($author_structure, $matches);

    // Site static pages
    $page_structure = $prefix . 'site/%pagename%';
    $page_rewrite = generate_rewrite_rules($page_structure, $matches);

    // Put them together.
    $rewrite = $site_rewrite + $page_rewrite + $search_rewrite + $category_rewrite + $author_rewrite;

    // Add on archive rewrite rules if needed.
    if ($doarchive) {
        $rewrite = $rewrite + $archive_rewrite;
    }

    $rewrite = $rewrite + $post_rewrite;

    $rewrite = apply_filters('rewrite_rules_array', $rewrite);
    return $rewrite;
}

function mod_rewrite_rules ($permalink_structure) {
    $site_root = str_replace('http://', '', trim(get_settings('siteurl')));
    $site_root = preg_replace('|([^/]*)(.*)|i', '$2', $site_root);
    if ('/' != substr($site_root, -1)) $site_root = $site_root . '/';
    
    $home_root = str_replace('http://', '', trim(get_settings('home')));
    $home_root = preg_replace('|([^/]*)(.*)|i', '$2', $home_root);
    if ('/' != substr($home_root, -1)) $home_root = $home_root . '/';
    
    $rules = "RewriteEngine On\n";
    $rules .= "RewriteBase $home_root\n";
    $rewrite = rewrite_rules('', $permalink_structure);
    foreach ($rewrite as $match => $query) {
        if (strstr($query, 'index.php')) {
            $rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA]\n";
        } else {
            $rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA]\n";
        }
    }

    $rules = apply_filters('rewrite_rules', $rules);

    return $rules;
}

function get_posts($args) {
	global $wpdb;
	parse_str($args, $r);
	if (!isset($r['numberposts'])) $r['numberposts'] = 5;
	if (!isset($r['offset'])) $r['offset'] = 0;
	// The following not implemented yet
	if (!isset($r['category'])) $r['category'] = '';
	if (!isset($r['orderby'])) $r['orderby'] = '';
	if (!isset($r['order'])) $r['order'] = '';

	$now = current_time('mysql');

	$posts = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->posts WHERE post_date <= '$now' AND (post_status = 'publish') GROUP BY $wpdb->posts.ID ORDER BY post_date DESC LIMIT " . $r['offset'] . ',' . $r['numberposts']);

    update_post_caches($posts);
	
	return $posts;
}

function check_comment($author, $email, $url, $comment, $user_ip) {
	if (1 == get_settings('comment_moderation')) return false; // If moderation is set to manual

	if ( (count(explode('http:', $comment)) - 1) >= get_settings('comment_max_links') )
		return false; // Check # of external links

	if ('' == trim( get_settings('moderation_keys') ) ) return true; // If moderation keys are empty
	$words = explode("\n", get_settings('moderation_keys') );
	foreach ($words as $word) {
		$word = trim($word);

		// Skip empty lines
		if (empty($word)) { continue; }

		$pattern = "#$word#i";
		if ( preg_match($pattern, $author) ) return false;
		if ( preg_match($pattern, $email) ) return false;
		if ( preg_match($pattern, $url) ) return false;
		if ( preg_match($pattern, $comment) ) return false;
		if ( preg_match($pattern, $user_ip) ) return false;
	}

	return true;
}

function query_posts($query) {
    global $wp_query;

    return $wp_query->query($query);
}

function update_post_caches($posts) {
    global $category_cache, $comment_count_cache, $post_meta_cache;
    global $wpdb;

    // No point in doing all this work if we didn't match any posts.
    if (! $posts) {
        return;
    }

    // Get the categories for all the posts
    foreach ($posts as $post) {
        $post_id_list[] = $post->ID;
    }
    $post_id_list = implode(',', $post_id_list);

    $dogs = $wpdb->get_results("SELECT DISTINCT
        ID, category_id, cat_name, category_nicename, category_description, category_parent
        FROM $wpdb->categories, $wpdb->post2cat, $wpdb->posts
        WHERE category_id = cat_ID AND post_id = ID AND post_id IN ($post_id_list)");
        
    if (!empty($dogs)) {
        foreach ($dogs as $catt) {
            $category_cache[$catt->ID][] = $catt;
        }
    }

    // Do the same for comment numbers
    $comment_counts = $wpdb->get_results("SELECT ID, COUNT( comment_ID ) AS ccount
        FROM $wpdb->posts
        LEFT JOIN $wpdb->comments ON ( comment_post_ID = ID  AND comment_approved =  '1')
        WHERE post_status =  'publish' AND ID IN ($post_id_list)
        GROUP BY ID");
    
    if ($comment_counts) {
        foreach ($comment_counts as $comment_count) {
            $comment_count_cache["$comment_count->ID"] = $comment_count->ccount;
        }
    }

    // Get post-meta info
    if ( $meta_list = $wpdb->get_results("
			SELECT post_id,meta_key,meta_value 
			FROM $wpdb->postmeta 
			WHERE post_id IN($post_id_list)
			ORDER BY post_id,meta_key
		", ARRAY_A) ) {
		
        // Change from flat structure to hierarchical:
        $post_meta_cache = array();
        foreach ($meta_list as $metarow) {
            $mpid = $metarow['post_id'];
            $mkey = $metarow['meta_key'];
            $mval = $metarow['meta_value'];
			
            // Force subkeys to be array type:
            if (!isset($post_meta_cache[$mpid]) || !is_array($post_meta_cache[$mpid]))
                $post_meta_cache[$mpid] = array();
            if (!isset($post_meta_cache[$mpid]["$mkey"]) || !is_array($post_meta_cache[$mpid]["$mkey"]))
                $post_meta_cache[$mpid]["$mkey"] = array();
			
            // Add a value to the current pid/key:
            $post_meta_cache[$mpid][$mkey][] = $mval;
        }
    }
}

function update_category_cache() {
    global $cache_categories, $wpdb;
    $dogs = $wpdb->get_results("SELECT * FROM $wpdb->categories");
    foreach ($dogs as $catt) {
        $cache_categories[$catt->cat_ID] = $catt;
    }
}

function update_user_cache() {
    global $cache_userdata, $wpdb;

    if ( $users = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_level > 0") ) :
		foreach ($users as $user) :
			$cache_userdata[$user->ID] = $user;
		endforeach;
		return true;
	else: 
		return false;
	endif;
}

function wp_head() {
	do_action('wp_head', '');
}

function is_single () {
    global $wp_query;

    return $wp_query->is_single;
}

function is_page () {
    global $wp_query;

    return $wp_query->is_page;
}

function is_archive () {
    global $wp_query;

    return $wp_query->is_archive;
}

function is_date () {
    global $wp_query;

    return $wp_query->is_date;
}

function is_year () {
    global $wp_query;

    return $wp_query->is_year;
}

function is_month () {
    global $wp_query;

    return $wp_query->is_month;
}

function is_day () {
    global $wp_query;

    return $wp_query->is_day;
}

function is_time () {
    global $wp_query;

    return $wp_query->is_time;
}

function is_author () {
    global $wp_query;

    return $wp_query->is_author;
}

function is_category () {
    global $wp_query;

    return $wp_query->is_category;
}

function is_search () {
    global $wp_query;

    return $wp_query->is_search;
}

function is_feed () {
    global $wp_query;

    return $wp_query->is_feed;
}

function is_home () {
    global $wp_query;

    return $wp_query->is_home;
}

function is_404 () {
    global $wp_query;

    return $wp_query->is_404;
}

function get_query_var($var) {
  global $wp_query;

  return $wp_query->get($var);
}

function have_posts() {
    global $wp_query;

    return $wp_query->have_posts();
}

function the_post() {
    start_wp(true);
}

?>