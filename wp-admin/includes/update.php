<?php
/**
 * WordPress Administration Update API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Selects the first update version from the update_core option.
 *
 * @return bool|object The response from the API on success, false on failure.
 */
function get_preferred_from_update_core() {
	$updates = get_core_updates();
	if ( ! is_array( $updates ) )
		return false;
	if ( empty( $updates ) )
		return (object) array( 'response' => 'latest' );
	return $updates[0];
}

/**
 * Get available core updates.
 *
 * @param array $options Set $options['dismissed'] to true to show dismissed upgrades too,
 * 	set $options['available'] to false to skip not-dismissed updates.
 * @return bool|array Array of the update objects on success, false on failure.
 */
function get_core_updates( $options = array() ) {
	$options = array_merge( array( 'available' => true, 'dismissed' => false ), $options );
	$dismissed = get_site_option( 'dismissed_update_core' );

	if ( ! is_array( $dismissed ) )
		$dismissed = array();

	$from_api = get_site_transient( 'update_core' );

	if ( ! isset( $from_api->updates ) || ! is_array( $from_api->updates ) )
		return false;

	$updates = $from_api->updates;
	$result = array();
	foreach ( $updates as $update ) {
		if ( $update->response == 'autoupdate' )
			continue;

		if ( array_key_exists( $update->current . '|' . $update->locale, $dismissed ) ) {
			if ( $options['dismissed'] ) {
				$update->dismissed = true;
				$result[] = $update;
			}
		} else {
			if ( $options['available'] ) {
				$update->dismissed = false;
				$result[] = $update;
			}
		}
	}
	return $result;
}

/**
 * Gets the best available (and enabled) Auto-Update for WordPress Core.
 *
 * If there's 1.2.3 and 1.3 on offer, it'll choose 1.3 if the install allows it, else, 1.2.3
 *
 * @since 3.7.0
 *
 * @return bool|array False on failure, otherwise the core update offering.
 */
function find_core_auto_update() {
	$updates = get_site_transient( 'update_core' );
	if ( ! $updates || empty( $updates->updates ) )
		return false;

	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$auto_update = false;
	$upgrader = new WP_Automatic_Updater;
	foreach ( $updates->updates as $update ) {
		if ( 'autoupdate' != $update->response )
			continue;

		if ( ! $upgrader->should_update( 'core', $update, ABSPATH ) )
			continue;

		if ( ! $auto_update || version_compare( $update->current, $auto_update->current, '>' ) )
			$auto_update = $update;
	}
	return $auto_update;
}

/**
 * Gets and caches the checksums for the given version of WordPress.
 *
 * @since 3.7.0
 *
 * @param string $version Version string to query.
 * @param string $locale  Locale to query.
 * @return bool|array False on failure. An array of checksums on success.
 */
function get_core_checksums( $version, $locale ) {
	$return = array();

	$url = $http_url = 'http://api.wordpress.org/core/checksums/1.0/?' . http_build_query( compact( 'version', 'locale' ), null, '&' );

	if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
		$url = set_url_scheme( $url, 'https' );

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
	);

	$response = wp_remote_get( $url, $options );
	if ( $ssl && is_wp_error( $response ) ) {
		trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
		$response = wp_remote_get( $http_url, $options );
	}

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
		return false;

	$body = trim( wp_remote_retrieve_body( $response ) );
	$body = json_decode( $body, true );

	if ( ! is_array( $body ) || ! isset( $body['checksums'] ) || ! is_array( $body['checksums'] ) )
		return false;

	return $body['checksums'];
}

function dismiss_core_update( $update ) {
	$dismissed = get_site_option( 'dismissed_update_core' );
	$dismissed[ $update->current . '|' . $update->locale ] = true;
	return update_site_option( 'dismissed_update_core', $dismissed );
}

function undismiss_core_update( $version, $locale ) {
	$dismissed = get_site_option( 'dismissed_update_core' );
	$key = $version . '|' . $locale;

	if ( ! isset( $dismissed[$key] ) )
		return false;

	unset( $dismissed[$key] );
	return update_site_option( 'dismissed_update_core', $dismissed );
}

function find_core_update( $version, $locale ) {
	$from_api = get_site_transient( 'update_core' );

	if ( ! isset( $from_api->updates ) || ! is_array( $from_api->updates ) )
		return false;

	$updates = $from_api->updates;
	foreach ( $updates as $update ) {
		if ( $update->current == $version && $update->locale == $locale )
			return $update;
	}
	return false;
}

function core_update_footer( $msg = '' ) {
	if ( !current_user_can('update_core') )
		return sprintf( __( 'Version %s' ), get_bloginfo( 'version', 'display' ) );

	$cur = get_preferred_from_update_core();
	if ( ! is_object( $cur ) )
		$cur = new stdClass;

	if ( ! isset( $cur->current ) )
		$cur->current = '';

	if ( ! isset( $cur->url ) )
		$cur->url = '';

	if ( ! isset( $cur->response ) )
		$cur->response = '';

	switch ( $cur->response ) {
	case 'development' :
		return sprintf( __( 'You are using a development version (%1$s). Cool! Please <a href="%2$s">stay updated</a>.' ), get_bloginfo( 'version', 'display' ), network_admin_url( 'update-core.php' ) );
	break;

	case 'upgrade' :
		return sprintf( '<strong>'.__( '<a href="%1$s">Get Version %2$s</a>' ).'</strong>', network_admin_url( 'update-core.php' ), $cur->current);
	break;

	case 'latest' :
	default :
		return sprintf( __( 'Version %s' ), get_bloginfo( 'version', 'display' ) );
	break;
	}
}
add_filter( 'update_footer', 'core_update_footer' );

function update_nag() {
	if ( is_multisite() && !current_user_can('update_core') )
		return false;

	global $pagenow;

	if ( 'update-core.php' == $pagenow )
		return;

	$cur = get_preferred_from_update_core();

	if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
		return false;

	if ( current_user_can('update_core') ) {
		$msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! <a href="%2$s">Please update now</a>.'), $cur->current, network_admin_url( 'update-core.php' ) );
	} else {
		$msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! Please notify the site administrator.'), $cur->current );
	}
	echo "<div class='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'update_nag', 3 );
add_action( 'network_admin_notices', 'update_nag', 3 );

// Called directly from dashboard
function update_right_now_message() {
	$theme_name = wp_get_theme();
	if ( current_user_can( 'switch_themes' ) ) {
		$theme_name = sprintf( '<a href="themes.php">%1$s</a>', $theme_name );
	}

	$msg = sprintf( __( 'WordPress %1$s running %2$s theme.' ), get_bloginfo( 'version', 'display' ), $theme_name );

	if ( current_user_can('update_core') ) {
		$cur = get_preferred_from_update_core();

		if ( isset( $cur->response ) && $cur->response == 'upgrade' )
			$msg .= " <a href='" . network_admin_url( 'update-core.php' ) . "' class='button'>" . sprintf( __('Update to %s'), $cur->current ? $cur->current : __( 'Latest' ) ) . '</a>';
	}

	echo "<p id='wp-version-message'>$msg</p>";
}

function get_plugin_updates() {
	$all_plugins = get_plugins();
	$upgrade_plugins = array();
	$current = get_site_transient( 'update_plugins' );
	foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
		if ( isset( $current->response[ $plugin_file ] ) ) {
			$upgrade_plugins[ $plugin_file ] = (object) $plugin_data;
			$upgrade_plugins[ $plugin_file ]->update = $current->response[ $plugin_file ];
		}
	}

	return $upgrade_plugins;
}

function wp_plugin_update_rows() {
	if ( !current_user_can('update_plugins' ) )
		return;

	$plugins = get_site_transient( 'update_plugins' );
	if ( isset($plugins->response) && is_array($plugins->response) ) {
		$plugins = array_keys( $plugins->response );
		foreach( $plugins as $plugin_file ) {
			add_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
		}
	}
}
add_action( 'admin_init', 'wp_plugin_update_rows' );

function wp_plugin_update_row( $file, $plugin_data ) {
	$current = get_site_transient( 'update_plugins' );
	if ( !isset( $current->response[ $file ] ) )
		return false;

	$r = $current->response[ $file ];

	$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
	$plugin_name = wp_kses( $plugin_data['Name'], $plugins_allowedtags );

	$details_url = self_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $r->slug . '&section=changelog&TB_iframe=true&width=600&height=800');

	$wp_list_table = _get_list_table('WP_Plugins_List_Table');

	if ( is_network_admin() || !is_multisite() ) {
		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';

		if ( ! current_user_can('update_plugins') )
			printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>.'), $plugin_name, esc_url($details_url), esc_attr($plugin_name), $r->new_version );
		else if ( empty($r->package) )
			printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>'), $plugin_name, esc_url($details_url), esc_attr($plugin_name), $r->new_version );
		else
			printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%5$s">update now</a>.'), $plugin_name, esc_url($details_url), esc_attr($plugin_name), $r->new_version, wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file, 'upgrade-plugin_' . $file) );

		/**
		 * Fires at the end of the update message container in each
		 * row of the plugins list table.
		 *
		 * The dynamic portion of the hook name, $file, refers to the path
		 * of the plugin's primary file relative to the plugins directory.
		 *
		 * @since 2.8.0
		 *
		 * @param array $plugin_data {
		 *     An array of plugin metadata.
		 *
		 *     @type string $name         The human-readable name of the plugin.
		 *     @type string $plugin_uri   Plugin URI.
		 *     @type string $version      Plugin version.
		 *     @type string $description  Plugin description.
		 *     @type string $author       Plugin author.
		 *     @type string $author_uri   Plugin author URI.
		 *     @type string $text_domain  Plugin text domain.
		 *     @type string $domain_path  Relative path to the plugin's .mo file(s).
		 *     @type bool   $network      Whether the plugin can only be activated network wide.
		 *     @type string $title        The human-readable title of the plugin.
		 *     @type string $author_name  Plugin author's name.
		 *     @type bool   $update       Whether there's an available update. Default null.
	 	 * }
	 	 * @param array $r {
	 	 *     An array of metadata about the available plugin update.
	 	 *
	 	 *     @type int    $id           Plugin ID.
	 	 *     @type string $slug         Plugin slug.
	 	 *     @type string $new_version  New plugin version.
	 	 *     @type string $url          Plugin URL.
	 	 *     @type string $package      Plugin update package URL.
	 	 * }
		 */
		do_action( "in_plugin_update_message-{$file}", $plugin_data, $r );

		echo '</div></td></tr>';
	}
}

function get_theme_updates() {
	$themes = wp_get_themes();
	$current = get_site_transient('update_themes');

	if ( ! isset( $current->response ) )
		return array();

	$update_themes = array();
	foreach ( $current->response as $stylesheet => $data ) {
		$update_themes[ $stylesheet ] = wp_get_theme( $stylesheet );
		$update_themes[ $stylesheet ]->update = $data;
	}

	return $update_themes;
}

function wp_theme_update_rows() {
	if ( !current_user_can('update_themes' ) )
		return;

	$themes = get_site_transient( 'update_themes' );
	if ( isset($themes->response) && is_array($themes->response) ) {
		$themes = array_keys( $themes->response );

		foreach( $themes as $theme ) {
			add_action( "after_theme_row_$theme", 'wp_theme_update_row', 10, 2 );
		}
	}
}
add_action( 'admin_init', 'wp_theme_update_rows' );

function wp_theme_update_row( $theme_key, $theme ) {
	$current = get_site_transient( 'update_themes' );
	if ( !isset( $current->response[ $theme_key ] ) )
		return false;
	$r = $current->response[ $theme_key ];
	$themes_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
	$theme_name = wp_kses( $theme['Name'], $themes_allowedtags );

	$details_url = add_query_arg( array( 'TB_iframe' => 'true', 'width' => 1024, 'height' => 800 ), $current->response[ $theme_key ]['url'] );

	$wp_list_table = _get_list_table('WP_MS_Themes_List_Table');

	echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';
	if ( ! current_user_can('update_themes') )
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>.'), $theme['Name'], esc_url($details_url), esc_attr($theme['Name']), $r->new_version );
	else if ( empty( $r['package'] ) )
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a>. <em>Automatic update is unavailable for this theme.</em>'), $theme['Name'], esc_url($details_url), esc_attr($theme['Name']), $r['new_version'] );
	else
		printf( __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%3$s">View version %4$s details</a> or <a href="%5$s">update now</a>.'), $theme['Name'], esc_url($details_url), esc_attr($theme['Name']), $r['new_version'], wp_nonce_url( self_admin_url('update.php?action=upgrade-theme&theme=') . $theme_key, 'upgrade-theme_' . $theme_key) );

	/**
	 * Fires at the end of the update message container in each
	 * row of the themes list table.
	 *
	 * The dynamic portion of the hook name, $theme_key, refers to
	 * the theme slug as found in the WordPress.org themes repository.
	 *
	 * @since 3.1.0
	 *
	 * @param WP_Theme $theme The WP_Theme object.
	 * @param array    $r {
	 *     An array of metadata about the available theme update.
	 *
	 *     @type string $new_version New theme version.
	 *     @type string $url         Theme URL.
	 *     @type string $package     Theme update package URL.
	 * }
	 */
	do_action( "in_theme_update_message-{$theme_key}", $theme, $r );

	echo '</div></td></tr>';
}

function maintenance_nag() {
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version
	global $upgrading;
	$nag = isset( $upgrading );
	if ( ! $nag ) {
		$failed = get_site_option( 'auto_core_update_failed' );
		/*
		 * If an update failed critically, we may have copied over version.php but not other files.
		 * In that case, if the install claims we're running the version we attempted, nag.
		 * This is serious enough to err on the side of nagging.
		 *
		 * If we simply failed to update before we tried to copy any files, then assume things are
		 * OK if they are now running the latest.
		 *
		 * This flag is cleared whenever a successful update occurs using Core_Upgrader.
		 */
		$comparison = ! empty( $failed['critical'] ) ? '>=' : '>';
		if ( version_compare( $failed['attempted'], $wp_version, $comparison ) )
			$nag = true;
	}

	if ( ! $nag )
		return false;

	if ( current_user_can('update_core') )
		$msg = sprintf( __('An automated WordPress update has failed to complete - <a href="%s">please attempt the update again now</a>.'), 'update-core.php' );
	else
		$msg = __('An automated WordPress update has failed to complete! Please notify the site administrator.');

	echo "<div class='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'maintenance_nag' );
add_action( 'network_admin_notices', 'maintenance_nag' );
