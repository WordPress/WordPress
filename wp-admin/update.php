<?php
/**
 * Update/Install Plugin/Theme administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

if ( ! defined( 'IFRAME_REQUEST' ) && isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ) ) )
	define( 'IFRAME_REQUEST', true );

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

if ( isset($_GET['action']) ) {
	$plugin = isset($_REQUEST['plugin']) ? trim($_REQUEST['plugin']) : '';
	$theme = isset($_REQUEST['theme']) ? urldecode($_REQUEST['theme']) : '';
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	if ( 'update-selected' == $action ) {
		if ( ! current_user_can( 'update_plugins' ) )
			wp_die( __( 'Sorry, you are not allowed to update plugins for this site.' ) );

		check_admin_referer( 'bulk-update-plugins' );

		if ( isset( $_GET['plugins'] ) )
			$plugins = explode( ',', stripslashes($_GET['plugins']) );
		elseif ( isset( $_POST['checked'] ) )
			$plugins = (array) $_POST['checked'];
		else
			$plugins = array();

		$plugins = array_map('urldecode', $plugins);

		$url = 'update.php?action=update-selected&amp;plugins=' . urlencode(implode(',', $plugins));
		$nonce = 'bulk-update-plugins';

		wp_enqueue_script( 'updates' );
		iframe_header();

		$upgrader = new Plugin_Upgrader( new Bulk_Plugin_Upgrader_Skin( compact( 'nonce', 'url' ) ) );
		$upgrader->bulk_upgrade( $plugins );

		iframe_footer();

	} elseif ( 'upgrade-plugin' == $action ) {
		if ( ! current_user_can('update_plugins') )
			wp_die(__('Sorry, you are not allowed to update plugins for this site.'));

		check_admin_referer('upgrade-plugin_' . $plugin);

		$title = __('Update Plugin');
		$parent_file = 'plugins.php';
		$submenu_file = 'plugins.php';

		wp_enqueue_script( 'updates' );
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$nonce = 'upgrade-plugin_' . $plugin;
		$url = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin );

		$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact('title', 'nonce', 'url', 'plugin') ) );
		$upgrader->upgrade($plugin);

		include(ABSPATH . 'wp-admin/admin-footer.php');

	} elseif ('activate-plugin' == $action ) {
		if ( ! current_user_can('update_plugins') )
			wp_die(__('Sorry, you are not allowed to update plugins for this site.'));

		check_admin_referer('activate-plugin_' . $plugin);
		if ( ! isset($_GET['failure']) && ! isset($_GET['success']) ) {
			wp_redirect( admin_url('update.php?action=activate-plugin&failure=true&plugin=' . urlencode( $plugin ) . '&_wpnonce=' . $_GET['_wpnonce']) );
			activate_plugin( $plugin, '', ! empty( $_GET['networkwide'] ), true );
			wp_redirect( admin_url('update.php?action=activate-plugin&success=true&plugin=' . urlencode( $plugin ) . '&_wpnonce=' . $_GET['_wpnonce']) );
			die();
		}
		iframe_header( __('Plugin Reactivation'), true );
		if ( isset($_GET['success']) )
			echo '<p>' . __('Plugin reactivated successfully.') . '</p>';

		if ( isset($_GET['failure']) ){
			echo '<p>' . __('Plugin failed to reactivate due to a fatal error.') . '</p>';

			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			wp_register_plugin_realpath( WP_PLUGIN_DIR . '/' . $plugin );
			include( WP_PLUGIN_DIR . '/' . $plugin );
		}
		iframe_footer();
	} elseif ( 'install-plugin' == $action ) {

		if ( ! current_user_can('install_plugins') )
			wp_die( __( 'Sorry, you are not allowed to install plugins on this site.' ) );

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); //for plugins_api..

		check_admin_referer( 'install-plugin_' . $plugin );
		$api = plugins_api( 'plugin_information', array(
			'slug' => $plugin,
			'fields' => array(
				'short_description' => false,
				'sections' => false,
				'requires' => false,
				'rating' => false,
				'ratings' => false,
				'downloaded' => false,
				'last_updated' => false,
				'added' => false,
				'tags' => false,
				'compatibility' => false,
				'homepage' => false,
				'donate_link' => false,
			),
		) );

		if ( is_wp_error( $api ) ) {
	 		wp_die( $api );
		}

		$title = __('Plugin Install');
		$parent_file = 'plugins.php';
		$submenu_file = 'plugin-install.php';
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$title = sprintf( __('Installing Plugin: %s'), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );
		if ( isset($_GET['from']) )
			$url .= '&from=' . urlencode(stripslashes($_GET['from']));

		$type = 'web'; //Install plugin type, From Web or an Upload.

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
		$upgrader->install($api->download_link);

		include(ABSPATH . 'wp-admin/admin-footer.php');

	} elseif ( 'upload-plugin' == $action ) {

		if ( ! current_user_can( 'upload_plugins' ) ) {
			wp_die( __( 'Sorry, you are not allowed to install plugins on this site.' ) );
		}

		check_admin_referer('plugin-upload');

		$file_upload = new File_Upload_Upgrader('pluginzip', 'package');

		$title = __('Upload Plugin');
		$parent_file = 'plugins.php';
		$submenu_file = 'plugin-install.php';
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$title = sprintf( __('Installing Plugin from uploaded file: %s'), esc_html( basename( $file_upload->filename ) ) );
		$nonce = 'plugin-upload';
		$url = add_query_arg(array('package' => $file_upload->id), 'update.php?action=upload-plugin');
		$type = 'upload'; //Install plugin type, From Web or an Upload.

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('type', 'title', 'nonce', 'url') ) );
		$result = $upgrader->install( $file_upload->package );

		if ( $result || is_wp_error($result) )
			$file_upload->cleanup();

		include(ABSPATH . 'wp-admin/admin-footer.php');

	} elseif ( 'upgrade-theme' == $action ) {

		if ( ! current_user_can('update_themes') )
			wp_die(__('Sorry, you are not allowed to update themes for this site.'));

		check_admin_referer('upgrade-theme_' . $theme);

		wp_enqueue_script( 'customize-loader' );
		wp_enqueue_script( 'updates' );

		$title = __('Update Theme');
		$parent_file = 'themes.php';
		$submenu_file = 'themes.php';
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$nonce = 'upgrade-theme_' . $theme;
		$url = 'update.php?action=upgrade-theme&theme=' . urlencode( $theme );

		$upgrader = new Theme_Upgrader( new Theme_Upgrader_Skin( compact('title', 'nonce', 'url', 'theme') ) );
		$upgrader->upgrade($theme);

		include(ABSPATH . 'wp-admin/admin-footer.php');
	} elseif ( 'update-selected-themes' == $action ) {
		if ( ! current_user_can( 'update_themes' ) )
			wp_die( __( 'Sorry, you are not allowed to update themes for this site.' ) );

		check_admin_referer( 'bulk-update-themes' );

		if ( isset( $_GET['themes'] ) )
			$themes = explode( ',', stripslashes($_GET['themes']) );
		elseif ( isset( $_POST['checked'] ) )
			$themes = (array) $_POST['checked'];
		else
			$themes = array();

		$themes = array_map('urldecode', $themes);

		$url = 'update.php?action=update-selected-themes&amp;themes=' . urlencode(implode(',', $themes));
		$nonce = 'bulk-update-themes';

		wp_enqueue_script( 'updates' );
		iframe_header();

		$upgrader = new Theme_Upgrader( new Bulk_Theme_Upgrader_Skin( compact( 'nonce', 'url' ) ) );
		$upgrader->bulk_upgrade( $themes );

		iframe_footer();
	} elseif ( 'install-theme' == $action ) {

		if ( ! current_user_can('install_themes') )
			wp_die( __( 'Sorry, you are not allowed to install themes on this site.' ) );

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ); //for themes_api..

		check_admin_referer( 'install-theme_' . $theme );
		$api = themes_api('theme_information', array('slug' => $theme, 'fields' => array('sections' => false, 'tags' => false) ) ); //Save on a bit of bandwidth.

		if ( is_wp_error($api) )
	 		wp_die($api);

		wp_enqueue_script( 'customize-loader' );

		$title = __('Install Themes');
		$parent_file = 'themes.php';
		$submenu_file = 'themes.php';
		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$title = sprintf( __('Installing Theme: %s'), $api->name . ' ' . $api->version );
		$nonce = 'install-theme_' . $theme;
		$url = 'update.php?action=install-theme&theme=' . urlencode( $theme );
		$type = 'web'; //Install theme type, From Web or an Upload.

		$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
		$upgrader->install($api->download_link);

		include(ABSPATH . 'wp-admin/admin-footer.php');

	} elseif ( 'upload-theme' == $action ) {

		if ( ! current_user_can( 'upload_themes' ) ) {
			wp_die( __( 'Sorry, you are not allowed to install themes on this site.' ) );
		}

		check_admin_referer('theme-upload');

		$file_upload = new File_Upload_Upgrader('themezip', 'package');

		wp_enqueue_script( 'customize-loader' );

		$title = __('Upload Theme');
		$parent_file = 'themes.php';
		$submenu_file = 'theme-install.php';

		require_once(ABSPATH . 'wp-admin/admin-header.php');

		$title = sprintf( __('Installing Theme from uploaded file: %s'), esc_html( basename( $file_upload->filename ) ) );
		$nonce = 'theme-upload';
		$url = add_query_arg(array('package' => $file_upload->id), 'update.php?action=upload-theme');
		$type = 'upload'; //Install plugin type, From Web or an Upload.

		$upgrader = new Theme_Upgrader( new Theme_Installer_Skin( compact('type', 'title', 'nonce', 'url') ) );
		$result = $upgrader->install( $file_upload->package );

		if ( $result || is_wp_error($result) )
			$file_upload->cleanup();

		include(ABSPATH . 'wp-admin/admin-footer.php');

	} else {
		/**
		 * Fires when a custom plugin or theme update request is received.
		 *
		 * The dynamic portion of the hook name, `$action`, refers to the action
		 * provided in the request for wp-admin/update.php. Can be used to
		 * provide custom update functionality for themes and plugins.
		 *
		 * @since 2.8.0
		 */
		do_action( "update-custom_{$action}" );
	}
}
