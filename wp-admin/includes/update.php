<?php

// The admin side of our 1.1 update system

function core_update_footer( $msg = '' ) {
	if ( !current_user_can('manage_options') )
		return sprintf( '| '.__( 'Version %s' ), $GLOBALS['wp_version'] );

	$cur = get_option( 'update_core' );

	switch ( $cur->response ) {
	case 'development' :
		return sprintf( '| '.__( 'You are using a development version (%s). Cool! Please <a href="%s">stay updated</a>.' ), $GLOBALS['wp_version'], $cur->url, $cur->current );
	break;

	case 'upgrade' :
		if ( current_user_can('manage_options') ) {
			return sprintf( '| <strong>'.__( '<a href="%2$s">Get Version %3$s</a>' ).'</strong>', $GLOBALS['wp_version'], $cur->url, $cur->current );
			break;
		}

	case 'latest' :
	default :
		return sprintf( '| '.__( 'Version %s' ), $GLOBALS['wp_version'], $cur->url, $cur->current );
	break;
	}
}
add_filter( 'update_footer', 'core_update_footer' );

function update_nag() {
	$cur = get_option( 'update_core' );

	if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
		return false;

	if ( current_user_can('manage_options') )
		$msg = sprintf( __('WordPress %2$s is available! <a href="%1$s">Please update now</a>.'), $cur->url, $cur->current );
	else
		$msg = sprintf( __('WordPress %2$s is available! Please notify the site administrator.'), $cur->url, $cur->current );

	echo "<div id='update-nag'>$msg</div>";
}
add_action( 'admin_notices', 'update_nag', 3 );

// Called directly from dashboard
function update_right_now_message() {
	$cur = get_option( 'update_core' );

	$msg = sprintf( __('This is WordPress version %s.'), $GLOBALS['wp_version'] );
	if ( isset( $cur->response ) && $cur->response == 'upgrade' && current_user_can('manage_options') )
		$msg .= " <a href='$cur->url' class='rbutton'>" . sprintf( __('Update to %s'), $cur->current ? $cur->current : __( 'Latest' ) ) . '</a>';

	echo "<span id='wp-version-message'>$msg</span>";
}

function wp_plugin_update_row( $file, $plugin_data ) {
	$current = get_option( 'update_plugins' );
	if ( !isset( $current->response[ $file ] ) )
		return false;

	$r = $current->response[ $file ];

	echo '<tr><td colspan="5" class="plugin-update">';
	if ( ! current_user_can('update_plugins') )
		printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a>.'), $plugin_data['Name'], $r->url, $r->new_version);
	else if ( empty($r->package) )
		printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> <em>automatic upgrade unavailable for this plugin</em>.'), $plugin_data['Name'], $r->url, $r->new_version);
	else
		printf( __('There is a new version of %1$s available. <a href="%2$s">Download version %3$s here</a> or <a href="%4$s">upgrade automatically</a>.'), $plugin_data['Name'], $r->url, $r->new_version, wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . $file, 'upgrade-plugin_' . $file) );
	
	echo '</td></tr>';
}
add_action( 'after_plugin_row', 'wp_plugin_update_row', 10, 2 );

function wp_update_plugin($plugin, $feedback = '') {
	global $wp_filesystem;

	if ( !empty($feedback) )
		add_filter('update_feedback', $feedback);

	// Is an update available?
	$current = get_option( 'update_plugins' );
	if ( !isset( $current->response[ $plugin ] ) )
		return new WP_Error('up_to_date', __('The plugin is at the latest version.'));

	// Is a filesystem accessor setup?
	if ( ! $wp_filesystem || ! is_object($wp_filesystem) )
		WP_Filesystem();

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.'));

	if ( $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error'), $wp_filesystem->errors);

	//Get the base plugin folder
	$plugins_dir = $wp_filesystem->wp_plugins_dir();
	if ( empty($plugins_dir) )
		return new WP_Error('fs_no_plugins_dir', __('Unable to locate WordPress Plugin directory.'));

	//And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress Content directory (wp-content).'));
	
	$plugins_dir = trailingslashit( $plugins_dir );
	$content_dir = trailingslashit( $content_dir );

	// Get the URL to the zip file
	$r = $current->response[ $plugin ];

	if ( empty($r->package) )
		return new WP_Error('no_package', __('Upgrade package not available.'));

	// Download the package
	$package = $r->package;
	apply_filters('update_feedback', sprintf(__('Downloading update from %s'), $package));
	$download_file = download_url($package);

	if ( is_wp_error($download_file) )
		return new WP_Error('download_failed', __('Download failed.'), $download_file->get_error_message());

	$working_dir = $content_dir . 'upgrade/' . basename($plugin, '.php');

	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('update_feedback', __('Unpacking the update'));
	// Unzip package to working directory
	$result = unzip_file($download_file, $working_dir);
	
	// Once extracted, delete the package
	unlink($download_file);
	
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	if ( is_plugin_active($plugin) ) {
		//Deactivate the plugin silently, Prevent deactivation hooks from running.
		apply_filters('update_feedback', __('Deactivating the plugin'));
		deactivate_plugins($plugin, true);
	}

	// Remove the existing plugin.
	apply_filters('update_feedback', __('Removing the old version of the plugin'));
	$this_plugin_dir = trailingslashit( dirname($plugins_dir . $plugin) );
	
	// If plugin is in its own directory, recursively delete the directory.
	if ( strpos($plugin, '/') && $this_plugin_dir != $plugins_dir ) //base check on if plugin includes directory seperator AND that its not the root plugin folder
		$deleted = $wp_filesystem->delete($this_plugin_dir, true);
	else
		$deleted = $wp_filesystem->delete($plugins_dir . $plugin);

	if ( ! $deleted ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('delete_failed', __('Could not remove the old plugin'));
	}

	apply_filters('update_feedback', __('Installing the latest version'));
	// Copy new version of plugin into place.
	$result = copy_dir($working_dir, $plugins_dir);
	if ( is_wp_error($result) ) {
		//$wp_filesystem->delete($working_dir, true); //TODO: Uncomment? This DOES mean that the new files are available in the upgrade folder if it fails.
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the plugin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	// Force refresh of plugin update information
	delete_option('update_plugins');
	
	if( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no plugin installed? Failsafe backup.
	
	$folder = $filelist[0];
	$plugin = get_plugins('/' . $folder); //Ensure to pass with leading slash
	$pluginfiles = array_keys($plugin); //Assume the requested plugin is the first in the list

	return  $folder . '/' . $pluginfiles[0];
}

?>
