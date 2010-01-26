<?php
/**
 * Gets the email message from the user's mailbox to add as
 * a WordPress post. Mailbox connection information must be
 * configured under Settings > Writing
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require(dirname(__FILE__) . '/wp-load.php');

if ( is_multisite() && ( !defined( 'POST_BY_EMAIL' ) || !POST_BY_EMAIL ) )
	die( __( 'This action has been disabled by the administrator' ) );

/** Allow a plugin to do a complete takeover of Post by Email **/
do_action('wp-mail.php');

/** Get the POP3 class with which to access the mailbox. */
require_once( ABSPATH . WPINC . '/class-pop3.php' );

/** Only check at this interval for new messages. */
if ( !defined('WP_MAIL_INTERVAL') )
	define('WP_MAIL_INTERVAL', 300); // 5 minutes

$last_checked = get_transient('mailserver_last_checked');

if ( $last_checked )
	wp_die(__('Slow down cowboy, no need to check for new mails so often!'));

set_transient('mailserver_last_checked', true, WP_MAIL_INTERVAL);

$time_difference = get_option('gmt_offset') * 3600;

$phone_delim = '::';

$pop3 = new POP3();
$count = 0;

if ( ! $pop3->connect(get_option('mailserver_url'), get_option('mailserver_port') ) ||
	! $pop3->user(get_option('mailserver_login')) ||
	( ! $count = $pop3->pass(get_option('mailserver_pass')) ) ) {
		$pop3->quit();
		wp_die( ( 0 === $count ) ? __('There doesn&#8217;t seem to be any new mail.') : esc_html($pop3->ERROR) );
}

for ( $i = 1; $i <= $count; $i++ ) {

	$message = $pop3->get($i);

	$bodysignal = false;
	$boundary = '';
	$charset = '';
	$content = '';
	$content_type = '';
	$content_transfer_encoding = '';
	$post_author = 1;
	$author_found = false;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	foreach ($message as $line) {
		// body signal
		if ( strlen($line) < 3 )
			$bodysignal = true;
		if ( $bodysignal ) {
			$content .= $line;
		} else {
			if ( preg_match('/Content-Type: /i', $line) ) {
				$content_type = trim($line);
				$content_type = substr($content_type, 14, strlen($content_type) - 14);
				$content_type = explode(';', $content_type);
				if ( ! empty( $content_type[1] ) ) {
					$charset = explode('=', $content_type[1]);
					$charset = ( ! empty( $charset[1] ) ) ? trim($charset[1]) : '';
				}
				$content_type = $content_type[0];
			}
			if ( preg_match('/Content-Transfer-Encoding: /i', $line) ) {
				$content_transfer_encoding = trim($line);
				$content_transfer_encoding = substr($content_transfer_encoding, 27, strlen($content_transfer_encoding) - 27);
				$content_transfer_encoding = explode(';', $content_transfer_encoding);
				$content_transfer_encoding = $content_transfer_encoding[0];
			}
			if ( ( $content_type == 'multipart/alternative' ) && ( false !== strpos($line, 'boundary="') ) && ( '' == $boundary ) ) {
				$boundary = trim($line);
				$boundary = explode('"', $boundary);
				$boundary = $boundary[1];
			}
			if (preg_match('/Subject: /i', $line)) {
				$subject = trim($line);
				$subject = substr($subject, 9, strlen($subject) - 9);
				// Captures any text in the subject before $phone_delim as the subject
				if ( function_exists('iconv_mime_decode') ) {
					$subject = iconv_mime_decode($subject, 2, get_option('blog_charset'));
				} else {
					$subject = wp_iso_descrambler($subject);
				}
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
					echo '<p>' . sprintf(__('Author is %s'), $author) . '</p>';
					$userdata = get_user_by_email($author);
					if ( empty($userdata) ) {
						$author_found = false;
					} else {
						$post_author = $userdata->ID;
						$author_found = true;
					}
				} else {
					$author_found = false;
				}
			}

			if (preg_match('/Date: /i', $line)) { // of the form '20 Mar 2002 20:32:37'
				$ddate = trim($line);
				$ddate = str_replace('Date: ', '', $ddate);
				if (strpos($ddate, ',')) {
					$ddate = trim(substr($ddate, strpos($ddate, ',') + 1, strlen($ddate)));
				}
				$date_arr = explode(' ', $ddate);
				$date_time = explode(':', $date_arr[3]);

				$ddate_H = $date_time[0];
				$ddate_i = $date_time[1];
				$ddate_s = $date_time[2];

				$ddate_m = $date_arr[1];
				$ddate_d = $date_arr[0];
				$ddate_Y = $date_arr[2];
				for ( $j = 0; $j < 12; $j++ ) {
					if ( $ddate_m == $dmonths[$j] ) {
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
	}

	// Set $post_status based on $author_found and on author's publish_posts capability
	if ( $author_found ) {
		$user = new WP_User($post_author);
		$post_status = ( $user->has_cap('publish_posts') ) ? 'publish' : 'pending';
	} else {
		// Author not found in DB, set status to pending.  Author already set to admin.
		$post_status = 'pending';
	}

	$subject = trim($subject);

	if ( $content_type == 'multipart/alternative' ) {
		$content = explode('--'.$boundary, $content);
		$content = $content[2];
		// match case-insensitive content-transfer-encoding
		if ( preg_match( '/Content-Transfer-Encoding: quoted-printable/i', $content, $delim) ) {
			$content = explode($delim[0], $content);
			$content = $content[1];
		}
		$content = strip_tags($content, '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
	}
	$content = trim($content);

	//Give Post-By-Email extending plugins full access to the content
	//Either the raw content or the content of the last quoted-printable section
	$content = apply_filters('wp_mail_original_content', $content);

	if ( false !== stripos($content_transfer_encoding, "quoted-printable") ) {
		$content = quoted_printable_decode($content);
	}

	if ( function_exists('iconv') && ! empty( $charset ) ) {
		$content = iconv($charset, get_option('blog_charset'), $content);
	}

	// Captures any text in the body after $phone_delim as the body
	$content = explode($phone_delim, $content);
	$content = empty( $content[1] ) ? $content[0] : $content[1];

	$content = trim($content);

	$post_content = apply_filters('phone_content', $content);

	$post_title = xmlrpc_getposttitle($content);

	if ($post_title == '') $post_title = $subject;

	$post_category = array(get_option('default_email_category'));

	$post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
	$post_data = add_magic_quotes($post_data);

	$post_ID = wp_insert_post($post_data);
	if ( is_wp_error( $post_ID ) )
		echo "\n" . $post_ID->get_error_message();

	// We couldn't post, for whatever reason. Better move forward to the next email.
	if ( empty( $post_ID ) )
		continue;

	do_action('publish_phone', $post_ID);

	echo "\n<p>" . sprintf(__('<strong>Author:</strong> %s'), esc_html($post_author)) . '</p>';
	echo "\n<p>" . sprintf(__('<strong>Posted title:</strong> %s'), esc_html($post_title)) . '</p>';

	if(!$pop3->delete($i)) {
		echo '<p>' . sprintf(__('Oops: %s'), esc_html($pop3->ERROR)) . '</p>';
		$pop3->reset();
		exit;
	} else {
		echo '<p>' . sprintf(__('Mission complete.  Message <strong>%s</strong> deleted.'), $i) . '</p>';
	}

}

$pop3->quit();

?>
