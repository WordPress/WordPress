<?php

require('../wp-config.php');

$user = get_userdatabylogin($log);
	
if (0 == $user->user_level) {
	$redirect_to = $site . '/wp-admin/profile.php';
} else {
	$redirect_to = $site . '/wp-admin/post.php';
}
header ("Location: $redirect_to");
?>