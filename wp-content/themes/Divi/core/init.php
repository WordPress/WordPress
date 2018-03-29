<?php
/**
 * Load Elegant Themes Core.
 *
 * @package \ET\Core
 */


if ( ! defined( 'ET_CORE' ) ) {
	// Note, this will be updated automatically during grunt release task.
	define( 'ET_CORE_VERSION', '3.0.60' );
	define( 'ET_CORE', true );
} else if ( ! defined( 'ET_CORE_OVERRIDE' ) ) {
	// Core has been loaded already and the override flag is not set.
	return;
}


if ( ! function_exists( 'et_core_autoloader' ) ):
/**
 * Callback for {@link spl_autoload_register()}.
 *
 * @param $class_name
 */
function et_core_autoloader( $class_name ) {
	if ( 0 !== strpos( $class_name, 'ET_Core' ) ) {
		return;
	}

	static $components    = null;
	static $et_core_path  = null;
	static $groups_loaded = array();

	if ( null === $et_core_path ) {
		$et_core_path = defined( 'ET_CORE_PATH_OVERRIDE' ) ?  ET_CORE_PATH_OVERRIDE : ET_CORE_PATH;
	}

	if ( null === $components ) {
		$components = et_core_get_components_metadata();
	}

	if ( ! isset( $components[ $class_name ] ) ) {
		return;
	}

	$file   = $et_core_path . $components[ $class_name ]['file'];
	$groups = $components[ $class_name ]['groups'];
	$slug   = $components[ $class_name ]['slug'];

	if ( ! file_exists( $file ) ) {
		return;
	}

	// Load component class
	require_once $file;

	/**
	 * Fires when a Core Component is loaded.
	 *
	 * The dynamic portion of the hook name, $slug, refers to the slug of the Core Component that was loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( "et_core_component_{$slug}_loaded" );

	if ( empty( $groups ) ) {
		return;
	}

	foreach( $groups as $group_name ) {
		if ( in_array( $group_name, $groups_loaded ) ) {
			continue;
		}

		$groups_loaded[] = $group_name;
		$slug            = $components['groups'][ $group_name ]['slug'];
		$init_file       = $components['groups'][ $group_name ]['init'];
		$init_file       = empty( $init_file ) ? null : $et_core_path . $init_file;

		et_core_initialize_component_group( $slug, $init_file );
	}
}
endif;


if ( ! function_exists( 'et_core_maybe_set_updated' ) ):
function et_core_maybe_set_updated() {
	// TODO: Move et_{*}_option() functions to core.
	$last_core_version = get_option( 'et_core_version', '' );

	if ( ET_CORE_VERSION === $last_core_version ) {
		return;
	}

	update_option( 'et_core_version', ET_CORE_VERSION );

	define( 'ET_CORE_UPDATED', true );
}
endif;


if ( ! function_exists( 'et_new_core_setup') ):
function et_new_core_setup() {
	$core_path   = defined( 'ET_CORE_PATH_OVERRIDE' ) ? ET_CORE_PATH_OVERRIDE : ET_CORE_PATH;
	$has_php_52x = -1 === version_compare( PHP_VERSION, '5.3' );

	require_once "{$core_path}functions.php";
	require_once "{$core_path}components/Updates.php";
	require_once "{$core_path}components/init.php";

	if ( $has_php_52x ) {
		spl_autoload_register( 'et_core_autoloader', true );
	} else {
		spl_autoload_register( 'et_core_autoloader', true, true );
	}

	// Initialize top-level components "group"
	et_core_init();
}
endif;


if ( ! function_exists( 'et_core_setup' ) ) :
/**
 * Setup Core.
 *
 * @since 1.0.0
 *
 * @param string $url Url used to load the Core assets.
 */
function et_core_setup( $url ) {
	if ( ! defined( 'ET_CORE_PATH' ) ) {
		define( 'ET_CORE_PATH', trailingslashit( dirname( __FILE__ ) ) );
		define( 'ET_CORE_URL', trailingslashit( $url ) . 'core/' );
		define( 'ET_CORE_TEXTDOMAIN', 'et-core' );
	}

	load_theme_textdomain( 'et-core', ET_CORE_PATH . 'languages/' );
	et_core_maybe_set_updated();
	et_new_core_setup();

	if ( is_admin() || ! empty( $_GET['et_fb'] ) ) {
		add_action( 'admin_enqueue_scripts', 'et_core_load_main_styles' );
	}
}
endif;
