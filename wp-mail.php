<?php
require(dirname(__FILE__) . '/wp-config.php');

require_once(ABSPATH.WPINC.'/class-pop3.php');

error_reporting(2037);

$time_difference = get_settings('gmt_offset') * 3600;

$phone_delim = get_settings('use_phoneemail');
if (empty($phone_delim)) $phone_delim = '::';

$pop3 = new POP3();

if (!$pop3->connect(get_settings('mailserver_url'), get_settings('mailserver_port'))) :
	echo "Ooops $pop3->ERROR <br />\n";
	exit;
endif;

$count = $pop3->login(get_settings('mailserver_login'), get_settings('mailserver_pass'));
if (0 == $count) die(__('There doesn&#8217;t seem to be any new mail.'));


for ($i=1; $i <= $count; $i++) :

	$message = $pop3->get($i);

	if(!$pop3->delete($i)) {
		echo '<p>Oops '.$pop3->ERROR.'</p></div>';
		$pop3->reset();
		exit;
	} else {
		echo "<p>Mission complete, message <strong>$i</strong> deleted.</p>";
	}

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
				if (!preg_match('#\=\?(.+)\?Q\?(.+)\?\=#i', $subject)) {
				  $subject = wp_iso_descrambler($subject);
				}
				// Captures any text in the subject before $phone_delim as the subject
				$subject = explode($phone_delim, $subject);
				$subject = $subject[0];
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

	$content = apply_filters('phone_content', $content);

	$post_title = xmlrpc_getposttitle($content);

	if ($post_title == '') $post_title = $subject;

	if (empty($post_categories)) $post_categories[] = get_settings('default_email_category');

	$post_title = addslashes(trim($post_title));
	// Make sure that we get a nice post-slug
	$post_name = sanitize_title( $post_title );
	$content = preg_replace("|\n([^\n])|", " $1", $content);
	$content = addslashes(trim($content));

	$sql = "INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_title, post_name, post_modified, post_modified_gmt) VALUES (1, '$post_date', '$post_date_gmt', '$content', '$post_title', '$post_name', '$post_date', '$post_date_gmt')";

	$result = $wpdb->query($sql);
	$post_ID = $wpdb->insert_id;

	do_action('publish_post', $post_ID);
	do_action('publish_phone', $post_ID);
	pingback($content, $post_ID);

	echo "\n<p><b>Posted title:</b> $post_title<br />";
	echo "\n<b>Posted content:</b><br /><pre>".$content.'</pre></p>';

	if (!$post_categories) $post_categories[] = 1;
	foreach ($post_categories as $post_category) :
	$post_category = intval($post_category);

	// Double check it's not there already
	$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_ID AND category_id = $post_category");

	 if (!$exists && $result) { 
		$wpdb->query("
		INSERT INTO $wpdb->post2cat
		(post_id, category_id)
		VALUES
		($post_ID, $post_category)
		");
	}
endforeach;

endfor;

$pop3->quit();

?>