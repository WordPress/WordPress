<?php

require_once('admin.php');

check_admin_referer();

/* if the ICQ UIN has been entered, check to see if it has only numbers */
if (!empty($_POST["newuser_icq"])) {
	if ((ereg("^[0-9]+$",$_POST["newuser_icq"]))==false) {
		die (__("<strong>ERROR</strong>: your ICQ UIN can only be a number, no letters allowed"));
		return false;
	}
}

/* checking e-mail address */
if (empty($_POST["newuser_email"])) {
	die (__("<strong>ERROR</strong>: please type your e-mail address"));
	return false;
} else if (!is_email($_POST["newuser_email"])) {
	die (__("<strong>ERROR</strong>: the e-mail address isn't correct"));
	return false;
}

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

$newuser_firstname = wp_specialchars($_POST['newuser_firstname']);
$newuser_lastname = wp_specialchars($_POST['newuser_lastname']);
$new_display_name = wp_specialchars($_POST['display_name']);
$newuser_nickname = $_POST['newuser_nickname'];
$newuser_nicename = sanitize_title($newuser_nickname);
$newuser_icq = wp_specialchars($_POST['newuser_icq']);
$newuser_aim = wp_specialchars($_POST['newuser_aim']);
$newuser_msn = wp_specialchars($_POST['newuser_msn']);
$newuser_yim = wp_specialchars($_POST['newuser_yim']);
$newuser_email = wp_specialchars($_POST['newuser_email']);
$newuser_url = wp_specialchars($_POST['newuser_url']);
$newuser_url = preg_match('/^(https?|ftps?|mailto|news|gopher):/is', $newuser_url) ? $newuser_url : 'http://' . $newuser_url; 
$user_description = $_POST['user_description'];

$result = $wpdb->query("UPDATE $wpdb->users SET $updatepassword user_email='$newuser_email', user_url='$newuser_url', user_nicename = '$newuser_nicename', display_name = '$new_display_name' WHERE ID = $user_ID");

update_usermeta( $user_ID, 'first_name', $newuser_firstname );
update_usermeta( $user_ID, 'last_name', $newuser_lastname );
update_usermeta( $user_ID, 'nickname', $newuser_nickname );
update_usermeta( $user_ID, 'description', $user_description );
update_usermeta( $user_ID, 'icq', $newuser_icq );
update_usermeta( $user_ID, 'aim', $newuser_aim );
update_usermeta( $user_ID, 'msn', $newuser_msn );
update_usermeta( $user_ID, 'yim', $newuser_yim );

do_action('profile_update', $user_ID);

if ( 'profile' == $_POST['from'] )
	$to = 'profile.php?updated=true';
else
	$to = 'profile.php?updated=true';

wp_redirect( $to );
exit;

?>