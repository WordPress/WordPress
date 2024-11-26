<?php
/**
 * Upgrade API: WP_Automatic_Updater class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Core class used for handling automatic background updates.
 *
 * @since 3.7.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader.php.
 */
#[AllowDynamicProperties]
class WP_Automatic_Updater {

	/**
	 * Tracks update results during processing.
	 *
	 * @var array
	 */
	protected $update_results = array();

	/**
	 * Determines whether the entire automatic updater is disabled.
	 *
	 * @since 3.7.0
	 *
	 * @return bool True if the automatic updater is disabled, false otherwise.
	 */
	public function is_disabled() {
		// Background updates are disabled if you don't want file changes.
		if ( ! wp_is_file_mod_allowed( 'automatic_updater' ) ) {
			return true;
		}

		if ( wp_installing() ) {
			return true;
		}

		// More fine grained control can be done through the WP_AUTO_UPDATE_CORE constant and filters.
		$disabled = defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED;

		/**
		 * Filters whether to entirely disable background updates.
		 *
		 * There are more fine-grained filters and controls for selective disabling.
		 * This filter parallels the AUTOMATIC_UPDATER_DISABLED constant in name.
		 *
		 * This also disables update notification emails. That may change in the future.
		 *
		 * @since 3.7.0
		 *
		 * @param bool $disabled Whether the updater should be disabled.
		 */
		return apply_filters( 'automatic_updater_disabled', $disabled );
	}

	/**
	 * Checks whether access to a given directory is allowed.
	 *
	 * This is used when detecting version control checkouts. Takes into account
	 * the PHP `open_basedir` restrictions, so that WordPress does not try to access
	 * directories it is not allowed to.
	 *
	 * @since 6.2.0
	 *
	 * @param string $dir The directory to check.
	 * @return bool True if access to the directory is allowed, false otherwise.
	 */
	public function is_allowed_dir( $dir ) {
		if ( is_string( $dir ) ) {
			$dir = trim( $dir );
		}

		if ( ! is_string( $dir ) || '' === $dir ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: The "$dir" argument. */
					__( 'The "%s" argument must be a non-empty string.' ),
					'$dir'
				),
				'6.2.0'
			);

			return false;
		}

		$open_basedir = ini_get( 'open_basedir' );

		if ( empty( $open_basedir ) ) {
			return true;
		}

		$open_basedir_list = explode( PATH_SEPARATOR, $open_basedir );

		foreach ( $open_basedir_list as $basedir ) {
			if ( '' !== trim( $basedir ) && str_starts_with( $dir, $basedir ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks for version control checkouts.
	 *
	 * Checks for Subversion, Git, Mercurial, and Bazaar. It recursively looks up the
	 * filesystem to the top of the drive, erring on the side of detecting a VCS
	 * checkout somewhere.
	 *
	 * ABSPATH is always checked in addition to whatever `$context` is (which may be the
	 * wp-content directory, for example). The underlying assumption is that if you are
	 * using version control *anywhere*, then you should be making decisions for
	 * how things get updated.
	 *
	 * @since 3.7.0
	 *
	 * @param string $context The filesystem path to check, in addition to ABSPATH.
	 * @return bool True if a VCS checkout was discovered at `$context` or ABSPATH,
	 *              or anywhere higher. False otherwise.
	 */
	public function is_vcs_checkout( $context ) {
		$context_dirs = array( untrailingslashit( $context ) );
		if ( ABSPATH !== $context ) {
			$context_dirs[] = untrailingslashit( ABSPATH );
		}

		$vcs_dirs   = array( '.svn', '.git', '.hg', '.bzr' );
		$check_dirs = array();

		foreach ( $context_dirs as $context_dir ) {
			// Walk up from $context_dir to the root.
			do {
				$check_dirs[] = $context_dir;

				// Once we've hit '/' or 'C:\', we need to stop. dirname will keep returning the input here.
				if ( dirname( $context_dir ) === $context_dir ) {
					break;
				}

				// Continue one level at a time.
			} while ( $context_dir = dirname( $context_dir ) );
		}

		$check_dirs = array_unique( $check_dirs );
		$checkout   = false;

		// Search all directories we've found for evidence of version control.
		foreach ( $vcs_dirs as $vcs_dir ) {
			foreach ( $check_dirs as $check_dir ) {
				if ( ! $this->is_allowed_dir( $check_dir ) ) {
					continue;
				}

				$checkout = is_dir( rtrim( $check_dir, '\\/' ) . "/$vcs_dir" );
				if ( $checkout ) {
					break 2;
				}
			}
		}

		/**
		 * Filters whether the automatic updater should consider a filesystem
		 * location to be potentially managed by a version control system.
		 *
		 * @since 3.7.0
		 *
		 * @param bool $checkout  Whether a VCS checkout was discovered at `$context`
		 *                        or ABSPATH, or anywhere higher.
		 * @param string $context The filesystem context (a path) against which
		 *                        filesystem status should be checked.
		 */
		return apply_filters( 'automatic_updates_is_vcs_checkout', $checkout, $context );
	}

	/**
	 * Tests to see if we can and should update a specific item.
	 *
	 * @since 3.7.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param string $type    The type of update being checked: 'core', 'theme',
	 *                        'plugin', 'translation'.
	 * @param object $item    The update offer.
	 * @param string $context The filesystem context (a path) against which filesystem
	 *                        access and status should be checked.
	 * @return bool True if the item should be updated, false otherwise.
	 */
	public function should_update( $type, $item, $context ) {
		// Used to see if WP_Filesystem is set up to allow unattended updates.
		$skin = new Automatic_Upgrader_Skin();

		if ( $this->is_disabled() ) {
			return false;
		}

		// Only relax the filesystem checks when the update doesn't include new files.
		$allow_relaxed_file_ownership = false;
		if ( 'core' === $type && isset( $item->new_files ) && ! $item->new_files ) {
			$allow_relaxed_file_ownership = true;
		}

		// If we can't do an auto core update, we may still be able to email the user.
		if ( ! $skin->request_filesystem_credentials( false, $context, $allow_relaxed_file_ownership )
			|| $this->is_vcs_checkout( $context )
		) {
			if ( 'core' === $type ) {
				$this->send_core_update_notification_email( $item );
			}
			return false;
		}

		// Next up, is this an item we can update?
		if ( 'core' === $type ) {
			$update = Core_Upgrader::should_update_to_version( $item->current );
		} elseif ( 'plugin' === $type || 'theme' === $type ) {
			$update = ! empty( $item->autoupdate );

			if ( ! $update && wp_is_auto_update_enabled_for_type( $type ) ) {
				// Check if the site admin has enabled auto-updates by default for the specific item.
				$auto_updates = (array) get_site_option( "auto_update_{$type}s", array() );
				$update       = in_array( $item->{$type}, $auto_updates, true );
			}
		} else {
			$update = ! empty( $item->autoupdate );
		}

		// If the `disable_autoupdate` flag is set, override any user-choice, but allow filters.
		if ( ! empty( $item->disable_autoupdate ) ) {
			$update = false;
		}

		/**
		 * Filters whether to automatically update core, a plugin, a theme, or a language.
		 *
		 * The dynamic portion of the hook name, `$type`, refers to the type of update
		 * being checked.
		 *
		 * Possible hook names include:
		 *
		 *  - `auto_update_core`
		 *  - `auto_update_plugin`
		 *  - `auto_update_theme`
		 *  - `auto_update_translation`
		 *
		 * Since WordPress 3.7, minor and development versions of core, and translations have
		 * been auto-updated by default. New installs on WordPress 5.6 or higher will also
		 * auto-update major versions by default. Starting in 5.6, older sites can opt-in to
		 * major version auto-updates, and auto-updates for plugins and themes.
		 *
		 * See the {@see 'allow_dev_auto_core_updates'}, {@see 'allow_minor_auto_core_updates'},
		 * and {@see 'allow_major_auto_core_updates'} filters for a more straightforward way to
		 * adjust core updates.
		 *
		 * @since 3.7.0
		 * @since 5.5.0 The `$update` parameter accepts the value of null.
		 *
		 * @param bool|null $update Whether to update. The value of null is internally used
		 *                          to detect whether nothing has hooked into this filter.
		 * @param object    $item   The update offer.
		 */
		$update = apply_filters( "auto_update_{$type}", $update, $item );

		if ( ! $update ) {
			if ( 'core' === $type ) {
				$this->send_core_update_notification_email( $item );
			}
			return false;
		}

		// If it's a core update, are we actually compatible with its requirements?
		if ( 'core' === $type ) {
			global $wpdb;

			$php_compat = version_compare( PHP_VERSION, $item->php_version, '>=' );
			if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) ) {
				$mysql_compat = true;
			} else {
				$mysql_compat = version_compare( $wpdb->db_version(), $item->mysql_version, '>=' );
			}

			if ( ! $php_compat || ! $mysql_compat ) {
				return false;
			}
		}

		// If updating a plugin or theme, ensure the minimum PHP version requirements are satisfied.
		if ( in_array( $type, array( 'plugin', 'theme' ), true ) ) {
			if ( ! empty( $item->requires_php ) && version_compare( PHP_VERSION, $item->requires_php, '<' ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Notifies an administrator of a core update.
	 *
	 * @since 3.7.0
	 *
	 * @param object $item The update offer.
	 * @return bool True if the site administrator is notified of a core update,
	 *              false otherwise.
	 */
	protected function send_core_update_notification_email( $item ) {
		$notified = get_site_option( 'auto_core_update_notified' );

		// Don't notify if we've already notified the same email address of the same version.
		if ( $notified
			&& get_site_option( 'admin_email' ) === $notified['email']
			&& $notified['version'] === $item->current
		) {
			return false;
		}

		// See if we need to notify users of a core update.
		$notify = ! empty( $item->notify_email );

		/**
		 * Filters whether to notify the site administrator of a new core update.
		 *
		 * By default, administrators are notified when the update offer received
		 * from WordPress.org sets a particular flag. This allows some discretion
		 * in if and when to notify.
		 *
		 * This filter is only evaluated once per release. If the same email address
		 * was already notified of the same new version, WordPress won't repeatedly
		 * email the administrator.
		 *
		 * This filter is also used on about.php to check if a plugin has disabled
		 * these notifications.
		 *
		 * @since 3.7.0
		 *
		 * @param bool   $notify Whether the site administrator is notified.
		 * @param object $item   The update offer.
		 */
		if ( ! apply_filters( 'send_core_update_notification_email', $notify, $item ) ) {
			return false;
		}

		$this->send_email( 'manual', $item );
		return true;
	}

	/**
	 * Updates an item, if appropriate.
	 *
	 * @since 3.7.0
	 *
	 * @param string $type The type of update being checked: 'core', 'theme', 'plugin', 'translation'.
	 * @param object $item The update offer.
	 * @return null|WP_Error
	 */
	public function update( $type, $item ) {
		$skin = new Automatic_Upgrader_Skin();

		switch ( $type ) {
			case 'core':
				// The Core upgrader doesn't use the Upgrader's skin during the actual main part of the upgrade, instead, firing a filter.
				add_filter( 'update_feedback', array( $skin, 'feedback' ) );
				$upgrader = new Core_Upgrader( $skin );
				$context  = ABSPATH;
				break;
			case 'plugin':
				$upgrader = new Plugin_Upgrader( $skin );
				$context  = WP_PLUGIN_DIR; // We don't support custom Plugin directories, or updates for WPMU_PLUGIN_DIR.
				break;
			case 'theme':
				$upgrader = new Theme_Upgrader( $skin );
				$context  = get_theme_root( $item->theme );
				break;
			case 'translation':
				$upgrader = new Language_Pack_Upgrader( $skin );
				$context  = WP_CONTENT_DIR; // WP_LANG_DIR;
				break;
		}

		// Determine whether we can and should perform this update.
		if ( ! $this->should_update( $type, $item, $context ) ) {
			return false;
		}

		/**
		 * Fires immediately prior to an auto-update.
		 *
		 * @since 4.4.0
		 *
		 * @param string $type    The type of update being checked: 'core', 'theme', 'plugin', or 'translation'.
		 * @param object $item    The update offer.
		 * @param string $context The filesystem context (a path) against which filesystem access and status
		 *                        should be checked.
		 */
		do_action( 'pre_auto_update', $type, $item, $context );

		$upgrader_item = $item;
		switch ( $type ) {
			case 'core':
				/* translators: %s: WordPress version. */
				$skin->feedback( __( 'Updating to WordPress %s' ), $item->version );
				/* translators: %s: WordPress version. */
				$item_name = sprintf( __( 'WordPress %s' ), $item->version );
				break;
			case 'theme':
				$upgrader_item = $item->theme;
				$theme         = wp_get_theme( $upgrader_item );
				$item_name     = $theme->Get( 'Name' );
				// Add the current version so that it can be reported in the notification email.
				$item->current_version = $theme->get( 'Version' );
				if ( empty( $item->current_version ) ) {
					$item->current_version = false;
				}
				/* translators: %s: Theme name. */
				$skin->feedback( __( 'Updating theme: %s' ), $item_name );
				break;
			case 'plugin':
				$upgrader_item = $item->plugin;
				$plugin_data   = get_plugin_data( $context . '/' . $upgrader_item );
				$item_name     = $plugin_data['Name'];
				// Add the current version so that it can be reported in the notification email.
				$item->current_version = $plugin_data['Version'];
				if ( empty( $item->current_version ) ) {
					$item->current_version = false;
				}
				/* translators: %s: Plugin name. */
				$skin->feedback( __( 'Updating plugin: %s' ), $item_name );
				break;
			case 'translation':
				$language_item_name = $upgrader->get_name_for_update( $item );
				/* translators: %s: Project name (plugin, theme, or WordPress). */
				$item_name = sprintf( __( 'Translations for %s' ), $language_item_name );
				/* translators: 1: Project name (plugin, theme, or WordPress), 2: Language. */
				$skin->feedback( sprintf( __( 'Updating translations for %1$s (%2$s)&#8230;' ), $language_item_name, $item->language ) );
				break;
		}

		$allow_relaxed_file_ownership = false;
		if ( 'core' === $type && isset( $item->new_files ) && ! $item->new_files ) {
			$allow_relaxed_file_ownership = true;
		}

		$is_debug = WP_DEBUG && WP_DEBUG_LOG;
		if ( 'plugin' === $type ) {
			$was_active = is_plugin_active( $upgrader_item );
			if ( $is_debug ) {
				error_log( '    Upgrading plugin ' . var_export( $item->slug, true ) . '...' );
			}
		}

		if ( 'theme' === $type && $is_debug ) {
			error_log( '    Upgrading theme ' . var_export( $item->theme, true ) . '...' );
		}

		/*
		 * Enable maintenance mode before upgrading the plugin or theme.
		 *
		 * This avoids potential non-fatal errors being detected
		 * while scraping for a fatal error if some files are still
		 * being moved.
		 *
		 * While these checks are intended only for plugins,
		 * maintenance mode is enabled for all upgrade types as any
		 * update could contain an error or warning, which could cause
		 * the scrape to miss a fatal error in the plugin update.
		 */
		if ( 'translation' !== $type ) {
			$upgrader->maintenance_mode( true );
		}

		// Boom, this site's about to get a whole new splash of paint!
		$upgrade_result = $upgrader->upgrade(
			$upgrader_item,
			array(
				'clear_update_cache'           => false,
				// Always use partial builds if possible for core updates.
				'pre_check_md5'                => false,
				// Only available for core updates.
				'attempt_rollback'             => true,
				// Allow relaxed file ownership in some scenarios.
				'allow_relaxed_file_ownership' => $allow_relaxed_file_ownership,
			)
		);

		/*
		 * After WP_Upgrader::upgrade() completes, maintenance mode is disabled.
		 *
		 * Re-enable maintenance mode while attempting to detect fatal errors
		 * and potentially rolling back.
		 *
		 * This avoids errors if the site is visited while fatal errors exist
		 * or while files are still being moved.
		 */
		if ( 'translation' !== $type ) {
			$upgrader->maintenance_mode( true );
		}

		// If the filesystem is unavailable, false is returned.
		if ( false === $upgrade_result ) {
			$upgrade_result = new WP_Error( 'fs_unavailable', __( 'Could not access filesystem.' ) );
		}

		if ( 'core' === $type ) {
			if ( is_wp_error( $upgrade_result )
				&& ( 'up_to_date' === $upgrade_result->get_error_code()
					|| 'locked' === $upgrade_result->get_error_code() )
			) {
				// Allow visitors to browse the site again.
				$upgrader->maintenance_mode( false );

				/*
				 * These aren't actual errors, treat it as a skipped-update instead
				 * to avoid triggering the post-core update failure routines.
				 */
				return false;
			}

			// Core doesn't output this, so let's append it, so we don't get confused.
			if ( is_wp_error( $upgrade_result ) ) {
				$upgrade_result->add( 'installation_failed', __( 'Installation failed.' ) );
				$skin->error( $upgrade_result );
			} else {
				$skin->feedback( __( 'WordPress updated successfully.' ) );
			}
		}

		$is_debug = WP_DEBUG && WP_DEBUG_LOG;

		if ( 'theme' === $type && $is_debug ) {
			error_log( '    Theme ' . var_export( $item->theme, true ) . ' has been upgraded.' );
		}

		if ( 'plugin' === $type ) {
			if ( $is_debug ) {
				error_log( '    Plugin ' . var_export( $item->slug, true ) . ' has been upgraded.' );
				if ( is_plugin_inactive( $upgrader_item ) ) {
					error_log( '    ' . var_export( $upgrader_item, true ) . ' is inactive and will not be checked for fatal errors.' );
				}
			}

			if ( $was_active && ! is_wp_error( $upgrade_result ) ) {

				/*
				 * The usual time limit is five minutes. However, as a loopback request
				 * is about to be performed, increase the time limit to account for this.
				 */
				if ( function_exists( 'set_time_limit' ) ) {
					set_time_limit( 10 * MINUTE_IN_SECONDS );
				}

				/*
				 * Avoids a race condition when there are 2 sequential plugins that have
				 * fatal errors. It seems a slight delay is required for the loopback to
				 * use the updated plugin code in the request. This can cause the second
				 * plugin's fatal error checking to be inaccurate, and may also affect
				 * subsequent plugin checks.
				 */
				sleep( 2 );

				if ( $this->has_fatal_error() ) {
					$upgrade_result = new WP_Error();
					$temp_backup    = array(
						array(
							'dir'  => 'plugins',
							'slug' => $item->slug,
							'src'  => WP_PLUGIN_DIR,
						),
					);

					$backup_restored = $upgrader->restore_temp_backup( $temp_backup );
					if ( is_wp_error( $backup_restored ) ) {
						$upgrade_result->add(
							'plugin_update_fatal_error_rollback_failed',
							sprintf(
								/* translators: %s: The plugin's slug. */
								__( "The update for '%s' contained a fatal error. The previously installed version could not be restored." ),
								$item->slug
							)
						);

						$upgrade_result->merge_from( $backup_restored );
					} else {
						$upgrade_result->add(
							'plugin_update_fatal_error_rollback_successful',
							sprintf(
								/* translators: %s: The plugin's slug. */
								__( "The update for '%s' contained a fatal error. The previously installed version has been restored." ),
								$item->slug
							)
						);

						$backup_deleted = $upgrader->delete_temp_backup( $temp_backup );
						if ( is_wp_error( $backup_deleted ) ) {
							$upgrade_result->merge_from( $backup_deleted );
						}
					}

					/*
					 * Should emails not be working, log the message(s) so that
					 * the log file contains context for the fatal error,
					 * and whether a rollback was performed.
					 *
					 * `trigger_error()` is not used as it outputs a stack trace
					 * to this location rather than to the fatal error, which will
					 * appear above this entry in the log file.
					 */
					if ( $is_debug ) {
						error_log( '    ' . implode( "\n", $upgrade_result->get_error_messages() ) );
					}
				} elseif ( $is_debug ) {
					error_log( '    The update for ' . var_export( $item->slug, true ) . ' has no fatal errors.' );
				}
			}
		}

		// All processes are complete. Allow visitors to browse the site again.
		if ( 'translation' !== $type ) {
			$upgrader->maintenance_mode( false );
		}

		$this->update_results[ $type ][] = (object) array(
			'item'     => $item,
			'result'   => $upgrade_result,
			'name'     => $item_name,
			'messages' => $skin->get_upgrade_messages(),
		);

		return $upgrade_result;
	}

	/**
	 * Kicks off the background update process, looping through all pending updates.
	 *
	 * @since 3.7.0
	 */
	public function run() {
		if ( $this->is_disabled() ) {
			return;
		}

		if ( ! is_main_network() || ! is_main_site() ) {
			return;
		}

		if ( ! WP_Upgrader::create_lock( 'auto_updater' ) ) {
			return;
		}

		$is_debug = WP_DEBUG && WP_DEBUG_LOG;

		if ( $is_debug ) {
			error_log( 'Automatic updates starting...' );
		}

		// Don't automatically run these things, as we'll handle it ourselves.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		remove_action( 'upgrader_process_complete', 'wp_version_check' );
		remove_action( 'upgrader_process_complete', 'wp_update_plugins' );
		remove_action( 'upgrader_process_complete', 'wp_update_themes' );

		// Next, plugins.
		wp_update_plugins(); // Check for plugin updates.
		$plugin_updates = get_site_transient( 'update_plugins' );
		if ( $plugin_updates && ! empty( $plugin_updates->response ) ) {
			if ( $is_debug ) {
				error_log( '  Automatic plugin updates starting...' );
			}

			foreach ( $plugin_updates->response as $plugin ) {
				$this->update( 'plugin', $plugin );
			}

			// Force refresh of plugin update information.
			wp_clean_plugins_cache();

			if ( $is_debug ) {
				error_log( '  Automatic plugin updates complete.' );
			}
		}

		// Next, those themes we all love.
		wp_update_themes();  // Check for theme updates.
		$theme_updates = get_site_transient( 'update_themes' );
		if ( $theme_updates && ! empty( $theme_updates->response ) ) {
			if ( $is_debug ) {
				error_log( '  Automatic theme updates starting...' );
			}

			foreach ( $theme_updates->response as $theme ) {
				$this->update( 'theme', (object) $theme );
			}
			// Force refresh of theme update information.
			wp_clean_themes_cache();

			if ( $is_debug ) {
				error_log( '  Automatic theme updates complete.' );
			}
		}

		if ( $is_debug ) {
			error_log( 'Automatic updates complete.' );
		}

		// Next, process any core update.
		wp_version_check(); // Check for core updates.
		$core_update = find_core_auto_update();

		if ( $core_update ) {
			$this->update( 'core', $core_update );
		}

		/*
		 * Clean up, and check for any pending translations.
		 * (Core_Upgrader checks for core updates.)
		 */
		$theme_stats = array();
		if ( isset( $this->update_results['theme'] ) ) {
			foreach ( $this->update_results['theme'] as $upgrade ) {
				$theme_stats[ $upgrade->item->theme ] = ( true === $upgrade->result );
			}
		}
		wp_update_themes( $theme_stats ); // Check for theme updates.

		$plugin_stats = array();
		if ( isset( $this->update_results['plugin'] ) ) {
			foreach ( $this->update_results['plugin'] as $upgrade ) {
				$plugin_stats[ $upgrade->item->plugin ] = ( true === $upgrade->result );
			}
		}
		wp_update_plugins( $plugin_stats ); // Check for plugin updates.

		// Finally, process any new translations.
		$language_updates = wp_get_translation_updates();
		if ( $language_updates ) {
			foreach ( $language_updates as $update ) {
				$this->update( 'translation', $update );
			}

			// Clear existing caches.
			wp_clean_update_cache();

			wp_version_check();  // Check for core updates.
			wp_update_themes();  // Check for theme updates.
			wp_update_plugins(); // Check for plugin updates.
		}

		// Send debugging email to admin for all development installations.
		if ( ! empty( $this->update_results ) ) {
			$development_version = str_contains( wp_get_wp_version(), '-' );

			/**
			 * Filters whether to send a debugging email for each automatic background update.
			 *
			 * @since 3.7.0
			 *
			 * @param bool $development_version By default, emails are sent if the
			 *                                  install is a development version.
			 *                                  Return false to avoid the email.
			 */
			if ( apply_filters( 'automatic_updates_send_debug_email', $development_version ) ) {
				$this->send_debug_email();
			}

			if ( ! empty( $this->update_results['core'] ) ) {
				$this->after_core_update( $this->update_results['core'][0] );
			} elseif ( ! empty( $this->update_results['plugin'] ) || ! empty( $this->update_results['theme'] ) ) {
				$this->after_plugin_theme_update( $this->update_results );
			}

			/**
			 * Fires after all automatic updates have run.
			 *
			 * @since 3.8.0
			 *
			 * @param array $update_results The results of all attempted updates.
			 */
			do_action( 'automatic_updates_complete', $this->update_results );
		}

		WP_Upgrader::release_lock( 'auto_updater' );
	}

	/**
	 * Checks whether to send an email and avoid processing future updates after
	 * attempting a core update.
	 *
	 * @since 3.7.0
	 *
	 * @param object $update_result The result of the core update. Includes the update offer and result.
	 */
	protected function after_core_update( $update_result ) {
		$wp_version = wp_get_wp_version();

		$core_update = $update_result->item;
		$result      = $update_result->result;

		if ( ! is_wp_error( $result ) ) {
			$this->send_email( 'success', $core_update );
			return;
		}

		$error_code = $result->get_error_code();

		/*
		 * Any of these WP_Error codes are critical failures, as in they occurred after we started to copy core files.
		 * We should not try to perform a background update again until there is a successful one-click update performed by the user.
		 */
		$critical = false;
		if ( 'disk_full' === $error_code || str_contains( $error_code, '__copy_dir' ) ) {
			$critical = true;
		} elseif ( 'rollback_was_required' === $error_code && is_wp_error( $result->get_error_data()->rollback ) ) {
			// A rollback is only critical if it failed too.
			$critical        = true;
			$rollback_result = $result->get_error_data()->rollback;
		} elseif ( str_contains( $error_code, 'do_rollback' ) ) {
			$critical = true;
		}

		if ( $critical ) {
			$critical_data = array(
				'attempted'  => $core_update->current,
				'current'    => $wp_version,
				'error_code' => $error_code,
				'error_data' => $result->get_error_data(),
				'timestamp'  => time(),
				'critical'   => true,
			);
			if ( isset( $rollback_result ) ) {
				$critical_data['rollback_code'] = $rollback_result->get_error_code();
				$critical_data['rollback_data'] = $rollback_result->get_error_data();
			}
			update_site_option( 'auto_core_update_failed', $critical_data );
			$this->send_email( 'critical', $core_update, $result );
			return;
		}

		/*
		 * Any other WP_Error code (like download_failed or files_not_writable) occurs before
		 * we tried to copy over core files. Thus, the failures are early and graceful.
		 *
		 * We should avoid trying to perform a background update again for the same version.
		 * But we can try again if another version is released.
		 *
		 * For certain 'transient' failures, like download_failed, we should allow retries.
		 * In fact, let's schedule a special update for an hour from now. (It's possible
		 * the issue could actually be on WordPress.org's side.) If that one fails, then email.
		 */
		$send               = true;
		$transient_failures = array( 'incompatible_archive', 'download_failed', 'insane_distro', 'locked' );
		if ( in_array( $error_code, $transient_failures, true ) && ! get_site_option( 'auto_core_update_failed' ) ) {
			wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'wp_maybe_auto_update' );
			$send = false;
		}

		$notified = get_site_option( 'auto_core_update_notified' );

		// Don't notify if we've already notified the same email address of the same version of the same notification type.
		if ( $notified
			&& 'fail' === $notified['type']
			&& get_site_option( 'admin_email' ) === $notified['email']
			&& $notified['version'] === $core_update->current
		) {
			$send = false;
		}

		update_site_option(
			'auto_core_update_failed',
			array(
				'attempted'  => $core_update->current,
				'current'    => $wp_version,
				'error_code' => $error_code,
				'error_data' => $result->get_error_data(),
				'timestamp'  => time(),
				'retry'      => in_array( $error_code, $transient_failures, true ),
			)
		);

		if ( $send ) {
			$this->send_email( 'fail', $core_update, $result );
		}
	}

	/**
	 * Sends an email upon the completion or failure of a background core update.
	 *
	 * @since 3.7.0
	 *
	 * @param string $type        The type of email to send. Can be one of 'success', 'fail', 'manual', 'critical'.
	 * @param object $core_update The update offer that was attempted.
	 * @param mixed  $result      Optional. The result for the core update. Can be WP_Error.
	 */
	protected function send_email( $type, $core_update, $result = null ) {
		update_site_option(
			'auto_core_update_notified',
			array(
				'type'      => $type,
				'email'     => get_site_option( 'admin_email' ),
				'version'   => $core_update->current,
				'timestamp' => time(),
			)
		);

		$next_user_core_update = get_preferred_from_update_core();

		// If the update transient is empty, use the update we just performed.
		if ( ! $next_user_core_update ) {
			$next_user_core_update = $core_update;
		}

		if ( 'upgrade' === $next_user_core_update->response
			&& version_compare( $next_user_core_update->version, $core_update->version, '>' )
		) {
			$newer_version_available = true;
		} else {
			$newer_version_available = false;
		}

		/**
		 * Filters whether to send an email following an automatic background core update.
		 *
		 * @since 3.7.0
		 *
		 * @param bool   $send        Whether to send the email. Default true.
		 * @param string $type        The type of email to send. Can be one of
		 *                            'success', 'fail', 'critical'.
		 * @param object $core_update The update offer that was attempted.
		 * @param mixed  $result      The result for the core update. Can be WP_Error.
		 */
		if ( 'manual' !== $type && ! apply_filters( 'auto_core_update_send_email', true, $type, $core_update, $result ) ) {
			return;
		}

		$admin_user = get_user_by( 'email', get_site_option( 'admin_email' ) );

		if ( $admin_user ) {
			$switched_locale = switch_to_user_locale( $admin_user->ID );
		} else {
			$switched_locale = switch_to_locale( get_locale() );
		}

		switch ( $type ) {
			case 'success': // We updated.
				/* translators: Site updated notification email subject. 1: Site title, 2: WordPress version. */
				$subject = __( '[%1$s] Your site has updated to WordPress %2$s' );
				break;

			case 'fail':   // We tried to update but couldn't.
			case 'manual': // We can't update (and made no attempt).
				/* translators: Update available notification email subject. 1: Site title, 2: WordPress version. */
				$subject = __( '[%1$s] WordPress %2$s is available. Please update!' );
				break;

			case 'critical': // We tried to update, started to copy files, then things went wrong.
				/* translators: Site down notification email subject. 1: Site title. */
				$subject = __( '[%1$s] URGENT: Your site may be down due to a failed update' );
				break;

			default:
				return;
		}

		// If the auto-update is not to the latest version, say that the current version of WP is available instead.
		$version = 'success' === $type ? $core_update->current : $next_user_core_update->current;
		$subject = sprintf( $subject, wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $version );

		$body = '';

		switch ( $type ) {
			case 'success':
				$body .= sprintf(
					/* translators: 1: Home URL, 2: WordPress version. */
					__( 'Howdy! Your site at %1$s has been updated automatically to WordPress %2$s.' ),
					home_url(),
					$core_update->current
				);
				$body .= "\n\n";
				if ( ! $newer_version_available ) {
					$body .= __( 'No further action is needed on your part.' ) . ' ';
				}

				// Can only reference the About screen if their update was successful.
				list( $about_version ) = explode( '-', $core_update->current, 2 );
				/* translators: %s: WordPress version. */
				$body .= sprintf( __( 'For more on version %s, see the About WordPress screen:' ), $about_version );
				$body .= "\n" . admin_url( 'about.php' );

				if ( $newer_version_available ) {
					/* translators: %s: WordPress latest version. */
					$body .= "\n\n" . sprintf( __( 'WordPress %s is also now available.' ), $next_user_core_update->current ) . ' ';
					$body .= __( 'Updating is easy and only takes a few moments:' );
					$body .= "\n" . network_admin_url( 'update-core.php' );
				}

				break;

			case 'fail':
			case 'manual':
				$body .= sprintf(
					/* translators: 1: Home URL, 2: WordPress version. */
					__( 'Please update your site at %1$s to WordPress %2$s.' ),
					home_url(),
					$next_user_core_update->current
				);

				$body .= "\n\n";

				/*
				 * Don't show this message if there is a newer version available.
				 * Potential for confusion, and also not useful for them to know at this point.
				 */
				if ( 'fail' === $type && ! $newer_version_available ) {
					$body .= __( 'An attempt was made, but your site could not be updated automatically.' ) . ' ';
				}

				$body .= __( 'Updating is easy and only takes a few moments:' );
				$body .= "\n" . network_admin_url( 'update-core.php' );
				break;

			case 'critical':
				if ( $newer_version_available ) {
					$body .= sprintf(
						/* translators: 1: Home URL, 2: WordPress version. */
						__( 'Your site at %1$s experienced a critical failure while trying to update WordPress to version %2$s.' ),
						home_url(),
						$core_update->current
					);
				} else {
					$body .= sprintf(
						/* translators: 1: Home URL, 2: WordPress latest version. */
						__( 'Your site at %1$s experienced a critical failure while trying to update to the latest version of WordPress, %2$s.' ),
						home_url(),
						$core_update->current
					);
				}

				$body .= "\n\n" . __( "This means your site may be offline or broken. Don't panic; this can be fixed." );

				$body .= "\n\n" . __( "Please check out your site now. It's possible that everything is working. If it says you need to update, you should do so:" );
				$body .= "\n" . network_admin_url( 'update-core.php' );
				break;
		}

		$critical_support = 'critical' === $type && ! empty( $core_update->support_email );
		if ( $critical_support ) {
			// Support offer if available.
			$body .= "\n\n" . sprintf(
				/* translators: %s: Support email address. */
				__( 'The WordPress team is willing to help you. Forward this email to %s and the team will work with you to make sure your site is working.' ),
				$core_update->support_email
			);
		} else {
			// Add a note about the support forums.
			$body .= "\n\n" . __( 'If you experience any issues or need support, the volunteers in the WordPress.org support forums may be able to help.' );
			$body .= "\n" . __( 'https://wordpress.org/support/forums/' );
		}

		// Updates are important!
		if ( 'success' !== $type || $newer_version_available ) {
			$body .= "\n\n" . __( 'Keeping your site updated is important for security. It also makes the internet a safer place for you and your readers.' );
		}

		if ( $critical_support ) {
			$body .= ' ' . __( "Reach out to WordPress Core developers to ensure you'll never have this problem again." );
		}

		// If things are successful and we're now on the latest, mention plugins and themes if any are out of date.
		if ( 'success' === $type && ! $newer_version_available && ( get_plugin_updates() || get_theme_updates() ) ) {
			$body .= "\n\n" . __( 'You also have some plugins or themes with updates available. Update them now:' );
			$body .= "\n" . network_admin_url();
		}

		$body .= "\n\n" . __( 'The WordPress Team' ) . "\n";

		if ( 'critical' === $type && is_wp_error( $result ) ) {
			$body .= "\n***\n\n";
			/* translators: %s: WordPress version. */
			$body .= sprintf( __( 'Your site was running version %s.' ), get_bloginfo( 'version' ) );
			$body .= ' ' . __( 'Some data that describes the error your site encountered has been put together.' );
			$body .= ' ' . __( 'Your hosting company, support forum volunteers, or a friendly developer may be able to use this information to help you:' );

			/*
			 * If we had a rollback and we're still critical, then the rollback failed too.
			 * Loop through all errors (the main WP_Error, the update result, the rollback result) for code, data, etc.
			 */
			if ( 'rollback_was_required' === $result->get_error_code() ) {
				$errors = array( $result, $result->get_error_data()->update, $result->get_error_data()->rollback );
			} else {
				$errors = array( $result );
			}

			foreach ( $errors as $error ) {
				if ( ! is_wp_error( $error ) ) {
					continue;
				}

				$error_code = $error->get_error_code();
				/* translators: %s: Error code. */
				$body .= "\n\n" . sprintf( __( 'Error code: %s' ), $error_code );

				if ( 'rollback_was_required' === $error_code ) {
					continue;
				}

				if ( $error->get_error_message() ) {
					$body .= "\n" . $error->get_error_message();
				}

				$error_data = $error->get_error_data();
				if ( $error_data ) {
					$body .= "\n" . implode( ', ', (array) $error_data );
				}
			}

			$body .= "\n";
		}

		$to      = get_site_option( 'admin_email' );
		$headers = '';

		$email = compact( 'to', 'subject', 'body', 'headers' );

		/**
		 * Filters the email sent following an automatic background core update.
		 *
		 * @since 3.7.0
		 *
		 * @param array $email {
		 *     Array of email arguments that will be passed to wp_mail().
		 *
		 *     @type string $to      The email recipient. An array of emails
		 *                            can be returned, as handled by wp_mail().
		 *     @type string $subject The email's subject.
		 *     @type string $body    The email message body.
		 *     @type string $headers Any email headers, defaults to no headers.
		 * }
		 * @param string $type        The type of email being sent. Can be one of
		 *                            'success', 'fail', 'manual', 'critical'.
		 * @param object $core_update The update offer that was attempted.
		 * @param mixed  $result      The result for the core update. Can be WP_Error.
		 */
		$email = apply_filters( 'auto_core_update_email', $email, $type, $core_update, $result );

		wp_mail( $email['to'], wp_specialchars_decode( $email['subject'] ), $email['body'], $email['headers'] );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	/**
	 * Checks whether an email should be sent after attempting plugin or theme updates.
	 *
	 * @since 5.5.0
	 *
	 * @param array $update_results The results of update tasks.
	 */
	protected function after_plugin_theme_update( $update_results ) {
		$successful_updates = array();
		$failed_updates     = array();

		if ( ! empty( $update_results['plugin'] ) ) {
			/**
			 * Filters whether to send an email following an automatic background plugin update.
			 *
			 * @since 5.5.0
			 * @since 5.5.1 Added the `$update_results` parameter.
			 *
			 * @param bool  $enabled        True if plugin update notifications are enabled, false otherwise.
			 * @param array $update_results The results of plugins update tasks.
			 */
			$notifications_enabled = apply_filters( 'auto_plugin_update_send_email', true, $update_results['plugin'] );

			if ( $notifications_enabled ) {
				foreach ( $update_results['plugin'] as $update_result ) {
					if ( true === $update_result->result ) {
						$successful_updates['plugin'][] = $update_result;
					} else {
						$failed_updates['plugin'][] = $update_result;
					}
				}
			}
		}

		if ( ! empty( $update_results['theme'] ) ) {
			/**
			 * Filters whether to send an email following an automatic background theme update.
			 *
			 * @since 5.5.0
			 * @since 5.5.1 Added the `$update_results` parameter.
			 *
			 * @param bool  $enabled        True if theme update notifications are enabled, false otherwise.
			 * @param array $update_results The results of theme update tasks.
			 */
			$notifications_enabled = apply_filters( 'auto_theme_update_send_email', true, $update_results['theme'] );

			if ( $notifications_enabled ) {
				foreach ( $update_results['theme'] as $update_result ) {
					if ( true === $update_result->result ) {
						$successful_updates['theme'][] = $update_result;
					} else {
						$failed_updates['theme'][] = $update_result;
					}
				}
			}
		}

		if ( empty( $successful_updates ) && empty( $failed_updates ) ) {
			return;
		}

		if ( empty( $failed_updates ) ) {
			$this->send_plugin_theme_email( 'success', $successful_updates, $failed_updates );
		} elseif ( empty( $successful_updates ) ) {
			$this->send_plugin_theme_email( 'fail', $successful_updates, $failed_updates );
		} else {
			$this->send_plugin_theme_email( 'mixed', $successful_updates, $failed_updates );
		}
	}

	/**
	 * Sends an email upon the completion or failure of a plugin or theme background update.
	 *
	 * @since 5.5.0
	 *
	 * @param string $type               The type of email to send. Can be one of 'success', 'fail', 'mixed'.
	 * @param array  $successful_updates A list of updates that succeeded.
	 * @param array  $failed_updates     A list of updates that failed.
	 */
	protected function send_plugin_theme_email( $type, $successful_updates, $failed_updates ) {
		// No updates were attempted.
		if ( empty( $successful_updates ) && empty( $failed_updates ) ) {
			return;
		}

		$unique_failures     = false;
		$past_failure_emails = get_option( 'auto_plugin_theme_update_emails', array() );

		/*
		 * When only failures have occurred, an email should only be sent if there are unique failures.
		 * A failure is considered unique if an email has not been sent for an update attempt failure
		 * to a plugin or theme with the same new_version.
		 */
		if ( 'fail' === $type ) {
			foreach ( $failed_updates as $update_type => $failures ) {
				foreach ( $failures as $failed_update ) {
					if ( ! isset( $past_failure_emails[ $failed_update->item->{$update_type} ] ) ) {
						$unique_failures = true;
						continue;
					}

					// Check that the failure represents a new failure based on the new_version.
					if ( version_compare( $past_failure_emails[ $failed_update->item->{$update_type} ], $failed_update->item->new_version, '<' ) ) {
						$unique_failures = true;
					}
				}
			}

			if ( ! $unique_failures ) {
				return;
			}
		}

		$admin_user = get_user_by( 'email', get_site_option( 'admin_email' ) );

		if ( $admin_user ) {
			$switched_locale = switch_to_user_locale( $admin_user->ID );
		} else {
			$switched_locale = switch_to_locale( get_locale() );
		}

		$body               = array();
		$successful_plugins = ( ! empty( $successful_updates['plugin'] ) );
		$successful_themes  = ( ! empty( $successful_updates['theme'] ) );
		$failed_plugins     = ( ! empty( $failed_updates['plugin'] ) );
		$failed_themes      = ( ! empty( $failed_updates['theme'] ) );

		switch ( $type ) {
			case 'success':
				if ( $successful_plugins && $successful_themes ) {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some plugins and themes have automatically updated' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Some plugins and themes have automatically updated to their latest versions on your site at %s. No further action is needed on your part.' ),
						home_url()
					);
				} elseif ( $successful_plugins ) {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some plugins were automatically updated' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Some plugins have automatically updated to their latest versions on your site at %s. No further action is needed on your part.' ),
						home_url()
					);
				} else {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some themes were automatically updated' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Some themes have automatically updated to their latest versions on your site at %s. No further action is needed on your part.' ),
						home_url()
					);
				}

				break;
			case 'fail':
			case 'mixed':
				if ( $failed_plugins && $failed_themes ) {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some plugins and themes have failed to update' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Plugins and themes failed to update on your site at %s.' ),
						home_url()
					);
				} elseif ( $failed_plugins ) {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some plugins have failed to update' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Plugins failed to update on your site at %s.' ),
						home_url()
					);
				} else {
					/* translators: %s: Site title. */
					$subject = __( '[%s] Some themes have failed to update' );
					$body[]  = sprintf(
						/* translators: %s: Home URL. */
						__( 'Howdy! Themes failed to update on your site at %s.' ),
						home_url()
					);
				}

				break;
		}

		if ( in_array( $type, array( 'fail', 'mixed' ), true ) ) {
			$body[] = "\n";
			$body[] = __( 'Please check your site now. It’s possible that everything is working. If there are updates available, you should update.' );
			$body[] = "\n";

			// List failed plugin updates.
			if ( ! empty( $failed_updates['plugin'] ) ) {
				$body[] = __( 'The following plugins failed to update. If there was a fatal error in the update, the previously installed version has been restored.' );

				foreach ( $failed_updates['plugin'] as $item ) {
					$body_message = '';
					$item_url     = '';

					if ( ! empty( $item->item->url ) ) {
						$item_url = ' : ' . esc_url( $item->item->url );
					}

					if ( $item->item->current_version ) {
						$body_message .= sprintf(
							/* translators: 1: Plugin name, 2: Current version number, 3: New version number, 4: Plugin URL. */
							__( '- %1$s (from version %2$s to %3$s)%4$s' ),
							html_entity_decode( $item->name ),
							$item->item->current_version,
							$item->item->new_version,
							$item_url
						);
					} else {
						$body_message .= sprintf(
							/* translators: 1: Plugin name, 2: Version number, 3: Plugin URL. */
							__( '- %1$s version %2$s%3$s' ),
							html_entity_decode( $item->name ),
							$item->item->new_version,
							$item_url
						);
					}

					$body[] = $body_message;

					$past_failure_emails[ $item->item->plugin ] = $item->item->new_version;
				}

				$body[] = "\n";
			}

			// List failed theme updates.
			if ( ! empty( $failed_updates['theme'] ) ) {
				$body[] = __( 'These themes failed to update:' );

				foreach ( $failed_updates['theme'] as $item ) {
					if ( $item->item->current_version ) {
						$body[] = sprintf(
							/* translators: 1: Theme name, 2: Current version number, 3: New version number. */
							__( '- %1$s (from version %2$s to %3$s)' ),
							html_entity_decode( $item->name ),
							$item->item->current_version,
							$item->item->new_version
						);
					} else {
						$body[] = sprintf(
							/* translators: 1: Theme name, 2: Version number. */
							__( '- %1$s version %2$s' ),
							html_entity_decode( $item->name ),
							$item->item->new_version
						);
					}

					$past_failure_emails[ $item->item->theme ] = $item->item->new_version;
				}

				$body[] = "\n";
			}
		}

		// List successful updates.
		if ( in_array( $type, array( 'success', 'mixed' ), true ) ) {
			$body[] = "\n";

			// List successful plugin updates.
			if ( ! empty( $successful_updates['plugin'] ) ) {
				$body[] = __( 'These plugins are now up to date:' );

				foreach ( $successful_updates['plugin'] as $item ) {
					$body_message = '';
					$item_url     = '';

					if ( ! empty( $item->item->url ) ) {
						$item_url = ' : ' . esc_url( $item->item->url );
					}

					if ( $item->item->current_version ) {
						$body_message .= sprintf(
							/* translators: 1: Plugin name, 2: Current version number, 3: New version number, 4: Plugin URL. */
							__( '- %1$s (from version %2$s to %3$s)%4$s' ),
							html_entity_decode( $item->name ),
							$item->item->current_version,
							$item->item->new_version,
							$item_url
						);
					} else {
						$body_message .= sprintf(
							/* translators: 1: Plugin name, 2: Version number, 3: Plugin URL. */
							__( '- %1$s version %2$s%3$s' ),
							html_entity_decode( $item->name ),
							$item->item->new_version,
							$item_url
						);
					}
					$body[] = $body_message;

					unset( $past_failure_emails[ $item->item->plugin ] );
				}

				$body[] = "\n";
			}

			// List successful theme updates.
			if ( ! empty( $successful_updates['theme'] ) ) {
				$body[] = __( 'These themes are now up to date:' );

				foreach ( $successful_updates['theme'] as $item ) {
					if ( $item->item->current_version ) {
						$body[] = sprintf(
							/* translators: 1: Theme name, 2: Current version number, 3: New version number. */
							__( '- %1$s (from version %2$s to %3$s)' ),
							html_entity_decode( $item->name ),
							$item->item->current_version,
							$item->item->new_version
						);
					} else {
						$body[] = sprintf(
							/* translators: 1: Theme name, 2: Version number. */
							__( '- %1$s version %2$s' ),
							html_entity_decode( $item->name ),
							$item->item->new_version
						);
					}

					unset( $past_failure_emails[ $item->item->theme ] );
				}

				$body[] = "\n";
			}
		}

		if ( $failed_plugins ) {
			$body[] = sprintf(
				/* translators: %s: Plugins screen URL. */
				__( 'To manage plugins on your site, visit the Plugins page: %s' ),
				admin_url( 'plugins.php' )
			);
			$body[] = "\n";
		}

		if ( $failed_themes ) {
			$body[] = sprintf(
				/* translators: %s: Themes screen URL. */
				__( 'To manage themes on your site, visit the Themes page: %s' ),
				admin_url( 'themes.php' )
			);
			$body[] = "\n";
		}

		// Add a note about the support forums.
		$body[] = __( 'If you experience any issues or need support, the volunteers in the WordPress.org support forums may be able to help.' );
		$body[] = __( 'https://wordpress.org/support/forums/' );
		$body[] = "\n" . __( 'The WordPress Team' );

		if ( '' !== get_option( 'blogname' ) ) {
			$site_title = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		} else {
			$site_title = parse_url( home_url(), PHP_URL_HOST );
		}

		$body    = implode( "\n", $body );
		$to      = get_site_option( 'admin_email' );
		$subject = sprintf( $subject, $site_title );
		$headers = '';

		$email = compact( 'to', 'subject', 'body', 'headers' );

		/**
		 * Filters the email sent following an automatic background update for plugins and themes.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $email {
		 *     Array of email arguments that will be passed to wp_mail().
		 *
		 *     @type string $to      The email recipient. An array of emails
		 *                           can be returned, as handled by wp_mail().
		 *     @type string $subject The email's subject.
		 *     @type string $body    The email message body.
		 *     @type string $headers Any email headers, defaults to no headers.
		 * }
		 * @param string $type               The type of email being sent. Can be one of 'success', 'fail', 'mixed'.
		 * @param array  $successful_updates A list of updates that succeeded.
		 * @param array  $failed_updates     A list of updates that failed.
		 */
		$email = apply_filters( 'auto_plugin_theme_update_email', $email, $type, $successful_updates, $failed_updates );

		$result = wp_mail( $email['to'], wp_specialchars_decode( $email['subject'] ), $email['body'], $email['headers'] );

		if ( $result ) {
			update_option( 'auto_plugin_theme_update_emails', $past_failure_emails );
		}

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	/**
	 * Prepares and sends an email of a full log of background update results, useful for debugging and geekery.
	 *
	 * @since 3.7.0
	 */
	protected function send_debug_email() {
		$admin_user = get_user_by( 'email', get_site_option( 'admin_email' ) );

		if ( $admin_user ) {
			$switched_locale = switch_to_user_locale( $admin_user->ID );
		} else {
			$switched_locale = switch_to_locale( get_locale() );
		}

		$body     = array();
		$failures = 0;

		/* translators: %s: Network home URL. */
		$body[] = sprintf( __( 'WordPress site: %s' ), network_home_url( '/' ) );

		// Core.
		if ( isset( $this->update_results['core'] ) ) {
			$result = $this->update_results['core'][0];

			if ( $result->result && ! is_wp_error( $result->result ) ) {
				/* translators: %s: WordPress version. */
				$body[] = sprintf( __( 'SUCCESS: WordPress was successfully updated to %s' ), $result->name );
			} else {
				/* translators: %s: WordPress version. */
				$body[] = sprintf( __( 'FAILED: WordPress failed to update to %s' ), $result->name );
				++$failures;
			}

			$body[] = '';
		}

		// Plugins, Themes, Translations.
		foreach ( array( 'plugin', 'theme', 'translation' ) as $type ) {
			if ( ! isset( $this->update_results[ $type ] ) ) {
				continue;
			}

			$success_items = wp_list_filter( $this->update_results[ $type ], array( 'result' => true ) );

			if ( $success_items ) {
				$messages = array(
					'plugin'      => __( 'The following plugins were successfully updated:' ),
					'theme'       => __( 'The following themes were successfully updated:' ),
					'translation' => __( 'The following translations were successfully updated:' ),
				);

				$body[] = $messages[ $type ];
				foreach ( wp_list_pluck( $success_items, 'name' ) as $name ) {
					/* translators: %s: Name of plugin / theme / translation. */
					$body[] = ' * ' . sprintf( __( 'SUCCESS: %s' ), $name );
				}
			}

			if ( $success_items !== $this->update_results[ $type ] ) {
				// Failed updates.
				$messages = array(
					'plugin'      => __( 'The following plugins failed to update:' ),
					'theme'       => __( 'The following themes failed to update:' ),
					'translation' => __( 'The following translations failed to update:' ),
				);

				$body[] = $messages[ $type ];

				foreach ( $this->update_results[ $type ] as $item ) {
					if ( ! $item->result || is_wp_error( $item->result ) ) {
						/* translators: %s: Name of plugin / theme / translation. */
						$body[] = ' * ' . sprintf( __( 'FAILED: %s' ), $item->name );
						++$failures;
					}
				}
			}

			$body[] = '';
		}

		if ( '' !== get_bloginfo( 'name' ) ) {
			$site_title = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		} else {
			$site_title = parse_url( home_url(), PHP_URL_HOST );
		}

		if ( $failures ) {
			$body[] = trim(
				__(
					"BETA TESTING?
=============

This debugging email is sent when you are using a development version of WordPress.

If you think these failures might be due to a bug in WordPress, could you report it?
 * Open a thread in the support forums: https://wordpress.org/support/forum/alphabeta
 * Or, if you're comfortable writing a bug report: https://core.trac.wordpress.org/

Thanks! -- The WordPress Team"
				)
			);
			$body[] = '';

			/* translators: Background update failed notification email subject. %s: Site title. */
			$subject = sprintf( __( '[%s] Background Update Failed' ), $site_title );
		} else {
			/* translators: Background update finished notification email subject. %s: Site title. */
			$subject = sprintf( __( '[%s] Background Update Finished' ), $site_title );
		}

		$body[] = trim(
			__(
				'UPDATE LOG
=========='
			)
		);
		$body[] = '';

		foreach ( array( 'core', 'plugin', 'theme', 'translation' ) as $type ) {
			if ( ! isset( $this->update_results[ $type ] ) ) {
				continue;
			}

			foreach ( $this->update_results[ $type ] as $update ) {
				$body[] = $update->name;
				$body[] = str_repeat( '-', strlen( $update->name ) );

				foreach ( $update->messages as $message ) {
					$body[] = '  ' . html_entity_decode( str_replace( '&#8230;', '...', $message ) );
				}

				if ( is_wp_error( $update->result ) ) {
					$results = array( 'update' => $update->result );

					// If we rolled back, we want to know an error that occurred then too.
					if ( 'rollback_was_required' === $update->result->get_error_code() ) {
						$results = (array) $update->result->get_error_data();
					}

					foreach ( $results as $result_type => $result ) {
						if ( ! is_wp_error( $result ) ) {
							continue;
						}

						if ( 'rollback' === $result_type ) {
							/* translators: 1: Error code, 2: Error message. */
							$body[] = '  ' . sprintf( __( 'Rollback Error: [%1$s] %2$s' ), $result->get_error_code(), $result->get_error_message() );
						} else {
							/* translators: 1: Error code, 2: Error message. */
							$body[] = '  ' . sprintf( __( 'Error: [%1$s] %2$s' ), $result->get_error_code(), $result->get_error_message() );
						}

						if ( $result->get_error_data() ) {
							$body[] = '         ' . implode( ', ', (array) $result->get_error_data() );
						}
					}
				}

				$body[] = '';
			}
		}

		$email = array(
			'to'      => get_site_option( 'admin_email' ),
			'subject' => $subject,
			'body'    => implode( "\n", $body ),
			'headers' => '',
		);

		/**
		 * Filters the debug email that can be sent following an automatic
		 * background core update.
		 *
		 * @since 3.8.0
		 *
		 * @param array $email {
		 *     Array of email arguments that will be passed to wp_mail().
		 *
		 *     @type string $to      The email recipient. An array of emails
		 *                           can be returned, as handled by wp_mail().
		 *     @type string $subject Email subject.
		 *     @type string $body    Email message body.
		 *     @type string $headers Any email headers. Default empty.
		 * }
		 * @param int   $failures The number of failures encountered while upgrading.
		 * @param mixed $results  The results of all attempted updates.
		 */
		$email = apply_filters( 'automatic_updates_debug_email', $email, $failures, $this->update_results );

		wp_mail( $email['to'], wp_specialchars_decode( $email['subject'] ), $email['body'], $email['headers'] );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	/**
	 * Performs a loopback request to check for potential fatal errors.
	 *
	 * Fatal errors cannot be detected unless maintenance mode is enabled.
	 *
	 * @since 6.6.0
	 *
	 * @global int $upgrading The Unix timestamp marking when upgrading WordPress began.
	 *
	 * @return bool Whether a fatal error was detected.
	 */
	protected function has_fatal_error() {
		global $upgrading;

		$maintenance_file = ABSPATH . '.maintenance';
		if ( ! file_exists( $maintenance_file ) ) {
			return false;
		}

		require $maintenance_file;
		if ( ! is_int( $upgrading ) ) {
			return false;
		}

		$scrape_key   = md5( $upgrading );
		$scrape_nonce = (string) $upgrading;
		$transient    = 'scrape_key_' . $scrape_key;
		set_transient( $transient, $scrape_nonce, 30 );

		$cookies       = wp_unslash( $_COOKIE );
		$scrape_params = array(
			'wp_scrape_key'   => $scrape_key,
			'wp_scrape_nonce' => $scrape_nonce,
		);
		$headers       = array(
			'Cache-Control' => 'no-cache',
		);

		/** This filter is documented in wp-includes/class-wp-http-streams.php */
		$sslverify = apply_filters( 'https_local_ssl_verify', false );

		// Include Basic auth in the loopback request.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . wp_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		// Time to wait for loopback request to finish.
		$timeout = 50; // 50 seconds.

		$is_debug = WP_DEBUG && WP_DEBUG_LOG;
		if ( $is_debug ) {
			error_log( '    Scraping home page...' );
		}

		$needle_start = "###### wp_scraping_result_start:$scrape_key ######";
		$needle_end   = "###### wp_scraping_result_end:$scrape_key ######";
		$url          = add_query_arg( $scrape_params, home_url( '/' ) );
		$response     = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout', 'sslverify' ) );

		if ( is_wp_error( $response ) ) {
			if ( $is_debug ) {
				error_log( 'Loopback request failed: ' . $response->get_error_message() );
			}
			return true;
		}

		// If this outputs `true` in the log, it means there were no fatal errors detected.
		if ( $is_debug ) {
			error_log( var_export( substr( $response['body'], strpos( $response['body'], '###### wp_scraping_result_start:' ) ), true ) );
		}

		$body                   = wp_remote_retrieve_body( $response );
		$scrape_result_position = strpos( $body, $needle_start );
		$result                 = null;

		if ( false !== $scrape_result_position ) {
			$error_output = substr( $body, $scrape_result_position + strlen( $needle_start ) );
			$error_output = substr( $error_output, 0, strpos( $error_output, $needle_end ) );
			$result       = json_decode( trim( $error_output ), true );
		}

		delete_transient( $transient );

		// Only fatal errors will result in a 'type' key.
		return isset( $result['type'] );
	}
}
