<?php

if (!function_exists('_')) {
	function _($string) {
		return $string;
	}
}


/* functions... */

/***** Formatting functions *****/
function wptexturize($text) {
	$textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
	$stop = count($textarr); $next = true; // loop stuff
	for ($i = 0; $i < $stop; $i++) {
		$curl = $textarr[$i];
		if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko')) {
			$curl = str_replace('<q>', '&#8220;', $curl);
			$curl = str_replace('</q>', '&#8221;', $curl);
		}
		if ('<' != $curl{0} && $next) { // If it's not a tag
			$curl = str_replace('---', '&#8212;', $curl);
			$curl = str_replace('--', '&#8211;', $curl);
			$curl = str_replace("...", '&#8230;', $curl);
			$curl = str_replace('``', '&#8220;', $curl);

			// This is a hack, look at this more later. It works pretty well though.
			$cockney = array("'tain't","'twere","'twas","'tis","'twill","'til","'bout","'nuff","'round");
			$cockneyreplace = array("&#8217;tain&#8217;t","&#8217;twere","&#8217;twas","&#8217;tis","&#8217;twill","&#8217;til","&#8217;bout","&#8217;nuff","&#8217;round");
			$curl = str_replace($cockney, $cockneyreplace, $curl);

		
			$curl = preg_replace("/'s/", "&#8217;s", $curl);
			$curl = preg_replace("/'(\d\d(?:&#8217;|')?s)/", "&#8217;$1", $curl);
			$curl = preg_replace('/(\s|\A|")\'/', '$1&#8216;', $curl);
			$curl = preg_replace("/(\d+)\"/", "$1&Prime;", $curl);
			$curl = preg_replace("/(\d+)'/", "$1&prime;", $curl);
			$curl = preg_replace("/(\S)'([^'\s])/", "$1&#8217;$2", $curl);
			$curl = preg_replace('/"([\s.]|\Z)/', '&#8221;$1', $curl);
			$curl = preg_replace('/(\s|\A)"/', '$1&#8220;', $curl);
			$curl = preg_replace("/'([\s.]|\Z)/", '&#8217;$1', $curl);
			$curl = preg_replace("/\(tm\)/i", '&#8482;', $curl);
			$curl = preg_replace("/\(c\)/i", '&#169;', $curl);
			$curl = preg_replace("/\(r\)/i", '&#174;', $curl);
			$curl = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $curl);
			$curl = str_replace("''", '&#8221;', $curl);
			
			$curl = preg_replace('/(d+)x(\d+)/', "$1&#215;$2", $curl);

		} elseif (strstr($curl, '<code') || strstr($curl, '<pre') || strstr($curl, '<kbd' || strstr($curl, '<style') || strstr($curl, '<script'))) {
			// strstr is fast
			$next = false;
		} else {
			$next = true;
		}
		$output .= $curl;
	}
	return $output;
	}

function wpautop($pee, $br=1) { 
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	$pee = preg_replace("/(\r\n|\n|\r)/", "\n", $pee); // cross-platform newlines 
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	$pee = preg_replace('/\n?(.+?)(\n\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end 
	$pee = str_replace('<br /></p>', '</p>', $pee);
	$pee = str_replace('<p><p>', '<p>', $pee);
	$pee = str_replace('</p></p>', '</p>', $pee);
	$pee = preg_replace('!<p>\s*(</?(?:table|ul|ol|li|pre|select|form|blockquote)>)!', "$1", $pee);
	$pee = preg_replace('!(</?(?:table|ul|ol|li|pre|select|form|blockquote)>)\s*</p>!', "$1", $pee); 
	if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
	$pee = preg_replace('!(</?(?:table|ul|ol|li|pre|select|form|blockquote|p)>)<br />!', "$1", $pee);
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	$pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
	
	return $pee; 
}

function popuplinks($text) {
	// Comment text in popup windows should be filtered through this.
	// Right now it's a moderately dumb function, ideally it would detect whether
	// a target or rel attribute was already there and adjust its actions accordingly.
	$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank' rel='external'>", $text);
	return $text;
}

function autobrize($content) {
	$content = preg_replace("/<br>\n/", "\n", $content);
	$content = preg_replace("/<br \/>\n/", "\n", $content);
	$content = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />\n", $content);
	return $content;
	}
function unautobrize($content) {
	$content = preg_replace("/<br>\n/", "\n", $content);   //for PHP versions before 4.0.5
	$content = preg_replace("/<br \/>\n/", "\n", $content);
	return $content;
	}


function format_to_edit($content) {
	global $autobr;
	$content = stripslashes($content);
	if ($autobr) { $content = unautobrize($content); }
	$content = htmlspecialchars($content);
	return $content;
	}
function format_to_post($content) {
	global $post_autobr,$comment_autobr;
	$content = addslashes($content);
	if ($post_autobr || $comment_autobr) { $content = autobrize($content); }
	return $content;
	}


function zeroise($number,$threshold) { // function to add leading zeros when necessary
	$l=strlen($number);
	if ($l<$threshold)
		for ($i=0; $i<($threshold-$l); $i=$i+1) { $number='0'.$number;	}
	return $number;
	}


function backslashit($string) {
	$string = preg_replace('/([a-z])/i', '\\\\\1', $string);
	return $string;
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
# // for debug purposes
#		echo $i." ".$mysqlstring;
	}
	return $j;
}

function addslashes_gpc($gpc) {
	if (!get_magic_quotes_gpc()) {
		$gpc = addslashes($gpc);
	}
	return $gpc;
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
	while ($weekday > $start_of_week) {
		$weekday = date('w',$day);
		$day = $day - 86400;
		$i = 0;
	}
	$week['start'] = $day + 86400 - $i;
	$week['end']   = $day + 691199;
	return $week;
}

function convert_chars($content,$flag="html") { // html/unicode entities output, defaults to html
	$newcontent = "";

	global $convert_chars2unicode, $convert_entities2unicode, $leavecodealone, $use_htmltrans;
	global $b2_htmltrans, $b2_htmltranswinuni;

	### this is temporary - will be replaced by proper config stuff
	$convert_chars2unicode = 1;
	if (($leavecodealone) || (!$use_htmltrans)) {
		$convert_chars2unicode = 0;
	}
	###


	// converts HTML-entities to their display values in order to convert them again later

	$content = preg_replace("/<title>(.+?)<\/title>/","",$content);
	$content = preg_replace("/<category>(.+?)<\/category>/","",$content);
	
#	$content = str_replace("&amp;","&#38;",$content);
	$content = strtr($content, $b2_htmltrans);

	return $content;
	
	// the following is the slowest. code. ever.
	/*
	for ($i=0; $i<strlen($content); $i=$i+1) {
		$j = substr($content,$i,1);
		$jnext = substr($content,$i+1,1);
		$jord = ord($j);
		if ($convert_chars2unicode) {
			switch($flag) {
				case "unicode":
	//				$j = str_replace("&","&#38;",$j);
					if (($jord>=128) || ($j == "&") || (($jord>=128) && ($jord<=159))) {
						$j = "&#".$jord.";";
					}
					break;
				case "html":
					if (($jord>=128) || (($jord>=128) && ($jord<=159))) {
						$j = "&#".$jord.";"; // $j = htmlentities($j);
					} elseif (($j == "&") && ($jnext != "#")) {
						$j = "&amp;";
					}
					break;
				case "xml":
					if ($jord>=128) {
						$j = "&#".$jord.";"; // $j = htmlentities($j);
	//					$j = htmlentities($j);
					} elseif (($j == "&") && ($jnext != "#")) {
						$j = "&#38;";
					}
					break;
			}
		}

		$newcontent .= $j;
	}

	// now converting: Windows CP1252 => Unicode (valid HTML)
	// (if you've ever pasted text from MSWord, you'll understand)

	$newcontent = strtr($newcontent, $b2_htmltranswinuni);

	// you can delete these 2 lines if you don't like <br /> and <hr />
	$newcontent = str_replace("<br>","<br />",$newcontent);
	$newcontent = str_replace("<hr>","<hr />",$newcontent);

	return $newcontent;
	*/
}

function convert_bbcode($content) {
	global $b2_bbcode, $use_bbcode;
	if ($use_bbcode) {
		$content = preg_replace($b2_bbcode["in"], $b2_bbcode["out"], $content);
	}
	$content = convert_bbcode_email($content);
	return $content;
}

function convert_bbcode_email($content) {
	global $use_bbcode;
	$bbcode_email["in"] = array(
		'#\[email](.+?)\[/email]#eis',
		'#\[email=(.+?)](.+?)\[/email]#eis'
	);
	$bbcode_email["out"] = array(
		"'<a href=\"mailto:'.antispambot('\\1').'\">'.antispambot('\\1').'</a>'",		// E-mail
		"'<a href=\"mailto:'.antispambot('\\1').'\">\\2</a>'"
	);

	$content = preg_replace($bbcode_email["in"], $bbcode_email["out"], $content);
	return $content;
}

function convert_gmcode($content) {
	global $b2_gmcode, $use_gmcode;
	if ($use_gmcode) {
		$content = preg_replace($b2_gmcode["in"], $b2_gmcode["out"], $content);
	}
	return $content;
}

function convert_smilies($content) {
	global $smilies_directory, $use_smilies;
	global $b2_smiliessearch, $b2_smiliesreplace;
	if ($use_smilies) {
		$content = str_replace($b2_smiliessearch, $b2_smiliesreplace, $content);
	}
	return $content;
}

function antispambot($emailaddy, $mailto=0) {
	$emailNOSPAMaddy = '';
	srand ((float) microtime() * 1000000);
	for ($i = 0; $i < strlen($emailaddy); $i = $i + 1) {
		$j = floor(rand(0, 1+$mailto));
		if ($j==0) {
			$emailNOSPAMaddy .= '&#'.ord(substr($emailaddy,$i,1)).';';
		} elseif ($j==1) {
			$emailNOSPAMaddy .= substr($emailaddy,$i,1);
		} elseif ($j==2) {
			$emailNOSPAMaddy .= '%'.zeroise(dechex(ord(substr($emailaddy, $i, 1))), 2);
		}
	}
	$emailNOSPAMaddy = str_replace('@','&#64;',$emailNOSPAMaddy);
	return $emailNOSPAMaddy;
}

function make_clickable($text) { // original function: phpBB, extended here for AIM & ICQ
    $ret = " " . $text;
    $ret = preg_replace("#([\n ])([a-z]+?)://([^, <>{}\n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $ret);
    $ret = preg_replace("#([\n ])aim:([^,< \n\r]+)#i", "\\1<a href=\"aim:goim?screenname=\\2\\3&message=Hello\">\\2\\3</a>", $ret);
    $ret = preg_replace("#([\n ])icq:([^,< \n\r]+)#i", "\\1<a href=\"http://wwp.icq.com/scripts/search.dll?to=\\2\\3\">\\2\\3</a>", $ret);
    $ret = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^,< \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $ret);
    $ret = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^,< \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
    $ret = substr($ret, 1);
    return $ret;
}


function is_email($user_email) {
	$chars = "/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i";
	if(strstr($user_email, '@') && strstr($user_email, '.')) {
		if (preg_match($chars, $user_email)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function strip_all_but_one_link($text, $mylink) {
	$match_link = '#(<a.+?href.+?'.'>)(.+?)(</a>)#';
	preg_match_all($match_link, $text, $matches);
	$count = count($matches[0]);
	for ($i=0; $i<$count; $i++) {
		if (!strstr($matches[0][$i], $mylink)) {
			$text = str_replace($matches[0][$i], $matches[2][$i], $text);
		}
	}
	return $text;
}


/***** // Formatting functions *****/



function get_lastpostdate() {
	global $tableposts, $cache_lastpostdate, $use_cache, $time_difference, $pagenow;
	if ((!isset($cache_lastpostdate)) OR (!$use_cache)) {
		$now = date("Y-m-d H:i:s",(time() + ($time_difference * 3600)));
		if ($pagenow != 'b2edit.php') {
			$showcatzero = 'post_category > 0 AND';
		} else {
			$showcatzero = '';
		}
		$sql = "SELECT * FROM $tableposts WHERE $showcatzero post_date <= '$now' ORDER BY post_date DESC LIMIT 1";
		$result = mysql_query($sql) or die("Your SQL query: <br />$sql<br /><br />MySQL said:<br />".mysql_error());
		++$querycount;
		$myrow = mysql_fetch_object($result);
		$lastpostdate = $myrow->post_date;
		$cache_lastpostdate = $lastpostdate;
//		echo $lastpostdate;
	} else {
		$lastpostdate = $cache_lastpostdate;
	}
	return $lastpostdate;
}

function user_pass_ok($user_login,$user_pass) {
	global $cache_userdata,$use_cache;
	if ((empty($cache_userdata[$user_login])) OR (!$use_cache)) {
		$userdata = get_userdatabylogin($user_login);
	} else {
		$userdata = $cache_userdata[$user_login];
	}
	return ($user_pass == $userdata->user_pass);
}

function get_currentuserinfo() { // a bit like get_userdata(), on steroids
	global $HTTP_COOKIE_VARS, $user_login, $userdata, $user_level, $user_ID, $user_nickname, $user_email, $user_url, $user_pass_md5;
	// *** retrieving user's data from cookies and db - no spoofing
	$user_login = $HTTP_COOKIE_VARS['wordpressuser'];
	$userdata = get_userdatabylogin($user_login);
	$user_level = $userdata->user_level;
	$user_ID = $userdata->ID;
	$user_nickname = $userdata->user_nickname;
	$user_email = $userdata->user_email;
	$user_url = $userdata->user_url;
	$user_pass_md5 = md5($userdata->user_pass);
}

function get_userdata($userid) {
	global $wpdb, $querycount, $cache_userdata, $use_cache, $tableusers;
	if ((empty($cache_userdata[$userid])) || (!$use_cache)) {
		$user = $wpdb->get_row("SELECT * FROM $tableusers WHERE ID = $userid");
		++$querycount;
		$cache_userdata[$userid] = $user;
	} else {
		$user = $cache_userdata[$userid];
	}
	return $user;
}

function get_userdata2($userid) { // for team-listing
	global $tableusers, $post;
	$user_data['ID'] = $userid;
	$user_data['user_login'] = $post->user_login;
	$user_data['user_firstname'] = $post->user_firstname;
	$user_data['user_lastname'] = $post->user_lastname;
	$user_data['user_nickname'] = $post->user_nickname;
	$user_data['user_level'] = $post->user_level;
	$user_data['user_email'] = $post->user_email;
	$user_data['user_url'] = $post->user_url;
	return $user_data;
}

function get_userdatabylogin($user_login) {
	global $tableusers, $querycount, $cache_userdata, $use_cache, $wpdb;
	if ((empty($cache_userdata["$user_login"])) OR (!$use_cache)) {
		$user = $wpdb->get_row("SELECT * FROM $tableusers WHERE user_login = '$user_login'");
		++$querycount;
		$cache_userdata["$user_login"] = $user;
	} else {
		$user = $cache_userdata["$user_login"];
	}
	return $user;
}

function get_userid($user_login) {
	global $tableusers, $querycount, $cache_userdata, $use_cache, $wpdb;
	if ((empty($cache_userdata["$user_login"])) OR (!$use_cache)) {
		$user_id = $wpdb->get_var("SELECT ID FROM $tableusers WHERE user_login = '$user_login'");

		++$querycount;
		$cache_userdata["$user_login"] = $user_id;
	} else {
		$user_id = $cache_userdata["$user_login"];
	}
	return $user_id;
}

function get_usernumposts($userid) {
	global $tableposts, $tablecomments, $querycount, $wpdb;
	++$querycount;
	return $wpdb->get_var("SELECT COUNT(*) FROM $tableposts WHERE post_author = $userid");
}

function get_settings($setting) {
	global $wpdb, $cache_settings, $use_cache, $querycount;
	if ((empty($cache_settings)) OR (!$use_cache)) {
		$settings = get_alloptions();
		$cache_settings = $settings;
	} else {
		$settings = $cache_settings;
	}
    if (!isset($settings->$setting)) {
        error_log("get_settings: Didn't find setting $setting");
    }
	return $settings->$setting;
}

function get_alloptions() {
    global $tableoptions, $wpdb;
    $options = $wpdb->get_results("SELECT option_name, option_value FROM $tableoptions");
    ++$querycount;
    if ($options) {
        foreach ($options as $option) {
            $all_options->{$option->option_name} = $option->option_value;
        }
    }
    return $all_options;
}
    
function get_postdata($postid) {
	global $tableusers, $tablecategories, $tableposts, $tablecomments, $querycount, $wpdb;
	$post = $wpdb->get_row("SELECT * FROM $tableposts WHERE ID = $postid");
	++$querycount;
	
	$postdata = array (
		'ID' => $post->ID, 
		'Author_ID' => $post->post_author, 
		'Date' => $post->post_date, 
		'Content' => $post->post_content, 
		'Excerpt' => $post->post_excerpt, 
		'Title' => $post->post_title, 
		'Category' => $post->post_category,
		'post_status' => $post->post_status,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_password' => $post->post_password
	);
	return $postdata;
}

function get_postdata2($postid=0) { // less flexible, but saves mysql queries
	global $post;
	$postdata = array (
		'ID' => $post->ID, 
		'Author_ID' => $post->post_author,
		'Date' => $post->post_date,
		'Content' => $post->post_content,
		'Excerpt' => $post->post_excerpt,
		'Title' => $post->post_title,
		'Category' => $post->post_category,
		'post_status' => $post->post_status,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_password' => $post->post_password
		);
	return $postdata;
}

function get_commentdata($comment_ID,$no_cache=0) { // less flexible, but saves mysql queries
	global $postc,$id,$commentdata,$tablecomments,$querycount;
	if ($no_cache) {
		$query="SELECT * FROM $tablecomments WHERE comment_ID = $comment_ID";
		$result=mysql_query($query);
		++$querycount;
		$myrow = mysql_fetch_array($result);
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
	global $tablecategories,$cache_catnames,$use_cache,$querycount;
	if ((!$cache_catnames) || (!$use_cache)) {
		$sql = "SELECT * FROM $tablecategories";
		$result = mysql_query($sql) or die('Oops, couldn\'t query the db for categories.');
		$querycount;
		while ($post = mysql_fetch_object($result)) {
			$cache_catnames[$post->cat_ID] = $post->cat_name;
		}
	}
	$cat_name = $cache_catnames[$cat_ID];
	return $cat_name;
}

function profile($user_login) {
	global $user_data;
	echo "<a href='b2profile.php?user=".$user_data->user_login."' onclick=\"javascript:window.open('b2profile.php?user=".$user_data->user_login."','Profile','toolbar=0,status=1,location=0,directories=0,menuBar=1,scrollbars=1,resizable=0,width=480,height=320,left=100,top=100'); return false;\">$user_login</a>";
}

function dropdown_categories($blog_ID=1, $default=1) {
	global $postdata,$tablecategories,$mode,$querycount;
	$query="SELECT * FROM $tablecategories";
	$result=mysql_query($query);
	++$querycount;
	$width = ($mode=="sidebar") ? "100%" : "170px";
	echo '<select name="post_category" style="width:'.$width.';" tabindex="2" id="category">';
    if ($postdata["Category"] != '') {
        $default = $postdata["Category"];
    }
	while($post = mysql_fetch_object($result)) {
		echo "<option value=\"".$post->cat_ID."\"";
		if ($post->cat_ID == $default)
			echo " selected";
		echo ">".$post->cat_name."</option>";
	}
	echo "</select>";
}

function touch_time($edit = 1) {
	global $month, $postdata, $time_difference;
	// echo $postdata['Date'];
	if ('draft' == $postdata['post_status']) {
		$checked = 'checked="checked" ';
		$edit = false;
	} else {
		$checked = ' ';
	}

	echo '<p><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp" '.$checked.'/> <label for="timestamp">Edit timestamp</label><br />';
	
	$time_adj = time() + ($time_difference * 3600);
	$jj = ($edit) ? mysql2date('d', $postdata['Date']) : date('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $postdata['Date']) : date('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $postdata['Date']) : date('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $postdata['Date']) : date('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $postdata['Date']) : date('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $postdata['Date']) : date('s', $time_adj);

	echo '<input type="text" name="jj" value="'.$jj.'" size="2" maxlength="2" />'."\n";
	echo "<select name=\"mm\">\n";
	for ($i=1; $i < 13; $i=$i+1) {
		echo "\t\t\t<option value=\"$i\"";
		if ($i == $mm)
		echo " selected";
		if ($i < 10) {
			$ii = "0".$i;
		} else {
			$ii = "$i";
		}
		echo ">".$month["$ii"]."</option>\n";
	} ?>
</select>
<input type="text" name="aa" value="<?php echo $aa ?>" size="4" maxlength="5" /> @ 
<input type="text" name="hh" value="<?php echo $hh ?>" size="2" maxlength="2" /> : 
<input type="text" name="mn" value="<?php echo $mn ?>" size="2" maxlength="2" /> : 
<input type="text" name="ss" value="<?php echo $ss ?>" size="2" maxlength="2" /> </p>
	<?php
}

function gzip_compression() {
	global $gzip_compressed;
		if (!$gzip_compressed) {
		$phpver = phpversion(); //start gzip compression
		if($phpver >= "4.0.4pl1") {
			if(extension_loaded("zlib")) { ob_start("ob_gzhandler"); }
		} else if($phpver > "4.0") {
			if(strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				if(extension_loaded("zlib")) { $do_gzip_compress = TRUE; ob_start(); ob_implicit_flush(0); header("Content-Encoding: gzip");  }
			}
		} //end gzip compression - that piece of script courtesy of the phpBB dev team
		$gzip_compressed=1;
	}
}

function alert_error($msg) { // displays a warning box with an error message (original by KYank)
	global $$HTTP_SERVER_VARS;
	?>
	<html>
	<head>
	<script language="JavaScript">
	<!--
	alert("<?php echo $msg ?>");
	history.back();
	//-->
	</script>
	</head>
	<body>
	<!-- this is for non-JS browsers (actually we should never reach that code, but hey, just in case...) -->
	<?php echo $msg; ?><br />
	<a href="<?php echo $HTTP_SERVER_VARS["HTTP_REFERER"]; ?>">go back</a>
	</body>
	</html>
	<?php
	exit;
}

function alert_confirm($msg) { // asks a question - if the user clicks Cancel then it brings them back one page
	?>
	<script language="JavaScript">
	<!--
	if (!confirm("<?php echo $msg ?>")) {
	history.back();
	}
	//-->
	</script>
	<?php
}

function redirect_js($url,$title="...") {
	?>
	<script language="JavaScript">
	<!--
	function redirect() {
	window.location = "<?php echo $url; ?>";
	}
	setTimeout("redirect();", 100);
	//-->
	</script>
	<p>Redirecting you : <b><?php echo $title; ?></b><br />
	<br />
	If nothing happens, click <a href="<?php echo $url; ?>">here</a>.</p>
	<?php
	exit();
}

// functions to count the page generation time (from phpBB2)
// ( or just any time between timer_start() and timer_stop() )

function timer_start() {
    global $timestart;
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $timestart = $mtime;
    return true;
}

function timer_stop($display=0,$precision=3) { //if called like timer_stop(1), will echo $timetotal
    global $timestart,$timeend;
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $timeend = $mtime;
    $timetotal = $timeend-$timestart;
    if ($display)
        echo number_format($timetotal,$precision);
    return $timetotal;
}


// pings Weblogs.com
function pingWeblogs($blog_ID = 1) {
	// original function by Dries Buytaert for Drupal
	global $use_weblogsping, $blogname,$siteurl,$blogfilename;
	if ((!(($blogname=="my weblog") && ($siteurl=="http://example.com") && ($blogfilename=="b2.php"))) && (!preg_match("/localhost\//",$siteurl)) && ($use_weblogsping)) {
		$client = new xmlrpc_client("/RPC2", "rpc.weblogs.com", 80);
		$message = new xmlrpcmsg("weblogUpdates.ping", array(new xmlrpcval($blogname), new xmlrpcval($siteurl."/".$blogfilename)));
		$result = $client->send($message);
		if (!$result || $result->faultCode()) {
			return false;
		}
		return true;
	} else {
		return false;
	}
}

// pings Weblogs.com/rssUpdates
function pingWeblogsRss($blog_ID = 1, $rss_url) {
	global $use_weblogsrssping, $blogname, $rss_url;
	if ($blogname != 'my weblog' && $rss_url != 'http://example.com/b2rdf.php' && $use_weblogsrssping) {
		$client = new xmlrpc_client('/RPC2', 'rssrpc.weblogs.com', 80);
		$message = new xmlrpcmsg('rssUpdate', array(new xmlrpcval($blogname), new xmlrpcval($rss_url)));
		$result = $client->send($message);
		if (!$result || $result->faultCode()) {
			return false;
		}
		return true;
	} else {
		return false;
	}
}

// pings CaféLog.com
function pingCafelog($cafelogID,$title='',$p='') {
	global $use_cafelogping, $blogname, $siteurl, $blogfilename;
	if ((!(($blogname=="my weblog") && ($siteurl=="http://example.com") && ($blogfilename=="b2.php"))) && (!preg_match("/localhost\//",$siteurl)) && ($use_cafelogping) && ($cafelogID != '')) {
		$client = new xmlrpc_client("/xmlrpc.php", "cafelog.tidakada.com", 80);
		$message = new xmlrpcmsg("b2.ping", array(new xmlrpcval($cafelogID), new xmlrpcval($title), new xmlrpcval($p)));
		$result = $client->send($message);
		if (!$result || $result->faultCode()) {
			return false;
		}
		return true;
	} else {
		return false;
	}
}

// pings Blo.gs
function pingBlogs($blog_ID="1") {
	global $use_blodotgsping, $blodotgsping_url, $use_rss, $blogname, $siteurl, $blogfilename;
	if ((!(($blogname=='my weblog') && ($siteurl=='http://example.com') && ($blogfilename=='b2.php'))) && (!preg_match('/localhost\//',$siteurl)) && ($use_blodotgsping)) {
		$url = ($blodotgsping_url == 'http://example.com') ? $siteurl.'/'.$blogfilename : $blodotgsping_url;
		$client = new xmlrpc_client('/', 'ping.blo.gs', 80);
		if ($use_rss) {
			$message = new xmlrpcmsg('weblogUpdates.extendedPing', array(new xmlrpcval($blogname), new xmlrpcval($url), new xmlrpcval($url), new xmlrpcval($siteurl.'/b2rss.xml')));
		} else {
			$message = new xmlrpcmsg('weblogUpdates.ping', array(new xmlrpcval($blogname), new xmlrpcval($url)));
		}
		$result = $client->send($message);
		if (!$result || $result->faultCode()) {
			return false;
		}
		return true;
	} else {
		return false;
	}
}


// trackback - send
function trackback($trackback_url, $title, $excerpt, $ID) {
	global $siteurl, $blogfilename, $blogname;
	global $querystring_start, $querystring_equal;
	$title = urlencode($title);
	$excerpt = urlencode(stripslashes($excerpt));
	$blog_name = urlencode($blogname);
	$url = urlencode($siteurl.'/'.$blogfilename.$querystring_start.'p'.$querystring_equal.$ID);
	$query_string = "title=$title&url=$url&blog_name=$blog_name&excerpt=$excerpt";
	if (strstr($trackback_url, '?')) {
		$trackback_url .= "&".$query_string;;
		$fp = @fopen($trackback_url, 'r');
		$result = @fread($fp, 4096);
		@fclose($fp);
/* debug code
		$debug_file = 'trackback.log';
		$fp = fopen($debug_file, 'a');
		fwrite($fp, "\n*****\nTrackback URL query:\n\n$trackback_url\n\nResponse:\n\n");
		fwrite($fp, $result);
		fwrite($fp, "\n\n");
		fclose($fp);
*/
	} else {
		$trackback_url = parse_url($trackback_url);
		$http_request  = 'POST '.$trackback_url['path']." HTTP/1.0\r\n";
		$http_request .= 'Host: '.$trackback_url['host']."\r\n";
		$http_request .= 'Content-Type: application/x-www-form-urlencoded'."\r\n";
		$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
		$http_request .= "\r\n";
		$http_request .= $query_string;
		$fs = @fsockopen($trackback_url['host'], 80);
		@fputs($fs, $http_request);
/* debug code
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
	}
	return $result;
}

// trackback - reply
function trackback_response($error = 0, $error_message = '') {
	if ($error) {
		echo '<?xml version="1.0" encoding="iso-8859-1"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
	} else {
		echo '<?xml version="1.0" encoding="iso-8859-1"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
	die();
}

function make_url_footnote($content) {
	global $siteurl;
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
		$link_url = (strtolower(substr($link_url,0,7)) != 'http://') ? $siteurl.$link_url : $link_url;
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
		$post_category = $matchcat[0];
		$post_category = preg_replace('/<category>/si', '', $post_category);
		$post_category = preg_replace('/<\/category>/si', '', $post_category);

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
	// original code by Mort (http://mort.mine.nu:8080)
	global $siteurl, $blogfilename, $b2_version;
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
	// This regexp comes straigth from phpfreaks.com
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
		$request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: b2/$b2_version PHP/" . phpversion() . "\r\n\r\n";
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
			$pagelinkedfrom = $siteurl.'/'.$blogfilename.'?p='.$post_ID.'&c=1';
			debug_fwrite($log, $pagelinkedfrom."\n");

			$client = new xmlrpc_client($path, $host, 80);
			$message = new xmlrpcmsg($method, array(new xmlrpcval($pagelinkedfrom), new xmlrpcval($pagelinkedto)));
			$result = $client->send($message);
			if ($result){
				if (!$result->value()){
					debug_fwrite($log, $result->faultCode().' -- '.$result->faultString());
				} else {
					$value = xmlrpc_decode($result->value());
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


/*
 balanceTags
 
 Balances Tags of string using a modified stack.
 
 @param text      Text to be balanced
 @return          Returns balanced text
 @author          Leonard Lin (leonard@acm.org)
 @version         v1.1
 @date            November 4, 2001
 @license         GPL v2.0
 @notes           
 @changelog       
             1.2  ***TODO*** Make better - change loop condition to $text
             1.1  Fixed handling of append/stack pop order of end text
                  Added Cleaning Hooks
             1.0  First Version
*/

function balanceTags($text, $is_comment = 0) {
	global $use_balanceTags;

	if ($is_comment) {
		// sanitise HTML attributes, remove frame/applet tags
		$text = preg_replace('#( on[a-z]{1,}|style|class|id)="(.*?)"#i', '', $text);
		$text = preg_replace('#( on[a-z]{1,}|style|class|id)=\'(.*?)\'#i', '', $text);
		$text = preg_replace('#([a-z]{1,})="(( |\t)*?)(javascript|vbscript|about):(.*?)"#i', '$1=""', $text);
		$text = preg_replace('#([a-z]{1,})=\'(( |\t)*?)(javascript|vbscript|about):(.*?)\'#i', '$1=""', $text);
		$text = preg_replace('#\<(\/{0,1})([a-z]{0,2})(frame|applet)(.*?)\>#i', '', $text);
	}
	
	if ($use_balanceTags == 0) {
		return $text;
	}
	$tagstack = array();
	$stacksize = 0;
	$tagqueue = '';
	$newtext = '';

	# b2 bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);

	# b2 bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);


	while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
		$newtext = $newtext . $tagqueue;

		$i = strpos($text,$regex[0]);
		$l = strlen($tagqueue) + strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';

		// Pop or Push
		if ($regex[1][0] == "/") { // End Tag
			$tag = strtolower(substr($regex[1],1));

			// if too many closing tags
			if($stacksize <= 0) { 
				$tag = '';
				//or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop ($tagstack);
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ($j=$stacksize-1;$j>=0;$j--) {
					if ($tagstack[$j] == $tag) {
					// add tag to tagqueue
						for ($k=$stacksize-1;$k>=$j;$k--){
							$tagqueue .= '</' . array_pop ($tagstack) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// Push if not img or br or hr
			if($tag != 'br' && $tag != 'img' && $tag != 'hr') {
				$stacksize = array_push ($tagstack, $tag);
			}

			// Attributes
			// $attributes = $regex[2];
			$attributes = $regex[2];
			if($attributes) {
				// fix to avoid CSS defacements
				if ($is_comment) {
					$attributes = str_replace('style=', 'title=', $attributes);
					$attributes = str_replace('class=', 'title=', $attributes);
					$attributes = str_replace('id=', 'title=', $attributes);
				}
				$attributes = ' '.$attributes;
			}

			$tag = '<'.$tag.$attributes.'>';
		}

		$newtext .= substr($text,0,$i) . $tag;
		$text = substr($text,$i+$l);
	}  

	// Clear Tag Queue
	$newtext = $newtext . $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while($x = array_pop($tagstack)) {
		$newtext = $newtext . '</' . $x . '>'; // Add remaining tags to close      
	}

	# b2 fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}

?>