<?php

if ( ! function_exists( 'et_core_enable_automatic_updates' ) ) :
function et_core_enable_automatic_updates( $url, $version ) {
	if ( ! is_admin() ) {
		return;
	}

	if ( class_exists( 'ET_Core_Updates' ) ) {
		return;
	}

	$url = trailingslashit( $url ) . 'core/';

	$core_dir = trailingslashit( dirname( __FILE__ ) );

	require_once $core_dir . 'functions.php';
	require_once $core_dir . 'admin/includes/class-updates.php';

	$et_core_updates = new ET_Core_Updates( $url, $version );

	$GLOBALS['et_core_updates'] = $et_core_updates;
}
endif;
