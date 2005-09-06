<?php

require_once('admin.php');

check_admin_referer();

if ( empty($_POST['email']) )
	die (__("<strong>ERROR</strong>: please type your e-mail address"));
elseif ( !is_email($_POST['email']) )
	die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));

$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
do_action('check_passwords', array($user_login, &$pass1, &$pass2));

if ( '' == $pass1 ) {
	if ( '' != $pass2 )
		die (__('<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.'));
	$updatepassword = '';
} else {
	if ('' == $pass2)
		die (__('<strong>ERROR</strong>: you typed your new password only once. Go back to type it twice.'));
	if ( $pass1 != $pass2 )
		die (__('<strong>ERROR</strong>: you typed two different passwords. Go back to correct that.'));
	$newuser_pass = $pass1;
	$updatepassword = "user_pass=MD5('$newuser_pass'), ";
	wp_clearcookie();
	wp_setcookie($user_login, $newuser_pass);
}

$first_name = wp_specialchars($_POST['first_name']);
$last_name = wp_specialchars($_POST['last_name']);
$display_name = wp_specialchars($_POST['display_name']);
$nickname = $_POST['nickname'];
$nicename = sanitize_title($nickname);
$jabber = wp_specialchars($_POST['jabber']);
$aim = wp_specialchars($_POST['aim']);
$yim = wp_specialchars($_POST['yim']);
$email = wp_specialchars($_POST['email']);
$url = wp_specialchars($_POST['url']);
$url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $url) ? $url : 'http://' . $url; 
$user_description = $_POST['user_description'];

$result = $wpdb->query("UPDATE $wpdb->users SET $updatepassword user_email='$email', user_url='$url', user_nicename = '$nicename', display_name = '$display_name' WHERE ID = '$user_ID'");

update_usermeta( $user_ID, 'first_name', $first_name );
update_usermeta( $user_ID, 'last_name', $last_name );
update_usermeta( $user_ID, 'nickname', $nickname );
update_usermeta( $user_ID, 'description', $user_description );
update_usermeta( $user_ID, 'jabber', $jabber );
update_usermeta( $user_ID, 'aim', $aim );
update_usermeta( $user_ID, 'yim', $yim );

do_action('profile_update', $user_ID);

if ( 'profile' == $_POST['from'] )
	$to = 'profile.php?updated=true';
else
	$to = 'profile.php?updated=true';

wp_redirect( $to );
exit;

?>