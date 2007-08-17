<?php

// The admin side of our 1.0 update system

function core_update_footer( $msg ) {
	$cur = get_option( 'update_core' );
	
	if ( ! isset( $cur->response ) )
		return $msg;

	switch ( $cur->response ) {
	case 'development' :
		return sprintf( __( '| You are using a development version (%s). Cool! Please <a href="%s">stay updated</a>.' ), $GLOBALS['wp_version'], 'http://wordpress.org/download/svn/' );
	break;

	case 'upgrade' :
		add_action( 'admin_footer', 'update_nag' );
		return sprintf( __( '| <strong>Your WordPress %s is out of date. <a href="%s">Please update</a>.</strong>' ), $GLOBALS['wp_version'], $cur->url );
	break;


	case 'latest' :
		return sprintf( __( '| Version %s' ), $GLOBALS['wp_version'] );
	break;
	}
}
add_filter( 'update_footer', 'core_update_footer' );

function update_nag() {
$cur = get_option( 'update_core' );
?>
<div id="update-nag"><?php printf( __('Update Available! <a href="%s">Please upgrade now</a>.'), $cur->url ); ?></div>
<?php
}

function update_nag_body( $class ) {
	$cur = get_option( 'update_core' );
	
	if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
		return $class;

	return "nagtime $class";
}
add_filter( 'admin_body_class', 'update_nag_body' );

?>