<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Main
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * {@internal Nobody should be able to overrule the real version number as this can cause
 *            serious issues with the options, so no if ( ! defined() ).}}
 */
define( 'WPSEO_VERSION', '26.5' );


if ( ! defined( 'WPSEO_PATH' ) ) {
	define( 'WPSEO_PATH', plugin_dir_path( WPSEO_FILE ) );
}

if ( ! defined( 'WPSEO_BASENAME' ) ) {
	define( 'WPSEO_BASENAME', plugin_basename( WPSEO_FILE ) );
}

/*
 * {@internal The prefix constants are used to build prefixed versions of dependencies.
 *            These should not be changed on run-time, thus missing the ! defined() check.}}
 */
define( 'YOAST_VENDOR_NS_PREFIX', 'YoastSEO_Vendor' );
define( 'YOAST_VENDOR_DEFINE_PREFIX', 'YOASTSEO_VENDOR__' );
define( 'YOAST_VENDOR_PREFIX_DIRECTORY', 'vendor_prefixed' );

define( 'YOAST_SEO_PHP_REQUIRED', '7.4' );
define( 'YOAST_SEO_WP_TESTED', '6.9' );
define( 'YOAST_SEO_WP_REQUIRED', '6.7' );

if ( ! defined( 'WPSEO_NAMESPACES' ) ) {
	define( 'WPSEO_NAMESPACES', true );
}


/* ***************************** CLASS AUTOLOADING *************************** */

/**
 * Autoload our class files.
 *
 * @param string $class_name Class name.
 *
 * @return void
 */
function wpseo_auto_load( $class_name ) {
	static $classes = null;

	if ( $classes === null ) {
		$classes = [
			'wp_list_table'   => ABSPATH . 'wp-admin/includes/class-wp-list-table.php',
			'walker_category' => ABSPATH . 'wp-includes/category-template.php',
		];
	}

	$cn = strtolower( $class_name );

	if ( ! class_exists( $class_name ) && isset( $classes[ $cn ] ) ) {
		require_once $classes[ $cn ];
	}
}

$yoast_autoload_file = WPSEO_PATH . 'vendor/autoload.php';

if ( is_readable( $yoast_autoload_file ) ) {
	$yoast_autoloader = require $yoast_autoload_file;
}
elseif ( ! class_exists( 'WPSEO_Options' ) ) { // Still checking since might be site-level autoload R.
	add_action( 'admin_init', 'yoast_wpseo_missing_autoload', 1 );

	return;
}

/**
 * Include the file from the `symfony/deprecation-contracts` dependency instead of autoloading it via composer.
 *
 * We need to do that because autoloading via composer prevents the vendor-prefixing of the dependency itself.
 * Note that we don't expect the function to be ever called since the OAuth2 library should not provide invalid input.
 */
$deprecation_contracts_file = WPSEO_PATH . 'vendor_prefixed/symfony/deprecation-contracts/functions.php';
if ( is_readable( $deprecation_contracts_file ) ) {
	include $deprecation_contracts_file;
}

if ( function_exists( 'spl_autoload_register' ) ) {
	spl_autoload_register( 'wpseo_auto_load' );
}
require_once WPSEO_PATH . 'src/functions.php';

/* ********************* DEFINES DEPENDING ON AUTOLOADED CODE ********************* */

/**
 * Defaults to production, for safety.
 */
if ( ! defined( 'YOAST_ENVIRONMENT' ) ) {
	define( 'YOAST_ENVIRONMENT', 'production' );
}

if ( YOAST_ENVIRONMENT === 'development' && isset( $yoast_autoloader ) ) {
	add_action(
		'plugins_loaded',
		/**
		 * Reregisters the autoloader so that Yoast SEO is at the front.
		 * This prevents conflicts with the development versions of our addons.
		 * An anonymous function is used so we can use the autoloader variable.
		 * As this is only loaded in development removing this action is not a concern.
		 *
		 * @return void
		 */
		static function () use ( $yoast_autoloader ) {
			$yoast_autoloader->unregister();
			$yoast_autoloader->register( true );
		},
		1
	);
}

/**
 * Only use minified assets when we are in a production environment.
 */
if ( ! defined( 'WPSEO_CSSJS_SUFFIX' ) ) {
	define( 'WPSEO_CSSJS_SUFFIX', ( YOAST_ENVIRONMENT !== 'development' ) ? '.min' : '' );
}

/* ***************************** PLUGIN (DE-)ACTIVATION *************************** */

/**
 * Run single site / network-wide activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being activated network-wide.
 *
 * @return void
 */
function wpseo_activate( $networkwide = false ) {
	if ( ! is_multisite() || ! $networkwide ) {
		_wpseo_activate();
	}
	else {
		/* Multi-site network activation - activate the plugin for all blogs. */
		wpseo_network_activate_deactivate( true );
	}

	// This is done so that the 'uninstall_{$file}' is triggered.
	register_uninstall_hook( WPSEO_FILE, '__return_false' );
}

/**
 * Run single site / network-wide de-activation of the plugin.
 *
 * @param bool $networkwide Whether the plugin is being de-activated network-wide.
 *
 * @return void
 */
function wpseo_deactivate( $networkwide = false ) {
	if ( ! is_multisite() || ! $networkwide ) {
		_wpseo_deactivate();
	}
	else {
		/* Multi-site network activation - de-activate the plugin for all blogs. */
		wpseo_network_activate_deactivate( false );
	}
}

/**
 * Run network-wide (de-)activation of the plugin.
 *
 * @param bool $activate True for plugin activation, false for de-activation.
 *
 * @return void
 */
function wpseo_network_activate_deactivate( $activate = true ) {
	global $wpdb;

	$network_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d", $wpdb->siteid ) );

	if ( is_array( $network_blogs ) && $network_blogs !== [] ) {
		foreach ( $network_blogs as $blog_id ) {
			switch_to_blog( $blog_id );

			if ( $activate === true ) {
				_wpseo_activate();
			}
			else {
				_wpseo_deactivate();
			}

			restore_current_blog();
		}
	}
}

/**
 * Runs on activation of the plugin.
 *
 * @return void
 */
function _wpseo_activate() {
	require_once WPSEO_PATH . 'inc/wpseo-functions.php';
	require_once WPSEO_PATH . 'inc/class-wpseo-installation.php';

	wpseo_load_textdomain(); // Make sure we have our translations available for the defaults.

	new WPSEO_Installation();

	WPSEO_Options::get_instance();
	if ( ! is_multisite() ) {
		WPSEO_Options::initialize();
	}
	else {
		WPSEO_Options::maybe_set_multisite_defaults( true );
	}
	WPSEO_Options::ensure_options_exist();

	if ( ! is_multisite() || ! ms_is_switched() ) {
		// Constructor has side effects so this registers all hooks.
		$GLOBALS['wpseo_rewrite'] = new WPSEO_Rewrite();
	}
	add_action( 'shutdown', [ 'WPSEO_Utils', 'clear_rewrites' ] );

	WPSEO_Options::set( 'indexing_reason', 'first_install' );
	WPSEO_Options::set( 'first_time_install', true );
	if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
		WPSEO_Options::set( 'should_redirect_after_install_free', true );
	}
	else {
		WPSEO_Options::set( 'activation_redirect_timestamp_free', time() );
	}

	// Reset tracking to be disabled by default.
	if ( ! YoastSEO()->helpers->product->is_premium() && WPSEO_Options::get( 'toggled_tracking' ) !== true ) {
		WPSEO_Options::set( 'tracking', false );
	}
	do_action( 'wpseo_register_roles' );
	WPSEO_Role_Manager_Factory::get()->add();

	do_action( 'wpseo_register_capabilities' );
	WPSEO_Capability_Manager_Factory::get()->add();

	// Clear cache so the changes are obvious.
	WPSEO_Utils::clear_cache();

	do_action( 'wpseo_activate' );
}

/**
 * On deactivation, flush the rewrite rules so XML sitemaps stop working.
 *
 * @return void
 */
function _wpseo_deactivate() {
	require_once WPSEO_PATH . 'inc/wpseo-functions.php';

	add_action( 'shutdown', [ 'WPSEO_Utils', 'clear_rewrites' ] );

	// Register capabilities, to make sure they are cleaned up.
	do_action( 'wpseo_register_roles' );
	do_action( 'wpseo_register_capabilities' );

	// Clean up capabilities.
	WPSEO_Role_Manager_Factory::get()->remove();
	WPSEO_Capability_Manager_Factory::get()->remove();

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
 * {@internal Unfortunately will fail if the plugin is in the must-use directory.
 *            {@link https://core.trac.wordpress.org/ticket/24205} }}
 *
 * @param int|WP_Site $blog_id Blog ID.
 *
 * @return void
 */
function wpseo_on_activate_blog( $blog_id ) {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( $blog_id instanceof WP_Site ) {
		$blog_id = (int) $blog_id->blog_id;
	}

	if ( is_plugin_active_for_network( WPSEO_BASENAME ) ) {
		switch_to_blog( $blog_id );
		wpseo_activate( false );
		restore_current_blog();
	}
}

/* ***************************** PLUGIN LOADING *************************** */

/**
 * Load translations.
 *
 * @return void
 */
function wpseo_load_textdomain() {
	$wpseo_path = str_replace( '\\', '/', WPSEO_PATH );
	$mu_path    = str_replace( '\\', '/', WPMU_PLUGIN_DIR );

	if ( stripos( $wpseo_path, $mu_path ) !== false ) {
		load_muplugin_textdomain( 'wordpress-seo', dirname( WPSEO_BASENAME ) . '/languages/' );
	}
	else {
		load_plugin_textdomain( 'wordpress-seo', false, dirname( WPSEO_BASENAME ) . '/languages/' );
	}
}

add_action( 'plugins_loaded', 'wpseo_load_textdomain' );


/**
 * On plugins_loaded: load the minimum amount of essential files for this plugin.
 *
 * @return void
 */
function wpseo_init() {
	require_once WPSEO_PATH . 'inc/wpseo-functions.php';
	require_once WPSEO_PATH . 'inc/wpseo-functions-deprecated.php';

	// Make sure our option and meta value validation routines and default values are always registered and available.
	WPSEO_Options::get_instance();
	WPSEO_Meta::init();

	if ( version_compare( WPSEO_Options::get( 'version', 1, [ 'wpseo' ] ), WPSEO_VERSION, '<' ) ) {
		// Invalidate the opcache in 10% of the cases, randomly staggered based on the site URL.
		// @TODO: Move the staggering logic to its own class, but only after a few releases after the complete sunset of the opcache invalidation.
		$random_seed               = hexdec( substr( hash( 'sha256', site_url() ), 0, 8 ) );
		$should_invalidate_opcache = ( $random_seed % 10 ) !== 0;

		/**
		 * Filter: 'Yoast\WP\SEO\should_invalidate_opcache' - Allow developers to enable / disable
		 * opcache invalidation upon upgrade of the Yoast SEO plugin.
		 *
		 * @since 26.1
		 *
		 * @param bool $should_invalidate Whether opcache should be invalidated.
		 */
		$should_invalidate_opcache = (bool) apply_filters( 'Yoast\WP\SEO\should_invalidate_opcache', $should_invalidate_opcache );
		if ( $should_invalidate_opcache && function_exists( 'opcache_reset' ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Prevent notices when opcache.restrict_api is set.
			@opcache_reset();
		}

		new WPSEO_Upgrade();
		// Get a cleaned up version of the $options.
	}

	$GLOBALS['wpseo_rewrite'] = new WPSEO_Rewrite();

	if ( WPSEO_Options::get( 'enable_xml_sitemap', null, [ 'wpseo' ] ) === true ) {
		$GLOBALS['wpseo_sitemaps'] = new WPSEO_Sitemaps();
	}

	if ( ! wp_doing_ajax() ) {
		require_once WPSEO_PATH . 'inc/wpseo-non-ajax-functions.php';
	}

	$integrations   = [];
	$integrations[] = new WPSEO_Slug_Change_Watcher();

	foreach ( $integrations as $integration ) {
		$integration->register_hooks();
	}
}

/**
 * Loads the rest api endpoints.
 *
 * @return void
 */
function wpseo_init_rest_api() {
	// We can't do anything when requirements are not met.
	if ( ! WPSEO_Utils::is_api_available() ) {
		return;
	}

	// Boot up REST API.
	$statistics_service = new WPSEO_Statistics_Service( new WPSEO_Statistics() );

	$endpoints   = [];
	$endpoints[] = new WPSEO_Endpoint_File_Size( new WPSEO_File_Size_Service() );
	$endpoints[] = new WPSEO_Endpoint_Statistics( $statistics_service );

	foreach ( $endpoints as $endpoint ) {
		$endpoint->register();
	}
}

/**
 * Used to load the required files on the plugins_loaded hook, instead of immediately.
 *
 * @return void
 */
function wpseo_admin_init() {
	new WPSEO_Admin_Init();
}

/* ***************************** BOOTSTRAP / HOOK INTO WP *************************** */
$spl_autoload_exists = function_exists( 'spl_autoload_register' );

if ( ! $spl_autoload_exists ) {
	add_action( 'admin_init', 'yoast_wpseo_missing_spl', 1 );
}

if ( ! wp_installing() && ( $spl_autoload_exists ) ) {
	add_action( 'plugins_loaded', 'wpseo_init', 14 );
	add_action( 'setup_theme', [ 'Yoast_Dynamic_Rewrites', 'instance' ], 1 );
	add_action( 'rest_api_init', 'wpseo_init_rest_api' );

	if ( is_admin() ) {

		new Yoast_Notifications();

		$yoast_addon_manager = new WPSEO_Addon_Manager();
		$yoast_addon_manager->register_hooks();

		if ( wp_doing_ajax() ) {
			require_once WPSEO_PATH . 'admin/ajax.php';

			// Plugin conflict ajax hooks.
			new Yoast_Plugin_Conflict_Ajax();

			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information but only loading the admin init class.
			if ( isset( $_POST['action'] ) && is_string( $_POST['action'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information but only loading the admin init class, We are strictly comparing only.
				if ( wp_unslash( $_POST['action'] ) === 'inline-save' ) {
					add_action( 'plugins_loaded', 'wpseo_admin_init', 15 );
				}
			}
		}
		else {
			add_action( 'plugins_loaded', 'wpseo_admin_init', 15 );
		}
	}

	add_action( 'plugins_loaded', 'load_yoast_notifications' );

	add_action( 'init', [ 'WPSEO_Replace_Vars', 'setup_statics_once' ] );

	// Initializes the Yoast indexables for the first time.
	YoastSEO();

	/**
	 * Action called when the Yoast SEO plugin file has loaded.
	 */
	do_action( 'wpseo_loaded' );
}

// Activation and deactivation hook.
register_activation_hook( WPSEO_FILE, 'wpseo_activate' );
register_deactivation_hook( WPSEO_FILE, 'wpseo_deactivate' );

add_action( 'wp_initialize_site', 'wpseo_on_activate_blog', 99 );
add_action( 'activate_blog', 'wpseo_on_activate_blog' );

// Registers SEO capabilities.
$wpseo_register_capabilities = new WPSEO_Register_Capabilities();
$wpseo_register_capabilities->register_hooks();

// Registers SEO roles.
$wpseo_register_capabilities = new WPSEO_Register_Roles();
$wpseo_register_capabilities->register_hooks();

/**
 * Wraps for notifications center class.
 *
 * @return void
 */
function load_yoast_notifications() {
	// Init Yoast_Notification_Center class.
	Yoast_Notification_Center::get();
}


/**
 * Throw an error if the PHP SPL extension is disabled (prevent white screens) and self-deactivate plugin.
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
 * Returns the notice in case of missing spl extension.
 *
 * @return void
 */
function yoast_wpseo_missing_spl_notice() {
	$message = esc_html__( 'The Standard PHP Library (SPL) extension seem to be unavailable. Please ask your web host to enable it.', 'wordpress-seo' );
	yoast_wpseo_activation_failed_notice( $message );
}

/**
 * Throw an error if the Composer autoload is missing and self-deactivate plugin.
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
 * Returns the notice in case of missing Composer autoload.
 *
 * @return void
 */
function yoast_wpseo_missing_autoload_notice() {
	/* translators: %1$s expands to Yoast SEO, %2$s / %3$s: links to the installation manual in the Readme for the Yoast SEO code repository on GitHub */
	$message = esc_html__( 'The %1$s plugin installation is incomplete. Please refer to %2$sinstallation instructions%3$s.', 'wordpress-seo' );
	$message = sprintf( $message, 'Yoast SEO', '<a href="https://github.com/Yoast/wordpress-seo#installation">', '</a>' );
	yoast_wpseo_activation_failed_notice( $message );
}

/**
 * Throw an error if the filter extension is disabled (prevent white screens) and self-deactivate plugin.
 *
 * @since 2.0
 * @deprecated 23.3
 * @codeCoverageIgnore
 *
 * @return void
 */
function yoast_wpseo_missing_filter() {
	_deprecated_function( __FUNCTION__, 'Yoast SEO 23.3' );
}

/**
 * Returns the notice in case of missing filter extension.
 *
 * @deprecated 23.3
 * @codeCoverageIgnore
 *
 * @return void
 */
function yoast_wpseo_missing_filter_notice() {
	_deprecated_function( __FUNCTION__, 'Yoast SEO 23.3' );
}

/**
 * Echo's the Activation failed notice with any given message.
 *
 * @param string $message Message string.
 *
 * @return void
 */
function yoast_wpseo_activation_failed_notice( $message ) {
	$title = sprintf(
		/* translators: %s: Yoast SEO. */
		esc_html__( '%s activation failed', 'wordpress-seo' ),
		'Yoast SEO'
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- This function is only called in 3 places that are safe.
	echo '<div class="error yoast-migrated-notice"><h4 class="yoast-notice-migrated-header">' . $title . '</h4><div class="notice-yoast-content"><p>' . strip_tags( $message, '<a>' ) . '</p></div></div>';
}

/**
 * The method will deactivate the plugin, but only once, done by the static $is_deactivated.
 *
 * @return void
 */
function yoast_wpseo_self_deactivate() {
	static $is_deactivated;

	if ( $is_deactivated === null ) {
		$is_deactivated = true;
		deactivate_plugins( WPSEO_BASENAME );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

/**
 * Aliasses added in order to keep compatibility with Yoast SEO: Local.
 */
class_alias( '\Yoast\WP\SEO\Initializers\Initializer_Interface', '\Yoast\WP\SEO\WordPress\Initializer' );
class_alias( '\Yoast\WP\SEO\Loadable_Interface', '\Yoast\WP\SEO\WordPress\Loadable' );
class_alias( '\Yoast\WP\SEO\Integrations\Integration_Interface', '\Yoast\WP\SEO\WordPress\Integration' );
