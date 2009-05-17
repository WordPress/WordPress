<?php
/**
 * WordPress Administration Update API
 *
 * @package WordPress
 * @subpackage Admin
 */

// The admin side of our 1.1 update system

/**
 * Selects the first update version from the update_core option
 *
 * @return object the response from the API
 */
function get_preferred_from_update_core() {
	$updates = get_core_updates();
	if ( !is_array( $updates ) )
		return false;
	if ( empty( $updates ) )
		return (object)array('response' => 'latest');
	return $updates[0];
}

/**
 * Get available core updates
 *
 * @param array $options Set $options['dismissed'] to true to show dismissed upgrades too,
 * 	set $options['available'] to false to skip not-dimissed updates.
 * @return array Array of the update objects
 */
function get_core_updates( $options = array() ) {
	$options = array_merge( array('available' => true, 'dismissed' => false ), $options );
	$dismissed = get_option( 'dismissed_update_core' );
	if ( !is_array( $dismissed ) ) $dismissed = array();
	$from_api = get_transient( 'update_core' );
	if ( empty($from_api) )
		return false;
	if ( !isset( $from_api->updates ) || !is_array( $from_api->updates ) ) return false;
	$updates = $from_api->updates;
	if ( !is_array( $updates ) ) return false;
	$result = array();
	foreach($updates as $update) {
		if ( array_key_exists( $update->current.'|'.$update->locale, $dismissed ) ) {
			if ( $options['dismissed'] ) {
				$update->dismissed = true;
				$result[]= $update;
			}
		} else {
			if ( $options['available'] ) {
				$update->dismissed = false;
				$result[]= $update;
			}
		}
	}
	return $result;
}

function dismiss_core_update( $update ) {
	$dismissed = get_option( 'dismissed_update_core' );
	$dismissed[ $update->current.'|'.$update->locale ] = true;
	return update_option( 'dismissed_update_core', $dismissed );
}

function undismiss_core_update( $version, $locale ) {
	$dismissed = get_option( 'dismissed_update_core' );
	$key = $version.'|'.$locale;
	if ( !isset( $dismissed[$key] ) ) return false;
	unset( $dismissed[$key] );
	return update_option( 'dismissed_update_core', $dismissed );
}

function find_core_update( $version, $locale ) {
	$from_api = get_transient( 'update_core' );
	if ( !is_array( $from_api->updates ) ) return false;
	$updates = $from_api->updates;
	foreach($updates as $update) {
		if ( $update->current == $version && $update->locale == $locale )
			return $update;
	}
	return false;
}

function core_update_footer( $msg = '' ) {
	if ( !current_user_can('manage_options') )
		return sprintf( __( 'Version %s' ), $GLOBALS['wp_version'] );

	$cur = get_preferred_from_update_core();
	if ( ! isset( $cur->current ) )
		$cur->current = '';

	if ( ! isset( $cur->url ) )
		$cur->url = '';

	if ( ! isset( $cur->response ) )
		$cur->response = '';

	switch ( $cur->response ) {
	case 'development' :
		return sprintf( __( 'You are using a development version (%1$s). Cool! Please <a href="%2$s">stay updated</a>.' ), $GLOBALS['wp_version'], 'update-core.php');
	break;

	case 'upgrade' :
		if ( current_user_can('manage_options') ) {
			return sprintf( '<strong>'.__( '<a href="%1$s">Get Version %2$s</a>' ).'</strong>', 'update-core.php', $cur->current);
			break;
		}

	case 'latest' :
	default :
		return sprintf( __( 'Version %s' ), $GLOBALS['wp_version'] );
	break;
	}
}
add_filter( 'update_footer', 'core_update_footer' );

function update_nag() {
	global $pagenow;

	if ( 'update-core.php' == $pagenow )
		return;

	$cur = get_preferred_from_update_core();

	if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
		return false;

	if ( current_user_can('manage_options') )
		$msg = sprintf( __('WordPress %1$s is available! <a href="%2$s">Please update now</a>.'), $cur->current, 'update-core.php' );
	else
		$msg = sprintf( __('WordPress %1$s is available! Please notify the site administrator.'), $cur->current );

	echo "<div id='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'update_nag', 3 );

// Called directly from dashboard
function update_right_now_message() {
	$cur = get_preferred_from_update_core();

	$msg = sprintf( __('You are using <span class="b">WordPress %s</span>.'), $GLOBALS['wp_version'] );
	if ( isset( $cur->response ) && $cur->response == 'upgrade' && current_user_can('manage_options') )
		$msg .= " <a href='update-core.php' class='button'>" . sprintf( __('Update to %s'), $cur->current ? $cur->current : __( 'Latest' ) ) . '</a>';

	echo "<span id='wp-version-message'>$msg</span>";
}

function wp_plugin_update_row( $file, $plugin_data ) {
	$current = get_transient( 'update_plugins' );
	if ( !isset( $current->response[ $file ] ) )
		return false;

	$r = $current->response[ $file ];

	$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
	$plugin_name = wp_kses( $plugin_data['Name'], $plugins_allowedtags );
		
	$details_url = admin_url('plugin-install.php?tab=plugin-information&plugin=' . $r->slug . '&TB_iframe=true&width=600&height=800');

	echo '<tr><td colspan="5" class="plugin-update">';
	if ( ! current_user_can('update_plugins') )
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s Details</a>.'), $plugin_name, clean_url($details_url), esc_attr($plugin_name), $r->new_version );
	else if ( empty($r->package) )
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s Details</a> <em>automatic upgrade unavailable for this plugin</em>.'), $plugin_name, clean_url($details_url), esc_attr($plugin_name), $r->new_version );
	else
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s Details</a> or <a href="%5$s">upgrade automatically</a>.'), $plugin_name, clean_url($details_url), esc_attr($plugin_name), $r->new_version, wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $file, 'upgrade-plugin_' . $file) );
	
	do_action( "in_plugin_update_message-$file", $plugin_data, $r );
	
	echo '</td></tr>';
}
add_action( 'after_plugin_row', 'wp_plugin_update_row', 10, 2 );

function wp_update_plugin($plugin, $feedback = '') {

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$upgrader = new Plugin_Upgrader();
	return $upgrader->upgrade($plugin);
}

function wp_update_theme($theme, $feedback = '') {

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$upgrader = new Theme_Upgrader();
	return $upgrader->upgrade($theme);
}


function wp_update_core($current, $feedback = '') {

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$upgrader = new Core_Upgrader();
	return $upgrader->upgrade($current);

}

function maintenance_nag() {
	global $upgrading;
	if ( ! isset( $upgrading ) )
		return false;

	if ( current_user_can('manage_options') )
		$msg = sprintf( __('An automated WordPress update has failed to complete - <a href="%s">please attempt the update again now</a>.'), 'update-core.php' );
	else
		$msg = __('An automated WordPress update has failed to complete! Please notify the site administrator.');

	echo "<div id='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'maintenance_nag' );

?>
