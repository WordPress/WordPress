<?php
/**
 * Upgrade API: WP_Upgrader, Plugin_Upgrader, Theme_Upgrader, Language_Pack_Upgrader,
 * Core_Upgrader, File_Upload_Upgrader, and WP_Automatic_Updater classes
 *
 * This set of classes are designed to be used to upgrade/install a local set of files
 * on the filesystem via the Filesystem Abstraction classes.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */

/**
 * Core class used for upgrading/installing themes.
 *
 * It is designed to upgrade/install themes from a local zip, remote zip URL,
 * or uploaded zip file.
 *
 * @since 2.8.0
 *
 * @see WP_Upgrader
 */
class Theme_Upgrader extends WP_Upgrader {

	/**
	 * Result of the theme upgrade offer.
	 *
	 * @since 2.8.0
	 * @access public
	 * @var array|WP_Error $result
	 * @see WP_Upgrader::$result
	 */
	public $result;

	/**
	 * Whether multiple themes are being upgraded/installed in bulk.
	 *
	 * @since 2.9.0
	 * @access public
	 * @var bool $bulk
	 */
	public $bulk = false;

	/**
	 * Initialize the upgrade strings.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function upgrade_strings() {
		$this->strings['up_to_date'] = __('The theme is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['remove_old'] = __('Removing the old version of the theme&#8230;');
		$this->strings['remove_old_failed'] = __('Could not remove the old theme.');
		$this->strings['process_failed'] = __('Theme update failed.');
		$this->strings['process_success'] = __('Theme updated successfully.');
	}

	/**
	 * Initialize the install strings.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function install_strings() {
		$this->strings['no_package'] = __('Install package not available.');
		$this->strings['downloading_package'] = __('Downloading install package from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the package&#8230;');
		$this->strings['installing_package'] = __('Installing the theme&#8230;');
		$this->strings['no_files'] = __('The theme contains no files.');
		$this->strings['process_failed'] = __('Theme install failed.');
		$this->strings['process_success'] = __('Theme installed successfully.');
		/* translators: 1: theme name, 2: version */
		$this->strings['process_success_specific'] = __('Successfully installed the theme <strong>%1$s %2$s</strong>.');
		$this->strings['parent_theme_search'] = __('This theme requires a parent theme. Checking if it is installed&#8230;');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_prepare_install'] = __('Preparing to install <strong>%1$s %2$s</strong>&#8230;');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_currently_installed'] = __('The parent theme, <strong>%1$s %2$s</strong>, is currently installed.');
		/* translators: 1: theme name, 2: version */
		$this->strings['parent_theme_install_success'] = __('Successfully installed the parent theme, <strong>%1$s %2$s</strong>.');
		$this->strings['parent_theme_not_found'] = __('<strong>The parent theme could not be found.</strong> You will need to install the parent theme, <strong>%s</strong>, before you can use this child theme.');
	}

	/**
	 * Check if a child theme is being installed and we need to install its parent.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Theme_Upgrader::install().
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param bool  $install_result
	 * @param array $hook_extra
	 * @param array $child_result
	 * @return type
	 */
	public function check_parent_theme_filter( $install_result, $hook_extra, $child_result ) {
		// Check to see if we need to install a parent theme
		$theme_info = $this->theme_info();

		if ( ! $theme_info->parent() )
			return $install_result;

		$this->skin->feedback( 'parent_theme_search' );

		if ( ! $theme_info->parent()->errors() ) {
			$this->skin->feedback( 'parent_theme_currently_installed', $theme_info->parent()->display('Name'), $theme_info->parent()->display('Version') );
			// We already have the theme, fall through.
			return $install_result;
		}

		// We don't have the parent theme, let's install it.
		$api = themes_api('theme_information', array('slug' => $theme_info->get('Template'), 'fields' => array('sections' => false, 'tags' => false) ) ); //Save on a bit of bandwidth.

		if ( ! $api || is_wp_error($api) ) {
			$this->skin->feedback( 'parent_theme_not_found', $theme_info->get('Template') );
			// Don't show activate or preview actions after install
			add_filter('install_theme_complete_actions', array($this, 'hide_activate_preview_actions') );
			return $install_result;
		}

		// Backup required data we're going to override:
		$child_api = $this->skin->api;
		$child_success_message = $this->strings['process_success'];

		// Override them
		$this->skin->api = $api;
		$this->strings['process_success_specific'] = $this->strings['parent_theme_install_success'];//, $api->name, $api->version);

		$this->skin->feedback('parent_theme_prepare_install', $api->name, $api->version);

		add_filter('install_theme_complete_actions', '__return_false', 999); // Don't show any actions after installing the theme.

		// Install the parent theme
		$parent_result = $this->run( array(
			'package' => $api->download_link,
			'destination' => get_theme_root(),
			'clear_destination' => false, //Do not overwrite files.
			'clear_working' => true
		) );

		if ( is_wp_error($parent_result) )
			add_filter('install_theme_complete_actions', array($this, 'hide_activate_preview_actions') );

		// Start cleaning up after the parents installation
		remove_filter('install_theme_complete_actions', '__return_false', 999);

		// Reset child's result and data
		$this->result = $child_result;
		$this->skin->api = $child_api;
		$this->strings['process_success'] = $child_success_message;

		return $install_result;
	}

	/**
	 * Don't display the activate and preview actions to the user.
	 *
	 * Hooked to the {@see 'install_theme_complete_actions'} filter by
	 * Theme_Upgrader::check_parent_theme_filter() when installing
	 * a child theme and installing the parent theme fails.
	 *
	 * @since 3.4.0
	 * @access public
	 *
	 * @param array $actions Preview actions.
	 * @return array
	 */
	public function hide_activate_preview_actions( $actions ) {
		unset($actions['activate'], $actions['preview']);
		return $actions;
	}

	/**
	 * Install a theme package.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the update cache optional.
	 * @access public
	 *
	 * @param string $package The full local path or URI of the package.
	 * @param array  $args {
	 *     Optional. Other arguments for installing a theme package. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the updates cache if successful.
	 *                                    Default true.
	 * }
	 *
	 * @return bool|WP_Error True if the install was successful, false or a WP_Error object otherwise.
	 */
	public function install( $package, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->install_strings();

		add_filter('upgrader_source_selection', array($this, 'check_package') );
		add_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'), 10, 3);
		// Clear cache so wp_update_themes() knows about the new theme.
		add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );

		$this->run( array(
			'package' => $package,
			'destination' => get_theme_root(),
			'clear_destination' => false, //Do not overwrite files.
			'clear_working' => true,
			'hook_extra' => array(
				'type' => 'theme',
				'action' => 'install',
			),
		) );

		remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
		remove_filter('upgrader_source_selection', array($this, 'check_package') );
		remove_filter('upgrader_post_install', array($this, 'check_parent_theme_filter'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		// Refresh the Theme Update information
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	/**
	 * Upgrade a theme.
	 *
	 * @since 2.8.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the update cache optional.
	 * @access public
	 *
	 * @param string $theme The theme slug.
	 * @param array  $args {
	 *     Optional. Other arguments for upgrading a theme. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the update cache if successful.
	 *                                    Default true.
	 * }
	 * @return bool|WP_Error True if the upgrade was successful, false or a WP_Error object otherwise.
	 */
	public function upgrade( $theme, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->upgrade_strings();

		// Is an update available?
		$current = get_site_transient( 'update_themes' );
		if ( !isset( $current->response[ $theme ] ) ) {
			$this->skin->before();
			$this->skin->set_result(false);
			$this->skin->error( 'up_to_date' );
			$this->skin->after();
			return false;
		}

		$r = $current->response[ $theme ];

		add_filter('upgrader_pre_install', array($this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array($this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array($this, 'delete_old_theme'), 10, 4);
		// Clear cache so wp_update_themes() knows about the new theme.
		add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );

		$this->run( array(
			'package' => $r['package'],
			'destination' => get_theme_root( $theme ),
			'clear_destination' => true,
			'clear_working' => true,
			'hook_extra' => array(
				'theme' => $theme,
				'type' => 'theme',
				'action' => 'update',
			),
		) );

		remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
		remove_filter('upgrader_pre_install', array($this, 'current_before'));
		remove_filter('upgrader_post_install', array($this, 'current_after'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_theme'));

		if ( ! $this->result || is_wp_error($this->result) )
			return $this->result;

		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		return true;
	}

	/**
	 * Upgrade several themes at once.
	 *
	 * @since 3.0.0
	 * @since 3.7.0 The `$args` parameter was added, making clearing the update cache optional.
	 * @access public
	 *
	 * @param array $themes The theme slugs.
	 * @param array $args {
	 *     Optional. Other arguments for upgrading several themes at once. Default empty array.
	 *
	 *     @type bool $clear_update_cache Whether to clear the update cache if successful.
	 *                                    Default true.
	 * }
	 * @return array[]|false An array of results, or false if unable to connect to the filesystem.
	 */
	public function bulk_upgrade( $themes, $args = array() ) {

		$defaults = array(
			'clear_update_cache' => true,
		);
		$parsed_args = wp_parse_args( $args, $defaults );

		$this->init();
		$this->bulk = true;
		$this->upgrade_strings();

		$current = get_site_transient( 'update_themes' );

		add_filter('upgrader_pre_install', array($this, 'current_before'), 10, 2);
		add_filter('upgrader_post_install', array($this, 'current_after'), 10, 2);
		add_filter('upgrader_clear_destination', array($this, 'delete_old_theme'), 10, 4);
		// Clear cache so wp_update_themes() knows about the new theme.
		add_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9, 0 );

		$this->skin->header();

		// Connect to the Filesystem first.
		$res = $this->fs_connect( array(WP_CONTENT_DIR) );
		if ( ! $res ) {
			$this->skin->footer();
			return false;
		}

		$this->skin->bulk_header();

		// Only start maintenance mode if:
		// - running Multisite and there are one or more themes specified, OR
		// - a theme with an update available is currently in use.
		// @TODO: For multisite, maintenance mode should only kick in for individual sites if at all possible.
		$maintenance = ( is_multisite() && ! empty( $themes ) );
		foreach ( $themes as $theme )
			$maintenance = $maintenance || $theme == get_stylesheet() || $theme == get_template();
		if ( $maintenance )
			$this->maintenance_mode(true);

		$results = array();

		$this->update_count = count($themes);
		$this->update_current = 0;
		foreach ( $themes as $theme ) {
			$this->update_current++;

			$this->skin->theme_info = $this->theme_info($theme);

			if ( !isset( $current->response[ $theme ] ) ) {
				$this->skin->set_result(true);
				$this->skin->before();
				$this->skin->feedback( 'up_to_date' );
				$this->skin->after();
				$results[$theme] = true;
				continue;
			}

			// Get the URL to the zip file
			$r = $current->response[ $theme ];

			$result = $this->run( array(
				'package' => $r['package'],
				'destination' => get_theme_root( $theme ),
				'clear_destination' => true,
				'clear_working' => true,
				'is_multi' => true,
				'hook_extra' => array(
					'theme' => $theme
				),
			) );

			$results[$theme] = $this->result;

			// Prevent credentials auth screen from displaying multiple times
			if ( false === $result )
				break;
		} //end foreach $plugins

		$this->maintenance_mode(false);

		/** This action is documented in wp-admin/includes/class-wp-upgrader.php */
		do_action( 'upgrader_process_complete', $this, array(
			'action' => 'update',
			'type' => 'theme',
			'bulk' => true,
			'themes' => $themes,
		) );

		$this->skin->bulk_footer();

		$this->skin->footer();

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_action( 'upgrader_process_complete', 'wp_clean_themes_cache', 9 );
		remove_filter('upgrader_pre_install', array($this, 'current_before'));
		remove_filter('upgrader_post_install', array($this, 'current_after'));
		remove_filter('upgrader_clear_destination', array($this, 'delete_old_theme'));

		// Refresh the Theme Update information
		wp_clean_themes_cache( $parsed_args['clear_update_cache'] );

		return $results;
	}

	/**
	 * Check that the package source contains a valid theme.
	 *
	 * Hooked to the {@see 'upgrader_source_selection'} filter by Theme_Upgrader::install().
	 * It will return an error if the theme doesn't have style.css or index.php
	 * files.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 *
	 * @param string $source The full path to the package source.
	 * @return string|WP_Error The source or a WP_Error.
	 */
	public function check_package( $source ) {
		global $wp_filesystem;

		if ( is_wp_error($source) )
			return $source;

		// Check the folder contains a valid theme
		$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit(WP_CONTENT_DIR), $source);
		if ( ! is_dir($working_directory) ) // Sanity check, if the above fails, let's not prevent installation.
			return $source;

		// A proper archive should have a style.css file in the single subdirectory
		if ( ! file_exists( $working_directory . 'style.css' ) ) {
			return new WP_Error( 'incompatible_archive_theme_no_style', $this->strings['incompatible_archive'],
				/* translators: %s: style.css */
				sprintf( __( 'The theme is missing the %s stylesheet.' ),
					'<code>style.css</code>'
				)
			);
		}

		$info = get_file_data( $working_directory . 'style.css', array( 'Name' => 'Theme Name', 'Template' => 'Template' ) );

		if ( empty( $info['Name'] ) ) {
			return new WP_Error( 'incompatible_archive_theme_no_name', $this->strings['incompatible_archive'],
				/* translators: %s: style.css */
				sprintf( __( 'The %s stylesheet doesn&#8217;t contain a valid theme header.' ),
					'<code>style.css</code>'
				)
			);
		}

		// If it's not a child theme, it must have at least an index.php to be legit.
		if ( empty( $info['Template'] ) && ! file_exists( $working_directory . 'index.php' ) ) {
			return new WP_Error( 'incompatible_archive_theme_no_index', $this->strings['incompatible_archive'],
				/* translators: %s: index.php */
				sprintf( __( 'The theme is missing the %s file.' ),
					'<code>index.php</code>'
				)
			);
		}

		return $source;
	}

	/**
	 * Turn on maintenance mode before attempting to upgrade the current theme.
	 *
	 * Hooked to the {@see 'upgrader_pre_install'} filter by Theme_Upgrader::upgrade() and
	 * Theme_Upgrader::bulk_upgrade().
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param bool|WP_Error  $return
	 * @param array          $theme
	 * @return bool|WP_Error
	 */
	public function current_before($return, $theme) {
		if ( is_wp_error($return) )
			return $return;

		$theme = isset($theme['theme']) ? $theme['theme'] : '';

		if ( $theme != get_stylesheet() ) //If not current
			return $return;
		//Change to maintenance mode now.
		if ( ! $this->bulk )
			$this->maintenance_mode(true);

		return $return;
	}

	/**
	 * Turn off maintenance mode after upgrading the current theme.
	 *
	 * Hooked to the {@see 'upgrader_post_install'} filter by Theme_Upgrader::upgrade()
	 * and Theme_Upgrader::bulk_upgrade().
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param bool|WP_Error  $return
	 * @param array          $theme
	 * @return bool|WP_Error
	 */
	public function current_after($return, $theme) {
		if ( is_wp_error($return) )
			return $return;

		$theme = isset($theme['theme']) ? $theme['theme'] : '';

		if ( $theme != get_stylesheet() ) // If not current
			return $return;

		// Ensure stylesheet name hasn't changed after the upgrade:
		if ( $theme == get_stylesheet() && $theme != $this->result['destination_name'] ) {
			wp_clean_themes_cache();
			$stylesheet = $this->result['destination_name'];
			switch_theme( $stylesheet );
		}

		//Time to remove maintenance mode
		if ( ! $this->bulk )
			$this->maintenance_mode(false);
		return $return;
	}

	/**
	 * Delete the old theme during an upgrade.
	 *
	 * Hooked to the {@see 'upgrader_clear_destination'} filter by Theme_Upgrader::upgrade()
	 * and Theme_Upgrader::bulk_upgrade().
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @global WP_Filesystem_Base $wp_filesystem Subclass
	 *
	 * @param bool   $removed
	 * @param string $local_destination
	 * @param string $remote_destination
	 * @param array  $theme
	 * @return bool
	 */
	public function delete_old_theme( $removed, $local_destination, $remote_destination, $theme ) {
		global $wp_filesystem;

		if ( is_wp_error( $removed ) )
			return $removed; // Pass errors through.

		if ( ! isset( $theme['theme'] ) )
			return $removed;

		$theme = $theme['theme'];
		$themes_dir = trailingslashit( $wp_filesystem->wp_themes_dir( $theme ) );
		if ( $wp_filesystem->exists( $themes_dir . $theme ) ) {
			if ( ! $wp_filesystem->delete( $themes_dir . $theme, true ) )
				return false;
		}

		return true;
	}

	/**
	 * Get the WP_Theme object for a theme.
	 *
	 * @since 2.8.0
	 * @since 3.0.0 The `$theme` argument was added.
	 * @access public
	 *
	 * @param string $theme The directory name of the theme. This is optional, and if not supplied,
	 *                      the directory name from the last result will be used.
	 * @return WP_Theme|false The theme's info object, or false `$theme` is not supplied
	 *                        and the last result isn't set.
	 */
	public function theme_info($theme = null) {

		if ( empty($theme) ) {
			if ( !empty($this->result['destination_name']) )
				$theme = $this->result['destination_name'];
			else
				return false;
		}
		return wp_get_theme( $theme );
	}

}
