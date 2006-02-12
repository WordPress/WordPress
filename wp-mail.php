<?php
require(dirname(__FILE__) . '/wp-config.php');

require_once(ABSPATH.WPINC.'/class-pop3.php');

error_reporting(2037);

$time_difference = get_settings('gmt_offset') * 3600;

$phone_delim = '::';

$pop3 = new POP3();

if (!$pop3->connect(get_settings('mailserver_url'), get_settings('mailserver_port'))) :
	echo "Ooops $pop3->ERROR <br />\n";
	exit;
endif;

$count = $pop3->login(get_settings('mailserver_login'), get_settings('mailserver_pass'));
if (0 == $count) die(__('There doesn&#8217;t seem to be any new mail.'));


for ($i=1; $i <= $count; $i++) :

	$message = $pop3->get($i);

	$content = '';
	$content_type = '';
	$boundary = '';
	$bodysignal = 0;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	foreach ($message as $line) :
		if (strlen($line) < 3) $bodysignal = 1;

		if ($bodysignal) {
			$content .= $line;
		} else {
			if (preg_match('/Content-Type: /i', $line)) {
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
			if (preg_match('/Subject: /i', $line)) {
				$subject = trim($line);
				$subject = substr($subject, 9, strlen($subject)-9);
				$subject = wp_iso_descrambler($subject);
				// Captures any text in the subject before $phone_delim as the subject
				$subject = explode($phone_delim, $subject);
				$subject = $subject[0];
			}

			// Set the author using the email address (To or Reply-To, the last used)
			// otherwise use the site admin
			if (preg_match('/From: /', $line) | preg_match('Reply-To: /', $line))  {
				$author=trim($line);
			if ( ereg("([a-zA-Z0-9\_\-\.]+@[\a-zA-z0-9\_\-\.]+)", $author , $regs) ) {
				$author = $regs[1];
				echo "Author = {$author} <p>";
				$author = $wpdb->escape($author);
				$result = $wpdb->get_row("SELECT ID FROM $wpdb->users WHERE user_email='$author' LIMIT 1");
				if (!$result)
					$post_author = 1;
				else
					$post_author = $result->ID;
			} else
				$post_author = 1;
			}

			if (preg_match('/Date: /i', $line)) { // of the form '20 Mar 2002 20:32:37'
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
				for ($j=0; $j<12; $j++) {
					if ($ddate_m == $dmonths[$j]) {
						$ddate_m = $j+1;
					}
				}

				$time_zn = intval($date_arr[4]) * 36;
				$ddate_U = gmmktime($ddate_H, $ddate_i, $ddate_s, $ddate_m, $ddate_d, $ddate_Y);
				$ddate_U = $ddate_U - $time_zn;
				$post_date = gmdate('Y-m-d H:i:s', $ddate_U + $time_difference);
				$post_date_gmt = gmdate('Y-m-d H:i:s', $ddate_U);
			}
		}
	endforeach;

	$subject = trim(str_replace(get_settings('subjectprefix'), '', $subject));

	if ($content_type == 'multipart/alternative') {
		$content = explode('--'.$boundary, $content);
		$content = $content[2];
		$content = explode('Content-Transfer-Encoding: quoted-printable', $content);
		$content = strip_tags($content[1], '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
	}
	$content = trim($content);
	// Captures any text in the body after $phone_delim as the body
	$content = explode($phone_delim, $content);
	$content[1] ? $content = $content[1] : $content = $content[0];

	echo "<p><b>Content-type:</b> $content_type, <b>boundary:</b> $boundary</p>\n";
	echo "<p><b>Raw content:</b><br /><pre>".$content.'</pre></p>';

	$content = trim($content);

	$post_content = apply_filters('phone_content', $content);

	$post_title = xmlrpc_getposttitle($content);

	if ($post_title == '') $post_title = $subject;

	if (empty($post_categories)) $post_categories[] = get_settings('default_email_category');

	$post_category = $post_categories;

	// or maybe we should leave the choice to email drafts? propose a way
	$post_status = 'publish';

	$post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
	$post_data = add_magic_quotes($post_data);

	$post_ID = wp_insert_post($post_data);

	if (!$post_ID) {
		// we couldn't post, for whatever reason. better move forward to the next email
		continue;
	}

	do_action('publish_phone', $post_ID);

	echo "\n<p><b>Author:</b> $post_author</p>";
	echo "\n<p><b>Posted title:</b> $post_title<br />";
	echo "\n<b>Posted content:</b><br /><pre>".$content.'</pre></p>';

	if(!$pop3->delete($i)) {
		echo '<p>Oops '.$pop3->ERROR.'</p></div>';
		$pop3->reset();
		exit;
	} else {
		echo "<p>Mission complete, message <strong>$i</strong> deleted.</p>";
	}

endfor;

$pop3->quit();

?>