<?php
/**
 * Plugins administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'activate_plugins' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage plugins for this site.' ) );
}

$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
$pagenum       = $wp_list_table->get_pagenum();

$action = $wp_list_table->current_action();

$plugin = isset( $_REQUEST['plugin'] ) ? wp_unslash( $_REQUEST['plugin'] ) : '';
$s      = isset( $_REQUEST['s'] ) ? urlencode( wp_unslash( $_REQUEST['s'] ) ) : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$query_args_to_remove = array(
	'error',
	'deleted',
	'activate',
	'activate-multi',
	'deactivate',
	'deactivate-multi',
	'enabled-auto-update',
	'disabled-auto-update',
	'enabled-auto-update-multi',
	'disabled-auto-update-multi',
	'_error_nonce',
);

$_SERVER['REQUEST_URI'] = remove_query_arg( $query_args_to_remove, $_SERVER['REQUEST_URI'] );

wp_enqueue_script( 'updates' );

if ( $action ) {

	switch ( $action ) {
		case 'activate':
			if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
				wp_die( __( 'Sorry, you are not allowed to activate this plugin.' ) );
			}

			if ( is_multisite() && ! is_network_admin() && is_network_only_plugin( $plugin ) ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			check_admin_referer( 'activate-plugin_' . $plugin );

			$result = activate_plugin( $plugin, self_admin_url( 'plugins.php?error=true&plugin=' . urlencode( $plugin ) ), is_network_admin() );
			if ( is_wp_error( $result ) ) {
				if ( 'unexpected_output' === $result->get_error_code() ) {
					$redirect = self_admin_url( 'plugins.php?error=true&charsout=' . strlen( $result->get_error_data() ) . '&plugin=' . urlencode( $plugin ) . "&plugin_status=$status&paged=$page&s=$s" );
					wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) );
					exit;
				} else {
					wp_die( $result );
				}
			}

			if ( ! is_network_admin() ) {
				$recent = (array) get_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_option( 'recently_activated', $recent );
			} else {
				$recent = (array) get_site_option( 'recently_activated' );
				unset( $recent[ $plugin ] );
				update_site_option( 'recently_activated', $recent );
			}

			if ( isset( $_GET['from'] ) && 'import' === $_GET['from'] ) {
				// Overrides the ?error=true one above and redirects to the Imports page, stripping the -importer suffix.
				wp_redirect( self_admin_url( 'import.php?import=' . str_replace( '-importer', '', dirname( $plugin ) ) ) );
			} elseif ( isset( $_GET['from'] ) && 'press-this' === $_GET['from'] ) {
				wp_redirect( self_admin_url( 'press-this.php' ) );
			} else {
				// Overrides the ?error=true one above.
				wp_redirect( self_admin_url( "plugins.php?activate=true&plugin_status=$status&paged=$page&s=$s" ) );
			}
			exit;

		case 'activate-selected':
			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_die( __( 'Sorry, you are not allowed to activate plugins for this site.' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			$plugins = isset( $_POST['checked'] ) ? (array) wp_unslash( $_POST['checked'] ) : array();

			if ( is_network_admin() ) {
				foreach ( $plugins as $i => $plugin ) {
					// Only activate plugins which are not already network activated.
					if ( is_plugin_active_for_network( $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			} else {
				foreach ( $plugins as $i => $plugin ) {
					// Only activate plugins which are not already active and are not network-only when on Multisite.
					if ( is_plugin_active( $plugin ) || ( is_multisite() && is_network_only_plugin( $plugin ) ) ) {
						unset( $plugins[ $i ] );
					}
					// Only activate plugins which the user can activate.
					if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			}

			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			activate_plugins( $plugins, self_admin_url( 'plugins.php?error=true' ), is_network_admin() );

			if ( ! is_network_admin() ) {
				$recent = (array) get_option( 'recently_activated' );
			} else {
				$recent = (array) get_site_option( 'recently_activated' );
			}

			foreach ( $plugins as $plugin ) {
				unset( $recent[ $plugin ] );
			}

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', $recent );
			} else {
				update_site_option( 'recently_activated', $recent );
			}

			wp_redirect( self_admin_url( "plugins.php?activate-multi=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;

		case 'update-selected':
			check_admin_referer( 'bulk-plugins' );

			if ( isset( $_GET['plugins'] ) ) {
				$plugins = explode( ',', wp_unslash( $_GET['plugins'] ) );
			} elseif ( isset( $_POST['checked'] ) ) {
				$plugins = (array) wp_unslash( $_POST['checked'] );
			} else {
				$plugins = array();
			}

			// Used in the HTML title tag.
			$title       = __( 'Update Plugins' );
			$parent_file = 'plugins.php';

			wp_enqueue_script( 'updates' );
			require_once ABSPATH . 'wp-admin/admin-header.php';

			echo '<div class="wrap">';
			echo '<h1>' . esc_html( $title ) . '</h1>';

			$url = self_admin_url( 'update.php?action=update-selected&amp;plugins=' . urlencode( implode( ',', $plugins ) ) );
			$url = wp_nonce_url( $url, 'bulk-update-plugins' );

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once ABSPATH . 'wp-admin/admin-footer.php';
			exit;

		case 'error_scrape':
			if ( ! current_user_can( 'activate_plugin', $plugin ) ) {
				wp_die( __( 'Sorry, you are not allowed to activate this plugin.' ) );
			}

			check_admin_referer( 'plugin-activation-error_' . $plugin );

			$valid = validate_plugin( $plugin );
			if ( is_wp_error( $valid ) ) {
				wp_die( $valid );
			}

			if ( ! WP_DEBUG ) {
				error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
			}

			ini_set( 'display_errors', true ); // Ensure that fatal errors are displayed.
			// Go back to "sandbox" scope so we get the same errors as before.
			plugin_sandbox_scrape( $plugin );
			/** This action is documented in wp-admin/includes/plugin.php */
			do_action( "activate_{$plugin}" );
			exit;

		case 'deactivate':
			if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
				wp_die( __( 'Sorry, you are not allowed to deactivate this plugin.' ) );
			}

			check_admin_referer( 'deactivate-plugin_' . $plugin );

			if ( ! is_network_admin() && is_plugin_active_for_network( $plugin ) ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			deactivate_plugins( $plugin, false, is_network_admin() );

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );
			} else {
				update_site_option( 'recently_activated', array( $plugin => time() ) + (array) get_site_option( 'recently_activated' ) );
			}

			if ( headers_sent() ) {
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s" ) . "' />";
			} else {
				wp_redirect( self_admin_url( "plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s" ) );
			}
			exit;

		case 'deactivate-selected':
			if ( ! current_user_can( 'deactivate_plugins' ) ) {
				wp_die( __( 'Sorry, you are not allowed to deactivate plugins for this site.' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			$plugins = isset( $_POST['checked'] ) ? (array) wp_unslash( $_POST['checked'] ) : array();
			// Do not deactivate plugins which are already deactivated.
			if ( is_network_admin() ) {
				$plugins = array_filter( $plugins, 'is_plugin_active_for_network' );
			} else {
				$plugins = array_filter( $plugins, 'is_plugin_active' );
				$plugins = array_diff( $plugins, array_filter( $plugins, 'is_plugin_active_for_network' ) );

				foreach ( $plugins as $i => $plugin ) {
					// Only deactivate plugins which the user can deactivate.
					if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
						unset( $plugins[ $i ] );
					}
				}
			}
			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			deactivate_plugins( $plugins, false, is_network_admin() );

			$deactivated = array();
			foreach ( $plugins as $plugin ) {
				$deactivated[ $plugin ] = time();
			}

			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', $deactivated + (array) get_option( 'recently_activated' ) );
			} else {
				update_site_option( 'recently_activated', $deactivated + (array) get_site_option( 'recently_activated' ) );
			}

			wp_redirect( self_admin_url( "plugins.php?deactivate-multi=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;

		case 'delete-selected':
			if ( ! current_user_can( 'delete_plugins' ) ) {
				wp_die( __( 'Sorry, you are not allowed to delete plugins for this site.' ) );
			}

			check_admin_referer( 'bulk-plugins' );

			// $_POST = from the plugin form; $_GET = from the FTP details screen.
			$plugins = isset( $_REQUEST['checked'] ) ? (array) wp_unslash( $_REQUEST['checked'] ) : array();
			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			$plugins = array_filter( $plugins, 'is_plugin_inactive' ); // Do not allow to delete activated plugins.
			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url( "plugins.php?error=true&main=true&plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			// Bail on all if any paths are invalid.
			// validate_file() returns truthy for invalid files.
			$invalid_plugin_files = array_filter( $plugins, 'validate_file' );
			if ( $invalid_plugin_files ) {
				wp_redirect( self_admin_url( "plugins.php?plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			require ABSPATH . 'wp-admin/update.php';

			$parent_file = 'plugins.php';

			if ( ! isset( $_REQUEST['verify-delete'] ) ) {
				wp_enqueue_script( 'jquery' );
				require_once ABSPATH . 'wp-admin/admin-header.php';

				?>
				<div class="wrap">
				<?php

				$plugin_info              = array();
				$have_non_network_plugins = false;

				foreach ( (array) $plugins as $plugin ) {
					$plugin_slug = dirname( $plugin );

					if ( '.' === $plugin_slug ) {
						$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						if ( $data ) {
							$plugin_info[ $plugin ]                     = $data;
							$plugin_info[ $plugin ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
							if ( ! $plugin_info[ $plugin ]['Network'] ) {
								$have_non_network_plugins = true;
							}
						}
					} else {
						// Get plugins list from that folder.
						$folder_plugins = get_plugins( '/' . $plugin_slug );
						if ( $folder_plugins ) {
							foreach ( $folder_plugins as $plugin_file => $data ) {
								$plugin_info[ $plugin_file ]                     = _get_plugin_data_markup_translate( $plugin_file, $data );
								$plugin_info[ $plugin_file ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
								if ( ! $plugin_info[ $plugin_file ]['Network'] ) {
									$have_non_network_plugins = true;
								}
							}
						}
					}
				}

				$plugins_to_delete = count( $plugin_info );

				?>
				<?php if ( 1 === $plugins_to_delete ) : ?>
					<h1><?php _e( 'Delete Plugin' ); ?></h1>
					<?php if ( $have_non_network_plugins && is_network_admin() ) : ?>
						<div class="error"><p><strong><?php _e( 'Caution:' ); ?></strong> <?php _e( 'This plugin may be active on other sites in the network.' ); ?></p></div>
					<?php endif; ?>
					<p><?php _e( 'You are about to remove the following plugin:' ); ?></p>
				<?php else : ?>
					<h1><?php _e( 'Delete Plugins' ); ?></h1>
					<?php if ( $have_non_network_plugins && is_network_admin() ) : ?>
						<div class="error"><p><strong><?php _e( 'Caution:' ); ?></strong> <?php _e( 'These plugins may be active on other sites in the network.' ); ?></p></div>
					<?php endif; ?>
					<p><?php _e( 'You are about to remove the following plugins:' ); ?></p>
				<?php endif; ?>
					<ul class="ul-disc">
						<?php

						$data_to_delete = false;

						foreach ( $plugin_info as $plugin ) {
							if ( $plugin['is_uninstallable'] ) {
								/* translators: 1: Plugin name, 2: Plugin author. */
								echo '<li>', sprintf( __( '%1$s by %2$s (will also <strong>delete its data</strong>)' ), '<strong>' . $plugin['Name'] . '</strong>', '<em>' . $plugin['AuthorName'] . '</em>' ), '</li>';
								$data_to_delete = true;
							} else {
								/* translators: 1: Plugin name, 2: Plugin author. */
								echo '<li>', sprintf( _x( '%1$s by %2$s', 'plugin' ), '<strong>' . $plugin['Name'] . '</strong>', '<em>' . $plugin['AuthorName'] ) . '</em>', '</li>';
							}
						}

						?>
					</ul>
				<p>
				<?php

				if ( $data_to_delete ) {
					_e( 'Are you sure you want to delete these files and data?' );
				} else {
					_e( 'Are you sure you want to delete these files?' );
				}

				?>
				</p>
				<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php

					foreach ( (array) $plugins as $plugin ) {
						echo '<input type="hidden" name="checked[]" value="' . esc_attr( $plugin ) . '" />';
					}

					?>
					<?php wp_nonce_field( 'bulk-plugins' ); ?>
					<?php submit_button( $data_to_delete ? __( 'Yes, delete these files and data' ) : __( 'Yes, delete these files' ), '', 'submit', false ); ?>
				</form>
				<?php

				$referer = wp_get_referer();

				?>
				<form method="post" action="<?php echo $referer ? esc_url( $referer ) : ''; ?>" style="display:inline;">
					<?php submit_button( __( 'No, return me to the plugin list' ), '', 'submit', false ); ?>
				</form>
				</div>
				<?php

				require_once ABSPATH . 'wp-admin/admin-footer.php';
				exit;
			} else {
				$plugins_to_delete = count( $plugins );
			} // End if verify-delete.

			$delete_result = delete_plugins( $plugins );

			// Store the result in a cache rather than a URL param due to object type & length.
			set_transient( 'plugins_delete_result_' . $user_ID, $delete_result );
			wp_redirect( self_admin_url( "plugins.php?deleted=$plugins_to_delete&plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		case 'clear-recent-list':
			if ( ! is_network_admin() ) {
				update_option( 'recently_activated', array() );
			} else {
				update_site_option( 'recently_activated', array() );
			}

			break;
		case 'resume':
			if ( is_multisite() ) {
				return;
			}

			if ( ! current_user_can( 'resume_plugin', $plugin ) ) {
				wp_die( __( 'Sorry, you are not allowed to resume this plugin.' ) );
			}

			check_admin_referer( 'resume-plugin_' . $plugin );

			$result = resume_plugin( $plugin, self_admin_url( "plugins.php?error=resuming&plugin_status=$status&paged=$page&s=$s" ) );

			if ( is_wp_error( $result ) ) {
				wp_die( $result );
			}

			wp_redirect( self_admin_url( "plugins.php?resume=true&plugin_status=$status&paged=$page&s=$s" ) );
			exit;
		case 'enable-auto-update':
		case 'disable-auto-update':
		case 'enable-auto-update-selected':
		case 'disable-auto-update-selected':
			if ( ! current_user_can( 'update_plugins' ) || ! wp_is_auto_update_enabled_for_type( 'plugin' ) ) {
				wp_die( __( 'Sorry, you are not allowed to manage plugins automatic updates.' ) );
			}

			if ( is_multisite() && ! is_network_admin() ) {
				wp_die( __( 'Please connect to your network admin to manage plugins automatic updates.' ) );
			}

			$redirect = self_admin_url( "plugins.php?plugin_status={$status}&paged={$page}&s={$s}" );

			if ( 'enable-auto-update' === $action || 'disable-auto-update' === $action ) {
				if ( empty( $plugin ) ) {
					wp_redirect( $redirect );
					exit;
				}

				check_admin_referer( 'updates' );
			} else {
				if ( empty( $_POST['checked'] ) ) {
					wp_redirect( $redirect );
					exit;
				}

				check_admin_referer( 'bulk-plugins' );
			}

			$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

			if ( 'enable-auto-update' === $action ) {
				$auto_updates[] = $plugin;
				$auto_updates   = array_unique( $auto_updates );
				$redirect       = add_query_arg( array( 'enabled-auto-update' => 'true' ), $redirect );
			} elseif ( 'disable-auto-update' === $action ) {
				$auto_updates = array_diff( $auto_updates, array( $plugin ) );
				$redirect     = add_query_arg( array( 'disabled-auto-update' => 'true' ), $redirect );
			} else {
				$plugins = (array) wp_unslash( $_POST['checked'] );

				if ( 'enable-auto-update-selected' === $action ) {
					$new_auto_updates = array_merge( $auto_updates, $plugins );
					$new_auto_updates = array_unique( $new_auto_updates );
					$query_args       = array( 'enabled-auto-update-multi' => 'true' );
				} else {
					$new_auto_updates = array_diff( $auto_updates, $plugins );
					$query_args       = array( 'disabled-auto-update-multi' => 'true' );
				}

				// Return early if all selected plugins already have auto-updates enabled or disabled.
				// Must use non-strict comparison, so that array order is not treated as significant.
				if ( $new_auto_updates == $auto_updates ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					wp_redirect( $redirect );
					exit;
				}

				$auto_updates = $new_auto_updates;
				$redirect     = add_query_arg( $query_args, $redirect );
			}

			/** This filter is documented in wp-admin/includes/class-wp-plugins-list-table.php */
			$all_items = apply_filters( 'all_plugins', get_plugins() );

			// Remove plugins that don't exist or have been deleted since the option was last updated.
			$auto_updates = array_intersect( $auto_updates, array_keys( $all_items ) );

			update_site_option( 'auto_update_plugins', $auto_updates );

			wp_redirect( $redirect );
			exit;
		default:
			if ( isset( $_POST['checked'] ) ) {
				check_admin_referer( 'bulk-plugins' );

				$screen   = get_current_screen()->id;
				$sendback = wp_get_referer();
				$plugins  = isset( $_POST['checked'] ) ? (array) wp_unslash( $_POST['checked'] ) : array();

				/** This action is documented in wp-admin/edit.php */
				$sendback = apply_filters( "handle_bulk_actions-{$screen}", $sendback, $action, $plugins ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
				wp_safe_redirect( $sendback );
				exit;
			}
			break;
	}
}

$wp_list_table->prepare_items();

wp_enqueue_script( 'plugin-install' );
add_thickbox();

add_screen_option( 'per_page', array( 'default' => 999 ) );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
				'<p>' . __( 'Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.' ) . '</p>' .
				'<p>' . __( 'The search for installed plugins will search for terms in their name, description, or author.' ) . ' <span id="live-search-desc" class="hide-if-no-js">' . __( 'The search results will be updated as you type.' ) . '</span></p>' .
				'<p>' . sprintf(
					/* translators: %s: WordPress Plugin Directory URL. */
					__( 'If you would like to see more plugins to choose from, click on the &#8220;Add New&#8221; button and you will be able to browse or search for additional plugins from the <a href="%s">WordPress Plugin Directory</a>. Plugins in the WordPress Plugin Directory are designed and developed by third parties, and are compatible with the license WordPress uses. Oh, and they&#8217;re free!' ),
					__( 'https://wordpress.org/plugins/' )
				) . '</p>',
	)
);
get_current_screen()->add_help_tab(
	array(
		'id'      => 'compatibility-problems',
		'title'   => __( 'Troubleshooting' ),
		'content' =>
				'<p>' . __( 'Most of the time, plugins play nicely with the core of WordPress and with other plugins. Sometimes, though, a plugin&#8217;s code will get in the way of another plugin, causing compatibility issues. If your site starts doing strange things, this may be the problem. Try deactivating all your plugins and re-activating them in various combinations until you isolate which one(s) caused the issue.' ) . '</p>' .
				'<p>' . sprintf(
					/* translators: %s: WP_PLUGIN_DIR constant value. */
					__( 'If something goes wrong with a plugin and you cannot use WordPress, delete or rename that file in the %s directory and it will be automatically deactivated.' ),
					'<code>' . WP_PLUGIN_DIR . '</code>'
				) . '</p>',
	)
);

$help_sidebar_autoupdates = '';

if ( current_user_can( 'update_plugins' ) && wp_is_auto_update_enabled_for_type( 'plugin' ) ) {
	get_current_screen()->add_help_tab(
		array(
			'id'      => 'plugins-themes-auto-updates',
			'title'   => __( 'Auto-updates' ),
			'content' =>
					'<p>' . __( 'Auto-updates can be enabled or disabled for each individual plugin. Plugins with auto-updates enabled will display the estimated date of the next auto-update. Auto-updates depends on the WP-Cron task scheduling system.' ) . '</p>' .
					'<p>' . __( 'Auto-updates are only available for plugins recognized by WordPress.org, or that include a compatible update system.' ) . '</p>' .
					'<p>' . __( 'Please note: Third-party themes and plugins, or custom code, may override WordPress scheduling.' ) . '</p>',
		)
	);

	$help_sidebar_autoupdates = '<p>' . __( '<a href="https://wordpress.org/support/article/plugins-themes-auto-updates/">Documentation on Auto-updates</a>' ) . '</p>';
}

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/managing-plugins/">Documentation on Managing Plugins</a>' ) . '</p>' .
	$help_sidebar_autoupdates .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

get_current_screen()->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter plugins list' ),
		'heading_pagination' => __( 'Plugins list navigation' ),
		'heading_list'       => __( 'Plugins list' ),
	)
);

// Used in the HTML title tag.
$title       = __( 'Plugins' );
$parent_file = 'plugins.php';

require_once ABSPATH . 'wp-admin/admin-header.php';

$invalid = validate_active_plugins();
if ( ! empty( $invalid ) ) {
	foreach ( $invalid as $plugin_file => $error ) {
		echo '<div id="message" class="error"><p>';
		printf(
			/* translators: 1: Plugin file, 2: Error message. */
			__( 'The plugin %1$s has been deactivated due to an error: %2$s' ),
			'<code>' . esc_html( $plugin_file ) . '</code>',
			esc_html( $error->get_error_message() )
		);
		echo '</p></div>';
	}
}

if ( isset( $_GET['error'] ) ) :

	if ( isset( $_GET['main'] ) ) {
		$errmsg = __( 'You cannot delete a plugin while it is active on the main site.' );
	} elseif ( isset( $_GET['charsout'] ) ) {
		$errmsg = sprintf(
			/* translators: %d: Number of characters. */
			_n(
				'The plugin generated %d character of <strong>unexpected output</strong> during activation.',
				'The plugin generated %d characters of <strong>unexpected output</strong> during activation.',
				$_GET['charsout']
			),
			$_GET['charsout']
		);
		$errmsg .= ' ' . __( 'If you notice &#8220;headers already sent&#8221; messages, problems with syndication feeds or other issues, try deactivating or removing this plugin.' );
	} elseif ( 'resuming' === $_GET['error'] ) {
		$errmsg = __( 'Plugin could not be resumed because it triggered a <strong>fatal error</strong>.' );
	} else {
		$errmsg = __( 'Plugin could not be activated because it triggered a <strong>fatal error</strong>.' );
	}

	?>
	<div id="message" class="error"><p><?php echo $errmsg; ?></p>
	<?php

	if ( ! isset( $_GET['main'] ) && ! isset( $_GET['charsout'] )
		&& isset( $_GET['_error_nonce'] ) && wp_verify_nonce( $_GET['_error_nonce'], 'plugin-activation-error_' . $plugin )
	) {
		$iframe_url = add_query_arg(
			array(
				'action'   => 'error_scrape',
				'plugin'   => urlencode( $plugin ),
				'_wpnonce' => urlencode( $_GET['_error_nonce'] ),
			),
			admin_url( 'plugins.php' )
		);

		?>
		<iframe style="border:0" width="100%" height="70px" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
		<?php
	}

	?>
	</div>
	<?php
elseif ( isset( $_GET['deleted'] ) ) :
	$delete_result = get_transient( 'plugins_delete_result_' . $user_ID );
	// Delete it once we're done.
	delete_transient( 'plugins_delete_result_' . $user_ID );

	if ( is_wp_error( $delete_result ) ) :
		?>
		<div id="message" class="error notice is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: Error message. */
					__( 'Plugin could not be deleted due to an error: %s' ),
					esc_html( $delete_result->get_error_message() )
				);
				?>
			</p>
		</div>
		<?php else : ?>
		<div id="message" class="updated notice is-dismissible">
			<p>
				<?php
				if ( 1 === (int) $_GET['deleted'] ) {
					_e( 'The selected plugin has been deleted.' );
				} else {
					_e( 'The selected plugins have been deleted.' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>
<?php elseif ( isset( $_GET['activate'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin activated.' ); ?></p></div>
<?php elseif ( isset( $_GET['activate-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Selected plugins activated.' ); ?></p></div>
<?php elseif ( isset( $_GET['deactivate'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin deactivated.' ); ?></p></div>
<?php elseif ( isset( $_GET['deactivate-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Selected plugins deactivated.' ); ?></p></div>
<?php elseif ( 'update-selected' === $action ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'All selected plugins are up to date.' ); ?></p></div>
<?php elseif ( isset( $_GET['resume'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin resumed.' ); ?></p></div>
<?php elseif ( isset( $_GET['enabled-auto-update'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin will be auto-updated.' ); ?></p></div>
<?php elseif ( isset( $_GET['disabled-auto-update'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Plugin will no longer be auto-updated.' ); ?></p></div>
<?php elseif ( isset( $_GET['enabled-auto-update-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Selected plugins will be auto-updated.' ); ?></p></div>
<?php elseif ( isset( $_GET['disabled-auto-update-multi'] ) ) : ?>
	<div id="message" class="updated notice is-dismissible"><p><?php _e( 'Selected plugins will no longer be auto-updated.' ); ?></p></div>
<?php endif; ?>

<div class="wrap">
<h1 class="wp-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<?php
if ( ( ! is_multisite() || is_network_admin() ) && current_user_can( 'install_plugins' ) ) {
	?>
	<a href="<?php echo esc_url( self_admin_url( 'plugin-install.php' ) ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'plugin' ); ?></a>
	<?php
}

if ( strlen( $s ) ) {
	echo '<span class="subtitle">';
	printf(
		/* translators: %s: Search query. */
		__( 'Search results for: %s' ),
		'<strong>' . esc_html( urldecode( $s ) ) . '</strong>'
	);
	echo '</span>';
}
?>

<hr class="wp-header-end">

<?php
/**
 * Fires before the plugins list table is rendered.
 *
 * This hook also fires before the plugins list table is rendered in the Network Admin.
 *
 * Please note: The 'active' portion of the hook name does not refer to whether the current
 * view is for active plugins, but rather all plugins actively-installed.
 *
 * @since 3.0.0
 *
 * @param array[] $plugins_all An array of arrays containing information on all installed plugins.
 */
do_action( 'pre_current_active_plugins', $plugins['all'] );
?>

<?php $wp_list_table->views(); ?>

<form class="search-form search-plugins" method="get">
<?php $wp_list_table->search_box( __( 'Search Installed Plugins' ), 'plugin' ); ?>
</form>

<form method="post" id="bulk-action-form">

<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $status ); ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr( $page ); ?>" />

<?php $wp_list_table->display(); ?>
</form>

	<span class="spinner"></span>
</div>

<?php
wp_print_request_filesystem_credentials_modal();
wp_print_admin_notice_templates();
wp_print_update_row_templates();

require_once ABSPATH . 'wp-admin/admin-footer.php';
