<?php

function update_core($from, $to) {
	global $wp_filesystem;

	// Sanity check the unzipped distribution
	apply_filters('update_feedback', __('Verifying the unpacked files'));
	if ( !file_exists($from . '/wordpress/wp-settings.php') || !file_exists($from . '/wordpress/wp-admin/admin.php') ||
		!file_exists($from . '/wordpress/wp-includes/functions.php') ) {
		$wp_filesystem->delete($from, true);
		return new WP_Error('insane_distro', __('The update could not be unpacked') );
	}
	
	apply_filters('update_feedback', __('Installing the latest version'));

	// Create maintenance file to signal that we are upgrading
	$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
	$maintenance_file = $to . '.maintenance';
	$wp_filesystem->delete($maintenance_file);
	$wp_filesystem->put_contents($maintenance_file, $maintenance_string, 0644);

	// Copy new versions of WP files into place.
	$result = copy_dir($from . '/wordpress', $to);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($maintenance_file);
		//$wp_filesystem->delete($working_dir, true); //TODO: Uncomment? This DOES mean that the new files are available in the upgrade folder if it fails.
		return $result;
	}

	// Might have to do upgrade in a separate step.
	apply_filters('update_feedback', __('Upgrading database'));
	// Get new db version
	global $wp_db_version;
	require (ABSPATH . WPINC . '/version.php');
	// Upgrade db
	define('WP_INSTALLING', true);
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	wp_upgrade();

	// Remove working directory
	$wp_filesystem->delete($from, true);

	// Remove maintenance file, we're done.
	$wp_filesystem->delete($maintenance_file);

	// Force refresh of update information
	delete_option('update_core');
}

?>