<?php

require('../wp-config.php');
require_once('auth.php');

get_currentuserinfo();

if (0 == $user_level) {
	$redirect_to = get_settings('siteurl') . '/wp-admin/profile.php';
} else {
	$redirect_to = get_settings('siteurl') . '/wp-admin/post.php';
}
header ("Location: $redirect_to");
?>