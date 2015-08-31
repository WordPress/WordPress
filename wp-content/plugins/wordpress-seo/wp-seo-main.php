<?php
/**
 * @package WPSEO\Main
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * @internal Nobody should be able to overrule the real version number as this can cause serious issues
 * with the options, so no if ( ! defined() )
 */
define( 'WPSEO_VERSION', '2.3.2' );

if ( ! defined( 'WPSEO_PATH' ) ) {
	define( 'WPSEO_PATH', plugin_dir_path( WPSEO_FILE ) );
}

if ( ! defined( 'WPSEO_BASENAME' ) ) {
	define( 'WPSEO_BASENAME', plugin_basename( WPSEO_FILE ) );
}

if ( ! defined( 'WPSEO_CSSJS_SUFFIX' ) ) {
	define( 'WPSEO_CSSJS_SUFFIX', ( ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? '' : '.min' ) );
}


/* ***************************** CLASS AUTOLOADING *************************** */

/**
 * Auto load our class files
 *
 * @param string $class Class name.
 *
 * @return void
 */
function wpseo_auto_load( $class ) {
	static $classes = null;

	if ( $classes === null ) {
		$classes = array(
			'wp_list_table'                      => ABSPATH . 'wp-admin/includes/class-wp-list-table.php',
			'walker_category'                    => ABSPATH . 'wp-includes/category-template.php',
			'pclzip'                             => ABSPATH . 'wp-admin/includes/class-pclzip.php',
		);
	}

	$cn = strtolower( $class );

	if ( ! class_exists( $class ) && isset( $classes[ $cn ] ) ) {
		require_once( $classes[ $cn ] );
	}
}

if ( file_exists( WPSEO_PATH . '/vendor/autoload_52.php' ) ) {
	require WPSEO_PATH . '/vendor/autoload_52.php';
}
elseif ( ! class_exists( 'WPSEO_Options' ) ) { // Still checking since might be site-level autoload R.
	add_action( 'admin_init', 'yoast_wpseo_missing_autoload', 1 );
	return;
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( 'wpseo_auto_load' );
}


/* ***************************** PLUGIN (DE-)ACTIVATION *************************** */

/**
 * Run single site / network-wide activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being activated network-wide.
 */
function wpseo_activate( $networkwide = false ) {
	if ( ! is_multisite() || ! $networkwide ) {
		_wpseo_activate();
	}
	else {
		/* Multi-site network activation - activate the plugin for all blogs */
		wpseo_network_activate_deactivate( true );
	}
}

/**
 * Run single site / network-wide de-activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being de-activated network-wide.
 */
function wpseo_deactivate( $networkwide = false ) {
	if ( ! is_multisite() || ! $networkwide ) {
		_wpseo_deactivate();
	}
	else {
		/* Multi-site network activation - de-activate the plugin for all blogs */
		wpseo_network_activate_deactivate( false );
	}
}

/**
 * Run network-wide (de-)activation of the plugin
 *
 * @param bool $activate True for plugin activation, false for de-activation.
 */
function wpseo_network_activate_deactivate( $activate = true ) {
	global $wpdb;

	$original_blog_id = get_current_blog_id(); // Alternatively use: $wpdb->blogid.
	$all_blogs        = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	if ( is_array( $all_blogs ) && $all_blogs !== array() ) {
		foreach ( $all_blogs as $blog_id ) {
			switch_to_blog( $blog_id );

			if ( $activate === true ) {
				_wpseo_activate();
			}
			else {
				_wpseo_deactivate();
			}
		}
		// Restore back to original blog.
		switch_to_blog( $original_blog_id );
	}
}

/**
 * Runs on activation of the plugin.
 */
function _wpseo_activate() {
	require_once( WPSEO_PATH . 'inc/wpseo-functions.php' );

	wpseo_load_textdomain(); // Make sure we have our translations available for the defaults.
	WPSEO_Options::get_instance();
	if ( ! is_multisite() ) {
		WPSEO_Options::initialize();
	}
	else {
		WPSEO_Options::maybe_set_multisite_defaults( true );
	}
	WPSEO_Options::ensure_options_exist();

	add_action( 'shutdown', 'flush_rewrite_rules' );

	wpseo_add_capabilities();

	// Clear cache so the changes are obvious.
	WPSEO_Utils::clear_cache();

	do_action( 'wpseo_activate' );
}

/**
 * On deactivation, flush the rewrite rules so XML sitemaps stop working.
 */
function _wpseo_deactivate() {
	require_once( WPSEO_PATH . 'inc/wpseo-functions.php' );

	add_action( 'shutdown', 'flush_rewrite_rules' );

	wpseo_remove_capabilities();

	// Clear cache so the changes are obvious.
	WPSEO_Utils::clear_cache();

	do_action( 'wpseo_deactivate' );
}

/**
 * Run wpseo activation routine on creation / activation of a multisite blog if WPSEO is activated
 * network-wide.
 *
 * Will only be called by multisite actions.
 *
 * @internal Unfortunately will fail if the plugin is in the must-use directory
 * @see      https://core.trac.wordpress.org/ticket/24205
 *
 * @param int $blog_id
 */
function wpseo_on_activate_blog( $blog_id ) {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	if ( is_plugin_active_for_network( plugin_basename( WPSEO_FILE ) ) ) {
		switch_to_blog( $blog_id );
		wpseo_activate( false );
		restore_current_blog();
	}
}


/* ***************************** PLUGIN LOADING *************************** */

/**
 * Load translations
 */
function wpseo_load_textdomain() {
	$wpseo_path = str_replace( '\\', '/', WPSEO_PATH );
	$mu_path    = str_replace( '\\', '/', WPMU_PLUGIN_DIR );

	if ( false !== stripos( $wpseo_path, $mu_path ) ) {
		load_muplugin_textdomain( 'wordpress-seo', dirname( WPSEO_BASENAME ) . '/languages/' );
	}
	else {
		load_plugin_textdomain( 'wordpress-seo', false, dirname( WPSEO_BASENAME ) . '/languages/' );
	}
}

add_action( 'init', 'wpseo_load_textdomain', 1 );


/**
 * On plugins_loaded: load the minimum amount of essential files for this plugin
 */
function wpseo_init() {
	require_once( WPSEO_PATH . 'inc/wpseo-functions.php' );

	// Make sure our option and meta value validation routines and default values are always registered and available.
	WPSEO_Options::get_instance();
	WPSEO_Meta::init();

	$options = WPSEO_Options::get_all();
	if ( version_compare( $options['version'], WPSEO_VERSION, '<' ) ) {
		new WPSEO_Upgrade();
		// Get a cleaned up version of the $options.
		$options = WPSEO_Options::get_all();
	}

	if ( $options['stripcategorybase'] === true ) {
		$GLOBALS['wpseo_rewrite'] = new WPSEO_Rewrite;
	}

	if ( $options['enablexmlsitemap'] === true ) {
		$GLOBALS['wpseo_sitemaps'] = new WPSEO_Sitemaps;
	}

	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		require_once( WPSEO_PATH . 'inc/wpseo-non-ajax-functions.php' );
	}

	// Init it here because the filter must be present on the frontend as well or it won't work in the customizer.
	if ( current_user_can( 'manage_options' ) ) {
		new WPSEO_Customizer();
	}
}

/**
 * Used to load the required files on the plugins_loaded hook, instead of immediately.
 */
function wpseo_frontend_init() {
	add_action( 'init', 'initialize_wpseo_front' );

	$options = WPSEO_Options::get_all();
	if ( $options['breadcrumbs-enable'] === true ) {
		/**
		 * If breadcrumbs are active (which they supposedly are if the users has enabled this settings,
		 * there's no reason to have bbPress breadcrumbs as well.
		 *
		 * @internal The class itself is only loaded when the template tag is encountered via
		 * the template tag function in the wpseo-functions.php file
		 */
		add_filter( 'bbp_get_breadcrumb', '__return_false' );
	}

	add_action( 'template_redirect', 'wpseo_frontend_head_init', 999 );
}

/**
 * Instantiate the different social classes on the frontend
 */
function wpseo_frontend_head_init() {
	$options = WPSEO_Options::get_all();
	if ( $options['twitter'] === true ) {
		add_action( 'wpseo_head', array( 'WPSEO_Twitter', 'get_instance' ), 40 );
	}

	if ( $options['opengraph'] === true ) {
		$GLOBALS['wpseo_og'] = new WPSEO_OpenGraph;
	}

	if ( $options['googleplus'] === true && is_singular() ) {
		add_action( 'wpseo_head', array( 'WPSEO_GooglePlus', 'get_instance' ), 35 );
	}
}

/**
 * Used to load the required files on the plugins_loaded hook, instead of immediately.
 */
function wpseo_admin_init() {
	new WPSEO_Admin_Init();
}


/* ***************************** BOOTSTRAP / HOOK INTO WP *************************** */
$spl_autoload_exists = function_exists( 'spl_autoload_register' );
$filter_exists       = function_exists( 'filter_input' );

if ( ! $spl_autoload_exists ) {
	add_action( 'admin_init', 'yoast_wpseo_missing_spl', 1 );
}

if ( ! $filter_exists ) {
	add_action( 'admin_init', 'yoast_wpseo_missing_filter', 1 );
}

if ( ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) && ( $spl_autoload_exists && $filter_exists ) ) {
	add_action( 'plugins_loaded', 'wpseo_init', 14 );

	if ( is_admin() ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			require_once( WPSEO_PATH . 'admin/ajax.php' );
		}
		else {
			add_action( 'plugins_loaded', 'wpseo_admin_init', 15 );
		}
	}
	else {
		add_action( 'plugins_loaded', 'wpseo_frontend_init', 15 );
	}

	add_action( 'admin_init', 'load_yoast_notifications' );
}

// Activation and deactivation hook.
register_activation_hook( WPSEO_FILE, 'wpseo_activate' );
register_activation_hook( WPSEO_FILE, array( 'WPSEO_Plugin_Conflict', 'hook_check_for_plugin_conflicts' ) );
register_deactivation_hook( WPSEO_FILE, 'wpseo_deactivate' );
add_action( 'wpmu_new_blog', 'wpseo_on_activate_blog' );
add_action( 'activate_blog', 'wpseo_on_activate_blog' );

/**
 * Wraps for notifications center class.
 */
function load_yoast_notifications() {
	// Init Yoast_Notification_Center class.
	Yoast_Notification_Center::get();
}


/**
 * Throw an error if the PHP SPL extension is disabled (prevent white screens) and self-deactivate plugin
 *
 * @since 1.5.4
 *
 * @return void
 */
function yoast_wpseo_missing_spl() {
	if ( is_admin() ) {
		add_action( 'admin_notices', 'yoast_wpseo_missing_spl_notice' );

		yoast_wpseo_self_deactivate();
	}
}

/**
 * Returns the notice in case of missing spl extension
 */
function yoast_wpseo_missing_spl_notice() {
	$message = esc_html__( 'The Standard PHP Library (SPL) extension seem to be unavailable. Please ask your web host to enable it.', 'wordpress-seo' );
	yoast_wpseo_activation_failed_notice( $message );
}

/**
 * Throw an error if the Composer autoload is missing and self-deactivate plugin
 *
 * @return void
 */
function yoast_wpseo_missing_autoload() {
	if ( is_admin() ) {
		add_action( 'admin_notices', 'yoast_wpseo_missing_autoload_notice' );

		yoast_wpseo_self_deactivate();
	}
}

/**
 * Returns the notice in case of missing Composer autoload
 */
function yoast_wpseo_missing_autoload_notice() {
	/* translators: %1$s expands to Yoast SEO, %2$s / %3$s: links to the installation manual in the Readme for the Yoast SEO code repository on GitHub */
	$message = esc_html__( 'The %1$s plugin installation is incomplete. Please refer to %2$sinstallation instructions%3$s.', 'wordpress-seo' );
	$message = sprintf( $message, 'Yoast SEO', '<a href="https://github.com/Yoast/wordpress-seo#installation">', '</a>' );
	yoast_wpseo_activation_failed_notice( $message );
}

/**
 * Throw an error if the filter extension is disabled (prevent white screens) and self-deactivate plugin
 *
 * @since 2.0
 *
 * @return void
 */
function yoast_wpseo_missing_filter() {
	if ( is_admin() ) {
		add_action( 'admin_notices', 'yoast_wpseo_missing_filter_notice' );

		yoast_wpseo_self_deactivate();
	}
}

/**
 * Returns the notice in case of missing filter extension
 */
function yoast_wpseo_missing_filter_notice() {
	$message = esc_html__( 'The filter extension seem to be unavailable. Please ask your web host to enable it.', 'wordpress-seo' );
	yoast_wpseo_activation_failed_notice( $message );
}

/**
 * Echo's the Activation failed notice with any given message.
 *
 * @param string $message
 */
function yoast_wpseo_activation_failed_notice( $message ) {
	echo '<div class="error"><p>' . __( 'Activation failed:', 'wordpress-seo' ) . ' ' . $message . '</p></div>';
}

/**
 * The method will deactivate the plugin, but only once, done by the static $is_deactivated
 */
function yoast_wpseo_self_deactivate() {
	static $is_deactivated;

	if ( $is_deactivated === null ) {
		$is_deactivated = true;
		deactivate_plugins( plugin_basename( WPSEO_FILE ) );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}


