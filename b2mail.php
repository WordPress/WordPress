<?php

# pop3-2-b2 mail to blog
# v0.3 20020716

require_once('wp-config.php');
require_once($abspath.$b2inc."/b2template.functions.php");
require_once($abspath.$b2inc.'/b2vars.php');
require_once($abspath.$b2inc.'/class.POP3.php');
require_once($abspath.$b2inc.'/b2functions.php');
require_once($abspath.$b2inc."/xmlrpc.inc");
require_once($abspath.$b2inc."/xmlrpcs.inc");

dbconnect();
timer_start();

$use_cache = 1;
$output_debugging_info = 0;	# =1 if you want to output debugging info
$autobr = get_settings('AutoBR');
$time_difference = get_settings('time_difference');

if ($use_phoneemail) {
	// if you're using phone email, the email will already be in your timezone
	$time_difference = 0;
}

error_reporting(2037);



$pop3 = new POP3();

if(!$pop3->connect($mailserver_url, $mailserver_port)) {
	echo "Ooops $pop3->ERROR <br />\n";
	exit;
}

$Count = $pop3->login($mailserver_login, $mailserver_pass);
if((!$Count) || ($Count == -1)) {
	echo "<h1>Login Failed: $pop3->ERROR</h1>\n";
	$pop3->quit();
	exit;
}


// ONLY USE THIS IF YOUR PHP VERSION SUPPORTS IT!
//register_shutdown_function($pop3->quit());

for ($iCount=1; $iCount<=$Count; $iCount++) {

	$MsgOne = $pop3->get($iCount);
	if((!$MsgOne) || (gettype($MsgOne) != 'array')) {
		echo "oops, $pop3->ERROR<br />\n";
		$pop3->quit();
		exit;
	}

	$content = '';
	$content_type = '';
	$boundary = '';
	$bodysignal = 0;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	while ( list ( $lineNum,$line ) = each ($MsgOne) ) {
		if (strlen($line) < 3) {
			$bodysignal = 1;
		}
		if ($bodysignal) {
			$content .= $line;
		} else {
			if (preg_match('/Content-Type: /', $line)) {
				$content_type = trim($line);
				$content_type = substr($content_type, 14, strlen($content_type)-14);
				$content_type = explode(';', $content_type);
				$content_type = $content_type[0];
			}
			if (($content_type == 'multipart/alternative') && (preg_match('/boundary="/', $line)) && ($boundary == '')) {
				$boundary = trim($line);
				$boundary = explode('"', $boundary);
				$boundary = $boundary[1];
			}
			if (preg_match('/Subject: /', $line)) {
				$subject = trim($line);
				$subject = substr($subject, 9, strlen($subject)-9);
				if ($use_phoneemail) {
					$subject = explode($phoneemail_separator, $subject);
					$subject = trim($subject[0]);
				}
				if (!ereg($subjectprefix, $subject)) {
					continue;
				}
			}
			if (preg_match('/Date: /', $line)) { // of the form '20 Mar 2002 20:32:37'
				$ddate = trim($line);
				$ddate = str_replace('Date: ', '', $ddate);
				if (strpos($ddate, ',')) {
					$ddate = trim(substr($ddate, strpos($ddate, ',')+1, strlen($ddate)));
				}
				$date_arr = explode(' ', $ddate);
				$date_time = explode(':', $date_arr[3]);
				
				$ddate_H = $date_time[0];
				$ddate_i = $date_time[1];
				$ddate_s = $date_time[2];
				
				$ddate_m = $date_arr[1];
				$ddate_d = $date_arr[0];
				$ddate_Y = $date_arr[2];
				for ($i=0; $i<12; $i++) {
					if ($ddate_m == $dmonths[$i]) {
						$ddate_m = $i+1;
					}
				}
				$ddate_U = mktime($ddate_H, $ddate_i, $ddate_s, $ddate_m, $ddate_d, $ddate_Y);
				$ddate_U = $ddate_U + ($time_difference * 3600);
				$post_date = date('Y-m-d H:i:s', $ddate_U);
			}
		}
	}

	$ddate_today = time() + ($time_difference * 3600);
	$ddate_difference_days = ($ddate_today - $ddate_U) / 86400;


	# starts buffering the output
	ob_start();

	if ($ddate_difference_days > 14) {
		echo 'too old<br />';
		continue;
	}

	if (preg_match('/'.$subjectprefix.'/', $subject)) {

		$userpassstring = '';

		echo '<div style="border: 1px dashed #999; padding: 10px; margin: 10px;">';
		echo "<p><b>$iCount</b></p><p><b>Subject: </b>$subject</p>\n";

		$subject = trim(str_replace($subjectprefix, '', $subject));

		if ($content_type == 'multipart/alternative') {
			$content = explode('--'.$boundary, $content);
			$content = $content[2];
			$content = explode('Content-Transfer-Encoding: quoted-printable', $content);
			$content = strip_tags($content[1], '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
		}
		$content = trim($content);

		echo "<p><b>Content-type:</b> $content_type, <b>boundary:</b> $boundary</p>\n";
		echo "<p><b>Raw content:</b><br /><xmp>".$content.'</xmp></p>';
		
		$btpos = strpos($content, $bodyterminator);
		if ($btpos) {
			$content = substr($content, 0, $btpos);
		}
		$content = trim($content);

		$blah = explode("\n", $content);
		$firstline = $blah[0];

		if ($use_phoneemail) {
			$btpos = strpos($firstline, $phoneemail_separator);
			if ($btpos) {
				$userpassstring = trim(substr($firstline, 0, $btpos));
				$content = trim(substr($content, $btpos+strlen($phoneemail_separator), strlen($content)));
				$btpos = strpos($content, $phoneemail_separator);
				if ($btpos) {
					$userpassstring = trim(substr($content, 0, $btpos));
					$content = trim(substr($content, $btpos+strlen($phoneemail_separator), strlen($content)));
				}
			}
			$contentfirstline = $blah[1];
		} else {
			$userpassstring = $firstline;
			$contentfirstline = '';
		}

		$blah = explode(':', $userpassstring);
		$user_login = $blah[0];
		$user_pass = $blah[1];

		$content = $contentfirstline.str_replace($firstline, '', $content);
		$content = trim($content);

		echo "<p><b>Login:</b> $user_login, <b>Pass:</b> $user_pass</p>";

		$sql = "SELECT ID, user_level FROM $tableusers WHERE user_login='$user_login' AND user_pass='$user_pass' ORDER BY ID DESC LIMIT 1";
		$result = mysql_query($sql);

		if (!mysql_num_rows($result)) {
			echo '<p><b>Wrong login or password.</b></p></div>';
			continue;
		}

		$row = mysql_fetch_object($result);
		$user_level = $row->user_level;
		$post_author = $row->ID;

		if ($user_level > 0) {

			$post_title = xmlrpc_getposttitle($content);
			$post_category = xmlrpc_getpostcategory($content);

			if ($post_title == '') {
				$post_title = $subject;
			}
			if ($post_category == '') {
				$post_category = $default_category;
			}

			if ($autobr) {
				$content = autobrize($content);
			}

			if (!$thisisforfunonly) {
				$post_title = addslashes(trim($post_title));
				$content = addslashes(trim($content));
				$sql = "INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category) VALUES ($post_author, '$post_date', '$content', '$post_title', $post_category)";
				$result = mysql_query($sql) or die('Couldn\'t add post: '.mysql_error());
				$post_ID = mysql_insert_id();

				if (isset($sleep_after_edit) && $sleep_after_edit > 0) {
					sleep($sleep_after_edit);
				}

				$blog_ID = 1;
				rss_update($blog_ID);
				pingWeblogs($blog_ID);
				pingCafelog($cafelogID, $post_title, $post_ID);
				pingBlogs($blog_ID);
				pingback($content, $post_ID);
			}
			echo "\n<p><b>Posted title:</b> $post_title<br />";
			echo "\n<b>Posted content:</b><br /><xmp>".$content.'</xmp></p>';

			if(!$pop3->delete($iCount)) {
				echo '<p>oops '.$pop3->ERROR.'</p></div>';
				$pop3->reset();
				exit;
			} else {
				echo "<p>Mission complete, message <b>$iCount</b> deleted </p>";
			}

		} else {
			echo '<p><b>Level 0 users can\'t post.</b></p>';
		}
		echo '</div>';
		if ($output_debugging_info) {
			ob_end_flush();
		} else {
			ob_end_clean();
		}
	}
}

$pop3->quit();

timer_stop($output_debugging_info);
exit;

?>
