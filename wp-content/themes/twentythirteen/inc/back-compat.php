<?php
/**
 * Prevent switching to Twenty Thirteen on old versions of WordPress. Switches
 * to the previously activated theme or the default theme.
 */
function twentythirteen_switch_theme( $theme_name, $theme ) {
	if ( version_compare( $GLOBALS['wp_version'], '3.6', '>=' ) )
		return;

	if ( 'twentythirteen' != $theme->template )
		switch_theme( $theme->template, $theme->stylesheet );
	elseif ( 'twentythirteen' != WP_DEFAULT_THEME )
		switch_theme( WP_DEFAULT_THEME );

	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'twentythirteen_upgrade_notice' );
}
add_action( 'after_switch_theme', 'twentythirteen_switch_theme', 10, 2 );

function twentythirteen_upgrade_notice() {
	$message = sprintf( __( 'Twenty Thirteen requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}