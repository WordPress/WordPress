<?php
/**
 * Update Plugin / Core administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('update_plugins') )
	wp_die(__('You do not have sufficient permissions to update plugins for this blog.'));

/**
 * Plugin upgrade display.
 *
 * @since 2.5
 *
 * @param string $plugin Plugin
 */
function do_plugin_upgrade($plugin) {
	global $wp_filesystem;

	$url = wp_nonce_url("update.php?action=upgrade-plugin&plugin=$plugin", "upgrade-plugin_$plugin");
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade Plugin') . '</h2>';
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	$was_activated = is_plugin_active($plugin); //Check now, It'll be deactivated by the next line if it is

	$result = wp_update_plugin($plugin, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed') );
	} else {
		//Result is the new plugin file relative to WP_PLUGIN_DIR
		show_message( __('Plugin upgraded successfully') );
		if( $result && $was_activated ){
			show_message(__('Attempting reactivation of the plugin'));
			echo '<iframe style="border:0" width="100%" height="170px" src="' . wp_nonce_url('update.php?action=activate-plugin&plugin=' . $result, 'activate-plugin_' . $result) .'"></iframe>';
		}
	}
	echo '</div>';
}

/**
 * Display upgrade WordPress for downloading latest or upgrading automatically form.
 *
 * @since 2.7
 *
 * @return null
 */
function core_upgrade_preamble() {
	$update = get_option('update_core');

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade WordPress') . '</h2>';

	if ( !isset($update->response) || 'latest' == $update->response ) {
		_e('You have the latest version of WordPress. You do not need to upgrade.');
		echo '</div>';
		return;
	}

	if ( 'development' == $update->response ) {
		$message = __('You are using a development version of WordPress.  You can upgrade to the latest nightly build automatically or download the nightly build and install it manually. Which would you like to do?');
		$submit = __('Download nightly build');
	} else {
		$message = 	sprintf(__('You can upgrade to version %s automatically or download the nightly build and install it manually. Which would you like to do?'), $update->current);
		$submit = sprintf(__('Download %s'), $update->current);
	}

	echo '<p>';
	echo $message;
	echo '</p>';
	echo '<form id="post" method="post" action="update.php?action=do-core-upgrade" name="upgrade">';
	wp_nonce_field('upgrade-core');
	echo '<p>';
	echo '<input id="upgrade" class="button" type="submit" value="' . __('Upgrade Automatically') . '" name="upgrade" />';
	echo '<a href="' . $update->package . '" class="button">' . $submit . '</a>';
	echo '</p>';
	echo '</form>';

	echo '</div>';
}

/**
 * Upgrade WordPress core display.
 *
 * @since 2.7
 *
 * @return null
 */
function do_core_upgrade() {
	global $wp_filesystem;

	$url = wp_nonce_url("update.php?action=upgrade-core", "upgrade-core");
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade WordPress') . '</h2>';
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	$result = wp_update_core('show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		if ('up_to_date' != $result->get_error_code() )
			show_message( __('Installation Failed') );
	} else {
		show_message( __('WordPress upgraded successfully') );
	}
	echo '</div>';
}

if ( isset($_GET['action']) ) {
	$plugin = isset($_GET['plugin']) ? trim($_GET['plugin']) : '';

	if ( 'upgrade-plugin' == $_GET['action'] ) {
		check_admin_referer('upgrade-plugin_' . $plugin);
		$title = __('Upgrade Plugin');
		$parent_file = 'plugins.php';
		require_once('admin-header.php');
		do_plugin_upgrade($plugin);
		include('admin-footer.php');
	} elseif ('activate-plugin' == $_GET['action'] ) {
		check_admin_referer('activate-plugin_' . $plugin);
		if( ! isset($_GET['failure']) && ! isset($_GET['success']) ) {
			wp_redirect( 'update.php?action=activate-plugin&failure=true&plugin=' . $plugin . '&_wpnonce=' . $_GET['_wpnonce'] );
			activate_plugin($plugin);
			wp_redirect( 'update.php?action=activate-plugin&success=true&plugin=' . $plugin . '&_wpnonce=' . $_GET['_wpnonce'] );
			die();
		}
			?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Plugin Reactivation'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_admin_css( 'global', true );
wp_admin_css( 'colors', true );
?>
</head>
<body>
<?php
		if( isset($_GET['success']) )
			echo '<p>' . __('Plugin reactivated successfully.') . '</p>';

		if( isset($_GET['failure']) ){
			echo '<p>' . __('Plugin failed to reactivate due to a fatal error.') . '</p>';
			error_reporting( E_ALL ^ E_NOTICE );
			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			include(WP_PLUGIN_DIR . '/' . $plugin);
		}
		echo "</body></html>";
	} elseif ( 'upgrade-core' == $_GET['action'] ) {
		$title = __('Upgrade WordPress');
		$parent_file = 'index.php';
		require_once('admin-header.php');
		core_upgrade_preamble();
		include('admin-footer.php');
	} elseif ( 'do-core-upgrade' ) {
		check_admin_referer('upgrade-core');
		$title = __('Upgrade WordPress');
		$parent_file = 'index.php';
		require_once('admin-header.php');
		do_core_upgrade();
		include('admin-footer.php');
	}
}

?>
