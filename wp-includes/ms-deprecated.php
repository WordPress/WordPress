<?php

function generate_random_password( $len = 8 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_generarte_password()' );
	$random_password = substr(md5(uniqid(microtime())), 0, intval( $len ) );
	$random_password = apply_filters('random_password', $random_password);
	return $random_password;
}

?>