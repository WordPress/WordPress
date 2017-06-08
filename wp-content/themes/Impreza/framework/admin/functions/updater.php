<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Auto-updater for the plugins bundled with the theme
 */

add_filter( 'us_config_addons', 'us_api_addons' );

function us_api_addons( $plugins, $force_request = FALSE ) {

	$license_activated = get_option('us_license_activated', 0);
	$license_secret = get_option('us_license_secret');

	if ( $license_activated AND $license_secret != '' ) {

		$urlparts = parse_url(site_url());
		$domain = $urlparts['host'];

		$url = "https://help.us-themes.com/us.api/check_addons_update/". strtolower( US_THEMENAME ) ."?secret=" . urlencode( $license_secret ) . "&domain=" . urlencode( $domain ) . "&current_version=" . urlencode( US_THEMEVERSION );
		$transient = 'us_update_addons_data_' . US_THEMENAME;

		if ( false !== $results = get_transient( $transient ) ) {
			$update_addons_data = $results;
		}

		if ( ( empty( $update_addons_data ) OR $force_request ) AND $results = us_api_remote_request( $url ) ) {
			set_transient( $transient, $results, 1800 ); // TODO: move to config
			$update_addons_data = $results;
		}

		if ( ! empty( $update_addons_data->data ) ) {
			foreach( $plugins as $i => $plugin ) {
				$slug = $plugin['slug'];
				if ( isset( $update_addons_data->data->$slug ) ) {
					$plugins[$i]['version'] = $update_addons_data->data->$slug->new_version;
					$plugins[$i]['source'] = $update_addons_data->data->$slug->package;
				}
			}
		}
	}

	return $plugins;
}

$addons = us_config( 'addons', array() );

foreach( $addons as $i => $addon ) {
	if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
		unset( $addons[$i] );
	}
}

if ( empty( $addons ) ) {
	return;
}

// Transient hook for automatical updates of bundled plugins
add_action( 'site_transient_update_plugins', 'us_addons_transient_update' );
function us_addons_transient_update( $trans ) {

	$installed_plugins = get_plugins();

	$addons = us_config( 'addons', array() );

	foreach( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		$plugin_basename = sprintf( '%s/%s.php', $addon['slug'], $addon['slug'] );

		if ( ! isset( $installed_plugins[ $plugin_basename ] ) ) {
			continue;
		}

		if ( version_compare( $installed_plugins[ $plugin_basename ]['Version'], $addon['version'], '<' ) ) {
			$trans->response[ $plugin_basename ] = new StdClass();
			$trans->response[ $plugin_basename ]->plugin = $plugin_basename;
			$trans->response[ $plugin_basename ]->url = $addon['changelog_url'];
			$trans->response[ $plugin_basename ]->slug = $addon['slug'];
			$trans->response[ $plugin_basename ]->package = $addon['source'];
			$trans->response[ $plugin_basename ]->new_version = $addon['version'];
			$trans->response[ $plugin_basename ]->id = '0';
		}
	}

	return $trans;
}

// Seen when user clicks "view details" on the plugin listing page
add_action( 'install_plugins_pre_plugin-information', 'us_addons_update_popup' );
function us_addons_update_popup() {

	if ( ! isset( $_GET['plugin'] ) ) {
		return;
	}

	$plugin_slug = sanitize_file_name( $_GET['plugin'] );

	$addons = us_config( 'addons', array() );

	foreach( $addons as $i => $addon ) {
		if ( empty( $addons[$i]['version'] ) OR empty( $addons[$i]['source'] ) ) {
			unset( $addons[$i] );
		}
	}

	foreach ( $addons as $addon ) {
		if ( $addon['slug'] == $plugin_slug ) {
			$changelog_url = $addon['changelog_url'];

			echo '<html><body style="height: 90%; background: #fcfcfc"><p>See the <a href="' . $changelog_url . '" ' . 'target="_blank">' . $changelog_url . '</a> for the detailed changelog</p></body></html>';

			exit;
		}
	}
}

add_action( 'admin_head', 'us_remove_bsf_update_counter' );
function us_remove_bsf_update_counter() {
	remove_action( 'admin_head', 'bsf_update_counter', 999 );
}
