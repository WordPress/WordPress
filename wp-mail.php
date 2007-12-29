<?php
require(dirname(__FILE__) . '/wp-config.php');

require_once(ABSPATH.WPINC.'/class-pop3.php');

error_reporting(2037);

$time_difference = get_option('gmt_offset') * 3600;

$phone_delim = '::';

$pop3 = new POP3();

if (!$pop3->connect(get_option('mailserver_url'), get_option('mailserver_port')))
	wp_die(wp_specialchars($pop3->ERROR));

if (!$pop3->user(get_option('mailserver_login')))
	wp_die(wp_specialchars($pop3->ERROR));

$count = $pop3->pass(get_option('mailserver_pass'));
if (false === $count)
	wp_die(wp_specialchars($pop3->ERROR));
if (0 == $count)
	echo "<p>There doesn't seem to be any new mail.</p>\n"; // will fall-through to end of for loop

for ($i=1; $i <= $count; $i++) :

	$message = $pop3->get($i);

	$content = '';
	$content_type = '';
	$content_transfer_encoding = '';
	$boundary = '';
	$bodysignal = 0;
	$post_author = 1;
	$author_found = false;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
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
			if (preg_match('/Content-Transfer-Encoding: /i', $line)) {
				$content_transfer_encoding = trim($line);
				$content_transfer_encoding = substr($content_transfer_encoding, 27, strlen($content_transfer_encoding)-14);
				$content_transfer_encoding = explode(';', $content_transfer_encoding);
				$content_transfer_encoding = $content_transfer_encoding[0];
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

			// Set the author using the email address (From or Reply-To, the last used)
			// otherwise use the site admin
			if ( preg_match('/(From|Reply-To): /', $line) )  {
				if ( preg_match('|[a-z0-9_.-]+@[a-z0-9_.-]+(?!.*<)|i', $line, $matches) )
					$author = $matches[0];
				else
					$author = trim($line);
				$author = sanitize_email($author);
				if ( is_email($author) ) {
					echo "Author = {$author} <p>";
					$userdata = get_user_by_email($author);
					if (!$userdata) {
						$post_author = 1;
						$author_found = false;
					} else {
						$post_author = $userdata->ID;
						$author_found = true;
					}
				} else {
					$post_author = 1;
					$author_found = false;
				}
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

	// Set $post_status based on $author_found and on author's publish_posts capability
	if ($author_found) {
		$user = new WP_User($post_author);
		if ($user->has_cap('publish_posts'))
			$post_status = 'publish';
		else
			$post_status = 'pending';
	} else {
		// Author not found in DB, set status to pending.  Author already set to admin.
		$post_status = 'pending';
	}

	$subject = trim($subject);

	if ($content_type == 'multipart/alternative') {
		$content = explode('--'.$boundary, $content);
		$content = $content[2];
		$content = explode('Content-Transfer-Encoding: quoted-printable', $content);
		$content = strip_tags($content[1], '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
	}
	$content = trim($content);

	if (stripos($content_transfer_encoding, "quoted-printable") !== false) {
		$content = quoted_printable_decode($content);
	}

	// Captures any text in the body after $phone_delim as the body
	$content = explode($phone_delim, $content);
	$content[1] ? $content = $content[1] : $content = $content[0];

	$content = trim($content);

	$post_content = apply_filters('phone_content', $content);

	$post_title = xmlrpc_getposttitle($content);

	if ($post_title == '') $post_title = $subject;

	if (empty($post_categories)) $post_categories[] = get_option('default_email_category');

	$post_category = $post_categories;

	$post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
	$post_data = add_magic_quotes($post_data);

	$post_ID = wp_insert_post($post_data);
	if ( is_wp_error( $post_ID ) )
		echo "\n" . $post_ID->get_error_message();

	if (!$post_ID) {
		// we couldn't post, for whatever reason. better move forward to the next email
		continue;
	}

	do_action('publish_phone', $post_ID);

	echo "\n<p><b>Author:</b> " . wp_specialchars($post_author) . "</p>";
	echo "\n<p><b>Posted title:</b> " . wp_specialchars($post_title) . "<br />";

	if(!$pop3->delete($i)) {
		echo '<p>Oops '.wp_specialchars($pop3->ERROR).'</p></div>';
		$pop3->reset();
		exit;
	} else {
		echo "<p>Mission complete, message <strong>$i</strong> deleted.</p>";
	}

endfor;

$pop3->quit();

?>
