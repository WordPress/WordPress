<?php

// The admin side of our 1.0 update system

function core_update_footer( $msg ) {
	if ( !current_user_can('manage_options') )
		return sprintf( '| '.__( 'Version %s' ), $GLOBALS['wp_version'] );

	$cur = get_option( 'update_core' );

	switch ( $cur->response ) {
	case 'development' :
		return sprintf( '| '.__( 'You are using a development version (%s). Cool! Please <a href="%s">stay updated</a>.' ), $GLOBALS['wp_version'], 'http://wordpress.org/download/svn/' );
	break;

	case 'upgrade' :
		return sprintf( '| <strong>'.__( 'Your WordPress %s is out of date. <a href="%s">Please update</a>.' ).'</strong>', $GLOBALS['wp_version'], $cur->url );
	break;

	case 'latest' :
	default :
		return sprintf( '| '.__( 'Version %s' ), $GLOBALS['wp_version'] );
	break;
	}
}
add_filter( 'update_footer', 'core_update_footer' );

function update_nag() {
	$cur = get_option( 'update_core' );

	if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
		return false;

	if ( current_user_can('manage_options') )
		$msg = sprintf( __('A new version of WordPress is available! <a href="%s">Please update now</a>.'), $cur->url );
	else
		$msg = __('A new version of WordPress is available! Please notify the site administrator.');

	echo "<div id='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'update_nag', 3 );

function wp_update_plugins() {
	global $wp_version;

	if ( !function_exists('fsockopen') )
		return false;

	$plugins = get_plugins();
	$active  = get_option( 'active_plugins' );
	$current = get_option( 'update_plugins' );

	$new_option = '';
	$new_option->last_checked = time();

	$plugin_changed = false;
	foreach ( $plugins as $file => $p ) {
		$new_option->checked[ $file ] = $p['Version'];

		if ( !isset( $current->checked[ $file ] ) ) {
			$plugin_changed = true;
			continue;
		}

		if ( $current->checked[ $file ] != $p['Version'] )
			$plugin_changed = true;
	}

	if (
		isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		!$plugin_changed
	)
		return false;

	$to_send->plugins = $plugins;
	$to_send->active = $active;
	$send = serialize( $to_send );

	$request = 'plugins=' . urlencode( $send );
	$http_request  = "POST /plugins/update-check/1.0/ HTTP/1.0\r\n";
	$http_request .= "Host: api.wordpress.org\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url') . "\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;

	$response = '';
	if( false != ( $fs = @fsockopen( 'api.wordpress.org', 80, $errno, $errstr, 3) ) && is_resource($fs) ) {
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}

	$response = unserialize( $response[1] );

	if ( $response )
		$new_option->response = $response;

	update_option( 'update_plugins', $new_option );
}
add_action( 'load-plugins.php', 'wp_update_plugins' );

function wp_plugin_update_row( $file ) {
	global $plugin_data;
	$current = get_option( 'update_plugins' );
	if ( !isset( $current->response[ $file ] ) )
		return false;

	$r = $current->response[ $file ];

	echo "<tr><td colspan='5' class='plugin-update'>";
	printf( __('There is a new version of %s available. <a href="%s">Download version %s here</a>.'), $plugin_data['Name'], $r->url, $r->new_version );
	echo "</td></tr>";
}
add_action( 'after_plugin_row', 'wp_plugin_update_row' );

?>
