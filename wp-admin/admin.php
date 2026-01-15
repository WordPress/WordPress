<?php
/**
 * WordPress Administration Bootstrap
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * In WordPress Administration Screens
 *
 * @since 2.3.2
 */
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', true );
}

if ( ! defined( 'WP_NETWORK_ADMIN' ) ) {
	define( 'WP_NETWORK_ADMIN', false );
}

if ( ! defined( 'WP_USER_ADMIN' ) ) {
	define( 'WP_USER_ADMIN', false );
}

if ( ! WP_NETWORK_ADMIN && ! WP_USER_ADMIN ) {
	define( 'WP_BLOG_ADMIN', true );
}

if ( isset( $_GET['import'] ) && ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	define( 'WP_LOAD_IMPORTERS', true );
}

/** Load WordPress Bootstrap */
require_once dirname( __DIR__ ) . '/wp-load.php';

nocache_headers();

if ( get_option( 'db_upgraded' ) ) {

	flush_rewrite_rules();
	update_option( 'db_upgraded', false, true );

	/**
	 * Fires on the next page load after a successful DB upgrade.
	 *
	 * @since 2.8.0
	 */
	do_action( 'after_db_upgrade' );

} elseif ( ! wp_doing_ajax() && empty( $_POST )
	&& (int) get_option( 'db_version' ) !== $wp_db_version
) {

	if ( ! is_multisite() ) {
		wp_redirect( admin_url( 'upgrade.php?_wp_http_referer=' . urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
		exit;
	}

	/**
	 * Filters whether to attempt to perform the multisite DB upgrade routine.
	 *
	 * In single site, the user would be redirected to wp-admin/upgrade.php.
	 * In multisite, the DB upgrade routine is automatically fired, but only
	 * when this filter returns true.
	 *
	 * If the network is 50 sites or less, it will run every time. Otherwise,
	 * it will throttle itself to reduce load.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param bool $do_mu_upgrade Whether to perform the Multisite upgrade routine. Default true.
	 */
	if ( apply_filters( 'do_mu_upgrade', true ) ) {
		$blog_count = get_blog_count();

		/*
		 * If there are 50 or fewer sites, run every time. Otherwise, throttle to reduce load:
		 * attempt to do no more than threshold value, with some +/- allowed.
		 */
		if ( $blog_count <= 50 || ( $blog_count > 50 && mt_rand( 0, (int) ( $blog_count / 50 ) ) === 1 ) ) {
			require_once ABSPATH . WPINC . '/http.php';

			$response = wp_remote_get(
				admin_url( 'upgrade.php?step=1' ),
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
				)
			);

			/** This action is documented in wp-admin/network/upgrade.php */
			do_action( 'after_mu_upgrade', $response );

			unset( $response );
		}

		unset( $blog_count );
	}
}

require_once ABSPATH . 'wp-admin/includes/admin.php';

auth_redirect();

// Schedule Trash collection.
if ( ! wp_next_scheduled( 'wp_scheduled_delete' ) && ! wp_installing() ) {
	wp_schedule_event( time(), 'daily', 'wp_scheduled_delete' );
}

// Schedule transient cleanup.
if ( ! wp_next_scheduled( 'delete_expired_transients' ) && ! wp_installing() ) {
	wp_schedule_event( time(), 'daily', 'delete_expired_transients' );
}

set_screen_options();

$date_format = __( 'F j, Y' );
$time_format = __( 'g:i a' );

wp_enqueue_script( 'common' );

/**
 * $pagenow is set in vars.php.
 * $wp_importers is sometimes set in wp-admin/includes/import.php.
 * The remaining variables are imported as globals elsewhere, declared as globals here.
 *
 * @global string $pagenow      The filename of the current screen.
 * @global array  $wp_importers
 * @global string $hook_suffix
 * @global string $plugin_page
 * @global string $typenow      The post type of the current screen.
 * @global string $taxnow       The taxonomy of the current screen.
 */
global $pagenow, $wp_importers, $hook_suffix, $plugin_page, $typenow, $taxnow;

$page_hook = null;

$editing = false;

if ( isset( $_GET['page'] ) ) {
	$plugin_page = wp_unslash( $_GET['page'] );
	$plugin_page = plugin_basename( $plugin_page );
}

if ( isset( $_REQUEST['post_type'] ) && post_type_exists( $_REQUEST['post_type'] ) ) {
	$typenow = $_REQUEST['post_type'];
} else {
	$typenow = '';
}

if ( isset( $_REQUEST['taxonomy'] ) && taxonomy_exists( $_REQUEST['taxonomy'] ) ) {
	$taxnow = $_REQUEST['taxonomy'];
} else {
	$taxnow = '';
}

if ( WP_NETWORK_ADMIN ) {
	require ABSPATH . 'wp-admin/network/menu.php';
} elseif ( WP_USER_ADMIN ) {
	require ABSPATH . 'wp-admin/user/menu.php';
} else {
	require ABSPATH . 'wp-admin/menu.php';
}

if ( current_user_can( 'manage_options' ) ) {
	wp_raise_memory_limit( 'admin' );
}

/**
 * Fires as an admin screen or script is being initialized.
 *
 * Note, this does not just run on user-facing admin screens.
 * It runs on admin-ajax.php and admin-post.php as well.
 *
 * This is roughly analogous to the more general {@see 'init'} hook, which fires earlier.
 *
 * @since 2.5.0
 */
do_action( 'admin_init' );

if ( isset( $plugin_page ) ) {
	if ( ! empty( $typenow ) ) {
		$the_parent = $pagenow . '?post_type=' . $typenow;
	} else {
		$the_parent = $pagenow;
	}

	$page_hook = get_plugin_page_hook( $plugin_page, $the_parent );
	if ( ! $page_hook ) {
		$page_hook = get_plugin_page_hook( $plugin_page, $plugin_page );

		// Back-compat for plugins using add_management_page().
		if ( empty( $page_hook ) && 'edit.php' === $pagenow && get_plugin_page_hook( $plugin_page, 'tools.php' ) ) {
			// There could be plugin specific params on the URL, so we need the whole query string.
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$query_string = $_SERVER['QUERY_STRING'];
			} else {
				$query_string = 'page=' . $plugin_page;
			}
			wp_redirect( admin_url( 'tools.php?' . $query_string ) );
			exit;
		}
	}
	unset( $the_parent );
}

$hook_suffix = '';
if ( isset( $page_hook ) ) {
	$hook_suffix = $page_hook;
} elseif ( isset( $plugin_page ) ) {
	$hook_suffix = $plugin_page;
} elseif ( isset( $pagenow ) ) {
	$hook_suffix = $pagenow;
}

set_current_screen();

// Handle plugin admin pages.
if ( isset( $plugin_page ) ) {
	if ( $page_hook ) {
		/**
		 * Fires before a particular screen is loaded.
		 *
		 * The load-* hook fires in a number of contexts. This hook is for plugin screens
		 * where a callback is provided when the screen is registered.
		 *
		 * The dynamic portion of the hook name, `$page_hook`, refers to a mixture of plugin
		 * page information including:
		 * 1. The page type. If the plugin page is registered as a submenu page, such as for
		 *    Settings, the page type would be 'settings'. Otherwise the type is 'toplevel'.
		 * 2. A separator of '_page_'.
		 * 3. The plugin basename minus the file extension.
		 *
		 * Together, the three parts form the `$page_hook`. Citing the example above,
		 * the hook name used would be 'load-settings_page_pluginbasename'.
		 *
		 * @see get_plugin_page_hook()
		 *
		 * @since 2.1.0
		 */
		do_action( "load-{$page_hook}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( ! isset( $_GET['noheader'] ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
		}

		/**
		 * Used to call the registered callback for a plugin screen.
		 *
		 * This hook uses a dynamic hook name, `$page_hook`, which refers to a mixture of plugin
		 * page information including:
		 * 1. The page type. If the plugin page is registered as a submenu page, such as for
		 *    Settings, the page type would be 'settings'. Otherwise the type is 'toplevel'.
		 * 2. A separator of '_page_'.
		 * 3. The plugin basename minus the file extension.
		 *
		 * Together, the three parts form the `$page_hook`. Citing the example above,
		 * the hook name used would be 'settings_page_pluginbasename'.
		 *
		 * @see get_plugin_page_hook()
		 *
		 * @since 1.5.0
		 */
		do_action( $page_hook );
	} else {
		if ( validate_file( $plugin_page ) ) {
			wp_die( __( 'Invalid plugin page.' ) );
		}

		if ( ! ( file_exists( WP_PLUGIN_DIR . "/$plugin_page" ) && is_file( WP_PLUGIN_DIR . "/$plugin_page" ) )
			&& ! ( file_exists( WPMU_PLUGIN_DIR . "/$plugin_page" ) && is_file( WPMU_PLUGIN_DIR . "/$plugin_page" ) )
		) {
			/* translators: %s: Admin page generated by a plugin. */
			wp_die( sprintf( __( 'Cannot load %s.' ), htmlentities( $plugin_page ) ) );
		}

		/**
		 * Fires before a particular screen is loaded.
		 *
		 * The load-* hook fires in a number of contexts. This hook is for plugin screens
		 * where the file to load is directly included, rather than the use of a function.
		 *
		 * The dynamic portion of the hook name, `$plugin_page`, refers to the plugin basename.
		 *
		 * @see plugin_basename()
		 *
		 * @since 1.5.0
		 */
		do_action( "load-{$plugin_page}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

		if ( ! isset( $_GET['noheader'] ) ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
		}

		if ( file_exists( WPMU_PLUGIN_DIR . "/$plugin_page" ) ) {
			include WPMU_PLUGIN_DIR . "/$plugin_page";
		} else {
			include WP_PLUGIN_DIR . "/$plugin_page";
		}
	}

	require_once ABSPATH . 'wp-admin/admin-footer.php';

	exit;
} elseif ( isset( $_GET['import'] ) ) {

	$importer = $_GET['import'];

	if ( ! current_user_can( 'import' ) ) {
		wp_die( __( 'Sorry, you are not allowed to import content into this site.' ) );
	}

	if ( validate_file( $importer ) ) {
		wp_redirect( admin_url( 'import.php?invalid=' . $importer ) );
		exit;
	}

	if ( ! isset( $wp_importers[ $importer ] ) || ! is_callable( $wp_importers[ $importer ][2] ) ) {
		wp_redirect( admin_url( 'import.php?invalid=' . $importer ) );
		exit;
	}

	/**
	 * Fires before an importer screen is loaded.
	 *
	 * The dynamic portion of the hook name, `$importer`, refers to the importer slug.
	 *
	 * Possible hook names include:
	 *
	 *  - `load-importer-blogger`
	 *  - `load-importer-wpcat2tag`
	 *  - `load-importer-livejournal`
	 *  - `load-importer-mt`
	 *  - `load-importer-rss`
	 *  - `load-importer-tumblr`
	 *  - `load-importer-wordpress`
	 *
	 * @since 3.5.0
	 */
	do_action( "load-importer-{$importer}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	// Used in the HTML title tag.
	$title        = __( 'Import' );
	$parent_file  = 'tools.php';
	$submenu_file = 'import.php';

	if ( ! isset( $_GET['noheader'] ) ) {
		require_once ABSPATH . 'wp-admin/admin-header.php';
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	define( 'WP_IMPORTING', true );

	/**
	 * Filters whether to filter imported data through kses on import.
	 *
	 * Multisite uses this hook to filter all data through kses by default,
	 * as a super administrator may be assisting an untrusted user.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $force Whether to force data to be filtered through kses. Default false.
	 */
	if ( apply_filters( 'force_filtered_html_on_import', false ) ) {
		kses_init_filters();  // Always filter imported data with kses on multisite.
	}

	call_user_func( $wp_importers[ $importer ][2] );

	require_once ABSPATH . 'wp-admin/admin-footer.php';

	// Make sure rules are flushed.
	flush_rewrite_rules( false );

	exit;
} else {
	/**
	 * Fires before a particular screen is loaded.
	 *
	 * The load-* hook fires in a number of contexts. This hook is for core screens.
	 *
	 * The dynamic portion of the hook name, `$pagenow`, is a global variable
	 * referring to the filename of the current screen, such as 'admin.php',
	 * 'post-new.php' etc. A complete hook for the latter would be
	 * 'load-post-new.php'.
	 *
	 * @since 2.1.0
	 */
	do_action( "load-{$pagenow}" ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

	/*
	 * The following hooks are fired to ensure backward compatibility.
	 * In all other cases, 'load-' . $pagenow should be used instead.
	 */
	if ( 'page' === $typenow ) {
		if ( 'post-new.php' === $pagenow ) {
			do_action( 'load-page-new.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		} elseif ( 'post.php' === $pagenow ) {
			do_action( 'load-page.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}
	} elseif ( 'edit-tags.php' === $pagenow ) {
		if ( 'category' === $taxnow ) {
			do_action( 'load-categories.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		} elseif ( 'link_category' === $taxnow ) {
			do_action( 'load-edit-link-categories.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		}
	} elseif ( 'term.php' === $pagenow ) {
		do_action( 'load-edit-tags.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}
}

if ( ! empty( $_REQUEST['action'] ) ) {
	$action = $_REQUEST['action'];

	/**
	 * Fires when an 'action' request variable is sent.
	 *
	 * The dynamic portion of the hook name, `$action`, refers to
	 * the action derived from the `GET` or `POST` request.
	 *
	 * @since 2.6.0
	 */
	do_action( "admin_action_{$action}" );
}
