<?php

require_once('admin.php');

check_admin_referer();

$errors = update_user($user_ID);

if (count($errors) != 0) {
	foreach ($errors as $id => $error) {
		echo $error . '<br/>';
	}
	exit;
}

if ( 'profile' == $_POST['from'] )
	$to = 'profile.php?updated=true';
else
	$to = 'profile.php?updated=true';

wp_redirect( $to );
exit;

?>