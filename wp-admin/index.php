<?php

require('../wp-config.php');

get_currentuserinfo();

if (0 == $user_level) {
	$redirect_to = $siteurl . '/wp-admin/profile.php';
} else {
	$redirect_to = $siteurl . '/wp-admin/post.php';
}
header ("Location: $redirect_to");
?>